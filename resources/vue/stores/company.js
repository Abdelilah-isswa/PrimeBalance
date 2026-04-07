import { defineStore } from 'pinia';
import axios from 'axios';

export const useCompanyStore = defineStore('company', {
  state: () => ({
    companies: [],
    currentCompany: null,
    loading: false,
  }),
  getters: {
    companyOptions: (state) => state.companies.map(c => ({ value: c.id, label: c.name })),
  },
  actions: {
    async fetchCompanies() {
      this.loading = true;
      try {
        const response = await axios.get('/companies');
        this.companies = response.data.data;
      } catch (error) {
        console.error('Fetch companies error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchCompany(id) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/v1/companies/${id}`);
        this.currentCompany = response.data.data;
        return this.currentCompany;
      } catch (error) {
        console.error('Fetch company error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createCompany(data) {
      try {
        const response = await axios.post('/api/v1/companies', data);
        this.companies.push(response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Create company error:', error);
      }
    },
    async updateCompany(id, data) {
      try {
        const response = await axios.put(`/api/v1/companies/${id}`, data);
        const index = this.companies.findIndex(c => c.id === id);
        if (index !== -1) this.companies[index] = response.data.data;
        if (this.currentCompany?.id === id) this.currentCompany = response.data.data;
      } catch (error) {
        console.error('Update company error:', error);
      }
    },
  },
});

