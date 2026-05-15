<template>

<div class="card stat-card border-0 shadow-sm">

    <div class="card-body">

        <div class="d-flex justify-content-between">

            <div>
                <p class="title">{{ title }}</p>
                <h3>{{ value }}</h3>
            </div>

            <div class="icon">
                <i :class="icon"></i>
            </div>

        </div>

    </div>

</div>

</template>

<script setup lang="ts">

defineProps({
    title:String,
    value:String,
    icon:String
})

</script>

<style scoped>

.stat-card{
    border-radius:20px;
}

.title{
    color:gray;
    margin-bottom:5px;
}

.icon{
    width:60px;
    height:60px;
    border-radius:15px;
    background:#dcfce7;
    color:#16a34a;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

</style>