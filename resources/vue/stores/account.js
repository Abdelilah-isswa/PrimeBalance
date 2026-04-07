import { defineStore } from 'pinia';
import axios from 'axios';

export const useAccountStore = defineStore('account', {
  state: () => ({
    accounts: [],
    currentAccount: null,
    loading: false,
  }),
  getters: {
    activeAccounts: (state) => state.accounts.filter(a => a.is_active),
  },
  actions: {
    async fetchAccounts(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/v1/companies/${companyId}/accounts`);
        this.accounts = response.data.data ?? [];
      } catch (error) {
        console.error('Fetch accounts error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchAccount(companyId, id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/v1/companies/${companyId}/accounts/${id}`);
        this.currentAccount = response.data.data;
        return this.currentAccount;
      } catch (error) {
        console.error('Fetch account error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createAccount(companyId, data) {
      try {
        const response = await axios.post(`/api/v1/companies/${companyId}/accounts`, data);
        this.accounts.push(response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Create account error:', error);
      }
    },
    async updateAccount(companyId, id, data) {
      try {
        const response = await axios.put(`/api/v1/companies/${companyId}/accounts/${id}`, data);
        const index = this.accounts.findIndex(a => a.id === id);
        if (index !== -1) this.accounts[index] = response.data.data;
        if (this.currentAccount?.id === id) this.currentAccount = response.data.data;
      } catch (error) {
        console.error('Update account error:', error);
      }
    },
  },
});

