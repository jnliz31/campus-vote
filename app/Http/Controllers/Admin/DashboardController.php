<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Position;
use App\Models\Candidate;
use App\Models\Vote;

class DashboardController extends Controller
{
    public function index()
    {
        $totalElections = Election::count();
        $activeElections = Election::where('status', 'active')->count();
        $totalVoters = \App\Models\Voter::count();
        $totalVotes = Vote::count();

        return response()->json([
            'stats' => [
                'total_elections' => $totalElections,
                'active_elections' => $activeElections,
                'total_voters' => $totalVoters,
                'total_votes' => $totalVotes,
            ]
        ]);
    }
}