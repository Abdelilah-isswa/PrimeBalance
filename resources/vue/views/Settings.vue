<template>
  <div class="pb-settings-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Settings</h1>
        <p class="pb-page-subtitle">Manage your personal information and current workspace settings.</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'profile' }"
            @click="activeTab = 'profile'"
          >
            Profile Info
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'company' }"
            @click="activeTab = 'company'"
            v-if="currentCompany"
          >
            Company Info
          </button>
        </div>
      </div>
    </div>

    <!-- Tab Content: Profile Info -->
    <div v-if="activeTab === 'profile'" class="pb-tab-content anim-slide-up">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Personal Profile</h2>
          <p class="pb-card-subtitle">Update your account's profile information and email address.</p>
        </div>
        
        <form @submit.prevent="updateProfile" class="pb-form">
          <div v-if="successMessage" class="pb-alert pb-alert-success">{{ successMessage }}</div>
          <div v-if="errorMessage" class="pb-alert pb-alert-error">{{ errorMessage }}</div>

          <div class="pb-form-grid">
            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Full Name</label>
              <input v-model="profileForm.name" type="text" class="pb-input" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Email Address</label>
              <input v-model="profileForm.email" type="email" class="pb-input" required>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Saving...' : 'Save Profile' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tab Content: Company Info -->
    <div v-if="activeTab === 'company' && currentCompany" class="pb-tab-content anim-slide-up">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Workspace Settings</h2>
          <p class="pb-card-subtitle">Manage details for {{ currentCompany.name }}.</p>
        </div>
        
        <form @submit.prevent="updateCompany" class="pb-form">
          <div v-if="companySuccess" class="pb-alert pb-alert-success">{{ companySuccess }}</div>
          <div v-if="companyError" class="pb-alert pb-alert-error">{{ companyError }}</div>

          <div class="pb-form-grid">
            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Company Name</label>
              <input v-model="companyForm.name" type="text" class="pb-input" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Address</label>
              <input v-model="companyForm.address" type="text" class="pb-input" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Currency</label>
              <select v-model="companyForm.currency" class="pb-input pb-select" required>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
                <option value="MAD">MAD</option>
                <option value="JPY">JPY</option>
                <option value="CAD">CAD</option>
              </select>
            </div>
          </div>

          <div class="pb-form-actions" style="justify-content: space-between;">
            <button type="button" class="pb-btn pb-btn-danger" @click="deactivateCompany" :disabled="submitting">
              Deactivate Company
            </button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Saving...' : 'Save Workspace' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import { useCompanyStore } from '../stores/company.js';
import axios from 'axios';

const router = useRouter();

const authStore = useAuthStore();
const companyStore = useCompanyStore();

const activeTab = ref('profile');

const profileForm = ref({ name: '', email: '' });
const companyForm = ref({ name: '', address: '', currency: '' });

const submitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const companySuccess = ref('');
const companyError = ref('');

const currentCompany = computed(() => {
  // Try to find the company ID from the route path to get context.
  const routeParts = window.location.pathname.split('/');
  const maybeId = routeParts[routeParts.indexOf('companies') + 1];
  
  if (maybeId && !isNaN(maybeId)) {
    return companyStore.companies.find(c => c.id == maybeId) || companyStore.companies[0];
  }
  return companyStore.companies[0] || null;
});

onMounted(async () => {
  if (authStore.user) {
    profileForm.value.name = authStore.user.name;
    profileForm.value.email = authStore.user.email;
  }
  if (companyStore.companies.length === 0) {
    await companyStore.fetchCompanies();
  }
  populateCompanyForm();
});

watch(currentCompany, () => {
  populateCompanyForm();
});

const populateCompanyForm = () => {
  if (currentCompany.value) {
    companyForm.value.name = currentCompany.value.name;
    companyForm.value.address = currentCompany.value.address;
    companyForm.value.currency = currentCompany.value.currency;
  }
};

const updateProfile = async () => {
  submitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  try {
    const res = await axios.put('/user', profileForm.value);
    authStore.user = res.data.data;
    successMessage.value = 'Profile updated successfully!';
    setTimeout(() => { successMessage.value = ''; }, 3000);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to update profile.';
  } finally {
    submitting.value = false;
  }
};

const updateCompany = async () => {
  if (!currentCompany.value) return;
  submitting.value = true;
  companySuccess.value = '';
  companyError.value = '';
  try {
    await axios.put(`/companies/${currentCompany.value.id}`, companyForm.value);
    await companyStore.fetchCompanies();
    companySuccess.value = 'Company details updated!';
    setTimeout(() => { companySuccess.value = ''; }, 3000);
  } catch (error) {
    companyError.value = error.response?.data?.message || 'Failed to update company.';
  } finally {
    submitting.value = false;
  }
};

const deactivateCompany = async () => {
  if (!currentCompany.value) return;
  if (!confirm("Are you sure you want to deactivate this company? This action cannot be undone.")) return;
  
  submitting.value = true;
  try {
    await axios.post(`/companies/${currentCompany.value.id}/deactivate`);
    await companyStore.fetchCompanies();
    router.push('/dashboard');
  } catch (err) {
    companyError.value = err.response?.data?.message || 'Failed to deactivate company.';
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
.pb-settings-page {
  animation: fadeIn 0.4s ease-out;
  max-width: 800px;
  margin: 0 auto;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.pb-page-header { margin-bottom: 2rem; padding-bottom: 0; }
.pb-header-content { display: flex; flex-direction: column; gap: 0.5rem; }
.pb-page-title { font-size: 28px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; margin: 0; }
.pb-page-subtitle { color: #64748b; font-size: 14px; margin-bottom: 1.5rem; }

.pb-tabs { display: flex; gap: 32px; border-bottom: 2px solid #f1f5f9; padding: 0 4px; }
.pb-tab-btn { position: relative; display: flex; align-items: center; padding: 12px 4px; border: none; background: transparent; color: #64748b; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.pb-tab-btn:hover { color: #1e293b; }
.pb-tab-btn--active { color: #4f46e5; }
.pb-tab-btn--active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: #4f46e5; border-radius: 2px; }

.pb-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.pb-card-header { padding: 2rem; border-bottom: 1px solid #f1f5f9; }
.pb-card-title { font-size: 18px; font-weight: 700; margin: 0 0 8px 0; color: #1a1a2e; }
.pb-card-subtitle { font-size: 14px; color: #64748b; margin: 0; }
.pb-form { padding: 2rem; }
.pb-form-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem; }
.pb-form-group { display: flex; flex-direction: column; gap: 8px; }
.pb-label { font-size: 13px; font-weight: 600; color: #1e293b; }
.pb-input { padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: all 0.2s; background: #f8fafc; }
.pb-input:focus { outline: none; border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
.pb-select { cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto; }
.pb-form-actions { display: flex; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; }
.pb-btn { padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:hover:not(:disabled) { background: #4338ca; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
.pb-btn-danger { background: transparent; color: #e11d48; border: 1px solid #fecaca; }
.pb-btn-danger:hover { background: #fee2e2; }

.pb-alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 14px; font-weight: 500; }
.pb-alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.pb-alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.anim-slide-up { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
