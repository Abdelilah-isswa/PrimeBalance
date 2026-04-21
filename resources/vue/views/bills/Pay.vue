<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>Pay Bill #{{ billId }}</h1>
      <div v-if="bill" style="margin: 0.75rem 0 1.25rem; color:#475569;">
        <div><strong>Total:</strong> {{ company?.currency }} {{ Number(bill.total_amount).toFixed(2) }}</div>
        <div><strong>Already Paid:</strong> {{ company?.currency }} {{ Number(bill.amount_paid || 0).toFixed(2) }}</div>
        <div><strong>Remaining:</strong> {{ company?.currency }} {{ remaining.toFixed(2) }}</div>
      </div>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;">
          <label>Amount to Pay:</label><br>
          <input v-model.number="form.amount" type="number" step="0.01" min="0.01" :max="remaining" required style="width:100%; padding:0.5rem;" />
        </div>
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
        <button type="submit" :disabled="remaining <= 0">Confirm Payment</button>
        <router-link :to="`/companies/${id}/bills/${billId}`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
      <p v-if="remaining <= 0" style="margin-top:1rem; color:#059669; font-weight:700;">This bill is already fully paid.</p>
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
const billId = route.params.billId;
const accounts = ref([]);
const categories = ref([]);
const company = ref(null);
const bill = ref(null);
const remaining = ref(0);
const form = ref({ amount: 0, account_id: '', category_id: '', date: new Date().toISOString().substring(0,10) });

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/bills/${billId}/pay`);
  accounts.value = res.data.data.accounts;
  categories.value = res.data.data.categories;

  // Load bill details so we can enforce remaining
  const billRes = await axios.get(`/companies/${id}/bills/${billId}`);
  company.value = billRes.data.data.company;
  bill.value = billRes.data.data.bill;
  remaining.value = Number(bill.value.total_amount) - Number(bill.value.amount_paid || 0);
  form.value.amount = remaining.value > 0 ? Number(remaining.value.toFixed(2)) : 0;
});

const submit = async () => {
  await axios.post(`/companies/${id}/bills/${billId}/pay`, form.value);
  router.push(`/companies/${id}/bills`);
};
</script>
