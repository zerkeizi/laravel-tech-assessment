<script setup>
import AppNavBar from './AppNavBar.vue';
</script>

<template>
    <div class="app-layout">
        <AppNavBar />
        <main class="app-content">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.app-content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 1.5rem;
}
</style>
