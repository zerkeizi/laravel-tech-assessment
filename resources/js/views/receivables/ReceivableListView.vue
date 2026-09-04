<script setup>
import { onMounted, reactive, ref } from 'vue';
import { RouterLink } from 'vue-router';
import receivablesApi from '../../api/receivables';
import partiesApi from '../../api/parties';
import DataTable from '../../components/shared/DataTable.vue';
import StatusBadge from '../../components/shared/StatusBadge.vue';

const rows = ref([]);
const meta = ref(null);
const parties = ref([]);
const filters = reactive({ party_id: '', status: '', due_from: '', due_to: '' });
const error = ref('');

const columns = [
    { key: 'description', label: 'Descrição' },
    { key: 'party', label: 'Cliente' },
    { key: 'amount', label: 'Valor' },
    { key: 'issue_date', label: 'Emissão' },
    { key: 'due_date', label: 'Vencimento' },
    { key: 'receipt_date', label: 'Recebimento' },
    { key: 'status', label: 'Status' },
];

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value ?? 0);
}

async function load(page = 1) {
    const { data } = await receivablesApi.index({ ...filters, page });
    rows.value = data.data;
    meta.value = { current_page: data.current_page, last_page: data.last_page, total: data.total };
}

async function receive(receivable) {
    error.value = '';
    try {
        await receivablesApi.receive(receivable.id);
        await load(meta.value?.current_page ?? 1);
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Não foi possível registrar o recebimento.';
    }
}

async function remove(receivable) {
    if (!confirm(`Excluir "${receivable.description}"?`)) return;
    await receivablesApi.destroy(receivable.id);
    await load(meta.value?.current_page ?? 1);
}

onMounted(async () => {
    const { data } = await partiesApi.index({ per_page: 100 });
    parties.value = data.data;
    await load();
});
</script>

<template>
    <div>
        <div class="header">
            <h1>Contas a Receber</h1>
            <RouterLink to="/receivables/create" class="btn">Nova</RouterLink>
        </div>

        <div class="filters">
            <select v-model="filters.party_id" @change="load()">
                <option value="">Todos os clientes</option>
                <option v-for="p in parties" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <select v-model="filters.status" @change="load()">
                <option value="">Todos os status</option>
                <option value="pendente">Pendente</option>
                <option value="recebido">Recebido</option>
                <option value="vencido">Vencido</option>
                <option value="cancelado">Cancelado</option>
            </select>
            <input v-model="filters.due_from" type="date" @change="load()">
            <input v-model="filters.due_to" type="date" @change="load()">
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <DataTable :columns="columns" :rows="rows" :meta="meta" @page-change="load">
            <template #cell-party="{ row }">{{ row.party?.name }}</template>
            <template #cell-amount="{ row }">{{ formatCurrency(row.amount) }}</template>
            <template #cell-status="{ row }"><StatusBadge :status="row.status" /></template>
            <template #actions="{ row }">
                <RouterLink :to="`/receivables/${row.id}/edit`">Editar</RouterLink>
                <button v-if="row.status === 'pendente'" type="button" @click="receive(row)">Receber</button>
                <button type="button" @click="remove(row)">Excluir</button>
            </template>
        </DataTable>
    </div>
</template>

<style scoped>
.header { display: flex; justify-content: space-between; align-items: center; }
.filters { display: flex; gap: 0.5rem; margin: 1rem 0; }
.btn { background: #1f2937; color: #fff; padding: 0.5rem 0.9rem; border-radius: 4px; text-decoration: none; }
.error { color: #dc2626; }
</style>
