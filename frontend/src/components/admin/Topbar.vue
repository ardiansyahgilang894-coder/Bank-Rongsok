<template>

    <div class="topbar">

        <div class="left">

            <button class="btn btn-light mobile-toggle" @click="$emit('toggleSidebar')">
                <i class="bi bi-list"></i>
            </button>

            <h4>Dashboard</h4>

        </div>

        <div class="profile">

            <button class="btn btn-light" @click="logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>

            <div class="avatar">
                A
            </div>

        </div>

    </div>

</template>

<script setup lang="ts">

defineOptions({
    name: 'AdminTopbar'
})

import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import api from '../../services/api'

defineEmits(['toggleSidebar'])

const router = useRouter()

const logout = async () => {

    try {

        await api.post('/logout')

        localStorage.removeItem('token')
        localStorage.removeItem('user')

        Swal.fire({
            icon: 'success',
            title: 'Logout berhasil',
            timer: 1500,
            showConfirmButton: false
        })

        router.push('/')

    } catch {

        Swal.fire({
            icon: 'error',
            title: 'Logout gagal'
        })

    }

}

</script>

<style scoped>
.topbar {
    background: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
}

.left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.profile {
    display: flex;
    align-items: center;
    gap: 20px;
}

.avatar {
    width: 40px;
    height: 40px;
    background: #16a34a;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-toggle {
    display: none;
}

@media(max-width:992px) {

    .mobile-toggle {
        display: block;
    }

}
</style>