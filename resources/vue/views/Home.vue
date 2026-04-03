<template>
  <div class="container mx-auto p-6">
    <h1>Welcome to FilRouge</h1>
    <div v-if="companies.length">
      <router-link v-for="company in companies" :key="company.id" :to="`/companies/${company.id}`" class="block p-4 bg-white shadow rounded mb-4">
        {{ company.name }}
      </router-link>
    </div>
    <router-link v-else to="/companies/create" class="bg-blue-500 text-white px-4 py-2 rounded">Create Company</router-link>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth.js';

const companies = ref([]);
const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
  try {
    const response = await axios.get('/companies');
    companies.value = response.data.data;
  } catch (error) {
    if (error.response.status === 401) router.push('/login');
  }
});
</script>
