<template>

<div class="wrapper">

    <Sidebar
      :isOpen="sidebarOpen"
      @close="sidebarOpen = false"
    />

    <div class="main">

        <Topbar
          @toggleSidebar="toggleSidebar"
        />

        <div class="content">
            <slot />
        </div>

    </div>

</div>

</template>

<script setup lang="ts">

import { ref } from 'vue'

import Sidebar from '../components/admin/AdminSidebar.vue'
import Topbar from '../components/admin/Topbar.vue'

const sidebarOpen = ref(false)

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

</script>

<style scoped>

.wrapper{
    display:flex;
}

.main{
    width:100%;
    min-height:100vh;
    background:#f8fafc;
}

.content{
    padding:20px;
}

</style>