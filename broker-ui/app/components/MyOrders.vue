<script setup lang="ts">
const { profileOrders, loading: profileOrdersLoading, fetchProfileOrders } = useProfileOrders()
const { cancelOrder, loading: orderCancelling } = useCancelOrder()
const { fetchProfile } = useAuth()

const symbolFilter = ref('')
const sideFilter = ref('')
const statusFilter = ref('')

const uniqueSymbols = computed(() => {
  const symbols = [...new Set(profileOrders.value.map(order => order.symbol))]
  return symbols.sort()
})

const handleCancelOrder = async (orderId: number) => {
  const result = await cancelOrder(orderId)

  if (result.success) {
    await Promise.all([
      fetchProfileOrders(),
      fetchProfile()
    ])
  }
}

watch([symbolFilter, sideFilter, statusFilter], () => {
  fetchProfileOrders({
    symbol: symbolFilter.value || undefined,
    side: sideFilter.value || undefined,
    status: statusFilter.value || undefined
  })
})

onMounted(() => {
  fetchProfileOrders()
})
</script>

<template>
  <div class="bg-background border shadow-sm rounded-lg">
    <div class="px-6 py-4 rounded-t-lg border-b">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight">My Orders</h2>
          <p class="text-sm text-muted-foreground mt-1">All your past and current orders</p>
        </div>
        <div class="flex items-center gap-2">
          <Select v-model="symbolFilter">
            <SelectTrigger class="w-32 h-9">
              <SelectValue placeholder="Symbol" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="symbol in uniqueSymbols" :key="symbol" :value="symbol">
                {{ symbol }}
              </SelectItem>
            </SelectContent>
          </Select>
          <Select v-model="sideFilter">
            <SelectTrigger class="w-28 h-9">
              <SelectValue placeholder="Side" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="buy">Buy</SelectItem>
              <SelectItem value="sell">Sell</SelectItem>
            </SelectContent>
          </Select>
          <Select v-model="statusFilter">
            <SelectTrigger class="w-32 h-9">
              <SelectValue placeholder="Status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="Open">Open</SelectItem>
              <SelectItem value="Filled">Filled</SelectItem>
              <SelectItem value="Cancelled">Cancelled</SelectItem>
            </SelectContent>
          </Select>
          <Button
            v-if="symbolFilter || sideFilter || statusFilter"
            size="sm"
            variant="ghost"
            class="h-9"
            @click="symbolFilter = ''; sideFilter = ''; statusFilter = ''"
          >
            Clear
          </Button>
        </div>
      </div>
    </div>

    <div class="">
      <div v-if="profileOrdersLoading && !profileOrders.length" class="flex items-center justify-center py-8">
        <Spinner class="h-6 w-6" />
      </div>
      <div v-else-if="profileOrders && profileOrders.length > 0" class="overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="px-4 py-3">ID</TableHead>
              <TableHead class="px-4 py-3">Symbol</TableHead>
              <TableHead class="px-4 py-3">Side</TableHead>
              <TableHead class="px-4 py-3">Price</TableHead>
              <TableHead class="px-4 py-3">Amount</TableHead>
              <TableHead class="px-4 py-3">Status</TableHead>
              <TableHead class="px-4 py-3">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="order in profileOrders" :key="order.id">
              <TableCell class="px-4 py-3 font-mono text-xs">#{{ order.id }}</TableCell>
              <TableCell class="px-4 py-3 font-medium">{{ order.symbol }}</TableCell>
              <TableCell>
                <span :class="{
                  'text-green-600 dark:text-green-400': order.side === 'buy',
                  'text-red-600 dark:text-red-400': order.side === 'sell'
                }" class="font-medium">
                  {{ order.side.toUpperCase() }}
                </span>
              </TableCell>
              <TableCell class=" px-4 py-3 ">
                ${{ order.price?.toFixed(2) || '0.00' }}
              </TableCell>
              <TableCell class=" px-4 py-3 ">
                {{ order.amount?.toFixed(4) || '0.0000' }}
              </TableCell>
              <TableCell class="px-4 py-3 ">
                <span :class="{
                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': order.status === 'Open',
                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': order.status === 'Filled',
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': order.status === 'Cancelled'
                }" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ order.status }}
                </span>
              </TableCell>
              <TableCell class="px-4 py-3 ">
                <Button
                  v-if="order.status === 'Open'"
                  size="sm"
                  variant="destructive"
                  @click="handleCancelOrder(order.id)"
                  :disabled="orderCancelling"
                >
                  <Spinner v-if="orderCancelling" class="mr-2 h-3 w-3" />
                  Cancel
                </Button>
                <span v-else class="text-muted-foreground text-sm">-</span>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div v-else class="text-center py-8 text-muted-foreground">
        No orders found. Try adjusting your filters or start trading!
      </div>
    </div>
  </div>
</template>
