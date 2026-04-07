<template>
  <div class="dashboard-layout">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <router-link :to="homeLink" class="sidebar-logo">PrimeBalance</router-link>
        <div class="sidebar-company">
          <span class="sidebar-company-label">Company</span>
          <select :value="currentCompanyId" @input="currentCompanyId = $event.target.value" class="sidebar-select">
            <option disabled value="">Select company</option>
            <option v-for="company in companies" :key="company.id" :value="String(company.id)">
              {{ company.name }}
            </option>
          </select>
        </div>
      </div>

      <nav class="sidebar-nav">
        <router-link
          v-for="link in navLinks"
          :key="link.path"
          :to="link.path"
          class="sidebar-link"
          :class="{ 'sidebar-link--active': route.path.startsWith(link.path) }"
        >
          <span class="sidebar-icon">{{ link.icon }}</span>
          <span>{{ link.label }}</span>
        </router-link>

        <div v-if="navLinks.length === 0" class="sidebar-empty">
          Select a company from the dropdown above to see navigation links.
        </div>
      </nav>

      <div class="sidebar-footer">
        <button class="sidebar-logout" @click="logout">Logout</button>
      </div>
    </aside>

    <main class="dashboard-content">
      <slot />
      <router-view />
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

  // Ensure currentCompanyId is set from route if available
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
    { path: `/companies/${id}`, label: 'Dashboard', icon: '📊' },
    { path: `/companies/${id}/invoices`, label: 'Invoices', icon: '🧾' },
    { path: `/companies/${id}/bills`, label: 'Bills', icon: '📉' },
    { path: `/companies/${id}/clients`, label: 'Clients', icon: '👥' },
    { path: `/companies/${id}/suppliers`, label: 'Suppliers', icon: '🏢' },
    { path: `/companies/${id}/accounts`, label: 'Accounts', icon: '💰' },
    { path: `/companies/${id}/categories`, label: 'Categories', icon: '📂' },
    { path: `/companies/${id}/transactions`, label: 'Transactions', icon: '💳' },
    { path: `/companies/${id}/documents`, label: 'Documents', icon: '📄' },
  ]
})
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background: #f3f4f6;
}

.sidebar {
  width: 240px;
  padding: 1.5rem 1rem;
  background: #111827;
  color: #f9fafb;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.sidebar-brand {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.sidebar-logo {
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #f8fafc;
}

.sidebar-company {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.sidebar-company-label {
  font-size: 0.8rem;
  color: #9ca3af;
}

.sidebar-select {
  width: 100%;
  padding: 0.6rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #374151;
  background: #1f2937;
  color: #f9fafb;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  flex: 1;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1rem;
  color: #d1d5db;
  border-radius: 0.75rem;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
}

.sidebar-link:hover {
  background: rgba(255, 255, 255, 0.06);
  color: #ffffff;
}

.sidebar-link--active {
  background: #2563eb;
  color: #ffffff;
}

.sidebar-icon {
  font-size: 1rem;
}

.sidebar-empty {
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #9ca3af;
  line-height: 1.5;
}

.sidebar-footer {
  padding-top: 1rem;
  border-top: 1px solid rgba(148, 163, 184, 0.2);
}

.sidebar-logout {
  width: 100%;
  padding: 0.85rem 1rem;
  border: none;
  border-radius: 0.75rem;
  background: #ef4444;
  color: white;
  cursor: pointer;
  transition: background 0.2s;
}

.sidebar-logout:hover {
  background: #dc2626;
}

.dashboard-content {
  flex: 1;
  padding: 1.5rem;
  overflow-x: hidden;
}
</style>
