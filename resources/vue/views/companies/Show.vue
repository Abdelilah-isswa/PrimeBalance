<template>
  <div>
    <div v-if="loading" style="padding:2rem; text-align:center; color:#374151;">
      Loading company overview...
    </div>

    <div v-else-if="error" style="padding:2rem; color:#b91c1c;">
      {{ error }}
    </div>

    <div v-else>
      <h1>
        {{ company?.name }}
        <span v-if="company?.deleted_at" style="color:red;">(Deactivated)</span>
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
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useCompanyStore } from '../../stores/company.js';

const route = useRoute();
const id = route.params.companyId;
const companyStore = useCompanyStore();
const company = computed(() => companyStore.currentCompany);
const loading = ref(true);
const error = ref(null);

const fmt = (v) => Number(v || 0).toFixed(2);

const metrics = computed(() => {
  const income = Number(company.value?.total_income || 0);
  const expense = Number(company.value?.total_expense || 0);
  return {
    totalIncome: income,
    totalExpense: expense,
    netProfit: income - expense,
  };
});

onMounted(async () => {
  loading.value = true;
  error.value = null;

  try {
    await companyStore.fetchCompany(id);
  } catch (err) {
    error.value = 'Unable to load company data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>
