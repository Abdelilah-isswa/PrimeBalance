import { defineStore } from 'pinia';
import axios from 'axios';

export const useInvoiceStore = defineStore('invoice', {
  state: () => ({
    invoices: [],
    currentInvoice: null,
    loading: false,
  }),
  actions: {
    async fetchInvoices(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/invoices`);
        this.invoices = response.data.data.invoices ?? [];
      } catch (error) {
        console.error('Fetch invoices error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchInvoice(companyId, id) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/invoices/${id}`);
        this.currentInvoice = response.data.data;
        return this.currentInvoice;
      } catch (error) {
        console.error('Fetch invoice error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createInvoice(companyId, data) {
      try {
        const response = await axios.post(`companies/${companyId}/clients/${data.client_id}/invoices`, data);
        this.invoices.unshift(response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Create invoice error:', error);
      }
    },
    async updateInvoice(companyId, id, data) {
      try {
        const response = await axios.put(`companies/${companyId}/invoices/${id}`, data);
        const index = this.invoices.findIndex(i => i.id === id);
        if (index !== -1) this.invoices[index] = response.data.data;
        if (this.currentInvoice?.id === id) this.currentInvoice = response.data.data;
      } catch (error) {
        console.error('Update invoice error:', error);
      }
    },
    async receivePayment(companyId, id, amount) {
      try {
        const response = await axios.post(`companies/${companyId}/invoices/${id}/receive`, { amount });
        // Refresh invoice
        await this.fetchInvoice(companyId, id);
      } catch (error) {
        console.error('Receive payment error:', error);
      }
    },
  },
});

