<template>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-4">

                <div class="card border-0 shadow">

                    <div class="card-body p-4">

                        <h3 class="text-center mb-4">
                            Register
                        </h3>

                        <form @submit.prevent="register">

                            <div class="mb-3">

                                <label>
                                    Nama
                                </label>

                                <input type="text" class="form-control" v-model="form.name">

                            </div>

                            <div class="mb-3">

                                <label>
                                    Email
                                </label>

                                <input type="email" class="form-control" v-model="form.email">

                            </div>

                            <div class="mb-3">

                                <label>
                                    Password
                                </label>

                                <input type="password" class="form-control" v-model="form.password">

                            </div>

                            <button class="btn btn-success w-100">
                                Register
                            </button>

                        </form>

                        <div class="text-center mt-3">

                            Sudah punya akun?

                            <router-link to="/">
                                Login
                            </router-link>

                        </div>

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
    name: '',
    email: '',
    password: ''
})

const register = async () => {

    // VALIDASI

    if (
        !form.name ||
        !form.email ||
        !form.password
    ) {

        Swal.fire({
            icon: 'warning',
            title: 'Semua field wajib diisi'
        })

        return
    }

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

        await api.post('/register', form)

        Swal.fire({
            icon: 'success',
            title: 'Register berhasil',
            timer: 1500,
            showConfirmButton: false
        })

        router.push({
            path: '/verify-otp',
            query: {
                email: form.email
            }
        })

    }

    catch (error: unknown) {

        let message = 'Register gagal'

        if (
            typeof error === 'object' &&
            error !== null &&
            'response' in error
        ) {

            const err = error as {
                response?: {
                    data?: {
                        message?: string
                    }
                }
            }

            message =
                err.response?.data?.message ||
                message
        }

        Swal.fire({
            icon: 'error',
            title: message
        })

    }

}

</script>