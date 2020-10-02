<template>
    <div id="main" v-if="isMobile === false">
        <div id="header">
          <div v-if="$auth.check()">
            <!-- <Mobile></Mobile> -->
            <!-- <Sidebar></Sidebar> -->
            <template>
              <sidebar-menu :menu="menu" />
            </template>
          </div>
            <!-- PAGE CONTAINER-->
          <div v-if="$auth.check()">
            <div class="page-container">
                <div class="upper-sidebar"><img class="logo" src="http://202.138.231.178:88/nuansa.com/wp-content/uploads/2019/08/nci.png"></div>
                <Menu></Menu>
                <div id="content" class="main-content">
                    <div v-cloak>
                        <div class="main-content">
                            <h5 class="ml-5 text-uppercase">{{currentRouteName}}</h5>
                            <div class="section__content section__content--p30">
                                <div class="container-fluid">
                                    <router-view></router-view>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div v-else>
                <div v-if="currentRouteName === 'display'" class="page-content--bge5 display">
                    <router-view></router-view>
                </div>
                <div v-else class="page-content--bge5">
                    <div class="container login-container">
                        <div class="login-wrap">
                            <div class="login-content">
                                <router-view></router-view>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
        <div class="sidebar-footer"></div>
        <div class="footer">
            <!-- <div class="row">
                <div class="col-md-6"> -->
                    <span class="copyrights">© RFID TOTAL SOLUTION</span>
                <!-- </div>
                <div class="col-md-6"> -->
                    <span class="pull-right version">v.1.0</span>
                <!-- </div> -->
            </div>
        </div>
        <div v-else>
          <h3 class="text-center text-uppercase">maaf hanya dapat diakses menggunakan desktop</h3>
        </div>
    </div>
</template>
<style>
/* .sidebar-footer {
  display: block;
}

.footer {
  display:block;
} */
</style>
<script>
  import Menu from './components/Menu.vue'
  import Sidebar from './components/Sidebar.vue'
  import Mobile from './components/Mobile.vue'
  import { SidebarMenu } from 'vue-sidebar-menu'
  import { isMobile } from 'mobile-device-detect';

  export default {
    data() {
      return {
        menu:JSON.parse(localStorage.getItem('role')),
        isMobile:isMobile ? true : false

      }
    },
    components: {
      Menu,
      SidebarMenu,
      Mobile
    },
    created() {
    },
    computed: {
        currentRouteName() {
            return this.$route.name;
        },
        storageToken() {
            return localStorage.role
        }
        
    },
    methods: {
    },
  
  }
</script>