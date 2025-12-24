<script setup lang="ts">
const TRADABLE_SYMBOLS = ['BTC', 'ETH', 'SOL', 'XRP', 'ADA', 'DOT', 'LINK', 'UNI', 'AVAX', 'MATIC'] as const

interface Props {
  open?: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:open': [value: boolean]
}>()

const open = computed({
  get: () => props.open,
  set: (value) => emit('update:open', value)
})

// Order form
const orderSymbol = ref('BTC')
const orderSide = ref<'buy' | 'sell'>('buy')
const orderPrice = ref('')
const orderAmount = ref('')

// Derived calculations for order form
const requiredUSD = computed(() => {
  if (!orderPrice.value || !orderAmount.value || orderSide.value !== 'buy') return 0
  return parseFloat(orderPrice.value) * parseFloat(orderAmount.value)
})

const lockedAsset = computed(() => {
  if (!orderAmount.value || orderSide.value !== 'sell') return { amount: 0, symbol: '' }
  return {
    amount: parseFloat(orderAmount.value),
    symbol: orderSymbol.value
  }
})

const { createOrder, loading: orderCreating, error: orderError, errors: orderErrors } = useCreateOrder()
const { fetchProfile } = useAuth()
const { fetchProfileOrders } = useProfileOrders()
const { fetchOrders } = useOrders()

const handlePlaceOrder = async () => {
  const result = await createOrder({
    symbol: orderSymbol.value,
    side: orderSide.value,
    price: parseFloat(orderPrice.value),
    amount: parseFloat(orderAmount.value)
  })

  if (result.success) {
    // Reset form
    orderSymbol.value = 'BTC'
    orderSide.value = 'buy'
    orderPrice.value = ''
    orderAmount.value = ''

    // Refresh orders and profile
    await Promise.all([
      fetchProfileOrders(),
      fetchOrders(),
      fetchProfile()
    ])

    // Close modal
    open.value = false
  }
}
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="max-w-md">
      <DialogHeader>
        <DialogTitle>Place Limit Order</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handlePlaceOrder" class="space-y-4">
        <!-- Error Message -->
        <Alert v-if="orderError && !Object.keys(orderErrors).length" variant="destructive">
          <AlertDescription>
            {{ orderError }}
          </AlertDescription>
        </Alert>

        <!-- Symbol Selector -->
        <div class="space-y-2">
          <Label for="order-symbol">Symbol</Label>
          <Select v-model="orderSymbol" required>
            <SelectTrigger id="order-symbol">
              <SelectValue placeholder="Select" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="symbol in TRADABLE_SYMBOLS" :key="symbol" :value="symbol">
                {{ symbol }}
              </SelectItem>
            </SelectContent>
          </Select>
          <p v-if="orderErrors.symbol" class="text-sm text-destructive">{{ orderErrors.symbol }}</p>
        </div>

        <!-- Side Selector (Buy/Sell Toggle) -->
        <div class="space-y-2">
          <Label>Side</Label>
          <div class="flex gap-2">
            <Button
              type="button"
              variant="outline"
              :class="[
                'flex-1',
                orderSide === 'buy' ? 'bg-green-600 text-white hover:bg-green-700' : ''
              ]"
              @click="orderSide = 'buy'"
            >
              Buy
            </Button>
            <Button
              type="button"
              variant="outline"
              :class="[
                'flex-1',
                orderSide === 'sell' ? 'bg-red-600 text-white hover:bg-red-700' : ''
              ]"
              @click="orderSide = 'sell'"
            >
              Sell
            </Button>
          </div>
          <p v-if="orderErrors.side" class="text-sm text-destructive">{{ orderErrors.side }}</p>
        </div>

        <!-- Price Input -->
        <div class="space-y-2">
          <Label for="order-price">Price (USD)</Label>
          <Input
            id="order-price"
            v-model="orderPrice"
            type="number"
            step="0.01"
            min="0"
            placeholder="0.00"
            required
            :disabled="orderCreating"
            :class="{ 'border-destructive': orderErrors.price }"
          />
          <p v-if="orderErrors.price" class="text-sm text-destructive">{{ orderErrors.price }}</p>
        </div>

        <!-- Amount Input -->
        <div class="space-y-2">
          <Label for="order-amount">Amount</Label>
          <Input
            id="order-amount"
            v-model="orderAmount"
            type="number"
            step="0.0001"
            min="0"
            placeholder="0.0000"
            required
            :disabled="orderCreating"
            :class="{ 'border-destructive': orderErrors.amount }"
          />
          <p v-if="orderErrors.amount" class="text-sm text-destructive">{{ orderErrors.amount }}</p>
        </div>

        <!-- Derived Text -->
        <div class="bg-muted p-3 rounded-md">
          <p class="text-sm">
            <span class="font-medium">
              {{ orderSide === 'buy' ? 'Required USD (excluding fee):' : 'Locked Asset:' }}
            </span>
            <span class="ml-2">
              {{ orderSide === 'buy' ? `$${requiredUSD.toFixed(2)}` : `${lockedAsset.amount.toFixed(4)} ${lockedAsset.symbol}` }}
            </span>
          </p>
        </div>

        <!-- Submit Button -->
        <Button
          type="submit"
          class="w-full"
          :disabled="orderCreating || !orderSymbol || !orderPrice || !orderAmount"
        >
          <Spinner v-if="orderCreating" class="mr-2 h-4 w-4" />
          {{ orderCreating ? 'Placing Order...' : 'Place Order' }}
        </Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
