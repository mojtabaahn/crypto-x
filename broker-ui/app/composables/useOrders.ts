interface OrderType {
  id: number
  symbol: string
  side: string
  price: number
  amount: number
  status: string
}

const orders = ref<OrderType[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

export const useOrders = () => {
  const fetchOrders = async (filters?: { symbol?: string; side?: string; status?: string }) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams()
      if (filters?.symbol) params.append('symbol', filters.symbol)
      if (filters?.side) params.append('side', filters.side)
      if (filters?.status) params.append('status', filters.status)

      const response = await $fetch(`/api/order?${params.toString()}`, {
        baseURL: 'http://localhost:8000',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      })

      orders.value = (response as any).data || response

      return { success: true, data: orders.value }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch orders'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const updateOrder = (orderId: number, updates: Partial<OrderType>) => {
    const index = orders.value.findIndex(o => o.id === orderId)
    if (index !== -1) {
      orders.value[index] = { ...orders.value[index], ...updates }
    }
  }

  const addOrder = (order: OrderType) => {
    orders.value.unshift(order)
  }

  return {
    orders,
    loading,
    error,
    fetchOrders,
    updateOrder,
    addOrder
  }
}
