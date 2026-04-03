<template>
  <div>
    <NavBar />
    <div style="padding:2rem; max-width:600px;">
      <h1>{{ isEdit ? 'Edit Supplier' : 'Add Supplier' }}</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;"><label>Name:</label><br><input v-model="form.name" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;"><label>Email:</label><br><input v-model="form.email" type="email" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;"><label>Address:</label><br><input v-model="form.address" required style="width:100%; padding:0.5rem;"></div>
        <div style="margin-bottom:1rem;"><label>Phone:</label><br><input v-model="form.phone" required style="width:100%; padding:0.5rem;"></div>
        <button type="submit">{{ isEdit ? 'Update' : 'Create' }}</button>
        <router-link :to="`/companies/${companyId}`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
      </form>
      <form v-if="isEdit && !hasBills" @submit.prevent="destroy" style="margin-top:2rem;">
        <button type="submit" style="background:#c62828;" onclick="return confirm('Delete this supplier?')">Delete Supplier</button>
      </form>
      <p v-if="error" style="color:red;">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import NavBar from '../../components/NavBar.vue';

const route = useRoute();
const router = useRouter();
const companyId = route.params.companyId;
const supplierId = route.params.supplierId;
const isEdit = computed(() => !!supplierId);
const hasBills = ref(true);
const form = ref({ name: '', email: '', address: '', phone: '' });
const error = ref('');

onMounted(async () => {
  if (isEdit.value) {
    const res = await axios.get(`/companies/${companyId}/suppliers/${supplierId}/edit`);
    const supplier = res.data.data.supplier;
    form.value = { name: supplier.name, email: supplier.email, address: supplier.address, phone: supplier.phone };
    hasBills.value = supplier.bills_count > 0;
  }
});

const submit = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`/companies/${companyId}/suppliers/${supplierId}`, form.value);
    } else {
      await axios.post(`/companies/${companyId}/suppliers`, form.value);
    }
    router.push(`/companies/${companyId}`);
  } catch (err) {
    error.value = err.response?.data?.message || 'Error';
  }
};

const destroy = async () => {
  await axios.delete(`/companies/${companyId}/suppliers/${supplierId}`);
  router.push(`/companies/${companyId}`);
};
</script>
