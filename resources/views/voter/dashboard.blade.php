@extends('layouts.dashboard')

@section('title', 'Voter Dashboard')

@section('page-title', 'Welcome')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">Welcome, <em>{{ $voter->name }}</em>!</h1>
        <p class="welcome-subtitle">"Cast your vote and make your voice heard."</p>
    </div>

    <!-- Election Status Alert -->
    <div id="election-status-alert" style="display: none; margin-bottom: 20px;">
        <div class="alert alert-info" id="election-status-message">
            <strong>⏳ Election Status:</strong> <span id="status-text"></span>
        </div>
    </div>

    <div class="feature-grid">
        <div class="feature-card">
            <h3 class="feature-title">Vote Now</h3>
            <p class="feature-description">Cast your vote in active elections and make your voice heard.</p>
            @if ($activeElection)
                @php
                    $hasVoted = \App\Models\Vote::where('voter_id', $voter->id)
                        ->where('election_id', $activeElection->id)
                        ->exists();
                @endphp

                @if (!$hasVoted)
                    <a href="{{ route('voter.vote') }}" class="btn btn-primary">Start Voting</a>
                @else
                    <button class="btn btn-secondary" disabled>Already Voted</button>
                @endif
            @else
                <button class="btn btn-secondary" disabled>No Active Election</button>
            @endif
        </div>

        <div class="feature-card">
            <h3 class="feature-title">View Vote</h3>
            <p class="feature-description">Check your voting history and verify your submissions.</p>
            <a href="{{ route('voter.votes') }}" class="btn btn-success">View History</a>
        </div>

        <div class="feature-card">
            <h3 class="feature-title">View Results</h3>
            <p class="feature-description">See real-time results and election outcomes.</p>
            <a href="{{ route('voter.results') }}" class="btn btn-purple">View Results</a>
        </div>
    </div>

    <div class="announcements-section">
        <div class="announcements-header">
            <span class="announcements-icon">📢</span>
            <h2 class="announcements-title">Announcements</h2>
        </div>

        @forelse($announcements as $announcement)
            <div class="announcement-item">
                <p class="announcement-text">{{ $announcement->content }}</p>
            </div>
        @empty
            <p style="text-align: center; color: #666;">No announcements at this time.</p>
        @endforelse
    </div>

    @push('scripts')
        <script src="{{ asset('js/election-realtime.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize real-time election updates
                const electionUpdates = new ElectionRealtimeUpdates({
                    pollInterval: 5000, // Check every 5 seconds

                    onStatusChange: function(data) {
                        console.log('Election status changed:', data.status);
                        showElectionStatusAlert(data);
                    },

                    onElectionEnded: function(data) {
                        console.log('Election ended!');
                        showElectionEndedAlert(data);
                        // Redirect to results page after 3 seconds
                        setTimeout(() => {
                            window.location.href = '{{ route('voter.results') }}';
                        }, 3000);
                    }
                });

                // Start monitoring if there's an active election
                @if ($activeElection)
                    electionUpdates.start();
                @endif

                function showElectionStatusAlert(data) {
                    const alertDiv = document.getElementById('election-status-alert');
                    const alertMessage = document.getElementById('election-status-message');
                    const statusText = document.getElementById('status-text');

                    if (data.status === 'active') {
                        statusText.textContent = `Active (${data.total_votes_cast} votes cast)`;
                        alertDiv.style.display = 'block';
                        alertMessage.className = 'alert alert-info';
                    } else if (data.status === 'ended') {
                        statusText.textContent = 'ENDED - Results will load shortly...';
                        alertDiv.style.display = 'block';
                        alertMessage.className = 'alert alert-success';
                    }
                }

                function showElectionEndedAlert(data) {
                    const alertDiv = document.getElementById('election-status-alert');
                    const alertMessage = document.getElementById('election-status-message');
                    const statusText = document.getElementById('status-text');

                    statusText.textContent = 'ELECTION ENDED! Redirecting to results...';
                    alertDiv.style.display = 'block';
                    alertMessage.className = 'alert alert-success';
                }

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    electionUpdates.stop();
                });
            });
        </script>
    @endpush
@endsection
