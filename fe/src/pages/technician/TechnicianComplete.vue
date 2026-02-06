<template>
  <TechnicianLayout>
    <section class="tech-page">
      <div class="page-title">
        <h1>Hoàn thành yêu cầu</h1>
        <p>Xác nhận khi đã xử lý xong công việc.</p>
      </div>

      <div class="card">
        <p class="notice">Sau khi hoàn thành, trạng thái job sẽ chuyển sang "Hoàn thành".</p>
        <div class="actions-vertical">
          <button class="btn btn-danger" @click="completeJob" :disabled="loading">Xác nhận hoàn thành</button>
          <button class="btn" @click="goBack">Quay lại</button>
        </div>
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
const loading = ref(false)
const errorMessage = ref('')

const completeJob = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await apiFetch(`/api/jobs/${route.params.id}/complete`, {
      method: 'POST',
    })
    router.push(`/technician/jobs/${route.params.id}`)
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const goBack = () => router.push(`/technician/jobs/${route.params.id}`)
</script>

<style scoped>
.tech-page {
  display: grid;
  gap: 16px;
}

.actions-vertical {
  display: grid;
  gap: 8px;
}
</style>
