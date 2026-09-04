<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import receivablesApi from '../../api/receivables';
import partiesApi from '../../api/parties';

const props = defineProps({ id: { type: String, default: null } });
const router = useRouter();

const parties = ref([]);
const form = reactive({ party_id: '', description: '', amount: '', issue_date: '', due_date: '' });
const errors = ref({});
const submitting = ref(false);

onMounted(async () => {
    const { data } = await partiesApi.index({ per_page: 100 });
    parties.value = data.data;

    if (props.id) {
        const { data: receivable } = await receivablesApi.show(props.id);
        form.party_id = receivable.party_id;
        form.description = receivable.description;
        form.amount = receivable.amount;
        form.issue_date = receivable.issue_date;
        form.due_date = receivable.due_date;
    }
});

async function submit() {
    errors.value = {};
    submitting.value = true;

    try {
        if (props.id) {
            await receivablesApi.update(props.id, form);
        } else {
            await receivablesApi.store(form);
        }
        router.push({ name: 'receivables.index' });
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="form-page">
        <h1>{{ id ? 'Editar' : 'Nova' }} Conta a Receber</h1>
        <form @submit.prevent="submit">
            <label>
                Cliente
                <select v-model="form.party_id" required>
                    <option value="" disabled>Selecione</option>
                    <option v-for="p in parties" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
                <span class="error" v-if="errors.party_id">{{ errors.party_id[0] }}</span>
            </label>
            <label>
                Descrição
                <input v-model="form.description" required>
                <span class="error" v-if="errors.description">{{ errors.description[0] }}</span>
            </label>
            <label>
                Valor
                <input v-model="form.amount" type="number" step="0.01" min="0.01" required>
                <span class="error" v-if="errors.amount">{{ errors.amount[0] }}</span>
            </label>
            <label>
                Data de Emissão
                <input v-model="form.issue_date" type="date" required>
                <span class="error" v-if="errors.issue_date">{{ errors.issue_date[0] }}</span>
            </label>
            <label>
                Data de Vencimento
                <input v-model="form.due_date" type="date" required>
                <span class="error" v-if="errors.due_date">{{ errors.due_date[0] }}</span>
            </label>
            <div class="actions">
                <button type="submit" :disabled="submitting">Salvar</button>
                <RouterLink to="/receivables">Cancelar</RouterLink>
            </div>
        </form>
    </div>
</template>

<style scoped>
.form-page form { display: flex; flex-direction: column; gap: 0.75rem; max-width: 420px; }
.form-page label { display: flex; flex-direction: column; gap: 0.25rem; }
.form-page input, .form-page select { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; }
.actions { display: flex; gap: 0.75rem; align-items: center; }
.error { color: #dc2626; font-size: 0.8rem; }
</style>
