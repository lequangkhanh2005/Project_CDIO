<template>
  <TechnicianLayout>
    <section class="tech-page">
      <div class="page-title">
        <h1>Chi tiết công việc</h1>
        <p>Xem thông tin cư dân, lịch hẹn và mô tả công việc.</p>
      </div>

      <div class="content-grid">
        <div class="card span-two">
          <h2>Chi tiết công việc</h2>
          <div v-if="job" class="detail-grid">
            <div>
              <p class="detail-label">Mã job</p>
              <p class="detail-value">{{ job.code }}</p>
            </div>
            <div>
              <p class="detail-label">Trạng thái</p>
              <p class="detail-value">{{ job.status }}</p>
            </div>
            <div>
              <p class="detail-label">Ngày đặt</p>
              <p class="detail-value">{{ formatDateTime(job.appointment_at) }}</p>
            </div>
            <div>
              <p class="detail-label">Địa chỉ / số phòng</p>
              <p class="detail-value">{{ job.location }}</p>
            </div>
            <div>
              <p class="detail-label">Chủ hộ</p>
              <p class="detail-value">{{ job.resident_name }}</p>
            </div>
            <div>
              <p class="detail-label">Số điện thoại</p>
              <p class="detail-value">{{ job.resident_phone }}</p>
            </div>
          </div>

          <div v-if="job" class="detail-block">
            <p class="detail-label">Hỏng gì / mô tả</p>
            <p class="detail-value">{{ job.description || 'Không có mô tả' }}</p>
          </div>

          <div v-if="job" class="detail-block">
            <p class="detail-label">Ảnh báo hỏng</p>
            <div v-if="job.report_image" class="image-grid">
              <img :src="job.report_image" alt="Ảnh báo hỏng" />
            </div>
            <p v-else class="notice">Chưa có ảnh báo hỏng.</p>
          </div>

          <div v-if="job" class="detail-block">
            <p class="detail-label">Ảnh hoàn thành</p>
            <div v-if="job.completion_image" class="image-grid">
              <img :src="job.completion_image" alt="Ảnh hoàn thành" />
            </div>
            <p v-else class="notice">Chưa có ảnh hoàn thành.</p>
          </div>

          <p v-if="!job && !loading" class="notice">Không tìm thấy job.</p>
        </div>

        <div class="card">
          <h2>Thông tin liên hệ</h2>
          <div v-if="job" class="contact-card">
            <div>
              <p class="detail-label">Chủ hộ</p>
              <p class="detail-value">{{ job.resident_name }}</p>
            </div>
            <div>
              <p class="detail-label">Số điện thoại</p>
              <p class="detail-value">{{ job.resident_phone }}</p>
            </div>
            <div>
              <p class="detail-label">Địa chỉ / số phòng</p>
              <p class="detail-value">{{ job.location }}</p>
            </div>
            <a v-if="job.resident_phone" class="btn btn-secondary" :href="`tel:${job.resident_phone}`">
              Gọi cư dân
            </a>
          </div>
        </div>

        <div class="card">
          <h2>Đánh giá khách hàng</h2>
          <div class="feedback-list">
            <div v-for="item in feedbacks" :key="item.id" class="feedback-item">
              <div>
                <p class="feedback-name">{{ item.resident_name || 'Khách hàng' }}</p>
                <p class="feedback-meta">{{ formatDateTime(item.created_at) }}</p>
              </div>
              <div class="feedback-rating">{{ formatRating(item.rating) }}</div>
              <p class="feedback-comment">{{ item.comment || 'Không có bình luận' }}</p>
            </div>
            <p v-if="!feedbacks.length && !loading" class="notice">Chưa có đánh giá.</p>
          </div>
        </div>

        <div class="card">
          <h2>Lịch sử xử lý</h2>
          <div class="timeline">
            <div v-for="item in histories" :key="item.id" class="timeline-item">
              <div class="timeline-time">{{ formatDateTime(item.created_at) }}</div>
              <div class="timeline-content">
                <strong>{{ item.action }}</strong>
                <p v-if="item.note" class="hint">{{ item.note }}</p>
              </div>
            </div>
            <p v-if="!histories.length && !loading" class="notice">Chưa có lịch sử.</p>
          </div>
        </div>
      </div>

      <p v-if="errorMessage" class="error" style="margin-top: 12px;">{{ errorMessage }}</p>
    </section>
  </TechnicianLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import TechnicianLayout from '../../layouts/TechnicianLayout.vue'
import { apiFetch } from '../../composables/useApi'

const route = useRoute()
const router = useRouter()
const job = ref(null)
const loading = ref(false)
const errorMessage = ref('')
const histories = ref([])
const feedbacks = ref([])

const formatDateTime = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

const formatRating = (value) => {
  if (!value) return 'Chưa đánh giá'
  return `${value}/5`
}

const loadJob = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const result = await apiFetch(`/api/jobs/${route.params.id}`)
    job.value = result.data
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const loadHistory = async () => {
  try {
    const result = await apiFetch(`/api/histories?job_id=${route.params.id}`)
    histories.value = result.data || []
  } catch (error) {
    errorMessage.value = error.message
  }
}

const loadFeedbacks = async () => {
  if (!job.value) return
  const params = new URLSearchParams()
  if (job.value.resident_phone) {
    params.append('resident_phone', job.value.resident_phone)
  } else if (job.value.resident_name) {
    params.append('resident_name', job.value.resident_name)
  }
  const result = await apiFetch(`/api/feedbacks?${params.toString()}`)
  feedbacks.value = result.data || []
}

onMounted(async () => {
  await loadJob()
  await loadHistory()
  await loadFeedbacks()
})
</script>

<style scoped>
.tech-page {
  display: grid;
  gap: 16px;
}

.image-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 8px;
}

.image-grid img {
  width: 100%;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}

.contact-card {
  display: grid;
  gap: 10px;
}

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

.timeline {
  display: grid;
  gap: 12px;
}

.timeline-item {
  display: grid;
  gap: 4px;
  padding: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #f8fafc;
}

.timeline-time {
  font-size: 12px;
  color: #64748b;
}
</style>
