import { defineStore } from 'pinia';
import axios from 'axios';

export const useBillStore = defineStore('bill', {
  state: () => ({
    bills: [],
    currentBill: null,
    loading: false,
  }),
  actions: {
    async fetchBills(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/bills`);
        this.bills = response.data.data.bills ?? [];
      } catch (error) {
        console.error('Fetch bills error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchBill(companyId, id) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/bills/${id}`);
        this.currentBill = response.data.data.bill ?? null;
        return this.currentBill;
      } catch (error) {
        console.error('Fetch bill error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createBill(companyId, data) {
      try {
        const response = await axios.post(`companies/${companyId}/bills`, data);
        await this.fetchBills(companyId);
        return response.data.data;
      } catch (error) {
        console.error('Create bill error:', error);
        throw error;
      }
    },
    async updateBill(companyId, id, data) {
      try {
        const response = await axios.put(`companies/${companyId}/bills/${id}`, data);
        const updated = response.data.data;
        const index = this.bills.findIndex(b => b.id === id);
        if (index !== -1) this.bills[index] = updated;
        if (this.currentBill?.id === id) this.currentBill = updated;
        return updated;
      } catch (error) {
        console.error('Update bill error:', error);
        throw error;
      }
    },
    async payBill(companyId, id, payload) {
      try {
        const response = await axios.post(`companies/${companyId}/bills/${id}/pay`, payload);
        const updated = response.data.data;
        const index = this.bills.findIndex(b => b.id === id);
        if (index !== -1) this.bills[index] = updated;
        if (this.currentBill?.id === id) this.currentBill = updated;
        return updated;
      } catch (error) {
        console.error('Pay bill error:', error);
        throw error;
      }
    },
  },
});

