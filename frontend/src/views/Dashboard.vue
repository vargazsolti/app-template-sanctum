<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api, sanctumLogout } from '@/services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { clearAuth } from '@/stores/auth'

const router = useRouter()
const me = ref(null)

onMounted(async () => {
  const { data } = await api.get('/api/v1/me')
  me.value = data
})

async function logout() {
  await sanctumLogout()

  // auth state ürítés (guard a /login-re fog engedni)
  clearAuth()
  router.replace('/login')
}
</script>

<template>
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-60 hidden md:flex flex-col border-r bg-white">
      <div class="p-4 flex items-center gap-3">
        <Avatar>
          <AvatarFallback>{{ (me?.name || 'U').slice(0,1).toUpperCase() }}</AvatarFallback>
        </Avatar>
        <div class="leading-tight">
          <div class="font-medium">{{ me?.name || 'User' }}</div>
          <div class="text-xs text-gray-500">{{ me?.email || '' }}</div>
        </div>
      </div>
      <Separator />
      <nav class="p-2 space-y-1">
        <Button variant="ghost" class="w-full justify-start">Dashboard</Button>
        <Button variant="ghost" class="w-full justify-start">Customers</Button>
        <Button variant="ghost" class="w-full justify-start">Leads</Button>
        <Button variant="ghost" class="w-full justify-start">Tasks</Button>
      </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col">
      <!-- Topbar -->
      <header class="h-16 border-b bg-white flex items-center px-4 gap-3">
        <Button variant="ghost" class="md:hidden">☰</Button>
        <div class="flex-1 max-w-xl">
          <input class="w-full rounded-md border px-3 py-2 bg-gray-50 focus:bg-white" placeholder="Search..." />
        </div>
        <Button variant="default" class="rounded-full w-8 h-8 p-0">+</Button>
        <Separator orientation="vertical" class="h-6" />
        <Button variant="ghost" class="text-sm" @click="logout">Logout</Button>
      </header>

      <!-- Content -->
      <main class="p-4 md:p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Welcome</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Card class="min-h-[220px]">
            <CardHeader><CardTitle>Panel 1</CardTitle></CardHeader>
            <CardContent class="text-gray-500">Content placeholder</CardContent>
          </Card>

          <Card class="min-h-[220px]">
            <CardHeader><CardTitle>Panel 2</CardTitle></CardHeader>
            <CardContent class="text-gray-500">Content placeholder</CardContent>
          </Card>

          <Card class="min-h-[220px]">
            <CardHeader><CardTitle>Panel 3</CardTitle></CardHeader>
            <CardContent class="text-gray-500">Content placeholder</CardContent>
          </Card>
        </div>
      </main>
    </div>
  </div>
</template>
