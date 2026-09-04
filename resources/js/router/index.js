import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const routes = [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', name: 'login', component: () => import('../views/LoginView.vue'), meta: { guestOnly: true } },
    { path: '/dashboard', name: 'dashboard', component: () => import('../views/DashboardView.vue') },
    { path: '/reports', name: 'reports', component: () => import('../views/ReportsView.vue') },

    { path: '/parties', name: 'parties.index', component: () => import('../views/parties/PartyListView.vue') },
    { path: '/parties/create', name: 'parties.create', component: () => import('../views/parties/PartyFormView.vue') },
    { path: '/parties/:id/edit', name: 'parties.edit', component: () => import('../views/parties/PartyFormView.vue'), props: true },

    { path: '/payables', name: 'payables.index', component: () => import('../views/payables/PayableListView.vue') },
    { path: '/payables/create', name: 'payables.create', component: () => import('../views/payables/PayableFormView.vue') },
    { path: '/payables/:id/edit', name: 'payables.edit', component: () => import('../views/payables/PayableFormView.vue'), props: true },

    { path: '/receivables', name: 'receivables.index', component: () => import('../views/receivables/ReceivableListView.vue') },
    { path: '/receivables/create', name: 'receivables.create', component: () => import('../views/receivables/ReceivableFormView.vue') },
    { path: '/receivables/:id/edit', name: 'receivables.edit', component: () => import('../views/receivables/ReceivableFormView.vue'), props: true },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const { state, fetchUser } = useAuth();

    if (!state.checked) {
        await fetchUser();
    }

    if (!to.meta.guestOnly && !state.user) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && state.user) {
        return { name: 'dashboard' };
    }
});

export default router;
