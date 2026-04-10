<template>
  <div class="pb-dashboard">
    <div v-if="loading" class="pb-dashboard-loading">
      Loading dashboard...
    </div>

    <div v-else-if="error" class="pb-dashboard-error">
      {{ error }}
    </div>

    <div v-else>
      <div class="pb-stats-grid">
        <!-- Total Income -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Actual Income</div>
            <div class="pb-stat-value pb-text-green">
              {{ formatCurrency(summary.income) }}
            </div>
           <div class="pb-stat-sub">Paid Invoices</div>
            <div class="pb-stat-trend pb-trend-up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Expenses -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-red">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Expenses</div>
            <div class="pb-stat-value pb-text-red">
              {{ formatCurrency(summary.expenses) }}
            </div>
            <div class="pb-stat-sub">Paid Bills</div>
            <div class="pb-stat-trend pb-trend-down">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              </svg>
            </div>
          </div>
        </div>

        <!-- Net Profit -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Profit</div>
            <div class="pb-stat-value pb-text-blue">
              {{ formatCurrency(summary.income - summary.expenses) }}
            </div>
              <div class="pb-stat-sub">Income - Expenses</div>
            <div class="pb-stat-trend pb-trend-up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              </svg>
            </div>
          </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-orange">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Overdue Invoices</div>
            <div class="pb-stat-value pb-text-orange">
              {{ summary.overdue }}
            </div>
            
          </div>
        </div>

        <!-- Unpaid Bills -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
              <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Unpaid Bills</div>
            <div class="pb-stat-value pb-text-purple">
              {{ summary.unpaidBills }}
            </div>
            
          </div>
        </div>

        <!-- Cash Balance -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-teal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 12v4a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-4M12 2v8M9 7l3-3 3 3"/>
              <path d="M2 12h20"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Total Balance</div>
            <div class="pb-stat-value pb-text-teal">
              {{ formatCurrency(summary.cashBalance || 0) }}
            </div>
            <div class="pb-stat-sub">Real money in accounts</div>
          </div>
        </div>

        <!-- Expected Income -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Expected Income</div>
            <div class="pb-stat-value pb-text-green">
              {{ formatCurrency(summary.expectedIncome) }}
            </div>
            <div class="pb-stat-sub">Unpaid invoices</div>
          </div>
        </div>
      </div>

      <div class="pb-middle-grid">
        <div class="pb-card pb-chart-card">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <polyline points="22 6 13.5 14.5 8.5 9.5 2 16"/>
                  <polyline points="16 6 22 6 22 12"/>
                </svg>
              </div>
              <div>
                <h3 class="pb-card-title">Income vs Expenses</h3>
                <p class="pb-card-subtitle">Comparison over the selected period</p>
              </div>
            </div>
          </div>
          <div class="pb-card-body pb-chart-body">
            <VueApexCharts
              v-if="hasIncomeExpenseChartData"
              type="line"
              height="220"
              :options="incomeExpenseChartOptions"
              :series="incomeExpenseChartSeries"
            />
            <div v-else class="pb-empty-state-small">
              <p>No income or expense data in this period.</p>
            </div>
          </div>
        </div>

        <div class="pb-card">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                </svg>
              </div>
              <div>
                <h3 class="pb-card-title">Recent Invoices</h3>
                <p class="pb-card-subtitle">Awaiting payment</p>
              </div>
            </div>
            <router-link
              v-if="currentCompany"
              :to="`/companies/${currentCompany.id}/invoices`"
              class="pb-link"
            >
              View all
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              </svg>
            </router-link>
          </div>

          <div class="pb-card-body">
            <div v-if="recentInvoices.length" class="pb-invoices-list">
              <div v-for="invoice in recentInvoices" :key="invoice.id" class="pb-invoice-item">
                <div class="pb-invoice-info">
                  <p class="pb-invoice-title">{{ invoice.client_name || 'Client' }}</p>
                  <p class="pb-invoice-date">Due {{ formatDate(invoice.due_date) }}</p>
                </div>
                <div class="pb-invoice-right">
                  <p class="pb-invoice-amount">{{ formatCurrency(invoice.total_amount) }}</p>
                  <span :class="['pb-status-badge', getStatusClass(invoice.status)]">
                    {{ getStatusLabel(invoice.status) }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="pb-empty-state-small">
              <p>No pending invoices</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="pb-main-grid">
        <!-- Recent Transactions -->
        <div class="pb-card" :class="{ 'pb-card-full': isViewer }">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
              </div>
              <div>
                <h3 class="pb-card-title">Recent Transactions</h3>
                <p class="pb-card-subtitle">Your latest financial activity</p>
              </div>
            </div>
            <router-link 
              v-if="currentCompany" 
              :to="`/companies/${currentCompany.id}/transactions`" 
              class="pb-link"
            >
              View all
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              </svg>
            </router-link>
          </div>
          
          <div class="pb-card-body">
            <div v-if="recentTransactions.length" class="pb-transactions-list">
              <div v-for="trans in recentTransactions" :key="trans.id" class="pb-transaction-item">
                <div class="pb-transaction-left">
                  <div class="pb-transaction-icon" :class="trans.amount > 0 ? 'pb-icon-income' : 'pb-icon-expense'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <path v-if="trans.amount > 0" d="M12 5v14M5 12h14"/>
                      <path v-else d="M20 12H4"/>
                    </svg>
                  </div>
                  <div>
                    <p class="pb-transaction-title">{{ trans.description }}</p>
                    <p class="pb-transaction-meta">
                      {{ trans.category?.name || 'Uncategorized' }}
                      <span class="pb-meta-separator">•</span>
                      {{ formatDate(trans.date) }}
                    </p>
                  </div>
                </div>
                <div class="pb-transaction-right">
                  <p :class="['pb-transaction-amount', trans.amount > 0 ? 'pb-amount-income' : 'pb-amount-expense']">
                    {{ trans.amount > 0 ? '+' : '' }}{{ formatCurrency(trans.amount) }}
                  </p>
                  <p class="pb-transaction-account">{{ trans.account?.name }}</p>
                </div>
              </div>
            </div>
            <div v-else class="pb-empty-state">
              <div class="pb-empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                  <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
              </div>
              <p class="pb-empty-title">No transactions yet</p>
              <p class="pb-empty-subtitle">When you add transactions, they'll appear here.</p>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div v-if="!isViewer" class="pb-card">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <polygon points="13 2 3 7 3 17 13 22 23 17 23 7 13 2"/>
                  <line x1="3" y1="7" x2="13" y2="12"/>
                  <line x1="13" y1="12" x2="23" y2="7"/>
                  <line x1="3" y1="17" x2="13" y2="22"/>
                  <line x1="13" y1="22" x2="23" y2="17"/>
                </svg>
              </div>
              <div>
                <h3 class="pb-card-title">Quick Actions</h3>
                <p class="pb-card-subtitle">Common tasks and shortcuts</p>
              </div>
            </div>
          </div>
          
          <div class="pb-card-body">
            <div class="pb-actions-list">
              <router-link 
                v-if="currentCompany" 
                :to="`/companies/${currentCompany.id}/invoices`" 
                class="pb-action-btn pb-action-primary"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                New Invoice
              </router-link>
              
              <router-link 
                v-if="currentCompany" 
                :to="`/companies/${currentCompany.id}/bills`" 
                class="pb-action-btn pb-action-secondary"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
                  <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                Add Bill
              </router-link>
              
              <router-link 
                :to="`/companies`" 
                class="pb-action-btn pb-action-outline"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                Manage Companies
              </router-link>
              
              <router-link 
                v-if="currentCompany" 
                :to="`/companies/${currentCompany.id}/transactions/new`" 
                class="pb-action-btn pb-action-outline"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Add Transaction
              </router-link>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useCompanyStore } from '../stores/company.js';
import { useInvoiceStore } from '../stores/invoice.js';
import { useBillStore } from '../stores/bill.js';
import { useAccountStore } from '../stores/account.js';
import { useTransactionStore } from '../stores/transaction.js';
import { useAuthStore } from '../stores/auth.js';
import { useDashboardStore } from '../stores/dashboard.js';
import VueApexCharts from 'vue3-apexcharts';

const route = useRoute();
const companyStore = useCompanyStore();
const invoiceStore = useInvoiceStore();
const billStore = useBillStore();
const accountStore = useAccountStore();
const transactionStore = useTransactionStore();
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();

const companies = computed(() => companyStore.companies);
const currentCompany = computed(() => companyStore.currentCompany);
const activeCompany = computed(() => currentCompany.value?.company || currentCompany.value || null);
const isViewer = computed(() => {
  const role = String(activeCompany.value?.pivot?.role || '').toLowerCase();
  return !role || role === 'viewer';
});
const recentTransactions = ref([]);
const recentInvoices = ref([]);
const summary = ref({
  income: 0,
  expectedIncome: 0,
  expenses: 0,
  overdue: 0,
  unpaidBills: 0,
  cashBalance: 0,
});
const loading = ref(true);
const error = ref(null);
const incomeExpenseChartLabels = ref([]);
const incomeSeries = ref([]);
const expenseSeries = ref([]);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0);
};

const isInDateRange = (dateString, start, end) => {
  if (!dateString) return false;
  const date = new Date(dateString);
  date.setHours(0, 0, 0, 0);
  const startDate = new Date(start);
  startDate.setHours(0, 0, 0, 0);
  const endDate = new Date(end);
  endDate.setHours(23, 59, 59, 999);
  
  return date >= startDate && date <= endDate;
};

const calculateSummary = () => {
  const invoices = invoiceStore.invoices || [];
  const bills = billStore.bills || [];
  const accounts = accountStore.accounts || [];
  const transactions = transactionStore.transactions || [];
  const start = dashboardStore.startDate;
  const end = dashboardStore.endDate;

  const filteredInvoices = invoices.filter(i => isInDateRange(i.created_at || i.date, start, end));
  const filteredBills = bills.filter(b => isInDateRange(b.created_at || b.date, start, end));
  const filteredTransactions = transactions.filter(t => isInDateRange(t.date || t.created_at, start, end));

  summary.value.income = filteredTransactions
    .filter((t) => String(t.type || "").toLowerCase() === "income")
    .reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);

  summary.value.expectedIncome = invoices
    .filter((i) => {
      const status = String(i.status || "").toLowerCase();
      return status !== "paid" && status !== "cancelled";
    })
    .reduce((sum, i) => {
      const total = parseFloat(i.total_amount || 0);
      const paid = parseFloat(i.amount_paid || 0);
      return sum + Math.max(total - paid, 0);
    }, 0);
  summary.value.expenses = filteredBills.reduce((sum, b) => sum + parseFloat(b.amount_paid || 0), 0);
  summary.value.overdue = invoices.filter(i => String(i.status || '').toLowerCase() === 'overdue').length;
  summary.value.unpaidBills = bills.filter(b => String(b.status || '').toLowerCase() !== 'paid').length;
  summary.value.cashBalance = accounts.reduce((sum, acc) => sum + parseFloat(acc.balance || 0), 0);
  recentTransactions.value = filteredTransactions.slice(0, 5);
  recentInvoices.value = filteredInvoices.filter(i => i.status !== 'paid').slice(0, 3);

  const trendMap = new Map();

  filteredTransactions
    .filter((t) => String(t.type || '').toLowerCase() === 'income')
    .forEach((t) => {
      const dateKey = String(t.date || t.created_at || '').substring(0, 10);
      if (!dateKey) return;
      const current = trendMap.get(dateKey) || { income: 0, expense: 0 };
      current.income += parseFloat(t.amount || 0);
      trendMap.set(dateKey, current);
    });

  filteredBills.forEach((b) => {
    const dateKey = String(b.date || b.created_at || '').substring(0, 10);
    if (!dateKey) return;
    const current = trendMap.get(dateKey) || { income: 0, expense: 0 };
    current.expense += parseFloat(b.amount_paid || 0);
    trendMap.set(dateKey, current);
  });

  const labels = Array.from(trendMap.keys()).sort();
  incomeExpenseChartLabels.value = labels;
  incomeSeries.value = labels.map((label) => Number((trendMap.get(label)?.income || 0).toFixed(2)));
  expenseSeries.value = labels.map((label) => Number((trendMap.get(label)?.expense || 0).toFixed(2)));
};

const hasIncomeExpenseChartData = computed(() => {
  return incomeExpenseChartLabels.value.length > 0;
});

const incomeExpenseChartSeries = computed(() => {
  return [
    { name: 'Income', data: incomeSeries.value },
    { name: 'Expenses', data: expenseSeries.value },
  ];
});

const incomeExpenseChartOptions = computed(() => {
  return {
    chart: {
      toolbar: { show: false },
      zoom: { enabled: false },
      animations: { easing: 'easeinout', speed: 350 },
      fontFamily: 'inherit',
    },
    stroke: {
      curve: 'smooth',
      width: 3,
    },
    xaxis: {
      categories: incomeExpenseChartLabels.value,
      labels: {
        style: { colors: '#64748b', fontSize: '11px' },
      },
    },
    yaxis: {
      labels: {
        style: { colors: '#64748b', fontSize: '11px' },
        formatter: (value) => `$${Number(value || 0).toFixed(0)}`,
      },
    },
    grid: {
      borderColor: '#e2e8f0',
      strokeDashArray: 4,
    },
    colors: ['#10b981', '#ef4444'],
    legend: {
      position: 'top',
      horizontalAlign: 'right',
    },
    dataLabels: {
      enabled: false,
    },
    tooltip: {
      y: {
        formatter: (value) => formatCurrency(value),
      },
    },
  };
});

const loadCompanyData = async (companyId) => {
  if (!companyId) return;

  try {
    error.value = null;
    await companyStore.fetchCompany(companyId);
    await Promise.all([
      invoiceStore.fetchInvoices(companyId),
      billStore.fetchBills(companyId),
      accountStore.fetchAccounts(companyId),
      transactionStore.fetchTransactions(companyId),
    ]);

    calculateSummary();
  } catch (err) {
    console.error('Unable to load company dashboard data:', err);
    error.value = 'Unable to load company data. Please refresh or select a different company.';
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(date);
};

const getStatusLabel = (status) => {
  const labels = {
    paid: 'Paid',
    unpaid: 'Unpaid',
    overdue: 'Overdue',
    sent: 'Sent',
    draft: 'Draft'
  };
  return labels[status] || status;
};

const getStatusClass = (status) => {
  const classes = {
    paid: 'pb-status-paid',
    unpaid: 'pb-status-unpaid',
    overdue: 'pb-status-overdue',
    sent: 'pb-status-sent',
    draft: 'pb-status-draft'
  };
  return classes[status] || 'pb-status-default';
};

const loadDefaultCompany = async () => {
  if (route.params.companyId) {
    await loadCompanyData(route.params.companyId);
    return;
  }

  if (companies.value.length === 1 && !currentCompany.value) {
    await loadCompanyData(companies.value[0].id);
  }
};

onMounted(async () => {
  loading.value = true;
  error.value = null;

  try {
    await companyStore.fetchCompanies();
    await loadDefaultCompany();
  } catch (err) {
    console.error('Dashboard load failed:', err);
    error.value = 'Unable to load the dashboard. Please refresh the page.';
  } finally {
    loading.value = false;
  }
});

watch(
  () => route.params.companyId,
  async (newCompanyId, oldCompanyId) => {
    if (newCompanyId && newCompanyId !== oldCompanyId) {
      await loadCompanyData(newCompanyId);
    }
  }
);

watch(
  [() => dashboardStore.startDate, () => dashboardStore.endDate],
  () => {
    calculateSummary();
  }
);
</script>

<style scoped>
/* Dashboard Container */
.pb-dashboard {
  padding: 1.25rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Header with Professional User Icon */
.pb-dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.pb-welcome-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.pb-user-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4f46e5;
  flex-shrink: 0;
}

.pb-welcome-text {
  flex: 1;
}

.pb-page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a2e;
  letter-spacing: -0.5px;
  margin-bottom: 0.25rem;
}

.pb-page-subtitle {
  font-size: 14px;
  color: #64748b;
}

.pb-date-range {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 13px;
  color: #1a1a2e;
  cursor: pointer;
  transition: all 0.15s;
}

.pb-date-range:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

/* Rest of your styles remain exactly the same as your original */
.pb-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.pb-stat-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1rem;
  display: flex;
  gap: 0.75rem;
  transition: all 0.2s;
}

.pb-stat-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.pb-stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: white;
}

.pb-stat-icon-green { background: #10b981; }
.pb-stat-icon-red { background: #ef4444; }
.pb-stat-icon-blue { background: #3b82f6; }
.pb-stat-icon-orange { background: #f59e0b; }
.pb-stat-icon-purple { background: #8b5cf6; }
.pb-stat-icon-teal { background: #14b8a6; }

.pb-stat-content {
  flex: 1;
}

.pb-stat-label {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 6px;
}

.pb-stat-value {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 4px;
}

.pb-text-green { color: #10b981; }
.pb-text-red { color: #ef4444; }
.pb-text-blue { color: #3b82f6; }
.pb-text-orange { color: #f59e0b; }
.pb-text-purple { color: #8b5cf6; }
.pb-text-teal { color: #14b8a6; }

.pb-stat-trend {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 500;
}

.pb-trend-up {
  color: #10b981;
}

.pb-trend-down {
  color: #ef4444;
}

.pb-stat-sub {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 4px;
}

.pb-main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1rem;
}

.pb-middle-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

.pb-chart-card {
  margin-bottom: 0;
}

.pb-chart-body {
  padding-top: 0.25rem;
  padding-bottom: 1rem;
}

.pb-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
}

.pb-card-full {
  grid-column: 1 / -1;
}

.pb-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e2e8f0;
}

.pb-card-title-section {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pb-card-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4f46e5;
}

.pb-card-title {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 2px;
}

.pb-card-subtitle {
  font-size: 12px;
  color: #64748b;
}

.pb-link {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #4f46e5;
  text-decoration: none;
  font-weight: 500;
  transition: gap 0.2s;
}

.pb-link:hover {
  gap: 10px;
  color: #4338ca;
}

.pb-card-body {
  padding: 1rem 1.25rem;
}

.pb-transactions-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  max-height: 280px;
  overflow-y: auto;
}

.pb-transaction-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.pb-transaction-item:last-child {
  border-bottom: none;
}

.pb-transaction-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pb-transaction-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pb-icon-income {
  background: #d1fae5;
  color: #10b981;
}

.pb-icon-expense {
  background: #fee2e2;
  color: #ef4444;
}

.pb-transaction-title {
  font-size: 14px;
  font-weight: 500;
  color: #1a1a2e;
  margin-bottom: 2px;
}

.pb-transaction-meta {
  font-size: 12px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 4px;
}

.pb-meta-separator {
  color: #cbd5e1;
}

.pb-transaction-right {
  text-align: right;
}

.pb-transaction-amount {
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 2px;
}

.pb-amount-income {
  color: #10b981;
}

.pb-amount-expense {
  color: #ef4444;
}

.pb-transaction-account {
  font-size: 11px;
  color: #94a3b8;
}

.pb-actions-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.pb-action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s;
}

.pb-action-primary {
  background: #4f46e5;
  color: white;
}

.pb-action-primary:hover {
  background: #4338ca;
  transform: translateY(-1px);
}

.pb-action-secondary {
  background: #10b981;
  color: white;
}

.pb-action-secondary:hover {
  background: #059669;
  transform: translateY(-1px);
}

.pb-action-outline {
  background: white;
  color: #1a1a2e;
  border: 1px solid #e2e8f0;
}

.pb-action-outline:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.pb-invoices-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.pb-invoice-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 12px;
}

.pb-invoice-title {
  font-size: 14px;
  font-weight: 500;
  color: #1a1a2e;
  margin-bottom: 4px;
}

.pb-invoice-date {
  font-size: 11px;
  color: #94a3b8;
}

.pb-invoice-right {
  text-align: right;
}

.pb-invoice-amount {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 4px;
}

.pb-status-badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
}

.pb-status-paid {
  background: #d1fae5;
  color: #065f46;
}

.pb-status-unpaid {
  background: #fef3c7;
  color: #92400e;
}

.pb-status-overdue {
  background: #fee2e2;
  color: #991b1b;
}

.pb-status-sent {
  background: #dbeafe;
  color: #1e40af;
}

.pb-status-draft {
  background: #f1f5f9;
  color: #475569;
}

.pb-empty-state {
  text-align: center;
  padding: 3rem 1.5rem;
}

.pb-empty-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1rem;
  background: #f1f5f9;
  border-radius: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
}

.pb-empty-title {
  font-size: 16px;
  font-weight: 500;
  color: #1a1a2e;
  margin-bottom: 0.25rem;
}

.pb-empty-subtitle {
  font-size: 13px;
  color: #94a3b8;
}

.pb-empty-state-small {
  text-align: center;
  padding: 2rem;
  color: #94a3b8;
  font-size: 13px;
}

/* Loading & Error States */
.pb-dashboard-loading,
.pb-dashboard-error {
  text-align: center;
  padding: 4rem;
  font-size: 14px;
  color: #64748b;
}

.pb-dashboard-error {
  color: #ef4444;
}

/* Responsive */
@media (max-width: 1024px) {
  .pb-dashboard {
    padding: 1.5rem;
  }

  .pb-middle-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .pb-main-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .pb-stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
}

@media (max-width: 640px) {
  .pb-dashboard {
    padding: 1rem;
  }
  
  .pb-page-title {
    font-size: 24px;
  }
  
  .pb-user-icon {
    width: 40px;
    height: 40px;
  }
  
  .pb-user-icon svg {
    width: 20px;
    height: 20px;
  }
  
  .pb-stat-value {
    font-size: 24px;
  }
  
  .pb-stat-icon {
    width: 40px;
    height: 40px;
  }
  
  .pb-card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .pb-transaction-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .pb-transaction-right {
    text-align: left;
    width: 100%;
  }
}
</style>