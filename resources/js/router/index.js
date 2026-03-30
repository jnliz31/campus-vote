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

// Authentication guard
router.beforeEach((to, from, next) => {
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    // Check if user is authenticated (based on session/token)
    const isAuthenticated = localStorage.getItem('auth_token');
    const userRole = localStorage.getItem('user_role');

    if (!isAuthenticated) {
      // Redirect to login
      next('/voter/login');
    } else if (to.meta.role && to.meta.role !== userRole) {
      // Redirect if role doesn't match
      next(`/${userRole}/dashboard`);
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
