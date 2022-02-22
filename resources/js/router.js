import Vue from 'vue';
import VueRouter from 'vue-router';
import HomeComponent from './components/HomeComponent';
import AboutComponent from './components/AboutComponent';

Vue.use(VueRouter);

export default new VueRouter({
    mode: 'history',
    routes: [
        {path: '/home', component: HomeComponent},
        {path: '/about', component: AboutComponent},
    ]
});