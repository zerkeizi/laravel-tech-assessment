<script setup>
import { useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';

const router = useRouter();
const { state, logout } = useAuth();

async function handleLogout() {
    await logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <nav class="navbar">
        <div class="navbar-links">
            <router-link to="/dashboard">Dashboard</router-link>
            <router-link to="/parties">Pessoas/Empresas</router-link>
            <router-link to="/payables">Contas a Pagar</router-link>
            <router-link to="/receivables">Contas a Receber</router-link>
            <router-link to="/reports">Relatórios</router-link>
        </div>
        <div class="navbar-user">
            <span>{{ state.user?.name }}</span>
            <button type="button" @click="handleLogout">Sair</button>
        </div>
    </nav>
</template>

<style scoped>
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: #1f2937;
    color: #fff;
}
.navbar-links {
    display: flex;
    gap: 1.25rem;
}
.navbar-links a {
    color: #d1d5db;
    text-decoration: none;
}
.navbar-links a.router-link-active {
    color: #fff;
    font-weight: 600;
}
.navbar-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.navbar-user button {
    background: #374151;
    color: #fff;
    border: none;
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    cursor: pointer;
}
</style>
