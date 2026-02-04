<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Báo cáo</h1>
      <p>Tổng hợp theo khoảng thời gian.</p>
    </div>

    <div class="card">
      <div class="field">
        <label for="from">Từ ngày</label>
        <input id="from" v-model="filters.from" type="date" />
      </div>
      <div class="field">
        <label for="to">Đến ngày</label>
        <input id="to" v-model="filters.to" type="date" />
      </div>
      <button class="btn" @click="loadSummary">Lọc</button>
    </div>

    <div class="content-grid" style="margin-top: 16px;">
      <div class="card">
        <h2>Tổng job</h2>
        <p class="metric">{{ summary.jobs_total }}</p>
      </div>
      <div class="card">
        <h2>Hoàn thành</h2>
        <p class="metric">{{ summary.jobs_completed }}</p>
      </div>
      <div class="card">
        <h2>Chi phí</h2>
        <p class="metric">{{ formatMoney(summary.worklog_cost_total) }}</p>
      </div>
      <div class="card">
        <h2>Doanh thu</h2>
        <p class="metric">{{ formatMoney(summary.invoice_paid) }}</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const summary = ref({
  jobs_total: 0,
  jobs_completed: 0,
  worklog_cost_total: 0,
  invoice_total: 0,
  invoice_paid: 0,
})

const filters = ref({
  from: '',
  to: '',
})

const formatMoney = (value) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0)

const loadSummary = async () => {
  const params = new URLSearchParams()
  if (filters.value.from) params.append('from', filters.value.from)
  if (filters.value.to) params.append('to', filters.value.to)
  const result = await apiFetch(`/api/reports/summary?${params.toString()}`)
  summary.value = result.data
}

loadSummary()
</script>
