<?php

namespace App\Http\Controllers\Voter;

use App\Models\Vote;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{


    /**
     * Show the voter dashboard.
     */
    public function dashboard()
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $voter = Auth::guard('voter')->user();
        $announcements = Announcement::orderBy('created_at', 'desc')->limit(4)->get();
        $activeElection = Election::where('status', 'active')->first();

        // Check if has voted in active election
        $hasVoted = false;
        if ($activeElection) {
            $hasVoted = Vote::where('voter_id', $voter->id)
                ->where('election_id', $activeElection->id)
                ->exists();
        }

        return response()->json([
            'voter' => $voter,
            'announcements' => $announcements,
            'active_election' => $activeElection,
            'has_voted' => $hasVoted,
        ]);
    }

    /**
     * Show the voting page.
     */
    public function vote()
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $voter = Auth::guard('voter')->user();
        $election = Election::where('status', 'active')->with(['positions.candidates'])->first();

        if (!$election) {
            return response()->json([
                'error' => 'No active election at the moment'
            ], 404);
        }

        // Check if voter has already voted
        $hasVoted = Vote::where('voter_id', $voter->id)
            ->where('election_id', $election->id)
            ->exists();

        if ($hasVoted) {
            return response()->json([
                'error' => 'You have already voted in this election',
                'has_voted' => true
            ], 403);
        }

        return response()->json([
            'election' => $election,
            'has_voted' => false,
        ]);
    }

    /**
     * Submit the vote.
     */
    public function submitVote(Request $request)
    {
        $voter = Auth::guard('voter')->user();
        $election = Election::where('status', 'active')
            ->with(['positions.candidates'])
            ->first();

        if (!$election) {
            return response()->json([
                'error' => 'No active election found'
            ], 404);
        }

        // Check if already voted (prevent duplicate)
        $hasVoted = Vote::where('voter_id', $voter->id)
            ->where('election_id', $election->id)
            ->exists();

        if ($hasVoted) {
            return response()->json([
                'error' => 'You have already voted in this election'
            ], 403);
        }

        // Validate votes
        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'required|exists:candidates,id',
        ]);

        // Validate that one vote per position
        $positionIds = $election->positions->pluck('id')->toArray();

        if (count($request->votes) !== count($positionIds)) {
            return response()->json([
                'error' => 'You must vote for all positions'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->votes as $positionId => $candidateId) {
                // Verify candidate belongs to the position
                $candidate = Candidate::where('id', $candidateId)
                    ->where('position_id', $positionId)
                    ->first();

                if (!$candidate) {
                    throw new \Exception('Invalid candidate for position.');
                }

                // Create vote with tracking
                Vote::create([
                    'voter_id' => $voter->id,
                    'candidate_id' => $candidateId,
                    'election_id' => $election->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            DB::commit();

            Log::info('Votes submitted', [
                'voter_id' => $voter->id,
                'election_id' => $election->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your vote has been submitted successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Vote submission error', [
                'voter_id' => $voter->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An error occurred while submitting your vote'
            ], 500);
        }
    }

    /**
     * Show voter's submitted votes.
     */
    public function showVotes()
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $voter = Auth::guard('voter')->user();

        $votes = Vote::where('voter_id', $voter->id)
            ->with(['candidate.position', 'election'])
            ->get()
            ->groupBy('election_id');

        return response()->json([
            'votes' => $votes->map(function ($electionVotes) {
                $election = $electionVotes->first()?->election;
                return [
                    'id' => $election?->id,
                    'election' => $election,
                    'positions' => $electionVotes->groupBy('candidate.position.name')->map(function ($positionVotes) {
                        return [
                            'name' => $positionVotes->first()?->candidate->position->name,
                            'selected_candidate' => $positionVotes->first()?->candidate->name,
                        ];
                    })->values(),
                    'created_at' => $electionVotes->first()?->created_at,
                ];
            })->values(),
        ]);
    }

    /**
     * Show election results.
     */
    public function results()
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        // Get all elections with results
        $elections = Election::with(['positions.candidates.votes'])->get();

        $results = $elections->map(function ($election) {
            $positions = $election->positions->map(function ($position) {
                $candidates = $position->candidates()->withCount('votes')->get();
                $totalVotes = $candidates->sum('votes_count');

                return [
                    'id' => $position->id,
                    'name' => $position->name,
                    'candidates' => $candidates->map(function ($candidate) use ($totalVotes) {
                        return [
                            'id' => $candidate->id,
                            'name' => $candidate->name,
                            'vote_count' => $candidate->votes_count,
                            'percentage' => $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 2) : 0,
                        ];
                    })->sortByDesc('vote_count')->values(),
                ];
            })->values();

            return [
                'id' => $election->id,
                'title' => $election->title,
                'status' => $election->status,
                'positions' => $positions,
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }

    /**
     * Show voter profile.
     */
    public function profile()
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $voter = Auth::guard('voter')->user();
        $votes_count = Vote::where('voter_id', $voter->id)->count();

        $voterArray = $voter instanceof \Illuminate\Database\Eloquent\Model
            ? $voter->toArray()
            : (array) $voter;

        return response()->json([
            'voter' => array_merge($voterArray, ['votes_count' => $votes_count]),
        ]);
    }

    /**
     * Update voter profile.
     */
    public function updateProfile(Request $request)
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::guard('voter')->id(),
            'student_id' => 'nullable|string|max:255',
        ]);

        $voter = Auth::guard('voter')->user();
        $voter->update($validated);

        $votes_count = Vote::where('voter_id', $voter->id)->count();

        $voterArray = $voter instanceof \Illuminate\Database\Eloquent\Model
            ? $voter->toArray()
            : (array) $voter;

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'voter' => array_merge($voterArray, ['votes_count' => $votes_count]),
        ]);
    }
}
