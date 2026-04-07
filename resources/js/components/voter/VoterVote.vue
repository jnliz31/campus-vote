<template>
    <div class="vote-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Cast Your Vote</h1>
                <p class="header-subtitle">
                    Select your preferred candidates for each position
                </p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
            <div class="spinner"></div>
            <p>Loading election...</p>
        </div>

        <!-- Already Voted State -->
        <div v-else-if="alreadyVoted" class="voted-state">
            <div class="voted-icon">✓</div>
            <h2>You Have Already Voted</h2>
            <p>
                Thank you for participating! You can view your votes and
                election results on the next page.
            </p>
            <p class="redirect-message">Redirecting you...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error && !election.title" class="error-state">
            <div class="error-icon">⚠️</div>
            <h2>{{ error }}</h2>
            <p>Redirecting you back to the dashboard...</p>
        </div>

        <!-- Valid Election State -->
        <template v-else>
            <!-- Election Details Card -->
            <div class="election-details-card">
                <div class="election-info">
                    <h2 class="election-title">{{ election.title }}</h2>
                    <p class="election-instruction">
                        Select candidates for each position according to the max
                        votes allowed
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitVote" id="voteForm">
                <div class="positions-grid">
                    <div
                        v-for="position in election.positions"
                        :key="position.id"
                        class="position-card"
                    >
                        <!-- Position Header -->
                        <div class="position-header">
                            <div class="position-info">
                                <h3 class="position-title">
                                    {{ position.name }}
                                </h3>
                                <p class="max-votes-info">
                                    🗳️ Max {{ position.max_votes }} vote{{
                                        position.max_votes !== 1 ? "s" : ""
                                    }}
                                </p>
                            </div>
                            <div
                                class="vote-counter"
                                :class="{
                                    'counter-valid':
                                        votes[position.id] &&
                                        votes[position.id].length ===
                                            position.max_votes,
                                    'counter-invalid':
                                        votes[position.id] &&
                                        votes[position.id].length !==
                                            position.max_votes &&
                                        votes[position.id].length > 0,
                                }"
                            >
                                {{ votes[position.id]?.length || 0 }}/{{
                                    position.max_votes
                                }}
                            </div>
                        </div>

                        <!-- Error Message -->
                        <p
                            v-if="validationErrors[position.id]"
                            class="position-error"
                        >
                            ⚠️ {{ validationErrors[position.id] }}
                        </p>

                        <!-- Candidates List -->
                        <div class="candidates-list">
                            <label
                                v-for="candidate in position.candidates"
                                :key="candidate.id"
                                class="candidate-item"
                                :class="{
                                    'candidate-checked':
                                        votes[position.id] &&
                                        votes[position.id].includes(
                                            candidate.id,
                                        ),
                                }"
                            >
                                <div class="candidate-checkbox-wrapper">
                                    <input
                                        :checked="
                                            votes[position.id] &&
                                            votes[position.id].includes(
                                                candidate.id,
                                            )
                                        "
                                        @change="
                                            toggleVote(
                                                position.id,
                                                candidate.id,
                                                position.max_votes,
                                            )
                                        "
                                        type="checkbox"
                                        class="candidate-checkbox"
                                    />
                                    <span class="checkbox-custom"></span>
                                </div>
                                <div class="candidate-avatar">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            fill="white"
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                                        />
                                    </svg>
                                </div>
                                <span class="candidate-name">{{
                                    candidate.name
                                }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="submit-section">
                    <button
                        type="submit"
                        class="btn-submit"
                        :disabled="loading || !isFormValid || hasExceededVotes"
                    >
                        <span v-if="loading" class="btn-spinner"></span>
                        <span class="btn-text">
                            {{
                                loading
                                    ? "Submitting your vote..."
                                    : hasExceededVotes
                                      ? "Please deselect one or more votes"
                                      : !isFormValid
                                        ? "Select exactly the required votes"
                                        : "Submit Vote"
                            }}
                        </span>
                    </button>
                </div>
            </form>

            <!-- Error Alert -->
            <div v-if="error" class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <div class="alert-content">
                    <p class="alert-title">Error</p>
                    <p class="alert-message">{{ error }}</p>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import { voterAPI } from "../../services/api.js";
import { useNotification } from "../../composables/useNotification.js";
import { useConfirmDialog } from "../../composables/useConfirmDialog.js";

export default {
    name: "VoterVote",
    setup() {
        const { error: showError } = useNotification();
        const { confirmDangerous: showConfirmDangerous } = useConfirmDialog();
        return { showError, showConfirmDangerous };
    },
    data() {
        return {
            election: {
                title: "",
                positions: [],
            },
            votes: {},
            loading: true,
            error: "",
            alreadyVoted: false,
            validationErrors: {},
        };
    },
    computed: {
        isFormValid() {
            for (const position of this.election.positions) {
                const selectedCount = this.votes[position.id]?.length || 0;
                if (selectedCount !== position.max_votes) {
                    return false;
                }
            }
            return true;
        },
        hasExceededVotes() {
            for (const position of this.election.positions) {
                const selectedCount = this.votes[position.id]?.length || 0;
                if (selectedCount > position.max_votes) {
                    return true;
                }
            }
            return false;
        },
    },
    mounted() {
        this.loadVotePage();
    },
    methods: {
        async loadVotePage() {
            this.loading = true;
            this.error = "";
            this.alreadyVoted = false;
            try {
                const response = await voterAPI.getVote();
                const data = response.data;

                // Check if user has already voted FIRST (before checking election)
                if (data.has_voted) {
                    this.loading = false;
                    this.alreadyVoted = true;
                    setTimeout(() => this.$router.push("/voter/votes"), 2500);
                    return;
                }

                // Then check if there's an active election
                if (!data.election) {
                    this.loading = false;
                    this.error = "No active election at the moment";
                    setTimeout(
                        () => this.$router.push("/voter/dashboard"),
                        2000,
                    );
                    return;
                }

                this.election = data.election;

                // Initialize votes object with empty arrays for each position
                this.election.positions.forEach((position) => {
                    this.votes[position.id] = [];
                });

                this.loading = false;
            } catch (error) {
                console.error("Error loading vote page:", error);
                this.loading = false;

                // Handle different error types based on response status
                if (error.response) {
                    const status = error.response.status;
                    const errorData = error.response.data;

                    // 403: User has already voted
                    if (status === 403 && errorData.has_voted) {
                        this.alreadyVoted = true;
                        setTimeout(
                            () => this.$router.push("/voter/votes"),
                            2500,
                        );
                        return;
                    }

                    // 404: No active election
                    if (status === 404) {
                        this.error =
                            errorData.error ||
                            "No active election at the moment";
                        setTimeout(
                            () => this.$router.push("/voter/dashboard"),
                            2000,
                        );
                        return;
                    }

                    // Other errors
                    this.error =
                        errorData.error ||
                        "An error occurred while loading the election";
                } else {
                    this.error = "No active election at the moment";
                }

                setTimeout(() => this.$router.push("/voter/dashboard"), 2000);
            }
        },
        toggleVote(positionId, candidateId, maxVotes) {
            if (!this.votes[positionId]) {
                this.votes[positionId] = [];
            }

            const index = this.votes[positionId].indexOf(candidateId);

            if (index > -1) {
                // Remove the vote
                this.votes[positionId].splice(index, 1);
                delete this.validationErrors[positionId];
            } else {
                // Add the vote only if max not reached
                if (this.votes[positionId].length < maxVotes) {
                    this.votes[positionId].push(candidateId);
                    // Clear error if we now have exact votes
                    if (this.votes[positionId].length === maxVotes) {
                        delete this.validationErrors[positionId];
                    }
                } else {
                    this.validationErrors[positionId] =
                        `You can only select ${maxVotes} candidate(s) for this position`;
                    this.error = `You have reached the maximum of ${maxVotes} vote(s) for this position. Deselect one to select another.`;
                    setTimeout(() => {
                        this.error = "";
                    }, 4000);
                }
            }
        },
        async submitVote() {
            // Check if all positions have exactly the required votes
            this.error = "";
            this.validationErrors = {};
            let valid = true;

            for (const position of this.election.positions) {
                const selectedCount = this.votes[position.id]?.length || 0;
                if (selectedCount !== position.max_votes) {
                    this.validationErrors[position.id] =
                        `Select exactly ${position.max_votes} candidate(s)`;
                    valid = false;
                }
            }

            if (!valid) {
                this.error =
                    "Please correct the selections below to match the required vote count for each position.";
                return;
            }

            const confirmed = await this.showConfirmDangerous(
                "Are you sure you want to submit your vote? This action cannot be undone.",
                {
                    title: "Submit Vote",
                    confirmText: "Submit Vote",
                },
            );

            if (!confirmed) {
                return;
            }

            this.loading = true;
            this.error = "";

            try {
                // Transform votes to format expected by backend
                const votesData = {};
                for (const position of this.election.positions) {
                    votesData[position.id] = this.votes[position.id];
                }

                const response = await voterAPI.submitVote(votesData);

                // Vote submitted successfully
                this.$router.push("/voter/votes");
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to submit vote. Please try again.";
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.vote-page {
    padding: 0;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #116b27 0%, #22863a 100%);
    padding: 40px;
    border-radius: 0;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 4px 12px rgba(17, 107, 39, 0.15);
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: white;
    letter-spacing: -0.5px;
}

.header-subtitle {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-weight: 400;
}

/* Loading State */
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 40px;
    background: white;
    border-radius: 8px;
    margin: 30px 20px;
    min-height: 400px;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e0e0e0;
    border-top: 4px solid #116b27;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.loading-container p {
    color: #666;
    font-size: 16px;
    font-weight: 500;
}

/* Error State */
.error-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 8px;
    margin: 30px 20px;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid #ffebee;
}

.error-icon {
    font-size: 80px;
    margin-bottom: 20px;
    animation: warning-pulse 2s ease-in-out infinite;
}

@keyframes warning-pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.error-state h2 {
    font-size: 24px;
    font-weight: 700;
    color: #d32f2f;
    margin: 0 0 12px 0;
}

.error-state p {
    font-size: 15px;
    color: #666;
    margin: 0;
}

.redirect-message {
    font-size: 14px;
    color: #888;
    margin-top: 16px;
    font-style: italic;
}

/* Voted State */
.voted-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 8px;
    margin: 30px 20px;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid #e8f5e9;
}

.voted-icon {
    font-size: 80px;
    color: #116b27;
    margin-bottom: 20px;
    animation: success-bounce 0.6s ease-in-out;
}

@keyframes success-bounce {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.voted-state h2 {
    font-size: 24px;
    font-weight: 700;
    color: #116b27;
    margin: 0 0 12px 0;
}

.voted-state p {
    font-size: 15px;
    color: #666;
    margin: 0;
    line-height: 1.6;
}

/* Election Details Card */
.election-details-card {
    background: white;
    padding: 24px;
    border-radius: 8px;
    border-left: 4px solid #116b27;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.election-info {
    max-width: 1200px;
    margin: 0 auto;
}

.election-title {
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: #116b27;
}

.election-instruction {
    font-size: 14px;
    color: #666;
    margin: 0;
    line-height: 1.5;
}

/* Form */
form {
    max-width: 1200px;
    margin: 0 auto;
}

.positions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

/* Position Card */
.position-card {
    background: white;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-top: 3px solid #116b27;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.position-card:hover {
    box-shadow: 0 4px 16px rgba(17, 107, 39, 0.12);
    transform: translateY(-2px);
}

.position-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    gap: 16px;
}

.position-info {
    flex: 1;
}

.position-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 8px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.max-votes-info {
    font-size: 13px;
    color: #666;
    margin: 0;
    font-weight: 500;
}

.vote-counter {
    background: #f5f5f5;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 700;
    color: #888;
    border: 2px solid #e0e0e0;
    text-align: center;
    min-width: 60px;
    transition: all 0.2s;
}

.counter-valid {
    background: #e8f5e9;
    color: #116b27;
    border-color: #116b27;
}

.counter-invalid {
    background: #fff3cd;
    color: #856404;
    border-color: #ffc107;
}

/* Error Message */
.position-error {
    font-size: 13px;
    color: #d32f2f;
    background: #ffebee;
    padding: 10px 12px;
    border-radius: 4px;
    margin: 0 0 16px 0;
    border-left: 3px solid #d32f2f;
}

/* Candidates List */
.candidates-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.candidate-item {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    gap: 12px;
}

.candidate-item:hover {
    background: #f5f5f5;
    border-color: #116b27;
    box-shadow: 0 2px 6px rgba(17, 107, 39, 0.08);
}

.candidate-checked {
    background: #f0f9f5;
    border-color: #116b27;
}

.candidate-checked .candidate-name {
    color: #116b27;
    font-weight: 600;
}

.candidate-checkbox-wrapper {
    position: relative;
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.candidate-checkbox {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #ddd;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    background: white;
}

.candidate-checkbox:checked ~ .checkbox-custom {
    background: #116b27;
    border-color: #116b27;
}

.candidate-checkbox:checked ~ .checkbox-custom::after {
    content: "✓";
    color: white;
    font-size: 14px;
    font-weight: bold;
    display: block;
}

.candidate-avatar {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #116b27 0%, #22863a 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.candidate-avatar svg {
    width: 20px;
    height: 20px;
}

.candidate-name {
    flex: 1;
    font-size: 15px;
    color: #333;
    font-weight: 500;
    transition: color 0.2s;
}

/* Submit Section */
.submit-section {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
    gap: 16px;
}

.btn-submit {
    background: linear-gradient(135deg, #116b27 0%, #22863a 100%);
    color: white;
    border: none;
    padding: 16px 48px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(17, 107, 39, 0.25);
    letter-spacing: 0.3px;
}

.btn-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, #0e5620 0%, #1a6b2e 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(17, 107, 39, 0.35);
}

.btn-submit:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(17, 107, 39, 0.25);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-text {
    display: inline-block;
}

.btn-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: white;
    border-radius: 50%;
    animation: spinner 0.8s linear infinite;
}

@keyframes spinner {
    to {
        transform: rotate(360deg);
    }
}

/* Alert */
.alert {
    display: flex;
    gap: 16px;
    padding: 16px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    border-left: 4px solid;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.alert-icon {
    font-size: 20px;
    flex-shrink: 0;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 4px 0;
}

.alert-message {
    font-size: 14px;
    margin: 0;
    line-height: 1.5;
}

.alert-error {
    background: #ffebee;
    color: #c62828;
    border-left-color: #d32f2f;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 32px 20px;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 28px;
    }

    .header-subtitle {
        font-size: 14px;
    }

    .loading-container,
    .error-state,
    .voted-state {
        margin: 20px;
        padding: 60px 30px;
        min-height: 350px;
    }

    .error-icon,
    .voted-icon {
        font-size: 60px;
        margin-bottom: 16px;
    }

    .error-state h2,
    .voted-state h2 {
        font-size: 20px;
    }

    .positions-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .submit-section {
        flex-direction: column;
    }

    .btn-submit {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .page-header {
        padding: 24px 16px;
    }

    .page-title {
        font-size: 24px;
    }

    .loading-container,
    .error-state,
    .voted-state {
        margin: 16px;
        padding: 40px 20px;
        min-height: 300px;
    }

    .error-icon,
    .voted-icon {
        font-size: 50px;
    }

    .error-state h2,
    .voted-state h2 {
        font-size: 18px;
    }

    .error-state p,
    .voted-state p {
        font-size: 14px;
    }

    .position-header {
        flex-direction: column;
    }

    .vote-counter {
        width: 100%;
    }

    .btn-submit {
        padding: 14px 24px;
        font-size: 15px;
    }
}
</style>
