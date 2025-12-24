export default defineNuxtRouteMiddleware(async (to, from) => {
  // Only check authentication on client side
  if (import.meta.client) {
    const { checkAuth } = useAuth()
    const isAuthenticated = await checkAuth()

    if (!isAuthenticated) {
      return navigateTo('/login')
    }
  }
})
