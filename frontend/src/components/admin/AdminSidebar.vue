<template>

    <div class="sidebar" :class="{ active: isOpen }">

        <div class="mobile-close">

            <button class="btn btn-light" @click="$emit('close')">
                ✕
            </button>

        </div>

        <div class="brand">
            <h4>♻ Bank Rongsok</h4>
        </div>

        <ul class="menu">

            <li>
                <router-link to="/dashboard">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </router-link>
            </li>

            <li>
                <router-link to="/activities">
                    <i class="bi bi-people-fill"></i>
                    Galeri Kegiatan
                </router-link>
            </li>

            <li>
                <router-link to="/funds">
                    <i class="bi bi-cash-stack"></i>
                    Transparansi Dana
                </router-link>
            </li>

            <li>
                <router-link to="/scrap-sales">
                    <i class="bi bi-recycle"></i>
                    Laporan Penjualan Rongsok
                </router-link>
            </li>

            <li>
                <router-link to="/donations">
                    <i class="bi bi-hand-thumbs-up"></i>
                    Dokumentasi Bantuan
                </router-link>
            </li>

        </ul>

    </div>

</template>

<script setup lang="ts">

defineProps<{
    isOpen: boolean
}>()

defineEmits(['close'])

</script>

<style scoped>
.sidebar {
    width: 260px;
    min-height: 100vh;
    background: linear-gradient(180deg,
            #16a34a,
            #15803d);
    color: white;
    padding: 20px;
    transition: .3s;
}

.brand {
    margin-bottom: 40px;
}

.menu {
    list-style: none;
    padding: 0;
}

.menu li {
    margin-bottom: 15px;
}

.menu a {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 10px;
    transition: .3s;
}

.menu a:hover {
    background: rgba(255, 255, 255, .1);
}

.router-link-active {
    background: rgba(255, 255, 255, .15);
}

.mobile-close {
    display: none;
}

/* MOBILE */

@media(max-width:992px) {

    .sidebar {
        position: fixed;
        left: -100%;
        top: 0;
        z-index: 999;
    }

    .sidebar.active {
        left: 0;
    }

    .mobile-close {
        display: flex;
        justify-content: end;
        margin-bottom: 20px;
    }

}
</style>