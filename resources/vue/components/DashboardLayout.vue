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
          <router-link
            to="/companies/create"
            class="pb-create-company-btn pb-create-company-btn--sm"
            :class="{ 'pb-create-company-btn--highlight': highlightCreateCompanyCta }"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add New Company
          </router-link>
        </div>
      </div>

      <nav class="pb-nav">
        <router-link
          v-for="link in navLinks"
          :key="link.label"
          :to="link.path"
          class="pb-nav-link"
          :class="{ 'pb-nav-link--active': !link.disabled && isActiveLink(link.path), 'pb-nav-link--disabled': link.disabled }"
          :aria-disabled="link.disabled ? 'true' : 'false'"
          :tabindex="link.disabled ? -1 : 0"
          :title="link.disabled ? 'Create or select a company first' : ''"
          @click="onNavLinkClick($event, link)"
        >
          <span class="pb-nav-icon" v-html="link.iconSvg"></span>
          <span class="pb-nav-label">{{ link.label }}</span>
        </router-link>
      </nav>

      <div class="pb-sidebar-footer">
        <div class="pb-footer-links">
          <router-link to="/dashboard/settings" class="pb-footer-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Settings
          </router-link>
          <button class="pb-footer-link" @click="copySupportEmail" title="Copy Support Email">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Support
          </button>
        </div>
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
      <header class="pb-dashboard-header">
        <div class="pb-welcome-section">
          <div class="pb-user-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="pb-welcome-text">
            <h1 class="pb-greeting-title">Hi, {{ authStore.user?.name || 'there' }}</h1>
            <p class="pb-greeting-subtitle">Welcome back. Here's what's happening with your business.</p>
          </div>
        </div>
        <div class="pb-header-actions" v-if="showDateFilter">
           <DateRangePicker />
        </div>
      </header>
      <div class="pb-content-wrapper">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, watch, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import { useCompanyStore } from '../stores/company.js'
import { useDashboardStore } from '../stores/dashboard.js'
import DateRangePicker from './DateRangePicker.vue'

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
const dashboardStore = useDashboardStore()

const currentCompanyId = ref(String(props.companyId || route.params.companyId || ''))
const companiesLoaded = ref(false)

const companies = computed(() => companyStore.companies)

const highlightCreateCompanyCta = computed(() => {
  return companiesLoaded.value && companies.value.length === 0
})

const showDateFilter = computed(() => {
  const allowedNames = ['Home', 'CompanyDashboard', 'Invoices', 'Bills', 'Transactions'];
  return allowedNames.includes(route.name);
})

onMounted(async () => {
  if (companyStore.companies.length === 0) {
    await companyStore.fetchCompanies()
  }

  companiesLoaded.value = true

  const routeCompanyId = route.params.companyId || props.companyId
  if (routeCompanyId) {
    currentCompanyId.value = String(routeCompanyId)
  }

  const storedCompanyId = localStorage.getItem('pb_active_company_id')
  const storedIsValid = storedCompanyId
    ? companyStore.companies.some((c) => String(c.id) === String(storedCompanyId))
    : false

  if (!currentCompanyId.value && storedCompanyId && storedIsValid) {
    currentCompanyId.value = String(storedCompanyId)
  }

  if (!storedIsValid && storedCompanyId) {
    localStorage.removeItem('pb_active_company_id')
  }

  if (currentCompanyId.value) {
    localStorage.setItem('pb_active_company_id', currentCompanyId.value)
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
  if (newVal) {
    localStorage.setItem('pb_active_company_id', newVal);
    if (newVal !== oldVal) {
      router.push(`/companies/${newVal}`);
    }
  } else if (!newVal && oldVal) {
    localStorage.removeItem('pb_active_company_id');
    router.push('/dashboard');
  }
})

const isActiveLink = (path) => {
  const currentPath = route.path;
  // Dashboard: active when on /dashboard OR /companies/:id (exact, not sub-pages like /companies/:id/invoices)
  if (path.match(/^\/(dashboard|companies\/\d+)$/) || path === '/dashboard') {
    // check if this IS the dashboard link by seeing if it's the path we built
    const dashPath = currentCompanyId.value ? `/companies/${currentCompanyId.value}` : '/dashboard';
    if (path === dashPath || path === '/dashboard') {
      return currentPath === dashPath || currentPath === '/dashboard' || /^\/companies\/\d+$/.test(currentPath);
    }
  }
  return currentPath.startsWith(path + '/') || currentPath === path;
}

const selectedCompany = computed(() => {
  return companies.value.find(company => String(company.id) === currentCompanyId.value)
})

const onNavLinkClick = (event, link) => {
  if (link?.disabled) {
    event.preventDefault()
    event.stopPropagation()
  }
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

const copySupportEmail = async () => {
  try {
    await navigator.clipboard.writeText('support@primebalance.com');
    alert('Support email (support@primebalance.com) copied to clipbaord! We look forward to hearing from you.');
  } catch (err) {
    alert('The support email is: support@primebalance.com');
  }
}

const homeLink = computed(() => {
  return '/'
})

const navLinks = computed(() => {
  const id = currentCompanyId.value
  const base = id ? `/companies/${id}` : null
  const noCompanies = companiesLoaded.value && companies.value.length === 0
  const companyRequiredDisabled = !base

  return [
    { 
      path: base ? base : `/dashboard`, 
      label: 'Dashboard', 
      disabled: false,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>`
    },
    { 
      path: base ? `${base}/invoices` : `/dashboard`, 
      label: 'Invoices', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>`
    },
    { 
      path: base ? `${base}/bills` : `/dashboard`, 
      label: 'Bills', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>`
    },
    { 
      path: base ? `${base}/clients` : `/dashboard`, 
      label: 'Clients', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`
    },
    { 
      path: base ? `${base}/suppliers` : `/dashboard`, 
      label: 'Suppliers', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`
    },
    { 
      path: base ? `${base}/accounts` : `/dashboard`, 
      label: 'Accounts', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>`
    },
    { 
      path: base ? `${base}/categories` : `/dashboard`, 
      label: 'Categories', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>`
    },
    { 
      path: base ? `${base}/transactions` : `/dashboard`, 
      label: 'Transactions', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`
    },
    { 
      path: base ? `${base}/documents` : `/dashboard`, 
      label: 'Documents', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>`
    },
    { 
      path: base ? `${base}/team` : `/dashboard`, 
      label: 'Team', 
      disabled: companyRequiredDisabled,
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>`
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

.pb-nav-link--disabled {
  opacity: 0.45;
  cursor: not-allowed;
  pointer-events: none;
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
  margin-bottom: 1.5rem;
}

.pb-create-company-btn {
  display: inline-flex;
  align-items: center;
  padding: 10px 16px;
  background: #4f46e5;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.pb-create-company-btn--sm {
  margin-top: 4px;
  padding: 8px 12px;
  font-size: 13px;
  background: transparent;
  color: #c7d2fe;
  border: 1px dashed rgba(79, 70, 229, 0.5);
  justify-content: center;
}

.pb-create-company-btn--sm:hover {
  background: rgba(79, 70, 229, 0.1);
  color: white;
  border-color: #4f46e5;
}

.pb-create-company-btn--highlight {
  border-style: solid;
  border-color: rgba(165, 180, 252, 0.95);
  background: linear-gradient(
    110deg,
    rgba(79, 70, 229, 0.10) 0%,
    rgba(165, 180, 252, 0.22) 45%,
    rgba(79, 70, 229, 0.10) 100%
  );
  background-size: 220% 100%;
  color: #ffffff;
  box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.45);
  animation: pbCtaPulse 1.35s ease-in-out infinite, pbCtaShimmer 1.15s linear infinite;
}

@keyframes pbCtaShimmer {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 100% 50%;
  }
}

@keyframes pbCtaPulse {
  0% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.42);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
  }
}

.pb-create-company-btn:not(.pb-create-company-btn--sm):hover {
  background: #4338ca;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}


/* Sidebar Footer */
.pb-sidebar-footer {
  padding: 1.25rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.pb-footer-links {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}

.pb-footer-link {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  color: #94a3b8;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.15s;
}

.pb-footer-link:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
  border-color: rgba(255, 255, 255, 0.15);
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
  padding: 0 1.5rem 1.5rem 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* New Top Header Styles */
.pb-dashboard-header {
  padding: 1.5rem 2rem;
  background: white;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pb-welcome-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.pb-user-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4f46e5;
  flex-shrink: 0;
}

.pb-greeting-title {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  line-height: 1.2;
}

.pb-greeting-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 4px 0 0 0;
}

.pb-header-actions {
  display: flex;
  align-items: center;
}

.pb-header-actions {
  display: flex;
  align-items: center;
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