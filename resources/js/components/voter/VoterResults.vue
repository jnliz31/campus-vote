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
                                            >{{ candidate.percentage }}%</span
                                        >
                                    </div>
                                    <span
                                        v-if="candidate.percentage <= 10"
                                        class="percentage-outside"
                                        >{{ candidate.percentage }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="empty-state">
            <p>No election results available yet.</p>
        </div>
    </div>
</template>

<script>
import { voterAPI } from "../../services/api.js";

export default {
    name: "VoterResults",
    data() {
        return {
            results: [],
            loading: true,
        };
    },
    mounted() {
        this.loadResults();
    },
    methods: {
        async loadResults() {
            try {
                const response = await voterAPI.getResults();
                this.results = response.data.results || [];
            } catch (error) {
                console.error("Error loading results:", error);
            } finally {
                this.loading = false;
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
    font-size: 16px;
}

.results-container {
    display: grid;
    gap: 40px;
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
    gap: 30px;
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
    display: grid;
    gap: 8px;
}

.result-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.candidate-name {
    font-size: 15px;
    font-weight: 500;
    color: #333;
}

.vote-count {
    font-size: 13px;
    color: #666;
}

.progress-bar {
    height: 28px;
    background-color: #f0f0f0;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
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
    min-width: 0;
    box-shadow: 0 2px 4px rgba(17, 107, 39, 0.3);
}

.percentage {
    font-size: 13px;
    font-weight: 700;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
}

.percentage-outside {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
}

.empty-state p {
    font-size: 18px;
    color: #666;
}
</style>
