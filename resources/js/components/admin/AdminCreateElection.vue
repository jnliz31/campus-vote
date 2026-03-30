<template>
  <div>
    <h1 class="page-title">Create Election</h1>

    <div class="form-container">
      <form @submit.prevent="createElection">
        <div class="form-group">
          <label for="title">Election Title:</label>
          <input v-model="form.title" type="text" id="title"  required>
        </div>

        <div class="form-group">
          <label for="description">Description:</label>
          <textarea v-model="form.description" id="description" rows="4"></textarea>
        </div>

        <div class="form-group">
          <h3>Add Positions</h3>
          <div v-for="(pos, idx) in form.positions" :key="idx" class="position-input">
            <input v-model="pos.name" type="text" placeholder="Position Name" required>
            <button type="button" @click="removePosition(idx)" class="btn btn-sm btn-danger">Remove</button>
          </div>
          <button type="button" @click="addPosition" class="btn btn-sm btn-secondary">Add Position</button>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Creating...' : 'Create Election' }}
          </button>
          <router-link to="/admin/elections" class="btn btn-secondary">Cancel</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { adminAPI } from '../../services/api.js';

export default {
  name: 'AdminCreateElection',
  data() {
    return {
      form: {
        title: '',
        description: '',
        positions: [{ name: '' }],
      },
      loading: false,
    };
  },
  methods: {
    addPosition() {
      this.form.positions.push({ name: '' });
    },
    removePosition(idx) {
      this.form.positions.splice(idx, 1);
    },
    async createElection() {
      if (!this.form.title) {
        alert('Please enter election title');
        return;
      }

      this.loading = true;

      try {
        const formData = {
          title: this.form.title,
          description: this.form.description,
          positions: this.form.positions.map(p => ({ name: p.name })).filter(p => p.name),
        };

        const response = await adminAPI.createElection(formData);
        
        this.$router.push('/admin/elections');
      } catch (error) {
        alert('Failed to create election');
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

.position-input {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

.position-input input {
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
</style>
