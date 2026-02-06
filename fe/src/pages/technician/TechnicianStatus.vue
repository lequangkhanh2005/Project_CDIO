<template>
  <TechnicianLayout>
    <section class="tech-page">
      <div class="page-title">
        <h1>Cập nhật trạng thái</h1>
        <p>Cập nhật trạng thái xử lý của job.</p>
      </div>

      <div class="card">
        <div class="field">
          <label for="status">Trạng thái</label>
          <select id="status" v-model="status">
            <option value="Moi">Mới</option>
            <option value="Dang xu ly">Đang xử lý</option>
            <option value="Doi vat tu">Đợi vật tư</option>
            <option value="Hoan thanh">Hoàn thành</option>
          </select>
        </div>
        <button class="btn" @click="submitStatus" :disabled="loading">Cập nhật</button>
      </div>

      <p v-if="errorMessage" class="error" style="margin-top: 12px;">{{ errorMessage }}</p>
    </section>
  </TechnicianLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import TechnicianLayout from '../../layouts/TechnicianLayout.vue'
import { apiFetch } from '../../composables/useApi'

const route = useRoute()
const router = useRouter()
const status = ref('Dang xu ly')
const loading = ref(false)
const errorMessage = ref('')

const submitStatus = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await apiFetch(`/api/jobs/${route.params.id}/status`, {
      method: 'PATCH',
      body: JSON.stringify({ status: status.value }),
    })
    router.push(`/technician/jobs/${route.params.id}`)
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.tech-page {
  display: grid;
  gap: 16px;
}
</style>
