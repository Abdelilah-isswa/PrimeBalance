<template>
  <div class="pb-wrap pb-about-page">
    <!-- Navbar (identical to Landing) -->
    <nav class="pb-nav">
      <div class="pb-nav-inner">
        <router-link to="/" class="pb-logo">
          <div class="pb-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="18" height="18">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <span>PrimeBalance</span>
        </router-link>

        <div class="pb-nav-links">
          <router-link :to="{ path: '/', hash: '' }">Home</router-link>
          <router-link :to="{ path: '/', hash: '#features' }">Features</router-link>
          <router-link :to="{ path: '/', hash: '#pricing' }">Pricing</router-link>
          <router-link to="/about" class="pb-active-link">About Us</router-link>
        </div>

        <div class="pb-nav-right">
          <template v-if="authStore.token">
            <router-link to="/dashboard"><button class="btn-primary">Dashboard</button></router-link>
          </template>
          <template v-else>
            <router-link to="/login"><button class="btn-ghost">Login</button></router-link>
            <router-link to="/register"><button class="btn-primary">Get Started</button></router-link>
          </template>
        </div>
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
            We set out to create software that does exactly that — tracking your transactions, clients, invoices, and suppliers under one beautiful roof without requiring a degree in finance.
          </p>
        </div>
      </section>

      <section class="pb-about-values">
        <h2>Our Core Values</h2>
        <div class="pb-values-grid">
          <div class="pb-value-card">
            <div class="pb-icon-box pb-icon-indigo">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                <path d="M2 17l10 5 10-5"/>
                <path d="M2 12l10 5 10-5"/>
              </svg>
            </div>
            <h3>Simplicity</h3>
            <p>We strip away the useless jargon. Accounting should be straight to the point.</p>
          </div>
          <div class="pb-value-card">
            <div class="pb-icon-box pb-icon-blue">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
              </svg>
            </div>
            <h3>Speed</h3>
            <p>Every millisecond counts. We built the software so everything loads instantly.</p>
          </div>
          <div class="pb-value-card">
            <div class="pb-icon-box pb-icon-green">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </div>
            <h3>Security</h3>
            <p>Your financial records are safely encrypted. Security is the foundation of everything.</p>
          </div>
        </div>
      </section>
    </main>

    <footer class="pb-footer">
      <div class="pb-footer-content">
        <div class="pb-brand">
          <div class="pb-logo-icon pb-footer-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" width="14" height="14">
              <path d="M3 12h2l2-5 3 8 2-5 2 5 2-5 2 5h2" stroke="white" fill="none"/>
            </svg>
          </div>
          <span class="pb-footer-brand-name">PrimeBalance</span>
          <p class="pb-footer-about">PrimeBalance helps modern teams manage invoices, bills, and transactions with clarity and speed.</p>
        </div>
        
        <div class="pb-footer-links-group">
          <strong>Product</strong>
          <a href="#features">Features</a>
          <a href="#pricing">Pricing</a>
          <router-link to="/about">About Us</router-link>
        </div>

        <div class="pb-footer-links-group">
          <strong>Contact</strong>
          <a href="mailto:support@primebalance.app">support@primebalance.app</a>
          <a href="tel:+212600000000">+212 6 00 00 00 00</a>
          <span class="pb-footer-contact-text">Casablanca, Morocco</span>
          <div class="pb-socials">
            <a href="#" aria-label="Twitter"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg></a>
            <a href="#" aria-label="GitHub"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg></a>
            <a href="#" aria-label="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>
          </div>
        </div>
      </div>
      <div class="pb-footer-bottom">
        © {{ new Date().getFullYear() }} PrimeBalance. All rights reserved.
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth.js';
const authStore = useAuthStore();
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.pb-wrap { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', system-ui, sans-serif; color: #1a1a2e; background: #ffffff; }

/* ── Navbar (3-col grid, identical to Landing) ─────────────── */
.pb-nav {
  padding: 0.9rem 2.5rem;
  background: #ffffff;
  border-bottom: 0.5px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.pb-nav-inner {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  width: 100%;
  gap: 1rem;
}

.pb-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 700;
  font-size: 18px;
  color: #1a1a2e;
  text-decoration: none;
  white-space: nowrap;
}

.pb-logo-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #4f46e5;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.pb-nav-links {
  display: flex;
  gap: 2rem;
  justify-content: center;
  align-items: center;
}

.pb-nav-links a {
  font-size: 15px;
  color: #64748b;
  text-decoration: none;
  transition: color 0.15s;
  white-space: nowrap;
}

.pb-nav-links a:hover {
  color: #1a1a2e;
}

.pb-active-link { color: #4f46e5 !important; font-weight: 600; }

.pb-nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-content: flex-end;
}

.btn-ghost {
  padding: 7px 16px;
  font-size: 14px;
  border: 0.5px solid #d1d5db;
  border-radius: 20px;
  background: transparent;
  color: #1a1a2e;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

.btn-primary {
  padding: 7px 18px;
  font-size: 14px;
  border: none;
  border-radius: 20px;
  background: #4f46e5;
  color: white;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

/* ── About Header ─────────────────────────────────────────── */
.pb-about-header {
  padding: 5rem 2.5rem;
  text-align: center;
  background: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
}

.pb-about-header h1 {
  font-size: 3rem;
  font-weight: 800;
  color: #0f0e2e;
  letter-spacing: -1px;
  margin-bottom: 1rem;
}

.pb-about-header p {
  font-size: 18px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
}

/* ── Main ─────────────────────────────────────────────────── */
.pb-about-main {
  max-width: 1400px;
  margin: 0 auto;
  padding: 4rem 2.5rem;
}

.pb-about-section {
  margin-bottom: 5rem;
}

.pb-about-img {
  width: 100%;
  height: auto;
  border-radius: 18px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  margin-bottom: 3rem;
}

.pb-about-text h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #1a1a2e;
  font-weight: 700;
}

.pb-about-text p {
  font-size: 16px;
  line-height: 1.8;
  color: #475569;
}

/* ── Values ───────────────────────────────────────────────── */
.pb-about-values { text-align: center; }

.pb-about-values h2 {
  font-size: 2rem;
  margin-bottom: 3rem;
  font-weight: 700;
  color: #1a1a2e;
}

.pb-values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  text-align: left;
}

.pb-value-card {
  padding: 2rem;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  transition: box-shadow 0.2s;
}

.pb-value-card:hover {
  box-shadow: 0 8px 30px rgba(79,70,229,0.1);
}

.pb-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
}

.pb-icon-indigo { background: #ede9fe; color: #4f46e5; }
.pb-icon-blue   { background: #dbeafe; color: #2563eb; }
.pb-icon-green  { background: #d1fae5; color: #059669; }

.pb-value-card h3 {
  font-size: 1.1rem;
  margin-bottom: 0.6rem;
  color: #1a1a2e;
  font-weight: 700;
}

.pb-value-card p {
  font-size: 14px;
  color: #64748b;
  line-height: 1.7;
}

/* ── Footer (identical to Landing) ────────────────────────── */
.pb-footer {
  padding: 4rem 2.5rem 1.5rem;
  background:
    radial-gradient(800px 320px at 10% 0%, rgba(79, 70, 229, 0.25), transparent 65%),
    #0f172a;
}
.pb-footer-content {
  max-width: 1400px;
  margin: 0 auto 3rem;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 2rem;
}
.pb-brand .pb-footer-icon {
  margin-bottom: 1rem;
}
.pb-footer-links-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.pb-footer-links-group strong {
  font-size: 15px;
  color: #f8fafc;
  margin-bottom: 4px;
}
.pb-footer-links-group a {
  color: #cbd5e1;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.15s;
}
.pb-footer-links-group a:hover {
  color: #ffffff;
}
.pb-footer-brand-name {
  font-size: 18px;
  font-weight: 700;
  color: #f8fafc;
}
.pb-footer-about {
  margin-top: 10px;
  color: #cbd5e1;
  font-size: 14px;
  line-height: 1.7;
  max-width: 320px;
}
.pb-footer-contact-text {
  color: #cbd5e1;
  font-size: 14px;
}
.pb-socials {
  display: flex;
  gap: 1rem;
  margin-top: 4px;
}
.pb-socials a {
  color: #cbd5e1;
}
.pb-socials a:hover {
  color: #ffffff;
}
.pb-footer-bottom {
  text-align: center;
  font-size: 12px;
  color: #94a3b8;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(148, 163, 184, 0.35);
  max-width: 1400px;
  margin: 0 auto;
}
</style>
