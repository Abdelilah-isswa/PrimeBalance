<template>
  <div class="pb-suppliers-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Suppliers</h1>
        <p class="pb-page-subtitle">Manage your vendor relationships and track accounts payable balances for {{ company?.name }}</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'manage' }"
            @click="activeTab = 'manage'"
          >
            Manage Suppliers
            <span class="pb-tab-badge">{{ suppliers.length }}</span>
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'create' }"
            @click="activeTab = 'create'"
          >
            Add New Supplier
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
                <th>Vendor Name</th>
                <th>Email Address</th>
                <th>Status</th>
                <th class="pb-text-right">Our Balance</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="supplier in suppliers" :key="supplier.id">
                <td>
                  <div class="pb-supplier-cell">
                    <div class="pb-supplier-avatar">{{ supplier.name?.charAt(0) }}</div>
                    <div>
                      <div class="pb-supplier-name">{{ supplier.name }}</div>
                      <div class="pb-supplier-sub">{{ supplier.phone }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ supplier.email }}</td>
                <td>
                  <span v-if="supplier.balance < 0" class="pb-status-pill pb-status--overdue">
                    We owe {{ company?.currency }}{{ Math.abs(supplier.balance).toFixed(2) }}
                  </span>
                  <span v-else-if="supplier.balance > 0" class="pb-status-pill pb-status--paid">
                    Overpaid
                  </span>
                  <span v-else class="pb-status-pill pb-status--draft">
                    Settled
                  </span>
                </td>
                <td class="pb-text-right pb-font-bold" :style="{ color: supplier.balance < 0 ? '#b91c1c' : (supplier.balance > 0 ? '#059669' : '#475569') }">
                  {{ company?.currency }} {{ Number(supplier.balance).toFixed(2) }}
                </td>
              </tr>
              <tr v-if="suppliers.length === 0">
                <td colspan="4" class="pb-empty-row">No suppliers found.</td>
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
          <h2 class="pb-card-title">Add New Supplier</h2>
          <p class="pb-card-subtitle">Enter the vendor information to add them to your directory.</p>
        </div>
        
        <form @submit.prevent="createSupplier" class="pb-form">
          <div class="pb-form-grid">
            <div class="pb-form-group">
              <label class="pb-label">Company Name</label>
              <input v-model="form.name" type="text" class="pb-input" placeholder="e.g. Acme Corp" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Email Address</label>
              <input v-model="form.email" type="email" class="pb-input" placeholder="sales@acme.com" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Phone Number</label>
              <input v-model="form.phone" type="text" class="pb-input" placeholder="+1 (555) 000-0000" required>
            </div>

            <div class="pb-form-group">
              <label class="pb-label">Business Address</label>
              <input v-model="form.address" type="text" class="pb-input" placeholder="456 Vendor Blvd, Suite 100" required>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="button" class="pb-btn pb-btn-secondary" @click="activeTab = 'manage'">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Adding...' : 'Add Supplier' }}
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
import { useSupplierStore } from '../../stores/supplier.js';

const route = useRoute();
const id = route.params.companyId;

const companyStore = useCompanyStore();
const supplierStore = useSupplierStore();

const activeTab = ref('manage');
const submitting = ref(false);
const form = ref({
  name: '',
  email: '',
  phone: '',
  address: ''
});

const suppliers = computed(() => supplierStore.suppliers);
const company = computed(() => companyStore.currentCompany);

onMounted(async () => {
  await Promise.all([
    companyStore.fetchCompany(id),
    supplierStore.fetchSuppliers(id)
  ]);
});

const createSupplier = async () => {
  submitting.value = true;
  try {
    await supplierStore.createSupplier(id, form.value);
    activeTab.value = 'manage';
    // Reset form
    form.value = { name: '', email: '', phone: '', address: '' };
  } catch (error) {
    console.error('Failed to add supplier:', error);
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-suppliers-page {
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

.pb-supplier-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pb-supplier-avatar {
  width: 32px;
  height: 32px;
  background: #f59e0b;
  color: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.pb-supplier-name {
  font-weight: 700;
  color: #1e293b;
}

.pb-supplier-sub {
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
