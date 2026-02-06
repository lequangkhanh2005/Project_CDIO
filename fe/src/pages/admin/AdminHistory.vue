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
        <p v-if="!histories.length" class="hint">Chưa có lịch sử.</p>
      </div>
    </div>

    <div class="card" style="margin-top: 16px;">
      <h2>Đánh giá khách hàng</h2>
      <div class="feedback-list">
        <div v-for="item in feedbacks" :key="item.id" class="feedback-item">
          <div>
            <p class="feedback-name">{{ item.resident_name || 'Khách hàng' }}</p>
            <p class="feedback-meta">{{ formatDate(item.created_at) }}</p>
          </div>
          <div class="feedback-rating">{{ formatRating(item.rating) }}</div>
          <p class="feedback-comment">{{ item.comment || 'Không có bình luận' }}</p>
        </div>
        <p v-if="!feedbacks.length" class="hint">Chưa có đánh giá nào.</p>
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
const feedbacks = ref([])
const filters = ref({ job_id: '' })

const formatDate = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

const formatRating = (value) => {
  if (!value) return 'Chưa đánh giá'
  return `${value}/5`
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

const loadFeedbacks = async () => {
  const result = await apiFetch('/api/feedbacks')
  feedbacks.value = result.data || []
}

onMounted(async () => {
  await loadJobs()
  await loadHistories()
  await loadFeedbacks()
})
</script>

<style scoped>
.feedback-list {
  display: grid;
  gap: 10px;
  margin-top: 10px;
}

.feedback-item {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 10px 12px;
  display: grid;
  gap: 6px;
  background: #fff;
}

.feedback-name {
  margin: 0;
  font-weight: 600;
}

.feedback-meta {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}

.feedback-rating {
  font-weight: 700;
  color: #1d4ed8;
}

.feedback-comment {
  margin: 0;
  color: #334155;
  font-size: 14px;
}
</style>