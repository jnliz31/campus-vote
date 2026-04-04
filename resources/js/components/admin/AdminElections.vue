<template>
    <div>
        <div class="page-header">
            <h1 class="page-title">Manage Elections</h1>
            <router-link to="/admin/elections/create" class="btn btn-primary"
                >Create New Election</router-link
            >
        </div>

        <div v-if="elections.length > 0" class="elections-container">
            <div
                v-for="election in elections"
                :key="election.id"
                class="election-card"
            >
                <div class="election-header">
                    <h3 class="election-title">{{ election.title }}</h3>
                    <span
                        :class="['status-badge', `status-${election.status}`]"
                        >{{ election.status }}</span
                    >
                </div>
                <div class="election-body">
                    <p>
                        <strong>Positions:</strong>
                        {{ election.positions_count }}
                    </p>
                    <p><strong>Votes:</strong> {{ election.votes_count }}</p>
                    <p>
                        <strong>Started:</strong>
                        {{ formatDate(election.created_at) }}
                    </p>
                </div>
                <div class="election-actions">
                    <router-link
                        :to="`/admin/elections/${election.id}/edit`"
                        class="btn btn-sm btn-secondary"
                        >Edit</router-link
                    >
                    <button
                        @click="endElection(election.id)"
                        v-if="election.status === 'active'"
                        class="btn btn-sm btn-warning"
                    >
                        End
                    </button>
                    <button
                        @click="deleteElection(election.id)"
                        class="btn btn-sm btn-danger"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="empty-state">
            <p>No elections created yet.</p>
            <router-link to="/admin/elections/create" class="btn btn-primary"
                >Create First Election</router-link
            >
        </div>
    </div>
</template>

<script>
import { adminAPI } from "../../services/api.js";

export default {
    name: "AdminElections",
    data() {
        return {
            elections: [],
            loading: true,
        };
    },
    mounted() {
        this.loadElections();
    },
    methods: {
        async loadElections() {
            try {
                const response = await adminAPI.getElections();
                this.elections = response.data.elections || [];
            } catch (error) {
                console.error("Error loading elections:", error);
            } finally {
                this.loading = false;
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        },
        async endElection(electionId) {
            if (!confirm("Are you sure you want to end this election?")) return;

            try {
                await adminAPI.endElection(electionId);
                this.loadElections();
            } catch (error) {
                alert("Failed to end election");
            }
        },
        async deleteElection(electionId) {
            if (
                !confirm(
                    "Are you sure you want to delete this election? This cannot be undone.",
                )
            )
                return;

            try {
                const response = await adminAPI.deleteElection(electionId);

                if (response.data.success) {
                    this.loadElections();
                } else {
                    alert(
                        "Failed to delete election: " +
                            (response.data.message || "Unknown error"),
                    );
                }
            } catch (error) {
                console.error("Error deleting election:", error);
                const errorMessage =
                    error.response?.data?.message ||
                    error.message ||
                    "Failed to delete election";
                alert(errorMessage);
            }
        },
    },
};
</script>

<style scoped>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-title {
    font-size: 32px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 16px;
}

.elections-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.election-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.election-header {
    background: linear-gradient(135deg, #116b27, #22863a);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 10px;
}

.election-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    flex: 1;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    background-color: rgba(255, 255, 255, 0.3);
}

.status-active {
    background-color: #28a745;
}

.status-ended {
    background-color: #6c757d;
}

.election-body {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.election-body p {
    margin: 8px 0;
    font-size: 14px;
    color: #666;
}

.election-actions {
    padding: 15px 20px;
    display: flex;
    gap: 8px;
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

.btn-primary {
    background-color: #0366d6;
    color: white;
}

.btn-primary:hover {
    background-color: #0256c1;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-warning {
    background-color: #ffc107;
    color: #333;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-danger {
    background-color: #d73a49;
    color: white;
}

.btn-danger:hover {
    background-color: #cb2431;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    flex: 1;
    text-align: center;
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
    margin-bottom: 20px;
}
</style>
