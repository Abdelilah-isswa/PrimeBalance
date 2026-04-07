<template>
  <div class="pb-wrap">

    <!-- Navbar -->
    <nav class="pb-nav">
      <div class="pb-nav-left">
        <div class="pb-logo">
          <div class="pb-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <span>PrimeBalance</span>
        </div>
        <div class="pb-nav-links">
          <a href="#">Home</a>
          <a href="#">Features</a>
          <a href="#">Overview</a>
          <a href="#">About</a>
        </div>
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
    </nav>

    <!-- Hero -->
    <section class="pb-hero-section">
      <div class="pb-hero-inner">

        <!-- Left copy -->
        <div class="pb-hero-copy">
          <div class="pb-badge">
            <span class="pb-badge-dot"></span>
            Trusted by 500+ businesses
          </div>
          <h1>Smart books,<br><span class="pb-accent">simple life</span></h1>
          <p class="pb-hero-sub">
            Track, plan, and grow your business finances with powerful tools designed for small businesses and sole traders.
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
            <div class="pb-stat-divider"></div>
            <div class="pb-stat">
              <div class="pb-stat-num">500+</div>
              <div class="pb-stat-label">financial institutions</div>
            </div>
          </div>
        </div>

        <!-- Right visual mockup -->
        <div class="pb-hero-visual">
          <div class="pb-visual-card pb-chart-card">
            <div class="pb-visual-card-top">
              <div>
                <div class="pb-visual-label">Net profit</div>
                <div class="pb-visual-value">$24,850</div>
                <div class="pb-visual-sub">April 2026</div>
              </div>
              <span class="pb-chip-green">+12.4%</span>
            </div>
            <div class="pb-bars">
              <div class="pb-bar" style="height:38px; background:#c7d2fe;"></div>
              <div class="pb-bar" style="height:52px; background:#818cf8;"></div>
              <div class="pb-bar" style="height:44px; background:#c7d2fe;"></div>
              <div class="pb-bar" style="height:68px; background:#4f46e5;"></div>
              <div class="pb-bar" style="height:48px; background:#c7d2fe;"></div>
              <div class="pb-bar" style="height:60px; background:#818cf8;"></div>
              <div class="pb-bar" style="height:76px; background:#4f46e5;"></div>
            </div>
          </div>

          <div class="pb-mini-card">
            <div class="pb-mini-icon" style="background:#d1fae5;">💰</div>
            <div class="pb-mini-info">
              <div class="pb-mini-title">Invoice paid</div>
              <div class="pb-mini-sub">Acme Corp · just now</div>
            </div>
            <div class="pb-mini-amount pb-green">+$3,200</div>
          </div>

          <div class="pb-mini-card">
            <div class="pb-mini-icon" style="background:#fef3c7;">📄</div>
            <div class="pb-mini-info">
              <div class="pb-mini-title">Invoice sent</div>
              <div class="pb-mini-sub">TechStart Ltd · 2h ago</div>
            </div>
            <div class="pb-mini-amount">$1,850</div>
          </div>
        </div>

      </div>
    </section>

    <!-- Features -->
    <section class="pb-features">
      <div class="pb-features-inner">
        <div class="pb-features-header">
          <h2>Everything you need</h2>
          <p>All the tools to run your business finances in one place.</p>
        </div>
        <div class="pb-features-grid">
          <div class="pb-feature-card">
            <div class="pb-feature-icon" style="background:#ede9fe;">🏢</div>
            <h3>Multi-company</h3>
            <p>Manage multiple companies from a single account. Switch between them instantly.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon" style="background:#dbeafe;">📄</div>
            <h3>Invoices & bills</h3>
            <p>Create and send invoices to clients. Track bills from suppliers. Download PDFs instantly.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon" style="background:#d1fae5;">💰</div>
            <h3>Transactions</h3>
            <p>Track income and expenses. Account balances update automatically with every payment.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon" style="background:#fef3c7;">📊</div>
            <h3>Dashboard</h3>
            <p>Clear overview of your finances — income, expenses, net profit, and unpaid invoices.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon" style="background:#fce7f3;">👥</div>
            <h3>Team management</h3>
            <p>Invite team members with different roles — owner, accountant, viewer, and more.</p>
          </div>
          <div class="pb-feature-card">
            <div class="pb-feature-icon" style="background:#ede9fe;">📧</div>
            <h3>Email notifications</h3>
            <p>Send invoices and team invitations directly by email with one click.</p>
          </div>
        </div>
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

    <!-- Footer -->
    <footer class="pb-footer">
      © {{ new Date().getFullYear() }} PrimeBalance. All rights reserved.
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
  font-family: 'Segoe UI', system-ui, sans-serif;
  color: #1a1a2e;
  background: #ffffff;
}

/* ── Navbar ───────────────────────────────────────────────── */
.pb-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.9rem 2.5rem;
  background: #ffffff;
  border-bottom: 0.5px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.pb-nav-left {
  display: flex;
  align-items: center;
  gap: 2.5rem;
}

.pb-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 15px;
  color: #1a1a2e;
  text-decoration: none;
}

.pb-logo-icon {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: #4f46e5;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pb-nav-links {
  display: flex;
  gap: 2rem;
}

.pb-nav-links a {
  font-size: 14px;
  color: #64748b;
  text-decoration: none;
  transition: color 0.15s;
}

.pb-nav-links a:hover {
  color: #1a1a2e;
}

.pb-nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
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
}
.btn-primary:hover { background: #4338ca; }

/* ── Hero ─────────────────────────────────────────────────── */
.pb-hero-section {
  padding: 5rem 2.5rem 0;
  background: #ffffff;
}

.pb-hero-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  gap: 4rem;
  max-width: 1100px;
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
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.15;
  color: #0f0e2e;
  margin-bottom: 1.2rem;
  letter-spacing: -1px;
}

.pb-accent { color: #4f46e5; }

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

/* Hero Visual */
.pb-hero-visual {
  background: linear-gradient(145deg, #f0effe 0%, #e8e4fd 100%);
  border-radius: 24px;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.pb-visual-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 1.25rem;
  border: 0.5px solid rgba(79,70,229,0.12);
}

.pb-visual-card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.pb-visual-label {
  font-size: 11px;
  color: #8b7ad1;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.pb-visual-value {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a2e;
}

.pb-visual-sub {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}

.pb-chip-green {
  background: #d1fae5;
  color: #065f46;
  font-size: 11px;
  padding: 3px 10px;
  border-radius: 12px;
  font-weight: 500;
}

.pb-bars {
  display: flex;
  gap: 6px;
  align-items: flex-end;
}

.pb-bar {
  flex: 1;
  border-radius: 4px 4px 0 0;
}

.pb-mini-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 0.85rem 1rem;
  border: 0.5px solid rgba(79,70,229,0.1);
  display: flex;
  align-items: center;
  gap: 10px;
}

.pb-mini-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.pb-mini-info { flex: 1; }

.pb-mini-title {
  font-size: 13px;
  font-weight: 500;
  color: #1a1a2e;
}

.pb-mini-sub {
  font-size: 11px;
  color: #94a3b8;
}

.pb-mini-amount {
  font-size: 13px;
  font-weight: 600;
  color: #1a1a2e;
}

.pb-green { color: #059669; }

/* ── Features ─────────────────────────────────────────────── */
.pb-features {
  padding: 5rem 2.5rem;
  background: #f8fafc;
  margin-top: 5rem;
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
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  font-size: 18px;
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
  padding: 1.5rem;
  text-align: center;
  font-size: 12px;
  color: #94a3b8;
  border-top: 0.5px solid #e2e8f0;
}

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
  .pb-hero-inner {
    grid-template-columns: 1fr;
    gap: 2.5rem;
    padding-bottom: 2rem;
  }

  .pb-hero-copy h1 { font-size: 2.2rem; }

  .pb-nav-links { display: none; }

  .pb-stats {
    flex-wrap: wrap;
    gap: 1.2rem;
  }

  .pb-stat-divider { display: none; }

  .pb-features-grid {
    grid-template-columns: 1fr;
  }
}
</style>