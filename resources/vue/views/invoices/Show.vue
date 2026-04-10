<template>
  <div class="pb-invoice-show">
    <!-- Header with Back Button and Quick Actions -->
    <div class="pb-page-header">
      <div class="pb-header-top">
        <router-link :to="`/companies/${id}/invoices`" class="pb-back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Back to Invoices
        </router-link>
        
        <div class="pb-actions">
          <button @click="printInvoice" class="pb-btn pb-btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
          </button>
          <button @click="downloadPdf" class="pb-btn pb-btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
          </button>
          <router-link v-if="!isViewer && invoice?.status !== 'paid'" :to="`/companies/${id}/invoices/${invoiceId}/receive`" class="pb-btn pb-btn-primary">
            Receive Payment
          </router-link>
        </div>
      </div>
    </div>

    <div class="pb-invoice-container">
      <!-- Premium Invoice Card -->
      <div class="pb-card pb-invoice-card anim-slide-up">
        <div class="pb-invoice-header">
          <div class="pb-brand-section">
            <div class="pb-logo-placeholder">PB</div>
            <div>
              <h2 class="pb-company-name">{{ company?.name }}</h2>
              <p class="pb-company-sub">Premium Billing Solutions</p>
            </div>
          </div>
          <div class="pb-invoice-meta">
            <h1 class="pb-invoice-title">INVOICE</h1>
            <div class="pb-meta-row">
              <span class="pb-meta-label">ID:</span>
              <span class="pb-meta-value">#{{ invoice?.id }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Date:</span>
              <span class="pb-meta-value">{{ formatDate(invoice?.created_at) }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Due Date:</span>
              <span class="pb-meta-value">{{ formatDate(invoice?.due_date) }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Created By:</span>
              <span class="pb-meta-value">{{ invoice?.creator?.name || 'Unknown' }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Status:</span>
              <span :class="['pb-status-pill', `pb-status--${invoice?.status}`]">{{ invoice?.status }}</span>
            </div>
          </div>
        </div>

        <div class="pb-invoice-billing">
          <div class="pb-bill-to">
            <h3 class="pb-section-title">Billed To</h3>
            <div class="pb-client-box">
              <p class="pb-client-name">{{ invoice?.client?.name }}</p>
              <p class="pb-client-email">{{ invoice?.client?.email }}</p>
              <p class="pb-client-address">{{ invoice?.client?.address || 'No address provided' }}</p>
              <p class="pb-client-phone">{{ invoice?.client?.phone }}</p>
            </div>
          </div>
        </div>

        <div class="pb-invoice-table-wrapper">
          <table class="pb-invoice-table">
            <thead>
              <tr>
                <th>Description</th>
                <th class="pb-text-right">Quantity</th>
                <th class="pb-text-right">Price</th>
                <th class="pb-text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in invoice?.items" :key="item.id">
                <td>{{ item.description }}</td>
                <td class="pb-text-right">{{ item.quantity }}</td>
                <td class="pb-text-right">{{ company?.currency }} {{ Number(item.price).toFixed(2) }}</td>
                <td class="pb-text-right pb-font-bold">{{ company?.currency }} {{ Number(item.total).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pb-invoice-footer">
          <div class="pb-notes">
            <h4 class="pb-notes-title">Notes & Terms</h4>
            <p class="pb-notes-text">Payment is due within 15 days. Thank you for your business!</p>
          </div>
          <div class="pb-summary">
            <div class="pb-summary-row">
              <span>Subtotal</span>
              <span>{{ company?.currency }} {{ Number(invoice?.total_amount).toFixed(2) }}</span>
            </div>
            <div class="pb-summary-row">
              <span>Tax (0%)</span>
              <span>{{ company?.currency }} 0.00</span>
            </div>
            <div class="pb-summary-row pb-total-row">
              <span>Amount Due</span>
              <span>{{ company?.currency }} {{ Number(invoice?.total_amount).toFixed(2) }}</span>
            </div>
            <div class="pb-summary-row" v-if="invoice?.amount_paid > 0">
              <span class="pb-text-green">Amount Paid</span>
              <span class="pb-text-green">{{ company?.currency }} {{ Number(invoice?.amount_paid).toFixed(2) }}</span>
            </div>
            <div class="pb-summary-row pb-remaining-row" v-if="invoice?.amount_paid > 0 && invoice?.status !== 'paid'">
              <span>Remaining</span>
              <span class="pb-text-red">{{ company?.currency }} {{ (Number(invoice?.total_amount) - Number(invoice?.amount_paid)).toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Options Sidebar -->
      <div v-if="!isViewer" class="pb-sidebar anim-fade-in">
        <div class="pb-card pb-options-card">
          <h3 class="pb-card-title-sm">Invoice Options</h3>
          <div class="pb-option-list">
            <router-link :to="`/companies/${id}/invoices/${invoiceId}/edit`" class="pb-option-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              <span>Edit Invoice</span>
            </router-link>
            <router-link v-if="invoice?.status !== 'paid'" :to="`/companies/${id}/invoices/${invoiceId}/receive`" class="pb-option-item pb-option-success">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              <span>{{ invoice?.status === 'partial' ? 'Record Remaining Payment' : 'Mark as Paid' }}</span>
            </router-link>
            <button @click="destroy" class="pb-option-item pb-option-danger">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
              <span>Delete Invoice</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useInvoiceStore } from '../../stores/invoice.js';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const invoiceId = route.params.invoiceId;

const invoiceStore = useInvoiceStore();
const company = ref(null);
const invoice = ref(null);
const isViewer = computed(() => {
  const role = String(company.value?.pivot?.role || 'viewer').toLowerCase();
  return role === 'viewer';
});

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/invoices/${invoiceId}`);
  company.value = res.data.data.company;
  invoice.value = res.data.data.invoice;
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const printInvoice = () => {
  window.print();
};

const downloadPdf = async () => {
  try {
    const response = await axios.get(`/companies/${id}/invoices/${invoiceId}/pdf`, {
      responseType: 'blob',
    });

    const blobUrl = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = blobUrl;
    link.setAttribute('download', `invoice-${invoiceId}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(blobUrl);
  } catch (err) {
    alert(err.response?.data?.message || 'Error downloading PDF');
  }
};

const destroy = async () => {
  if (!confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) return;
  try {
    await axios.delete(`/companies/${id}/invoices/${invoiceId}`);
    router.push(`/companies/${id}/invoices`);
  } catch (err) {
    alert(err.response?.data?.message || 'Error deleting invoice');
  }
};
</script>

<style scoped>
.pb-invoice-show {
  max-width: 1200px;
  margin: 0 auto;
  padding-bottom: 4rem;
}

.pb-page-header {
  margin-bottom: 2rem;
}

.pb-header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pb-back-link {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: color 0.2s;
}

.pb-back-link:hover {
  color: #4f46e5;
}

.pb-actions {
  display: flex;
  gap: 12px;
}

.pb-invoice-container {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 2rem;
  align-items: start;
}

@media (max-width: 992px) {
  .pb-invoice-container {
    grid-template-columns: 1fr;
  }
}

.pb-card {
  background: white;
  border-radius: 24px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}

.pb-invoice-card {
  padding: 3rem;
}

.pb-invoice-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 4rem;
}

.pb-brand-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.pb-logo-placeholder {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 18px;
}

.pb-company-name {
  font-size: 20px;
  font-weight: 800;
  color: #1a1a2e;
  margin: 0;
}

.pb-company-sub {
  font-size: 13px;
  color: #64748b;
  margin: 2px 0 0 0;
}

.pb-invoice-meta {
  text-align: right;
}

.pb-invoice-title {
  font-size: 32px;
  font-weight: 900;
  color: #1e293b;
  letter-spacing: -1px;
  margin: 0 0 1rem 0;
}

.pb-meta-row {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-bottom: 4px;
}

.pb-meta-label {
  color: #94a3b8;
  font-weight: 600;
  font-size: 13px;
}

.pb-meta-value {
  color: #1e293b;
  font-weight: 700;
  font-size: 13px;
}

.pb-invoice-billing {
  margin-bottom: 4rem;
}

.pb-section-title {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  color: #94a3b8;
  letter-spacing: 1px;
  margin-bottom: 1rem;
}

.pb-client-name {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.pb-client-email, .pb-client-address, .pb-client-phone {
  font-size: 14px;
  color: #64748b;
  margin: 2px 0;
}

.pb-invoice-table-wrapper {
  margin-bottom: 4rem;
}

.pb-invoice-table {
  width: 100%;
  border-collapse: collapse;
}

.pb-invoice-table th {
  text-align: left;
  padding: 1rem 0;
  border-bottom: 2px solid #f1f5f9;
  color: #94a3b8;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.pb-invoice-table td {
  padding: 1.5rem 0;
  border-bottom: 1px solid #f1f5f9;
  color: #1e293b;
  font-size: 15px;
}

.pb-invoice-footer {
  display: flex;
  justify-content: space-between;
  gap: 4rem;
  padding-top: 2rem;
}

.pb-notes {
  flex: 1;
}

.pb-notes-title {
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 8px 0;
}

.pb-notes-text {
  font-size: 14px;
  color: #64748b;
  line-height: 1.6;
}

.pb-summary {
  width: 250px;
}

.pb-summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 14px;
  color: #64748b;
}

.pb-total-row {
  margin-top: 20px;
  padding-top: 15px;
  border-top: 2px solid #f1f5f9;
  color: #1e293b;
  font-weight: 800;
  font-size: 18px;
}

/* Sidebar Options */
.pb-options-card {
  padding: 1.5rem;
}

.pb-card-title-sm {
  font-size: 14px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 1.5rem;
}

.pb-option-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pb-option-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  color: #475569;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s;
  background: transparent;
  width: 100%;
  border: none;
  text-align: left;
  cursor: pointer;
}

.pb-option-item:hover {
  background: #f8fafc;
  color: #4f46e5;
}

.pb-option-danger:hover {
  background: #fff1f2;
  color: #e11d48;
}

/* Buttons */
.pb-btn {
  padding: 10px 20px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.pb-btn-primary {
  background: #4f46e5;
  color: white;
}

.pb-btn-primary:hover {
  background: #4338ca;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.pb-btn-secondary {
  background: white;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.pb-btn-secondary:hover {
  background: #f8fafc;
  color: #1e293b;
}

/* Status Pills (Same as list) */
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

/* Animations */
.anim-slide-up {
  animation: slideUp 0.5s ease-out;
}

.anim-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@media print {
  .pb-page-header, .pb-sidebar {
    display: none !important;
  }
  .pb-invoice-show {
    padding: 0;
  }
  .pb-invoice-container {
    display: block;
  }
  .pb-card {
    border: none;
    box-shadow: none;
  }
}
</style>
