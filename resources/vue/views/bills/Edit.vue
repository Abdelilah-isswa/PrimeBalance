<template>
  <div class="pb-bill-edit">
    <div class="pb-page-header">
      <div class="pb-header-top">
        <router-link :to="`/companies/${id}/bills/${billId}`" class="pb-back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Back to Bill
        </router-link>
      </div>
      <h1 class="pb-page-title">Edit Bill #{{ billId }}</h1>
      <p class="pb-page-subtitle">Update bill details for {{ company?.name }}</p>
    </div>

    <div class="pb-card pb-form-card" v-if="form">
      <div class="pb-card-header">
        <h2 class="pb-card-title">Bill Details</h2>
        <p class="pb-card-subtitle">You can edit bills only if they are not fully paid.</p>
      </div>

      <div v-if="error" class="pb-alert pb-alert-error">{{ error }}</div>
      <div v-if="successMsg" class="pb-alert pb-alert-success">{{ successMsg }}</div>

      <form @submit.prevent="update" class="pb-form">
        <div class="pb-form-grid">
          <div class="pb-form-group">
            <label class="pb-label">Supplier</label>
            <select v-model="form.supplier_id" class="pb-input" :disabled="readOnly">
              <option disabled value="">Select a supplier</option>
              <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>

          <div class="pb-form-group">
            <label class="pb-label">Due Date</label>
            <input v-model="form.due_date" type="date" class="pb-input" :disabled="readOnly" />
          </div>

          <div class="pb-form-group pb-form-group--full">
            <label class="pb-label">Description</label>
            <textarea v-model="form.description" class="pb-input" rows="3" :disabled="readOnly" placeholder="e.g. Office supplies, services..." />
          </div>

          <div class="pb-form-group">
            <label class="pb-label">Amount ({{ company?.currency }})</label>
            <input v-model="form.total_amount" type="number" step="0.01" min="0" class="pb-input" :disabled="readOnly" required />
          </div>

        </div>

        <div class="pb-form-actions">
          <router-link :to="`/companies/${id}/bills/${billId}`" class="pb-btn pb-btn-secondary">Cancel</router-link>
          <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting || readOnly">
            {{ submitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>

    <div v-else class="pb-loading">Loading bill...</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const billId = route.params.billId;

const company = ref(null);
const suppliers = ref([]);
const form = ref(null);

const submitting = ref(false);
const error = ref('');
const successMsg = ref('');

const readOnly = computed(() => {
  return form.value?.status === 'paid' || Number(form.value?.transactions_count || 0) > 0;
});

onMounted(async () => {
  try {
    const [billRes, suppliersRes] = await Promise.all([
      axios.get(`companies/${id}/bills/${billId}`),
      axios.get(`companies/${id}/suppliers/balances`),
    ]);

    company.value = billRes.data.data.company;
    suppliers.value = suppliersRes.data.data.suppliers ?? [];

    const bill = billRes.data.data.bill;
    form.value = {
      supplier_id: bill.supplier_id,
      description: bill.description || '',
      due_date: bill.due_date || '',
      total_amount: bill.total_amount,
      transactions_count: bill.transactions_count || 0,
    };

    if (Number(bill.transactions_count || 0) > 0) {
      error.value = 'This bill is linked to transactions and cannot be edited.';
    } else if (bill.status === 'paid') {
      error.value = 'This bill is fully paid and cannot be edited.';
    }
  } catch (err) {
    error.value = 'Failed to load bill.';
  }
});

const update = async () => {
  submitting.value = true;
  error.value = '';
  successMsg.value = '';

  try {
    await axios.put(`companies/${id}/bills/${billId}`, form.value);
    successMsg.value = 'Bill updated successfully!';
    setTimeout(() => router.push(`/companies/${id}/bills/${billId}`), 800);
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update bill.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-bill-edit { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.pb-page-header { margin-bottom: 2rem; }
.pb-header-top { margin-bottom: 1rem; }
.pb-back-link { display: flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s; }
.pb-back-link:hover { color: #4f46e5; }
.pb-page-title { font-size: 28px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; margin: 0 0 4px; }
.pb-page-subtitle { color: #64748b; font-size: 14px; }

.pb-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; width: 100%; }
.pb-form-card { padding: 0; }
.pb-card-header { padding: 1.5rem 1.75rem 0; }
.pb-card-title { margin: 0; font-size: 16px; font-weight: 800; color: #1a1a2e; }
.pb-card-subtitle { margin: 6px 0 0; color: #64748b; font-size: 13px; }

.pb-form { padding: 1.5rem 1.75rem 1.75rem; }
.pb-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.pb-form-group--full { grid-column: 1 / -1; }
.pb-label { display:block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; }
.pb-input { width: 100%; padding: 10px 12px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 14px; }
.pb-input:disabled { background: #f1f5f9; cursor: not-allowed; }

.pb-form-actions { margin-top: 1.25rem; display: flex; justify-content: flex-end; gap: 10px; }
.pb-btn { padding: 10px 16px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
.pb-btn-secondary { background: white; color: #1a1a2e; border: 1px solid #e2e8f0; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.pb-alert { margin: 1rem 1.75rem 0; padding: 10px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; }
.pb-alert-error { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.pb-alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

.pb-loading { color: #64748b; font-weight: 600; }
</style>
