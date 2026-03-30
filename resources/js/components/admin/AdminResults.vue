<template>
  <div>
    <h1 class="page-title">Election Results</h1>

    <div v-if="loading" class="loading">Loading results...</div>
    <div v-else-if="results.length > 0" class="results-container">
      <div v-for="election in results" :key="election.id" class="election-section">
        <h2 class="election-title">{{ election.title }}</h2>
        
        <div class="positions-grid">
          <div v-for="position in election.positions" :key="position.id" class="position-section">
            <h3 class="position-name">{{ position.name }}</h3>
            
            <div class="candidates-results">
              <div v-for="candidate in position.candidates" :key="candidate.id" class="candidate-result">
                <span class="candidate-name">{{ candidate.name }}</span>
                <span class="vote-count">{{ candidate.vote_count }} votes</span>
                <div class="progress-bar" :style="{ width: (candidate.percentage || 0) + '%' }"></div>
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
import { adminAPI } from '../../services/api.js';

export default {
  name: 'AdminResults',
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
        const response = await adminAPI.getResults();
        this.results = response.data.results || [];
      } catch (error) {
        console.error('Error loading results:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
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
  height: 20px;
  background: linear-gradient(90deg, #116b27, #22863a);
  border-radius: 4px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 8px;
}
</style>
