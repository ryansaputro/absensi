<template>
    <div id="main">
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

  export default {
    data() {
      return {
        // menu:JSON.parse(localStorage.getItem('role'))
        menu: [
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
                        href: '/data-kehadiran',
                        title: 'Data Kehadiran',
                        icon: 'fa fa-calendar'
                    },
                    {
                        title: 'Laporan',
                        icon: 'fa fa-book',
                        child: [
                            {
                                href: '/laporan-absensi',
                                title: 'Laporan Kehadiran'
                            },
                            {
                                href: '/rekap-absensi',
                                title: 'Rekap Absensi'
                            },
                            {
                                href: '/rekap-keterlambatan',
                                title: 'Rekap Keterlambatan'
                            },
                            // {
                            //     href: '/laporan-terlambat',
                            //     title: 'Laporan Terlambat & PLA'
                            // },
                            // {
                            //     href: '/laporan-overtime',
                            //     title: 'Laporan Overtime'
                            // },
                            // {
                            //     href: '/laporan-semua',
                            //     title: 'Laporan Absensi'
                            // },
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
                            // {
                            //     href: '/lokasi',
                            //     title: 'Lokasi'
                            // },
                            // {
                            //     href: '/jadwal_libur',
                            //     title: 'Jadwal Libur Tahunan'
                            // }
                        ]
                    },
                ]
      }
    },
    components: {
      Menu,
      SidebarMenu,
      Mobile
    },
    created() {
        if(typeof(localStorage.getItem('user')) !== 'undefined'){
            window.Permissions = localStorage.getItem('user');
            console.log(localStorage.getItem('user'))
            console.log(localStorage.getItem('role'))            
        }else{
            window.Permissions = [];
        }

        // this.menu = [{"href":"\/dashboard","title":"Dashboard","icon":"fa fa-area-chart"},{"href":"\/absensi","title":"Absensi","icon":"fa fa-user"},{"href":"\/lacak","title":"Lacak Personnel","icon":"fa fa-blind"},{"href":"\/pantau","title":"Pantau Personnel","icon":"fa fa-eye"},{"href":"\/data-kehadiran","title":"Data Kehadiran","icon":"fa fa-calendar"},{"title":"Laporan","icon":"fa fa-book","child":[{"href":"\/laporan-absensi","title":"Laporan Kehadiran"},{"href":"\/rekap-absensi","title":"Rekap Absensi"},{"href":"\/rekap-keterlambatan","title":"Rekap Keterlambatan"}]},{"title":"Master Data","icon":"fa fa-database","child":[{"href":"\/pengguna","title":"Karyawan"}]}];
        this.getProjects();
        // console.log("a")
        // this.menus = JSON.parse(localStorage.getItem('role'));

        
    },
    // beforeRouteUpdate (to, from, next) {
        // this.menus = JSON.parse(localStorage.getItem('role'))
    // },
    //  beforeRouteEnter (to, from, next) {
    //     getPost(to.params.id, (err, menu) => {
    //     next(vm => vm.setData(err, menu))
    //     })
    // },
    // when route changes and this component is already rendered,
    // the logic will be slightly different.
    // beforeRouteUpdate (to, from, next) {
    //     this.menu = null
    //     getPost(to.params.id, (err, menu) => {
    //     this.setData(err, menu)
    //     next()
    //     })
    // },
    // updated: {
    // },
    computed: {
        currentRouteName() {
            // console.log(this.menu)
            // console.log(localStorage.getItem('role'))
            return this.$route.name;
        },
        
    },
    methods: {
        // setData (err, menu) {
        //     if (err) {
        //         this.error = err.toString()
        //     } else {
        //         this.menu = localStorage.getItem('role')
        //     }
        // },
        getProjects() {
            console.log("user" +localStorage.getItem('role'))
        }
    } 
  }
</script>