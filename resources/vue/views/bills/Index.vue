<template>
  <div>
    <div style="padding:2rem;">
      <h1>Bills - {{ company?.name }}</h1>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">ID</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Supplier</th>
            <th style="padding:0.5rem; text-align:right; border:1px solid #ddd;">Amount</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Status</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Date</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="bill in bills" :key="bill.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">#{{ bill.id }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ bill.supplier?.name }}</td>
            <td style="padding:0.5rem; text-align:right; border:1px solid #ddd;">{{ company?.currency }} {{ Number(bill.total_amount).toFixed(2) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ bill.status }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ bill.created_at?.substring(0,10) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <router-link :to="`/companies/${id}/bills/${bill.id}`"><button style="padding:0.25rem 0.5rem;">View</button></router-link>
            </td>
          </tr>
        </tbody>
      </table>
      <router-link :to="`/companies/${id}`"><button>Back</button></router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const id = route.params.companyId;
const company = ref(null);
const bills = ref([]);

onMounted(async () => {
  const res = await axios.get(`companies/${id}/bills`);
  company.value = res.data.data.company;
  bills.value = res.data.data.bills;
});
</script>
