<template>
  <div class="pb-dashboard-layout">
    <!-- Sidebar -->
    <aside class="pb-sidebar">
      <div class="pb-sidebar-header">
        <router-link :to="homeLink" class="pb-logo">
          <div class="pb-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <span>PrimeBalance</span>
        </router-link>
        
        <div class="pb-company-selector">
          <div class="pb-company-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
            <span>Company</span>
          </div>
          <select v-model="currentCompanyId" class="pb-select">
            <option disabled value="">Select company</option>
            <option v-for="company in companies" :key="company.id" :value="String(company.id)">
              {{ company.name }}
            </option>
          </select>
        </div>
      </div>

      <nav class="pb-nav">
        <router-link
          v-for="link in navLinks"
          :key="link.path"
          :to="link.path"
          class="pb-nav-link"
          :class="{ 'pb-nav-link--active': isActiveLink(link.path) }"
        >
          <span class="pb-nav-icon" v-html="link.iconSvg"></span>
          <span class="pb-nav-label">{{ link.label }}</span>
        </router-link>

        <div v-if="navLinks.length === 0" class="pb-nav-empty">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <p>Select a company to see navigation</p>
        </div>
      </nav>

      <div class="pb-sidebar-footer">
        <button class="pb-logout-btn" @click="logout">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="pb-main-content">
      <div class="pb-content-wrapper">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, watch, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import { useCompanyStore } from '../stores/company.js'

const props = defineProps({
  companyId: {
    type: [String, Number],
    default: null
  }
})

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const companyStore = useCompanyStore()

const currentCompanyId = ref(String(props.companyId || route.params.companyId || ''))

onMounted(async () => {
  if (companyStore.companies.length === 0) {
    await companyStore.fetchCompanies()
  }

  const companyId = route.params.companyId || props.companyId
  if (companyId && !currentCompanyId.value) {
    currentCompanyId.value = String(companyId)
  }
})

watch(() => props.companyId, (newVal) => {
  if (newVal) {
    currentCompanyId.value = newVal
  }
})

watch(
  () => route.params.companyId,
  (newVal) => {
    if (newVal) {
      currentCompanyId.value = newVal
    }
  }
)

watch(currentCompanyId, (newVal, oldVal) => {
  if (newVal && newVal !== oldVal) {
    router.push(`/companies/${newVal}`)
  } else if (!newVal && oldVal) {
    router.push('/dashboard')
  }
})

const isActiveLink = (path) => {
  if (path === '/dashboard') {
    return route.path === '/dashboard'
  }
  return route.path.startsWith(path)
}

const companies = computed(() => companyStore.companies)

const selectedCompany = computed(() => {
  return companies.value.find(company => String(company.id) === currentCompanyId.value)
})

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

const homeLink = computed(() => {
  return '/'
})

const navLinks = computed(() => {
  if (!currentCompanyId.value) return []
  const id = currentCompanyId.value
  return [
    { 
      path: `/dashboard`, 
      label: 'Dashboard', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>`
    },
    { 
      path: `/companies/${id}/invoices`, 
      label: 'Invoices', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>`
    },
    { 
      path: `/companies/${id}/bills`, 
      label: 'Bills', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>`
    },
    { 
      path: `/companies/${id}/clients`, 
      label: 'Clients', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`
    },
    { 
      path: `/companies/${id}/suppliers`, 
      label: 'Suppliers', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`
    },
    { 
      path: `/companies/${id}/accounts`, 
      label: 'Accounts', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>`
    },
    { 
      path: `/companies/${id}/categories`, 
      label: 'Categories', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>`
    },
    { 
      path: `/companies/${id}/transactions`, 
      label: 'Transactions', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`
    },
    { 
      path: `/companies/${id}/documents`, 
      label: 'Documents', 
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>`
    }
  ]
})
</script>

<style scoped>
/* Layout */
.pb-dashboard-layout {
  display: flex;
  min-height: 100vh;
  background: #f8fafc;
}

/* Sidebar */
.pb-sidebar {
  width: 280px;
  background: #1a1a2e;
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

/* Sidebar Header */
.pb-sidebar-header {
  padding: 1.5rem 1.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.pb-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  margin-bottom: 1.5rem;
}

.pb-logo-icon {
  width: 32px;
  height: 32px;
  background: #4f46e5;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pb-logo span {
  font-size: 18px;
  font-weight: 700;
  color: white;
  letter-spacing: -0.3px;
}

/* Company Selector */
.pb-company-selector {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pb-company-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #94a3b8;
}

.pb-select {
  width: 100%;
  padding: 10px 12px;
  background: #2d2d44;
  border: 1px solid #3d3d54;
  border-radius: 10px;
  color: white;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.15s;
}

.pb-select:hover {
  background: #3d3d54;
  border-color: #4f46e5;
}

.pb-select:focus {
  outline: none;
  border-color: #4f46e5;
}

/* Navigation */
.pb-nav {
  flex: 1;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.pb-nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 10px;
  color: #94a3b8;
  text-decoration: none;
  transition: all 0.15s;
  font-size: 14px;
  font-weight: 500;
}

.pb-nav-link:hover {
  background: rgba(79, 70, 229, 0.1);
  color: #c7d2fe;
}

.pb-nav-link--active {
  background: #4f46e5;
  color: white;
}

.pb-nav-link--active .pb-nav-icon {
  color: white;
}

.pb-nav-icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pb-nav-icon svg {
  width: 18px;
  height: 18px;
}

.pb-nav-label {
  flex: 1;
}

/* Empty State */
.pb-nav-empty {
  text-align: center;
  padding: 2rem 1rem;
  color: #64748b;
}

.pb-nav-empty svg {
  margin-bottom: 0.75rem;
  opacity: 0.5;
}

.pb-nav-empty p {
  font-size: 13px;
}

/* Sidebar Footer */
.pb-sidebar-footer {
  padding: 1.25rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.pb-logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px 16px;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  border-radius: 10px;
  color: #f87171;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
}

.pb-logout-btn:hover {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.3);
  color: #fca5a5;
}

/* Main Content */
.pb-main-content {
  flex: 1;
  overflow-x: auto;
}

.pb-content-wrapper {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Scrollbar Styling */
.pb-sidebar::-webkit-scrollbar {
  width: 4px;
}

.pb-sidebar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.pb-sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

.pb-sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .pb-sidebar {
    width: 240px;
  }
  
  .pb-content-wrapper {
    padding: 1rem;
  }
  
  .pb-nav-link {
    padding: 8px 10px;
    font-size: 13px;
  }
  
  .pb-logo span {
    font-size: 16px;
  }
}

@media (max-width: 640px) {
  .pb-sidebar {
    position: fixed;
    z-index: 100;
    transform: translateX(-100%);
    transition: transform 0.2s;
  }
  
  /* Add hamburger menu button in main content for mobile */
  .pb-sidebar.pb-sidebar--open {
    transform: translateX(0);
  }
}
</style>