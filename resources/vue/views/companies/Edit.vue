<template>
  <div>
    <div style="padding:2rem;">
      <h1>Manage Company</h1>

      <!-- View Mode -->
      <div v-if="!editMode" style="margin:2rem 0;">
        <p><strong>Name:</strong> {{ company?.name }}</p>
        <p><strong>Address:</strong> {{ company?.address }}</p>
        <p><strong>Currency:</strong> {{ company?.currency }}</p>
        <p><strong>Start Date:</strong> {{ company?.start_date }}</p>
        <p v-if="company?.end_date"><strong>End Date:</strong> {{ company?.end_date }} <span style="color:red;">(Deactivated)</span></p>
        <div style="display:flex; gap:0.5rem; margin-top:1rem;">
          <button @click="editMode = true">Update</button>
          <button v-if="!company?.end_date" @click="deactivate" style="background:#c62828;">Deactivate Company</button>
        </div>
      </div>

      <!-- Edit Mode -->
      <form v-else @submit.prevent="update" style="margin:2rem 0;">
        <div><label>Name:</label><br><input v-model="form.name" required style="width:100%; padding:0.5rem; margin-bottom:1rem;"></div>
        <div><label>Address:</label><br><input v-model="form.address" required style="width:100%; padding:0.5rem; margin-bottom:1rem;"></div>
        <div>
          <label>Currency:</label><br>
          <select v-model="form.currency" style="padding:0.5rem; margin-bottom:1rem;">
            <option value="USD">USD</option><option value="EUR">EUR</option>
            <option value="GBP">GBP</option><option value="MAD">MAD</option>
          </select>
        </div>
        <button type="submit">Save</button>
        <button type="button" @click="editMode = false" style="margin-left:0.5rem;">Cancel</button>
      </form>

      <!-- Invite User -->
      <h2>Invite User</h2>
      <form @submit.prevent="invite" style="margin:1rem 0;">
        <div><label>Email:</label><br><input v-model="inviteForm.email" type="email" required style="padding:0.5rem; margin-bottom:0.5rem;"></div>
        <div>
          <label>Role:</label><br>
          <select v-model="inviteForm.role" style="padding:0.5rem; margin-bottom:0.5rem;">
            <option value="owner">Owner</option>
            <option value="accountant">Accountant</option>
            <option value="standard_user">Standard User</option>
            <option value="viewer">Viewer</option>
          </select>
        </div>
        <button type="submit">Send Invitation</button>
      </form>
      <p v-if="inviteSuccess" style="color:green;">{{ inviteSuccess }}</p>
      <p v-if="inviteError" style="color:red;">{{ inviteError }}</p>

      <!-- Users List -->
      <h2>Company Users</h2>
      <table style="width:100%; border-collapse:collapse; margin:1rem 0;">
        <thead>
          <tr style="background:#f5f5f5;">
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Name</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Email</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Role</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Joined</th>
            <th style="padding:0.5rem; text-align:left; border:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ user.name }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ user.email }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <select v-if="user.id !== authStore.user?.id" :value="user.pivot.role" @change="updateRole(user.id, $event.target.value)" style="padding:0.25rem;">
                <option value="owner">Owner</option>
                <option value="accountant">Accountant</option>
                <option value="standard_user">Standard User</option>
                <option value="viewer">Viewer</option>
              </select>
              <span v-else>{{ user.pivot.role }}</span>
            </td>
            <td style="padding:0.5rem; border:1px solid #ddd;">{{ user.pivot.created_at?.substring(0,10) }}</td>
            <td style="padding:0.5rem; border:1px solid #ddd;">
              <button v-if="user.id !== authStore.user?.id" @click="removeUser(user.id)" style="background:#c62828; padding:0.25rem 0.5rem;">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>

      <router-link :to="`/companies/${id}`"><button style="margin-top:1rem;">Back to Company</button></router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth.js';

const route = useRoute();
const router = useRouter();
const id = route.params.companyId;
const authStore = useAuthStore();
const company = ref(null);
const users = ref([]);
const editMode = ref(false);
const form = ref({ name: '', address: '', currency: '' });
const inviteForm = ref({ email: '', role: 'viewer' });
const inviteSuccess = ref('');
const inviteError = ref('');

onMounted(async () => {
  try {
    const [showRes, usersRes] = await Promise.all([
      axios.get(`/companies/${id}`),
      axios.get(`/companies/${id}/users`),
    ]);
    company.value = showRes.data.data.company;
    form.value = { name: company.value.name, address: company.value.address, currency: company.value.currency };
    users.value = usersRes.data.data.users || [];
  } catch (err) {
    console.error('Error loading company:', err.response?.data);
  }
});

const update = async () => {
  await axios.put(`/companies/${id}`, form.value);
  company.value = { ...company.value, ...form.value };
  editMode.value = false;
};

const deactivate = async () => {
  if (!confirm('Deactivate this company?')) return;
  await axios.post(`/companies/${id}/deactivate`);
  router.push('/companies');
};

const invite = async () => {
  inviteSuccess.value = ''; inviteError.value = '';
  try {
    await axios.post(`/companies/${id}/invite`, inviteForm.value);
    inviteSuccess.value = 'Invitation sent to ' + inviteForm.value.email;
    inviteForm.value.email = '';
  } catch (err) {
    inviteError.value = err.response?.data?.message || 'Error sending invitation';
  }
};

const updateRole = async (userId, role) => {
  await axios.put(`/companies/${id}/users/${userId}/role`, { role });
  const usersRes = await axios.get(`/companies/${id}/users`);
  users.value = usersRes.data.data;
};

const removeUser = async (userId) => {
  if (!confirm('Remove this user?')) return;
  await axios.delete(`/companies/${id}/users/${userId}`);
  users.value = users.value.filter(u => u.id !== userId);
};
</script>
