import { reactive } from 'vue';
import axios from '../lib/axios';

const state = reactive({
    user: null,
    checked: false,
});

async function fetchUser() {
    try {
        const { data } = await axios.get('/api/user');
        state.user = data;
    } catch {
        state.user = null;
    } finally {
        state.checked = true;
    }
}

async function login(email, password) {
    await axios.get('/sanctum/csrf-cookie');
    await axios.post('/login', { email, password });
    await fetchUser();
}

async function logout() {
    await axios.post('/logout');
    state.user = null;
}

export function useAuth() {
    return { state, fetchUser, login, logout };
}
