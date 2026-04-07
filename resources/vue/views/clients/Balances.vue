<template>
  <div>
    <div style="padding:2rem;">
      <h1>Client Balances - {{ company?.name }}</h1>
      <table style="width:100%; border-collapse:collapse; margin:2rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Client</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Email</th>
            <th style="padding:0.5rem; text-align:right; border:1px solid #ddd;">Balance</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ client.name }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ client.email }}</td>
            <td style="padding:0.5rem; text-align:right; border:1px solid #ddd;" :style="{ color: client.balance < 0 ? '#c62828' : '#2e7d32', fontWeight:'bold' }">
              {{ company?.currency }} {{ Number(client.balance).toFixed(2) }}
            </td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <span v-if="client.balance < 0" style="color:#c62828;">Owes {{ company?.currency }} {{ Math.abs(client.balance).toFixed(2) }}</span>
              <span v-else-if="client.balance > 0" style="color:#2e7d32;">Overpaid</span>
              <span v-else style="color:#666;">Settled</span>
            </td>
          </tr>
        </tbody>
      </table>
      <router-link :to="`/companies/${id}`"><button>Back to Dashboard</button></router-link>
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
const clients = ref([]);

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/clients/balances`);
  company.value = res.data.data.company;
  clients.value = res.data.data.clients;
});
</script>
