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
        const response = await axios.get(`companies/${companyId}/accounts`);
        this.accounts = response.data.data.accounts ?? [];
      } catch (error) {
        console.error('Fetch accounts error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchAccount(companyId, id) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/accounts/${id}`);
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
        const response = await axios.post(`companies/${companyId}/accounts`, data);
        await this.fetchAccounts(companyId);
        return response.data.data;
      } catch (error) {
        console.error('Create account error:', error);
        throw error;
      }
    },
    async updateAccount(companyId, id, data) {
      try {
        await axios.put(`companies/${companyId}/accounts/${id}`, data);
        await this.fetchAccounts(companyId);
      } catch (error) {
        console.error('Update account error:', error);
        throw error;
      }
    },
    async deleteAccount(companyId, accountId) {
      try {
        await axios.delete(`companies/${companyId}/accounts/${accountId}`);
        await this.fetchAccounts(companyId);
      } catch (error) {
        console.error('Delete account error:', error);
        throw error;
      }
    },
  },
});

