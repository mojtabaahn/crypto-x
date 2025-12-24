export const useCancelOrder = () => {
  const loading = ref(false)
  const error = ref<string | null>(null)

  const cancelOrder = async (orderId: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await $fetch(`/api/order/${orderId}/cancel`, {
        method: 'POST',
        baseURL: 'http://localhost:8000',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      })

      return { success: true, data: response }
    } catch (err: any) {
      error.value = err.response?._data?.message || err.message || 'Failed to cancel order'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    cancelOrder
  }
}
