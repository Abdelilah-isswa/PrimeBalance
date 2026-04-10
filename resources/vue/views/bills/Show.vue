<template>
  <div class="pb-bill-show">
    <div class="pb-page-header">
      <div class="pb-header-top">
        <router-link :to="`/companies/${id}/bills`" class="pb-back-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Back to Bills
        </router-link>
        
        <div class="pb-actions">
          <button @click="downloadPdf" class="pb-btn pb-btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
          </button>
          <button @click="window.print()" class="pb-btn pb-btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
          </button>
        </div>
      </div>
    </div>

    <div class="pb-bill-container" v-if="bill">
      <div class="pb-card pb-bill-card anim-slide-up">
        <div class="pb-bill-header">
          <div class="pb-brand-section">
            <div class="pb-logo-placeholder">PB</div>
            <div>
              <h2 class="pb-company-name">{{ company?.name }}</h2>
              <p class="pb-company-sub">Supplier Bill Record</p>
            </div>
          </div>
          <div class="pb-bill-meta">
            <h1 class="pb-bill-title">BILL</h1>
            <div class="pb-meta-row">
              <span class="pb-meta-label">ID:</span>
              <span class="pb-meta-value">#{{ bill?.id }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Date:</span>
              <span class="pb-meta-value">{{ bill?.created_at?.substring(0, 10) }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Due:</span>
              <span class="pb-meta-value">{{ bill?.due_date ? bill.due_date.substring(0, 10) : '-' }}</span>
            </div>
            <div class="pb-meta-row">
              <span class="pb-meta-label">Status:</span>
              <span :class="['pb-status-pill', `pb-status--${bill?.status}`]">{{ bill?.status }}</span>
            </div>
          </div>
        </div>

        <div class="pb-bill-billing">
          <div class="pb-bill-from">
            <h3 class="pb-section-title">From Supplier</h3>
            <div class="pb-supplier-box">
              <p class="pb-supplier-name">{{ bill?.supplier?.name }}</p>
              <p class="pb-supplier-email">{{ bill?.supplier?.email }}</p>
              <p class="pb-supplier-address">{{ bill?.supplier?.address || 'No address provided' }}</p>
            </div>
          </div>
        </div>

        <div class="pb-bill-table-wrapper">
          <table class="pb-bill-table">
            <thead>
              <tr>
                <th>Description</th>
                <th class="pb-text-left">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ bill.description || 'General Supplies/Services' }}</td>
                <td class="pb-text-left pb-font-bold">{{ company?.currency }} {{ Number(bill.total_amount).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pb-bill-footer">
          <div class="pb-summary">
            <div class="pb-summary-row">
              <span>Amount Paid</span>
              <span style="color:#059669; font-weight:800;">{{ company?.currency }} {{ Number(bill.amount_paid || 0).toFixed(2) }}</span>
            </div>
            <div class="pb-summary-row" v-if="Number(bill.total_amount) - Number(bill.amount_paid || 0) > 0">
              <span>Remaining</span>
              <span style="color:#dc2626; font-weight:800;">{{ company?.currency }} {{ (Number(bill.total_amount) - Number(bill.amount_paid || 0)).toFixed(2) }}</span>
            </div>
            <div class="pb-summary-row pb-total-row">
              <span>Total Amount</span>
              <span>{{ company?.currency }} {{ Number(bill.total_amount).toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!isViewer" class="pb-sidebar anim-fade-in">
        <div class="pb-card pb-options-card">
          <h3 class="pb-card-title-sm">Bill Options</h3>
          <div class="pb-option-list">
            <router-link
              v-if="bill?.status !== 'paid'"
              :to="`/companies/${id}/bills/${billId}/edit`"
              class="pb-option-item"
            >
              <span>Edit Bill</span>
            </router-link>

            <router-link
              v-if="bill?.status !== 'paid'"
              :to="`/companies/${id}/bills/${billId}/pay`"
              class="pb-option-item"
            >
              <span>Record Payment</span>
            </router-link>

            <button v-if="Number(bill?.amount_paid || 0) === 0" @click="destroy" class="pb-option-item pb-option-danger">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
              <span>Delete Bill</span>
            </button>

            <div v-else style="color:#64748b; font-size:13px; font-weight:600; padding:10px 12px;">
              Bills linked to transactions cannot be deleted.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const billId = route.params.billId;

const company = ref(null);
const bill = ref(null);
const isViewer = computed(() => {
  const role = String(company.value?.pivot?.role || 'viewer').toLowerCase();
  return role === 'viewer';
});

onMounted(async () => {
  try {
    const res = await axios.get(`companies/${id}/bills/${billId}`);
    company.value = res.data.data.company;
    bill.value = res.data.data.bill;
  } catch (err) {
    console.error(err);
  }
});

const destroy = async () => {
  if (!confirm('Are you sure you want to delete this bill?')) return;
  try {
    await axios.delete(`companies/${id}/bills/${billId}`);
    router.push(`/companies/${id}/bills`);
  } catch (err) {
    alert(err.response?.data?.message || 'Error deleting bill');
  }
};

const downloadPdf = async () => {
  try {
    const response = await axios.get(`companies/${id}/bills/${billId}/pdf`, {
      responseType: 'blob',
    });

    const blobUrl = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = blobUrl;
    link.setAttribute('download', `bill-${billId}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(blobUrl);
  } catch (err) {
    alert(err.response?.data?.message || 'Error downloading PDF');
  }
};
</script>

<style scoped>
.pb-bill-show { max-width: 1200px; margin: 0 auto; padding-bottom: 4rem; }
.pb-page-header { margin-bottom: 2rem; }
.pb-header-top { display: flex; justify-content: space-between; align-items: center; }
.pb-back-link { display: flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s; }
.pb-back-link:hover { color: #4f46e5; }
.pb-actions { display: flex; gap: 12px; }
.pb-btn { padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; border: none; }
.pb-btn-secondary { background: white; color: #1a1a2e; border: 1px solid #e2e8f0; }
.pb-bill-container { display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start; }
.pb-card { background: white; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
.pb-bill-card { padding: 3rem; }
.pb-bill-header { display: flex; justify-content: space-between; margin-bottom: 4rem; }
.pb-brand-section { display: flex; align-items: center; gap: 1rem; }
.pb-logo-placeholder { width: 44px; height: 44px; background: #4f46e5; color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; }
.pb-company-name { font-size: 20px; font-weight: 800; color: #1a1a2e; margin: 0; }
.pb-company-sub { font-size: 13px; color: #64748b; margin: 0; }
.pb-bill-meta { text-align: right; }
.pb-bill-title { font-size: 32px; font-weight: 900; color: #1a1a2e; margin: 0 0 1rem; letter-spacing: -1px; }
.pb-meta-row { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 4px; font-size: 14px; }
.pb-meta-label { color: #64748b; font-weight: 500; }
.pb-meta-value { color: #1a1a2e; font-weight: 700; }
.pb-status-pill { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.pb-status--paid { background: #d1fae5; color: #065f46; }
.pb-status--unpaid { background: #fef3c7; color: #92400e; }
.pb-status--partial { background: #dbeafe; color: #1d4ed8; }
.pb-status--cancelled { background: #fee2e2; color: #991b1b; }
.pb-bill-billing { margin-bottom: 3rem; }
.pb-section-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 1rem; }
.pb-supplier-name { font-size: 18px; font-weight: 700; color: #1a1a2e; margin: 0 0 4px; }
.pb-supplier-email, .pb-supplier-address { font-size: 14px; color: #64748b; margin: 2px 0; }
.pb-bill-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
.pb-bill-table th { text-align: left; padding: 12px; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 13px; font-weight: 600; }
.pb-bill-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.pb-bill-footer { display: flex; justify-content: flex-end; }
.pb-summary { width: 300px; }
.pb-summary-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; }
.pb-total-row { border-bottom: none; color: #1a1a2e; font-size: 18px; font-weight: 800; }
.pb-sidebar { display: flex; flex-direction: column; gap: 1.5rem; }
.pb-options-card { padding: 1.5rem; }
.pb-card-title-sm { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 1.25rem; text-transform: uppercase; letter-spacing: 0.5px; }
.pb-option-list { display: flex; flex-direction: column; gap: 8px; }
.pb-option-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; color: #475569; text-decoration: none; font-size: 14px; font-weight: 600; border: none; background: #f8fafc; cursor: pointer; text-align: left; }
.pb-option-item:hover { background: #f1f5f9; color: #1a1a2e; }
.pb-option-danger { color: #e11d48; background: #fff1f2; }
.pb-option-danger:hover { background: #ffe4e6; color: #be123c; }
.anim-slide-up { animation: slideUp 0.5s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
