<template>
    <div>
        <h1 class="page-title">Election Results</h1>

        <div v-if="results.length > 0" class="results-container">
            <div
                v-for="election in results"
                :key="election.id"
                class="election-section"
            >
                <h2 class="election-title">{{ election.title }}</h2>

                <div class="positions-grid">
                    <div
                        v-for="position in election.positions"
                        :key="position.id"
                        class="position-section"
                    >
                        <h3 class="position-name">{{ position.name }}</h3>

                        <div class="candidates-results">
                            <div
                                v-for="candidate in position.candidates"
                                :key="candidate.id"
                                class="candidate-result"
                            >
                                <div class="result-info">
                                    <span class="candidate-name">{{
                                        candidate.name
                                    }}</span>
                                    <span class="vote-count"
                                        >{{ candidate.vote_count }} votes</span
                                    >
                                </div>
                                <div class="progress-bar">
                                    <div
                                        class="progress-fill"
                                        :style="{
                                            width:
                                                (candidate.percentage || 0) +
                                                '%',
                                        }"
                                    >
                                        <span
                                            v-if="candidate.percentage > 10"
                                            class="percentage"
                                            >{{
                                                Math.round(
                                                    candidate.percentage,
                                                )
                                            }}%</span
                                        >
                                    </div>
                                    <span
                                        v-if="candidate.percentage <= 10"
                                        class="percentage-outside"
                                        >{{
                                            Math.round(candidate.percentage)
                                        }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="empty-state">
            <p>No election results available.</p>
        </div>
    </div>
</template>

<script>
import { useElectionStore } from "../../stores/electionStore.js";

export default {
    name: "AdminResults",
    setup() {
        const electionStore = useElectionStore();
        return { electionStore };
    },
    computed: {
        results() {
            return this.electionStore.results || [];
        },
        loading() {
            return this.electionStore.isLoading;
        },
    },
    async mounted() {
        await this.loadResults();
    },
    methods: {
        async loadResults() {
            try {
                await this.electionStore.loadAdminResults();
            } catch (error) {
                console.error("Error loading results:", error);
            }
        },
    },
};
</script>

<style scoped>
.page-title {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 30px;
    color: #333;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.results-container {
    display: grid;
    gap: 30px;
}

.election-section {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.election-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #116b27;
}

.positions-grid {
    display: grid;
    gap: 20px;
}

.position-section {
    border: 1px solid #eee;
    padding: 20px;
    border-radius: 6px;
}

.position-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.candidates-results {
    display: grid;
    gap: 15px;
}

.candidate-result {
    display: flex;
    gap: 15px;
    align-items: center;
}

.result-info {
    display: flex;
    gap: 15px;
    align-items: center;
    min-width: 280px;
}

.candidate-name {
    width: 200px;
    font-size: 14px;
    font-weight: 500;
}

.vote-count {
    width: 80px;
    font-size: 13px;
    color: #666;
}

.progress-bar {
    flex: 1;
    height: 28px;
    background: #f0f0f0;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    min-width: 150px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #116b27, #22863a);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    transition: width 0.5s ease;
    box-shadow: 0 2px 4px rgba(17, 107, 39, 0.3);
}

.percentage {
    color: white;
    font-size: 13px;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
}

.percentage-outside {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #333;
    font-size: 13px;
    font-weight: 700;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
}
</style>
