<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Quản lý cư dân</h1>
      <p>Quản lý thông tin liên hệ và địa chỉ cư dân.</p>
    </div>

    <div class="content-grid">
      <div class="card span-two">
        <h2>Danh sách cư dân</h2>
        <div class="job-list">
          <div
            v-for="resident in residents"
            :key="resident.id"
            class="job-item"
            :class="{ active: selectedResident?.id === resident.id }"
            @click="selectResident(resident)"
          >
            <p class="job-item-title">{{ resident.name }}</p>
            <div class="job-meta">
              <span>{{ resident.phone }}</span>
              <span>{{ resident.unit || '-' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Thông tin cư dân</h2>
        <div class="field">
          <label for="name">Họ và tên</label>
          <input id="name" v-model="form.name" />
        </div>
        <div class="field">
          <label for="phone">Số điện thoại</label>
          <input id="phone" v-model="form.phone" />
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" />
        </div>
        <div class="field">
          <label for="building">Tòa</label>
          <input id="building" v-model="form.building" />
        </div>
        <div class="field">
          <label for="unit">Căn</label>
          <input id="unit" v-model="form.unit" />
        </div>
        <div class="field">
          <label for="address">Địa chỉ</label>
          <input id="address" v-model="form.address" />
        </div>
        <div class="field">
          <label for="note">Ghi chú</label>
          <textarea id="note" v-model="form.note"></textarea>
        </div>
        <button class="btn" @click="saveResident">{{ selectedResident ? 'Cập nhật' : 'Tạo mới' }}</button>
        <button v-if="selectedResident" class="btn btn-danger" style="margin-top: 8px;" @click="deleteResident">
          Xóa cư dân
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const residents = ref([])
const selectedResident = ref(null)
const form = ref({
  name: '',
  phone: '',
  email: '',
  building: '',
  unit: '',
  address: '',
  note: '',
})

const loadResidents = async () => {
  const result = await apiFetch('/api/residents')
  residents.value = result.data
}

const selectResident = (resident) => {
  selectedResident.value = resident
  form.value = { ...resident }
}

const resetForm = () => {
  selectedResident.value = null
  form.value = {
    name: '',
    phone: '',
    email: '',
    building: '',
    unit: '',
    address: '',
    note: '',
  }
}

const saveResident = async () => {
  if (selectedResident.value) {
    await apiFetch(`/api/residents/${selectedResident.value.id}`, {
      method: 'PUT',
      body: JSON.stringify(form.value),
    })
  } else {
    await apiFetch('/api/residents', {
      method: 'POST',
      body: JSON.stringify(form.value),
    })
  }
  await loadResidents()
  resetForm()
}

const deleteResident = async () => {
  if (!selectedResident.value) return
  await apiFetch(`/api/residents/${selectedResident.value.id}`, { method: 'DELETE' })
  await loadResidents()
  resetForm()
}

onMounted(loadResidents)
</script>
