<template>
  <div class="pb-team-page">
    <div class="pb-page-header">
      <div class="pb-header-content">
        <h1 class="pb-page-title">Team Management</h1>
        <p class="pb-page-subtitle">Manage company members, roles, and pending invitations.</p>
        
        <div class="pb-tabs">
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'members' }"
            @click="activeTab = 'members'"
          >
            Team Members
            <span class="pb-tab-badge">{{ members.length }}</span>
          </button>
          <button 
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'invitations' }"
            @click="activeTab = 'invitations'"
          >
            Invitations
            <span class="pb-tab-badge">{{ invitations.length }}</span>
          </button>
          <button 
            v-if="canInvite"
            class="pb-tab-btn" 
            :class="{ 'pb-tab-btn--active': activeTab === 'invite' }"
            @click="activeTab = 'invite'"
          >
            Send Invite
            <span class="pb-tab-badge">+</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tab: Members -->
    <div v-if="activeTab === 'members'" class="pb-tab-content anim-fade-in">
      <div class="pb-card">
        <div class="pb-table-wrapper">
          <table class="pb-table">
            <thead>
              <tr>
                <th>Member Name</th>
                <th>Email</th>
                <th>Role</th>
                <th class="pb-text-center">Joined</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="member in members" :key="member.id">
                <td class="pb-font-medium">{{ member.name }}</td>
                <td>{{ member.email }}</td>
                <td>
                  <template v-if="editRoleId === member.id">
                    <select v-model="editRoleValue" class="pb-input pb-input-sm" style="min-width: 130px;">
                      <option value="admin">Admin</option>
                      <option value="accountant">Accountant</option>
                      <option value="viewer">Viewer</option>
                    </select>
                  </template>
                  <template v-else>
                    <span :class="['pb-status-pill', getRoleBadgeClass(member.pivot?.role)]">
                      {{ member.pivot?.role }}
                    </span>
                  </template>
                </td>
                <td class="pb-text-center">{{ formatDate(member.pivot?.created_at) }}</td>
                <td class="pb-text-center">
                  <div class="pb-action-group" v-if="canManageTeam && member.id !== authStore.user?.id && member.pivot?.role !== 'owner'">
                    <template v-if="editRoleId === member.id">
                      <button @click="saveRole(member.id)" class="pb-btn-icon pb-icon-success" title="Save Role" :disabled="loading">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                      </button>
                      <button @click="editRoleId = null" class="pb-btn-icon pb-icon-neutral" title="Cancel" :disabled="loading">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      </button>
                    </template>
                    <template v-else>
                      <button @click="startRoleEdit(member)" class="pb-btn-icon pb-icon-primary" title="Change Role" :disabled="loading">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                      </button>
                      <button @click="removeMember(member.id)" class="pb-btn-icon pb-icon-danger" title="Remove Member" :disabled="loading">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                      </button>
                    </template>
                  </div>
                  <div v-else-if="member.id === authStore.user?.id" class="pb-text-muted">
                    <small>You</small>
                  </div>
                  <div v-else class="pb-text-muted">
                    <small>—</small>
                  </div>
                </td>
              </tr>
              <tr v-if="members.length === 0">
                <td colspan="5" class="pb-empty-row">No members found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab: Invitations -->
    <div v-if="activeTab === 'invitations'" class="pb-tab-content anim-fade-in">
      <div class="pb-card">
        <div class="pb-table-wrapper">
          <table class="pb-table">
            <thead>
              <tr>
                <th>Invitee Email</th>
                <th>Assigned Role</th>
                <th>Status</th>
                <th>Sent At</th>
                <th class="pb-text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inv in invitations" :key="inv.id">
                <td class="pb-font-medium">{{ inv.email }}</td>
                <td>
                  <span :class="['pb-status-pill', getRoleBadgeClass(inv.role)]">{{ inv.role }}</span>
                </td>
                <td>
                  <span :class="['pb-status-pill', inv.status === 'pending' ? 'pb-status--active' : 'pb-status--inactive']">
                    {{ inv.status }}
                  </span>
                </td>
                <td>{{ formatDate(inv.created_at) }}</td>
                <td class="pb-text-center">
                  <div class="pb-action-group">
                    <button v-if="canManageTeam" @click="revokeInvitation(inv.id)" class="pb-btn-icon pb-icon-danger" title="Revoke Invitation" :disabled="loading">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="invitations.length === 0">
                <td colspan="5" class="pb-empty-row">No pending invitations.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab: Invite -->
    <div v-if="activeTab === 'invite' && canInvite" class="pb-tab-content anim-fade-in">
      <div class="pb-card pb-form-card">
        <div class="pb-card-header">
          <h2 class="pb-card-title">Send Notification</h2>
          <p class="pb-card-subtitle">Invite a new member to collaborate in this company workspace.</p>
        </div>
        
        <form @submit.prevent="sendInvite" class="pb-form">
          <div v-if="successMsg" class="pb-alert pb-alert-success">{{ successMsg }}</div>
          <div v-if="errorMsg" class="pb-alert pb-alert-error">{{ errorMsg }}</div>

          <div class="pb-form-grid">
            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Email Address</label>
              <input v-model="inviteForm.email" type="email" class="pb-input" placeholder="colleague@example.com" required>
            </div>

            <div class="pb-form-group pb-col-full">
              <label class="pb-label">Workspace Role</label>
              <select v-model="inviteForm.role" class="pb-input" required>
                <option value="admin">Admin (Manage Workspace)</option>
                <option value="accountant">Accountant (Manage Books)</option>
                <option value="viewer">Viewer (Read Only)</option>
              </select>
            </div>
          </div>

          <div class="pb-form-actions">
            <button type="submit" class="pb-btn pb-btn-primary" :disabled="loading">
              {{ loading ? 'Sending Invite...' : 'Send Invitation' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth.js';
import axios from 'axios';

const route = useRoute();
const id = route.params.companyId;

const authStore = useAuthStore();
const activeTab = ref('members');
const loading = ref(false);

const members = ref([]);
const invitations = ref([]);

const editRoleId = ref(null);
const editRoleValue = ref('');

const inviteForm = ref({ email: '', role: 'admin' });
const successMsg = ref('');
const errorMsg = ref('');

const currentUserRole = computed(() => {
  const me = members.value.find(m => m.id === authStore.user?.id);
  return me?.pivot?.role || 'viewer';
});

const canInvite = computed(() => ['owner', 'admin'].includes(currentUserRole.value));
const canManageTeam = computed(() => currentUserRole.value === 'owner');

onMounted(() => {
  fetchMembers();
  fetchInvitations();
});

const fetchMembers = async () => {
  try {
    const res = await axios.get(`companies/${id}/users`);
    members.value = res.data.data.users || [];
  } catch (err) {
    console.error('Failed to load members', err);
  }
};

const fetchInvitations = async () => {
  try {
    const res = await axios.get(`companies/${id}/invitations`);
    invitations.value = res.data.data.invitations || [];
  } catch (err) {
    console.error('Failed to load invitations', err);
  }
};

const sendInvite = async () => {
  loading.value = true;
  successMsg.value = '';
  errorMsg.value = '';
  try {
    await axios.post(`companies/${id}/invite`, inviteForm.value);
    successMsg.value = 'Invitation sent successfully!';
    inviteForm.value.email = '';
    inviteForm.value.role = 'admin';
    fetchInvitations();
    setTimeout(() => { successMsg.value = ''; }, 3000);
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Error sending invitation';
  } finally {
    loading.value = false;
  }
};

const revokeInvitation = async (invId) => {
  if (!confirm('Revoke this invitation?')) return;
  loading.value = true;
  try {
    await axios.delete(`companies/${id}/invitations/${invId}`);
    await fetchInvitations();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to revoke');
  } finally {
    loading.value = false;
  }
};

const startRoleEdit = (member) => {
  if (!canManageTeam.value || member?.pivot?.role === 'owner') return;
  editRoleId.value = member.id;
  editRoleValue.value = member.pivot?.role || 'viewer';
};

const saveRole = async (userId) => {
  loading.value = true;
  try {
    await axios.put(`companies/${id}/users/${userId}/role`, { role: editRoleValue.value });
    editRoleId.value = null;
    await fetchMembers();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to update role');
  } finally {
    loading.value = false;
  }
};

const removeMember = async (userId) => {
  if (!confirm('Remove this member from the company?')) return;
  loading.value = true;
  try {
    await axios.delete(`companies/${id}/users/${userId}`);
    await fetchMembers();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to remove user');
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric'
  });
};

const getRoleBadgeClass = (role) => {
  switch(role) {
    case 'owner': return 'pb-status--owner';
    case 'admin': return 'pb-status--admin';
    case 'accountant': return 'pb-status--active';
    default: return 'pb-status--active';
  }
};
</script>

<style scoped>
.pb-team-page { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.pb-page-header { margin-bottom: 2rem; padding-bottom: 0; }
.pb-header-content { display: flex; flex-direction: column; gap: 0.5rem; }
.pb-page-title { font-size: 28px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; margin: 0; }
.pb-page-subtitle { color: #64748b; font-size: 14px; margin-bottom: 1.5rem; }

.pb-tabs { display: flex; gap: 32px; border-bottom: 2px solid #f1f5f9; padding: 0 4px; }
.pb-tab-btn { position: relative; display: flex; align-items: center; gap: 10px; padding: 12px 4px; border: none; background: transparent; color: #64748b; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.pb-tab-btn:hover { color: #1e293b; }
.pb-tab-btn--active { color: #4f46e5; }
.pb-tab-btn--active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: #4f46e5; border-radius: 2px; }
.pb-tab-badge { display: flex; align-items: center; justify-content: center; min-width: 24px; height: 24px; padding: 0 6px; border: 1.5px solid #cbd5e1; background: transparent; color: #64748b; border-radius: 12px; font-size: 12px; font-weight: 700; transition: all 0.2s; }
.pb-tab-btn--active .pb-tab-badge { border-color: #4f46e5; color: #4f46e5; }

.pb-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.pb-table-wrapper { overflow-x: auto; }
.pb-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.pb-table th { text-align: left; padding: 1.25rem 1.5rem; background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.pb-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; color: #1a1a2e; vertical-align: middle; }
.pb-table tr:hover { background: #f8fafc; }

.pb-status-pill { display: inline-flex; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.pb-status--active { background: #d1fae5; color: #065f46; }
.pb-status--admin { background: #e0e7ff; color: #4338ca; }
.pb-status--owner { background: #fce7f3; color: #be185d; }
.pb-status--inactive { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

.pb-font-medium { font-weight: 500; }
.pb-text-center { text-align: center; }
.pb-text-muted { color: #94a3b8; }

.pb-input-sm { padding: 8px !important; font-size: 13px !important; border-radius: 6px !important; }
.pb-action-group { display: flex; justify-content: center; gap: 8px; }
.pb-btn-icon { width: 32px; height: 32px; border-radius: 8px; border: none; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; color: #64748b; }
.pb-btn-icon:hover:not(:disabled) { background: #f1f5f9; }
.pb-btn-icon:disabled { opacity: 0.5; cursor: not-allowed; }
.pb-icon-primary:hover { color: #4f46e5; background: #e0e7ff; }
.pb-icon-danger:hover { color: #e11d48; background: #ffe4e6; }
.pb-icon-success:hover { color: #059669; background: #d1fae5; }
.pb-icon-neutral:hover { color: #475569; background: #e2e8f0; }
.pb-empty-row { text-align: center; padding: 4rem !important; color: #94a3b8; }

.pb-form-card { width: 100%; }
.pb-card-header { padding: 2rem; border-bottom: 1px solid #f1f5f9; }
.pb-card-title { font-size: 20px; font-weight: 700; margin: 0 0 8px 0; color: #1a1a2e; }
.pb-card-subtitle { font-size: 14px; color: #64748b; margin: 0; }
.pb-form { padding: 2rem; }
.pb-form-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem; }
.pb-col-full { grid-column: 1 / -1; }
.pb-form-group { display: flex; flex-direction: column; gap: 8px; }
.pb-label { font-size: 13px; font-weight: 600; color: #64748b; }
.pb-input { padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: all 0.2s; background: #f8fafc; }
.pb-input:focus { outline: none; border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
.pb-form-actions { display: flex; justify-content: flex-end; padding-top: 2rem; border-top: 1px solid #f1f5f9; }
.pb-btn { padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
.pb-btn-primary { background: #4f46e5; color: white; }
.pb-btn-primary:hover:not(:disabled) { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }

.pb-alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 14px; font-weight: 500; }
.pb-alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.pb-alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.anim-fade-in { animation: fadeIn 0.3s ease-out; }
</style>
