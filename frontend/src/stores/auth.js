import { reactive } from 'vue'
import { api } from '@/services/api'

export const auth = reactive({
  loaded: false,
  user: null,
})

export async function loadMe(force = false) {
  if (!auth.loaded || force) {
    try {
      const { data } = await api.get('/api/v1/me')
      auth.user = data ?? null
    } catch {
      auth.user = null
    } finally {
      auth.loaded = true
    }
  }
  return !!auth.user
}

export function clearAuth() {
  auth.loaded = false
  auth.user = null
}
