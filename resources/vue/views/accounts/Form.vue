<template>
  <div>
    <div style="padding:2rem; max-width:600px;">
      <h1>{{ isEdit ? 'Edit Account' : 'Create Account' }}</h1>
      <form @submit.prevent="submit">
        <div style="margin-bottom:1rem;"><label>Name:</label><br><input v-model="form.name" required style="width:100%; padding:0.5rem;"></div>
        <div v-if="!isEdit" style="margin-bottom:1rem;"><label>Initial Balance:</label><br><input v-model="form.balance" type="number" step="0.01" required style="width:100%; padding:0.5rem;"></div>
        <div v-if="isEdit" style="margin-bottom:1rem;"><label>Balance:</label><br><input :value="account?.balance" disabled style="width:100%; padding:0.5rem; background:#f5f5f5;"><small>Balance cannot be edited directly</small></div>
        <div style="margin-bottom:1rem;">
          <label><input type="checkbox" v-model="form.is_active"> Active</label>
        </div>
        <button type="submit">{{ isEdit ? 'Update' : 'Create' }}</button>
        <router-link :to="`/companies/${companyId}/accounts`"><button type="button" style="margin-left:0.5rem;">Cancel</button></router-link>
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
const accountId = route.params.accountId;
const isEdit = computed(() => !!accountId);
const account = ref(null);
const form = ref({ name: '', balance: 0, is_active: true });

onMounted(async () => {
  if (isEdit.value) {
    const res = await axios.get(`/companies/${companyId}/accounts/${accountId}/edit`);
    account.value = res.data.data.account;
    form.value = { name: account.value.name, is_active: account.value.is_active };
  }
});

const submit = async () => {
  if (isEdit.value) {
    await axios.put(`/companies/${companyId}/accounts/${accountId}`, form.value);
  } else {
    await axios.post(`/companies/${companyId}/accounts`, form.value);
  }
  router.push(`/companies/${companyId}/accounts`);
};
</script>
