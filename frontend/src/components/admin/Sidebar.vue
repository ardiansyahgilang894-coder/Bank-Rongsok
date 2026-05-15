<template>
    <AdminSidebar :isOpen="isOpen" @close="$emit('close')" />
</template>

<script setup lang="ts">
defineOptions({
    name: 'AppSidebar'
})

defineProps<{
    isOpen: boolean
}>()

defineEmits(['close'])

</script>
