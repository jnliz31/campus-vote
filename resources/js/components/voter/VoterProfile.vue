<template>
  <div>
    <h1 class="page-title">My Profile</h1>

    <div v-if="loading" class="loading">Loading profile...</div>
    <div v-else class="profile-container">
      <div class="profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            {{ voter.name.charAt(0).toUpperCase() }}
          </div>
          <div class="profile-info">
            <h2 class="profile-name">{{ voter.name }}</h2>
            <p class="profile-email">{{ voter.email }}</p>
          </div>
        </div>

        <div class="profile-details">
          <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ voter.email }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Student ID:</span>
            <span class="detail-value">{{ voter.student_id || 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Member Since:</span>
            <span class="detail-value">{{ formatDate(voter.created_at) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Total Votes Cast:</span>
            <span class="detail-value">{{ voter.votes_count || 0 }}</span>
          </div>
        </div>

        <div class="profile-actions">
          <button @click="editProfile" class="btn btn-primary">Edit Profile</button>
          <button @click="changePassword" class="btn btn-secondary">Change Password</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { voterAPI } from '../../services/api.js';

export default {
  name: 'VoterProfile',
  data() {
    return {
      voter: {
        name: '',
        email: '',
        student_id: '',
        created_at: new Date(),
        votes_count: 0
      },
      loading: true,
    };
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    async loadProfile() {
      try {
        const response = await voterAPI.getProfile();
        this.voter = response.data.voter || this.voter;
      } catch (error) {
        console.error('Error loading profile:', error);
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
    },
    editProfile() {
      alert('Profile editing feature coming soon');
    },
    changePassword() {
      alert('Password change feature coming soon');
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

.profile-container {
  max-width: 600px;
}

.profile-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.profile-header {
  background: linear-gradient(135deg, #116b27, #22863a);
  color: white;
  padding: 30px;
  display: flex;
  align-items: center;
  gap: 20px;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  font-weight: 600;
  flex-shrink: 0;
}

.profile-info {
  flex: 1;
}

.profile-name {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
}

.profile-email {
  margin: 5px 0 0 0;
  opacity: 0.9;
  font-size: 14px;
}

.profile-details {
  padding: 30px;
  border-bottom: 1px solid #eee;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: #333;
  font-size: 14px;
}

.detail-value {
  color: #666;
  font-size: 14px;
}

.profile-actions {
  padding: 20px 30px;
  display: flex;
  gap: 10px;
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
  flex: 1;
  text-align: center;
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
