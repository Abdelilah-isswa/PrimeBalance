<template>
  <div>
    <h1>
      {{ company?.name }}
      <span v-if="company?.end_date" style="color:red;">(Deactivated)</span>
    </h1>

    <!-- Metrics -->
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
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCompanyStore } from '../../stores/company.js';

const route = useRoute();
const id = route.params.companyId;
const companyStore = useCompanyStore();
const company = computed(() => companyStore.currentCompany);

const fmt = (v) => Number(v || 0).toFixed(2);

onMounted(async () => {
  await companyStore.fetchCompany(id);
});
</script>
