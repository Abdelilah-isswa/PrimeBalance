<template>
  <div class="pb-bill-show">
    <!-- Header with Back Button and Quick Actions -->
    <div class="pb-page-header">
      <div class="pb-header-top">
        <router-link :to="`/companies/${id}/bills`" class="pb-back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Back to Bills
        </router-link>
        
        <div class="pb-actions">
          <button @click="printBill" class="pb-btn pb-btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
          </button>
          <router-link v-if="bill?.status !== 'paid'" :to="`/companies/${id}/bills/${billId}/pay`" class="pb-btn pb-btn-primary">
            Record Payment
          </router-link>
        </div>
      </div>
    </div>

    <div class="pb-bill-container">
      <!-- Premium Bill Card -->
      <div class="pb-card pb-bill-card anim-slide-up">
        <div class="pb-bill-header">
          <div class="pb-brand-section">
            <div class="pb-logo-placeholder pb-logo--amber">BILL</div>
            <div>
              <h2 class="pb-company-name">{{ bill?.supplier?.name }}</h2>
              <p class="pb-company-sub">Vendor Invoice / Bill</p>
            </div>
          </div>
          <div class="pb-bill-meta">
            <h1 class="pb-bill-title">PAYABLE</h1>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Bill ID:</span>
              <span class="pb-meta-value">#{{ bill?.id }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Date:</span>
              <span class="pb-meta-value">{{ formatDate(bill?.created_at) }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Status:</span>
              <span :class="['pb-status-pill', `pb-status--${bill?.status}`]">{{ bill?.status }}</span>
            </div>
          </div>
        </div>

        <div class="pb-bill-details">
          <div class="pb-pay-to">
            <h3 class="pb-section-title">Record of Purchase</h3>
            <div class="pb-vendor-box">
              <p class="pb-vendor-name">{{ bill?.supplier?.name }}</p>
              <p class="pb-vendor-email">{{ bill?.supplier?.email }}</p>
              <p class="pb-vendor-address">{{ bill?.supplier?.address || 'Supplier address not on file' }}</p>
            </div>
          </div>
        </div>

        <div class="pb-bill-table-wrapper">
          <table class="pb-bill-table">
            <thead>
              <tr>
                <th>Description</th>
                <th class="pb-text-right">Total Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Expense recorded from {{ bill?.supplier?.name }}</td>
                <td class="pb-text-right pb-font-bold">{{ company?.currency }} {{ Number(bill?.total_amount).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pb-bill-footer">
          <div class="pb-summary">
            <div class="pb-summary-row pb-total-row">
              <span>Total Payable</span>
              <span>{{ company?.currency }} {{ Number(bill?.total_amount).toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Options Sidebar -->
      <div class="pb-sidebar anim-fade-in">
        <div class="pb-card pb-options-card">
          <h3 class="pb-card-title-sm">Bill Options</h3>
          <div class="pb-option-list">
            <router-link :to="`/companies/${id}/bills/${billId}/edit`" class="pb-option-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              <span>Edit Bill</span>
            </router-link>
            <button @click="destroy" class="pb-option-item pb-option-danger">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
              <span>Delete Bill</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const billId = route.params.billId;

const company = ref(null);
const bill = ref(null);

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/bills/${billId}`);
  company.value = res.data.data.company;
  bill.value = res.data.data.bill;
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const printBill = () => {
  window.print();
};

const destroy = async () => {
  if (!confirm('Delete this bill? This action cannot be undone.')) return;
  try {
    await axios.delete(`/companies/${id}/bills/${billId}`);
    router.push(`/companies/${id}/bills`);
  } catch (err) {
    alert(err.response?.data?.message || 'Error deleting bill');
  }
};
</script>

<style scoped>
.pb-bill-show {
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
  color: #f59e0b;
}

.pb-actions {
  display: flex;
  gap: 12px;
}

.pb-bill-container {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 2rem;
  align-items: start;
}

@media (max-width: 992px) {
  .pb-bill-container {
    grid-template-columns: 1fr;
  }
}

.pb-card {
  background: white;
  border-radius: 24px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
}

.pb-bill-card {
  padding: 3rem;
}

.pb-bill-header {
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
  background: #f59e0b;
  color: white;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
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

.pb-bill-meta {
  text-align: right;
}

.pb-bill-title {
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

.pb-bill-details {
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

.pb-vendor-name {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.pb-vendor-email, .pb-vendor-address {
  font-size: 14px;
  color: #64748b;
  margin: 2px 0;
}

.pb-bill-table-wrapper {
  margin-bottom: 3rem;
}

.pb-bill-table {
  width: 100%;
  border-collapse: collapse;
}

.pb-bill-table th {
  text-align: left;
  padding: 1rem 0;
  border-bottom: 2px solid #f1f5f9;
  color: #94a3b8;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.pb-bill-table td {
  padding: 1.5rem 0;
  border-bottom: 1px solid #f1f5f9;
  color: #1e293b;
  font-size: 15px;
}

.pb-bill-footer {
  display: flex;
  justify-content: flex-end;
  padding-top: 1rem;
}

.pb-summary {
  width: 300px;
}

.pb-total-row {
  display: flex;
  justify-content: space-between;
  color: #1e293b;
  font-weight: 800;
  font-size: 22px;
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
  color: #f59e0b;
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
  background: #f59e0b;
  color: white;
}

.pb-btn-primary:hover {
  background: #d97706;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
.pb-status--unpaid { background: #fee2e2; color: #991b1b; }
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
}
</style>
