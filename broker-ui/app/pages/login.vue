<script setup lang="ts">
const email = ref('')
const password = ref('')

const { login, loading, errors, errorMessage } = useLogin()

const handleLogin = async () => {
  const result = await login({
    email: email.value,
    password: password.value
  })

  if (result.success) {
    await navigateTo('/dashboard')
  }
}
</script>

<template>
  <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 px-4">
    <Card class="w-full max-w-md">
      <CardHeader class="space-y-1">
        <CardTitle class="text-2xl font-bold text-center">
          Welcome back
        </CardTitle>
        <CardDescription class="text-center">
          Sign in to your account to continue
        </CardDescription>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="handleLogin" class="space-y-4">
          <Alert v-if="errorMessage && !Object.keys(errors).length" variant="destructive" class="mb-4">
            <AlertDescription>
              {{ errorMessage }}
            </AlertDescription>
          </Alert>

          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="email"
              type="email"
              placeholder="name@example.com"
              :disabled="loading"
              required
              autocomplete="email"
              :class="{ 'border-destructive': errors.email }"
            />
            <p v-if="errors.email" class="text-sm text-destructive">{{ errors.email[0] }}</p>
          </div>

          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <Label for="password">Password</Label>
            </div>
            <Input
              id="password"
              v-model="password"
              type="password"
              placeholder="Enter your password"
              :disabled="loading"
              required
              autocomplete="current-password"
              :class="{ 'border-destructive': errors.password }"
            />
            <p v-if="errors.password" class="text-sm text-destructive">{{ errors.password[0] }}</p>
          </div>

          <Button
            type="submit"
            class="w-full"
            :disabled="loading"
          >
            <Spinner v-if="loading" class="mr-2 h-4 w-4" />
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </Button>
        </form>

      </CardContent>
    </Card>
  </div>
</template>
