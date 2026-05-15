<template>
    <AdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Transparansi Dana</h2>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" @click="showCreateModal = true">
                        <i class="bi bi-plus-lg"></i> Program Dana Baru
                    </button>
                </div>
            </div>

            <!-- Fund Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Program Aktif</h5>
                            <h3 class="text-primary">{{ stats.active_funds || 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Dana Terkumpul</h5>
                            <h3 class="text-success">Rp {{ formatCurrency(stats.total_raised || 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Target Dana</h5>
                            <h3 class="text-warning">Rp {{ formatCurrency(stats.total_target || 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Progress</h5>
                            <h3 class="text-info">{{ stats.progress_percentage || 0 }}%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Funds Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Program Dana</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Judul Program</th>
                                <th>Target</th>
                                <th>Terkumpul</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Tanggal Mulai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="fund in funds" :key="fund.id">
                                <td>
                                    <strong>{{ fund.title }}</strong>
                                    <br />
                                    <small class="text-muted">{{ fund.description }}</small>
                                </td>
                                <td>Rp {{ formatCurrency(fund.target_amount) }}</td>
                                <td>Rp {{ formatCurrency(fund.current_amount) }}</td>
                                <td>
                                    <div class="progress" style="height: 20px">
                                        <div class="progress-bar" :style="{
                                            width: `${fund.target_amount > 0 ? Math.round((fund.current_amount / fund.target_amount) * 100) : 0}%`,
                                        }">
                                            {{ fund.target_amount > 0 ? Math.round((fund.current_amount /
                                            fund.target_amount) * 100) : 0 }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small :class="{
                                        'badge bg-success': fund.status === 'active',
                                        'badge bg-warning': fund.status === 'inactive',
                                        'badge bg-info': fund.status === 'completed',
                                    }">
                                        {{ fund.status }}
                                    </small>
                                </td>
                                <td>{{ formatDate(fund.start_date) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" @click="editFund(fund)">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" @click="deleteFund(fund.id)">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Create/Edit Fund -->
            <div class="modal" :class="{ show: showCreateModal }"
                :style="{ display: showCreateModal ? 'block' : 'none' }">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ editingFund ? 'Edit Program Dana' : 'Program Dana Baru' }}</h5>
                            <button type="button" class="btn-close" @click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="saveFund">
                                <div class="mb-3">
                                    <label class="form-label">Judul Program</label>
                                    <input v-model="formData.title" type="text" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea v-model="formData.description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Target Dana (Rp)</label>
                                        <input v-model.number="formData.target_amount" type="number"
                                            class="form-control" required />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dana Terkumpul (Rp)</label>
                                        <input v-model.number="formData.current_amount" type="number"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input v-model="formData.start_date" type="date" class="form-control"
                                            required />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input v-model="formData.end_date" type="date" class="form-control" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select v-model="formData.status" class="form-control" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="completed">Completed</option>
                                    </select>
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

const funds = ref([])
const stats = ref({})
const showCreateModal = ref(false)
const editingFund = ref(null)
const formData = ref({
    title: '',
    description: '',
    target_amount: 0,
    current_amount: 0,
    status: 'active',
    start_date: '',
    end_date: '',
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

const fetchFunds = async () => {
    try {
        const res = await api.get('/funds')
        funds.value = res.data.data
    } catch (error) {
        console.error('Error fetching funds:', error)
    }
}

const fetchStats = async () => {
    try {
        const res = await api.get('/funds/stats/overview')
        stats.value = res.data
    } catch (error) {
        console.error('Error fetching stats:', error)
    }
}

const editFund = (fund: any) => {
    editingFund.value = fund
    formData.value = { ...fund }
    showCreateModal.value = true
}

const saveFund = async () => {
    try {
        if (editingFund.value) {
            await api.put(`/funds/${editingFund.value.id}`, formData.value)
        } else {
            await api.post('/funds', formData.value)
        }
        fetchFunds()
        fetchStats()
        closeModal()
    } catch (error) {
        console.error('Error saving fund:', error)
    }
}

const deleteFund = async (id: number) => {
    if (confirm('Yakin ingin menghapus program dana ini?')) {
        try {
            await api.delete(`/funds/${id}`)
            fetchFunds()
            fetchStats()
        } catch (error) {
            console.error('Error deleting fund:', error)
        }
    }
}

const closeModal = () => {
    showCreateModal.value = false
    editingFund.value = null
    formData.value = {
        title: '',
        description: '',
        target_amount: 0,
        current_amount: 0,
        status: 'active',
        start_date: '',
        end_date: '',
    }
}

onMounted(() => {
    fetchFunds()
    fetchStats()
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
