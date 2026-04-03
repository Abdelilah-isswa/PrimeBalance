<template>
  <div>
    <NavBar />
    <div style="padding:2rem;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1>{{ company?.name }} <span v-if="company?.end_date" style="color:red; font-size:1rem;">(Deactivated)</span></h1>
        <div v-if="isOwner && !company?.end_date" style="display:flex; gap:0.5rem;">
          <router-link :to="`/companies/${id}/edit`"><button>Manage Company</button></router-link>
          <router-link :to="`/companies/${id}/documents`"><button>Documents</button></router-link>
          <router-link :to="`/companies/${id}/transactions`"><button>Transactions</button></router-link>
        </div>
      </div>

      <!-- Date Filter -->
      <div style="display:flex; gap:0.5rem; margin:1rem 0; align-items:flex-end;">
        <div><label>From:</label><br><input type="date" v-model="startDate"></div>
        <div><label>To:</label><br><input type="date" v-model="endDate"></div>
        <button @click="loadDashboard">Filter</button>
        <button @click="clearFilter" style="background:#666;">Clear</button>
      </div>

      <!-- Metrics -->
      <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin:1rem 0;">
        <div style="padding:1rem; background:#e8f5e9; border-radius:4px;">
          <h3 style="margin:0 0 0.5rem; color:#2e7d32;">Total Income</h3>
          <p style="margin:0; font-size:1.5rem; font-weight:bold; color:#2e7d32;">{{ company?.currency }} {{ fmt(metrics.totalIncome) }}</p>
        </div>
        <div style="padding:1rem; background:#ffebee; border-radius:4px;">
          <h3 style="margin:0 0 0.5rem; color:#c62828;">Total Expense</h3>
          <p style="margin:0; font-size:1.5rem; font-weight:bold; color:#c62828;">{{ company?.currency }} {{ fmt(metrics.totalExpense) }}</p>
        </div>
        <div style="padding:1rem; background:#e3f2fd; border-radius:4px;">
          <h3 style="margin:0 0 0.5rem; color:#1565c0;">Net Profit</h3>
          <p style="margin:0; font-size:1.5rem; font-weight:bold; color:#1565c0;">{{ company?.currency }} {{ fmt(metrics.netProfit) }}</p>
        </div>
        <div style="padding:1rem; background:#f3e5f5; border-radius:4px;">
          <h3 style="margin:0 0 0.5rem; color:#6a1b9a;">Bank Balance</h3>
          <p style="margin:0; font-size:1.5rem; font-weight:bold; color:#6a1b9a;">{{ company?.currency }} {{ fmt(metrics.bankBalance) }}</p>
        </div>
        <div style="padding:1rem; background:#fff9c4; border-radius:4px;">
          <h3 style="margin:0 0 0.5rem; color:#f57f17;">Unpaid Invoices</h3>
          <p style="margin:0; font-size:1.5rem; font-weight:bold; color:#f57f17;">{{ metrics.unpaidInvoices }}</p>
          <router-link :to="`/companies/${id}/clients/balances`" style="color:#f57f17; font-size:0.9rem;">View Client Balances</router-link>
        </div>
        <div style="padding:1rem; background:#fce4ec; border-radius:4px;">
          <h3 style="margin:0 0 0.5rem; color:#c2185b;">Unpaid Bills</h3>
          <p style="margin:0; font-size:1.5rem; font-weight:bold; color:#c2185b;">{{ metrics.unpaidBills }}</p>
          <router-link :to="`/companies/${id}/suppliers/balances`" style="color:#c2185b; font-size:0.9rem;">View Supplier Balances</router-link>
        </div>
      </div>

      <template v-if="!company?.end_date">
        <!-- Categories -->
        <h2>Categories</h2>
        <router-link :to="`/companies/${id}/categories`"><button style="margin-bottom:1rem;">Manage Categories</button></router-link>
        <ul><li v-for="c in company?.categories" :key="c.id">{{ c.name }}</li></ul>

        <!-- Clients -->
        <h2>Clients</h2>
        <router-link v-if="isOwner" :to="`/companies/${id}/clients/create`"><button style="margin-bottom:1rem;">Add Client</button></router-link>
        <ul>
          <li v-for="client in company?.clients" :key="client.id" style="margin-bottom:0.5rem;">
            {{ client.name }} - {{ client.email }} - {{ client.phone }}
            <template v-if="isOwner">
              <router-link :to="`/companies/${id}/clients/${client.id}/edit`"><button style="padding:0.25rem 0.5rem; margin-left:0.5rem;">Edit</button></router-link>
              <router-link :to="`/companies/${id}/clients/${client.id}/invoices/create`"><button style="padding:0.25rem 0.5rem; margin-left:0.5rem;">Add Invoice</button></router-link>
            </template>
          </li>
        </ul>

        <!-- Suppliers -->
        <h2>Suppliers</h2>
        <router-link v-if="isOwner" :to="`/companies/${id}/suppliers/create`"><button style="margin-bottom:1rem;">Add Supplier</button></router-link>
        <ul>
          <li v-for="supplier in company?.suppliers" :key="supplier.id" style="margin-bottom:0.5rem;">
            {{ supplier.name }} - {{ supplier.email }} - {{ supplier.phone }}
            <template v-if="isOwner">
              <router-link :to="`/companies/${id}/suppliers/${supplier.id}/edit`"><button style="padding:0.25rem 0.5rem; margin-left:0.5rem;">Edit</button></router-link>
              <router-link :to="`/companies/${id}/suppliers/${supplier.id}/bills/create`"><button style="padding:0.25rem 0.5rem; margin-left:0.5rem;">Add Bill</button></router-link>
            </template>
          </li>
        </ul>

        <!-- Accounts -->
        <h2>Accounts</h2>
        <router-link v-if="isOwner" :to="`/companies/${id}/accounts`"><button style="margin-bottom:1rem;">Manage Accounts</button></router-link>
        <ul>
          <li v-for="account in company?.accounts" :key="account.id">
            {{ account.name }} - Balance: {{ account.balance }} - {{ account.is_active ? 'Active' : 'Inactive' }}
          </li>
        </ul>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import NavBar from '../../components/NavBar.vue';

const route = useRoute();
const id = route.params.companyId;
const company = ref(null);
const metrics = ref({ totalIncome: 0, totalExpense: 0, netProfit: 0, bankBalance: 0, unpaidInvoices: 0, unpaidBills: 0 });
const startDate = ref('');
const endDate = ref('');

const isOwner = computed(() => company.value?.pivot?.role === 'owner');
const fmt = (v) => Number(v || 0).toFixed(2);

const loadDashboard = async () => {
  const params = {};
  if (startDate.value) params.start_date = startDate.value;
  if (endDate.value) params.end_date = endDate.value;
  const res = await axios.get(`/companies/${id}`, { params });
  company.value = res.data.data.company;
  metrics.value = res.data.data;
};

const clearFilter = () => { startDate.value = ''; endDate.value = ''; loadDashboard(); };

onMounted(loadDashboard);
</script>
