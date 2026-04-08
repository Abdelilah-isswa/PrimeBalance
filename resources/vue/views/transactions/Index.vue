<template>
  <div>
    <div style="padding:2rem;">
      <h1>Transactions - {{ company?.name }}</h1>
      <router-link :to="`/companies/${id}/transactions/create`"><button style="margin-bottom:1rem;">Add Transaction</button></router-link>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Date</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Type</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Description</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Account</th>
            <th style="padding:0.5rem; text-align:right; border:1px solid #ddd;">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tx in transactions" :key="tx.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ tx.date?.substring(0,10) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;" :style="{ color: tx.type === 'income' ? '#2e7d32' : '#c62828' }">{{ tx.type }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ tx.description }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ tx.account?.name }}</td>
            <td style="padding:0.5rem; text-align:right; border:1px solid #ddd;" :style="{ color: tx.type === 'income' ? '#2e7d32' : '#c62828' }">
              {{ tx.type === 'income' ? '+' : '-' }} {{ company?.currency }} {{ Number(tx.amount).toFixed(2) }}
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
const transactions = ref([]);

onMounted(async () => {
  const res = await axios.get(`companies/${id}/transactions`);
  company.value = res.data.data?.company || null;
  transactions.value = res.data.data?.transactions || [];
});
</script>
