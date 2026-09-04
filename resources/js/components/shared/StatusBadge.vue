<script setup>
defineProps({
    status: { type: String, required: true },
});

const colors = {
    pendente: '#f59e0b',
    pago: '#16a34a',
    recebido: '#16a34a',
    vencido: '#dc2626',
    cancelado: '#6b7280',
};
</script>

<template>
    <span class="status-badge" :style="{ backgroundColor: colors[status] ?? '#6b7280' }">
        {{ status }}
    </span>
</template>

<style scoped>
.status-badge {
    color: #fff;
    padding: 0.15rem 0.6rem;
    border-radius: 999px;
    font-size: 0.75rem;
    text-transform: capitalize;
}
</style>
