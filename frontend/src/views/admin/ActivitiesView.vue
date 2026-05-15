<template>
    <AdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Galeri Kegiatan</h2>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" @click="showCreateModal = true">
                        <i class="bi bi-plus-lg"></i> Tambah Kegiatan
                    </button>
                </div>
            </div>

            <!-- Activities Grid -->
            <div class="row g-4">
                <div v-for="activity in activities" :key="activity.id" class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div v-if="activity.images.length > 0" class="mb-3">
                                <img :src="getImageUrl(activity.images[0]?.image_path ?? '')" alt="Activity image"
                                    class="img-fluid rounded activity-thumbnail" />
                            </div>
                            <h5 class="card-title">{{ activity.title }}</h5>
                            <p class="card-text text-muted">{{ activity.description.substring(0, 100) }}...</p>
                            <div class="mb-3">
                                <small class="badge bg-primary">{{ activity.status }}</small>
                                <small class="badge bg-secondary">{{ formatDate(activity.date) }}</small>
                            </div>
                            <small class="text-muted d-block mb-2">📍 {{ activity.location }}</small>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-sm btn-outline-primary" @click="editActivity(activity)">
                                    Edit
                                </button>
                                <router-link :to="`/activities/${activity.id}`" class="btn btn-sm btn-outline-info">
                                    Detail
                                </router-link>
                                <button class="btn btn-sm btn-outline-success" @click="openUploadModal(activity)">
                                    Upload Foto
                                </button>
                                <button class="btn btn-sm btn-outline-danger" @click="deleteActivity(activity.id)">
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <div v-if="activity.images.length > 0" class="card-footer bg-light">
                            <div class="d-flex align-items-center justify-content-between">
                                <small>{{ activity.images.length }} Foto</small>
                                <button class="btn btn-sm btn-outline-secondary" @click="openUploadModal(activity)">
                                    Tambah Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Create/Edit Activity -->
            <div class="modal" :class="{ show: showCreateModal }"
                :style="{ display: showCreateModal ? 'block' : 'none' }">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ editingActivity ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h5>
                            <button type="button" class="btn-close" @click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="saveActivity">
                                <div class="mb-3">
                                    <label class="form-label">Judul Kegiatan</label>
                                    <input v-model="formData.title" type="text" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea v-model="formData.description" class="form-control" rows="3"
                                        required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Kegiatan</label>
                                        <input v-model="formData.date" type="date" class="form-control" required />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lokasi</label>
                                        <input v-model="formData.location" type="text" class="form-control" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select v-model="formData.status" class="form-control" required>
                                        <option value="planning">Planning</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
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

            <div class="modal" :class="{ show: showUploadModal }"
                :style="{ display: showUploadModal ? 'block' : 'none' }">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload Foto Kegiatan</h5>
                            <button type="button" class="btn-close" @click="closeUploadModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Pilih Foto</label>
                                <input type="file" accept="image/*" class="form-control" @change="handleFileChange" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Caption (opsional)</label>
                                <input v-model="uploadCaption" type="text" class="form-control"
                                    placeholder="Deskripsi singkat" />
                            </div>
                            <div v-if="uploadPreview" class="mb-3">
                                <label class="form-label">Preview</label>
                                <img :src="uploadPreview" alt="Preview" class="img-fluid rounded" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeUploadModal">Batal</button>
                            <button type="button" class="btn btn-primary" @click="uploadImage"
                                :disabled="uploadLoading">
                                <span v-if="uploadLoading" class="spinner-border spinner-border-sm"></span>
                                <span v-else>Upload</span>
                            </button>
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

interface ActivityType {
    id: number
    title: string
    description: string
    date: string
    location: string
    status: string
    images: Array<{ id: number; image_path: string; caption?: string }>
}

interface ActivityForm {
    title: string
    description: string
    date: string
    location: string
    status: string
}

const activities = ref<ActivityType[]>([])
const showCreateModal = ref(false)
const showUploadModal = ref(false)
const editingActivity = ref<ActivityType | null>(null)
const uploadTarget = ref<ActivityType | null>(null)
const uploadFile = ref<File | null>(null)
const uploadCaption = ref('')
const uploadPreview = ref<string | null>(null)
const uploadLoading = ref(false)
const formData = ref<ActivityForm>({
    title: '',
    description: '',
    date: '',
    location: '',
    status: 'planning',
})

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const getImageUrl = (path: string) => {
    const base = api.defaults.baseURL ? api.defaults.baseURL.replace(/\/api$/, '') : ''
    return `${base}/storage/${path}`
}

const fetchActivities = async () => {
    try {
        const res = await api.get('/activities')
        activities.value = res.data.data
    } catch (error) {
        console.error('Error fetching activities:', error)
    }
}

const editActivity = (activity: ActivityType) => {
    editingActivity.value = activity
    formData.value = {
        title: activity.title,
        description: activity.description,
        date: activity.date,
        location: activity.location,
        status: activity.status,
    }
    showCreateModal.value = true
}

const openUploadModal = (activity: ActivityType) => {
    uploadTarget.value = activity
    uploadFile.value = null
    uploadCaption.value = ''
    uploadPreview.value = null
    showUploadModal.value = true
}

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement
    const file = input.files?.[0] ?? null
    uploadFile.value = file

    if (file) {
        uploadPreview.value = URL.createObjectURL(file)
    } else {
        uploadPreview.value = null
    }
}

const uploadImage = async () => {
    if (!uploadTarget.value || !uploadFile.value) {
        alert('Silakan pilih foto terlebih dahulu.')
        return
    }

    uploadLoading.value = true

    try {
        const formDataUpload = new FormData()
        formDataUpload.append('image', uploadFile.value)
        if (uploadCaption.value) {
            formDataUpload.append('caption', uploadCaption.value)
        }

        await api.post(
            `/activities/${uploadTarget.value.id}/upload-image`,
            formDataUpload,
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }
        )

        fetchActivities()
        closeUploadModal()
    } catch (error) {
        console.error('Error uploading image:', error)
        alert('Gagal upload foto kegiatan.')
    } finally {
        uploadLoading.value = false
    }
}

const saveActivity = async () => {
    try {
        if (editingActivity.value) {
            await api.put(`/activities/${editingActivity.value.id}`, formData.value)
        } else {
            await api.post('/activities', formData.value)
        }
        fetchActivities()
        closeModal()
    } catch (error) {
        console.error('Error saving activity:', error)
    }
}

const deleteActivity = async (id: number) => {
    if (confirm('Yakin ingin menghapus kegiatan ini?')) {
        try {
            await api.delete(`/activities/${id}`)
            fetchActivities()
        } catch (error) {
            console.error('Error deleting activity:', error)
        }
    }
}

const closeUploadModal = () => {
    showUploadModal.value = false
    uploadTarget.value = null
    uploadFile.value = null
    uploadCaption.value = ''
    uploadPreview.value = null
}

const closeModal = () => {
    showCreateModal.value = false
    editingActivity.value = null
    formData.value = {
        title: '',
        description: '',
        date: '',
        location: '',
        status: 'planning',
    }
}

onMounted(() => {
    fetchActivities()
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

.activity-thumbnail {
    max-height: 180px;
    width: 100%;
    object-fit: cover;
}
</style>