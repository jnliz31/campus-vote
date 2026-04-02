<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoterAuthController extends Controller
{
    public function showLogin()
    {
        return view('index');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('voter')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $voter = Auth::guard('voter')->user();
            
            // Return JSON for API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'voter' => $voter,
                    'redirect' => '/voter/dashboard',
                    'auth_token' => 'authenticated',
                    'user_role' => 'voter'
                ]);
            }
            
            return redirect()->intended('/voter/dashboard');
        }

        // Return JSON error for API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function check(Request $request)
    {
        $voter = Auth::guard('voter')->user();
        if ($voter) {
            return response()->json([
                'authenticated' => true,
                'user_role' => 'voter',
                'user' => $voter,
                'auth_token' => 'authenticated'
            ]);
        }

        $admin = Auth::guard('admin')->user();
        if ($admin) {
            return response()->json([
                'authenticated' => true,
                'user_role' => 'admin',
                'user' => $admin,
                'auth_token' => 'authenticated'
            ]);
        }

        return response()->json([
            'authenticated' => false,
            'user_role' => null,
            'user' => null
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('voter')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect('/');
    }
}