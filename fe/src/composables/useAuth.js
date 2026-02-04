import { computed, ref } from 'vue'

const tokenKey = 'cm_token'
const userKey = 'cm_user'

const token = ref(localStorage.getItem(tokenKey) || '')
const initialUser = (() => {
  try {
    return JSON.parse(localStorage.getItem(userKey) || 'null')
  } catch {
    return null
  }
})()
const user = ref(initialUser)

const setSession = (newToken, newUser) => {
  token.value = newToken
  user.value = newUser
  localStorage.setItem(tokenKey, newToken)
  localStorage.setItem(userKey, JSON.stringify(newUser))
}

const clearSession = () => {
  token.value = ''
  user.value = null
  localStorage.removeItem(tokenKey)
  localStorage.removeItem(userKey)
}

export const useAuth = () => {
  const isLoggedIn = computed(() => Boolean(token.value))
  const role = computed(() => user.value?.role || '')
  const isAdmin = computed(() => role.value === 'admin')
  const isTechnician = computed(() => role.value === 'technician')

  return {
    token,
    user,
    isLoggedIn,
    role,
    isAdmin,
    isTechnician,
    setSession,
    clearSession,
  }
}
