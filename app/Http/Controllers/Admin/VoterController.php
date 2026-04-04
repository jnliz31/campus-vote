<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voter;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->expectsJson()) {
            return view('index');
        }

        $voters = Voter::orderBy('name')->get();
        return response()->json([
            'voters' => $voters,
        ]);
    }

    public function destroy(Voter $voter)
    {
        try {
            // Check if voter has any votes
            $voteCount = $voter->votes()->count();
            if ($voteCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete voter with existing votes ({$voteCount} votes found). This maintains vote integrity.",
                ], 422);
            }

            $voter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Voter deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete voter: ' . $e->getMessage(),
            ], 500);
        }
    }
}
