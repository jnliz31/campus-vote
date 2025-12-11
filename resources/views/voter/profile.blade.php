@extends('layouts.dashboard')

@section('title', 'Profile')

@section('page-title', 'Profile')

@section('content')
    <div class="card">
        <h2 class="card-title">My Profile</h2>
        <p style="color: #666; margin-bottom: 30px;">Manage your Account</p>

        <div class="profile-card">
            <div class="profile-info">
                <span class="profile-label">Email:</span> {{ $voter->email }}
            </div>
            <div class="profile-info">
                <span class="profile-label">Name:</span> {{ $voter->name }}
            </div>
            <div class="profile-info">
                <span class="profile-label">Course:</span> {{ $voter->course ?? 'Not specified' }}
            </div>

            <div class="profile-actions">
                <button class="btn-dark">Use Another Account</button>
                <form method="POST" action="{{ route('voter.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-dark">Log Out</button>
                </form>
            </div>
        </div>
        @if ($voter->google_id)
            <div class="google-account">
                <img src="{{ $voter->avatar }}" alt="Avatar" class="rounded-circle">
                <p>Signed in with: {{ $voter->campus_email }}</p>
            </div>
        @endif

        <div class="profile-footer">
            <a href="#">About CampusVote</a> |
            <a href="#">Help</a> |
            <a href="#">Privacy Policy</a>
        </div>
    </div>
@endsection
