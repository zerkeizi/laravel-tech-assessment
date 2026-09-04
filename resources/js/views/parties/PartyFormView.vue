<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import partiesApi from '../../api/parties';

const props = defineProps({ id: { type: String, default: null } });
const router = useRouter();

const form = reactive({ type: 'individual', name: '', document: '', email: '', phone: '' });
const errors = ref({});
const submitting = ref(false);

onMounted(async () => {
    if (props.id) {
        const { data } = await partiesApi.show(props.id);
        Object.assign(form, data);
    }
});

async function submit() {
    errors.value = {};
    submitting.value = true;

    try {
        if (props.id) {
            await partiesApi.update(props.id, form);
        } else {
            await partiesApi.store(form);
        }
        router.push({ name: 'parties.index' });
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="form-page">
        <h1>{{ id ? 'Editar' : 'Nova' }} Pessoa/Empresa</h1>
        <form @submit.prevent="submit">
            <label>
                Tipo
                <select v-model="form.type">
                    <option value="individual">Pessoa Física</option>
                    <option value="company">Empresa</option>
                </select>
            </label>
            <label>
                Nome / Razão Social
                <input v-model="form.name" required>
                <span class="error" v-if="errors.name">{{ errors.name[0] }}</span>
            </label>
            <label>
                CPF/CNPJ
                <input v-model="form.document" required>
                <span class="error" v-if="errors.document">{{ errors.document[0] }}</span>
            </label>
            <label>
                E-mail
                <input v-model="form.email" type="email">
                <span class="error" v-if="errors.email">{{ errors.email[0] }}</span>
            </label>
            <label>
                Telefone
                <input v-model="form.phone">
            </label>
            <div class="actions">
                <button type="submit" :disabled="submitting">Salvar</button>
                <RouterLink to="/parties">Cancelar</RouterLink>
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
