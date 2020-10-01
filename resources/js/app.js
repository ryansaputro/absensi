require('./bootstrap');
import 'es6-promise/auto'
import axios from 'axios'
import Vue from 'vue'
import VueAuth from '@websanova/vue-auth'
import VueAxios from 'vue-axios'
import VueRouter from 'vue-router'
import Index from './Index'
import auth from './auth'
import router from './router'
import VueSweetalert2 from 'vue-sweetalert2'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import Permissions from './mixin'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'



Vue.mixin(Permissions)
Vue.use(VueSidebarMenu)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueSweetalert2);


/* IN MAIN FILE */
import Moment from 'moment'
Moment.locale('id')
Vue.prototype.$moment = Moment
/* INSIDE A COMPONENT */
// console.log(this.$moment().format("LL"))

// Set Vue globally
window.Vue = Vue
// Set Vue router
Vue.router = router
Vue.use(VueRouter)
// Set Vue authentication
Vue.use(VueAxios, axios)
axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api/v1`
Vue.use(VueAuth, auth)
// Load Index
Vue.component('index', Index);

const app = new Vue({
  locale: 'id-ID',
  el: '#RyanApp',
  router
});