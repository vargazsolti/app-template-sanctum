<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { sanctumLogin, api } from '@/services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { clearAuth, loadMe } from '@/stores/auth'

const email = ref('john@example.com')
const password = ref('password')
const remember = ref(false)
const loading = ref(false)
const error = ref('')

const router = useRouter()
const route = useRoute()

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await sanctumLogin(email.value, password.value, remember.value)

    // ⬇️ friss auth állapot
    clearAuth()
    await loadMe(true)

    router.replace((route.query.r) || '/')
  } catch (e) {
    error.value = e?.response?.data?.message || 'Invalid credentials.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center p-4">
    <Card class="w-full max-w-md shadow-md">
      <CardHeader>
        <CardTitle class="text-center text-2xl">Sign in</CardTitle>
        <p class="text-center text-sm text-gray-500">Use your existing Laravel users</p>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input id="email" type="email" v-model="email" required autocomplete="email" />
          </div>
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <Label for="password">Password</Label>
              <a class="text-sm text-blue-600 hover:underline" href="#">Forgot?</a>
            </div>
            <Input id="password" type="password" v-model="password" required autocomplete="current-password" />
          </div>
          <div class="flex items-center gap-2">
            <Checkbox id="remember" v-model:checked="remember" />
            <Label for="remember">Remember me</Label>
          </div>
          <Button class="w-full" :disabled="loading">
            <span v-if="!loading">Sign in</span>
            <span v-else>Signing in…</span>
          </Button>
          <p v-if="error" class="text-sm text-red-600 text-center">{{ error }}</p>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
