import { defineStore } from 'pinia';
import axios from 'axios';
console.log(axios.defaults)
axios.defaults.baseURL = 'http://127.0.0.1:8000/api/v1/';
axios.defaults.headers.common['Content-Type'] = 'application/json';

if (localStorage.getItem('token')) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
  }),
  actions: {
    async login(credentials) {
      const response = await axios.post('login', credentials);
      console.log(response)
      this.token = response.data.data.token;
      localStorage.setItem('token', this.token);
      console.log(this.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      await this.fetchUser();
    },
    async fetchUser() {
      if (!this.token) return;
      try {

        const response = await axios.get('user');
        this.user = response.data;
      } catch (error) {
        console.error('Fetch user error:', error);
        this.logout();
      }
    },
    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    },
  },
});
