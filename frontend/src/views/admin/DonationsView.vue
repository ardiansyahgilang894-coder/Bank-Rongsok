<template>
  <AdminLayout>
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-md-6">
          <h2>Dokumentasi Penyaluran Bantuan</h2>
        </div>
        <div class="col-md-6 text-end">
          <button class="btn btn-primary" @click="showCreateModal = true">
            <i class="bi bi-plus-lg"></i> Penyaluran Baru
          </button>
        </div>
      </div>

      <!-- Distribution Grid -->
      <div class="row g-4">
        <div v-for="distribution in distributions" :key="distribution.id" class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ distribution.title }}</h5>
              <p class="card-text text-muted">{{ distribution.description || '-' }}</p>
              <div class="mb-3">
                <small class="badge bg-primary">{{ distribution.items.length }} Jenis Barang</small>
                <small class="badge bg-secondary">{{ distribution.recipient_count }} Penerima</small>
              </div>
              <small class="text-muted d-block mb-2">📍 {{ distribution.location }}</small>
              <small class="text-muted d-block mb-3">📅 {{ formatDate(distribution.distribution_date) }}</small>

              <!-- Items List -->
              <div v-if="distribution.items.length > 0" class="mb-3">
                <h6 class="mb-2">
                  <small>Barang yang disalurkan:</small>
                </h6>
                <ul class="list-unstyled small">
                  <li v-for="item in distribution.items" :key="item.id" class="mb-1">
                    • {{ item.item_name }} ({{ item.quantity }} unit)
                  </li>
                </ul>
              </div>

              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" @click="editDistribution(distribution)">
                  Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteDistribution(distribution.id)">
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Create/Edit Distribution -->
      <div class="modal" :class="{ show: showCreateModal }" :style="{ display: showCreateModal ? 'block' : 'none' }">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                {{ editingDistribution ? 'Edit Penyaluran' : 'Penyaluran Bantuan Baru' }}
              </h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveDistribution">
                <div class="mb-3">
                  <label class="form-label">Judul Penyaluran</label>
                  <input v-model="formData.title" type="text" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea v-model="formData.description" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi</label>
                    <input v-model="formData.location" type="text" class="form-control" required />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Penyaluran</label>
                    <input v-model="formData.distribution_date" type="date" class="form-control" required />
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Jumlah Penerima</label>
                  <input v-model.number="formData.recipient_count" type="number" class="form-control" required />
                </div>

                <!-- Items Section -->
                <div class="mb-3">
                  <label class="form-label">Barang yang Disalurkan</label>
                  <div v-if="formData.items.length > 0" class="mb-3">
                    <div v-for="(item, index) in formData.items" :key="index" class="card mb-2">
                      <div class="card-body p-2">
                        <div class="row">
                          <div class="col-md-7">
                            <input
                              v-model="item.item_name"
                              type="text"
                              placeholder="Nama Barang"
                              class="form-control form-control-sm"
                            />
                          </div>
                          <div class="col-md-3">
                            <input
                              v-model.number="item.quantity"
                              type="number"
                              placeholder="Qty"
                              class="form-control form-control-sm"
                            />
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger" @click="removeItem(index)">
                              Hapus
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-secondary" @click="addItem">
                    + Tambah Barang
                  </button>
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
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import AdminLayout from '../../layouts/AdminLayout.vue'

const distributions = ref([])
const showCreateModal = ref(false)
const editingDistribution = ref(null)
const formData = ref({
  title: '',
  description: '',
  location: '',
  distribution_date: '',
  recipient_count: 0,
  notes: '',
  items: [{ item_name: '', quantity: 1 }],
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const fetchDistributions = async () => {
  try {
    const res = await api.get('/donations')
    distributions.value = res.data.data
  } catch (error) {
    console.error('Error fetching distributions:', error)
  }
}

const editDistribution = (distribution: any) => {
  editingDistribution.value = distribution
  formData.value = {
    ...distribution,
    items: distribution.items || [{ item_name: '', quantity: 1 }],
  }
  showCreateModal.value = true
}

const addItem = () => {
  formData.value.items.push({ item_name: '', quantity: 1 })
}

const removeItem = (index: number) => {
  formData.value.items.splice(index, 1)
}

const saveDistribution = async () => {
  try {
    // Filter out empty items
    const items = formData.value.items.filter((item) => item.item_name)

    const payload = {
      title: formData.value.title,
      description: formData.value.description,
      location: formData.value.location,
      distribution_date: formData.value.distribution_date,
      recipient_count: formData.value.recipient_count,
      notes: formData.value.notes,
      items: items,
    }

    if (editingDistribution.value) {
      await api.put(`/donations/${editingDistribution.value.id}`, payload)
    } else {
      await api.post('/donations', payload)
    }
    fetchDistributions()
    closeModal()
  } catch (error) {
    console.error('Error saving distribution:', error)
  }
}

const deleteDistribution = async (id: number) => {
  if (confirm('Yakin ingin menghapus penyaluran bantuan ini?')) {
    try {
      await api.delete(`/donations/${id}`)
      fetchDistributions()
    } catch (error) {
      console.error('Error deleting distribution:', error)
    }
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingDistribution.value = null
  formData.value = {
    title: '',
    description: '',
    location: '',
    distribution_date: '',
    recipient_count: 0,
    notes: '',
    items: [{ item_name: '', quantity: 1 }],
  }
}

onMounted(() => {
  fetchDistributions()
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
  transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}
</style>
