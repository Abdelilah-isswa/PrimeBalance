<template>
  <div>
    <div style="padding:2rem; width: 100%;">
      <h1 style="margin-bottom: 0.5rem;">{{ isEdit ? 'Edit Invoice' : (client?.name ? 'Create Invoice for ' + client.name : 'Create Invoice') }}</h1>
      
      <!-- Success Result -->
      <div v-if="result" class="pb-result-card">
        <div class="pb-result-icon" aria-hidden="true">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2>Invoice Sent Successfully!</h2>
        <div class="pb-result-rows">
          <div class="pb-result-row">
            <span>Invoice #</span>
            <span class="pb-text-blue">{{ result.id }}</span>
          </div>
          <div class="pb-result-row">
            <span>Status</span>
            <span :class="['pb-status-pill', `pb-status--${result.status}`]">{{ result.status }}</span>
          </div>
          <div class="pb-result-row">
            <span>Amount</span>
            <span>{{ result.total_amount }}</span>
          </div>
        </div>
        <p style="color: #64748b; margin-top: 1.5rem; font-size: 0.95rem;">The invoice has been sent to {{ result.client?.name }} and is now marked as sent.</p>
        <router-link :to="`/companies/${companyId}/invoices/${result.id}`" class="pb-btn pb-btn-primary" style="margin-top: 1.5rem;">
          View Invoice
        </router-link>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="handleSubmit">
        <div v-if="formError" class="pb-alert pb-alert-error">{{ formError }}</div>
        <div v-if="formSuccess" class="pb-alert pb-alert-success">{{ formSuccess }}</div>
        
        <!-- Client Selection & Due Date -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
          <div v-if="!isEdit">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #64748b; font-size: 0.9rem;">Client *</label>
            <select v-model="form.client_id" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: #fff; color: #1a1a2e;">
              <option disabled value="">Select a client</option>
              <option v-for="entry in clients" :key="entry.id" :value="entry.id">{{ entry.name }}</option>
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
              <div style="font-size: 1.8rem; font-weight: 700; color: #1a1a2e;">{{ calculatedTotal.toFixed(2) }}</div>
            </div>
          </div>

          <!-- Add Item Button -->
          <div style="text-align: center;">
            <button v-if="!showItemForm" type="button" @click="showItemForm = true" style="padding: 0.55rem 1.5rem; background: #4f46e5; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
              + Add Item
            </button>
          </div>

          <!-- Add Item Form -->
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
          <router-link :to="`/companies/${companyId}`"><button type="button" style="padding: 0.55rem 1.5rem; background: #94a3b8; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">Cancel</button></router-link>
          <button v-if="isEdit" type="button" @click="submit('draft')" :disabled="submitting" style="padding: 0.55rem 1.5rem; background: #64748b; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" :style="{ opacity: submitting ? 0.5 : 1 }">
            {{ submitting ? 'Saving...' : 'Save Changes' }}
          </button>
          <button v-if="!isEdit" type="button" @click="submit('draft')" :disabled="submitting" style="padding: 0.55rem 1.5rem; background: #64748b; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" :style="{ opacity: submitting ? 0.5 : 1 }">
            {{ submitting ? 'Processing...' : 'Save as Draft' }}
          </button>
          <button type="submit" :disabled="submitting" style="padding: 0.55rem 1.5rem; background: #4f46e5; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" :style="{ opacity: submitting ? 0.5 : 1 }">
            {{ submitting ? 'Processing...' : isEdit ? 'Save & Send' : 'Send Invoice' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.pb-result-card {
  background: white;
  border: 1px solid #a7f3d0;
  border-radius: 20px;
  padding: 3rem;
  text-align: center;
  margin-bottom: 2rem;
}
.pb-result-icon { font-size: 2rem; width: 60px; height: 60px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #059669; }
.pb-result-card h2 { font-size: 1.5rem; color: #1a1a2e; margin-bottom: 1.5rem; }
.pb-result-rows { display: flex; flex-direction: column; gap: 1rem; text-align: left; max-width: 360px; margin: 0 auto; }
.pb-result-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 10px; font-size: 14px; }
.pb-btn { padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; display: inline-flex; align-items: center; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
.pb-status-pill { display: inline-flex; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.pb-status--paid { background: #d1fae5; color: #065f46; }
.pb-status--sent { background: #dbeafe; color: #1e40af; }
.pb-status--draft { background: #f1f5f9; color: #475569; }
.pb-text-blue { color: #2563eb; font-weight: 600; }
</style>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useInvoiceStore } from '../../stores/invoice.js';
import { useClientStore } from '../../stores/client.js';

const route = useRoute();
const router = useRouter();
const companyId = route.params.companyId;
const clientId = route.params.clientId;
const invoiceId = route.params.invoiceId;

const invoiceStore = useInvoiceStore();
const clientStore = useClientStore();

const isEdit = computed(() => !!invoiceId);
const clients = computed(() => clientStore.clients || []);
const client = ref(null);
const submitting = ref(false);
const showItemForm = ref(false);
const result = ref(null);
const formError = ref('');
const formSuccess = ref('');
const form = ref({ 
  total_amount: '', 
  status: 'draft', 
  due_date: '',
  client_id: clientId,
  items: [],
});

const newItem = ref({
  description: '',
  price: 0,
  quantity: 1,
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
    quantity: Number(newItem.value.quantity),
  });
  
  newItem.value = {
    description: '',
    price: 0,
    quantity: 1,
  };
  showItemForm.value = false;
};

const removeItem = (index) => {
  form.value.items.splice(index, 1);
};

onMounted(async () => {
  if (!isEdit.value) {
    await clientStore.fetchClients(companyId);
  }

  if (isEdit.value) {
    const res = await invoiceStore.fetchInvoice(companyId, invoiceId);
    form.value = { 
      total_amount: res.total_amount, 
      status: res.status,
      due_date: res.due_date,
      client_id: res.client_id,
      items: res.items || [],
    };
    client.value = res.client;
  }
});

const submit = async (action) => {
  formError.value = '';
  formSuccess.value = '';

  if (!isEdit.value && !clients.value.length) {
    formError.value = 'No clients available. Please create a client first.';
    return;
  }

  if (!isEdit.value && !form.value.client_id) {
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
      ...(isEdit.value ? {} : { client_id: form.value.client_id }),
      status: form.value.status,
      due_date: form.value.due_date,
      items: form.value.items,
    };

    if (action === 'send') {
      submitData.send_email = true;
    }

    if (isEdit.value) {
      await invoiceStore.updateInvoice(companyId, invoiceId, submitData);
      if (action === 'send') {
        // For edit + send, show success and navigate after
        result.value = invoiceStore.currentInvoice;
        formSuccess.value = 'Invoice updated and email was requested for sending.';
      } else {
        router.push(`/companies/${companyId}/invoices/${invoiceId}`);
      }
    } else {
      const invoice = await invoiceStore.createInvoice(companyId, submitData);
      if (action === 'send') {
        // Show success card for 3 seconds then navigate
        result.value = invoice;
        formSuccess.value = 'Invoice created and email was requested for sending.';
      } else {
        router.push(`/companies/${companyId}`);
      }
    }
  } catch (error) {
    console.error('Error:', error);
    formError.value = error?.response?.data?.message || 'Failed to save invoice';
  } finally {
    submitting.value = false;
  }
};

const handleSubmit = async () => {
  await submit('send');
};
</script>

<style scoped>
.pb-result-card {
  background: white;
  border: 1px solid #a7f3d0;
  border-radius: 20px;
  padding: 3rem;
  text-align: center;
  margin-bottom: 2rem;
}
.pb-result-icon { font-size: 2rem; width: 60px; height: 60px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #059669; }
.pb-result-card h2 { font-size: 1.5rem; color: #1a1a2e; margin-bottom: 1.5rem; }
.pb-result-rows { display: flex; flex-direction: column; gap: 1rem; text-align: left; max-width: 360px; margin: 0 auto; }
.pb-result-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 10px; font-size: 14px; }
.pb-btn { padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; display: inline-flex; align-items: center; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
.pb-status-pill { display: inline-flex; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.pb-status--paid { background: #d1fae5; color: #065f46; }
.pb-status--sent { background: #dbeafe; color: #1e40af; }
.pb-status--draft { background: #f1f5f9; color: #475569; }
.pb-text-blue { color: #2563eb; font-weight: 600; }
</style>
