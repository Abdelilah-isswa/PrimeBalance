<template>
  <DashboardLayout :company-id="id">
    <h1>
      {{ company?.name }}
      <span v-if="company?.end_date" style="color:red;">(Deactivated)</span>
    </h1>

    <!-- Metrics فقط -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin:1rem 0;">
      <div style="padding:1rem; background:#e8f5e9;">
        <h3>Total Income</h3>
        <p>{{ company?.currency }} {{ fmt(metrics.totalIncome) }}</p>
      </div>

      <div style="padding:1rem; background:#ffebee;">
        <h3>Total Expense</h3>
        <p>{{ company?.currency }} {{ fmt(metrics.totalExpense) }}</p>
      </div>

      <div style="padding:1rem; background:#e3f2fd;">
        <h3>Net Profit</h3>
        <p>{{ company?.currency }} {{ fmt(metrics.netProfit) }}</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import NavBar from '../../components/NavBar.vue';
import { useCompanyStore } from '../../stores/company.js';
import { useAuthStore } from '../../stores/auth.js';

const route = useRoute();
const id = route.params.companyId;
const companyStore = useCompanyStore();
const authStore = useAuthStore();
const company = computed(() => companyStore.currentCompany);

const isOwner = computed(() => company.value?.pivot?.role === 'owner');
const fmt = (v) => Number(v || 0).toFixed(2);

onMounted(async () => {
  await companyStore.fetchCompany(id);
});
</script>
