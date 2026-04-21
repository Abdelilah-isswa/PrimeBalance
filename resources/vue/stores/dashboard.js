import { defineStore } from 'pinia';

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    startDate: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
    endDate: new Date().toISOString().split('T')[0],
  }),
  actions: {
    setDateRange(start, end) {
      this.startDate = start;
      this.endDate = end;
    },
  },
});
