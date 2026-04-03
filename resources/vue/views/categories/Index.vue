<template>
  <div>
    <NavBar />
    <div style="padding:2rem;">
      <h1>Categories - {{ company?.name }}</h1>
      <form @submit.prevent="store" style="margin:1rem 0; display:flex; gap:0.5rem;">
        <input v-model="newName" placeholder="Category name" required style="padding:0.5rem; flex:1;">
        <button type="submit">Add</button>
      </form>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Name</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cat in categories" :key="cat.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <input v-if="editId === cat.id" v-model="editName" style="padding:0.25rem;">
              <span v-else>{{ cat.name }}</span>
            </td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <template v-if="editId === cat.id">
                <button @click="update(cat.id)" style="padding:0.25rem 0.5rem;">Save</button>
                <button @click="editId = null" style="padding:0.25rem 0.5rem; margin-left:0.5rem;">Cancel</button>
              </template>
              <template v-else>
                <button @click="startEdit(cat)" style="padding:0.25rem 0.5rem;">Edit</button>
                <button @click="destroy(cat.id)" style="background:#c62828; padding:0.25rem 0.5rem; margin-left:0.5rem;">Delete</button>
              </template>
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
const categories = ref([]);
const newName = ref('');
const editId = ref(null);
const editName = ref('');

onMounted(async () => {
  const res = await axios.get(`/companies/${id}/categories`);
  company.value = res.data.data.company;
  categories.value = res.data.data.categories;
});

const store = async () => {
  const res = await axios.post(`/companies/${id}/categories`, { name: newName.value });
  categories.value.push(res.data.data);
  newName.value = '';
};

const startEdit = (cat) => { editId.value = cat.id; editName.value = cat.name; };

const update = async (catId) => {
  await axios.put(`/companies/${id}/categories/${catId}`, { name: editName.value });
  categories.value = categories.value.map(c => c.id === catId ? { ...c, name: editName.value } : c);
  editId.value = null;
};

const destroy = async (catId) => {
  if (!confirm('Delete this category?')) return;
  await axios.delete(`/companies/${id}/categories/${catId}`);
  categories.value = categories.value.filter(c => c.id !== catId);
};
</script>
