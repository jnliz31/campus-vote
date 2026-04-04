<template>
    <div>
        <h1 class="page-title">Edit Election</h1>

        <div class="form-container">
            <form @submit.prevent="updateElection">
                <div class="form-group">
                    <label for="title">Election Title:</label>
                    <input
                        v-model="form.title"
                        type="text"
                        id="title"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea
                        v-model="form.description"
                        id="description"
                        rows="4"
                    ></textarea>
                </div>

                <div class="form-group">
                    <h3>Edit Positions</h3>
                    <div
                        v-for="(pos, idx) in form.positions"
                        :key="idx"
                        class="position-container"
                    >
                        <div class="position-input">
                            <div class="position-name-group">
                                <input
                                    v-model="pos.name"
                                    type="text"
                                    placeholder="Position Name"
                                    required
                                />
                            </div>
                            <div class="max-votes-group">
                                <label
                                    for="max-votes-${idx}"
                                    class="max-votes-label"
                                    >Max Votes Allowed:</label
                                >
                                <input
                                    :id="`max-votes-${idx}`"
                                    v-model.number="pos.max_votes"
                                    type="number"
                                    placeholder="1"
                                    min="1"
                                    required
                                />
                            </div>
                            <button
                                type="button"
                                @click="removePosition(idx)"
                                class="btn btn-sm btn-danger"
                            >
                                Remove Position
                            </button>
                        </div>

                        <div class="candidates-section">
                            <h4>
                                Candidates for
                                {{ pos.name || "Position " + (idx + 1) }}
                            </h4>
                            <div
                                v-for="(candidate, cIdx) in pos.candidates"
                                :key="cIdx"
                                class="candidate-input"
                            >
                                <input
                                    v-model="candidate.name"
                                    type="text"
                                    placeholder="Candidate Name"
                                    required
                                />
                                <button
                                    type="button"
                                    @click="removeCandidate(idx, cIdx)"
                                    class="btn btn-sm btn-danger"
                                >
                                    Remove
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="addCandidate(idx)"
                                class="btn btn-sm btn-secondary"
                            >
                                Add Candidate
                            </button>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="addPosition"
                        class="btn btn-sm btn-secondary"
                    >
                        Add Position
                    </button>
                </div>

                <div class="form-actions">
                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="updating"
                    >
                        {{ updating ? "Updating..." : "Update Election" }}
                    </button>
                    <router-link to="/admin/elections" class="btn btn-secondary"
                        >Cancel</router-link
                    >
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { adminAPI } from "../../services/api.js";

export default {
    name: "AdminEditElection",
    data() {
        return {
            election: { title: "", description: "", positions: [] },
            form: {
                title: "",
                description: "",
                positions: [],
            },
            loading: true,
            updating: false,
        };
    },
    mounted() {
        this.loadElection();
    },
    methods: {
        async loadElection() {
            try {
                const id = this.$route.params.id;
                const response = await adminAPI.getElection(id);
                this.election = response.data.election || {};

                // Initialize form with election data
                this.form = {
                    title: this.election.title || "",
                    description: this.election.description || "",
                    positions: this.election.positions
                        ? this.election.positions.map((position) => ({
                              name: position.name,
                              max_votes: position.max_votes || 1,
                              candidates: position.candidates
                                  ? position.candidates.map((candidate) => ({
                                        name: candidate.name,
                                    }))
                                  : [{ name: "" }],
                          }))
                        : [
                              {
                                  name: "",
                                  max_votes: 1,
                                  candidates: [{ name: "" }],
                              },
                          ],
                };
            } catch (error) {
                console.error("Error loading election:", error);
                alert("Failed to load election");
                this.$router.push("/admin/elections");
            } finally {
                this.loading = false;
            }
        },
        addPosition() {
            this.form.positions.push({
                name: "",
                max_votes: 1,
                candidates: [{ name: "" }],
            });
        },
        removePosition(idx) {
            this.form.positions.splice(idx, 1);
        },
        addCandidate(positionIdx) {
            this.form.positions[positionIdx].candidates.push({ name: "" });
        },
        removeCandidate(positionIdx, candidateIdx) {
            this.form.positions[positionIdx].candidates.splice(candidateIdx, 1);
        },
        async updateElection() {
            if (!this.form.title) {
                alert("Please enter election title");
                return;
            }

            // Validate that each position has at least one candidate with a name
            for (let i = 0; i < this.form.positions.length; i++) {
                const position = this.form.positions[i];
                if (!position.name) {
                    alert(`Please enter a name for position ${i + 1}`);
                    return;
                }

                if (!position.max_votes || position.max_votes < 1) {
                    alert(
                        `Please enter a valid max votes for position: ${position.name}`,
                    );
                    return;
                }

                const validCandidates = position.candidates.filter((c) =>
                    c.name.trim(),
                );
                if (validCandidates.length === 0) {
                    alert(
                        `Please add at least one candidate for position: ${position.name}`,
                    );
                    return;
                }
            }

            this.updating = true;

            try {
                const formData = {
                    title: this.form.title,
                    description: this.form.description,
                    positions: this.form.positions
                        .map((p) => ({
                            name: p.name,
                            max_votes: p.max_votes,
                            candidates: p.candidates
                                .filter((c) => c.name.trim())
                                .map((c) => c.name.trim()),
                        }))
                        .filter((p) => p.name),
                };

                const response = await adminAPI.updateElection(
                    this.election.id,
                    formData,
                );

                alert("Election updated successfully!");
                this.$router.push("/admin/elections");
            } catch (error) {
                console.error("Error updating election:", error);
                alert(
                    "Failed to update election: " +
                        (error.response?.data?.message || error.message),
                );
            } finally {
                this.updating = false;
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

.form-container {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #116b27;
    box-shadow: 0 0 0 3px rgba(17, 107, 39, 0.1);
}

.position-container {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
}

.position-input {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    align-items: flex-start;
}

.position-name-group {
    flex: 2;
}

.position-name-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.position-name-group input:focus {
    outline: none;
    border-color: #116b27;
    box-shadow: 0 0 0 3px rgba(17, 107, 39, 0.1);
}

.max-votes-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 0.8;
}

.max-votes-label {
    font-weight: 600;
    font-size: 12px;
    color: #333;
    white-space: nowrap;
}

.max-votes-group input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.max-votes-group input:focus {
    outline: none;
    border-color: #116b27;
    box-shadow: 0 0 0 3px rgba(17, 107, 39, 0.1);
}

.candidates-section {
    margin-left: 20px;
}

.candidates-section h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #555;
}

.candidate-input {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
}

.candidate-input input {
    flex: 1;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
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
    flex: 1;
}

.btn-primary:hover:not(:disabled) {
    background-color: #0256c1;
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    flex: 1;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-danger {
    background-color: #d73a49;
    color: white;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
}
</style>
