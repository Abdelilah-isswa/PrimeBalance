import { defineStore } from 'pinia';
import axios from 'axios';

export const useTransactionStore = defineStore('transaction', {
  state: () => ({
    transactions: [],
    loading: false,
  }),
  actions: {
    async fetchTransactions(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/v1/companies/${companyId}/transactions`);
        this.transactions = response.data.data;
      } catch (error) {
        console.error('Fetch transactions error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createTransaction(companyId, data) {
      try {
        const response = await axios.post(`/api/v1/companies/${companyId}/transactions`, data);
        this.transactions.unshift(response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Create transaction error:', error);
      }
    },
  },
});

