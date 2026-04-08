<template>
  <div class="pb-invoices-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Invoices</h1>
        <p class="pb-page-subtitle">Manage your billing and generate new invoices for {{ company?.name }}</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'manage' }"
            @click="activeTab = 'manage'"
          >
            Manage Invoices
            <span class="pb-tab-badge">{{ invoices.length }}</span>
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'create' }"
            @click="activeTab = 'create'"
          >
            Generate Invoice
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
                <th>Invoice ID</th>
                <th>Client</th>
                <th>Status</th>
                <th>Date</th>
                <th class="pb-text-right">Amount</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inv in invoices" :key="inv.id">
                <td>
                  <span class="pb-id-label">#{{ inv.id }}</span>
                </td>
                <td>
                  <div class="pb-client-cell">
                    <div class="pb-client-avatar">{{ inv.client?.name?.charAt(0) }}</div>
                    <span>{{ inv.client?.name }}</span>
                  </div>
                </td>
                <td>
                  <span :class="['pb-status-pill', `pb-status--${inv.status}`]">
                    {{ inv.status }}
                  </span>
                </td>
                <td>{{ inv.created_at?.substring(0, 10) }}</td>
                <td class="pb-text-right pb-font-bold">
                  {{ company?.currency }} {{ Number(inv.total_amount).toFixed(2) }}
                </td>
                <td class="pb-text-center">
                  <router-link :to="`/companies/${id}/invoices/${inv.id}`" class="pb-view-btn">
                    View
                  </router-link>
                </td>
              </tr>
              <tr v-if="invoices.length === 0">
                <td colspan="6" class="pb-empty-row">No invoices found.</td>
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
          <h2 class="pb-card-title">Generate New Invoice</h2>
          <p class="pb-card-subtitle">Select a client and enter the invoice details below.</p>
        </div>
        
        <form @submit.prevent="createInvoice" class="pb-form">
          <div class="pb-form-grid">
            <div class="pb-form-group">
              <label class="pb-label">Client</label>
              <select v-model="form.client_id" class="pb-input" required>
                <option disabled value="">Select a client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.name }}
                </option>
              </select>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Amount ({{ company?.currency }})</label>
              <input 
                v-model="form.total_amount" 
                type="number" 
                step="0.01" 
                class="pb-input" 
                placeholder="0.00"
                required
              >
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Status</label>
              <select v-model="form.status" class="pb-input">
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="paid">Paid</option>
              </select>
            </div>

            <div class="pb-form-group pb-flex-end">
              <label class="pb-checkbox-label">
                <input type="checkbox" v-model="form.send_email">
                <span>Send invoice via email to client</span>
              </label>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="button" class="pb-btn pb-btn-secondary" @click="activeTab = 'manage'">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Generating...' : 'Generate Invoice' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCompanyStore } from '../../stores/company.js';
import { useInvoiceStore } from '../../stores/invoice.js';
import { useClientStore } from '../../stores/client.js';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;

const companyStore = useCompanyStore();
const invoiceStore = useInvoiceStore();
const clientStore = useClientStore();

const activeTab = ref('manage');
const submitting = ref(false);
const form = ref({
  client_id: '',
  total_amount: '',
  status: 'draft',
  send_email: false
});

const invoices = computed(() => invoiceStore.invoices);
const company = computed(() => companyStore.currentCompany);
const clients = computed(() => clientStore.clients);

onMounted(async () => {
  await Promise.all([
    companyStore.fetchCompany(id),
    invoiceStore.fetchInvoices(id),
    clientStore.fetchClients(id)
  ]);
});

const createInvoice = async () => {
  submitting.value = true;
  try {
    await invoiceStore.createInvoice(id, form.value);
    activeTab.value = 'manage';
    // Reset form
    form.value = { client_id: '', total_amount: '', status: 'draft', send_email: false };
  } catch (error) {
    console.error('Failed to create invoice:', error);
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-invoices-page {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.pb-page-header {
  margin-bottom: 2rem;
  padding-bottom: 0; /* Let tabs handle the bottom padding/line */
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

.pb-id-label {
  font-family: monospace;
  background: #f1f5f9;
  padding: 4px 8px;
  border-radius: 6px;
  color: #64748b;
  font-weight: 600;
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
.pb-status--sent { background: #dbeafe; color: #1e40af; }
.pb-status--draft { background: #f1f5f9; color: #475569; }
.pb-status--overdue { background: #fee2e2; color: #991b1b; }

.pb-view-btn {
  color: #4f46e5;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.15s;
}

.pb-view-btn:hover {
  color: #312e81;
  text-decoration: underline;
}

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

.pb-checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #475569;
  cursor: pointer;
}

.pb-flex-end {
  justify-content: flex-end;
  padding-bottom: 12px;
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

/* Utilities */
.pb-text-right { text-align: right; }
.pb-text-center { text-align: center; }
.pb-font-bold { font-weight: 700; }

.anim-fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
