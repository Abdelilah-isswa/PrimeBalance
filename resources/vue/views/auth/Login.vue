<template>
  <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:#f0f2f5;">
    <div class="card" style="width:100%; max-width:420px; padding:2.5rem;">
      <h1 style="text-align:center; margin-bottom:1.5rem;">Welcome Back</h1>
      <form @submit.prevent="login">
        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" placeholder="••••••••" required>
        </div>
        <button type="submit" style="width:100%; margin-top:0.5rem;" :disabled="loading">{{ loading ? 'Logging in...' : 'Login' }}</button>
      </form>
      <div v-if="error" class="alert-error" style="margin-top:1rem;">{{ error }}</div>
      <p style="text-align:center; margin-top:1.5rem; font-size:0.9rem; color:#64748b;">
        Don't have an account? <router-link to="/register">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth.js';
import axios from 'axios';

const form = ref({ email: '', password: '' });
const loading = ref(false);
const error = ref('');
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const login = async () => {
  loading.value = true;
  error.value = '';
  try {
    await authStore.login(form.value);
    const invitation = route.query.invitation;
    
    router.push(invitation ? `/invitations/${invitation}` : '/dashboard');
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed';
  } finally {
    loading.value = false;
  }
};
</script>
