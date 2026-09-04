<script setup>
import { useAuth } from './composables/useAuth';
import AppLayout from './components/layout/AppLayout.vue';

const { state } = useAuth();
</script>

<template>
    <AppLayout v-if="state.user">
        <router-view />
    </AppLayout>
    <router-view v-else />
</template>
