import { defineStore } from 'pinia';
import axios from 'axios';

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    loading: false,
  }),
  actions: {
    async fetchCategories(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/v1/companies/${companyId}/categories`);
        this.categories = response.data.data;
      } catch (error) {
        console.error('Fetch categories error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createCategory(companyId, data) {
      try {
        const response = await axios.post(`/api/v1/companies/${companyId}/categories`, data);
        this.categories.push(response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Create category error:', error);
      }
    },
    async updateCategory(companyId, id, data) {
      try {
        const response = await axios.put(`/api/v1/companies/${companyId}/categories/${id}`, data);
        const index = this.categories.findIndex(c => c.id === id);
        if (index !== -1) this.categories[index] = response.data.data;
      } catch (error) {
        console.error('Update category error:', error);
      }
    },
  },
});

