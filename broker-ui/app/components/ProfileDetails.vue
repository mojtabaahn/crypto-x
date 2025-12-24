<script setup lang="ts">
const { profile } = useAuth()
const { logout, loading: logoutLoading } = useLogout()

const handleLogout = async () => {
  const result = await logout()
  if (result.success) {
    await navigateTo('/login')
  }
}
</script>

<template>
  <div class="lg:col-span-1 lg:pl-8">
    <div>
      <div class="mb-6">
        <p class="text-sm text-muted-foreground">Signed in as</p>
        <p class="font-semibold text-base mt-1">{{ profile?.name || 'Trader' }} <span class="font-normal text-muted-foreground">({{ profile?.email || '' }})</span></p>
        <Button
          variant="outline"
          size="sm"
          @click="handleLogout"
          :disabled="logoutLoading"
          class="mt-4"
        >
          <Spinner v-if="logoutLoading" class="mr-2 h-4 w-4" />
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" x2="9" y1="12" y2="12"></line>
          </svg>
          Logout
        </Button>
      </div>

      <p class="text-sm text-muted-foreground mb-4">Wallet Summary</p>

      <TooltipProvider>
        <div v-if="profile.assets && profile.assets.length > 0" class="space-y-1">
          <Tooltip>
            <TooltipTrigger as-child>
              <div class="group flex justify-between items-center py-3 px-4 -mx-4 hover:bg-muted/50 rounded-lg cursor-help transition-colors">
                <div class="flex flex-col">
                  <span class="font-semibold text-base">USD</span>
                  <span class="text-xs text-muted-foreground">US Dollar</span>
                </div>
                <span class="font-mono text-base">{{ ((profile.balance || 0) + (profile.locked_balance || 0)).toFixed(2) }}</span>
              </div>
            </TooltipTrigger>
            <TooltipContent>
              <div class="space-y-1">
                <div class="flex justify-between gap-4">
                  <span class="text-muted-foreground">Available:</span>
                  <span class="font-medium">${{ profile.balance?.toFixed(2) || '0.00' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                  <span class="text-muted-foreground">Locked:</span>
                  <span class="font-medium text-amber-600 dark:text-amber-400">${{ profile.locked_balance?.toFixed(2) || '0.00' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                  <span class="text-muted-foreground">Total:</span>
                  <span class="font-bold">${{ ((profile.balance || 0) + (profile.locked_balance || 0)).toFixed(2) }}</span>
                </div>
              </div>
            </TooltipContent>
          </Tooltip>

          <Tooltip v-for="asset in profile.assets" :key="asset.id">
            <TooltipTrigger as-child>
              <div class="group flex justify-between items-center py-3 px-4 -mx-4 hover:bg-muted/50 rounded-lg cursor-help transition-colors">
                <div class="flex flex-col">
                  <span class="font-semibold text-base">{{ asset.symbol }}</span>
                  <span class="text-xs text-muted-foreground">Cryptocurrency</span>
                </div>
                <span class="font-mono text-base">{{ ((asset.amount || 0) + (asset.locked_amount || 0)).toFixed(4) }}</span>
              </div>
            </TooltipTrigger>
            <TooltipContent>
              <div class="space-y-1">
                <div class="flex justify-between gap-4">
                  <span class="text-muted-foreground">Available:</span>
                  <span class="font-medium">{{ asset.amount?.toFixed(4) || '0.0000' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                  <span class="text-muted-foreground">Locked:</span>
                  <span class="font-medium text-amber-600 dark:text-amber-400">{{ asset.locked_amount?.toFixed(4) || '0.0000' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                  <span class="text-muted-foreground">Total:</span>
                  <span class="font-bold">{{ ((asset.amount || 0) + (asset.locked_amount || 0)).toFixed(4) }}</span>
                </div>
              </div>
            </TooltipContent>
          </Tooltip>
        </div>
        <div v-else class="text-center py-12 text-muted-foreground">
          <p class="text-sm">No assets yet</p>
          <p class="text-xs mt-1">Start trading to see your assets here</p>
        </div>
      </TooltipProvider>
    </div>
  </div>
</template>
