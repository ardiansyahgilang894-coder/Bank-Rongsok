<template>
  <AdminLayout>
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-md-6">
          <h2>Laporan Penjualan Rongsok</h2>
        </div>
        <div class="col-md-6 text-end">
          <button class="btn btn-primary" @click="showCreateModal = true">
            <i class="bi bi-plus-lg"></i> Tambah Penjualan
          </button>
        </div>
      </div>

      <!-- Sales Stats -->
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h5>Total Penjualan</h5>
              <h3 class="text-success">Rp {{ formatCurrency(report.total_sales || 0) }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h5>Total Kuantitas</h5>
              <h3 class="text-info">{{ report.total_quantity || 0 }} Unit</h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h5>Total Berat</h5>
              <h3 class="text-warning">{{ report.total_weight_kg || 0 }} Kg</h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h5>Rata-rata Penjualan</h5>
              <h3 class="text-primary">Rp {{ formatCurrency(report.average_per_sale || 0) }}</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <label class="form-label">Dari Tanggal</label>
              <input v-model="filterDateFrom" type="date" class="form-control" @change="fetchReport" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Sampai Tanggal</label>
              <input v-model="filterDateTo" type="date" class="form-control" @change="fetchReport" />
            </div>
            <div class="col-md-4">
              <label class="form-label">&nbsp;</label>
              <button class="btn btn-outline-secondary w-100" @click="resetFilter">Reset Filter</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sales Table -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Daftar Penjualan</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="bg-light">
              <tr>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Kuantitas</th>
                <th>Berat (Kg)</th>
                <th>Harga/Unit</th>
                <th>Total Harga</th>
                <th>Catatan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sale in sales" :key="sale.id">
                <td>{{ formatDate(sale.sale_date) }}</td>
                <td><strong>{{ sale.item_name }}</strong></td>
                <td>{{ sale.quantity }}</td>
                <td>{{ sale.weight_kg || '-' }}</td>
                <td>Rp {{ formatCurrency(sale.price_per_unit) }}</td>
                <td><strong>Rp {{ formatCurrency(sale.total_price) }}</strong></td>
                <td>{{ sale.notes || '-' }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" @click="editSale(sale)">
                    Edit
                  </button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteSale(sale.id)">
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal Create/Edit Sale -->
      <div class="modal" :class="{ show: showCreateModal }" :style="{ display: showCreateModal ? 'block' : 'none' }">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ editingSale ? 'Edit Penjualan' : 'Tambah Penjualan' }}</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveSale">
                <div class="mb-3">
                  <label class="form-label">Tanggal Penjualan</label>
                  <input v-model="formData.sale_date" type="date" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Nama Barang</label>
                  <input v-model="formData.item_name" type="text" class="form-control" required />
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Kuantitas</label>
                    <input v-model.number="formData.quantity" type="number" class="form-control" required />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Berat (Kg)</label>
                    <input v-model.number="formData.weight_kg" type="number" class="form-control" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Harga per Unit (Rp)</label>
                    <input v-model.number="formData.price_per_unit" type="number" class="form-control" required />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Total Harga (Rp)</label>
                    <input
                      v-model.number="formData.total_price"
                      type="number"
                      class="form-control"
                      disabled
                    />
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Catatan</label>
                  <textarea v-model="formData.notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" @click="closeModal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-backdrop fade" :class="{ show: showCreateModal }" v-if="showCreateModal"></div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import api from '../../services/api'
import AdminLayout from '../../layouts/AdminLayout.vue'

const sales = ref([])
const report = ref({})
const showCreateModal = ref(false)
const editingSale = ref(null)
const filterDateFrom = ref('')
const filterDateTo = ref('')
const formData = ref({
  sale_date: '',
  item_name: '',
  quantity: 0,
  weight_kg: 0,
  price_per_unit: 0,
  total_price: 0,
  notes: '',
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const fetchSales = async () => {
  try {
    let url = '/scrap-sales'
    if (filterDateFrom.value && filterDateTo.value) {
      url += `?date_from=${filterDateFrom.value}&date_to=${filterDateTo.value}`
    }
    const res = await api.get(url)
    sales.value = res.data.data
  } catch (error) {
    console.error('Error fetching sales:', error)
  }
}

const fetchReport = async () => {
  try {
    let url = '/scrap-sales/report/overview'
    if (filterDateFrom.value && filterDateTo.value) {
      url += `?date_from=${filterDateFrom.value}&date_to=${filterDateTo.value}`
    }
    const res = await api.get(url)
    report.value = res.data
  } catch (error) {
    console.error('Error fetching report:', error)
  }
}

const resetFilter = () => {
  filterDateFrom.value = ''
  filterDateTo.value = ''
  fetchSales()
  fetchReport()
}

const editSale = (sale: any) => {
  editingSale.value = sale
  formData.value = { ...sale }
  showCreateModal.value = true
}

const saveSale = async () => {
  try {
    formData.value.total_price = formData.value.quantity * formData.value.price_per_unit
    if (editingSale.value) {
      await api.put(`/scrap-sales/${editingSale.value.id}`, formData.value)
    } else {
      await api.post('/scrap-sales', formData.value)
    }
    fetchSales()
    fetchReport()
    closeModal()
  } catch (error) {
    console.error('Error saving sale:', error)
  }
}

const deleteSale = async (id: number) => {
  if (confirm('Yakin ingin menghapus penjualan ini?')) {
    try {
      await api.delete(`/scrap-sales/${id}`)
      fetchSales()
      fetchReport()
    } catch (error) {
      console.error('Error deleting sale:', error)
    }
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingSale.value = null
  formData.value = {
    sale_date: '',
    item_name: '',
    quantity: 0,
    weight_kg: 0,
    price_per_unit: 0,
    total_price: 0,
    notes: '',
  }
}

// Auto calculate total price when quantity or price changes
watch([() => formData.value.quantity, () => formData.value.price_per_unit], () => {
  formData.value.total_price = formData.value.quantity * formData.value.price_per_unit
})

onMounted(() => {
  fetchSales()
  fetchReport()
})
</script>

<style scoped>
.modal.show {
  display: block !important;
  background: rgba(0, 0, 0, 0.5);
}

.card {
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

table tbody tr:hover {
  background-color: #f8f9fa;
}
</style>
