<template>
  <div>
    <NavBar />
    <div class="page" style="max-width:700px;">
      <h1>My Companies</h1>
      <router-link to="/companies/create"><button style="margin-bottom:1.5rem;">+ New Company</button></router-link>
      <div v-if="companies.length" style="display:flex; flex-direction:column; gap:1rem;">
        <router-link
          v-for="company in companies"
          :key="company.id"
          :to="`/companies/${company.id}`"
          style="text-decoration:none;"
        >
          <div class="card" style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.5rem; cursor:pointer; transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow=''">
            <div>
              <h3 style="color:#1a1a2e; margin-bottom:0.25rem;">{{ company.name }}</h3>
              <p style="color:#64748b; font-size:0.85rem;">{{ company.address }} · {{ company.currency }}</p>
            </div>
            <span v-if="company.end_date" style="color:#dc2626; font-size:0.8rem; font-weight:600;">Deactivated</span>
            <span v-else style="color:#16a34a; font-size:0.8rem; font-weight:600;">Active</span>
          </div>
        </router-link>
      </div>
      <div v-else class="card" style="text-align:center; padding:3rem; color:#64748b;">
        <p style="margin-bottom:1rem;">No companies yet.</p>
        <router-link to="/companies/create"><button>Create your first company</button></router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import NavBar from '../components/NavBar.vue';

const companies = ref([]);
const router = useRouter();

onMounted(async () => {
  try {
    const res = await axios.get('/companies');
    companies.value = res.data.data;
    if (companies.value.length === 1) {
      router.push(`/companies/${companies.value[0].id}`);
    }
  } catch (err) {
    if (err.response?.status === 401) router.push('/login');
  }
});
</script>
