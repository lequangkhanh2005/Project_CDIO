<template>
  <TechnicianLayout>
    <section class="tech-page">
      <div class="page-title">
        <h1>Ghi worklog</h1>
        <p>Nhập thời gian và chi phí cho job.</p>
      </div>

      <div class="card">
        <div class="field">
          <label for="hours">Số giờ</label>
          <input id="hours" v-model="form.hours" type="number" min="0" step="0.5" />
        </div>
        <div class="field">
          <label for="cost">Chi phí</label>
          <input id="cost" v-model="form.cost" type="number" min="0" step="1000" />
        </div>
        <div class="field">
          <label for="note">Ghi chú</label>
          <textarea id="note" v-model="form.note"></textarea>
        </div>
        <button class="btn btn-secondary" @click="submitWorklog" :disabled="loading">Lưu worklog</button>
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

const form = ref({
  hours: 1,
  cost: 0,
  note: '',
})

const submitWorklog = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await apiFetch(`/api/jobs/${route.params.id}/worklogs`, {
      method: 'POST',
      body: JSON.stringify(form.value),
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
