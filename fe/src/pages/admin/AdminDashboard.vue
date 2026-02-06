<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Dashboard</h1>
      <p>Tổng quan hoạt động bảo trì và tài chính.</p>
    </div>

    <div class="content-grid">
      <div class="card">
        <h2>Job</h2>
        <p class="hint">Tổng job</p>
        <p class="metric">{{ summary.jobs_total }}</p>
      </div>
      <div class="card">
        <h2>Hoàn thành</h2>
        <p class="hint">Job đã hoàn thành</p>
        <p class="metric">{{ summary.jobs_completed }}</p>
      </div>
      <div class="card">
        <h2>Chi phí</h2>
        <p class="hint">Tổng worklog</p>
        <p class="metric">{{ formatMoney(summary.worklog_cost_total) }}</p>
      </div>
      <div class="card">
        <h2>Doanh thu</h2>
        <p class="hint">Hóa đơn đã thu</p>
        <p class="metric">{{ formatMoney(summary.invoice_paid) }}</p>
      </div>
      <div class="card span-two">
        <h2>Đánh giá gần đây</h2>
        <div class="feedback-list">
          <div v-for="item in latestFeedbacks" :key="item.id" class="feedback-item">
            <div>
              <p class="feedback-name">{{ item.resident_name || 'Khách hàng' }}</p>
              <p class="feedback-meta">{{ formatDate(item.created_at) }}</p>
            </div>
            <div class="feedback-rating">{{ formatRating(item.rating) }}</div>
            <p class="feedback-comment">{{ item.comment || 'Không có bình luận' }}</p>
          </div>
          <p v-if="!latestFeedbacks.length" class="hint">Chưa có đánh giá nào.</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const summary = ref({
  jobs_total: 0,
  jobs_completed: 0,
  worklog_cost_total: 0,
  invoice_total: 0,
  invoice_paid: 0,
})
const feedbacks = ref([])

const latestFeedbacks = computed(() => feedbacks.value.slice(0, 5))

const formatMoney = (value) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0)

const formatDate = (value) => (value ? new Date(value).toLocaleString('vi-VN') : '-')

const formatRating = (value) => {
  if (!value) return 'Chưa đánh giá'
  return `${value}/5`
}

const loadSummary = async () => {
  const result = await apiFetch('/api/reports/summary')
  summary.value = result.data
}

const loadFeedbacks = async () => {
  const result = await apiFetch('/api/feedbacks')
  feedbacks.value = result.data || []
}

onMounted(async () => {
  await loadSummary()
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