import { createRouter, createWebHistory } from 'vue-router';

// Auth pages
import VoterLogin from '../components/auth/VoterLogin.vue';
import VoterRegister from '../components/auth/VoterRegister.vue';
import AdminLogin from '../components/auth/AdminLogin.vue';

// Voter pages
import VoterDashboard from '../components/voter/VoterDashboard.vue';
import VoterVote from '../components/voter/VoterVote.vue';
import VoterVotes from '../components/voter/VoterVotes.vue';
import VoterResults from '../components/voter/VoterResults.vue';
import VoterProfile from '../components/voter/VoterProfile.vue';

// Admin pages
import AdminDashboard from '../components/admin/AdminDashboard.vue';
import AdminElections from '../components/admin/AdminElections.vue';
import AdminCreateElection from '../components/admin/AdminCreateElection.vue';
import AdminEditElection from '../components/admin/AdminEditElection.vue';
import AdminVoters from '../components/admin/AdminVoters.vue';
import AdminResults from '../components/admin/AdminResults.vue';
import AdminAnnouncements from '../components/admin/AdminAnnouncements.vue';

// Layouts
import DashboardLayout from '../components/layouts/DashboardLayout.vue';
import AuthLayout from '../components/layouts/AuthLayout.vue';

// Import API for auth checks
import { authAPI } from '../services/api.js';

const routes = [
  // Auth routes (no authentication required)
  {
    path: '/',
    component: AuthLayout,
    children: [
      {
        path: '',
        redirect: '/voter/login'
      },
      {
        path: '/voter/login',
        name: 'voter-login',
        component: VoterLogin,
      },
      {
        path: '/voter/register',
        name: 'voter-register',
        component: VoterRegister,
      },
      {
        path: '/admin/login',
        name: 'admin-login',
        component: AdminLogin,
      },
    ]
  },

  // Voter protected routes
  {
    path: '/voter',
    component: DashboardLayout,
    meta: { requiresAuth: true, role: 'voter' },
    children: [
      {
        path: 'dashboard',
        name: 'voter-dashboard',
        component: VoterDashboard,
      },
      {
        path: 'vote',
        name: 'voter-vote',
        component: VoterVote,
      },
      {
        path: 'votes',
        name: 'voter-votes',
        component: VoterVotes,
      },
      {
        path: 'results',
        name: 'voter-results',
        component: VoterResults,
      },
      {
        path: 'profile',
        name: 'voter-profile',
        component: VoterProfile,
      },
    ]
  },

  // Admin protected routes
  {
    path: '/admin',
    component: DashboardLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: AdminDashboard,
      },
      {
        path: 'elections',
        name: 'admin-elections',
        component: AdminElections,
      },
      {
        path: 'elections/create',
        name: 'admin-create-election',
        component: AdminCreateElection,
      },
      {
        path: 'elections/:id/edit',
        name: 'admin-edit-election',
        component: AdminEditElection,
      },
      {
        path: 'voters',
        name: 'admin-voters',
        component: AdminVoters,
      },
      {
        path: 'results',
        name: 'admin-results',
        component: AdminResults,
      },
      {
        path: 'announcements',
        name: 'admin-announcements',
        component: AdminAnnouncements,
      },
    ]
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Check server-side auth status and sync with localStorage
async function syncAuthStatus() {
  try {
    // Check voter auth status
    const voterCheck = await authAPI.voterCheck().catch(() => null);
    if (voterCheck?.data?.authenticated) {
      localStorage.setItem('auth_token', voterCheck.data.auth_token);
      localStorage.setItem('user_role', voterCheck.data.user_role);
      return;
    }

    // Check admin auth status
    const adminCheck = await authAPI.adminCheck().catch(() => null);
    if (adminCheck?.data?.authenticated) {
      localStorage.setItem('auth_token', adminCheck.data.auth_token);
      localStorage.setItem('user_role', adminCheck.data.user_role);
      return;
    }

    // Not authenticated
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_role');
  } catch (error) {
    console.log('Auth sync error:', error);
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_role');
  }
}

// Authentication guard
router.beforeEach(async (to, from, next) => {
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    // First check localStorage
    let isAuthenticated = localStorage.getItem('auth_token');
    let userRole = localStorage.getItem('user_role');

    // If not in localStorage, check server-side
    if (!isAuthenticated) {
      await syncAuthStatus();
      isAuthenticated = localStorage.getItem('auth_token');
      userRole = localStorage.getItem('user_role');
    }

    if (!isAuthenticated) {
      // Redirect to appropriate login based on route
      const roleFromRoute = to.meta.role || 'voter';
      next(`/${roleFromRoute}/login`);
    } else if (to.meta.role && to.meta.role !== userRole) {
      // Redirect if role doesn't match
      next(`/${userRole}/dashboard` || `/${userRole}/login`);
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
