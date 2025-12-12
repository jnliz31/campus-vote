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
        $activeElection = Election::where('status', 'active')->with(['positions.candidates'])->first();
        $elections = Election::with(['positions.candidates'])->get();

        $stats = [
            'active_positions' => 0,
            'total_candidates' => 0,
            'votes_cast' => 0,
            'participation_rate' => 0,
        ];

        if ($activeElection) {
            $stats['active_positions'] = $activeElection->positions->count();
            $stats['total_candidates'] = $activeElection->candidates->count();
            $stats['votes_cast'] = $activeElection->votes->count();
        }

        return view('admin.manage-election', compact('activeElection', 'elections', 'stats'));
    }

    public function create()
    {
        $activeElection = Election::where('status', 'active')->first();
        return view('admin.create-election', compact('activeElection'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'positions' => 'required|array|min:1',
            'positions.*.name' => 'required|string|max:255',
            'positions.*.candidates' => 'required|array|min:1',
            'positions.*.candidates.*' => 'required|string|max:255',
        ]);

        // End any active elections
        Election::where('status', 'active')->update(['status' => 'ended']);

        $election = Election::create([
            'title' => $request->title,
            'status' => 'active',
        ]);

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

        return redirect()->route('admin.elections.index')->with('success', 'Election created successfully!');
    }

    public function edit(Election $election)
    {
        $election->load(['positions.candidates']);
        return view('admin.edit-election', compact('election'));
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

        return redirect()->route('admin.elections.index')->with('success', 'Election ended successfully!');
    }

    public function destroy(Election $election)
    {
        $election->delete();
        return redirect()->route('admin.elections.index')->with('success', 'Election deleted successfully!');
    }
}
