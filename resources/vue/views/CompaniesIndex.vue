<template>
  <div>
    <h1>Companies</h1>
    <button @click="createCompany">Create Company</button>
    <ul>
      <li v-for="company in companies" :key="company.id">
        <router-link :to="`/companies/${company.id}`">{{ company.name }}</router-link>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const companies = ref([]);
const router = useRouter();

onMounted(async () => {
  const response = await fetch('/api/v1/companies', {
    headers: {
      'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
  });
  const data = await response.json();
  companies.value = data.data;
});

const createCompany = () => router.push('/companies/create');
