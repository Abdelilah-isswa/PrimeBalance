<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>{{ isEdit ? 'Edit Bill' : 'Create Bill for ' + supplier?.name }}</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;"><label>Total Amount:</label><br><input v-model="form.total_amount" type="number" step="0.01" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;">
          <label>Status:</label><br>
          <select v-model="form.status" style="width:100%; padding:0.5rem;">
            <option value="draft">Draft</option>
            <option value="unpaid">Unpaid</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
            <option value="cancelled">Cancelled</option>
          </select>
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
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const companyId = route.params.companyId;
const supplierId = route.params.supplierId;
const billId = route.params.billId;
const isEdit = computed(() => !!billId);
const supplier = ref(null);
const form = ref({ total_amount: '', status: 'draft' });

onMounted(async () => {
  if (isEdit.value) {
    const res = await axios.get(`/companies/${companyId}/bills/${billId}/edit`);
    const bill = res.data.data.bill;
    form.value = { total_amount: bill.total_amount, status: bill.status };
    supplier.value = bill.supplier;
  }
});

const submit = async () => {
  if (isEdit.value) {
    await axios.put(`/companies/${companyId}/bills/${billId}`, form.value);
    router.push(`/companies/${companyId}/bills/${billId}`);
  } else {
    await axios.post(`/companies/${companyId}/suppliers/${supplierId}/bills`, form.value);
    router.push(`/companies/${companyId}`);
  }
};
</script>
