import { createRouter, createWebHistory } from 'vue-router'
import Login from './views/Login.vue'
import Dashboard from './views/Dashboard.vue'
import { api } from './services/api'
import { loadMe } from '@/stores/auth' 


const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', name: 'login', component: Login, meta: { guest: true } },
    { path: '/', name: 'dashboard', component: Dashboard, meta: { auth: true } },
  ],
})

let meCache = { loaded: false, ok: false }

async function ensureAuth() {
  if (meCache.loaded) return meCache.ok
  try {
    const { data } = await api.get('/api/v1/me')
    meCache.ok = !!data?.id
  } catch {
    meCache.ok = false
  } finally {
    meCache.loaded = true
  }
  return meCache.ok
}

router.beforeEach(async (to) => {
  if (to.meta.auth) {
    const ok = await loadMe()
    if (!ok) return { name: 'login', query: { r: to.fullPath } }
  }
  if (to.meta.guest) {
    const ok = await loadMe()
    if (ok) return { name: 'dashboard' }
  }
})

export default router
