<template>
  <div class="pb-wrap pb-about-page">
    <!-- Navbar (simplified for subpage) -->
    <nav class="pb-nav">
      <div class="pb-nav-left">
        <router-link to="/" class="pb-logo">
          <div class="pb-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" width="14" height="14">
              <path d="M3 12h2l2-5 3 8 2-5 2 5 2-5 2 5h2" stroke="white" fill="none"/>
            </svg>
          </div>
          <span>PrimeBalance</span>
        </router-link>
        <div class="pb-nav-links">
          <router-link to="/">Home</router-link>
          <router-link to="/about" class="pb-active-link">About Us</router-link>
        </div>
      </div>
      <div class="pb-nav-right">
        <template v-if="authStore.token">
          <router-link to="/dashboard"><button class="btn-primary">Dashboard</button></router-link>
        </template>
        <template v-else>
          <router-link to="/login"><button class="btn-ghost">Login</button></router-link>
        </template>
      </div>
    </nav>

    <!-- Header Section -->
    <header class="pb-about-header">
      <h1>About PrimeBalance</h1>
      <p>We are on a mission to completely automate your company's accounting lifecycle.</p>
    </header>

    <!-- Main Content -->
    <main class="pb-about-main">
      <section class="pb-about-section">
        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Our Team" class="pb-about-img" />
        <div class="pb-about-text">
          <h2>Our Story</h2>
          <p>
            Started in 2026, PrimeBalance was born out of pure frustration with existing, outdated accounting platforms. Small and medium businesses needed modern, clean, and blazingly fast tools.
            We set out to create software that does exactly that - tracking your transactions, clients, invoices, and suppliers under one beautiful roof without requiring a degree in finance.
          </p>
        </div>
      </section>

      <section class="pb-about-values">
        <h2>Our Core Values</h2>
        <div class="pb-values-grid">
          <div class="pb-value-card">
            <div class="pb-icon-box">🌟</div>
            <h3>Simplicity</h3>
            <p>We strip away the useless jargon. Accounting should be straight to the point.</p>
          </div>
          <div class="pb-value-card">
            <div class="pb-icon-box">⚡</div>
            <h3>Speed</h3>
            <p>Every millisecond counts. We built the software so everything loads instantly.</p>
          </div>
          <div class="pb-value-card">
            <div class="pb-icon-box">🔒</div>
            <h3>Security</h3>
            <p>Your financial records are safely encrypted. Security is the foundation of everything.</p>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="pb-footer">
      © {{ new Date().getFullYear() }} PrimeBalance. Built for the modern business.
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth.js';
const authStore = useAuthStore();
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.pb-wrap { font-family: 'Inter', system-ui, sans-serif; color: #1a1a2e; background: #ffffff; }

.pb-nav {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.9rem 2.5rem; background: #ffffff; border-bottom: 0.5px solid #e2e8f0;
  position: sticky; top: 0; z-index: 100;
}
.pb-logo { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 15px; color: #1a1a2e; text-decoration: none; }
.pb-logo-icon { width: 28px; height: 28px; border-radius: 6px; background: #4f46e5; display: flex; align-items: center; justify-content: center; }
.pb-nav-links { display: flex; gap: 2rem; margin-left: 2rem; }
.pb-nav-links a { font-size: 14px; color: #64748b; text-decoration: none; transition: color 0.15s; }
.pb-nav-links a:hover { color: #1a1a2e; }
.pb-active-link { color: #4f46e5 !important; font-weight: 600; }
.btn-ghost { padding: 7px 16px; font-size: 13px; border: 0.5px solid #d1d5db; border-radius: 20px; background: transparent; color: #1a1a2e; cursor: pointer; text-decoration: none; }
.btn-primary { padding: 7px 18px; font-size: 13px; border: none; border-radius: 20px; background: #4f46e5; color: white; cursor: pointer; text-decoration: none; }

.pb-about-header { padding: 5rem 2rem; text-align: center; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.pb-about-header h1 { font-size: 3rem; font-weight: 800; color: #0f0e2e; letter-spacing: -1px; margin-bottom: 1rem; }
.pb-about-header p { font-size: 18px; color: #64748b; max-width: 600px; margin: 0 auto; }

.pb-about-main { max-width: 1000px; margin: 0 auto; padding: 4rem 2rem; }
.pb-about-section { margin-bottom: 5rem; }
.pb-about-img { width: 100%; height: auto; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); margin-bottom: 3rem; }
.pb-about-text h2 { font-size: 2rem; margin-bottom: 1rem; color: #1a1a2e; }
.pb-about-text p { font-size: 16px; line-height: 1.8; color: #475569; }

.pb-about-values { text-align: center; }
.pb-about-values h2 { font-size: 2rem; margin-bottom: 3rem; }
.pb-values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align: left; }
.pb-value-card { padding: 2rem; border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
.pb-icon-box { font-size: 2rem; margin-bottom: 1rem; }
.pb-value-card h3 { font-size: 1.25rem; margin-bottom: 0.5rem; color: #1a1a2e; }
.pb-value-card p { font-size: 14px; color: #64748b; line-height: 1.6; }

.pb-footer { padding: 3rem; text-align: center; font-size: 14px; color: #94a3b8; border-top: 1px solid #e2e8f0; background: #f8fafc; }
</style>
