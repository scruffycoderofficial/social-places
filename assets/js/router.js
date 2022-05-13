import Vue from 'vue';
import Router from 'vue-router';

import Login from "./components/Auth/Login";
import Dashboard from "./components/Dashboard";
import Register from "./components/Auth/Register";

Vue.use(Router);

const routes = [
    {
        path: "/",
        name: "dashboard",
        component: Dashboard
    },
    {
        path: '/login',
        component: Login
    },
    {
        path: '/register',
        component: Register
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('./components/Auth/Profile.vue')
    },
    {
        path: "/contacts",
        name: "contacts",
        component: () => import('./components/ContactList.vue')
    },
    {
        path: "/contact",
        name: "contact",
        component: () => import('./components/ContactAdd.vue')
    }
];

const router = new Router({
    mode: 'history',
    routes : routes
});

export default router