<script setup lang="ts">
const TRADABLE_SYMBOLS = ['BTC', 'ETH', 'SOL', 'XRP', 'ADA', 'DOT', 'LINK', 'UNI', 'AVAX', 'MATIC'] as const

interface Props {
  isOrderModalOpen?: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:isOrderModalOpen': [value: boolean]
}>()

const { orders, loading: ordersLoading, fetchOrders } = useOrders()

const orderbookSymbol = ref('BTC')

const sellOrders = computed(() => {
  return orders.value
    .filter(o => o.side === 'sell' && o.symbol === orderbookSymbol.value)
    .sort((a, b) => a.price - b.price)
})

const buyOrders = computed(() => {
  return orders.value
    .filter(o => o.side === 'buy' && o.symbol === orderbookSymbol.value)
    .sort((a, b) => b.price - a.price)
})

watch(orderbookSymbol, () => {
  fetchOrders({ symbol: orderbookSymbol.value })
})

onMounted(() => {
  fetchOrders({ symbol: orderbookSymbol.value })
})
</script>

<template>
  <div class="bg-background border shadow-sm rounded-lg">
    <div class="px-6 py-4 rounded-t-lg border-b">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight">Orderbook</h2>
          <p class="text-sm text-muted-foreground mt-1">View all open orders in the market</p>
        </div>
        <div class="flex items-center gap-2">
          <Select v-model="orderbookSymbol">
            <SelectTrigger class="w-32 h-9">
              <SelectValue placeholder="Select" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="symbol in TRADABLE_SYMBOLS" :key="symbol" :value="symbol">
                {{ symbol }}
              </SelectItem>
            </SelectContent>
          </Select>
          <Button size="sm" @click="emit('update:isOrderModalOpen', true)">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Place Order
          </Button>
        </div>
      </div>
    </div>

    <div class="">
      <div v-if="ordersLoading && !orders.length" class="flex items-center justify-center py-8">
        <Spinner class="h-6 w-6" />
      </div>
      <div v-else class="">
        <div>
          <h3 class="text-sm font-medium text-red-600 dark:text-red-400 px-4 py-2">Sell Orders</h3>
          <div v-if="sellOrders.length > 0" class="space-y-1">
            <div
              v-for="order in sellOrders"
              :key="order.id"
              class="flex justify-between items-center text-sm py-2 px-4 hover:bg-muted"
            >
              <span class="font-medium">${{ order.price?.toFixed(2) || '0.00' }}</span>
              <span>{{ order.amount?.toFixed(4) || '0.0000' }}</span>
            </div>
          </div>
          <div v-else class="text-center py-4 text-muted-foreground text-sm">
            No sell orders
          </div>
        </div>

        <Separator />

        <div>
          <h3 class="text-sm font-medium text-green-600 dark:text-green-400 px-4 py-2">Buy Orders</h3>
          <div v-if="buyOrders.length > 0" class="space-y-1">
            <div
              v-for="order in buyOrders"
              :key="order.id"
              class="flex justify-between items-center text-sm py-2 px-4 hover:bg-muted"
            >
              <span class="font-medium">${{ order.price?.toFixed(2) || '0.00' }}</span>
              <span>{{ order.amount?.toFixed(4) || '0.0000' }}</span>
            </div>
          </div>
          <div v-else class="text-center py-4 text-muted-foreground text-sm">
            No buy orders
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
