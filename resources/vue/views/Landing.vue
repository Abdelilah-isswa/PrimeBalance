<template>
  <div class="pb-wrap">

    <!-- Navbar -->
    <nav class="pb-nav">
      <div class="pb-nav-inner">
        <!-- Logo -->
        <router-link to="/" class="pb-logo">
          <div class="pb-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" width="14" height="14">
              <path d="M3 12h2l2-5 3 8 2-5 2 5 2-5 2 5h2" stroke="white" fill="none"/>
            </svg>
          </div>
          <span>PrimeBalance</span>
        </router-link>

        <!-- Center Links -->
        <div class="pb-nav-links">
          <router-link to="/">Home</router-link>
          <a href="#features">Features</a>
          <router-link to="/about">About Us</router-link>
        </div>

        <!-- Right Actions -->
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

    <!-- Hero -->
    <section class="pb-hero-section">
      <div class="pb-hero-inner">

        <div class="pb-hero-copy">
          <div class="pb-badge">
            <span class="pb-badge-dot"></span>
            Next‑gen business accounting
          </div>

          <h1>
            Smart accounting for a
            <span class="pb-outline">smarter</span>
            <span class="pb-accent">future</span>
          </h1>
          <p class="pb-hero-sub">
            Track, plan, and grow your business finances with powerful tools designed exclusively for modern businesses and ambitious founders.
          </p>
          <div class="pb-hero-ctas">
            <template v-if="authStore.token">
              <router-link to="/dashboard"><button class="btn-hero-primary">Go to Dashboard</button></router-link>
            </template>
            <template v-else>
              <router-link to="/register"><button class="btn-hero-primary">Start for free</button></router-link>
              <router-link to="/login"><button class="btn-hero-ghost">Login</button></router-link>
            </template>
          </div>
          <div class="pb-stats">
            <div class="pb-stat">
              <div class="pb-stat-num">$1.2B</div>
              <div class="pb-stat-label">managed through platform</div>
            </div>
            <div class="pb-stat-divider"></div>
            <div class="pb-stat">
              <div class="pb-stat-num">98%</div>
              <div class="pb-stat-label">customer satisfaction</div>
            </div>
          </div>
        </div>

        <div class="pb-hero-visual">
          <div class="pb-hero-visual-inner">
            <img
              src="/images/landing-hero.png"
              alt="PrimeBalance preview"
              class="pb-hero-img"
            />
            <div class="pb-hero-float">
              <div class="pb-hero-float-label">Trusted by teams worldwide</div>
              <div class="pb-hero-float-metric">5,000+</div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- Trusted By -->
    <section class="pb-trusted-by">
      <p class="pb-trusted-label">TRUSTED BY INNOVATIVE COMPANIES WORLDWIDE</p>
      <div class="pb-trusted-logos">
        <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe" class="pb-trusted-logo" />
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/0e/Shopify_logo_2018.svg" alt="Shopify" class="pb-trusted-logo" />
        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b9/Slack_Technologies_Logo.svg" alt="Slack" class="pb-trusted-logo" />
        <img src="https://upload.wikimedia.org/wikipedia/commons/3/33/Figma-logo.svg" alt="Figma" class="pb-trusted-logo" />
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/93/Amazon_Web_Services_Logo.svg" alt="AWS" class="pb-trusted-logo" />
       
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="pb-features">
      <div class="pb-features-inner">
        <div class="pb-features-header">
          <h2>Everything you need</h2>
          <p>All the tools to run your business finances in one place.</p>
        </div>
        <div class="pb-features-grid">
          <div class="pb-feature-card">
            <div class="pb-feature-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            <h3>Multi-company</h3>
            <p>Manage multiple companies from a single account. Switch between them instantly.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h3>Invoices & bills</h3>
            <p>Create and send invoices to clients. Track bills from suppliers. Download PDFs instantly.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <h3>Transactions</h3>
            <p>Track income and expenses. Account balances update automatically with every payment.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            </div>
            <h3>Dashboard</h3>
            <p>Clear overview of your finances — income, expenses, net profit, and unpaid invoices.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#db2777" stroke-width="1.5"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <h3>Team management</h3>
            <p>Invite team members with different roles — owner, accountant, viewer, and more.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h3>Email notifications</h3>
            <p>Send invoices and team invitations directly by email with one click.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Additional Real Image Banner -->
    <section class="pb-image-banner">
      <div class="pb-banner-inner">
        <div class="pb-banner-text">
          <h2>Focus on scale. We'll handle the numbers.</h2>
          <p>Join thousands of growing startups tracking everything flawlessly in PrimeBalance.</p>
        </div>
        <img class="pb-banner-img" src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Team working" />
      </div>
    </section>

    <!-- CTA -->
    <section class="pb-cta">
      <h2>Ready to get started?</h2>
      <p>Create your free account and start managing your finances today.</p>
      <router-link v-if="authStore.token" to="/dashboard">
        <button class="btn-hero-primary">Go to Dashboard</button>
      </router-link>
      <router-link v-else to="/register">
        <button class="btn-hero-primary">Create free account</button>
      </router-link>
    </section>

    <!-- Footer with Links and Socials -->
    <footer class="pb-footer">
      <div class="pb-footer-content">
        <div class="pb-brand">
          <div class="pb-logo-icon pb-footer-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" width="14" height="14">
              <path d="M3 12h2l2-5 3 8 2-5 2 5 2-5 2 5h2" stroke="white" fill="none"/>
            </svg>
          </div>
          <span style="font-size: 15px; font-weight: 700; color: #1a1a2e;">PrimeBalance</span>
          <p style="margin-top: 10px; color: #64748b; font-size: 13px;">Making accounting accessible for modern companies.</p>
        </div>
        
        <div class="pb-footer-links-group">
          <strong>Product</strong>
          <a href="#features">Features</a>
          <router-link to="/about">Pricing</router-link>
          <router-link to="/about">About Us</router-link>
        </div>

        <div class="pb-footer-links-group">
          <strong>Connect</strong>
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
/* ── Reset & Base ─────────────────────────────────────────── */
* { box-sizing: border-box; margin: 0; padding: 0; }

.pb-wrap {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', system-ui, sans-serif;
  color: #1a1a2e;
  background: #ffffff;
}

/* ── Navbar ───────────────────────────────────────────────── */
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
  gap: 8px;
  font-weight: 600;
  font-size: 15px;
  color: #1a1a2e;
  text-decoration: none;
  white-space: nowrap;
}

.pb-logo-icon {
  width: 28px;
  height: 28px;
  border-radius: 6px;
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
  font-size: 14px;
  color: #64748b;
  text-decoration: none;
  transition: color 0.15s;
  white-space: nowrap;
}

.pb-nav-links a:hover, .pb-nav-links a.router-link-active {
  color: #1a1a2e;
}

.pb-nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-content: flex-end;
}

/* Buttons */
.btn-ghost {
  padding: 7px 16px;
  font-size: 13px;
  border: 0.5px solid #d1d5db;
  border-radius: 20px;
  background: transparent;
  color: #1a1a2e;
  cursor: pointer;
  transition: background 0.15s;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}
.btn-ghost:hover { background: #f8fafc; }

.btn-primary {
  padding: 7px 18px;
  font-size: 13px;
  border: none;
  border-radius: 20px;
  background: #4f46e5;
  color: #ffffff;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.15s;
  text-decoration: none;
}
.btn-primary:hover { background: #4338ca; }

/* ── Hero ─────────────────────────────────────────────────── */
.pb-hero-section {
  padding: 5.25rem 2.5rem 0;
  background: radial-gradient(900px 520px at 70% 20%, rgba(79,70,229,0.16), rgba(255,255,255,0) 60%),
              radial-gradient(700px 420px at 25% 55%, rgba(237,233,254,1), rgba(255,255,255,0) 65%),
              #ffffff;
  position: relative;
  overflow: hidden;
}

.pb-hero-inner {
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  align-items: center;
  gap: 4rem;
  max-width: 1180px;
  margin: 0 auto;
}

.pb-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ede9fe;
  color: #4f46e5;
  font-size: 12px;
  font-weight: 500;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 1.5rem;
}

.pb-badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #4f46e5;
  display: inline-block;
}

.pb-hero-copy h1 {
  font-size: 3.15rem;
  font-weight: 700;
  line-height: 1.08;
  color: #0f0e2e;
  margin-bottom: 1.2rem;
  letter-spacing: -1px;
}

.pb-accent { color: #4f46e5; }

.pb-outline {
  display: inline-block;
  margin: 0 10px;
  color: transparent;
  -webkit-text-stroke: 1.25px #4f46e5;
  text-stroke: 1.25px #4f46e5;
}

.pb-hero-sub {
  font-size: 15px;
  color: #64748b;
  line-height: 1.7;
  margin-bottom: 2rem;
  max-width: 420px;
}

.pb-hero-ctas {
  display: flex;
  gap: 10px;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.btn-hero-primary {
  padding: 12px 26px;
  font-size: 14px;
  border: none;
  border-radius: 24px;
  background: #4f46e5;
  color: #ffffff;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.15s, transform 0.1s;
}
.btn-hero-primary:hover { background: #4338ca; transform: translateY(-1px); }

.btn-hero-ghost {
  padding: 12px 26px;
  font-size: 14px;
  border: 0.5px solid #d1d5db;
  border-radius: 24px;
  background: transparent;
  color: #1a1a2e;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-hero-ghost:hover { background: #f8fafc; }

.pb-stats {
  display: flex;
  gap: 2rem;
  align-items: center;
}

.pb-stat-num {
  font-size: 22px;
  font-weight: 700;
  color: #0f0e2e;
}

.pb-stat-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.pb-stat-divider {
  width: 0.5px;
  height: 36px;
  background: #e2e8f0;
}

/* Hero Visual Real Image */
.pb-hero-visual {
  transform: translateY(-18px);
}

.pb-hero-visual-inner {
  position: relative;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 18px 44px rgba(15,14,46,0.12);
  background: #f8fafc;
}

.pb-hero-img {
  width: 100%;
  height: auto;
  display: block;
}

.pb-hero-float {
  position: absolute;
  right: 14px;
  bottom: 14px;
  background: rgba(255,255,255,0.92);
  border: 0.5px solid #e2e8f0;
  backdrop-filter: blur(8px);
  padding: 10px 12px;
  border-radius: 16px;
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.pb-hero-float-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 600;
  white-space: nowrap;
}

.pb-hero-float-metric {
  font-size: 18px;
  font-weight: 800;
  color: #0f0e2e;
}

/* ── Trusted By ─────────────────────────────────────────────  */
.pb-trusted-by {
  padding: 4rem 2.5rem;
  text-align: center;
  background: #f8fafc;
  margin-top: 5rem;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
}
.pb-trusted-label {
  font-size: 12px;
  font-weight: 700;
  color: #94a3b8;
  margin-bottom: 2rem;
  letter-spacing: 1px;
}
.pb-trusted-logos {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 3.5rem;
}
.pb-trusted-logo {
  height: 32px;
  max-width: 140px;
  object-fit: contain;
  filter: grayscale(100%) opacity(0.45);
  transition: filter 0.25s, opacity 0.25s;
}
.pb-trusted-logo:hover {
  filter: grayscale(0%) opacity(1);
}
.pb-trusted-logo:hover {
  filter: grayscale(0%) opacity(1);
}

/* ── Features ─────────────────────────────────────────────── */
.pb-features {
  padding: 5rem 2.5rem;
  background: #ffffff;
}

.pb-features-inner {
  max-width: 1100px;
  margin: 0 auto;
}

.pb-features-header {
  text-align: center;
  margin-bottom: 3rem;
}

.pb-features-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #0f0e2e;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.pb-features-header p {
  font-size: 15px;
  color: #64748b;
}

.pb-features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;
}

.pb-feature-card {
  background: #ffffff;
  border-radius: 16px;
  border: 0.5px solid #e2e8f0;
  padding: 1.5rem;
  transition: box-shadow 0.2s;
}

.pb-feature-card:hover {
  box-shadow: 0 4px 20px rgba(79,70,229,0.08);
}

.pb-feature-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.pb-feature-card h3 {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 0.4rem;
}

.pb-feature-card p {
  font-size: 13px;
  color: #64748b;
  line-height: 1.6;
}

/* Image Banner */
.pb-image-banner {
  padding: 5rem 2.5rem;
  background: #f8fafc;
}
.pb-banner-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 3rem;
  background: white;
  padding: 3rem;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
.pb-banner-text h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #1a1a2e;
}
.pb-banner-text p {
  color: #64748b;
  font-size: 15px;
  line-height: 1.6;
}
.pb-banner-img {
  width: 100%;
  max-width: 400px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* ── CTA ──────────────────────────────────────────────────── */
.pb-cta {
  padding: 5rem 2.5rem;
  text-align: center;
  background: #ffffff;
}

.pb-cta h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #0f0e2e;
  margin-bottom: 0.75rem;
  letter-spacing: -0.5px;
}

.pb-cta p {
  font-size: 15px;
  color: #64748b;
  margin-bottom: 2rem;
}

/* ── Footer ───────────────────────────────────────────────── */
.pb-footer {
  padding: 4rem 2.5rem 1.5rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}
.pb-footer-content {
  max-width: 1100px;
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
  font-size: 14px;
  color: #1e293b;
  margin-bottom: 4px;
}
.pb-footer-links-group a {
  color: #64748b;
  text-decoration: none;
  font-size: 13px;
  transition: color 0.15s;
}
.pb-footer-links-group a:hover {
  color: #4f46e5;
}
.pb-socials {
  display: flex;
  gap: 1rem;
}
.pb-socials a {
  color: #94a3b8;
}
.pb-socials a:hover {
  color: #4f46e5;
}
.pb-footer-bottom {
  text-align: center;
  font-size: 12px;
  color: #94a3b8;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
  max-width: 1100px;
  margin: 0 auto;
}

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
  .pb-hero-inner, .pb-banner-inner {
    grid-template-columns: 1fr;
    gap: 2.5rem;
    padding-bottom: 2rem;
  }
  .pb-banner-inner {
    flex-direction: column;
    text-align: center;
  }

  .pb-hero-copy h1 { font-size: 2.25rem; }

  .pb-outline {
    margin: 0 6px;
    -webkit-text-stroke: 1.1px #4f46e5;
    text-stroke: 1.1px #4f46e5;
  }

  .pb-nav-links { display: none; }

  .pb-stats {
    flex-wrap: wrap;
    gap: 1.2rem;
  }

  .pb-stat-divider { display: none; }

  .pb-features-grid {
    grid-template-columns: 1fr;
  }
  
  .pb-footer-content {
    grid-template-columns: 1fr;
  }
}
</style>