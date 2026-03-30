<template>
  <div>
    <h1 class="page-title">Dashboard</h1>

    <div v-if="loading" class="loading">Loading dashboard...</div>
    <div v-else>
      <div class="stats-grid">
        <div class="stat-card green">
          <div class="stat-number">{{ stats.total_elections }}</div>
          <div class="stat-label">Total Elections</div>
        </div>
        <div class="stat-card blue">
          <div class="stat-number">{{ stats.active_elections }}</div>
          <div class="stat-label">Active Elections</div>
        </div>
        <div class="stat-card orange">
          <div class="stat-number">{{ stats.total_voters }}</div>
          <div class="stat-label">Registered Voters</div>
        </div>
        <div class="stat-card red">
          <div class="stat-number">{{ stats.total_votes }}</div>
          <div class="stat-label">Total Votes Cast</div>
        </div>
      </div>

      <div class="actions-section">
        <router-link to="/admin/elections/create" class="btn btn-primary">
          Create New Election
        </router-link>
        <router-link to="/admin/elections" class="btn btn-secondary">
          Manage Elections
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { adminAPI } from '../../services/api.js';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      stats: {
        total_elections: 0,
        active_elections: 0,
        total_voters: 0,
        total_votes: 0,
      },
      loading: true,
    };
  },
  mounted() {
    this.loadDashboard();
  },
  methods: {
    async loadDashboard() {
      try {
        const response = await adminAPI.getDashboard();
        this.stats = response.data.stats || this.stats;
      } catch (error) {
        console.error('Error loading dashboard:', error);
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
  font-size: 16px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-left: 4px solid;
}

.stat-card.green {
  border-left-color: #22863a;
}

.stat-card.blue {
  border-left-color: #0366d6;
}

.stat-card.red {
  border-left-color: #d73a49;
}

.stat-card.orange {
  border-left-color: #f9826c;
}

.stat-number {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 14px;
  color: #666;
  text-transform: uppercase;
}

.actions-section {
  display: flex;
  gap: 10px;
  margin-bottom: 30px;
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
</style>
