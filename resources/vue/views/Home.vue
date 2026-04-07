<template>
  <DashboardLayout :company-id="currentCompany?.id">
    <div class="container mx-auto px-6 py-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Income Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Income</h3>
          <p class="text-3xl font-bold text-green-600">{{ summary.income || '0.00' }} <span class="text-sm text-gray-500">USD</span></p>
        </div>
        <!-- Total Expenses Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Expenses</h3>
          <p class="text-3xl font-bold text-red-600">{{ summary.expenses || '0.00' }} <span class="text-sm text-gray-500">USD</span></p>
        </div>
        <!-- Overdue Invoices -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Overdue</h3>
          <p class="text-3xl font-bold text-orange-600">{{ summary.overdue || 0 }}</p>
        </div>
        <!-- Unpaid Bills -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Unpaid Bills</h3>
          <p class="text-3xl font-bold text-blue-600">{{ summary.unpaidBills || 0 }}</p>
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Recent Transactions</h3>
          </div>
          <div class="p-6">
            <div v-if="recentTransactions.length" class="space-y-4">
              <div v-for="trans in recentTransactions" :key="trans.id" class="flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0">
                <div>
                  <p class="font-medium text-gray-900">{{ trans.description }}</p>
                  <p class="text-sm text-gray-500">{{ trans.category?.name || 'Uncategorized' }} · {{ trans.date }}</p>
                </div>
                <div class="text-right">
                  <p :class="['font-bold', trans.amount > 0 ? 'text-green-600' : 'text-red-600']">{{ trans.amount > 0 ? '+' : '' }}{{ trans.amount.toFixed(2) }}</p>
                  <p class="text-sm text-gray-500">{{ trans.account?.name }}</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12 text-gray-500">
              No recent transactions.
            </div>
          </div>
        </div>
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md">
          <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h3>
            <router-link to="/companies" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg text-center font-medium hover:bg-blue-700 mb-4">
              Manage Companies
            </router-link>
            <router-link v-if="currentCompany" :to="`/companies/${currentCompany.id}/invoices`" class="block w-full bg-green-600 text-white py-3 px-4 rounded-lg text-center font-medium hover:bg-green-700 mb-4">
              + New Invoice
            </router-link>
            <router-link v-if="currentCompany" :to="`/companies/${currentCompany.id}/bills`" class="block w-full bg-purple-600 text-white py-3 px-4 rounded-lg text-center font-medium hover:bg-purple-700">
              + New Bill
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import DashboardLayout from '../components/DashboardLayout.vue';
import { useCompanyStore } from '../stores/company.js';
import { useInvoiceStore } from '../stores/invoice.js';
import { useBillStore } from '../stores/bill.js';
import { useAccountStore } from '../stores/account.js';
import { useTransactionStore } from '../stores/transaction.js';
import { useAuthStore } from '../stores/auth.js';

const route = useRoute();
const router = useRouter();
const companyStore = useCompanyStore();
const invoiceStore = useInvoiceStore();
const billStore = useBillStore();
const accountStore = useAccountStore();
const transactionStore = useTransactionStore();
const authStore = useAuthStore();

const companies = computed(() => companyStore.companies);
const currentCompany = computed(() => companyStore.currentCompany);
const recentTransactions = ref([]);
const summary = ref({
  income: 0,
  expenses: 0,
  overdue: 0,
  unpaidBills: 0,
});

onMounted(async () => {
  await companyStore.fetchCompanies();

  if (companies.value.length === 1 && !currentCompany.value) {
    await companyStore.fetchCompany(companies.value[0].id);
  }

  // Load summary from first company or skip if none
  if (currentCompany.value) {
    const companyId = currentCompany.value.id;
    await Promise.all([
      invoiceStore.fetchInvoices(companyId),
      billStore.fetchBills(companyId),
      accountStore.fetchAccounts(companyId),
      transactionStore.fetchTransactions(companyId),
    ]);
    // Mock summary calcs (enhance backend later)
    summary.value.income = invoiceStore.invoices.reduce((sum, i) => sum + parseFloat(i.total_amount || 0), 0);
    summary.value.expenses = billStore.bills.reduce((sum, b) => sum + parseFloat(b.total_amount || 0), 0);
    summary.value.overdue = invoiceStore.invoices.filter(i => i.status === 'overdue').length;
    summary.value.unpaidBills = billStore.bills.filter(b => b.status !== 'paid').length;
    recentTransactions.value = transactionStore.transactions.slice(0, 5);
  }
});
</script>
