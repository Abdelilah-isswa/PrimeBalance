<template>
  <nav style="background:#1a1a2e; color:white; padding:0.75rem 2rem; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
    <div style="display:flex; gap:1.5rem; align-items:center;">
      <router-link to="/" style="color:white; font-weight:700; font-size:1.1rem; letter-spacing:0.5px;">PrimeBalance</router-link>
      
      <!-- Company Selector with Dropdown -->
      <div v-if="companies.length" style="display:flex; gap:0.5rem; align-items:center;">
        <select @change="switchCompany" style="width:auto; padding:0.4rem 0.6rem; background:#2d2d44; color:white; border:1px solid #4f46e5; border-radius:6px; font-size:0.85rem;">
          <option v-for="c in companies" :key="c.id" :value="c.id" :selected="c.id == currentCompanyId">{{ c.name }}</option>
        </select>
        
        <!-- Dropdown Menu Button -->
        <div style="position:relative;">
          <button 
            @click="dropdownOpen = !dropdownOpen"
            style="padding:0.4rem 0.8rem; background:#4f46e5; color:white; border:none; border-radius:6px; cursor:pointer; font-size:0.85rem; font-weight:600; transition:background 0.2s;"
            @mouseenter="$event.target.style.background='#6366f1'"
            @mouseleave="$event.target.style.background='#4f46e5'">
            Menu ▼
          </button>
          
          <!-- Dropdown Content -->
          <div v-if="dropdownOpen" 
            style="position:absolute; top:100%; left:0; background:#2d2d44; border:1px solid #4f46e5; border-radius:6px; min-width:200px; margin-top:0.5rem; z-index:1000; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
            <router-link :to="`/companies/${currentCompanyId}`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; border-bottom:1px solid #4f46e5; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              📊 Dashboard
            </router-link>
            <router-link :to="`/companies/${currentCompanyId}/clients`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; border-bottom:1px solid #4f46e5; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              👥 Clients
            </router-link>
            <router-link :to="`/companies/${currentCompanyId}/suppliers`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; border-bottom:1px solid #4f46e5; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              🏢 Suppliers
            </router-link>
            <router-link :to="`/companies/${currentCompanyId}/accounts`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; border-bottom:1px solid #4f46e5; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              💰 Accounts
            </router-link>
            <router-link :to="`/companies/${currentCompanyId}/categories`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; border-bottom:1px solid #4f46e5; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              📂 Categories
            </router-link>
            <router-link :to="`/companies/${currentCompanyId}/documents`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; border-bottom:1px solid #4f46e5; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              📄 Documents
            </router-link>
            <router-link :to="`/companies/${currentCompanyId}/transactions`" @click="dropdownOpen=false" style="display:block; padding:0.75rem 1rem; color:#a5b4fc; text-decoration:none; transition:background 0.2s;" @mouseenter="$event.target.style.background='#3d3d54'" @mouseleave="$event.target.style.background='transparent'">
              💳 Transactions
            </router-link>
          </div>
        </div>
      </div>
    </div>
    <div style="display:flex; gap:1rem; align-items:center;">
      <span style="font-size:0.85rem; color:#a5b4fc;">{{ authStore.user?.name }}</span>
      <button @click="logout" class="btn-danger btn-sm">Logout</button>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const companies = ref([]);
const currentCompanyId = ref(route.params.companyId || null);
const dropdownOpen = ref(false);

onMounted(async () => {
  try {
    const res = await axios.get('/companies');
    companies.value = res.data.data;
  } catch {}
});

const switchCompany = (e) => {
  currentCompanyId.value = e.target.value;
  dropdownOpen.value = false;
  router.push(`/companies/${e.target.value}`);
};

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
