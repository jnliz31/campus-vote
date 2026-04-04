<template>
    <div>
        <div class="election-header">
            <h1 class="election-title">Ongoing Election</h1>
            <a href="#" class="election-link">{{ election.title }}</a>
            <p class="election-instruction">
                (Select candidates for each position according to the max votes
                allowed)
            </p>
        </div>

        <form @submit.prevent="submitVote" id="voteForm">
            <div class="positions-grid">
                <div
                    v-for="position in election.positions"
                    :key="position.id"
                    class="position-card"
                >
                    <h2 class="position-title">{{ position.name }}</h2>
                    <p class="max-votes-info">
                        Max votes allowed: {{ position.max_votes }}
                    </p>
                    <p
                        :class="{
                            'selected-count': true,
                            'vote-count-valid':
                                votes[position.id] &&
                                votes[position.id].length ===
                                    position.max_votes,
                            'vote-count-invalid':
                                votes[position.id] &&
                                votes[position.id].length !==
                                    position.max_votes,
                        }"
                    >
                        Selected: {{ votes[position.id]?.length || 0 }} /
                        {{ position.max_votes }}
                    </p>
                    <p
                        v-if="validationErrors[position.id]"
                        class="position-error"
                    >
                        ⚠️ {{ validationErrors[position.id] }}
                    </p>

                    <label
                        v-for="candidate in position.candidates"
                        :key="candidate.id"
                        class="candidate-item"
                    >
                        <div class="candidate-avatar">
                            <svg viewBox="0 0 24 24">
                                <path
                                    fill="white"
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                                />
                            </svg>
                        </div>
                        <span class="candidate-name">{{ candidate.name }}</span>
                        <input
                            :checked="
                                votes[position.id] &&
                                votes[position.id].includes(candidate.id)
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
                    </label>
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px">
                <button
                    type="submit"
                    class="btn btn-success"
                    style="padding: 15px 40px; font-size: 16px"
                    :disabled="loading || !isFormValid || hasExceededVotes"
                >
                    {{
                        loading
                            ? "Submitting..."
                            : hasExceededVotes
                              ? "Please adjust selections - you've selected too many votes"
                              : !isFormValid
                                ? "Select exactly the required votes for all positions"
                                : "Submit Vote"
                    }}
                </button>
            </div>
        </form>

        <div v-if="error" class="alert alert-error">
            {{ error }}
        </div>
    </div>
</template>

<script>
import { voterAPI } from "../../services/api.js";

export default {
    name: "VoterVote",
    data() {
        return {
            election: {
                title: "",
                positions: [],
            },
            votes: {},
            loading: false,
            error: "",
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
            try {
                const response = await voterAPI.getVote();
                const data = response.data;

                if (!data.election) {
                    this.$router.push("/voter/dashboard");
                    return;
                }

                this.election = data.election;

                // Initialize votes object with empty arrays for each position
                this.election.positions.forEach((position) => {
                    this.votes[position.id] = [];
                });

                if (data.has_voted) {
                    this.$router.push("/voter/votes");
                    return;
                }
            } catch (error) {
                console.error("Error loading vote page:", error);
                this.error = "No active election at the moment";
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

            if (
                !confirm(
                    "Are you sure you want to submit your vote? This action cannot be undone.",
                )
            ) {
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
.election-header {
    margin-bottom: 40px;
}

.election-title {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.election-link {
    display: block;
    font-size: 20px;
    color: #0366d6;
    text-decoration: none;
    margin-bottom: 10px;
}

.election-link:hover {
    text-decoration: underline;
}

.election-instruction {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.positions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.position-card {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.position-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

.max-votes-info {
    font-size: 13px;
    color: #666;
    margin: 0 0 8px 0;
    padding: 8px;
    background-color: #f0f5f8;
    border-left: 3px solid #0366d6;
    border-radius: 3px;
}

.selected-count {
    font-size: 13px;
    margin: 0 0 15px 0;
    font-weight: 600;
    padding: 8px;
    border-radius: 3px;
}

.vote-count-valid {
    color: #22863a;
    background-color: #d4edda;
    border-left: 3px solid #22863a;
}

.vote-count-invalid {
    color: #856404;
    background-color: #fff3cd;
    border-left: 3px solid #ffc107;
}

.position-error {
    font-size: 13px;
    color: #d32f2f;
    margin: -10px 0 15px 0;
    font-weight: 600;
    padding: 8px;
    background-color: #ffebee;
    border-left: 3px solid #d32f2f;
    border-radius: 3px;
}

.candidate-item {
    display: flex;
    align-items: center;
    padding: 15px;
    margin-bottom: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.candidate-item:hover {
    background-color: #f8f9fa;
    border-color: #116b27;
}

.candidate-item input:checked ~ .candidate-name {
    color: #116b27;
    font-weight: 600;
}

.candidate-avatar {
    width: 40px;
    height: 40px;
    background-color: #116b27;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.candidate-avatar svg {
    width: 24px;
    height: 24px;
}

.candidate-name {
    flex: 1;
    font-size: 16px;
    color: #333;
}

.candidate-checkbox {
    margin-left: auto;
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-success {
    background-color: #22863a;
    color: white;
}

.btn-success:hover:not(:disabled) {
    background-color: #1a6b2e;
}

.btn-success:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-top: 20px;
    border-left: 4px solid;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left-color: #dc3545;
}
</style>
