<template>
  <div>
    <NavBar />
    <div style="padding:2rem; max-width:700px;">
      <h1>Invoice #{{ invoice?.id }}</h1>
      <div style="padding:1rem; background:#f5f5f5; border-radius:4px; margin:1rem 0;">
        <p><strong>Client:</strong> {{ invoice?.client?.name }}</p>
        <p><strong>Email:</strong> {{ invoice?.client?.email }}</p>
        <p><strong>Amount:</strong> {{ company?.currency }} {{ Number(invoice?.total_amount).toFixed(2) }}</p>
        <p><strong>Status:</strong> {{ invoice?.status }}</p>
        <p><strong>Created:</strong> {{ invoice?.created_at?.substring(0,10) }}</p>
      </div>
      <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a :href="`/api/v1/companies/${id}/invoices/${invoiceId}/pdf`" target="_blank"><button style="background:#1565c0; color:white;">Download PDF</button></a>
        <router-link :to="`/companies/${id}/invoices/${invoiceId}/edit`"><button>Edit</button></router-link>
        <router-link v-if="invoice?.status !== 'paid'" :to="`/companies/${id}/invoices/${invoiceId}/receive`"><button style="background:#2e7d32; color:white;">Receive Payment</button></router-link>
        <button @click="destroy" style="background:#c62828; color:white;">Delete</button>
      </div>
      <router-link :to="`/companies/${id}/invoices`"><button style="margin-top:1rem;">Back to Invoices</button></router-link>
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
const invoiceId = route.params.invoiceId;
const company = ref(null);
const invoice = ref(null);

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/invoices/${invoiceId}`);
  company.value = res.data.data.company;
  invoice.value = res.data.data.invoice;
});

const destroy = async () => {
  if (!confirm('Delete this invoice?')) return;
  try {
    await axios.delete(`/companies/${id}/invoices/${invoiceId}`);
    router.push(`/companies/${id}/invoices`);
  } catch (err) {
    alert(err.response?.data?.message || 'Error deleting invoice');
  }
};
</script>
