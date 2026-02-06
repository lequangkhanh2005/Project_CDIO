import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../pages/auth/LoginPage.vue'
import AdminDashboard from '../pages/admin/AdminDashboard.vue'
import TechnicianJobs from '../pages/technician/TechnicianJobs.vue'
import TechnicianJobDetail from '../pages/technician/TechnicianJobDetail.vue'
import AdminResidents from '../pages/admin/AdminResidents.vue'
import AdminWorklogs from '../pages/admin/AdminWorklogs.vue'
import AdminInvoices from '../pages/admin/AdminInvoices.vue'
import AdminReports from '../pages/admin/AdminReports.vue'
import AdminHistory from '../pages/admin/AdminHistory.vue'
import { useAuth } from '../composables/useAuth'

const routes = [
  { path: '/', redirect: '/technician/menu' },
  { path: '/login', redirect: '/technician/login' },
  { path: '/technician/login', name: 'LoginTechnician', component: LoginPage },

  { path: '/technician/menu', name: 'TechnicianDashboard', component: AdminDashboard, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/jobs', name: 'TechnicianJobs', component: TechnicianJobs, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/jobs/:id', name: 'TechnicianJobDetail', component: TechnicianJobDetail, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/residents', name: 'TechnicianResidents', component: AdminResidents, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/worklogs', name: 'TechnicianWorklogs', component: AdminWorklogs, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/invoices', name: 'TechnicianInvoices', component: AdminInvoices, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/reports', name: 'TechnicianReports', component: AdminReports, meta: { requiresAuth: true, role: 'technician' } },
  { path: '/technician/history', name: 'TechnicianHistory', component: AdminHistory, meta: { requiresAuth: true, role: 'technician' } },

  { path: '/admin/dashboard', redirect: '/technician/menu' },
  { path: '/admin/jobs', redirect: '/technician/jobs' },
  { path: '/admin/residents', redirect: '/technician/residents' },
  { path: '/admin/worklogs', redirect: '/technician/worklogs' },
  { path: '/admin/invoices', redirect: '/technician/invoices' },
  { path: '/admin/reports', redirect: '/technician/reports' },
  { path: '/admin/history', redirect: '/technician/history' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const { isLoggedIn, role, user, clearSession } = useAuth()

  if (to.path === '/technician/login') {
    return true
  }

  if (to.meta?.requiresAuth && (!isLoggedIn.value || !user.value)) {
    return { path: '/technician/login' }
  }

  if (to.meta?.role && role.value && to.meta.role !== role.value) {
    clearSession()
    return { path: '/technician/login' }
  }

  return true
})

export default router
