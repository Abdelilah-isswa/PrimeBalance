import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const routes = [
  { path: '/login', name: 'Login', component: () => import('../views/auth/Login.vue') },
  { path: '/register', name: 'Register', component: () => import('../views/auth/Register.vue') },
  { path: '/', name: 'Landing', component: () => import('../views/Landing.vue') },
  { path: '/dashboard', name: 'Home', component: () => import('../views/Home.vue'), meta: { auth: true } },
  { path: '/companies', name: 'Companies', component: () => import('../views/companies/Index.vue'), meta: { auth: true } },
  { path: '/companies/create', name: 'CompanyCreate', component: () => import('../views/companies/Create.vue'), meta: { auth: true } },
  { path: '/companies/:companyId', name: 'CompanyShow', component: () => import('../views/companies/Show.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/edit', name: 'CompanyEdit', component: () => import('../views/companies/Edit.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/categories', name: 'Categories', component: () => import('../views/categories/Index.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/clients/create', name: 'ClientCreate', component: () => import('../views/clients/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/clients/balances', name: 'ClientBalances', component: () => import('../views/clients/Balances.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/clients/:clientId/edit', name: 'ClientEdit', component: () => import('../views/clients/Form.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/suppliers/create', name: 'SupplierCreate', component: () => import('../views/suppliers/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/suppliers/balances', name: 'SupplierBalances', component: () => import('../views/suppliers/Balances.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/suppliers/:supplierId/edit', name: 'SupplierEdit', component: () => import('../views/suppliers/Form.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/accounts', name: 'Accounts', component: () => import('../views/accounts/Index.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/accounts/create', name: 'AccountCreate', component: () => import('../views/accounts/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/accounts/:accountId/edit', name: 'AccountEdit', component: () => import('../views/accounts/Form.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/invoices', name: 'Invoices', component: () => import('../views/invoices/Index.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/clients/:clientId/invoices/create', name: 'InvoiceCreate', component: () => import('../views/invoices/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/invoices/:invoiceId', name: 'InvoiceShow', component: () => import('../views/invoices/Show.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/invoices/:invoiceId/edit', name: 'InvoiceEdit', component: () => import('../views/invoices/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/invoices/:invoiceId/receive', name: 'InvoiceReceive', component: () => import('../views/invoices/Receive.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/bills', name: 'Bills', component: () => import('../views/bills/Index.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/suppliers/:supplierId/bills/create', name: 'BillCreate', component: () => import('../views/bills/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/bills/:billId', name: 'BillShow', component: () => import('../views/bills/Show.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/bills/:billId/edit', name: 'BillEdit', component: () => import('../views/bills/Form.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/bills/:billId/pay', name: 'BillPay', component: () => import('../views/bills/Pay.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/transactions', name: 'Transactions', component: () => import('../views/transactions/Index.vue'), meta: { auth: true } },
  { path: '/companies/:companyId/transactions/create', name: 'TransactionCreate', component: () => import('../views/transactions/Form.vue'), meta: { auth: true } },

  { path: '/companies/:companyId/documents', name: 'Documents', component: () => import('../views/documents/Index.vue'), meta: { auth: true } },
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
