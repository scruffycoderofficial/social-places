import Vue from 'vue';
import router from "./router";
import store from './store';
import {
    BootstrapVue,
    FormPlugin,
    TablePlugin
} from 'bootstrap-vue'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import '../styles/app.css'

import VeeValidate from 'vee-validate';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faHome,
    faUser,
    faUserPlus,
    faSignInAlt,
    faSignOutAlt
} from '@fortawesome/free-solid-svg-icons';
library.add(faHome, faUser, faUserPlus, faSignInAlt, faSignOutAlt);

Vue.use(BootstrapVue)
Vue.use(FormPlugin)
Vue.use(TablePlugin)
Vue.use(VeeValidate);
Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.config.productionTip = false

import App from './components/App';

router.beforeEach((to, from, next) => {
    const publicPages = ['/login', '/register', '/', '/contact'];
    const authRequired = !publicPages.includes(to.path);
    const loggedIn = localStorage.getItem('user');

    if (authRequired && !loggedIn) {
        next('/login');
    } else {
        next();
    }
});
/**
const app = new Vue({
    router,
    store,
    el: '#app',
    render: h => h(App)
});
 */