<template>
  <div style="min-height:100vh; background:#f0f2f5; display:flex; align-items:center; justify-content:center;">
    <div class="card" style="max-width:480px; width:100%; padding:2.5rem; text-align:center;">

      <!-- Loading -->
      <div v-if="loading">
        <p style="color:#64748b;">Loading invitation...</p>
      </div>

      <!-- Expired / Not Found -->
      <div v-else-if="error">
        <div style="margin-bottom:1rem; display:flex; justify-content:center;">
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <h2 style="color:#dc2626; margin-bottom:0.5rem;">Invitation Invalid</h2>
        <p style="color:#64748b;">{{ error }}</p>
        <router-link to="/"><button style="margin-top:1.5rem;">Go Home</button></router-link>
      </div>

      <!-- Invitation Details -->
      <div v-else-if="invitation">
        <div style="margin-bottom:1rem; display:flex; justify-content:center;">
          <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <h1 style="margin-bottom:0.5rem;">You're Invited!</h1>
        <p style="color:#64748b; margin-bottom:1.5rem;">
          You have been invited to join <strong style="color:#1a1a2e;">{{ invitation.company?.name }}</strong>
          as a <strong style="color:#4f46e5;">{{ invitation.role?.replace('_', ' ') }}</strong>.
        </p>

        <div class="card" style="background:#f8fafc; padding:1rem; margin-bottom:1.5rem; text-align:left;">
          <p style="margin:0.25rem 0; font-size:0.9rem;"><strong>Company:</strong> {{ invitation.company?.name }}</p>
          <p style="margin:0.25rem 0; font-size:0.9rem;"><strong>Role:</strong> {{ invitation.role?.replace('_', ' ') }}</p>
        </div>

        <!-- Not logged in -->
        <div v-if="!authStore.token">
          <p style="color:#64748b; font-size:0.9rem; margin-bottom:1rem;">Please login or register to accept this invitation.</p>
          <div style="display:flex; gap:1rem; justify-content:center;">
            <router-link :to="`/login?invitation=${token}`"><button>Login</button></router-link>
            <router-link :to="`/register?invitation=${token}`"><button class="btn-secondary">Register</button></router-link>
          </div>
        </div>

        <!-- Logged in -->
        <div v-else style="display:flex; gap:1rem; justify-content:center;">
          <button @click="accept" class="btn-success" :disabled="processing">
            {{ processing ? 'Processing...' : 'Accept Invitation' }}
          </button>
          <button @click="decline" class="btn-danger" :disabled="processing">Decline</button>
        </div>

        <div v-if="actionError" class="alert-error" style="margin-top:1rem;">{{ actionError }}</div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth.js';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const token = route.params.token;
const invitation = ref(null);
const loading = ref(true);
const error = ref('');
const actionError = ref('');
const processing = ref(false);

onMounted(async () => {
  try {
    const res = await axios.get(`/invitations/${token}`);
    invitation.value = res.data.data;
  } catch (err) {
    error.value = err.response?.data?.message || 'Invitation not found or expired';
  } finally {
    loading.value = false;
  }
});

const accept = async () => {
  processing.value = true;
  try {
    const res = await axios.post(`/invitations/${token}/accept`);
    router.push(res.data.data.redirect || '/dashboard');
  } catch (err) {
    actionError.value = err.response?.data?.message || 'Error accepting invitation';
  } finally {
    processing.value = false;
  }
};

const decline = async () => {
  processing.value = true;
  try {
    await axios.post(`/invitations/${token}/decline`);
    router.push('/');
  } catch (err) {
    actionError.value = err.response?.data?.message || 'Error declining invitation';
  } finally {
    processing.value = false;
  }
};
</script>
