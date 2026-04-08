import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import DashboardLayout from '../components/DashboardLayout.vue';

const routes = [
  { path: '/login', name: 'Login', component: () => import('../views/auth/Login.vue') },
  { path: '/register', name: 'Register', component: () => import('../views/auth/Register.vue') },
  { path: '/invitations/:token', name: 'Invitation', component: () => import('../views/Invitation.vue') },
  { path: '/', name: 'Landing', component: () => import('../views/Landing.vue') },
  { path: '/about', name: 'AboutUs', component: () => import('../views/AboutUs.vue') },
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { auth: true },
    children: [
      { path: '', name: 'Home', component: () => import('../views/Home.vue') },
      { path: 'settings', name: 'Settings', component: () => import('../views/Settings.vue') },
      { path: '/companies/create', name: 'CompanyCreate', component: () => import('../views/companies/Create.vue') }
    ]
  },
  { path: '/companies', name: 'Companies', component: () => import('../views/companies/Index.vue'), meta: { auth: true } },
  {
    path: '/companies/:companyId',
    component: DashboardLayout,
    props: true,
    meta: { auth: true },
    children: [
      { path: '', name: 'CompanyDashboard', component: () => import('../views/Home.vue') },
      { path: 'edit', name: 'CompanyEdit', component: () => import('../views/companies/Edit.vue') },
      { path: 'invoices', name: 'Invoices', component: () => import('../views/invoices/Index.vue') },
      { path: 'invoices/create', name: 'InvoiceCreate', component: () => import('../views/invoices/Form.vue') },
      { path: 'invoices/:invoiceId', name: 'InvoiceShow', component: () => import('../views/invoices/Show.vue') },
      { path: 'bills', name: 'Bills', component: () => import('../views/bills/Index.vue') },
      { path: 'bills/create', name: 'BillCreate', component: () => import('../views/bills/Form.vue') },
      { path: 'accounts', name: 'Accounts', component: () => import('../views/accounts/Index.vue') },
      { path: 'accounts/create', name: 'AccountCreate', component: () => import('../views/accounts/Form.vue') },
      { path: 'clients', name: 'Clients', component: () => import('../views/clients/Balances.vue') },
      { path: 'clients/create', name: 'ClientCreate', component: () => import('../views/clients/Form.vue') },
      { path: 'suppliers', name: 'Suppliers', component: () => import('../views/suppliers/Balances.vue') },
      { path: 'suppliers/create', name: 'SupplierCreate', component: () => import('../views/suppliers/Form.vue') },
      { path: 'categories', name: 'Categories', component: () => import('../views/categories/Index.vue') },
      { path: 'transactions', name: 'Transactions', component: () => import('../views/transactions/Index.vue') },
      { path: 'documents', name: 'Documents', component: () => import('../views/documents/Index.vue') },
      { path: 'team', name: 'Team', component: () => import('../views/team/Index.vue') },
    ]
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const authStore = useAuthStore();
  if (to.meta.auth && !authStore.token) {
    return '/login';
  }
  if ((to.path === '/login' || to.path === '/register') && authStore.token) {
    return '/dashboard';
  }
});

export default router;

