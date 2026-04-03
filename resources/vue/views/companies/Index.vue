<template>
  <div class="container mx-auto p-6">
    <h1>Companies</h1>
    <router-link to="/companies/create" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Create Company</router-link>
    <div v-if="companies.length" class="space-y-4">
      <div v-for="company in companies" :key="company.id" class="p-6 bg-white shadow rounded">
        <h2 class="text-xl font-bold">{{ company.name }}</h2>
        <router-link :to="`/companies/${company.id}`" class="text-blue-500 hover:underline">View</router-link>
      </div>
    </div>
    <p v-else>No companies. Create one to get started.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const companies = ref([]);

onMounted(async () => {
  try {
    const response = await axios.get('/companies');
    companies.value = response.data.data;
  } catch (error) {
    console.error('Error fetching companies', error);
  }
});
</script>
