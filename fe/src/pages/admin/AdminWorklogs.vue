<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Worklog & Chi phí</h1>
      <p>Quản lý nhật ký công việc và chi phí phát sinh.</p>
    </div>

    <div class="content-grid">
      <div class="card span-two">
        <h2>Danh sách worklog</h2>
        <div class="job-list">
          <div
            v-for="log in worklogs"
            :key="log.id"
            class="job-item"
            :class="{ active: selectedLog?.id === log.id }"
            @click="selectLog(log)"
          >
            <p class="job-item-title">Job #{{ log.maintenance_job_id }}</p>
            <div class="job-meta">
              <span>{{ formatMoney(log.cost) }}</span>
              <span>{{ formatDate(log.logged_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Cập nhật worklog</h2>
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
        <button class="btn" @click="updateLog" :disabled="!selectedLog">Cập nhật</button>
        <button class="btn btn-danger" style="margin-top: 8px;" @click="deleteLog" :disabled="!selectedLog">
          Xóa worklog
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const worklogs = ref([])
const selectedLog = ref(null)
const form = ref({
  hours: 0,
  cost: 0,
  note: '',
})

const formatMoney = (value) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0)

const formatDate = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

const loadWorklogs = async () => {
  const result = await apiFetch('/api/worklogs')
  worklogs.value = result.data
}

const updateLog = async () => {
  if (!selectedLog.value) return
  await apiFetch(`/api/worklogs/${selectedLog.value.id}`, {
    method: 'PUT',
    body: JSON.stringify(form.value),
  })
  await loadWorklogs()
}

const deleteLog = async () => {
  if (!selectedLog.value) return
  await apiFetch(`/api/worklogs/${selectedLog.value.id}`, { method: 'DELETE' })
  selectedLog.value = null
  form.value = { hours: 0, cost: 0, note: '' }
  await loadWorklogs()
}

const selectLog = (log) => {
  selectedLog.value = log
  form.value = {
    hours: log.hours,
    cost: log.cost,
    note: log.note || '',
  }
}

onMounted(loadWorklogs)
</script>
