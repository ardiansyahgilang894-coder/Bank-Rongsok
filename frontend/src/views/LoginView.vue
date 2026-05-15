<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <div class="card shadow border-0">
                    <div class="card-body">

                        <h3 class="text-center mb-4">
                            Login Admin
                        </h3>

                        <form @submit.prevent="login">

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" v-model="form.email" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" v-model="form.password" class="form-control">
                            </div>

                            <button class="btn btn-success w-100">
                                Login
                            </button>

                            <div class="text-center mt-3">

                                Belum punya akun?

                                <router-link to="/register">
                                    Register
                                </router-link>

                            </div>

                            <div class="text-center mt-2">
                                <router-link :to="{ path: '/verify-otp', query: { email: form.email } }">
                                    Verifikasi OTP
                                </router-link>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

import { reactive } from 'vue'
import api from '../services/api'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
    email: '',
    password: ''
})

const login = async () => {

    // VALIDASI KOSONG

    if (!form.email || !form.password) {

        Swal.fire({
            icon: 'warning',
            title: 'Form wajib diisi'
        })

        return
    }

    // VALIDASI FORMAT EMAIL

    const emailRegex =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/

    if (!emailRegex.test(form.email)) {

        Swal.fire({
            icon: 'warning',
            title: 'Format email tidak valid'
        })

        return
    }

    try {

        const response = await api.post(
            '/login',
            form
        )

        localStorage.setItem(
            'token',
            response.data.token
        )

        localStorage.setItem(
            'user',
            JSON.stringify(response.data.user)
        )

        Swal.fire({
            icon: 'success',
            title: 'Login berhasil',
            timer: 1500,
            showConfirmButton: false
        })

        router.push('/dashboard')

    }

    catch (error: unknown) {

        let message = 'Login gagal'
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

        if (status === 403 && message === 'Akun belum diverifikasi OTP') {
            Swal.fire({
                icon: 'info',
                title: 'Akun belum diverifikasi',
                text: 'Silakan masukkan OTP yang dikirim ke email Anda',
                timer: 2000,
                showConfirmButton: false
            })

            router.push({
                path: '/verify-otp',
                query: {
                    email: form.email
                }
            })

            return
        }

        Swal.fire({
            icon: 'error',
            title: message
        })

    }

}

</script>