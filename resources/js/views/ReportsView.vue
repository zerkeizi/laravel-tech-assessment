<script setup>
import { onMounted, reactive, ref } from 'vue';
import reportsApi from '../api/reports';
import partiesApi from '../api/parties';
import StatusBadge from '../components/shared/StatusBadge.vue';

const parties = ref([]);
const filters = reactive({ type: 'both', party_id: '', status: '', date_from: '', date_to: '' });
const items = ref([]);
const totals = ref(null);
const meta = ref(null);

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value ?? 0);
}

async function load(page = 1) {
    const { data } = await reportsApi.generate({ ...filters, page });
    items.value = data.items.data;
    meta.value = { current_page: data.items.current_page, last_page: data.items.last_page };
    totals.value = data.totals;
}

onMounted(async () => {
    const { data } = await partiesApi.index({ per_page: 100 });
    parties.value = data.data;
    await load();
});
</script>

<template>
    <div>
        <h1>Relatório Financeiro</h1>

        <div class="filters">
            <select v-model="filters.type" @change="load()">
                <option value="both">Contas a Pagar e Receber</option>
                <option value="payable">Contas a Pagar</option>
                <option value="receivable">Contas a Receber</option>
            </select>
            <select v-model="filters.party_id" @change="load()">
                <option value="">Todos</option>
                <option v-for="p in parties" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <select v-model="filters.status" @change="load()">
                <option value="">Todos os status</option>
                <option value="pendente">Pendente</option>
                <option value="pago">Pago</option>
                <option value="recebido">Recebido</option>
                <option value="vencido">Vencido</option>
                <option value="cancelado">Cancelado</option>
            </select>
            <input v-model="filters.date_from" type="date" @change="load()">
            <input v-model="filters.date_to" type="date" @change="load()">
        </div>

        <div class="totals" v-if="totals">
            <div class="card"><span class="label">Quantidade</span><span class="value">{{ totals.count }}</span></div>
            <div class="card"><span class="label">Valor Total</span><span class="value">{{ formatCurrency(totals.total_amount) }}</span></div>
            <div class="card" v-for="(amount, status) in totals.by_status" :key="status">
                <span class="label">Total {{ status }}</span>
                <span class="value">{{ formatCurrency(amount) }}</span>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Cliente/Fornecedor</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="items.length === 0"><td colspan="6" class="empty">Nenhum registro encontrado.</td></tr>
                <tr v-for="item in items" :key="`${item.kind}-${item.id}`">
                    <td>{{ item.kind === 'payable' ? 'Pagar' : 'Receber' }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.party?.name }}</td>
                    <td>{{ formatCurrency(item.amount) }}</td>
                    <td>{{ item.due_date }}</td>
                    <td><StatusBadge :status="item.status" /></td>
                </tr>
            </tbody>
        </table>

        <div class="pagination" v-if="meta && meta.last_page > 1">
            <button type="button" :disabled="meta.current_page <= 1" @click="load(meta.current_page - 1)">Anterior</button>
            <span>Página {{ meta.current_page }} de {{ meta.last_page }}</span>
            <button type="button" :disabled="meta.current_page >= meta.last_page" @click="load(meta.current_page + 1)">Próxima</button>
        </div>
    </div>
</template>

<style scoped>
.filters { display: flex; gap: 0.5rem; margin: 1rem 0; flex-wrap: wrap; }
.totals { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
.totals .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.75rem 1rem; }
.totals .label { display: block; font-size: 0.75rem; color: #6b7280; text-transform: capitalize; }
.totals .value { font-size: 1.1rem; font-weight: 600; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { text-align: left; padding: 0.5rem 0.75rem; border-bottom: 1px solid #e5e7eb; }
.empty { text-align: center; color: #6b7280; padding: 1.5rem; }
.pagination { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.75rem; }
</style>
