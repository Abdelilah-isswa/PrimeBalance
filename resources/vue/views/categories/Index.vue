<template>
  <div class="pb-categories-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Categories</h1>
        <p class="pb-page-subtitle">Manage transaction categories for {{ company?.name }}</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'manage' }"
            @click="activeTab = 'manage'"
          >
            Manage Categories
            <span class="pb-tab-badge">{{ categories.length }}</span>
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'create' }"
            @click="activeTab = 'create'"
          >
            Add Category
            <span class="pb-tab-badge">+</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tab Content: Manage -->
    <div v-if="activeTab === 'manage'" class="pb-tab-content anim-fade-in">
      <div class="pb-card">
        <div class="pb-table-wrapper">
          <table class="pb-table">
            <thead>
              <tr>
                <th>Category Name</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cat in categories" :key="cat.id">
                <td>
                  <div class="pb-category-cell">
                    <div class="pb-category-avatar">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h16"/><path d="M4 15h16"/><path d="M10 3L8 21"/><path d="M16 3l-2 18"/></svg>
                    </div>
                    <div v-if="editId === cat.id" class="pb-edit-wrapper">
                      <input v-model="editName" class="pb-input pb-input-sm" @keyup.enter="update(cat.id)" autofocus>
                    </div>
                    <span v-else class="pb-category-name">{{ cat.name }}</span>
                  </div>
                </td>
                <td class="pb-text-center">
                  <div v-if="editId === cat.id" class="pb-action-group">
                    <button @click="update(cat.id)" class="pb-btn-icon pb-icon-success" title="Save">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                    <button @click="editId = null" class="pb-btn-icon pb-icon-neutral" title="Cancel">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                  </div>
                  <div v-else class="pb-action-group">
                    <button @click="startEdit(cat)" class="pb-btn-icon pb-icon-primary" title="Edit">
                      <span>Edit</span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button @click="destroy(cat.id)" class="pb-btn-icon pb-icon-danger" title="Delete">
                      <span>Delete</span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="categories.length === 0">
                <td colspan="2" class="pb-empty-row">No categories found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab Content: Create -->
    <div v-if="activeTab === 'create'" class="pb-tab-content anim-fade-in">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Add New Category</h2>
          <p class="pb-card-subtitle">Create a new category for classifying transactions.</p>
        </div>
        
        <form @submit.prevent="store" class="pb-form">
          <div class="pb-form-grid">
            <div class="pb-form-group">
              <label class="pb-label">Category Name</label>
              <input v-model="newName" type="text" class="pb-input" placeholder="e.g., Office Supplies, Rent" required autofocus>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="button" class="pb-btn pb-btn-secondary" @click="activeTab = 'manage'">Cancel</button>
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="submitting">
              {{ submitting ? 'Adding...' : 'Add Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const id = route.params.companyId;

const company = ref(null);
const categories = ref([]);
const newName = ref('');
const editId = ref(null);
const editName = ref('');
const activeTab = ref('manage');
const submitting = ref(false);

onMounted(async () => {
  try {
    const res = await axios.get(`companies/${id}/categories`);
    company.value = res.data.data.company;
    categories.value = res.data.data.categories;
  } catch (error) {
    console.error("Failed to load categories", error);
  }
});

const store = async () => {
  submitting.value = true;
  try {
    const res = await axios.post(`companies/${id}/categories`, { name: newName.value });
    categories.value.push(res.data.data);
    newName.value = '';
    activeTab.value = 'manage';
  } catch (error) {
    console.error("Failed to add category", error);
  } finally {
    submitting.value = false;
  }
};

const startEdit = (cat) => { 
  editId.value = cat.id; 
  editName.value = cat.name; 
};

const update = async (catId) => {
  if (!editName.value.trim()) return;
  try {
    await axios.put(`companies/${id}/categories/${catId}`, { name: editName.value });
    categories.value = categories.value.map(c => c.id === catId ? { ...c, name: editName.value } : c);
    editId.value = null;
  } catch (error) {
    console.error("Failed to update category", error);
  }
};

const destroy = async (catId) => {
  if (!confirm('Are you sure you want to delete this category?')) return;
  try {
    await axios.delete(`companies/${id}/categories/${catId}`);
    categories.value = categories.value.filter(c => c.id !== catId);
  } catch (error) {
    console.error("Failed to delete category", error);
    alert(error.response?.data?.message || 'Error deleting category');
  }
};
</script>

<style scoped>
.pb-categories-page {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.pb-page-header {
  margin-bottom: 2rem;
  padding-bottom: 0;
}

.pb-header-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.pb-page-title {
  font-size: 28px;
  font-weight: 800;
  color: #1a1a2e;
  letter-spacing: -0.5px;
  margin: 0;
}

.pb-page-subtitle {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 1.5rem;
}

/* Tabs */
.pb-tabs {
  display: flex;
  gap: 32px;
  border-bottom: 2px solid #f1f5f9;
  padding: 0 4px;
}

.pb-tab-btn {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 4px;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.pb-tab-btn:hover {
  color: #1e293b;
}

.pb-tab-btn--active {
  color: #4f46e5;
}

.pb-tab-btn--active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 2px;
  background: #4f46e5;
  border-radius: 2px;
}

.pb-tab-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 24px;
  height: 24px;
  padding: 0 6px;
  border: 1.5px solid #cbd5e1;
  background: transparent;
  color: #64748b;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  transition: all 0.2s;
}

.pb-tab-btn--active .pb-tab-badge {
  border-color: #4f46e5;
  color: #4f46e5;
}

/* Table Card */
.pb-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow: hidden;
}

.pb-table-wrapper {
  overflow-x: auto;
}

.pb-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
}

.pb-table th {
  text-align: left;
  padding: 1.25rem 1.5rem;
  background: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 12px;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e2e8f0;
}

.pb-table td {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  color: #1a1a2e;
}

.pb-table tr:hover {
  background: #f8fafc;
}

.pb-category-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pb-category-avatar {
  width: 32px;
  height: 32px;
  background: #f1f5f9;
  color: #64748b;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pb-category-name {
  font-weight: 600;
  color: #1e293b;
}

.pb-edit-wrapper {
  flex: 1;
  max-width: 300px;
}

.pb-input-sm {
  padding: 8px 12px !important;
  font-size: 14px !important;
  width: 100%;
}

.pb-action-group {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.pb-btn-icon {
  width: auto;
  min-width: 32px;
  height: 32px;
  padding: 0 8px;
  border-radius: 8px;
  border: none;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  color: #64748b;
  font-size: 13px;
  font-weight: 500;
}

.pb-btn-icon:hover {
  background: #f1f5f9;
}

.pb-icon-primary:hover { color: #4f46e5; background: #e0e7ff; }
.pb-icon-danger:hover { color: #e11d48; background: #ffe4e6; }
.pb-icon-success:hover { color: #059669; background: #d1fae5; }
.pb-icon-neutral:hover { color: #475569; background: #e2e8f0; }

.pb-empty-row {
  text-align: center;
  padding: 4rem !important;
  color: #94a3b8;
}

/* Form Styles */
.pb-form-card {
  width: 100%;
}

.pb-card-header {
  padding: 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.pb-card-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 8px 0;
  color: #1a1a2e;
}

.pb-form {
  padding: 2rem;
}

.pb-form-grid {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.pb-form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pb-label {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.pb-input {
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 14px;
  transition: all 0.2s;
  background: #f8fafc;
}

.pb-input:focus {
  outline: none;
  border-color: #4f46e5;
  background: white;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.pb-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 2rem;
  border-top: 1px solid #f1f5f9;
}

.pb-btn {
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.pb-btn-primary {
  background: #4f46e5;
  color: white;
}

.pb-btn-primary:hover:not(:disabled) {
  background: #4338ca;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.pb-btn-secondary {
  background: transparent;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.pb-btn-secondary:hover {
  background: #f8fafc;
  color: #1e293b;
}

/* Utilities */
.pb-text-center { text-align: center; }

.anim-fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
