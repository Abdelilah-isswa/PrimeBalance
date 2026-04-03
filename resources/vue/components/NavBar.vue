<template>
  <nav style="background:#1a1a2e; color:white; padding:0.75rem 2rem; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
    <div style="display:flex; gap:1.5rem; align-items:center;">
      <router-link to="/" style="color:white; font-weight:700; font-size:1.1rem; letter-spacing:0.5px;">FilRouge</router-link>
      <select v-if="companies.length" @change="switchCompany" style="width:auto; padding:0.4rem 0.6rem; background:#2d2d44; color:white; border:1px solid #4f46e5; border-radius:6px; font-size:0.85rem;">
        <option v-for="c in companies" :key="c.id" :value="c.id" :selected="c.id == currentCompanyId">{{ c.name }}</option>
      </select>
    </div>
    <div style="display:flex; gap:1rem; align-items:center;">
      <span style="font-size:0.85rem; color:#a5b4fc;">{{ authStore.user?.name }}</span>
      <button @click="logout" class="btn-danger btn-sm">Logout</button>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const companies = ref([]);
const currentCompanyId = ref(route.params.companyId || null);

onMounted(async () => {
  try {
    const res = await axios.get('/companies');
    companies.value = res.data.data;
  } catch {}
});

const switchCompany = (e) => {
  router.push(`/companies/${e.target.value}`);
};

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
