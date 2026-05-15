<template>
    <AdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2>Detail Kegiatan</h2>
                    <p class="text-muted">Lihat informasi dan galeri foto lengkap untuk kegiatan ini.</p>
                </div>
                <div class="col-md-4 text-end align-self-center">
                    <router-link to="/activities" class="btn btn-outline-secondary">
                        Kembali ke Kegiatan
                    </router-link>
                </div>
            </div>

            <div v-if="activity" class="row g-4">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>{{ activity.title }}</h4>
                            <p class="text-muted">{{ formatDate(activity.date) }} • {{ activity.location || 'Lokasitidak tersedia' }}</p>
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">{{ activity.status }}</span>
                                <span class="badge bg-secondary">{{ activity.images.length }} Foto</span>
                            </div>
                            <p>{{ activity.description }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <strong>Informasi</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>Tanggal:</strong> {{ formatDate(activity.date) }}
                            </div>
                            <div class="mb-2">
                                <strong>Lokasi:</strong> {{ activity.location || '—' }}
                            </div>
                            <div>
                                <strong>Status:</strong> {{ activity.status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Galeri Foto Kegiatan</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="activity.images.length > 0" class="row g-3">
                                <div v-for="image in activity.images" :key="image.id" class="col-md-6">
                                    <div class="gallery-card position-relative">
                                        <img :src="getImageUrl(image.image_path)" alt="Foto kegiatan"
                                            class="img-fluid rounded" />
                                        <button type="button" class="btn btn-sm btn-danger delete-photo"
                                            @click="deleteImage(image)">
                                            Hapus
                                        </button>
                                        <div class="gallery-caption mt-2">
                                            {{ image.caption || 'Foto Kegiatan' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-muted py-5">
                                Belum ada foto untuk kegiatan ini.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center text-muted mt-5">
                Memuat detail kegiatan...
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'
import AdminLayout from '../../layouts/AdminLayout.vue'

interface ActivityImage {
    id: number
    image_path: string
    caption?: string
}

interface ActivityType {
    id: number
    title: string
    description: string
    date: string
    location: string
    status: string
    images: ActivityImage[]
}

const route = useRoute()
const router = useRouter()
const activity = ref<ActivityType | null>(null)

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

const fetchActivity = async () => {
    const id = route.params.id
    if (!id) {
        router.push('/activities')
        return
    }

    try {
        const res = await api.get(`/activities/${id}`)
        activity.value = res.data
    } catch (error) {
        console.error('Error fetching activity detail:', error)
        router.push('/activities')
    }
}

const deleteImage = async (image: ActivityImage) => {
    if (!activity.value) {
        return
    }

    if (!confirm('Yakin ingin menghapus foto ini?')) {
        return
    }

    try {
        await api.delete(`/activities/${activity.value.id}/images/${image.id}`)
        fetchActivity()
    } catch (error) {
        console.error('Error deleting image:', error)
        alert('Gagal menghapus foto.')
    }
}

onMounted(() => {
    fetchActivity()
})
</script>

<style scoped>
.gallery-card {
    border: 1px solid #e9ecef;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.gallery-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
}

.gallery-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.delete-photo {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
    padding: 0.35rem 0.65rem;
}
</style>
