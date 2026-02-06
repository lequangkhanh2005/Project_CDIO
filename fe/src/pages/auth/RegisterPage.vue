<template>
  <div class="simple-auth">
    <div class="login-card">
      <h1>Register</h1>

      <form class="login-form" @submit.prevent="submitRegister">
        <div class="field">
          <label for="name">Full name</label>
          <input id="name" v-model="form.name" type="text" placeholder="Full name" />
        </div>
        <div class="field">
          <label for="phone">Phone</label>
          <input id="phone" v-model="form.phone" type="tel" placeholder="Phone" />
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" placeholder="Email" />
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" v-model="form.password" type="password" placeholder="Password" />
        </div>
        <button class="btn" type="submit">register</button>
      </form>

      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>

      <div class="footer-links">
        <RouterLink to="/login">Login</RouterLink>
        <span>•</span>
        <a href="javascript:void(0)">Need help?</a>
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
    if (!result?.token || !result?.data) {
      throw new Error('Phản hồi đăng ký không hợp lệ.')
    }
    setSession(result.token, result.data)
    router.push('/tech')
  } catch (error) {
    errorMessage.value = error.message
  }
}
</script>

<style scoped>
.simple-auth {
  min-height: 100vh;
  display: grid;
  place-items: center;
  background: #8b7c73;
  background-image: linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(0, 0, 0, 0.05));
  padding: 24px;
}

.login-card {
  width: min(360px, 100%);
  background: #ffffff;
  border-radius: 4px;
  padding: 28px 24px 20px;
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
  text-align: center;
}

.login-card h1 {
  margin: 0 0 18px;
  font-weight: 500;
  letter-spacing: 0.02em;
  color: #2f3a4a;
}

.login-form {
  display: grid;
  gap: 12px;
}

.field {
  display: grid;
  gap: 6px;
  text-align: left;
}

label {
  font-size: 12px;
  color: #6f7a85;
}

input {
  border: 1px solid #d7d7d7;
  padding: 10px 12px;
  border-radius: 2px;
  outline: none;
}

input:focus {
  border-color: #4f8ef7;
}

.btn {
  border: 0;
  padding: 10px 12px;
  border-radius: 2px;
  background: #4f8ef7;
  color: #fff;
  font-weight: 600;
  text-transform: lowercase;
  cursor: pointer;
}

.error {
  margin-top: 10px;
  color: #c43b3b;
  font-size: 12px;
}

.footer-links {
  margin-top: 16px;
  font-size: 12px;
  color: #7a8792;
  display: flex;
  gap: 6px;
  justify-content: center;
}

.footer-links a {
  color: inherit;
  text-decoration: none;
}

.footer-links a:hover {
  text-decoration: underline;
}
</style>
