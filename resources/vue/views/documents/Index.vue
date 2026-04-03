<template>
  <div>
    <NavBar />
    <div style="padding:2rem;">
      <h1>Documents - {{ company?.name }}</h1>
      <div style="margin:1rem 0;">
        <label><strong>View:</strong></label>
        <select v-model="type" @change="load" style="padding:0.5rem; margin-left:0.5rem; border-radius:4px;">
          <option value="invoices">Invoices</option>
          <option value="bills">Bills</option>
        </select>
      </div>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">ID</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">{{ type === 'invoices' ? 'Client' : 'Supplier' }}</th>
            <th style="padding:0.5rem; text-align:right; border:1px solid #ddd;">Amount</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Status</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Date</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documents" :key="doc.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">#{{ doc.id }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ type === 'invoices' ? doc.client?.name : doc.supplier?.name }}</td>
            <td style="padding:0.5rem; text-align:right; border:1px solid #ddd;">{{ company?.currency }} {{ Number(doc.total_amount).toFixed(2) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <span :style="{ padding:'0.25rem 0.5rem', borderRadius:'4px', background: doc.status === 'paid' ? '#e8f5e9' : doc.status === 'cancelled' ? '#ffebee' : '#fff9c4', color: doc.status === 'paid' ? '#2e7d32' : doc.status === 'cancelled' ? '#c62828' : '#f57f17' }">
                {{ doc.status }}
              </span>
            </td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ doc.created_at?.substring(0,10) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <router-link :to="`/companies/${id}/${type}/${doc.id}`"><button style="padding:0.25rem 0.5rem;">View</button></router-link>
            </td>
          </tr>
        </tbody>
      </table>
      <router-link :to="`/companies/${id}`"><button>Back</button></router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import NavBar from '../../components/NavBar.vue';

const route = useRoute();
const id = route.params.companyId;
const company = ref(null);
const documents = ref([]);
const type = ref('invoices');

const load = async () => {
  const res = await axios.get(`/companies/${id}/documents`, { params: { type: type.value } });
  company.value = res.data.data.company;
  documents.value = res.data.data.documents;
};

onMounted(load);
</script>
