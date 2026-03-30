<template>
  <div>
    <h1 class="page-title">Edit Election</h1>
    <div v-if="loading" class="loading">Loading election...</div>
    <div v-else class="form-container">
      <p>Election: {{ election.title }}</p>
      <p>Edit functionality coming soon</p>
      <router-link to="/admin/elections" class="btn btn-secondary">Back to Elections</router-link>
    </div>
  </div>
</template>

<script>
import { adminAPI } from '../../services/api.js';

export default {
  name: 'AdminEditElection',
  data() {
    return {
      election: { title: '' },
      loading: true,
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
      } catch (error) {
        console.error('Error loading election:', error);
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

.form-container {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  max-width: 600px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  margin-top: 20px;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}
</style>
