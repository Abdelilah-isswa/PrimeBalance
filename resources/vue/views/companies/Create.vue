<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>Create Company</h1>
      <form @submit.prevent="createCompany">
        <div style="margin-bottom:1rem;"><label>Name:</label><br><input v-model="form.name" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;"><label>Address:</label><br><input v-model="form.address" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;">
          <label>Currency:</label><br>
          <select v-model="form.currency" required style="width:100%; padding:0.5rem;">
            <option value="USD">USD - US Dollar</option>
            <option value="EUR">EUR - Euro</option>
            <option value="GBP">GBP - British Pound</option>
            <option value="MAD">MAD - Moroccan Dirham</option>
            <option value="JPY">JPY - Japanese Yen</option>
            <option value="CAD">CAD - Canadian Dollar</option>
          </select>
        </div>
        <button type="submit" :disabled="loading">Create</button>
        <router-link to="/companies"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
      <p v-if="error" style="color:red; margin-top:1rem;">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const form = ref({ name: '', address: '', currency: 'USD' });
const loading = ref(false);
const error = ref('');

const createCompany = async () => {
  loading.value = true;
  error.value = '';
  try {
    await axios.post('/companies', form.value);
    router.push('/companies');
  } catch (err) {
    error.value = err.response?.data?.message || 'Error creating company';
  } finally {
    loading.value = false;
  }
};
</script>
