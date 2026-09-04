<script setup>
import { statusLabel } from '../../lib/labels';

defineProps({
    status: { type: String, required: true },
});

const colors = {
    pending: '#f59e0b',
    paid: '#16a34a',
    received: '#16a34a',
    overdue: '#dc2626',
    cancelled: '#6b7280',
};
</script>

<template>
    <span class="status-badge" :style="{ backgroundColor: colors[status] ?? '#6b7280' }">
        {{ statusLabel(status) }}
    </span>
</template>

<style scoped>
.status-badge {
    color: #fff;
    padding: 0.15rem 0.6rem;
    border-radius: 999px;
    font-size: 0.75rem;
}
</style>
