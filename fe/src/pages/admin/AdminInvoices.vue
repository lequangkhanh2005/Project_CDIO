<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Hóa đơn</h1>
      <p>Quản lý hóa đơn và trạng thái thanh toán.</p>
    </div>

    <div class="content-grid">
      <div class="card span-two">
        <h2>Danh sách hóa đơn</h2>
        <div class="job-list">
          <div
            v-for="invoice in invoices"
            :key="invoice.id"
            class="job-item"
            :class="{ active: selectedInvoice?.id === invoice.id }"
            @click="selectInvoice(invoice)"
          >
            <p class="job-item-title">{{ invoice.code }}</p>
            <div class="job-meta">
              <span>{{ invoice.status }}</span>
              <span>{{ formatMoney(invoice.amount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Thông tin hóa đơn</h2>
        <div class="field">
          <label for="code">Mã hóa đơn</label>
          <input id="code" v-model="form.code" />
        </div>
        <div class="field">
          <label for="resident_id">Cư dân</label>
          <select id="resident_id" v-model="form.resident_id">
            <option value="">Chọn cư dân</option>
            <option v-for="resident in residents" :key="resident.id" :value="resident.id">
              {{ resident.name }}
            </option>
          </select>
        </div>
        <div class="field">
          <label for="job_id">Job liên quan</label>
          <select id="job_id" v-model="form.job_id">
            <option value="">Không có</option>
            <option v-for="job in jobs" :key="job.id" :value="job.id">
              {{ job.code }}
            </option>
          </select>
        </div>
        <div class="field">
          <label for="amount">Số tiền</label>
          <input id="amount" v-model="form.amount" type="number" min="0" step="1000" />
        </div>
        <div class="field">
          <label for="status">Trạng thái</label>
          <input id="status" v-model="form.status" />
        </div>
        <div class="field">
          <label for="issued_at">Ngày phát hành</label>
          <input id="issued_at" v-model="form.issued_at" type="datetime-local" />
        </div>
        <div class="field">
          <label for="note">Ghi chú</label>
          <textarea id="note" v-model="form.note"></textarea>
        </div>
        <button class="btn" @click="saveInvoice">{{ selectedInvoice ? 'Cập nhật' : 'Tạo mới' }}</button>
        <button v-if="selectedInvoice" class="btn btn-danger" style="margin-top: 8px;" @click="deleteInvoice">
          Xóa hóa đơn
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const invoices = ref([])
const residents = ref([])
const jobs = ref([])
const selectedInvoice = ref(null)
const form = ref({
  code: '',
  resident_id: '',
  job_id: '',
  amount: 0,
  status: 'Cho thanh toan',
  issued_at: '',
  note: '',
})

const formatMoney = (value) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0)

const loadInvoices = async () => {
  const result = await apiFetch('/api/invoices')
  invoices.value = result.data
}

const loadResidents = async () => {
  const result = await apiFetch('/api/residents')
  residents.value = result.data
}

const loadJobs = async () => {
  const result = await apiFetch('/api/jobs')
  jobs.value = result.data
}

const selectInvoice = (invoice) => {
  selectedInvoice.value = invoice
  form.value = {
    code: invoice.code,
    resident_id: invoice.resident_id,
    job_id: invoice.job_id || '',
    amount: invoice.amount,
    status: invoice.status,
    issued_at: invoice.issued_at ? invoice.issued_at.slice(0, 16) : '',
    note: invoice.note || '',
  }
}

const resetForm = () => {
  selectedInvoice.value = null
  form.value = {
    code: '',
    resident_id: '',
    job_id: '',
    amount: 0,
    status: 'Cho thanh toan',
    issued_at: '',
    note: '',
  }
}

const saveInvoice = async () => {
  const payload = { ...form.value }
  if (selectedInvoice.value) {
    await apiFetch(`/api/invoices/${selectedInvoice.value.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  } else {
    await apiFetch('/api/invoices', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  }
  await loadInvoices()
  resetForm()
}

const deleteInvoice = async () => {
  if (!selectedInvoice.value) return
  await apiFetch(`/api/invoices/${selectedInvoice.value.id}`, { method: 'DELETE' })
  await loadInvoices()
  resetForm()
}

onMounted(async () => {
  await loadInvoices()
  await loadResidents()
  await loadJobs()
})
</script>
