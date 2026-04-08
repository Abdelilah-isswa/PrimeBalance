<template>
  <div>
    <div style="padding:2rem;">
      <h1>Invoices - {{ company?.name }}</h1>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">ID</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Client</th>
            <th style="padding:0.5rem; text-align:right; border:1px solid #ddd;">Amount</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Status</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Date</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="inv in invoices" :key="inv.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">#{{ inv.id }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ inv.client?.name }}</td>
            <td style="padding:0.5rem; text-align:right; border:1px solid #ddd;">{{ company?.currency }} {{ Number(inv.total_amount).toFixed(2) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ inv.status }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ inv.created_at?.substring(0,10) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <router-link :to="`/companies/${id}/invoices/${inv.id}`"><button style="padding:0.25rem 0.5rem;">View</button></router-link>
            </td>
          </tr>
        </tbody>
      </table>
      <router-link :to="`/companies/${id}`"><button>Back</button></router-link>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCompanyStore } from '../../stores/company.js';
import { useInvoiceStore } from '../../stores/invoice.js';

const route = useRoute();
const id = route.params.companyId;
const companyStore = useCompanyStore();
const invoiceStore = useInvoiceStore();

const invoices = computed(() => invoiceStore.invoices);
const company = computed(() => companyStore.currentCompany);

onMounted(async () => {
  await companyStore.fetchCompany(id);
  await invoiceStore.fetchInvoices(id);
});
</script>
