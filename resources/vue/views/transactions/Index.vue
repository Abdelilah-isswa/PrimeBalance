<template>
  <div class="pb-transactions-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Transactions</h1>
        <p class="pb-page-subtitle">Track your income and expenses, and categorize your cash flow.</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'manage' }"
            @click="activeTab = 'manage'"
          >
            Manage Transactions
            <span class="pb-tab-badge">{{ filteredTransactions.length }}</span>
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'create' }"
            @click="openCreate"
          >
            Record Transaction
            <span class="pb-tab-badge">+</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tab Content: Manage -->
    <div v-if="activeTab === 'manage'" class="pb-tab-content anim-fade-in">
      <div class="pb-card">
        <div class="pb-table-wrapper">
          <table class="pb-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Account</th>
                <th>Category</th>
                <th class="pb-text-right">Amount</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tx in filteredTransactions" :key="tx.id">
                <template v-if="editId === tx.id">
                  <!-- EDIT MODE -->
                  <td style="padding: 8px;">
                    <input v-model="form.date" type="date" class="pb-input pb-input-sm" required>
                  </td>
                  <td style="padding: 8px;">
                    <select v-model="form.type" class="pb-input pb-input-sm" required>
                      <option value="income">Income</option>
                      <option value="expense">Expense</option>
                    </select>
                  </td>
                  <td style="padding: 8px;">
                    <input v-model="form.description" class="pb-input pb-input-sm" required>
                  </td>
                  <td style="padding: 8px;">
                    <select v-model="form.account_id" class="pb-input pb-input-sm" required>
                      <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                    </select>
                  </td>
                  <td style="padding: 8px;">
                    <select v-model="form.category_id" class="pb-input pb-input-sm">
                      <option value="">None</option>
                      <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                  </td>
                  <td style="padding: 8px;">
                    <input v-model="form.amount" type="number" step="0.01" class="pb-input pb-input-sm pb-text-right" required>
                  </td>
                  <td class="pb-text-center">
                    <div class="pb-action-group">
                      <button @click="saveEdit" class="pb-btn-icon pb-icon-success" title="Save" :disabled="submitting">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                      </button>
                      <button @click="cancelEdit" class="pb-btn-icon pb-icon-neutral" title="Cancel" :disabled="submitting">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      </button>
                    </div>
                  </td>
                </template>
                <template v-else>
                  <!-- VIEW MODE -->
                  <td>{{ formatDate(tx.date) }}</td>
                  <td>
                    <span :class="['pb-status-pill', tx.type === 'income' ? 'pb-status--paid' : 'pb-status--overdue']">
                      {{ tx.type }}
                    </span>
                  </td>
                  <td class="pb-font-medium">{{ tx.description }}</td>
                  <td>{{ tx.account?.name }}</td>
                  <td>{{ tx.category?.name || '-' }}</td>
                  <td class="pb-text-right pb-font-bold" :class="tx.type === 'income' ? 'pb-text-success' : 'pb-text-danger'">
                    {{ tx.type === 'income' ? '+' : '-' }}{{ company?.currency }} {{ Number(tx.amount).toFixed(2) }}
                  </td>
                  <td class="pb-text-center">
                    <div class="pb-action-group">
                      <button @click="startEdit(tx)" class="pb-btn-action pb-icon-primary" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                      </button>
                      <button @click="destroyTx(tx.id)" class="pb-btn-action pb-icon-danger" title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Delete
                      </button>
                    </div>
                  </td>
                </template>
              </tr>
              <tr v-if="filteredTransactions.length === 0">
                <td colspan="7" class="pb-empty-row">No transactions found for this period.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab Content: Create -->
    <div v-if="activeTab === 'create'" class="pb-tab-content anim-fade-in">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Record Transaction</h2>
          <p class="pb-card-subtitle">Log a new income or expense manually.</p>
        </div>
        
        <form @submit.prevent="createTx" class="pb-form">
          <div class="pb-form-grid">
            <div class="pb-form-group">
              <label class="pb-label">Date</label>
              <input v-model="form.date" type="date" class="pb-input" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Type</label>
              <select v-model="form.type" class="pb-input" required>
                <option value="income">Income (+)</option>
                <option value="expense">Expense (-)</option>
              </select>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Amount ({{ company?.currency }})</label>
              <input v-model="form.amount" type="number" step="0.01" class="pb-input" placeholder="0.00" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Description</label>
              <input v-model="form.description" type="text" class="pb-input" placeholder="e.g., Office Supplies" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Account</label>
              <select v-model="form.account_id" class="pb-input" required>
                <option disabled value="">Select an account</option>
                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ company?.currency }}{{ acc.balance }})</option>
              </select>
            </div>
            
            <div class="pb-form-group">
              <label class="pb-label">Category (Optional)</label>
              <select v-model="form.category_id" class="pb-input">
                <option value="">None</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="button" class="pb-btn pb-btn-secondary" @click="activeTab = 'manage'">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Recording...' : 'Record Transaction' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useDashboardStore } from '../../stores/dashboard.js';
import { useCompanyStore } from '../../stores/company.js';
import { useTransactionStore } from '../../stores/transaction.js';
import axios from 'axios';

const route = useRoute();
const id = route.params.companyId;

const dashboardStore = useDashboardStore();
const companyStore = useCompanyStore();
const txStore = useTransactionStore();

const activeTab = ref('manage');
const submitting = ref(false);
const editId = ref(null);

const accounts = ref([]);
const categories = ref([]);

const form = ref({
  date: new Date().toISOString().substring(0, 10),
  type: 'expense',
  amount: '',
  description: '',
  account_id: '',
  category_id: ''
});

const company = computed(() => companyStore.currentCompany);

// Computed list filtering by the global dashboard date picker query
const filteredTransactions = computed(() => {
  let txs = txStore.transactions || [];
  const start = route.query.start_date;
  const end = route.query.end_date;
  
  if (start) txs = txs.filter(tx => tx.date.substring(0, 10) >= start);
  if (end) txs = txs.filter(tx => tx.date.substring(0, 10) <= end);
  
  return txs;
});

onMounted(async () => {
  await Promise.all([
    companyStore.fetchCompany(id),
    txStore.fetchTransactions(id)
  ]);
  
  // Load dependencies for dropdowns
  const accRes = await axios.get(`companies/${id}/accounts`);
  accounts.value = accRes.data.data.accounts || [];
  
  const catRes = await axios.get(`companies/${id}/categories`);
  categories.value = catRes.data.data.categories || [];
});

const openCreate = () => {
  resetForm();
  activeTab.value = 'create';
};

const resetForm = () => {
  form.value = {
    date: new Date().toISOString().substring(0, 10),
    type: 'expense',
    amount: '',
    description: '',
    account_id: '',
    category_id: ''
  };
};

const createTx = async () => {
  submitting.value = true;
  try {
    const payload = { ...form.value };
    if (!payload.category_id) delete payload.category_id; // null category 
    await txStore.createTransaction(id, payload);
    resetForm();
    activeTab.value = 'manage';
  } catch (err) {
    console.error('Failed to create tx', err);
    alert(err.response?.data?.message || 'Error creating transaction');
  } finally {
    submitting.value = false;
  }
};

const startEdit = (tx) => {
  editId.value = tx.id;
  form.value = {
    date: tx.date.substring(0, 10),
    type: tx.type,
    amount: tx.amount,
    description: tx.description,
    account_id: tx.account_id,
    category_id: tx.category_id || ''
  };
};

const cancelEdit = () => {
  editId.value = null;
  resetForm();
};

const saveEdit = async () => {
  submitting.value = true;
  try {
    const payload = { ...form.value };
    if (!payload.category_id) delete payload.category_id;
    await txStore.updateTransaction(id, editId.value, payload);
    editId.value = null;
  } catch (err) {
    console.error('Failed to update tx', err);
    alert(err.response?.data?.message || 'Error updating transaction');
  } finally {
    submitting.value = false;
  }
};

const destroyTx = async (txId) => {
  if (!confirm('Are you sure you want to delete this transaction? Balance will be adjusted.')) return;
  try {
    await txStore.deleteTransaction(id, txId);
  } catch (err) {
    alert(err.response?.data?.message || 'Error deleting transaction');
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<style scoped>
.pb-transactions-page {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.pb-page-header {
  margin-bottom: 2rem;
  padding-bottom: 0;
}

.pb-header-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.pb-page-title {
  font-size: 28px;
  font-weight: 800;
  color: #1a1a2e;
  letter-spacing: -0.5px;
  margin: 0;
}

.pb-page-subtitle {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 1.5rem;
}

/* Tabs */
.pb-tabs {
  display: flex;
  gap: 32px;
  border-bottom: 2px solid #f1f5f9;
  padding: 0 4px;
}

.pb-tab-btn {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 4px;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.pb-tab-btn:hover {
  color: #1e293b;
}

.pb-tab-btn--active {
  color: #4f46e5;
}

.pb-tab-btn--active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 2px;
  background: #4f46e5;
  border-radius: 2px;
}

.pb-tab-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 24px;
  height: 24px;
  padding: 0 6px;
  border: 1.5px solid #cbd5e1;
  background: transparent;
  color: #64748b;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  transition: all 0.2s;
}

.pb-tab-btn--active .pb-tab-badge {
  border-color: #4f46e5;
  color: #4f46e5;
}

/* Table Card */
.pb-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow: hidden;
}

.pb-table-wrapper {
  overflow-x: auto;
}

.pb-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.pb-table th {
  text-align: left;
  padding: 1.25rem 1.5rem;
  background: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 12px;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e2e8f0;
}

.pb-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  color: #1a1a2e;
  vertical-align: middle;
}

.pb-table tr:hover {
  background: #f8fafc;
}

.pb-status-pill {
  display: inline-flex;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.pb-status--paid { background: #d1fae5; color: #065f46; }
.pb-status--overdue { background: #fee2e2; color: #991b1b; }

.pb-text-success { color: #059669; }
.pb-text-danger { color: #e11d48; }
.pb-font-medium { font-weight: 500; }
.pb-font-bold { font-weight: 700; }

.pb-input-sm {
  padding: 8px !important;
  font-size: 13px !important;
  width: 100%;
  min-width: 100px;
  border-radius: 6px !important;
}

.pb-action-group {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.pb-btn-icon {
  width: auto;
  min-width: 32px;
  height: 32px;
  padding: 0 10px;
  border-radius: 8px;
  border: none;
  background: transparent;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  cursor: pointer;
  transition: all 0.2s;
  color: #64748b;
  font-size: 13px;
  font-weight: 500;
  white-space: nowrap;
}

.pb-btn-action {
  height: 32px;
  padding: 0 12px;
  border-radius: 8px;
  border: none;
  background: transparent;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 13px;
  font-weight: 500;
  white-space: nowrap;
  color: #64748b;
}

.pb-btn-icon:hover:not(:disabled) {
  background: #f1f5f9;
}

.pb-btn-icon:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pb-icon-primary:hover { color: #4f46e5; background: #e0e7ff; }
.pb-icon-danger:hover { color: #e11d48; background: #ffe4e6; }
.pb-icon-success:hover { color: #059669; background: #d1fae5; }
.pb-icon-neutral:hover { color: #475569; background: #e2e8f0; }

.pb-empty-row {
  text-align: center;
  padding: 4rem !important;
  color: #94a3b8;
}

/* Form Styles */
.pb-form-card {
  max-width: 800px;
  margin: 0 auto;
}

.pb-card-header {
  padding: 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.pb-card-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 8px 0;
  color: #1a1a2e;
}

.pb-form {
  padding: 2rem;
}

.pb-form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.pb-col-full {
  grid-column: 1 / -1;
}

.pb-form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pb-label {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.pb-input {
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 14px;
  transition: all 0.2s;
  background: #f8fafc;
}

.pb-input:focus {
  outline: none;
  border-color: #4f46e5;
  background: white;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.pb-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 2rem;
  border-top: 1px solid #f1f5f9;
}

.pb-btn {
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.pb-btn-primary {
  background: #4f46e5;
  color: white;
}

.pb-btn-primary:hover:not(:disabled) {
  background: #4338ca;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.pb-btn-primary:disabled {
  background: #818cf8;
  cursor: not-allowed;
}

.pb-btn-secondary {
  background: transparent;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.pb-btn-secondary:hover {
  background: #f8fafc;
  color: #1e293b;
}

/* Utilities */
.pb-text-center { text-align: center; }
.pb-text-right { text-align: right; }

.anim-fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
