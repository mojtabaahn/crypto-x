export const useLogin = () => {
  const loading = ref(false)
  const errors = ref<Record<string, string>>({})
  const errorMessage = ref('')

  const login = async (credentials: { email: string; password: string }) => {
    loading.value = true
    errors.value = {}
    errorMessage.value = ''

    try {
      await $fetch('/login', {
        method: 'POST',
        baseURL: 'http://localhost:8000',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        },
        body: credentials
      })

      return { success: true }
    } catch (error: any) {
      if (error.response?.status === 422) {
        const data = error.response._data
        if (data.errors) {
          errors.value = data.errors
          errorMessage.value = data.message || 'Please check your input'
        }
      }
      else if (error.response?.status === 401) {
        errorMessage.value = 'Invalid email or password'
      }
      else {
        errorMessage.value = error.message || 'Login failed. Please try again.'
      }

      return { success: false }
    } finally {
      loading.value = false
    }
  }

  return {
    login,
    loading,
    errors,
    errorMessage
  }
}
