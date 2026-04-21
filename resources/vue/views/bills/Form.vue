<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>{{ isEdit ? 'Edit Bill' : 'Create Bill' }}</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;">
          <label>Supplier:</label><br>
          <select v-model="form.supplier_id" required style="width:100%; padding:0.5rem;" :disabled="readOnly">
            <option disabled value="">Select a supplier</option>
            <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>

        <div style="margin-bottom:1rem;"><label>Description:</label><br><textarea v-model="form.description" rows="3" style="width:100%; padding:0.5rem;" :disabled="readOnly"></textarea></div>

        <div style="margin-bottom:1rem;"><label>Due Date:</label><br><input v-model="form.due_date" type="date" style="width:100%; padding:0.5rem;" :disabled="readOnly"></div>

        <div style="margin-bottom:1rem;"><label>Total Amount:</label><br><input v-model="form.total_amount" type="number" step="0.01" required min="0" style="width:100%; padding:0.5rem;" :disabled="readOnly"></div>
        <div style="margin-bottom:1rem;">
          <label>Status:</label><br>
          <select v-model="form.status" style="width:100%; padding:0.5rem;" :disabled="readOnly">
            <option value="unpaid">Unpaid</option>
            <option value="partial">Partially Paid</option>
            <option value="paid">Fully Paid</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <button type="submit" :disabled="readOnly">{{ isEdit ? 'Update' : 'Create' }}</button>
        <router-link :to="`/companies/${companyId}`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
      <p v-if="readOnly" style="margin-top:1rem; color:#dc2626; font-weight:600;">This bill is fully paid and cannot be edited.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useBillStore } from '../../stores/bill.js';
import { useSupplierStore } from '../../stores/supplier.js';

const route = useRoute();
const router = useRouter();
const companyId = route.params.companyId;
const billId = route.params.billId;

const billStore = useBillStore();
const supplierStore = useSupplierStore();

const isEdit = computed(() => !!billId);
const suppliers = computed(() => supplierStore.suppliers);
const form = ref({ supplier_id: '', description: '', due_date: '', total_amount: '', status: 'unpaid' });
const readOnly = computed(() => form.value?.status === 'paid');

onMounted(async () => {
  await supplierStore.fetchSuppliers(companyId);
  if (isEdit.value) {
    const bill = await billStore.fetchBill(companyId, billId);
    if (bill) {
      form.value = {
        supplier_id: bill.supplier_id,
        description: bill.description || '',
        due_date: bill.due_date || '',
        total_amount: bill.total_amount,
        status: bill.status,
      };
    }
  }
});

const submit = async () => {
  if (isEdit.value) {
    await billStore.updateBill(companyId, billId, form.value);
    router.push(`/companies/${companyId}/bills/${billId}`);
  } else {
    await billStore.createBill(companyId, form.value);
    router.push(`/companies/${companyId}`);
  }
};
</script>
