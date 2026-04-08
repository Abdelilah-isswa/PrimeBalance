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
        const response = await axios.get(`companies/${companyId}/transactions`);
        this.transactions = response.data.data.transactions ?? [];
      } catch (error) {
        console.error('Fetch transactions error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createTransaction(companyId, data) {
      try {
        const response = await axios.post(`companies/${companyId}/transactions`, data);
        await this.fetchTransactions(companyId);
        return response.data.data;
      } catch (error) {
        console.error('Create transaction error:', error);
        throw error;
      }
    },
    async updateTransaction(companyId, transactionId, data) {
      try {
        await axios.put(`companies/${companyId}/transactions/${transactionId}`, data);
        await this.fetchTransactions(companyId);
      } catch (error) {
        console.error('Update transaction error:', error);
        throw error;
      }
    },
    async deleteTransaction(companyId, transactionId) {
      try {
        await axios.delete(`companies/${companyId}/transactions/${transactionId}`);
        await this.fetchTransactions(companyId);
      } catch (error) {
        console.error('Delete transaction error:', error);
        throw error;
      }
    },
  },
});

