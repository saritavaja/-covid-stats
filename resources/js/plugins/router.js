import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

/**
 * Importing pages
 */

import HomePage from './../pages/HomePage';

const routes = [
    {path: '/', component: HomePage},
]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
export default new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
})
