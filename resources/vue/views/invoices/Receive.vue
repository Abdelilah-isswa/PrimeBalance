<template>
  <div class="pb-receive-payment">
    <div class="pb-page-header">
      <div class="pb-header-top">
        <router-link :to="`/companies/${id}/invoices/${invoiceId}`" class="pb-back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Back to Invoice
        </router-link>
      </div>
      <h1 class="pb-page-title">Record Payment</h1>
      <p class="pb-page-subtitle">Invoice #{{ invoiceId }} — {{ company?.name }}</p>
    </div>

    <!-- Invoice Summary -->
    <div class="pb-invoice-summary" v-if="invoice">
      <div class="pb-summary-stat">
        <span class="pb-stat-label">Total Amount</span>
        <span class="pb-stat-value">{{ company?.currency }} {{ Number(invoice.total_amount).toFixed(2) }}</span>
      </div>
      <div class="pb-summary-stat">
        <span class="pb-stat-label">Already Paid</span>
        <span class="pb-stat-value pb-text-green">{{ company?.currency }} {{ Number(invoice.amount_paid || 0).toFixed(2) }}</span>
      </div>
      <div class="pb-summary-stat">
        <span class="pb-stat-label">Remaining</span>
        <span class="pb-stat-value pb-text-red">{{ company?.currency }} {{ remaining }}</span>
      </div>
      <div class="pb-summary-stat">
        <span class="pb-stat-label">Status</span>
        <span :class="['pb-status-pill', `pb-status--${invoice.status}`]">{{ invoice.status }}</span>
      </div>
    </div>

    <!-- Success Result -->
    <div v-if="result" class="pb-result-card">
      <div class="pb-result-icon">✓</div>
      <h2>Payment Recorded!</h2>
      <div class="pb-result-rows">
        <div class="pb-result-row">
          <span>Amount Paid Now</span>
          <span class="pb-text-green">+ {{ company?.currency }} {{ Number(result.paid).toFixed(2) }}</span>
        </div>
        <div class="pb-result-row">
          <span>New Status</span>
          <span :class="['pb-status-pill', `pb-status--${result.status}`]">{{ result.status }}</span>
        </div>
        <div class="pb-result-row" v-if="result.remaining > 0">
          <span>Still Outstanding</span>
          <span class="pb-text-red">{{ company?.currency }} {{ Number(result.remaining).toFixed(2) }}</span>
        </div>
      </div>
      <router-link :to="`/companies/${id}/invoices/${invoiceId}`" class="pb-btn pb-btn-primary" style="margin-top: 1.5rem;">
        View Invoice
      </router-link>
    </div>

    <!-- Payment Form -->
    <div class="pb-card pb-form-card" v-else-if="invoice && invoice.status !== 'paid'">
      <div class="pb-card-header">
        <h2 class="pb-card-title">Payment Details</h2>
        <p class="pb-card-subtitle">Enter the payment amount and select which account to credit.</p>
      </div>

      <div v-if="error" class="pb-alert pb-alert-error">{{ error }}</div>

      <form @submit.prevent="submit" class="pb-form">
        <div class="pb-form-grid">
          <div class="pb-form-group">
            <label class="pb-label">Amount Being Paid ({{ company?.currency }})</label>
            <input v-model="form.amount_paid" type="number" step="0.01" class="pb-input" :placeholder="`Max: ${remaining}`" :max="remaining" required min="0.01" />
            <span class="pb-hint">Enter less than the total to record a partial payment</span>
          </div>

          <div class="pb-form-group">
            <label class="pb-label">Payment Date</label>
            <input v-model="form.date" type="date" class="pb-input" required />
          </div>

          <div class="pb-form-group">
            <label class="pb-label">Credit To Account</label>
            <select v-model="form.account_id" class="pb-input" required>
              <option disabled value="">Select account</option>
              <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                {{ acc.name }} ({{ company?.currency }}{{ Number(acc.balance).toFixed(2) }})
              </option>
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
          <router-link :to="`/companies/${id}/invoices/${invoiceId}`" class="pb-btn pb-btn-secondary">Cancel</router-link>
          <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
            {{ submitting ? 'Recording...' : 'Record Payment' }}
          </button>
        </div>
      </form>
    </div>
    <div v-else-if="invoice?.status === 'paid'" class="pb-card" style="padding: 3rem; text-align: center; color: #059669;">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 1rem; display:block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Invoice Fully Paid</h2>
      <p style="color: #64748b; margin-bottom: 1.5rem;">This invoice has already been fully paid.</p>
      <router-link :to="`/companies/${id}/invoices/${invoiceId}`" class="pb-btn pb-btn-secondary">← Back to Invoice</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const invoiceId = route.params.invoiceId;

const company = ref(null);
const invoice = ref(null);
const accounts = ref([]);
const categories = ref([]);
const submitting = ref(false);
const error = ref('');
const result = ref(null);

const form = ref({
  amount_paid: '',
  date: new Date().toISOString().substring(0, 10),
  account_id: '',
  category_id: '',
});

const remaining = computed(() => {
  if (!invoice.value) return '0.00';
  const total = Number(invoice.value.total_amount);
  const paid = Number(invoice.value.amount_paid || 0);
  return Math.max(0, total - paid).toFixed(2);
});

onMounted(async () => {
  try {
    const res = await axios.get(`companies/${id}/invoices/${invoiceId}/receive`);
    company.value = res.data.data.company;
    invoice.value = res.data.data.invoice;
    accounts.value = res.data.data.accounts;
    categories.value = res.data.data.categories;
    form.value.amount_paid = remaining.value;
  } catch (err) {
    error.value = 'Failed to load invoice data.';
  }
});

const submit = async () => {
  submitting.value = true;
  error.value = '';
  try {
    const res = await axios.post(`companies/${id}/invoices/${invoiceId}/receive`, form.value);
    result.value = res.data.data;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to record payment.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-receive-payment { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.pb-page-header { margin-bottom: 2rem; }
.pb-header-top { margin-bottom: 1rem; }
.pb-back-link { display: flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s; }
.pb-back-link:hover { color: #4f46e5; }
.pb-page-title { font-size: 28px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; margin: 0 0 4px; }
.pb-page-subtitle { color: #64748b; font-size: 14px; }

/* Summary bar */
.pb-invoice-summary {
  display: flex;
  gap: 2rem;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.5rem 2rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  align-items: center;
}
.pb-summary-stat { display: flex; flex-direction: column; gap: 4px; }
.pb-stat-label { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.pb-stat-value { font-size: 20px; font-weight: 700; color: #1a1a2e; }

/* Result */
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

/* Form */
.pb-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; width: 100%; }
.pb-form-card { width: 100%; }
.pb-card-header { padding: 2rem; border-bottom: 1px solid #f1f5f9; }
.pb-card-title { font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #1a1a2e; }
.pb-card-subtitle { font-size: 14px; color: #64748b; margin: 0; }
.pb-form { padding: 2rem; }
.pb-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.pb-form-group { display: flex; flex-direction: column; gap: 6px; }
.pb-label { font-size: 13px; font-weight: 600; color: #64748b; }
.pb-hint { font-size: 12px; color: #94a3b8; }
.pb-input { padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: #f8fafc; transition: all 0.2s; }
.pb-input:focus { outline: none; border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79,70,229,0.1); }
.pb-form-actions { display: flex; justify-content: flex-end; gap: 12px; padding-top: 2rem; border-top: 1px solid #f1f5f9; }
.pb-btn { padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; display: inline-flex; align-items: center; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:hover:not(:disabled) { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
.pb-btn-primary:disabled { background: #818cf8; cursor: not-allowed; }
.pb-btn-secondary { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
.pb-btn-secondary:hover { background: #f8fafc; }
.pb-alert { padding: 1rem; border-radius: 10px; margin: 1rem 2rem; font-size: 14px; font-weight: 500; }
.pb-alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

/* Status pills */
.pb-status-pill { display: inline-flex; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.pb-status--paid { background: #d1fae5; color: #065f46; }
.pb-status--partial { background: #fef3c7; color: #92400e; }
.pb-status--sent { background: #dbeafe; color: #1e40af; }
.pb-status--draft { background: #f1f5f9; color: #475569; }
.pb-status--overdue { background: #fee2e2; color: #991b1b; }

.pb-text-green { color: #059669; }
.pb-text-red { color: #e11d48; }
</style>
