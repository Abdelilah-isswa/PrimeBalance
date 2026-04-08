<template>
  <div class="pb-settings-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Account Settings</h1>
        <p class="pb-page-subtitle">Manage your personal information and account preferences</p>
      </div>
    </div>

    <div class="pb-card pb-form-card anim-slide-up">
      <div class="pb-card-header">
        <h2 class="pb-card-title">Profile Information</h2>
        <p class="pb-card-subtitle">Update your account's profile information and email address.</p>
      </div>
      
      <form @submit.prevent="updateProfile" class="pb-form">
        <div v-if="successMessage" class="pb-alert pb-alert-success">
          {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="pb-alert pb-alert-error">
          {{ errorMessage }}
        </div>

        <div class="pb-form-grid">
          <div class="pb-form-group pb-col-full">
            <label class="pb-label">Full Name</label>
            <input v-model="form.name" type="text" class="pb-input" required>
          </div>

          <div class="pb-form-group pb-col-full">
            <label class="pb-label">Email Address</label>
            <input v-model="form.email" type="email" class="pb-input" required>
          </div>
        </div>

        <div class="pb-form-actions">
          <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
            {{ submitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth.js';
import axios from 'axios';

const authStore = useAuthStore();
const form = ref({ name: '', email: '' });
const submitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

onMounted(() => {
  if (authStore.user) {
    form.value.name = authStore.user.name;
    form.value.email = authStore.user.email;
  }
});

const updateProfile = async () => {
  submitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  
  try {
    const res = await axios.put('/user', form.value);
    authStore.user = res.data.data;
    successMessage.value = 'Profile updated successfully!';
    setTimeout(() => { successMessage.value = ''; }, 3000);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to update profile.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-settings-page {
  animation: fadeIn 0.4s ease-out;
  max-width: 800px;
  margin: 0 auto;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.pb-page-header {
  margin-bottom: 2rem;
}

.pb-header-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.pb-page-title {
  font-size: 28px;
  font-weight: 800;
  color: #1a1a2e;
  letter-spacing: -0.5px;
  margin: 0;
}

.pb-page-subtitle {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 1.5rem;
}

/* Card */
.pb-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow: hidden;
}

/* Form Styles */
.pb-card-header {
  padding: 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.pb-card-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 8px 0;
  color: #1a1a2e;
}

.pb-card-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.pb-form {
  padding: 2rem;
}

.pb-form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.pb-form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pb-label {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
}

.pb-input {
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  transition: all 0.2s;
  background: #f8fafc;
}

.pb-input:focus {
  outline: none;
  border-color: #4f46e5;
  background: white;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.pb-form-actions {
  display: flex;
  justify-content: flex-end;
  padding-top: 1.5rem;
  border-top: 1px solid #f1f5f9;
}

.pb-btn {
  padding: 12px 24px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.pb-btn-primary {
  background: #4f46e5;
  color: white;
}

.pb-btn-primary:hover:not(:disabled) {
  background: #4338ca;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* Alerts */
.pb-alert {
  padding: 1rem;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  font-size: 14px;
  font-weight: 500;
}

.pb-alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.pb-alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.anim-slide-up {
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
