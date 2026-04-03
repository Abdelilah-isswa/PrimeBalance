<template>
  <div>
    <NavBar />
    <div style="padding:2rem; max-width:700px;">
      <h1>Bill #{{ bill?.id }}</h1>
      <div style="padding:1rem; background:#f5f5f5; border-radius:4px; margin:1rem 0;">
        <p><strong>Supplier:</strong> {{ bill?.supplier?.name }}</p>
        <p><strong>Amount:</strong> {{ company?.currency }} {{ Number(bill?.total_amount).toFixed(2) }}</p>
        <p><strong>Status:</strong> {{ bill?.status }}</p>
        <p><strong>Created:</strong> {{ bill?.created_at?.substring(0,10) }}</p>
      </div>
      <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <router-link :to="`/companies/${id}/bills/${billId}/edit`"><button>Edit</button></router-link>
        <router-link v-if="bill?.status !== 'paid'" :to="`/companies/${id}/bills/${billId}/pay`"><button style="background:#2e7d32; color:white;">Pay Bill</button></router-link>
        <button @click="destroy" style="background:#c62828; color:white;">Delete</button>
      </div>
      <router-link :to="`/companies/${id}/bills`"><button style="margin-top:1rem;">Back to Bills</button></router-link>
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
const company = ref(null);
const bill = ref(null);

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/bills/${billId}`);
  company.value = res.data.data.company;
  bill.value = res.data.data.bill;
});

const destroy = async () => {
  if (!confirm('Delete this bill?')) return;
  await axios.delete(`/companies/${id}/bills/${billId}`);
  router.push(`/companies/${id}/bills`);
};
</script>
