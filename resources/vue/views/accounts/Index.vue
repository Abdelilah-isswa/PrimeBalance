<template>
  <div>
    <div style="padding:2rem;">
      <h1>Accounts - {{ company?.name }}</h1>
      <router-link :to="`/companies/${id}/accounts/create`"><button style="margin-bottom:1rem;">Create Account</button></router-link>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Name</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Balance</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Status</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="account in accounts" :key="account.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ account.name }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ company?.currency }} {{ Number(account.balance).toFixed(2) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ account.is_active ? 'Active' : 'Inactive' }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <router-link :to="`/companies/${id}/accounts/${account.id}/edit`"><button style="padding:0.25rem 0.5rem;">Edit</button></router-link>
              <button @click="deleteOrArchive(account)" style="background:#c62828; padding:0.25rem 0.5rem; margin-left:0.5rem;">
                {{ account.transactions_count > 0 ? 'Archive' : 'Delete' }}
              </button>
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
const accounts = ref([]);

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/accounts`);
  company.value = res.data.data.company;
  accounts.value = res.data.data.accounts;
});

const deleteOrArchive = async (account) => {
  const msg = account.transactions_count > 0 ? 'Archive this account?' : 'Delete this account?';
  if (!confirm(msg)) return;
  await axios.delete(`/companies/${id}/accounts/${account.id}`);
  const res = await axios.get(`/companies/${id}/accounts`);
  accounts.value = res.data.data.accounts;
};
</script>
