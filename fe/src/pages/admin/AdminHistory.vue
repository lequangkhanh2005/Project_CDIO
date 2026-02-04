<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Lịch sử</h1>
      <p>Ghi nhận thay đổi trạng thái và thao tác trên job.</p>
    </div>

    <div class="card">
      <div class="field">
        <label for="job">Lọc theo job</label>
        <select id="job" v-model="filters.job_id">
          <option value="">Tất cả</option>
          <option v-for="job in jobs" :key="job.id" :value="job.id">
            {{ job.code }} - {{ job.title }}
          </option>
        </select>
      </div>
      <button class="btn" @click="loadHistories">Lọc</button>
    </div>

    <div class="card" style="margin-top: 16px;">
      <h2>Nhật ký</h2>
      <div class="job-list">
        <div v-for="item in histories" :key="item.id" class="job-item">
          <p class="job-item-title">{{ item.action }}</p>
          <div class="job-meta">
            <span>Job #{{ item.maintenance_job_id }}</span>
            <span>{{ formatDate(item.created_at) }}</span>
          </div>
          <p class="hint" v-if="item.note">{{ item.note }}</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const histories = ref([])
const jobs = ref([])
const filters = ref({ job_id: '' })

const formatDate = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

const loadJobs = async () => {
  const result = await apiFetch('/api/jobs')
  jobs.value = result.data
}

const loadHistories = async () => {
  const params = new URLSearchParams()
  if (filters.value.job_id) params.append('job_id', filters.value.job_id)
  const result = await apiFetch(`/api/histories?${params.toString()}`)
  histories.value = result.data
}

onMounted(async () => {
  await loadJobs()
  await loadHistories()
})
</script>
