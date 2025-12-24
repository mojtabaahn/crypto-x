export const useLogout = () => {
  const loading = ref(false)
  const error = ref<string | null>(null)

  const logout = async () => {
    loading.value = true
    error.value = null

    try {
      await $fetch('/logout', {
        method: 'POST',
        baseURL: 'http://localhost:8000',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      })

      return { success: true }
    } catch (err: any) {
      error.value = err.message || 'Failed to logout'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    logout,
    loading,
    error
  }
}
