<template>
  <div class="pb-clients-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Clients</h1>
        <p class="pb-page-subtitle">Manage your customer base and track their outstanding balances for {{ company?.name }}</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'manage' }"
            @click="activeTab = 'manage'"
          >
            Manage Clients
            <span class="pb-tab-badge">{{ clients.length }}</span>
          </button>
          <button 
            v-if="canManageClients"
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'create' }"
            @click="activeTab = 'create'"
          >
            Add New Client
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
                <th>Customer Name</th>
                <th>Email Address</th>
                <th>Status</th>
                <th class="pb-text-right">Balance</th>
                <th v-if="canManageClients" class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in clients" :key="client.id">
                <td v-if="editId === client.id">
                  <input v-model="editForm.name" type="text" class="pb-input pb-input-sm" required>
                </td>
                <td v-else>
                  <div class="pb-client-cell">
                    <div class="pb-client-avatar">{{ client.name?.charAt(0) }}</div>
                    <div>
                      <div class="pb-client-name">{{ client.name }}</div>
                      <div class="pb-client-sub">{{ client.phone }}</div>
                    </div>
                  </div>
                </td>
                <td v-if="editId === client.id">
                  <input v-model="editForm.email" type="email" class="pb-input pb-input-sm" required>
                </td>
                <td v-else>{{ client.email }}</td>
                <td>
                  <span v-if="client.balance < 0" class="pb-status-pill pb-status--overdue">
                    Owes {{ company?.currency }}{{ Math.abs(client.balance).toFixed(2) }}
                  </span>
                  <span v-else-if="client.balance > 0" class="pb-status-pill pb-status--paid">
                    Overpaid
                  </span>
                  <span v-else class="pb-status-pill pb-status--draft">
                    Settled
                  </span>
                </td>
                <td class="pb-text-right pb-font-bold" :style="{ color: client.balance < 0 ? '#b91c1c' : (client.balance > 0 ? '#059669' : '#475569') }">
                  {{ company?.currency }} {{ Number(client.balance).toFixed(2) }}
                </td>
                <td v-if="canManageClients" class="pb-text-center">
                  <div class="pb-actions-cell">
                    <template v-if="editId === client.id">
                      <button class="pb-action-btn pb-action-btn--primary" :disabled="submitting" @click="saveEdit">Save</button>
                      <button class="pb-action-btn" :disabled="submitting" @click="cancelEdit">Cancel</button>
                    </template>
                    <template v-else>
                      <button class="pb-action-btn" @click="startEdit(client)">Edit</button>
                      <button class="pb-action-btn pb-action-btn--danger" @click="deleteClient(client)">Delete</button>
                    </template>
                  </div>
                </td>
              </tr>
              <tr v-if="clients.length === 0">
                <td :colspan="canManageClients ? 5 : 4" class="pb-empty-row">No clients found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab Content: Create -->
    <div v-if="canManageClients && activeTab === 'create'" class="pb-tab-content anim-fade-in">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Add New Client</h2>
          <p class="pb-card-subtitle">Enter the customer information to add them to your directory.</p>
        </div>
        
        <form @submit.prevent="createClient" class="pb-form">
          <div class="pb-form-grid">
            <div class="pb-form-group">
              <label class="pb-label">Full Name</label>
              <input v-model="form.name" type="text" class="pb-input" placeholder="e.g. John Doe" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Email Address</label>
              <input v-model="form.email" type="email" class="pb-input" placeholder="customer@example.com" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Phone Number</label>
              <input v-model="form.phone" type="text" class="pb-input" placeholder="+1 (555) 000-0000" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Billing Address</label>
              <input v-model="form.address" type="text" class="pb-input" placeholder="123 Business St, City" required>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="button" class="pb-btn pb-btn-secondary" @click="activeTab = 'manage'">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Adding...' : 'Add Client' }}
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
import { useClientStore } from '../../stores/client.js';

const route = useRoute();
const id = route.params.companyId;

const companyStore = useCompanyStore();
const clientStore = useClientStore();

const activeTab = ref('manage');
const submitting = ref(false);
const editId = ref(null);
const editForm = ref({ name: '', email: '', phone: '', address: '' });
const form = ref({
  name: '',
  email: '',
  phone: '',
  address: ''
});

const clients = computed(() => clientStore.clients);
const company = computed(() => companyStore.currentCompany);
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
const canManageClients = computed(() => ['owner', 'admin', 'accountant'].includes(currentCompanyRole.value));

onMounted(async () => {
  await Promise.all([
    companyStore.fetchCompanies(),
    companyStore.fetchCompany(id),
    clientStore.fetchClients(id)
  ]);

  if (!canManageClients.value) {
    activeTab.value = 'manage';
  }
});

const createClient = async () => {
  if (!canManageClients.value) return;
  submitting.value = true;
  try {
    await clientStore.createClient(id, form.value);
    activeTab.value = 'manage';
    // Reset form
    form.value = { name: '', email: '', phone: '', address: '' };
  } catch (error) {
    console.error('Failed to add client:', error);
  } finally {
    submitting.value = false;
  }
};

const startEdit = (client) => {
  if (!canManageClients.value) return;

  editId.value = client.id;
  editForm.value = {
    name: client.name || '',
    email: client.email || '',
    phone: client.phone || '',
    address: client.address || '',
  };
};

const cancelEdit = () => {
  editId.value = null;
  editForm.value = { name: '', email: '', phone: '', address: '' };
};

const saveEdit = async () => {
  if (!canManageClients.value || !editId.value) return;

  submitting.value = true;
  try {
    await clientStore.updateClient(id, editId.value, editForm.value);
    await clientStore.fetchClients(id);
    cancelEdit();
  } catch (error) {
    alert(error?.response?.data?.message || 'Failed to update client');
  } finally {
    submitting.value = false;
  }
};

const deleteClient = async (client) => {
  if (!canManageClients.value) return;

  if (!confirm(`Delete client ${client.name}?`)) return;

  try {
    await clientStore.deleteClient(id, client.id);
  } catch (error) {
    alert(error?.response?.data?.message || 'Failed to delete client');
  }
};
</script>

<style scoped>
.pb-clients-page {
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
}

.pb-table tr:hover {
  background: #f8fafc;
}

.pb-client-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pb-client-avatar {
  width: 32px;
  height: 32px;
  background: #4f46e5;
  color: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.pb-client-name {
  font-weight: 700;
  color: #1e293b;
}

.pb-client-sub {
  font-size: 12px;
  color: #94a3b8;
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
.pb-status--draft { background: #f1f5f9; color: #475569; }
.pb-status--overdue { background: #fee2e2; color: #991b1b; }

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

.pb-form {
  padding: 2rem;
}

.pb-form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
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

.pb-input-sm {
  padding: 8px 10px;
  border-radius: 10px;
  font-size: 13px;
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

.pb-btn-secondary {
  background: transparent;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.pb-btn-secondary:hover {
  background: #f8fafc;
  color: #1e293b;
}

.pb-actions-cell {
  display: inline-flex;
  gap: 8px;
}

.pb-action-btn {
  border: 1px solid #cbd5e1;
  background: #fff;
  color: #334155;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.pb-action-btn--primary {
  border-color: #4f46e5;
  color: #4f46e5;
}

.pb-action-btn--danger {
  border-color: #ef4444;
  color: #ef4444;
}

/* Utilities */
.pb-text-right { text-align: right; }
.pb-text-center { text-align: center; }
.pb-font-bold { font-weight: 700; }

.anim-fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
