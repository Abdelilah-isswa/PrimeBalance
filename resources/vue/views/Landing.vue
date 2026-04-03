<template>
  <div>
    <!-- Navbar -->
    <nav style="background:#1a1a2e; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
      <span style="color:white; font-weight:700; font-size:1.2rem; letter-spacing:0.5px;">FilRouge</span>
      <div style="display:flex; gap:1rem;">
        <router-link to="/login"><button class="btn-secondary btn-sm">Login</button></router-link>
        <router-link to="/register"><button class="btn-sm">Get Started</button></router-link>
      </div>
    </nav>

    <!-- Hero -->
    <section style="background:linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%); color:white; padding:6rem 2rem; text-align:center;">
      <h1 style="font-size:3rem; font-weight:800; margin-bottom:1rem; color:white;">Manage Your Business Finances</h1>
      <p style="font-size:1.2rem; color:#a5b4fc; max-width:600px; margin:0 auto 2.5rem;">
        FilRouge helps you track invoices, bills, transactions, and clients — all in one place, for multiple companies.
      </p>
      <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <router-link to="/register"><button style="padding:0.85rem 2rem; font-size:1rem;">Start for Free</button></router-link>
        <router-link to="/login"><button class="btn-secondary" style="padding:0.85rem 2rem; font-size:1rem;">Login</button></router-link>
      </div>
    </section>

    <!-- Features -->
    <section style="padding:5rem 2rem; background:#f0f2f5;">
      <h2 style="text-align:center; font-size:2rem; font-weight:700; color:#1a1a2e; margin-bottom:3rem;">Everything You Need</h2>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:1.5rem; max-width:1100px; margin:0 auto;">
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">🏢</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Multi-Company</h3>
          <p style="color:#64748b; font-size:0.9rem;">Manage multiple companies from a single account. Switch between them instantly.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">📄</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Invoices & Bills</h3>
          <p style="color:#64748b; font-size:0.9rem;">Create and send invoices to clients. Track bills from suppliers. Download PDFs instantly.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">💰</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Transactions</h3>
          <p style="color:#64748b; font-size:0.9rem;">Track income and expenses. Account balances update automatically with every payment.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">📊</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Dashboard</h3>
          <p style="color:#64748b; font-size:0.9rem;">Get a clear overview of your finances — income, expenses, net profit, and unpaid invoices.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">👥</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Team Management</h3>
          <p style="color:#64748b; font-size:0.9rem;">Invite team members with different roles — owner, accountant, viewer, and more.</p>
        </div>
        <div class="card" style="padding:2rem; text-align:center;">
          <div style="font-size:2.5rem; margin-bottom:1rem;">📧</div>
          <h3 style="margin-bottom:0.5rem; color:#1a1a2e;">Email Notifications</h3>
          <p style="color:#64748b; font-size:0.9rem;">Send invoices and team invitations directly by email with one click.</p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section style="background:#1a1a2e; color:white; padding:5rem 2rem; text-align:center;">
      <h2 style="font-size:2rem; font-weight:700; color:white; margin-bottom:1rem;">Ready to get started?</h2>
      <p style="color:#a5b4fc; margin-bottom:2rem; font-size:1rem;">Create your free account and start managing your finances today.</p>
      <router-link to="/register"><button style="padding:0.85rem 2.5rem; font-size:1rem;">Create Free Account</button></router-link>
    </section>

    <!-- Footer -->
    <footer style="background:#0f0f1a; color:#64748b; text-align:center; padding:1.5rem; font-size:0.85rem;">
      © {{ new Date().getFullYear() }} FilRouge. All rights reserved.
    </footer>
  </div>
</template>

<script setup>
</script>
