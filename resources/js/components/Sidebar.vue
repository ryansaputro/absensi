<template>
<!-- MENU SIDEBAR-->
<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <a href="#">
            <img src="images/icon/logo.png" alt="Cool Admin" />
        </a>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <router-link tag="li" :to="{ path: 'dashboard'}">
                    <a><i class="fas fa-tachometer-alt"></i>Dashboard</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'person'}">
                    <a><i class="fas fa-chart-bar"></i>Person</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'pantau_personnel'}">
                    <a><i class="fas fa-desktop"></i>Pantau Personnel</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'lacak_personnel'}">
                    <a><i class="fas fa-chart-bar"></i>Lacak Personnel</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'kehadiran'}">
                    <a><i class="fas fa-user"></i>Kehadiran</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'laporan'}">
                    <a><i class="fas fa-book"></i>Laporan</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'master_user'}">
                    <a><i class="fas fa-users"></i>Master User</a>
                </router-link>
                <router-link tag="li" :to="{ path: 'master_user'}">
                    <a><i class="fas fa-map"></i>Master Lokasi</a>
                </router-link>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Dropdown button
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                    </div>
            </ul>
        </nav>
    </div>
</aside>
<!-- END MENU SIDEBAR-->
</template>
<script>
  export default {
    data() {
      return {
        routes: {
          // UNLOGGED
          unlogged: [
            { name: 'Register', path: 'register' },
            { name: 'Login', path: 'login'}
          ],
          // LOGGED USER
          user: [
            { name: 'Dashboard', path: 'dashboard' }
          ],
          // LOGGED ADMIN
          admin: [
            { name: 'Dashboard', path: 'admin.dashboard' }
          ]
        },
        activeItem: 'about'
      }
    },
    mounted() {
      //
    },
    computed: {
      currentRouteName() {
          return this.$route.name;
      }
    },
    methods: {
        isActive: function (menuItem) {
        return this.activeItem === menuItem
        },
        setActive: function (menuItem) {
        this.activeItem = menuItem // no need for Vue.set()
        }
    }
  }
</script>
<style>
.navbar {
  margin-bottom: 30px;
}
</style>