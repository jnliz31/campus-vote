@extends('layouts.app')

@section('title', 'Student Login - CampusVote')

@section('content')
    <div class="auth-container">
        <div class="auth-left">
            <div class="logo-box">
                <div class="logo-icon">
                    <img src="{{ asset('images/download.webp') }}" alt="Campusvote-logo">
                </div>
            </div>

            <div class="info-description">
                Campus Vote is your online student election platform. Log in to view candidates, cast your vote securely,
                and make your voice heard on campus decisions. Your vote counts!
            </div>
        </div>

        <div class="auth-right">
            <div class="login-card">
                <h1 class="login-title">Student Login</h1>

                @if ($errors->any())
                    <div class="error-message">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Traditional Login Form --}}
                <form method="POST" action="{{ route('voter.login') }}">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Student Email"
                            value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-input" placeholder="Password" required>
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>

                 <div class="divider">
                    <span>or continue with email</span>
                </div>

                {{-- Google Sign-In Button --}}
                <a href="{{ route('voter.google.redirect') }}" class="btn-google">
                    <svg class="google-icon" viewBox="0 0 24 24" width="20" height="20">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    Sign in with Gmail
                </a>

                <div class="login-footer">
                    <a href="{{ route('voter.register.form') }}" class="login-link">Don't have an account? Register</a>
                    <br>
                    <a href="{{ route('admin.login') }}" class="login-link">Login as Admin</a>
                </div>

                @if (env('ALLOWED_CAMPUS_DOMAIN'))
                    <p class="campus-domain-note">
                        💡 Use your campus email (@{{ env('ALLOWED_CAMPUS_DOMAIN') }}) for instant access
                    </p>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Google Sign-In Button */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 20px;
            margin-bottom: 20px;
            background: white;
            border: 1px solid #dadce0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #3c4043;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-google:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border-color: #d2d4d6;
        }

        .google-icon {
            margin-right: 12px;
            flex-shrink: 0;
        }

        /* Divider */
        .divider {
            position: relative;
            text-align: center;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .divider span {
            background: white;
            padding: 0 12px;
            font-size: 13px;
            color: #666;
            position: relative;
            z-index: 1;
        }

        /* Success Message */
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Campus Domain Note */
        .campus-domain-note {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 16px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        /* Adjust existing button spacing */
        .btn-login {
            margin-top: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-google {
                font-size: 13px;
                padding: 10px 16px;
            }

            .divider {
                margin: 20px 0;
            }

            .campus-domain-note {
                font-size: 11px;
            }
        }
    </style>
@endsection
