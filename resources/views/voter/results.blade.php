@extends('layouts.dashboard')

@section('title', 'View Results')

@section('page-title', 'Results')

@section('content')
    <div class="results-header">
        <h1 class="election-title">Election Results</h1>

        @if ($election)
            <h2 style="font-size: 20px; color: #666; margin-top: 10px;">{{ $election->title }}</h2>

            <!-- Election Status Badge -->
            <div style="margin: 15px 0;">
                <span class="badge" id="election-status-badge"
                    style="font-size: 14px; padding: 8px 16px; border-radius: 20px; @if ($election->status === 'active') background-color: #28a745; @else background-color: #6c757d; @endif color: white;">
                    {{ ucfirst($election->status) }}
                </span>
            </div>

            <div class="results-meta">
                <div class="results-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#666">
                        <path
                            d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" />
                    </svg>
                    <span id="election-date">{{ $election->created_at->format('F j, Y') }}</span>
                </div>
                <div class="results-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#666">
                        <path
                            d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                    </svg>
                    Last Updated: <span id="last-updated"
                        style="font-weight: 600; color: #333;">{{ now()->format('g:i A') }}</span>
                    <span id="live-indicator"
                        style="margin-left: 8px; font-size: 11px; color: #28a745; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                        <span
                            style="width: 6px; height: 6px; background-color: #28a745; border-radius: 50%; animation: pulse-dot 1.5s infinite;"></span>
                        LIVE
                    </span>
                </div>
                <div class="results-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#666">
                        <path
                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg>
                    <span id="total-votes"
                        style="font-weight: 600; font-size: 16px; color: #333; transition: all 0.3s ease;"
                        data-initial-votes="{{ $totalVotes ?? 0 }}">{{ $totalVotes ?? 0 }}</span> Total Votes
                </div>
            </div>

            <style>
                @keyframes pulse-dot {

                    0%,
                    100% {
                        opacity: 1;
                    }

                    50% {
                        opacity: 0.5;
                    }
                }

                #total-votes {
                    transition: all 0.3s ease;
                }
            </style>
        @endif
    </div>

    @if ($election && count($results) > 0)
        <div id="results-container">
            @foreach ($results as $positionName => $candidates)
                <div class="position-results" data-position="{{ $positionName }}">
                    <h3 class="position-results-title">{{ $positionName }}</h3>

                    @foreach ($candidates as $index => $candidate)
                        <div class="candidate-result" data-candidate-id="{{ $candidate['id'] }}">
                            <div class="candidate-avatar">
                                <svg viewBox="0 0 24 24">
                                    <path fill="white"
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>

                            <div class="result-info">
                                <div class="result-name"><span class="candidate-name">{{ $candidate['name'] }}</span> -
                                    <span class="percentage">{{ $candidate['percentage'] }}</span>%
                                    (<span class="vote-count">{{ $candidate['votes'] }}</span> votes)
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar {{ $index == 0 ? 'first' : ($index == 1 ? 'second' : 'third') }}"
                                        style="width: {{ $candidate['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <p class="empty-state-title">No Results Available</p>
                <p class="empty-state-text">There are no election results to display at this time.</p>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="{{ asset('js/election-realtime.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize with actual vote count from server
                const totalVotesElement = document.getElementById('total-votes');
                let previousTotalVotes = totalVotesElement ? parseInt(totalVotesElement.textContent) || 0 : 0;
                let pollInterval = null;

                // Initialize real-time election updates for results
                const electionUpdates = new ElectionRealtimeUpdates({
                    pollInterval: 3000, // Check every 3 seconds for more frequent updates on results page

                    onStatusChange: function(data) {
                        console.log('Election status changed:', data.status);
                        updateStatusBadge(data);
                    },

                    onResultsUpdate: function(data) {
                        console.log('Results updated:', data);
                        updateResults(data);
                        updateLastUpdated();
                    }
                });

                // Start monitoring results updates - always poll for active elections
                startLivePolling();

                function startLivePolling() {
                    // Initial fetch
                    electionUpdates.fetchResults();

                    // Set up polling every 3 seconds
                    if (pollInterval) clearInterval(pollInterval);
                    pollInterval = setInterval(() => {
                        electionUpdates.fetchResults();
                    }, 3000);
                }

                function updateStatusBadge(data) {
                    const badge = document.getElementById('election-status-badge');
                    if (badge) {
                        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        badge.style.backgroundColor = data.status === 'active' ? '#28a745' : '#6c757d';
                    }
                }

                function updateResults(data) {
                    if (!data.results) return;

                    const container = document.getElementById('results-container');
                    if (!container) return;

                    let totalVotes = 0;

                    // Update each position's results
                    for (const [positionName, candidates] of Object.entries(data.results)) {
                        const positionDiv = container.querySelector(`[data-position="${positionName}"]`);
                        if (!positionDiv) continue;

                        candidates.forEach((candidate, index) => {
                            totalVotes += candidate.votes;

                            const candidateDiv = positionDiv.querySelector(
                                `[data-candidate-id="${candidate.id}"]`);
                            if (candidateDiv) {
                                const voteCountEl = candidateDiv.querySelector('.vote-count');
                                const percentageEl = candidateDiv.querySelector('.percentage');

                                // Check if vote count changed for animation
                                if (voteCountEl && voteCountEl.textContent !== String(candidate.votes)) {
                                    // Add animation class
                                    voteCountEl.style.transition = 'all 0.4s ease';
                                    voteCountEl.style.transform = 'scale(1.2)';
                                    voteCountEl.textContent = candidate.votes;

                                    // Reset transform
                                    setTimeout(() => {
                                        voteCountEl.style.transform = 'scale(1)';
                                    }, 50);
                                }

                                // Update percentage with animation
                                if (percentageEl) {
                                    percentageEl.textContent = candidate.percentage;
                                }

                                // Update progress bar with animation
                                const progressBar = candidateDiv.querySelector('.progress-bar');
                                if (progressBar) {
                                    progressBar.style.width = candidate.percentage + '%';
                                }
                            }
                        });
                    }

                    // Update total votes with animation
                    updateTotalVotesDisplay(totalVotes);
                }

                function updateTotalVotesDisplay(totalVotes) {
                    const totalVotesElement = document.getElementById('total-votes');
                    if (totalVotesElement) {
                        const currentValue = parseInt(totalVotesElement.textContent) || 0;

                        // Only animate if value changed
                        if (currentValue !== totalVotes) {
                            // Add pulse effect
                            totalVotesElement.style.transition = 'all 0.3s ease';
                            totalVotesElement.style.transform = 'scale(1.15)';
                            totalVotesElement.style.color = '#28a745';

                            // Animate counter if difference is not too large
                            if (Math.abs(totalVotes - currentValue) <= 10) {
                                animateCounter(currentValue, totalVotes, totalVotesElement);
                            } else {
                                // Just update directly for large jumps
                                totalVotesElement.textContent = totalVotes;
                                setTimeout(() => {
                                    totalVotesElement.style.transform = 'scale(1)';
                                    totalVotesElement.style.color = '#333';
                                }, 50);
                            }

                            previousTotalVotes = totalVotes;
                        }
                    }
                }

                function animateCounter(start, end, element) {
                    const duration = 600; // milliseconds
                    const increment = (end - start) / (duration / 50);
                    let current = start;

                    const counterInterval = setInterval(() => {
                        current += increment;
                        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                            current = end;
                            clearInterval(counterInterval);
                            element.textContent = end;
                            setTimeout(() => {
                                element.style.transform = 'scale(1)';
                                element.style.color = '#333';
                            }, 50);
                        } else {
                            element.textContent = Math.floor(current);
                        }
                    }, 50);
                }

                function updateLastUpdated() {
                    const element = document.getElementById('last-updated');
                    if (element) {
                        const now = new Date();
                        element.textContent = now.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                    }
                }

                // Stop polling when election ends
                const originalFetchResults = electionUpdates.fetchResults.bind(electionUpdates);
                electionUpdates.fetchResults = async function() {
                    const result = await originalFetchResults();
                    if (result && result.status === 'ended') {
                        clearInterval(pollInterval);
                    }
                    return result;
                };

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    if (pollInterval) clearInterval(pollInterval);
                    electionUpdates.stop();
                });
            });
        </script>
    @endpush
@endsection
