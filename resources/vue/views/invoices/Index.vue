<template>
  <div class="pb-invoices-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Invoices</h1>
        <p class="pb-page-subtitle">{{ canCreateInvoice ? `Manage your billing and generate new invoices for ${company?.name || 'your company'}` : `Manage invoices for ${company?.name || 'your company'}` }}</p>
        
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
            v-if="canCreateInvoice"
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
                <th>Created By</th>
                <th>Date</th>
                <th class="pb-text-right">Total</th>
                <th class="pb-text-right">Paid</th>
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
                <td>{{ inv.creator?.name || 'Unknown' }}</td>
                <td>{{ inv.created_at?.substring(0, 10) }}</td>
                <td class="pb-text-right pb-font-bold">
                  {{ company?.currency }} {{ Number(inv.total_amount).toFixed(2) }}
                </td>
                <td class="pb-text-right" :style="{ color: inv.amount_paid > 0 ? '#059669' : '#94a3b8' }">
                  <strong>{{ company?.currency }} {{ Number(inv.amount_paid || 0).toFixed(2) }}</strong>
                </td>
                <td class="pb-text-center">
                  <router-link :to="`/companies/${id}/invoices/${inv.id}`" class="pb-view-btn">
                    View
                  </router-link>
                </td>
              </tr>
              <tr v-if="invoices.length === 0">
                <td colspan="8" class="pb-empty-row">No invoices found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab Content: Create -->
    <div v-if="canCreateInvoice && activeTab === 'create'" class="pb-tab-content anim-fade-in">
      <div class="pb-card pb-form-card" style="max-width: 100%; width: 100%;">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Generate New Invoice</h2>
          <p class="pb-card-subtitle">Select a client, add items, set a due date, and generate the invoice.</p>
        </div>
        
        <form @submit.prevent="handleSubmit" class="pb-form" style="padding: 2rem;">
          <div v-if="formError" class="pb-alert pb-alert-error">{{ formError }}</div>
          <div v-if="formSuccess" class="pb-alert pb-alert-success">{{ formSuccess }}</div>

          <!-- Client Selection & Due Date -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
              <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #64748b; font-size: 0.9rem;">Client *</label>
              <select v-model="form.client_id" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: #fff; color: #1a1a2e;">
                <option disabled value="">Select a client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.name }}
                </option>
              </select>
            </div>

            <div>
              <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #64748b; font-size: 0.9rem;">Due Date *</label>
              <input v-model="form.due_date" type="date" required :style="{ borderColor: dueDateError ? '#ef4444' : '#cbd5e1' }" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: #fff; color: #1a1a2e;">
              <div v-if="dueDateError" style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; font-weight: 500;">{{ dueDateError }}</div>
            </div>
          </div>

          <!-- Items Section -->
          <div style="margin-bottom: 2rem; border: 1.5px solid #cbd5e1; padding: 1.5rem; border-radius: 8px; background: #f8fafc;">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.1rem; color: #1a1a2e;">Invoice Items</h3>
            
            <!-- Items List Table -->
            <div v-if="form.items.length > 0" style="margin-bottom: 1.5rem; overflow-x: auto;">
              <table style="width: 100%; border-collapse: collapse; background: #fff;">
                <thead>
                  <tr style="background: #f1f5f9; border-bottom: 1.5px solid #cbd5e1;">
                    <th style="text-align: left; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b;">Description</th>
                    <th style="text-align: right; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; width: 120px;">Price</th>
                    <th style="text-align: right; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; width: 120px;">Quantity</th>
                    <th style="text-align: right; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; width: 120px;">Total</th>
                    <th style="text-align: center; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; width: 80px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.items" :key="index" style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 0.75rem 1rem; color: #334155;">{{ item.description }}</td>
                    <td style="text-align: right; padding: 0.75rem 1rem; color: #334155; font-family: monospace;">${{ Number(item.price).toFixed(2) }}</td>
                    <td style="text-align: right; padding: 0.75rem 1rem; color: #334155; font-family: monospace;">{{ item.quantity }}</td>
                    <td style="text-align: right; padding: 0.75rem 1rem; color: #1a1a2e; font-weight: 600; font-family: monospace;">${{ calculateItemTotal(item).toFixed(2) }}</td>
                    <td style="text-align: center; padding: 0.75rem 1rem;">
                      <button type="button" @click="removeItem(index)" style="padding: 0.4rem 0.8rem; background: #ef4444; color: #fff; border: none; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">Remove</button>
                    </td>
                  </tr>
                </tbody>
              </table>
              
              <!-- Invoice Total Display -->
              <div style="text-align: right; margin-top: 1rem; padding: 1rem; background: #fff; border-radius: 6px; border: 1px solid #e2e8f0;">
                <div style="font-size: 1.2rem; color: #64748b; margin-bottom: 0.5rem;">Subtotal</div>
                <div style="font-size: 1.8rem; font-weight: 700; color: #1a1a2e;">{{ company?.currency }} {{ calculatedTotal.toFixed(2) }}</div>
              </div>
            </div>

            <!-- No Items Message -->
            <div v-if="form.items.length === 0" style="text-align: center; color: #94a3b8; padding: 2rem; font-style: italic; background: #fff; border-radius: 6px; border: 1px dashed #cbd5e1;">
              No items added yet. Click the button below to add your first item.
            </div>

            <!-- Add Item Button (Initially Visible) -->
            <div style="text-align: center;">
              <button v-if="!showItemForm" type="button" @click="showItemForm = true" style="padding: 0.55rem 1.5rem; background: #4f46e5; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                + Add Item
              </button>
            </div>

            <!-- Add Item Form (Shown on Click) -->
            <div v-if="showItemForm" style="background: #fff; padding: 1.5rem; border-radius: 6px; margin-top: 1rem; border: 1.5px solid #cbd5e1;">
              <h4 style="margin: 0 0 1rem 0; font-size: 1rem; color: #1a1a2e;">Add New Item</h4>
              <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                  <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #64748b; font-size: 0.9rem;">Description *</label>
                  <input v-model="newItem.description" type="text" placeholder="Item description" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
                </div>
                <div>
                  <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #64748b; font-size: 0.9rem;">Price *</label>
                  <input v-model.number="newItem.price" type="number" placeholder="0.00" step="0.01" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
                </div>
                <div>
                  <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #64748b; font-size: 0.9rem;">Quantity *</label>
                  <input v-model.number="newItem.quantity" type="number" placeholder="1" min="1" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
                </div>
              </div>
              <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" @click="showItemForm = false" style="padding: 0.55rem 1.2rem; background: #94a3b8; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                  Cancel
                </button>
                <button type="button" @click="addItem" style="padding: 0.55rem 1.2rem; background: #10b981; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                  Add to Invoice
                </button>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" @click="activeTab = 'manage'" style="padding: 0.55rem 1.5rem; background: #94a3b8; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
              Cancel
            </button>
            <button type="button" @click="submit('draft')" :disabled="submitting" style="padding: 0.55rem 1.5rem; background: #64748b; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; opacity: var(--opacity);" :style="{ opacity: submitting ? 0.5 : 1 }">
              {{ submitting ? 'Processing...' : 'Save as Draft' }}
            </button>
            <button type="submit" :disabled="submitting" style="padding: 0.55rem 1.5rem; background: #4f46e5; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" :style="{ opacity: submitting ? 0.5 : 1 }">
              {{ submitting ? 'Processing...' : 'Send Invoice' }}
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
const showItemForm = ref(false);
const formError = ref('');
const formSuccess = ref('');
const form = ref({
  client_id: '',
  status: 'draft',
  due_date: '',
  items: []
});

const newItem = ref({
  description: '',
  price: 0,
  quantity: 1
});

const invoices = computed(() => invoiceStore.invoices);
const company = computed(() => companyStore.currentCompany);
const clients = computed(() => clientStore.clients);
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
const canCreateInvoice = computed(() => {
  return currentCompanyRole.value !== 'viewer' && currentCompanyRole.value !== 'viwer';
});

const calculatedTotal = computed(() => {
  return form.value.items.reduce((sum, item) => {
    return sum + calculateItemTotal(item);
  }, 0);
});

const calculateItemTotal = (item) => {
  return (Number(item.price) || 0) * (Number(item.quantity) || 0);
};

const dueDateError = computed(() => {
  if (!form.value.due_date) return '';
  const selectedDate = new Date(form.value.due_date);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  if (selectedDate <= today) {
    return 'Due date must be after today';
  }
  return '';
});

const addItem = () => {
  if (!newItem.value.description || !newItem.value.price || newItem.value.quantity < 1) {
    return;
  }
  
  form.value.items.push({
    description: newItem.value.description,
    price: Number(newItem.value.price),
    quantity: Number(newItem.value.quantity)
  });
  
  // Reset new item form and hide it
  newItem.value = {
    description: '',
    price: 0,
    quantity: 1
  };
  showItemForm.value = false;
};

const removeItem = (index) => {
  form.value.items.splice(index, 1);
};

onMounted(async () => {
  await Promise.all([
    companyStore.fetchCompanies(),
    companyStore.fetchCompany(id),
    invoiceStore.fetchInvoices(id),
    clientStore.fetchClients(id)
  ]);

  if (!canCreateInvoice.value) {
    activeTab.value = 'manage';
  }
});

const submit = async (action) => {
  formError.value = '';
  formSuccess.value = '';

  if (!form.value.client_id) {
    formError.value = 'Please select a client before sending the invoice.';
    return;
  }

  if (form.value.items.length === 0) {
    formError.value = 'Add at least one item before sending the invoice.';
    return;
  }

  if (!form.value.due_date) {
    formError.value = 'Please choose a due date.';
    return;
  }

  if (dueDateError.value) {
    formError.value = dueDateError.value;
    return;
  }

  submitting.value = true;
  try {
    const submitData = {
      ...form.value,
      status: 'draft',
    };

    // If action is 'send', automatically send email
    if (action === 'send') {
      submitData.send_email = true;
    }

    await invoiceStore.createInvoice(id, submitData);
    formSuccess.value = action === 'send'
      ? 'Invoice created and email was requested for sending.'
      : 'Invoice saved as draft.';
    activeTab.value = 'manage';
    // Reset form
    form.value = { client_id: '', status: 'draft', due_date: '', items: [] };
    showItemForm.value = false;
  } catch (error) {
    console.error('Failed to create invoice:', error);
    formError.value = error?.response?.data?.message || 'Failed to create invoice. Please try again.';
  } finally {
    submitting.value = false;
  }
};

const handleSubmit = async (e) => {
  // This is called by the form's @submit.prevent
  await submit('send');
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

.pb-alert {
  padding: 0.9rem 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  font-size: 14px;
  font-weight: 500;
}

.pb-alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.pb-alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

/* Utilities */
.pb-text-right { text-align: right; }
.pb-text-center { text-align: center; }
.pb-font-bold { font-weight: 700; }

.anim-fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
