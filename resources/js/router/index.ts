import { createRouter, createWebHistory } from 'vue-router';

import DashboardPage from '../pages/DashboardPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import ProjectBoardPage from '../pages/ProjectBoardPage.vue';
import ProjectsPage from '../pages/ProjectsPage.vue';
import RegisterPage from '../pages/RegisterPage.vue';
import AppLayout from '../components/AppLayout.vue';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            meta: { requiresAuth: true },
            children: [
                { path: '', redirect: { name: 'dashboard' } },
                { path: 'dashboard', name: 'dashboard', component: DashboardPage },
                { path: 'projects', name: 'projects', component: ProjectsPage },
                { path: 'projects/:id', name: 'project-board', component: ProjectBoardPage, props: true },
            ],
        },
        {
            path: '/login',
            name: 'login',
            component: LoginPage,
            meta: { guestOnly: true },
        },
        {
            path: '/register',
            name: 'register',
            component: RegisterPage,
            meta: { guestOnly: true },
        },
    ],
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();
    await auth.initialize();

    if (to.matched.some((record) => record.meta.requiresAuth) && !auth.isAuthenticated) {
        return { name: 'login' };
    }

    if (to.matched.some((record) => record.meta.guestOnly) && auth.isAuthenticated) {
        return { name: 'dashboard' };
    }
});

export default router;
