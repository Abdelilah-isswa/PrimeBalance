<template>
  <div>
    <!-- Navbar -->
    <nav style="background:#1a1a2e; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
      <div style="display:flex; gap:1.5rem; align-items:center;">
        <router-link to="/" style="color:white; font-weight:700; font-size:1.2rem; letter-spacing:0.5px;">PrimeBalance</router-link>
        
        <!-- Company Selector with Dropdown (when logged in) -->
        <div v-if="authStore.token && companies.length" style="display:flex; gap:0.5rem; align-items:center;">
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
      
      <div style="display:flex; gap:1rem;">
        <template v-if="authStore.token">
          <router-link to="/dashboard"><button class="btn-sm">Go to Dashboard</button></router-link>
        </template>
        <template v-else>
          <router-link to="/login"><button class="btn-secondary btn-sm">Login</button></router-link>
          <router-link to="/register"><button class="btn-sm">Get Started</button></router-link>
        </template>
      </div>
    </nav>

    <!-- Hero -->
    <section style="background:linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%); color:white; padding:6rem 2rem; text-align:center;">
      <h1 style="font-size:3rem; font-weight:800; margin-bottom:1rem; color:white;">Manage Your Business Finances</h1>
      <p style="font-size:1.2rem; color:#a5b4fc; max-width:600px; margin:0 auto 2.5rem;">
        PrimeBalance helps you track invoices, bills, transactions, and clients — all in one place, for multiple companies.
      </p>
      <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <template v-if="authStore.token">
          <router-link to="/dashboard"><button style="padding:0.85rem 2rem; font-size:1rem;">Go to Dashboard</button></router-link>
        </template>
        <template v-else>
          <router-link to="/register"><button style="padding:0.85rem 2rem; font-size:1rem;">Start for Free</button></router-link>
          <router-link to="/login"><button class="btn-secondary" style="padding:0.85rem 2rem; font-size:1rem;">Login</button></router-link>
        </template>
      </div>
    </section>

    <!-- Features -->
    <section style="padding:5rem 2rem; background:#f0f2f5;">
      <h2 style="text-align:center; font-size:2rem; font-weight:700; color:#1a1a2e; margin-bottom:3rem;">Everything You Need</h2>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:1.5rem; max-width:1100px; margin:0 auto;">
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">🏢</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Multi-Company</h3>
          <p style="color:#64748b; font-size:0.9rem;">Manage multiple companies from a single account. Switch between them instantly.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">📄</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Invoices & Bills</h3>
          <p style="color:#64748b; font-size:0.9rem;">Create and send invoices to clients. Track bills from suppliers. Download PDFs instantly.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">💰</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Transactions</h3>
          <p style="color:#64748b; font-size:0.9rem;">Track income and expenses. Account balances update automatically with every payment.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">📊</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Dashboard</h3>
          <p style="color:#64748b; font-size:0.9rem;">Get a clear overview of your finances — income, expenses, net profit, and unpaid invoices.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">👥</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Team Management</h3>
          <p style="color:#64748b; font-size:0.9rem;">Invite team members with different roles — owner, accountant, viewer, and more.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">📧</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Email Notifications</h3>
          <p style="color:#64748b; font-size:0.9rem;">Send invoices and team invitations directly by email with one click.</p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section style="background:#1a1a2e; color:white; padding:5rem 2rem; text-align:center;">
      <h2 style="font-size:2rem; font-weight:700; color:white; margin-bottom:1rem;">Ready to get started?</h2>
      <p style="color:#a5b4fc; margin-bottom:2rem; font-size:1rem;">Create your free account and start managing your finances today.</p>
      <router-link v-if="authStore.token" to="/dashboard"><button style="padding:0.85rem 2.5rem; font-size:1rem;">Go to Dashboard</button></router-link>
      <router-link v-else to="/register"><button style="padding:0.85rem 2.5rem; font-size:1rem;">Create Free Account</button></router-link>
    </section>

    <!-- Footer -->
    <footer style="background:#0f0f1a; color:#64748b; text-align:center; padding:1.5rem; font-size:0.85rem;">
      © {{ new Date().getFullYear() }} PrimeBalance. All rights reserved.
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();
const companies = ref([]);
const currentCompanyId = ref(null);
const dropdownOpen = ref(false);

onMounted(async () => {
  if (authStore.token) {
    try {
      const res = await axios.get('/companies');
      companies.value = res.data.data;
      if (companies.value.length > 0) {
        currentCompanyId.value = companies.value[0].id;
      }
    } catch {}
  }
});

const switchCompany = (e) => {
  currentCompanyId.value = e.target.value;
  dropdownOpen.value = false;
  router.push(`/companies/${e.target.value}`);
};
</script>
