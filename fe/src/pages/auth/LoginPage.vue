<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-header">
        <div class="brand-icon">CM</div>
        <div>
          <h1>Đăng nhập</h1>
          <p>Truy cập hệ thống quản lý bảo trì.</p>
        </div>
      </div>

      <form class="auth-form" @submit.prevent="submitLogin">
        <div class="field">
          <label for="phone">Số điện thoại</label>
          <input id="phone" v-model="form.phone" type="tel" placeholder="Nhập số điện thoại" />
        </div>
        <div class="field">
          <label for="password">Mật khẩu</label>
          <input id="password" v-model="form.password" type="password" placeholder="Nhập mật khẩu" />
        </div>
        <button class="btn" type="submit">Đăng nhập</button>
      </form>

      <p v-if="errorMessage" class="error" style="margin-top: 10px;">
        {{ errorMessage }}
      </p>

      <div class="auth-footer">
        <p>Chưa có tài khoản?</p>
        <RouterLink to="/register">Đăng ký</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiFetch } from '../../composables/useApi'
import { useAuth } from '../../composables/useAuth'

const router = useRouter()
const { setSession } = useAuth()

const form = ref({
  phone: '',
  password: '',
})

const errorMessage = ref('')

const submitLogin = async () => {
  errorMessage.value = ''
  try {
    const result = await apiFetch('/api/auth/login', {
      method: 'POST',
      body: JSON.stringify(form.value),
    })
    setSession(result.token, result.data)
    if (result.data.role === 'admin') {
      router.push('/admin/dashboard')
    } else {
      router.push('/')
    }
  } catch (error) {
    errorMessage.value = error.message
  }
}
</script>
