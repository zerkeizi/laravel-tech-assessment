<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const route = useRoute();
const { login } = useAuth();

const email = ref('');
const password = ref('');
const error = ref('');
const submitting = ref(false);

async function handleSubmit() {
    error.value = '';
    submitting.value = true;

    try {
        await login(email.value, password.value);
        router.push(route.query.redirect || { name: 'dashboard' });
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Falha ao autenticar.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="login-page">
        <form class="login-card" @submit.prevent="handleSubmit">
            <h1>Controle Financeiro</h1>
            <label>
                E-mail
                <input v-model="email" type="email" required autofocus>
            </label>
            <label>
                Senha
                <input v-model="password" type="password" required>
            </label>
            <p v-if="error" class="error">{{ error }}</p>
            <button type="submit" :disabled="submitting">Entrar</button>
        </form>
    </div>
</template>

<style scoped>
.login-page {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background: #f3f4f6;
}
.login-card {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    width: 320px;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.login-card label {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.875rem;
}
.login-card input {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
}
.login-card button {
    padding: 0.6rem;
    background: #1f2937;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.error {
    color: #dc2626;
    font-size: 0.875rem;
}
</style>
