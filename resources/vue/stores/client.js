import { defineStore } from 'pinia';
import axios from 'axios';

export const useClientStore = defineStore('client', {
  state: () => ({
    clients: [],
    currentClient: null,
    loading: false,
  }),
  actions: {
    async fetchClients(companyId) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/clients/balances`);
        this.clients = response.data.data.clients ?? [];
      } catch (error) {
        console.error('Fetch clients error:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchClient(companyId, id) {
      this.loading = true;
      try {
        const response = await axios.get(`companies/${companyId}/clients/${id}`);
        this.currentClient = response.data.data;
        return this.currentClient;
      } catch (error) {
        console.error('Fetch client error:', error);
      } finally {
        this.loading = false;
      }
    },
    async createClient(companyId, data) {
      try {
        const response = await axios.post(`companies/${companyId}/clients`, data);
        await this.fetchClients(companyId);
        return response.data.data;
      } catch (error) {
        console.error('Create client error:', error);
        throw error;
      }
    },
    async updateClient(companyId, id, data) {
      try {
        const response = await axios.put(`companies/${companyId}/clients/${id}`, data);
        const index = this.clients.findIndex(c => c.id === id);
        if (index !== -1) this.clients[index] = response.data.data;
        if (this.currentClient?.id === id) this.currentClient = response.data.data;
        return response.data.data;
      } catch (error) {
        console.error('Update client error:', error);
        throw error;
      }
    },
    async deleteClient(companyId, id) {
      try {
        await axios.delete(`companies/${companyId}/clients/${id}`);
        this.clients = this.clients.filter((c) => c.id !== id);
      } catch (error) {
        console.error('Delete client error:', error);
        throw error;
      }
    },
  },
});

