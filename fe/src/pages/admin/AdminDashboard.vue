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
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const summary = ref({
  jobs_total: 0,
  jobs_completed: 0,
  worklog_cost_total: 0,
  invoice_total: 0,
  invoice_paid: 0,
})

const formatMoney = (value) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0)

const loadSummary = async () => {
  const result = await apiFetch('/api/reports/summary')
  summary.value = result.data
}

onMounted(loadSummary)
</script>
