<script setup>
import { onMounted, ref } from 'vue';
import dashboardApi from '../api/dashboard';

const summary = ref(null);
const loading = ref(true);

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value ?? 0);
}

onMounted(async () => {
    const { data } = await dashboardApi.summary();
    summary.value = data;
    loading.value = false;
});
</script>

<template>
    <div>
        <h1>Dashboard</h1>
        <p v-if="loading">Carregando...</p>
        <div v-else class="cards">
            <div class="card">
                <span class="label">Total a Receber</span>
                <span class="value">{{ formatCurrency(summary.total_receivable) }}</span>
            </div>
            <div class="card">
                <span class="label">Total Recebido</span>
                <span class="value">{{ formatCurrency(summary.total_received) }}</span>
            </div>
            <div class="card">
                <span class="label">Total Vencido a Receber</span>
                <span class="value">{{ formatCurrency(summary.total_overdue_receivable) }}</span>
            </div>
            <div class="card">
                <span class="label">Total a Pagar</span>
                <span class="value">{{ formatCurrency(summary.total_payable) }}</span>
            </div>
            <div class="card">
                <span class="label">Total Pago</span>
                <span class="value">{{ formatCurrency(summary.total_paid) }}</span>
            </div>
            <div class="card">
                <span class="label">Total Vencido a Pagar</span>
                <span class="value">{{ formatCurrency(summary.total_overdue_payable) }}</span>
            </div>
            <div class="card highlight">
                <span class="label">Saldo Previsto</span>
                <span class="value">{{ formatCurrency(summary.projected_balance) }}</span>
            </div>
            <div class="card highlight">
                <span class="label">Saldo Realizado</span>
                <span class="value">{{ formatCurrency(summary.realized_balance) }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}
.card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.card.highlight {
    background: #eef2ff;
}
.card .label {
    font-size: 0.8rem;
    color: #6b7280;
}
.card .value {
    font-size: 1.35rem;
    font-weight: 600;
}
</style>
