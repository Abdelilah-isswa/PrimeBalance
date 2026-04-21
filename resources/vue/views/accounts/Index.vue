<template>
  <div class="pb-accounts-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Accounts</h1>
        <p class="pb-page-subtitle">Manage your bank accounts, credit cards, and cash registers.</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'manage' }"
            @click="activeTab = 'manage'"
          >
            Manage Accounts
            <span class="pb-tab-badge">{{ accounts.length }}</span>
          </button>
          <button 
            v-if="canManageAccounts"
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'create' }"
            @click="openCreate"
          >
            Add Account
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
                <th>Account Name</th>
                <th class="pb-text-right">Balance</th>
                <th>Status</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accounts" :key="account.id">
                <template v-if="editId === account.id">
                  <!-- EDIT MODE -->
                  <td style="padding: 8px;">
                    <input v-model="form.name" type="text" class="pb-input pb-input-sm" required autofocus>
                  </td>
                  <td style="padding: 8px; text-align: right;">
                    <input v-model="form.balance" type="number" step="0.01" class="pb-input pb-input-sm pb-text-right" required>
                  </td>
                  <td style="padding: 8px;">
                     <select v-model="form.is_active" class="pb-input pb-input-sm">
                       <option :value="true">Active</option>
                       <option :value="false">Inactive</option>
                     </select>
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
                  <td class="pb-font-medium">{{ account.name }}</td>
                  <td class="pb-text-right pb-font-bold pb-text-primary">
                    {{ company?.currency }} {{ Number(account.balance).toFixed(2) }}
                  </td>
                  <td>
                    <span :class="['pb-status-pill', account.is_active ? 'pb-status--active' : 'pb-status--inactive']">
                      {{ account.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="pb-text-center">
                    <div v-if="canManageAccounts" class="pb-action-group">
                      <button @click="startEdit(account)" class="pb-btn-icon pb-icon-primary" title="Edit">
                        <span>Edit</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      </button>
                      <button @click="destroyAccount(account)" class="pb-btn-icon pb-icon-danger" :title="account.transactions_count > 0 ? 'Archive' : 'Delete'">
                        <span>{{ account.transactions_count > 0 ? 'Archive' : 'Delete' }}</span>
                        <svg v-if="account.transactions_count > 0" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                      </button>
                    </div>
                  </td>
                </template>
              </tr>
              <tr v-if="accounts.length === 0">
                <td colspan="4" class="pb-empty-row">No accounts found. Create your first one.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab Content: Create -->
    <div v-if="activeTab === 'create' && canManageAccounts" class="pb-tab-content anim-fade-in">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Add New Account</h2>
          <p class="pb-card-subtitle">Create a new container for your funds.</p>
        </div>
        
        <form @submit.prevent="createAccount" class="pb-form">
          <div class="pb-form-grid">
            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Account Name</label>
              <input v-model="form.name" type="text" class="pb-input" placeholder="e.g., Main Checking or Petty Cash" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Starting Balance ({{ company?.currency }})</label>
              <input v-model="form.balance" type="number" step="0.01" class="pb-input" placeholder="0.00" required>
            </div>
            
            <div class="pb-form-group pb-col-full">
               <label class="pb-label">Status</label>
               <select v-model="form.is_active" class="pb-input">
                 <option :value="true">Active</option>
                 <option :value="false">Inactive</option>
               </select>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="button" class="pb-btn pb-btn-secondary" @click="activeTab = 'manage'">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Adding...' : 'Add Account' }}
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
import { useCompanyStore } from '../../stores/company.js';
import { useAccountStore } from '../../stores/account.js';

const route = useRoute();
const id = route.params.companyId;

const companyStore = useCompanyStore();
const accountStore = useAccountStore();

const activeTab = ref('manage');
const submitting = ref(false);
const editId = ref(null);

const form = ref({
  name: '',
  balance: 0,
  is_active: true
});

const company = computed(() => companyStore.currentCompany);
const accounts = computed(() => accountStore.accounts || []);
const companyMembership = computed(() => {
  return (companyStore.companies || []).find((c) => String(c.id) === String(id));
});
const currentCompanyRole = computed(() => {
  const role =
    companyMembership.value?.pivot?.role ||
    company.value?.pivot?.role ||
    company.value?.company?.pivot?.role ||
    company.value?.userRole ||
    'viewer';

  return String(role).toLowerCase();
});
const canManageAccounts = computed(() => ['owner', 'admin'].includes(currentCompanyRole.value));

onMounted(async () => {
  await Promise.all([
    companyStore.fetchCompanies(),
    companyStore.fetchCompany(id),
    accountStore.fetchAccounts(id)
  ]);
});

const openCreate = () => {
  if (!canManageAccounts.value) return;
  resetForm();
  activeTab.value = 'create';
};

const resetForm = () => {
  form.value = {
    name: '',
    balance: 0,
    is_active: true
  };
};

const createAccount = async () => {
  if (!canManageAccounts.value) return;
  submitting.value = true;
  try {
    await accountStore.createAccount(id, form.value);
    resetForm();
    activeTab.value = 'manage';
  } catch (err) {
    console.error('Failed to create account', err);
    alert(err.response?.data?.message || 'Error creating account');
  } finally {
    submitting.value = false;
  }
};

const startEdit = (acc) => {
  if (!canManageAccounts.value) return;
  editId.value = acc.id;
  form.value = {
    name: acc.name,
    balance: acc.balance,
    is_active: acc.is_active
  };
};

const cancelEdit = () => {
  editId.value = null;
  resetForm();
};

const saveEdit = async () => {
  if (!canManageAccounts.value) return;
  submitting.value = true;
  try {
    await accountStore.updateAccount(id, editId.value, form.value);
    editId.value = null;
  } catch (err) {
    console.error('Failed to update account', err);
    alert(err.response?.data?.message || 'Error updating account');
  } finally {
    submitting.value = false;
  }
};

const destroyAccount = async (account) => {
  if (!canManageAccounts.value) return;
  const msg = account.transactions_count > 0 ? 'Archive this account?' : 'Are you sure you want to delete this account?';
  if (!confirm(msg)) return;
  try {
    await accountStore.deleteAccount(id, account.id);
  } catch (err) {
    alert(err.response?.data?.message || 'Error executing action');
  }
};
</script>

<style scoped>
.pb-accounts-page {
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
.pb-status--active { background: #d1fae5; color: #065f46; }
.pb-status--inactive { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

.pb-text-primary { color: #1e293b; }
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
  padding: 0 8px;
  border-radius: 8px;
  border: none;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  color: #64748b;
  font-size: 13px;
  font-weight: 500;
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
  width: 100%;
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

.pb-card-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.pb-form {
  padding: 2rem;
}

.pb-form-grid {
  display: grid;
  grid-template-columns: 1fr;
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
