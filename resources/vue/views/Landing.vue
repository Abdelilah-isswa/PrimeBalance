<template>
  <div class="pb-wrap">

    <!-- Navbar -->
    <nav class="pb-nav">
      <div class="pb-nav-inner">
        <!-- Logo -->
        <router-link to="/" class="pb-logo">
          <div class="pb-logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <span>PrimeBalance</span>
        </router-link>

        <!-- Center Links -->
        <div class="pb-nav-links">
          <router-link to="/" :class="{ 'pb-nav-link-active': activeNav === 'home' }" @click.prevent="goHome">Home</router-link>
          <a href="#features" :class="{ 'pb-nav-link-active': activeNav === 'features' }" @click.prevent="goToSection('features')">Features</a>
          <a href="#pricing" :class="{ 'pb-nav-link-active': activeNav === 'pricing' }" @click.prevent="goToSection('pricing')">Pricing</a>
          <router-link to="/about" :class="{ 'pb-nav-link-active': activeNav === 'about' }" @click="setActiveNav('about')">About Us</router-link>
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
            Built for modern finance teams
          </div>

          <h1>Money clarity for businesses that move fast.</h1>
          <p class="pb-hero-sub">
            PrimeBalance helps you manage invoices, bills, and transactions from one workspace so every decision is based on real numbers.
          </p>
          <div class="pb-hero-ctas">
            <template v-if="authStore.token">
              <router-link to="/dashboard"><button class="btn-hero-primary">Open Dashboard</button></router-link>
            </template>
            <template v-else>
              <router-link to="/register"><button class="btn-hero-primary">Get Started</button></router-link>
            </template>
          </div>
        </div>

        <div class="pb-hero-visual">
          <div class="pb-hero-visual-inner">
            <img
              src="/images/landing-hero.png"
              alt="PrimeBalance product dashboard preview"
              class="pb-hero-img"
            />
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
          <div class="pb-section-badge">
            <span class="pb-section-badge-dot"></span>
            Everything in one place
          </div>
          <h2>Everything you <span class="pb-outline">need</span></h2>
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

    <!-- Reviews -->
    <section class="pb-reviews">
      <div class="pb-reviews-inner">
        <div class="pb-reviews-header">
          <div class="pb-section-badge">
            <span class="pb-section-badge-dot"></span>
            Reviews
          </div>
          <h2>Loved by <span class="pb-outline">busy</span> teams</h2>
          <p>Founders and finance teams use PrimeBalance to stay on top of cash flow, bills, and reporting.</p>
        </div>

        <div class="pb-reviews-grid">
          <article class="pb-review-card">
            <p class="pb-review-quote">“PrimeBalance made our monthly close faster and way less stressful. Everything is clear at a glance.”</p>
            <div class="pb-review-footer">
              <img class="pb-review-avatar" src="https://i.pravatar.cc/80?img=32" alt="Reviewer avatar" loading="lazy" />
              <div class="pb-review-meta">
                <div class="pb-review-name">Sara A.</div>
                <div class="pb-review-role">Operations Lead</div>
              </div>
              <div class="pb-review-rating">★★★★★</div>
            </div>
          </article>

          <article class="pb-review-card">
            <p class="pb-review-quote">“Bills + transactions linking is exactly what we needed. It feels simple but still powerful.”</p>
            <div class="pb-review-footer">
              <img class="pb-review-avatar" src="https://i.pravatar.cc/80?img=12" alt="Reviewer avatar" loading="lazy" />
              <div class="pb-review-meta">
                <div class="pb-review-name">Mehdi K.</div>
                <div class="pb-review-role">Founder</div>
              </div>
              <div class="pb-review-rating">★★★★★</div>
            </div>
          </article>

          <article class="pb-review-card">
            <p class="pb-review-quote">“The dashboard gives us confidence. We can see what’s unpaid and what’s coming next week.”</p>
            <div class="pb-review-footer">
              <img class="pb-review-avatar" src="https://i.pravatar.cc/80?img=47" alt="Reviewer avatar" loading="lazy" />
              <div class="pb-review-meta">
                <div class="pb-review-name">Lina N.</div>
                <div class="pb-review-role">Accountant</div>
              </div>
              <div class="pb-review-rating">★★★★★</div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Additional Real Image Banner -->
    <section class="pb-image-banner">
      <div class="pb-banner-inner">
        <div class="pb-banner-text">
          <div class="pb-section-badge">
            <span class="pb-section-badge-dot"></span>
            Built for growth
          </div>
          <h2>Focus on <span class="pb-outline">scale</span>. We'll handle the numbers.</h2>
          <p>Join thousands of growing startups tracking everything flawlessly in PrimeBalance.</p>
        </div>
        <div class="pb-banner-visual">
          <div class="pb-banner-visual-inner">
            <img class="pb-banner-img" src="/images/scale-banner.jpg" alt="PrimeBalance on tablet" />
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="pb-pricing">
      <div class="pb-pricing-inner">
        <div class="pb-pricing-header">
          <div class="pb-section-badge">
            <span class="pb-section-badge-dot"></span>
            Pricing
          </div>
          <h2>Flexible Plans for Every Need</h2>
          <p>Whether you're a solo founder or managing a team, choose the plan that fits your workflow.</p>
        </div>

        <div class="pb-pricing-grid">
          <article class="pb-price-card">
            <h3>Free Plan</h3>
            <p class="pb-price-desc">For daily tracking and lightweight finance management.</p>
            <div class="pb-price-row"><span class="pb-price">$0</span><span class="pb-price-period">/month</span></div>
            <ul class="pb-price-features">
              <li>Secure storage and transfers</li>
              <li>Real-time balance tracking</li>
              <li>Basic market insights</li>
              <li>Core AI support</li>
            </ul>
            <router-link to="/register"><button class="pb-price-btn">Get started</button></router-link>
          </article>

          <article class="pb-price-card pb-price-card-featured">
            <h3>Pro + AI</h3>
            <p class="pb-price-desc">For growing teams who need smarter automation and insights.</p>
            <div class="pb-price-row"><span class="pb-price">$23</span><span class="pb-price-period">/month</span></div>
            <ul class="pb-price-features">
              <li>Everything in Free</li>
              <li>Smart AI assistant</li>
              <li>Personalized recommendations</li>
              <li>Multi-currency support</li>
            </ul>
            <router-link to="/register"><button class="pb-price-btn pb-price-btn-featured">Get started</button></router-link>
          </article>

          <article class="pb-price-card">
            <h3>Business Plan</h3>
            <p class="pb-price-desc">For companies with multi-user access and advanced reporting needs.</p>
            <div class="pb-price-row"><span class="pb-price">$36</span><span class="pb-price-period">/month</span></div>
            <ul class="pb-price-features">
              <li>Everything in Pro + AI</li>
              <li>Role-based team access</li>
              <li>Shared workspaces</li>
              <li>Exportable financial reports</li>
            </ul>
            <router-link to="/register"><button class="pb-price-btn">Get started</button></router-link>
          </article>
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
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const activeNav = ref('home');

const setActiveNav = (section) => {
  activeNav.value = section;
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const goHome = async () => {
  setActiveNav('home');

  if (route.path !== '/') {
    await router.push('/');
  }

  if (window.location.hash) {
    history.replaceState(null, '', window.location.pathname + window.location.search);
  }

  scrollToTop();
};

const goToSection = async (section) => {
  setActiveNav(section);

  if (route.path !== '/') {
    await router.push('/');
  }

  const el = document.getElementById(section);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  history.replaceState(null, '', `#${section}`);
};

const syncActiveNavFromHash = () => {
  if (route.path === '/about') {
    activeNav.value = 'about';
    return;
  }

  if (window.location.hash === '#features') {
    activeNav.value = 'features';
    return;
  }

  if (window.location.hash === '#pricing') {
    activeNav.value = 'pricing';
    return;
  }

  activeNav.value = 'home';
};

onMounted(() => {
  syncActiveNavFromHash();
  window.addEventListener('hashchange', syncActiveNavFromHash);
  window.addEventListener('popstate', syncActiveNavFromHash);
});

onBeforeUnmount(() => {
  window.removeEventListener('hashchange', syncActiveNavFromHash);
  window.removeEventListener('popstate', syncActiveNavFromHash);
});
</script>

<style scoped>
/* ── Reset & Base ─────────────────────────────────────────── */
* { box-sizing: border-box; margin: 0; padding: 0; }

.pb-wrap {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', system-ui, sans-serif;
  color: #1a1a2e;
  background: #ffffff;
  font-size: 16px;
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
  gap: 10px;
  font-weight: 700;
  font-size: 18px;
  letter-spacing: -0.3px;
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

.pb-nav-links a.pb-nav-link-active {
  color: #4f46e5;
  font-weight: 600;
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
  font-size: 14px;
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
  font-size: 14px;
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
  padding: 0 0 0 2.5rem;
  background:
    radial-gradient(800px 500px at 18% 10%, rgba(79, 70, 229, 0.1), transparent 65%),
    radial-gradient(900px 600px at 85% 70%, rgba(59, 130, 246, 0.08), transparent 70%),
    #f5f7fb;
  position: relative;
  overflow: hidden;
  min-height: calc(100vh - 72px);
}

.pb-hero-inner {
  display: grid;
  grid-template-columns: minmax(360px, 43%) 57%;
  align-items: center;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
  min-height: calc(100vh - 72px);
}

.pb-hero-copy {
  padding-top: 2.5rem;
  padding-bottom: 2.5rem;
}

.pb-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ede9fe;
  color: #4f46e5;
  font-size: 13px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 20px;
  margin-bottom: 1rem;
}

.pb-badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #4f46e5;
  display: inline-block;
}

.pb-hero-copy h1 {
  font-size: clamp(2.1rem, 4.8vw, 3.8rem);
  font-weight: 800;
  line-height: 1.05;
  color: #0f172a;
  margin-bottom: 1rem;
  letter-spacing: -0.9px;
  max-width: 560px;
}

.pb-accent { color: #4f46e5; }

.pb-outline {
  display: inline-block;
  margin: 0 10px;
  color: transparent;
  -webkit-text-stroke: 1.25px #4f46e5;
}

.pb-hero-sub {
  font-size: 17px;
  color: #475569;
  line-height: 1.75;
  margin-bottom: 1.6rem;
  max-width: 520px;
}

.pb-hero-ctas {
  display: flex;
  gap: 10px;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.btn-hero-primary {
  padding: 12px 26px;
  font-size: 15px;
  border: none;
  border-radius: 24px;
  background: #4f46e5;
  color: #ffffff;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.15s, transform 0.12s;
}
.btn-hero-primary:hover { background: #4338ca; transform: translateY(-1px); }

.btn-hero-ghost {
  padding: 12px 26px;
  font-size: 15px;
  border: 1px solid #cbd5e1;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.7);
  color: #0f172a;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-hero-ghost:hover { background: #ffffff; }

.pb-stats {
  display: flex;
  gap: 1.4rem;
  align-items: center;
}

.pb-stat-num {
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
}

.pb-stat-label {
  font-size: 13px;
  color: #64748b;
}

.pb-stat-divider {
  width: 1px;
  height: 34px;
  background: #dbe2ea;
}

/* Hero Visual Real Image */
.pb-hero-visual {
  position: relative;
  display: flex;
  justify-content: stretch;
  width: 100%;
  height: 100%;
  min-height: calc(100vh - 72px);
}

.pb-hero-visual-inner {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0;
  background: transparent;
  border: none;
  overflow: hidden;
  box-shadow: none;
  transform: none;
}

.pb-hero-img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  object-position: 45% center;
}

.pb-hero-float {
  position: absolute;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid #dbe2ea;
  border-radius: 14px;
  padding: 9px 12px;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
}

.pb-hero-float-top {
  right: 16px;
  top: 16px;
}

.pb-hero-float-bottom {
  left: 16px;
  bottom: 16px;
}

.pb-hero-float-label {
  font-size: 11px;
  color: #64748b;
  font-weight: 600;
  margin-bottom: 2px;
}

.pb-hero-float-metric {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
}

/* ── Trusted By ─────────────────────────────────────────────  */
.pb-trusted-by {
  padding: 4rem 2.5rem;
  text-align: center;
  background: #ffffff;
  margin-top: 5rem;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
}
.pb-trusted-label {
  font-size: 13px;
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

.pb-section-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(237,233,254,0.9);
  color: #4f46e5;
  font-size: 12px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 20px;
  margin-bottom: 1rem;
  border: 0.5px solid #e2e8f0;
}

.pb-section-badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #4f46e5;
  display: inline-block;
}

.pb-features-header h2 {
  font-size: 2.6rem;
  font-weight: 700;
  color: #0f0e2e;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.pb-features-header p {
  font-size: 17px;
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
  transition: box-shadow 0.2s, transform 0.12s, border-color 0.2s;
}

.pb-feature-card:hover {
  box-shadow: 0 8px 24px rgba(79,70,229,0.10);
  transform: translateY(-2px);
  border-color: rgba(79,70,229,0.35);
}

.pb-feature-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

/* ── Reviews ─────────────────────────────────────────────── */
.pb-reviews {
  padding: 5rem 2.5rem;
  background: #ffffff;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
}

.pb-reviews-inner {
  max-width: 1100px;
  margin: 0 auto;
}

.pb-reviews-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.pb-reviews-header h2 {
  font-size: 2.6rem;
  font-weight: 700;
  color: #0f0e2e;
  margin-bottom: 0.6rem;
  letter-spacing: -0.5px;
}

.pb-reviews-header p {
  font-size: 17px;
  color: #64748b;
  max-width: 640px;
  margin: 0 auto;
  line-height: 1.7;
}

.pb-reviews-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.pb-review-card {
  background: #ffffff;
  border-radius: 18px;
  border: 0.5px solid #e2e8f0;
  padding: 1.5rem;
  box-shadow: 0 10px 26px rgba(15,14,46,0.05);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 190px;
}

.pb-review-quote {
  font-size: 16px;
  color: #1a1a2e;
  line-height: 1.7;
}

.pb-review-footer {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 1.25rem;
}

.pb-review-avatar {
  width: 36px;
  height: 36px;
  border-radius: 999px;
  background: #ede9fe;
  border: 0.5px solid #e2e8f0;
  object-fit: cover;
  flex-shrink: 0;
}

.pb-review-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.pb-review-name {
  font-size: 14px;
  font-weight: 700;
  color: #0f0e2e;
}

.pb-review-role {
  font-size: 13px;
  color: #64748b;
}

.pb-review-rating {
  margin-left: auto;
  font-size: 12px;
  letter-spacing: 1px;
  color: #4f46e5;
  white-space: nowrap;
}

.pb-feature-card h3 {
  font-size: 17px;
  font-weight: 600;
  color: #1a1a2e;
  margin-bottom: 0.4rem;
}

.pb-feature-card p {
  font-size: 15px;
  color: #64748b;
  line-height: 1.6;
}

/* Image Banner */
.pb-image-banner {
  padding: 5rem 2.5rem;
  background: #ffffff;
}
.pb-banner-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  align-items: center;
  gap: 3rem;
  background: white;
  padding: 3rem;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  border: 0.5px solid #e2e8f0;
}
.pb-banner-text h2 {
  font-size: 2.6rem;
  margin-bottom: 1rem;
  color: #1a1a2e;
  letter-spacing: -0.5px;
}
.pb-banner-text p {
  color: #64748b;
  font-size: 17px;
  line-height: 1.6;
}

.pb-banner-visual-inner {
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 18px 44px rgba(15,14,46,0.12);
  background: #f8fafc;
}

.pb-banner-img {
  width: 100%;
  border-radius: 0;
  box-shadow: none;
  display: block;
  height: 320px;
  object-fit: cover;
}

/* Pricing */
.pb-pricing {
  padding: 5rem 2.5rem;
  background: #ffffff;
  border-top: 1px solid #f1f5f9;
}

.pb-pricing-inner {
  max-width: 1100px;
  margin: 0 auto;
}

.pb-pricing-header {
  text-align: center;
  margin-bottom: 2rem;
}

.pb-pricing-header h2 {
  font-size: 2.6rem;
  font-weight: 700;
  color: #0f0e2e;
  margin-bottom: 0.7rem;
  letter-spacing: -0.5px;
}

.pb-pricing-header p {
  font-size: 17px;
  color: #64748b;
  max-width: 660px;
  margin: 0 auto;
  line-height: 1.7;
}

.pb-pricing-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.pb-price-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
}

.pb-price-card h3 {
  font-size: 1.7rem;
  color: #0f172a;
  margin-bottom: 0.35rem;
}

.pb-price-desc {
  font-size: 14px;
  line-height: 1.6;
  color: #64748b;
  margin-bottom: 1rem;
  min-height: 44px;
}

.pb-price-row {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin-bottom: 1rem;
}

.pb-price {
  font-size: 3rem;
  line-height: 1;
  font-weight: 800;
  color: #0f172a;
}

.pb-price-period {
  font-size: 28px;
  color: #0f172a;
  font-weight: 600;
}

.pb-price-features {
  list-style: none;
  margin: 0 0 1.2rem;
  padding: 0;
  border-top: 1px solid #edf2f7;
  padding-top: 1rem;
  display: grid;
  gap: 0.65rem;
}

.pb-price-features li {
  position: relative;
  padding-left: 1.6rem;
  color: #334155;
  font-size: 15px;
  line-height: 1.5;
}

.pb-price-features li::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0.34rem;
  width: 14px;
  height: 14px;
  border-radius: 999px;
  background: #0f172a;
}

.pb-price-btn {
  margin-top: auto;
  width: 100%;
  border-radius: 999px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #0f172a;
  font-size: 14px;
  font-weight: 600;
  padding: 12px 14px;
  cursor: pointer;
}

.pb-price-card-featured {
  background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
  border-color: #bfdbfe;
}

.pb-price-btn-featured {
  background: #ffffff;
}

/* ── CTA ──────────────────────────────────────────────────── */
.pb-cta {
  padding: 5rem 2.5rem;
  text-align: center;
  background: #ffffff;
}

.pb-cta h2 {
  font-size: 2.6rem;
  font-weight: 700;
  color: #0f0e2e;
  margin-bottom: 0.75rem;
  letter-spacing: -0.5px;
}

.pb-cta p {
  font-size: 17px;
  color: #64748b;
  margin-bottom: 2rem;
}

/* ── Footer ───────────────────────────────────────────────── */
.pb-footer {
  padding: 4rem 2.5rem 1.5rem;
  background: #ffffff;
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
  font-size: 15px;
  color: #1e293b;
  margin-bottom: 4px;
}
.pb-footer-links-group a {
  color: #64748b;
  text-decoration: none;
  font-size: 14px;
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

  .pb-hero-inner {
    min-height: auto;
    gap: 1.5rem;
    max-width: none;
  }

  .pb-hero-visual {
    justify-content: center;
    min-height: auto;
    height: auto;
  }

  .pb-hero-visual-inner {
    transform: none;
    border-radius: 18px;
    height: auto;
  }

  .pb-hero-section {
    min-height: auto;
    padding: 3rem 1rem 1.5rem;
  }

  .pb-hero-copy {
    padding-top: 0;
    padding-bottom: 0;
  }

  .pb-hero-img {
    height: auto;
    object-position: center;
  }

  .pb-banner-inner {
    text-align: center;
  }

  .pb-hero-copy h1 { font-size: 2.5rem; }

  .pb-outline {
    margin: 0 6px;
    -webkit-text-stroke: 1.1px #4f46e5;
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

  .pb-reviews-grid {
    grid-template-columns: 1fr;
  }

  .pb-pricing-grid {
    grid-template-columns: 1fr;
  }
  
  .pb-footer-content {
    grid-template-columns: 1fr;
  }
}
</style>