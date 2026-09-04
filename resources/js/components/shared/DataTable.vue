<script setup>
defineProps({
    columns: { type: Array, required: true }, // [{ key, label }]
    rows: { type: Array, required: true },
    meta: { type: Object, default: null }, // { current_page, last_page, total }
});

const emit = defineEmits(['page-change']);
</script>

<template>
    <table class="data-table">
        <thead>
            <tr>
                <th v-for="col in columns" :key="col.key">{{ col.label }}</th>
                <th v-if="$slots.actions">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="rows.length === 0">
                <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="empty">Nenhum registro encontrado.</td>
            </tr>
            <tr v-for="row in rows" :key="row.id">
                <td v-for="col in columns" :key="col.key">
                    <slot :name="`cell-${col.key}`" :row="row">{{ row[col.key] }}</slot>
                </td>
                <td v-if="$slots.actions">
                    <slot name="actions" :row="row" />
                </td>
            </tr>
        </tbody>
    </table>

    <div class="pagination" v-if="meta && meta.last_page > 1">
        <button type="button" :disabled="meta.current_page <= 1" @click="emit('page-change', meta.current_page - 1)">Anterior</button>
        <span>Página {{ meta.current_page }} de {{ meta.last_page }}</span>
        <button type="button" :disabled="meta.current_page >= meta.last_page" @click="emit('page-change', meta.current_page + 1)">Próxima</button>
    </div>
</template>

<style scoped>
.data-table {
    width: 100%;
    border-collapse: collapse;
}
.data-table th, .data-table td {
    text-align: left;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}
.data-table .empty {
    text-align: center;
    color: #6b7280;
    padding: 1.5rem;
}
.pagination {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.75rem;
}
</style>
