<template>
    <div id="main">
        <div id="header">
          <div v-if="$auth.check()">
            <Mobile></Mobile>
            <!-- <Sidebar></Sidebar> -->
            <template>
              <sidebar-menu :menu="menu" />
            </template>
          </div>
            <!-- PAGE CONTAINER-->
          <div v-if="$auth.check()">
            <div class="page-container">
                <Menu></Menu>
                <div id="content" class="main-content">
                    <div v-cloak>
                        <div class="main-content">
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
                  <div class="page-content--bge5">
                      <div class="container">
                          <div class="login-wrap">
                              <div class="login-content">
                                  <router-view></router-view>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>  
        </div>
    </div>
</template>
<script>
  import Menu from './components/Menu.vue'
  import Sidebar from './components/Sidebar.vue'
  import Mobile from './components/Mobile.vue'
  import { SidebarMenu } from 'vue-sidebar-menu'

  export default {
    data() {
      return {
        menu: [
                    {
                        header: true,
                        title: 'Administrator RFID',
                        hiddenOnCollapse: false
                    },
                    {
                        href: '/dashboard',
                        title: 'Dashboard',
                        icon: 'fa fa-area-chart'
                    },
                    {
                        href: '/absensi',
                        title: 'Absensi',
                        icon: 'fa fa-user'
                    },
                    {
                        href: '/lacak',
                        title: 'Lacak Personnel',
                        icon: 'fa fa-blind'
                    },
                    {
                        href: '/pantau',
                        title: 'Pantau Personnel',
                        icon: 'fa fa-eye'
                    },
                    {
                        title: 'Laporan',
                        icon: 'fa fa-book',
                        child: [
                            {
                                href: '/laporan-terlambat',
                                title: 'Laporan Terlambat & PLA'
                            },
                            {
                                href: '/laporan-overtime',
                                title: 'Laporan Overtime'
                            },
                            {
                                href: '/laporan-semua',
                                title: 'Laporan Absensi'
                            },
                        ]
                    },
                    {
                        title: 'Master Data',
                        icon: 'fa fa-database',
                        child: [
                            {
                                href: '/pengguna',
                                title: 'Karyawan'
                            },
                            {
                                href: '/lokasi',
                                title: 'Lokasi'
                            },
                            {
                                href: '/jadwal_libur',
                                title: 'Jadwal Libur Tahunan'
                            }
                        ]
                    }
                ]
      }
    },
    components: {
      Menu,
      SidebarMenu,
      Mobile
    },
  }
</script>