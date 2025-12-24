interface CreateOrderType {
  symbol: string
  side: string
  price: number
  amount: number
}

export const useCreateOrder = () => {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const errors = ref<Record<string, string>>({})

  const createOrder = async (orderData: CreateOrderType) => {
    loading.value = true
    error.value = null
    errors.value = {}

    try {
      const response = await $fetch('/api/order', {
        method: 'POST',
        baseURL: 'http://localhost:8000',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        },
        body: orderData
      })

      return { success: true, data: response }
    } catch (err: any) {
      if (err.response?.status === 422) {
        const data = err.response._data
        if (data.errors) {
          errors.value = data.errors
          error.value = data.message || 'Please check your input'
        }
      }
      else {
        error.value = err.response?._data?.message || err.message || 'Failed to create order'
      }

      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    errors,
    createOrder
  }
}
