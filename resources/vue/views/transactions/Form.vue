<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>Add Transaction</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;">
          <label>Type:</label><br>
          <select v-model="form.type" style="width:100%; padding:0.5rem;">
            <option value="income">Income</option>
            <option value="expense">Expense</option>
          </select>
        </div>
        <div style="margin-bottom:1rem;">
          <label>Account:</label><br>
          <select v-model="form.account_id" required style="width:100%; padding:0.5rem;">
            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
          </select>
        </div>
        <div style="margin-bottom:1rem;">
          <label>Category:</label><br>
          <select v-model="form.category_id" style="width:100%; padding:0.5rem;">
            <option value="">None</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div style="margin-bottom:1rem;"><label>Amount:</label><br><input v-model="form.amount" type="number" step="0.01" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;"><label>Description:</label><br><input v-model="form.description" style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;"><label>Date:</label><br><input v-model="form.date" type="date" required style="width:100%; padding:0.5rem;"></div>
        <button type="submit">Create</button>
        <router-link :to="`/companies/${id}/transactions`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const accounts = ref([]);
const categories = ref([]);
const form = ref({ type: 'income', account_id: '', category_id: '', amount: '', description: '', date: new Date().toISOString().substring(0,10) });

onMounted(async () => {
  const [accRes, catRes] = await Promise.all([
    axios.get(`/companies/${id}/accounts`),
    axios.get(`/companies/${id}/categories`),
  ]);
  accounts.value = accRes.data.data.accounts;
  categories.value = catRes.data.data.categories;
});

const submit = async () => {
  await axios.post(`/companies/${id}/transactions`, form.value);
  router.push(`/companies/${id}/transactions`);
};
</script>
