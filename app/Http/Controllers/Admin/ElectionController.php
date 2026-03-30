<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Position;
use App\Models\Candidate;
use App\Events\ElectionEnded;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    public function index()
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $elections = Election::with(['positions.candidates', 'votes'])->get();

        $electionsData = $elections->map(function ($election) {
            return [
                'id' => $election->id,
                'title' => $election->title,
                'status' => $election->status,
                'positions_count' => $election->positions->count(),
                'votes_count' => $election->votes->count(),
                'created_at' => $election->created_at,
            ];
        });

        return response()->json([
            'elections' => $electionsData,
        ]);
    }

    public function create()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'positions' => 'required|array|min:1',
            'positions.*.name' => 'required|string|max:255',
        ]);

        // End any active elections
        Election::where('status', 'active')->update(['status' => 'ended']);

        $election = Election::create([
            'title' => $request->title,
            'status' => 'active',
        ]);

        foreach ($request->positions as $index => $positionData) {
            Position::create([
                'election_id' => $election->id,
                'name' => $positionData['name'],
                'order' => $index,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Election created successfully!',
            'election' => $election,
        ], 201);
    }

    public function edit(Election $election)
    {
        if (!request()->expectsJson()) {
            return view('index');
        }

        $election->load(['positions.candidates']);

        return response()->json([
            'election' => $election,
        ]);
    }

    public function update(Request $request, Election $election)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'positions' => 'required|array|min:1',
            'positions.*.name' => 'required|string|max:255',
            'positions.*.candidates' => 'required|array|min:1',
            'positions.*.candidates.*' => 'required|string|max:255',
        ]);

        $election->update(['title' => $request->title]);

        // Delete existing positions and candidates
        $election->positions()->delete();

        foreach ($request->positions as $index => $positionData) {
            $position = Position::create([
                'election_id' => $election->id,
                'name' => $positionData['name'],
                'order' => $index,
            ]);

            foreach ($positionData['candidates'] as $candidateName) {
                Candidate::create([
                    'position_id' => $position->id,
                    'name' => $candidateName,
                ]);
            }
        }

        return redirect()->route('admin.elections.index')->with('success', 'Election updated successfully!');
    }

    public function endElection(Election $election)
    {
        $election->update(['status' => 'ended']);

        // Dispatch event to broadcast election results to all connected voters
        ElectionEnded::dispatch($election);

        return response()->json([
            'success' => true,
            'message' => 'Election ended successfully!',
        ]);
    }

    public function destroy(Election $election)
    {
        $election->delete();
        return response()->json([
            'success' => true,
            'message' => 'Election deleted successfully!',
        ]);
    }
}
