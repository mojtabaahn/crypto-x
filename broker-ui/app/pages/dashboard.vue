<script setup lang="ts">
definePageMeta({
  middleware: ['auth']
})

const { profile, loading: profileLoading, error: profileError, fetchProfile } = useAuth()

const isOrderModalOpen = ref(false)

onMounted(async () => {
  await fetchProfile()

  setInterval(async () => {
    await fetchProfile()
  }, 10000)
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
    <div v-if="profileLoading && !profile" class="flex items-center justify-center py-32">
      <div class="text-center">
        <Spinner class="h-12 w-12 mx-auto mb-4" />
        <p class="text-muted-foreground">Loading...</p>
      </div>
    </div>

    <main v-if="profile" class="container mx-auto px-6 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">
          Welcome back, <span class="text-primary">{{ profile?.name || 'Trader' }}</span>
        </h1>
        <p class="text-muted-foreground mt-2">Ready to make some winning trades today?</p>
      </div>

      <Alert v-if="profileError" variant="destructive" class="mb-6">
        <AlertDescription>
          {{ profileError }}
        </AlertDescription>
      </Alert>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-3 space-y-6">
          <Orderbook v-model:is-order-modal-open="isOrderModalOpen" />
          <MyOrders />
        </div>

        <ProfileDetails />
      </div>
    </main>

    <PlaceOrderModal v-model:open="isOrderModalOpen" />
  </div>
</template>
