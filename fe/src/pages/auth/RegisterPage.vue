<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-header">
        <div class="brand-icon">CM</div>
        <div>
          <h1>Đăng ký</h1>
          <p>Tạo tài khoản kỹ thuật viên mới.</p>
        </div>
      </div>

      <form class="auth-form" @submit.prevent="submitRegister">
        <div class="field">
          <label for="name">Họ và tên</label>
          <input id="name" v-model="form.name" type="text" placeholder="Nhập họ và tên" />
        </div>
        <div class="field">
          <label for="phone">Số điện thoại</label>
          <input id="phone" v-model="form.phone" type="tel" placeholder="Nhập số điện thoại" />
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" placeholder="Nhập email" />
        </div>
        <div class="field">
          <label for="password">Mật khẩu</label>
          <input id="password" v-model="form.password" type="password" placeholder="Tạo mật khẩu" />
        </div>
        <button class="btn" type="submit">Đăng ký</button>
      </form>

      <p v-if="errorMessage" class="error" style="margin-top: 10px;">
        {{ errorMessage }}
      </p>

      <div class="auth-footer">
        <p>Đã có tài khoản?</p>
        <RouterLink to="/login">Đăng nhập</RouterLink>
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
  name: '',
  phone: '',
  email: '',
  password: '',
  role: 'technician',
})

const errorMessage = ref('')

const submitRegister = async () => {
  errorMessage.value = ''
  try {
    const result = await apiFetch('/api/auth/register', {
      method: 'POST',
      body: JSON.stringify(form.value),
    })
    setSession(result.token, result.data)
    router.push('/')
  } catch (error) {
    errorMessage.value = error.message
  }
}
</script>
