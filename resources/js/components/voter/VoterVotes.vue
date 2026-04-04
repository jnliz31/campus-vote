<template>
  <div>
    <h1 class="page-title">Your Votes</h1>

    <div v-if="votes.length > 0" class="votes-container">
      <div v-for="vote in votes" :key="vote.id" class="vote-card">
        <div class="vote-header">
          <h3 class="vote-election">{{ vote.election.title }}</h3>
          <span class="vote-date">{{ formatDate(vote.created_at) }}</span>
        </div>
        <div class="vote-body">
          <div v-for="position in vote.positions" :key="position.id" class="vote-item">
            <span class="position-name">{{ position.name }}: </span>
            <strong class="candidate-name">{{ position.selected_candidate }}</strong>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="empty-state">
      <p>You haven't voted yet.</p>
      <router-link to="/voter/vote" class="btn btn-primary">Cast Your Vote</router-link>
    </div>
  </div>
</template>

<script>
import { voterAPI } from '../../services/api.js';

export default {
  name: 'VoterVotes',
  data() {
    return {
      votes: [],
      loading: true,
    };
  },
  mounted() {
    this.loadVotes();
  },
  methods: {
    async loadVotes() {
      try {
        const response = await voterAPI.getVotes();
        this.votes = response.data.votes || [];
      } catch (error) {
        console.error('Error loading votes:', error);
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
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

.votes-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 20px;
}

.vote-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.vote-header {
  background: linear-gradient(135deg, #116b27, #22863a);
  color: white;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.vote-election {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.vote-date {
  font-size: 12px;
  opacity: 0.9;
}

.vote-body {
  padding: 20px;
}

.vote-item {
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.vote-item:last-child {
  border-bottom: none;
}

.position-name {
  font-size: 14px;
  color: #666;
}

.candidate-name {
  font-size: 14px;
  color: #116b27;
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
</style>
