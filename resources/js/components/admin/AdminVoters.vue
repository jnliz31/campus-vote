<template>
  <div>
    <h1 class="page-title">Voters Management</h1>

    <div v-if="loading" class="loading">Loading voters...</div>
    <div v-else-if="voters.length > 0" class="voters-container">
      <table class="voters-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="voter in voters" :key="voter.id">
            <td>{{ voter.name }}</td>
            <td>{{ voter.email }}</td>
            <td>{{ formatDate(voter.created_at) }}</td>
            <td>
              <button @click="deleteVoter(voter.id)" class="btn btn-sm btn-danger">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="empty-state">
      <p>No voters found.</p>
    </div>
  </div>
</template>

<script>
import { adminAPI } from '../../services/api.js';

export default {
  name: 'AdminVoters',
  data() {
    return {
      voters: [],
      loading: true,
    };
  },
  mounted() {
    this.loadVoters();
  },
  methods: {
    async loadVoters() {
      try {
        const response = await adminAPI.getVoters();
        this.voters = response.data.voters || [];
      } catch (error) {
        console.error('Error loading voters:', error);
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    async deleteVoter(voterId) {
      if (!confirm('Are you sure?')) return;
      
      try {
        await adminAPI.deleteVoter(voterId);
        this.loadVoters();
      } catch (error) {
        alert('Failed to delete voter');
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

.voters-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.voters-table {
  width: 100%;
  border-collapse: collapse;
}

.voters-table thead {
  background-color: #f8f9fa;
  border-bottom: 2px solid #ddd;
}

.voters-table th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: #333;
}

.voters-table td {
  padding: 15px;
  border-bottom: 1px solid #ddd;
}

.voters-table tbody tr:hover {
  background-color: #f8f9fa;
}

.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
}

.btn-danger {
  background-color: #d73a49;
  color: white;
}

.btn-danger:hover {
  background-color: #cb2431;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 8px;
}
</style>
