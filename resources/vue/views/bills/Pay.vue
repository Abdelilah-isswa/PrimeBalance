<template>
  <div>
    <NavBar />
    <div style="padding:2rem; max-width:600px;">
      <h1>Pay Bill #{{ billId }}</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;">
          <label>Account:</label><br>
          <select v-model="form.account_id" required style="width:100%; padding:0.5rem;">
            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ Number(acc.balance).toFixed(2) }})</option>
          </select>
        </div>
        <div style="margin-bottom:1rem;">
          <label>Category:</label><br>
          <select v-model="form.category_id" style="width:100%; padding:0.5rem;">
            <option value="">None</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div style="margin-bottom:1rem;"><label>Date:</label><br><input v-model="form.date" type="date" required style="width:100%; padding:0.5rem;"></div>
        <button type="submit">Confirm Payment</button>
        <router-link :to="`/companies/${id}/bills/${billId}`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import NavBar from '../../components/NavBar.vue';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const billId = route.params.billId;
const accounts = ref([]);
const categories = ref([]);
const form = ref({ account_id: '', category_id: '', date: new Date().toISOString().substring(0,10) });

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/bills/${billId}/pay`);
  accounts.value = res.data.data.accounts;
  categories.value = res.data.data.categories;
});

const submit = async () => {
  await axios.post(`/companies/${id}/bills/${billId}/pay`, form.value);
  router.push(`/companies/${id}/bills`);
};
</script>
