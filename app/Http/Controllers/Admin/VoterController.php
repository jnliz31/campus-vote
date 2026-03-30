<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voter;

class VoterController extends Controller
{
    public function index()
    {
        $voters = Voter::orderBy('name')->get();
        return response()->json([
            'voters' => $voters,
        ]);
    }

    public function destroy(Voter $voter)
    {
        $voter->delete();
        return response()->json([
            'success' => true,
            'message' => 'Voter deleted successfully!',
        ]);
    }
}