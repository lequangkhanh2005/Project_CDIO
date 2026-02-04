import { createRouter, createWebHistory } from 'vue-router'
<<<<<<< Updated upstream
import Home from '../components/Client/Home.vue'
import AdminHome from '../components/Admin/TrangChu.vue'
=======
import TechnicianDashboard from '../pages/technician/TechnicianDashboard.vue'
import LoginPage from '../pages/auth/LoginPage.vue'
import RegisterPage from '../pages/auth/RegisterPage.vue'
import AdminDashboard from '../pages/admin/AdminDashboard.vue'
import AdminJobs from '../pages/admin/AdminJobs.vue'
import AdminResidents from '../pages/admin/AdminResidents.vue'
import AdminWorklogs from '../pages/admin/AdminWorklogs.vue'
import AdminInvoices from '../pages/admin/AdminInvoices.vue'
import AdminReports from '../pages/admin/AdminReports.vue'
import AdminHistory from '../pages/admin/AdminHistory.vue'
import { useAuth } from '../composables/useAuth'
>>>>>>> Stashed changes

const routes = [
  {
    path: '/',
<<<<<<< Updated upstream
    component: Home,
    meta: { layout: 'Client' }
  },
  {
    path: '/admin',
    component: AdminHome,
    meta: { layout: 'Admin' }
  }
=======
    name: 'TechnicianDashboard',
    component: TechnicianDashboard,
    meta: { requiresAuth: true, role: 'technician' },
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterPage,
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/jobs',
    name: 'AdminJobs',
    component: AdminJobs,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/residents',
    name: 'AdminResidents',
    component: AdminResidents,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/worklogs',
    name: 'AdminWorklogs',
    component: AdminWorklogs,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/invoices',
    name: 'AdminInvoices',
    component: AdminInvoices,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/reports',
    name: 'AdminReports',
    component: AdminReports,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/history',
    name: 'AdminHistory',
    component: AdminHistory,
    meta: { requiresAuth: true, role: 'admin' },
  },
>>>>>>> Stashed changes
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})
<<<<<<< Updated upstream
=======

router.beforeEach((to) => {
  const { isLoggedIn, role } = useAuth()

  if (to.meta?.requiresAuth && !isLoggedIn.value) {
    return { path: '/login' }
  }

  if (to.meta?.role && role.value && to.meta.role !== role.value) {
    return { path: role.value === 'admin' ? '/admin/dashboard' : '/' }
  }

  return true
})
>>>>>>> Stashed changes

export default router
