import { defineStore } from 'pinia';
import axios from 'axios';

export const useSupplierStore = defineStore('supplier', {
  state: () => ({
    suppliers: [],
    currentSupplier: null,
    loading: false,
  }),
  actions: {
    async fetchSuppliers(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/suppliers`);
        this.suppliers = response.data.data;
      } catch (error) {
        console.error('Fetch suppliers error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchSupplier(companyId, id) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/suppliers/${id}`);
        this.currentSupplier = response.data.data;
        return this.currentSupplier;
      } catch (error) {
        console.error('Fetch supplier error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createSupplier(companyId, data) {
      try {
        const response = await axios.post(`companies/${companyId}/suppliers`, data);
        this.suppliers.push(response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Create supplier error:', error);
      }
    },
    async updateSupplier(companyId, id, data) {
      try {
        const response = await axios.put(`companies/${companyId}/suppliers/${id}`, data);
        const index = this.suppliers.findIndex(s => s.id === id);
        if (index !== -1) this.suppliers[index] = response.data.data;
        if (this.currentSupplier?.id === id) this.currentSupplier = response.data.data;
      } catch (error) {
        console.error('Update supplier error:', error);
      }
    },
  },
});

