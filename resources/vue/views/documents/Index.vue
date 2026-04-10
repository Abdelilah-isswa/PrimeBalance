<template>
  <div class="pb-documents-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Documents</h1>
        <p class="pb-page-subtitle">View and manage history of documents for {{ company?.name }}</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': type === 'invoices' }"
            @click="type = 'invoices'; load()"
          >
            Invoices
            <span class="pb-tab-badge">{{ invoiceCount }}</span>
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': type === 'bills' }"
            @click="type = 'bills'; load()"
          >
            Bills
            <span class="pb-tab-badge">{{ billCount }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tab Content -->
    <div class="pb-tab-content anim-fade-in">
      <div class="pb-card">
        <div class="pb-table-wrapper">
          <table class="pb-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>{{ type === 'invoices' ? 'Client' : 'Supplier' }}</th>
                <th>Status</th>
                <th>Date</th>
                <th class="pb-text-right">Amount</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="doc in documents" :key="doc.id">
                <td>
                  <span class="pb-id-label">#{{ doc.id }}</span>
                </td>
                <td>
                  <div class="pb-client-cell">
                    <div class="pb-client-avatar">
                      {{ (type === 'invoices' ? doc.client?.name : doc.supplier?.name)?.charAt(0) || 'D' }}
                    </div>
                    <span>{{ type === 'invoices' ? doc.client?.name : doc.supplier?.name }}</span>
                  </div>
                </td>
                <td>
                  <span :class="[
                      'pb-status-pill', 
                      doc.status === 'paid' ? 'pb-status--paid' : 
                      doc.status === 'cancelled' ? 'pb-status--overdue' : 
                      'pb-status--draft'
                    ]">
                    {{ doc.status }}
                  </span>
                </td>
                <td>{{ doc.created_at?.substring(0, 10) }}</td>
                <td class="pb-text-right pb-font-bold">{{ company?.currency }} {{ Number(doc.total_amount).toFixed(2) }}</td>
                <td class="pb-text-center">
                  <router-link :to="`/companies/${id}/${type}/${doc.id}`" class="pb-view-btn">
                    View Document
                  </router-link>
                </td>
              </tr>
              <tr v-if="documents.length === 0">
                <td colspan="6" class="pb-empty-row">No documents found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const id = route.params.companyId;
const company = ref(null);
const documents = ref([]);
const type = ref('invoices');
const invoiceCount = ref(0);
const billCount = ref(0);

const load = async () => {
  try {
    const activeType = type.value;
    const otherType = activeType === 'invoices' ? 'bills' : 'invoices';

    const [activeRes, otherRes] = await Promise.all([
      axios.get(`companies/${id}/documents`, { params: { type: activeType } }),
      axios.get(`companies/${id}/documents`, { params: { type: otherType } }),
    ]);

    company.value = activeRes.data.data.company;
    documents.value = activeRes.data.data.documents;

    const activeCount = activeRes.data.data.documents.length;
    const otherCount = otherRes.data.data.documents.length;

    if (activeType === 'invoices') {
      invoiceCount.value = activeCount;
      billCount.value = otherCount;
    } else {
      billCount.value = activeCount;
      invoiceCount.value = otherCount;
    }
  } catch (err) {
    console.error("Failed to load documents", err);
  }
};

onMounted(load);
</script>

<style scoped>
.pb-documents-page {
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
  vertical-align: middle;
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

/* Utilities */
.pb-text-right { text-align: right; }
.pb-text-center { text-align: center; }
.pb-font-bold { font-weight: 700; }
.pb-font-medium { font-weight: 500; }

.anim-fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
