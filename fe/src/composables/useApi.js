import { useAuth } from './useAuth'

const apiBase = import.meta.env.VITE_API_BASE || ''

export const apiFetch = async (path, options = {}) => {
  const { token } = useAuth()
  const headers = {
    'Content-Type': 'application/json',
    ...(options.headers || {}),
  }

  if (token.value) {
    headers.Authorization = `Bearer ${token.value}`
  }

  const response = await fetch(`${apiBase}${path}`, {
    ...options,
    headers,
  })

  const contentType = response.headers.get('Content-Type') || ''
  const payload = contentType.includes('application/json') ? await response.json() : null

  if (!response.ok) {
    const message = payload?.message || 'Có lỗi xảy ra.'
    throw new Error(message)
  }

  return payload
}
