<template>
  <div class="pb-dashboard">
    <div v-if="loading" class="pb-dashboard-loading">
      Loading dashboard...
    </div>

    <div v-else-if="error" class="pb-dashboard-error">
      {{ error }}
    </div>

    <div v-else>
      <!-- Welcome Section -->
      <div class="pb-dashboard-header">
        <div>
          <h1 class="pb-page-title">Dashboard</h1>
          <p class="pb-page-subtitle">
            Welcome back, {{ authStore.user?.name || 'there' }}. Here's what's happening with your business.
          </p>
        </div>
        <div class="pb-header-actions">
          <div class="pb-date-range">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Last 30 days</span>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="pb-stats-grid">
        <!-- Total Income -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Total Income</div>
            <div class="pb-stat-value pb-text-green">
              {{ formatCurrency(summary.income) }}
            </div>
            <div class="pb-stat-trend pb-trend-up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="18 15 12 9 6 15"/>
              </svg>
              <span>+12.5% from last month</span>
            </div>
          </div>
        </div>

        <!-- Total Expenses -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-red">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Total Expenses</div>
            <div class="pb-stat-value pb-text-red">
              {{ formatCurrency(summary.expenses) }}
            </div>
            <div class="pb-stat-trend pb-trend-down">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
              </svg>
              <span>+3.2% from last month</span>
            </div>
          </div>
        </div>

        <!-- Net Profit -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Net Profit</div>
            <div class="pb-stat-value pb-text-blue">
              {{ formatCurrency(summary.income - summary.expenses) }}
            </div>
            <div class="pb-stat-trend pb-trend-up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="18 15 12 9 6 15"/>
              </svg>
              <span>+8.3% from last month</span>
            </div>
          </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-orange">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            <div class="pb-stat-sub">Need attention</div>
          </div>
        </div>

        <!-- Unpaid Bills -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
              <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Unpaid Bills</div>
            <div class="pb-stat-value pb-text-purple">
              {{ summary.unpaidBills }}
            </div>
            <div class="pb-stat-sub">Due soon</div>
          </div>
        </div>

        <!-- Cash Balance -->
        <div class="pb-stat-card">
          <div class="pb-stat-icon pb-stat-icon-teal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 12v4a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-4M12 2v8M9 7l3-3 3 3"/>
              <path d="M2 12h20"/>
            </svg>
          </div>
          <div class="pb-stat-content">
            <div class="pb-stat-label">Cash Balance</div>
            <div class="pb-stat-value pb-text-teal">
              {{ formatCurrency(summary.cashBalance || 0) }}
            </div>
            <div class="pb-stat-sub">All accounts</div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="pb-main-grid">
        <!-- Recent Transactions -->
        <div class="pb-card pb-card-large">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </router-link>
          </div>
          
          <div class="pb-card-body">
            <div v-if="recentTransactions.length" class="pb-transactions-list">
              <div v-for="trans in recentTransactions" :key="trans.id" class="pb-transaction-item">
                <div class="pb-transaction-left">
                  <div class="pb-transaction-icon" :class="trans.amount > 0 ? 'pb-icon-income' : 'pb-icon-expense'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
              </div>
              <p class="pb-empty-title">No transactions yet</p>
              <p class="pb-empty-subtitle">When you add transactions, they'll appear here.</p>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="pb-card">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                  <polyline points="10 9 9 9 8 9"/>
                </svg>
                New Invoice
              </router-link>
              
              <router-link 
                v-if="currentCompany" 
                :to="`/companies/${currentCompany.id}/bills`" 
                class="pb-action-btn pb-action-secondary"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
                  <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                Add Bill
              </router-link>
              
              <router-link 
                :to="`/companies`" 
                class="pb-action-btn pb-action-outline"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Add Transaction
              </router-link>
            </div>
          </div>
        </div>

        <!-- Recent Invoices -->
        <div class="pb-card">
          <div class="pb-card-header">
            <div class="pb-card-title-section">
              <div class="pb-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
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
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
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

const route = useRoute();
const companyStore = useCompanyStore();
const invoiceStore = useInvoiceStore();
const billStore = useBillStore();
const accountStore = useAccountStore();
const transactionStore = useTransactionStore();
const authStore = useAuthStore();

const companies = computed(() => companyStore.companies);
const currentCompany = computed(() => companyStore.currentCompany);
const recentTransactions = ref([]);
const recentInvoices = ref([]);
const summary = ref({
  income: 0,
  expenses: 0,
  overdue: 0,
  unpaidBills: 0,
  cashBalance: 0,
});
const loading = ref(true);
const error = ref(null);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0);
};

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

    const invoices = invoiceStore.invoices || [];
    const bills = billStore.bills || [];
    const accounts = accountStore.accounts || [];
    const transactions = transactionStore.transactions || [];

    summary.value.income = invoices.reduce((sum, i) => sum + parseFloat(i.total_amount || 0), 0);
    summary.value.expenses = bills.reduce((sum, b) => sum + parseFloat(b.total_amount || 0), 0);
    summary.value.overdue = invoices.filter(i => i.status === 'overdue').length;
    summary.value.unpaidBills = bills.filter(b => b.status !== 'paid').length;
    summary.value.cashBalance = accounts.reduce((sum, acc) => sum + parseFloat(acc.balance || 0), 0);
    recentTransactions.value = transactions.slice(0, 5);
    recentInvoices.value = invoices.filter(i => i.status !== 'paid').slice(0, 3);
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
</script>

<style scoped>
/* Dashboard Container */
.pb-dashboard {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Header */
.pb-dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.pb-page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a2e;
  letter-spacing: -0.5px;
  margin-bottom: 0.5rem;
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
  border-radius: 24px;
  font-size: 13px;
  color: #1a1a2e;
  cursor: pointer;
  transition: background 0.15s;
}

.pb-date-range:hover {
  background: #f1f5f9;
}

/* Stats Grid */
.pb-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.pb-stat-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.25rem;
  display: flex;
  gap: 1rem;
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
  font-size: 28px;
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

/* Main Grid */
.pb-main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

/* Cards */
.pb-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
}

.pb-card-large {
  grid-row: span 2;
}

.pb-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
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
  padding: 1.5rem;
}

/* Transactions List */
.pb-transactions-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.pb-transaction-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
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

/* Quick Actions */
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

/* Invoices List */
.pb-invoices-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
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

/* Empty States */
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

/* Responsive */
@media (max-width: 1024px) {
  .pb-dashboard {
    padding: 1.5rem;
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