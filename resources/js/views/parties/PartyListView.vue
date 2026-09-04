<script setup>
import { onMounted, reactive, ref } from 'vue';
import { RouterLink } from 'vue-router';
import partiesApi from '../../api/parties';
import DataTable from '../../components/shared/DataTable.vue';

const rows = ref([]);
const meta = ref(null);
const filters = reactive({ search: '', type: '' });
const error = ref('');

const columns = [
    { key: 'name', label: 'Nome / Razão Social' },
    { key: 'document', label: 'CPF/CNPJ' },
    { key: 'type', label: 'Tipo' },
    { key: 'email', label: 'E-mail' },
    { key: 'phone', label: 'Telefone' },
];

async function load(page = 1) {
    const { data } = await partiesApi.index({ ...filters, page });
    rows.value = data.data;
    meta.value = { current_page: data.current_page, last_page: data.last_page, total: data.total };
}

async function remove(party) {
    if (!confirm(`Excluir "${party.name}"?`)) return;

    error.value = '';
    try {
        await partiesApi.destroy(party.id);
        await load(meta.value?.current_page ?? 1);
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Não foi possível excluir.';
    }
}

onMounted(() => load());
</script>

<template>
    <div>
        <div class="header">
            <h1>Pessoas/Empresas</h1>
            <RouterLink to="/parties/create" class="btn">Novo</RouterLink>
        </div>

        <div class="filters">
            <input v-model="filters.search" placeholder="Buscar por nome ou documento" @keyup.enter="load()">
            <select v-model="filters.type" @change="load()">
                <option value="">Todos os tipos</option>
                <option value="individual">Pessoa Física</option>
                <option value="company">Empresa</option>
            </select>
            <button type="button" @click="load()">Filtrar</button>
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <DataTable :columns="columns" :rows="rows" :meta="meta" @page-change="load">
            <template #actions="{ row }">
                <RouterLink :to="`/parties/${row.id}/edit`">Editar</RouterLink>
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
