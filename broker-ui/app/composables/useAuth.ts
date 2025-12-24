interface ProfileType {
    id: number
    name: string
    email: string
    balance: number
    assets: Array<{
        id: number
        symbol: string
        amount: number
        locked_amount: number
    }>
}

const loading = ref(false)
const error = ref<string | null>(null)
const profile = ref<ProfileType | null>(null)

export const useAuth = () => {
  const isAuthenticated = computed(() => profile.value !== null)

  const fetchProfile = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await $fetch('/api/profile', {
        baseURL: 'http://localhost:8000',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        }
      })

      profile.value = (response as any).data || response as ProfileType
      return { success: true, data: profile.value }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch profile'
      profile.value = null
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const checkAuth = async () => {
    const result = await fetchProfile()
    return result.success
  }

  return {
    profile,
    loading,
    error,
    isAuthenticated,
    fetchProfile,
    checkAuth
  }
}
