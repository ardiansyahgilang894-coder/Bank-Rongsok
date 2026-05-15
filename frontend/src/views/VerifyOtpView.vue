<template>

    <div class="container">

        <div class="row justify-content-center min-vh-100 align-items-center">

            <div class="col-md-4">

                <div class="card border-0 shadow-lg">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">

                            <h3>Verifikasi OTP</h3>

                            <p class="text-muted">
                                Masukkan kode OTP yang dikirim ke email
                            </p>

                        </div>

                        <form @submit.prevent="verifyOtp">

                            <div class="mb-3">

                                <input type="text" class="form-control text-center otp-input" placeholder="Masukkan OTP"
                                    v-model="otp" maxlength="6">

                            </div>

                            <button class="btn btn-success w-100" :disabled="loading">

                                <span v-if="loading" class="spinner-border spinner-border-sm"></span>

                                <span v-else>
                                    Verifikasi
                                </span>

                            </button>

                        </form>

                        <div class="text-center mt-3">

                            <button class="btn btn-link" :disabled="countdown > 0" @click="resendOtp">

                                <span v-if="countdown > 0">
                                    Kirim ulang dalam {{ countdown }}s
                                </span>

                                <span v-else>
                                    Kirim ulang OTP
                                </span>

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>

<script setup lang="ts">

import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const otp = ref('')
const loading = ref(false)
const countdown = ref(60)

const email =
    route.query.email as string

const startCountdown = () => {

    const timer = setInterval(() => {

        countdown.value--

        if (countdown.value <= 0) {
            clearInterval(timer)
        }

    }, 1000)

}

onMounted(() => {
    startCountdown()
})

const verifyOtp = async () => {

    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Email tidak tersedia',
            text: 'Silakan kembali ke halaman register atau login.'
        })

        router.push('/register')
        return
    }

    if (!otp.value) {

        Swal.fire({
            icon: 'warning',
            title: 'OTP wajib diisi'
        })

        return
    }

    loading.value = true

    try {

        await api.post('/verify-otp', {
            email,
            otp: otp.value
        })

        Swal.fire({
            icon: 'success',
            title: 'Email berhasil diverifikasi',
            timer: 1500,
            showConfirmButton: false
        })

        router.push('/')

    }

    catch (error: unknown) {

        let message = 'Verifikasi gagal'
        let status = 0

        if (
            typeof error === 'object' &&
            error !== null &&
            'response' in error
        ) {

            const err = error as {
                response?: {
                    status?: number
                    data?: {
                        message?: string
                    }
                }
            }

            status = err.response?.status || 0
            message =
                err.response?.data?.message ||
                message
        }

        if (status === 200 && message === 'User sudah diverifikasi') {
            Swal.fire({
                icon: 'success',
                title: 'Akun sudah diverifikasi',
                timer: 1500,
                showConfirmButton: false
            })
            router.push('/')
            return
        }

        Swal.fire({
            icon: 'error',
            title: message
        })

    }

    finally {
        loading.value = false
    }

}

const resendOtp = async () => {

    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Email tidak tersedia',
            text: 'Silakan kembali ke halaman register atau login.'
        })
        router.push('/register')
        return
    }

    try {

        await api.post('/resend-otp', {
            email
        })

        countdown.value = 60

        startCountdown()

        Swal.fire({
            icon: 'success',
            title: 'OTP berhasil dikirim ulang'
        })

    }

    catch {

        Swal.fire({
            icon: 'error',
            title: 'Gagal mengirim ulang OTP'
        })

    }

}

</script>

<style scoped>
.otp-input {
    font-size: 32px;
    letter-spacing: 10px;
    font-weight: bold;
    height: 70px;
}
</style>