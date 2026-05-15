<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <StatCard title="Total Kegiatan" :value="`${analytics.activities?.total || 0}`"
                        icon="bi bi-calendar2-event" />
                </div>
                <div class="col-md-3">
                    <StatCard title="Penjualan Rongsok"
                        :value="`Rp ${formatCurrency(analytics.scrap_sales?.total_sales || 0)}`" icon="bi bi-recycle" />
                </div>
                <div class="col-md-3">
                    <StatCard title="Dana Terkumpul" :value="`Rp ${formatCurrency(analytics.funds?.total_raised || 0)}`"
                        icon="bi bi-cash-stack" />
                </div>
                <div class="col-md-3">
                    <StatCard title="Penerima Bantuan" :value="`${analytics.donations?.total_recipients || 0}`"
                        icon="bi bi-heart-fill" />
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Grafik Penjualan Rongsok Bulanan</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-white-50">Tahun</span>
                                <select v-model="selectedYear" @change="fetchMonthlyRevenue"
                                    class="form-select form-select-sm w-auto">
                                    <option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="monthlyRevenue.length > 0">

                                <div class="chart-summary d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <div class="text-secondary">Total Pendapatan</div>
                                        <div class="h4 mb-0">
                                            Rp {{ formatCurrency(totalMonthlyRevenue) }}
                                        </div>
                                    </div>
                                </div>

                                <apexchart type="area" height="350" :options="chartOptions" :series="chartSeries" />

                            </div>
                            <p v-else class="text-muted">Data penjualan bulanan belum tersedia.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Activities -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Kegiatan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="recentActivities.length > 0" class="list-group">
                                <div v-for="activity in recentActivities" :key="activity.id" class="list-group-item">
                                    <h6 class="mb-1">{{ activity.title }}</h6>
                                    <small class="text-muted d-block">{{ formatDate(activity.date) }}</small>
                                    <small class="text-muted">{{ activity.location }}</small>
                                </div>
                            </div>
                            <p v-else class="text-muted">Tidak ada kegiatan terbaru</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Distributions -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Penyaluran Bantuan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="recentDistributions.length > 0" class="list-group">
                                <div v-for="distribution in recentDistributions" :key="distribution.id"
                                    class="list-group-item">
                                    <h6 class="mb-1">{{ distribution.title }}</h6>
                                    <small class="text-muted d-block">
                                        {{ formatDate(distribution.distribution_date) }}
                                    </small>
                                    <small class="text-muted">
                                        {{ distribution.recipient_count }} penerima
                                    </small>
                                </div>
                            </div>
                            <p v-else class="text-muted">Tidak ada penyaluran bantuan terbaru</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'
import AdminLayout from '../../layouts/AdminLayout.vue'
import StatCard from '../../components/admin/StatCard.vue'
import type { ApexOptions } from 'apexcharts'

interface AnalyticsData {
    activities?: {
        total: number
        ongoing: number
        completed: number
    }
    funds?: {
        active_funds: number
        total_raised: number
        total_target: number
        progress_percentage: number
    }
    scrap_sales?: {
        total_sales: number
        total_quantity: number
        this_month_sales: number
    }
    donations?: {
        total_distributions: number
        total_recipients: number
    }
}

interface RecentActivity {
    id: number
    title: string
    date: string
    location: string
}

interface RecentDistribution {
    id: number
    title: string
    distribution_date: string
    recipient_count: number
}

interface MonthlyRevenuePoint {
    month: number
    amount: number
}

const analytics = ref<AnalyticsData>({})
const recentActivities = ref<RecentActivity[]>([])
const recentDistributions = ref<RecentDistribution[]>([])
const monthlyRevenue = ref<MonthlyRevenuePoint[]>([])
const selectedYear = ref<number>(new Date().getFullYear())
const yearOptions = ref<number[]>([])
const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']


const fetchRevenueYears = async () => {
    try {

        const res = await api.get('/dashboard/revenue-years')

        yearOptions.value = res.data

    } catch (error) {

        console.error('Error fetching years:', error)

    }
}

const monthLabel = (month: number) => {
    return monthNames[month - 1] || ''
}

const formatCurrency = (value: number): string => {
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

const totalMonthlyRevenue = computed(() => {
    return monthlyRevenue.value.reduce((sum, item) => sum + item.amount, 0)
})

const fetchDashboardData = async () => {
    try {
        const analyticsRes = await api.get('/dashboard/analytics')
        const activitiesRes = await api.get('/dashboard/recent-activities')
        const distributionsRes = await api.get('/dashboard/recent-distributions')

        analytics.value = analyticsRes.data
        recentActivities.value = activitiesRes.data
        recentDistributions.value = distributionsRes.data
    } catch (error) {
        console.error('Error fetching dashboard data:', error)
    }
}

const fetchMonthlyRevenue = async () => {
    try {
        const res = await api.get('/dashboard/monthly-revenue', {
            params: { year: selectedYear.value },
        })
        monthlyRevenue.value = res.data.map((item: MonthlyRevenuePoint) => ({
            month: item.month,
            amount: Number(item.amount),
        }))
    } catch (error) {
        console.error('Error fetching monthly revenue:', error)
    }
}


const chartSeries = computed(() => [
    {
        name: 'Pendapatan',
        data: monthlyRevenue.value.map(item => item.amount)
    }
])

const chartOptions = computed<ApexOptions>(() => ({
    chart: {
        toolbar: { show: false },
        zoom: { enabled: false },
    },

    dataLabels: {
        enabled: false,
    },

    stroke: {
        curve: 'smooth',
        width: 3,
    },

    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.6,
            opacityTo: 0.2,
        },
    },

    colors: ['#0d6efd'],

    xaxis: {
        categories: monthlyRevenue.value.map(item =>
            monthLabel(item.month)
        ),
    },

    yaxis: {
        labels: {
            formatter: (value: number): string => {
                return 'Rp ' + formatCurrency(value)
            },
        },
    },

    tooltip: {
        y: {
            formatter: (value: number): string => {
                return 'Rp ' + formatCurrency(value)
            },
        },
    },

    grid: {
        borderColor: '#e9ecef',
    },
}))


onMounted(() => {
    fetchDashboardData()
    fetchMonthlyRevenue()
    fetchRevenueYears()
})
</script>

<style scoped>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.list-group-item {
    border-left: 4px solid #0d6efd;
    padding: 0.75rem 1rem;
}

.revenue-chart {
    min-height: 320px;
}

.chart-summary {
    gap: 1rem;
}

.bar {
    background: linear-gradient(180deg, #0d6efd, #7abfff);
    border-radius: 16px 16px 0 0;
    position: relative;
    transition: height 0.4s ease;
    min-height: 24px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding-bottom: 8px;
    color: white;
    font-size: 0.75rem;
}

.chart-bar-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.bar {
    background: linear-gradient(180deg, #0d6efd, #7abfff);
    border-radius: 16px 16px 0 0;
    position: relative;
    transition: height 0.4s ease;
    min-height: 24px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding-bottom: 8px;
    color: white;
    font-size: 0.75rem;
}

.bar-value {
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    opacity: 0.85;
    font-size: 0.72rem;
}

.chart-bar-item small {
    display: block;
    margin-top: 0.75rem;
    color: #6c757d;
}

.card-header.bg-info {
    background: #0dcaf0;
}
</style>