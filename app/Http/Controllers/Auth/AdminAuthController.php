<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
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

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $admin = Auth::guard('admin')->user();
            
            // Return JSON for API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'admin' => $admin,
                    'redirect' => '/admin/dashboard',
                    'auth_token' => 'authenticated',
                    'user_role' => 'admin'
                ]);
            }
            
            return redirect()->intended('/admin/dashboard');
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
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect('/admin/login');
    }
}