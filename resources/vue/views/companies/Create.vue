<template>
  <div class="pb-company-create-layout">
    <div class="pb-onboarding-container anim-fade-in">
      <div class="pb-onboarding-header">
        <div class="pb-logo-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <h1 class="pb-title">Create a Company</h1>
        <p class="pb-subtitle">Set up a new workspace for your business to track finances naturally.</p>
      </div>

      <div class="pb-card pb-form-card">
        <form @submit.prevent="createCompany" class="pb-form">
          <div v-if="error" class="pb-alert pb-alert-error">
            {{ error }}
          </div>

          <div class="pb-form-grid">
            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Company Name</label>
              <input v-model="form.name" type="text" class="pb-input" placeholder="e.g., Acme Corp" required autofocus>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Address</label>
              <input v-model="form.address" type="text" class="pb-input" placeholder="123 Business Rd, City" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Base Currency</label>
              <select v-model="form.currency" class="pb-input pb-select" required>
                <option value="USD">USD - US Dollar ($)</option>
                <option value="EUR">EUR - Euro (€)</option>
                <option value="GBP">GBP - British Pound (£)</option>
                <option value="MAD">MAD - Moroccan Dirham</option>
                <option value="JPY">JPY - Japanese Yen (¥)</option>
                <option value="CAD">CAD - Canadian Dollar ($)</option>
              </select>
            </div>
          </div>

          <div class="pb-form-actions">
            <router-link to="/dashboard" class="pb-btn pb-btn-secondary" v-if="hasCompanies">Cancel</router-link>
            <button type="button" v-else class="pb-btn pb-btn-secondary pb-invisible">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="loading">
              {{ loading ? 'Creating Workspace...' : 'Create Company Workspace' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCompanyStore } from '../../stores/company.js';

const router = useRouter();
const companyStore = useCompanyStore();

const form = ref({ name: '', address: '', currency: 'USD' });
const loading = ref(false);
const error = ref('');

const hasCompanies = computed(() => companyStore.companies.length > 0);

onMounted(async () => {
    // Attempt to load companies first to see if they can 'cancel' back to dashboard
    if (companyStore.companies.length === 0) {
        await companyStore.fetchCompanies();
    }
});

const createCompany = async () => {
  loading.value = true;
  error.value = '';
  try {
    const newCompany = await companyStore.createCompany(form.value);
    router.push(`/companies/${newCompany.id}`);
  } catch (err) {
    error.value = err.response?.data?.message || 'Error creating company. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.pb-company-create-layout {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  padding: 2rem;
}

.pb-onboarding-container {
  width: 100%;
  max-width: 500px;
}

.pb-onboarding-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.pb-logo-badge {
  width: 56px;
  height: 56px;
  background: #4f46e5;
  color: white;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem auto;
  box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
}

.pb-title {
  font-size: 32px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.5px;
}

.pb-subtitle {
  color: #64748b;
  font-size: 15px;
  margin: 0;
}

/* Card */
.pb-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.025);
  overflow: hidden;
}

.pb-form {
  padding: 2.5rem;
}

/* Forms */
.pb-form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.pb-form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pb-col-full {
  grid-column: 1 / -1;
}

.pb-label {
  font-size: 13px;
  font-weight: 600;
  color: #475569;
}

.pb-input {
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  transition: all 0.2s;
  background: #f8fafc;
  color: #1e293b;
}

.pb-input:focus {
  outline: none;
  border-color: #4f46e5;
  background: white;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.pb-select {
  cursor: pointer;
  appearance: none;
  background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
  background-repeat: no-repeat;
  background-position: right 1rem top 50%;
  background-size: 0.65rem auto;
}

/* Actions */
.pb-form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pb-invisible {
  visibility: hidden;
}

.pb-btn {
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.pb-btn-primary {
  background: #4f46e5;
  color: white;
  flex: 1;
  margin-left: 12px;
}

.pb-btn-primary:hover:not(:disabled) {
  background: #4338ca;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.pb-btn-primary:disabled {
  background: #818cf8;
  cursor: not-allowed;
}

.pb-btn-secondary {
  background: transparent;
  color: #64748b;
}

.pb-btn-secondary:hover {
  background: #f1f5f9;
  color: #1e293b;
}

/* Alerts */
.pb-alert-error {
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  font-size: 14px;
  font-weight: 500;
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
  text-align: center;
}

/* Animations */
.anim-fade-in {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
