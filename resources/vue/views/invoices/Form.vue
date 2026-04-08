<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>{{ isEdit ? 'Edit Invoice' : 'Create Invoice for ' + client?.name }}</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;"><label>Total Amount:</label><br><input v-model="form.total_amount" type="number" step="0.01" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;">
          <label>Status:</label><br>
          <select v-model="form.status" style="width:100%; padding:0.5rem;">
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
            <option value="paid">Paid</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div v-if="!isEdit" style="margin-bottom:1rem;">
          <label><input type="checkbox" v-model="form.send_email"> Send email to client</label>
        </div>
        <button type="submit">{{ isEdit ? 'Update' : 'Create' }}</button>
        <router-link :to="`/companies/${companyId}`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useInvoiceStore } from '../../stores/invoice.js';

const route = useRoute();
const router = useRouter();
const companyId = route.params.companyId;
const clientId = route.params.clientId;
const invoiceId = route.params.invoiceId;

const invoiceStore = useInvoiceStore();

const isEdit = computed(() => !!invoiceId);
const client = ref(null);
const form = ref({ total_amount: '', status: 'draft', send_email: false, client_id: clientId });

onMounted(async () => {
  if (isEdit.value) {
    const res = await invoiceStore.fetchInvoice(companyId, invoiceId);
    form.value = { total_amount: res.total_amount, status: res.status };
    client.value = res.client;
  }
});

const submit = async () => {
  if (isEdit.value) {
    await invoiceStore.updateInvoice(companyId, invoiceId, form.value);
    router.push(`/companies/${companyId}/invoices/${invoiceId}`);
  } else {
    await invoiceStore.createInvoice(companyId, form.value);
    router.push(`/companies/${companyId}`);
  }
};
</script>
