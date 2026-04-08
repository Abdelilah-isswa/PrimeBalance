<template>
  <div class="pb-invoice-edit">
    <div class="pb-page-header">
      <div class="pb-header-top">
        <router-link :to="`/companies/${id}/invoices/${invoiceId}`" class="pb-back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Back to Invoice
        </router-link>
      </div>
      <h1 class="pb-page-title">Edit Invoice #{{ invoiceId }}</h1>
      <p class="pb-page-subtitle">Update invoice details for {{ company?.name }}</p>
    </div>

    <div class="pb-card pb-form-card" v-if="form">
      <div class="pb-card-header">
        <h2 class="pb-card-title">Invoice Details</h2>
        <p class="pb-card-subtitle">Modify the invoice amount or status below.</p>
      </div>

      <div v-if="error" class="pb-alert pb-alert-error">{{ error }}</div>
      <div v-if="successMsg" class="pb-alert pb-alert-success">{{ successMsg }}</div>

      <form @submit.prevent="update" class="pb-form">
        <div class="pb-form-grid">
          <div class="pb-form-group">
            <label class="pb-label">Client</label>
            <input :value="form.client_name" class="pb-input" disabled style="background:#f1f5f9; cursor:not-allowed;" />
          </div>

          <div class="pb-form-group">
            <label class="pb-label">Amount ({{ company?.currency }})</label>
            <input v-model="form.total_amount" type="number" step="0.01" class="pb-input" required min="0.01" />
          </div>

          <div class="pb-form-group">
            <label class="pb-label">Status</label>
            <select v-model="form.status" class="pb-input">
              <option value="draft">Draft</option>
              <option value="sent">Sent</option>
              <option value="paid">Paid</option>
              <option value="partial">Partial</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
        </div>

        <div class="pb-form-actions">
          <router-link :to="`/companies/${id}/invoices/${invoiceId}`" class="pb-btn pb-btn-secondary">Cancel</router-link>
          <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
            {{ submitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>

    <div v-else class="pb-loading">Loading invoice...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const invoiceId = route.params.invoiceId;

const company = ref(null);
const form = ref(null);
const submitting = ref(false);
const error = ref('');
const successMsg = ref('');

onMounted(async () => {
  try {
    const res = await axios.get(`companies/${id}/invoices/${invoiceId}`);
    company.value = res.data.data.company;
    const inv = res.data.data.invoice;
    form.value = {
      total_amount: inv.total_amount,
      status: inv.status,
      client_name: inv.client?.name || '',
    };
  } catch (err) {
    error.value = 'Failed to load invoice.';
  }
});

const update = async () => {
  submitting.value = true;
  error.value = '';
  try {
    await axios.put(`companies/${id}/invoices/${invoiceId}`, {
      total_amount: form.value.total_amount,
      status: form.value.status,
    });
    successMsg.value = 'Invoice updated successfully!';
    setTimeout(() => router.push(`/companies/${id}/invoices/${invoiceId}`), 1000);
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update invoice.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-invoice-edit { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.pb-page-header { margin-bottom: 2rem; }
.pb-header-top { margin-bottom: 1rem; }
.pb-back-link { display: flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s; }
.pb-back-link:hover { color: #4f46e5; }
.pb-page-title { font-size: 28px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; margin: 0 0 4px; }
.pb-page-subtitle { color: #64748b; font-size: 14px; }

.pb-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; width: 100%; }
.pb-form-card { width: 100%; }
.pb-card-header { padding: 2rem; border-bottom: 1px solid #f1f5f9; }
.pb-card-title { font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #1a1a2e; }
.pb-card-subtitle { font-size: 14px; color: #64748b; margin: 0; }
.pb-form { padding: 2rem; }
.pb-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.pb-form-group { display: flex; flex-direction: column; gap: 8px; }
.pb-label { font-size: 13px; font-weight: 600; color: #64748b; }
.pb-input { padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.pb-input:focus { outline: none; border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79,70,229,0.1); }
.pb-form-actions { display: flex; justify-content: flex-end; gap: 12px; padding-top: 2rem; border-top: 1px solid #f1f5f9; }
.pb-btn { padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; display: inline-flex; align-items: center; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:hover:not(:disabled) { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
.pb-btn-primary:disabled { background: #818cf8; cursor: not-allowed; }
.pb-btn-secondary { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
.pb-btn-secondary:hover { background: #f8fafc; }
.pb-alert { padding: 1rem; border-radius: 10px; margin: 1.5rem 2rem; font-size: 14px; font-weight: 500; }
.pb-alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.pb-alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.pb-loading { padding: 4rem; text-align: center; color: #94a3b8; }
</style>
