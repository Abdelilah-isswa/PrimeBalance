<template>
  <div class="pb-datepicker" ref="container">
    <button class="pb-datepicker-trigger" @click="isOpen = !isOpen">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      <span>{{ formattedRange }}</span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :style="{ transform: isOpen ? 'rotate(180deg)' : '' }">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>

    <div v-if="isOpen" class="pb-datepicker-dropdown">
      <div class="pb-datepicker-header">
        <button @click="prevMonth" class="pb-nav-btn">&lt;</button>
        <span class="pb-current-month">{{ monthNames[viewDate.getMonth()] }} {{ viewDate.getFullYear() }}</span>
        <button @click="nextMonth" class="pb-nav-btn">&gt;</button>
      </div>

      <div class="pb-datepicker-calendar">
        <div v-for="day in weekDays" :key="day" class="pb-calendar-weekday">{{ day }}</div>
        
        <div
          v-for="date in calendarDays"
          :key="date.toISOString()"
          class="pb-calendar-day"
          :class="{
            'pb-day-outside': date.getMonth() !== viewDate.getMonth(),
            'pb-day-selected': isSelected(date),
            'pb-day-in-range': isInRange(date),
            'pb-day-start': isStart(date),
            'pb-day-end': isEnd(date),
            'pb-day-today': isToday(date)
          }"
          @click="selectDate(date)"
        >
          {{ date.getDate() }}
        </div>
      </div>

      <!-- Removed extra buttons from beside the date picker -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useDashboardStore } from '../stores/dashboard.js';

const dashboardStore = useDashboardStore();
const isOpen = ref(false);
const container = ref(null);
const viewDate = ref(new Date(dashboardStore.endDate));

const weekDays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

const formattedRange = computed(() => {
  const start = new Date(dashboardStore.startDate);
  const end = new Date(dashboardStore.endDate);
  const opt = { month: 'short', day: 'numeric' };
  return `${start.toLocaleDateString('en-US', opt)} - ${end.toLocaleDateString('en-US', opt)}`;
});

const calendarDays = computed(() => {
  const year = viewDate.value.getFullYear();
  const month = viewDate.value.getMonth();
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  
  const startDay = new Date(firstDay);
  startDay.setDate(firstDay.getDate() - firstDay.getDay());
  
  const days = [];
  const totalDays = 42; // 6 weeks
  
  for (let i = 0; i < totalDays; i++) {
    days.push(new Date(startDay));
    startDay.setDate(startDay.getDate() + 1);
  }
  
  return days;
});

const selectDate = (date) => {
  const start = dashboardStore.startDate;
  const end = dashboardStore.endDate;
  const dateStr = date.toISOString().split('T')[0];

  if (!start || (start && end)) {
    dashboardStore.startDate = dateStr;
    dashboardStore.endDate = '';
  } else {
    if (dateStr < start) {
      dashboardStore.endDate = start;
      dashboardStore.startDate = dateStr;
    } else {
      dashboardStore.endDate = dateStr;
    }
  }
};

const isSelected = (date) => {
  const d = date.toISOString().split('T')[0];
  return d === dashboardStore.startDate || d === dashboardStore.endDate;
};

const isInRange = (date) => {
  const d = date.toISOString().split('T')[0];
  const start = dashboardStore.startDate;
  const end = dashboardStore.endDate;
  return start && end && d > start && d < end;
};

const isStart = (date) => date.toISOString().split('T')[0] === dashboardStore.startDate;
const isEnd = (date) => date.toISOString().split('T')[0] === dashboardStore.endDate;
const isToday = (date) => date.toDateString() === new Date().toDateString();

const prevMonth = () => {
  viewDate.value = new Date(viewDate.value.setMonth(viewDate.value.getMonth() - 1));
};

const nextMonth = () => {
  viewDate.value = new Date(viewDate.value.setMonth(viewDate.value.getMonth() + 1));
};

const clear = () => {
  dashboardStore.startDate = '';
  dashboardStore.endDate = '';
};

const handleClickOutside = (e) => {
  if (container.value && !container.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<style scoped>
.pb-datepicker {
  position: relative;
  font-family: inherit;
}

.pb-datepicker-trigger {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 16px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  color: #1e293b;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.pb-datepicker-trigger:hover {
  border-color: #4f46e5;
  background: #f8fafc;
}

.pb-datepicker-trigger span {
  min-width: 120px;
}

/* At narrower widths, hide the textual range and caret, leaving only the calendar icon */
@media (max-width: 920px) {
  .pb-datepicker-trigger {
    padding: 8px;
    gap: 6px;
  }

  .pb-datepicker-trigger span {
    display: none;
    min-width: 0;
  }

  .pb-datepicker-trigger > svg:last-child {
    display: none;
  }
}

.pb-datepicker-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 320px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  padding: 16px;
  z-index: 1000;
  animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
  from { transform: translateY(-10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.pb-datepicker-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.pb-current-month {
  font-weight: 700;
  color: #1e293b;
}

.pb-nav-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  background: #f1f5f9;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.pb-nav-btn:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.pb-datepicker-calendar {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
}

.pb-calendar-weekday {
  text-align: center;
  font-size: 12px;
  font-weight: 700;
  color: #94a3b8;
  padding: 8px 0;
}

.pb-calendar-day {
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.1s;
  position: relative;
}

.pb-calendar-day:hover:not(.pb-day-in-range) {
  background: #f1f5f9;
}

.pb-day-outside {
  color: #cbd5e1;
}

.pb-day-selected {
  background: #4f46e5 !important;
  color: white !important;
}

.pb-day-in-range {
  background: #eef2ff;
  border-radius: 0;
  color: #4f46e5;
}

.pb-day-start {
  border-radius: 8px 0 0 8px !important;
}

.pb-day-end {
  border-radius: 0 8px 8px 0 !important;
}

.pb-day-today::after {
  content: '';
  position: absolute;
  bottom: 4px;
  width: 4px;
  height: 4px;
  background: currentColor;
  border-radius: 50%;
}

.pb-datepicker-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
}

.pb-clear-btn {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.pb-apply-btn {
  background: #4f46e5;
  border: none;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.pb-apply-btn:hover {
  background: #4338ca;
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4);
}
</style>
