import VueRouter from 'vue-router'
// Pages
// import Home from './pages/home'
import About from './pages/about'
import Register from './pages/Register'
import Login from './pages/login'
import Display from './pages/user/Dashboard'
import AdminDashboard from './pages/admin/Dashboard'
import PenggunaRead from './pages/admin/master_user/Read'
import PenggunaCreate from './pages/admin/master_user/Create'
import PenggunaUpdate from './pages/admin/master_user/Update'
import Absensi from './pages/admin/absensi/Read'
import Lacak from './pages/admin/lacak/Read'
import Pantau from './pages/admin/pantau/Read'
import LaporanTerlambat from './pages/admin/laporan/LaporanTerlambat'
// import LaporanDigantiSore from './pages/admin/laporan/LaporanDigantiSore'
// import LaporanOvertime from './pages/admin/laporan/LaporanOvertime'
import LaporanSemua from './pages/admin/laporan/LaporanSemua'
import LaporanAbsensi from './pages/admin/laporan/LaporanAbsensi'
import RekapAbsensi from './pages/admin/laporan/RekapAbsensi'
import RekapKeterlambatan from './pages/admin/laporan/RekapKeterlambatan'
import ListDataKehadiran from './pages/admin/data_kehadiran/Read'
import UpdateDataKehadiran from './pages/admin/data_kehadiran/Update'
import NewDataKehadiran from './pages/admin/data_kehadiran/Create'
import NotFound from './components/404'
import gantiPassword from './components/gantiPassword'
import AdminAppPenggunaApp from './pages/admin/pengguna_aplikasi/Read'
// Routes
const routes = [
    {
        path: '/ganti-password',
        name: 'gantiPassword',
        component: gantiPassword,
        meta: {
            auth: true,
            menus:'read-404'
        }
    },
    {
        path: '/404',
        name: 'NotFound',
        component: NotFound,
        meta: {
            auth: true,
            menus:'read-404'
        }
    },
    {
        path: '/display',
        name: 'display',
        component: Display,
        meta: {
            auth: undefined
        }
    },
    {
        path: '/about',
        name: 'about',
        component: About,
        meta: {
            auth: undefined
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            auth: false
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            auth: false
        }
    },
    // USER ROUTES
    {
        path: '/dashboard',
        name: 'dashboard',
        component: AdminDashboard,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/absensi',
        name: 'Absensi',
        component: Absensi,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/lacak',
        name: 'Lacak Personnel',
        component: Lacak,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/pantau',
        name: 'Pantau Personnel',
        component: Pantau,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/data-kehadiran',
        name: 'Data Kehadiran',
        component: ListDataKehadiran,
        meta: {
            auth: true,
            menus: 'read-data-kehadiran'
        }
    },
    {
        path: '/data-kehadiran/create',
        name: 'Data Kehadiran Baru',
        component: NewDataKehadiran,
        meta: {
            auth: true,
            menus: 'create-data-kehadiran'
        }
    },
    {
        path: '/data-kehadiran/:id',
        name: 'Update Data Kehadiran',
        component: UpdateDataKehadiran,
        meta: {
            auth: true,
            menus: 'edit-data-kehadiran'
        }
    },
    {
        path: '/laporan-terlambat',
        name: 'Laporan Terlambat & PLA',
        component: LaporanTerlambat,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/laporan-absensi',
        name: 'Laporan Absensi',
        component: LaporanAbsensi,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/rekap-absensi',
        name: 'Rekap Absensi',
        component: RekapAbsensi,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/rekap-keterlambatan',
        name: 'Rekap Keterlambatan',
        component: RekapKeterlambatan,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    // {
    //     path: '/laporan-diganti-sore',
    //     name: 'Laporan Diganti Sore',
    //     component: LaporanDigantiSore,
    //     meta: {
    //         auth: true
    //     }
    // },
    // {
    //     path: '/laporan-overtime',
    //     name: 'Laporan Overtime',
    //     component: LaporanOvertime,
    //     meta: {
    //         auth: true
    //     }
    // },
    {
        path: '/laporan-semua',
        name: 'Laporan Absensi',
        component: LaporanSemua,
        meta: {
            auth: true,
            menus: 'read-absensi'
        }
    },
    {
        path: '/user-login',
        name: 'Pengguna Aplikasi',
        component: AdminAppPenggunaApp,
        meta: {
            auth: true,
            menus: 'read-pengguna'
        }
    },
    {
        path: '/pengguna',
        name: 'karyawan',
        component: PenggunaRead,
        meta: {
            auth: true,
            menus: 'read-pengguna'
        }
    },
    {
        path: '/pengguna/create',
        name: 'karyawan baru',
        component: PenggunaCreate,
        meta: {
            auth: true,
            menus: 'create-pengguna'
        }
    },
    {
        path: '/pengguna/:id',
        name: 'perbarui karyawan',
        component: PenggunaUpdate,
        meta: {
            auth: true,
            menus: 'edit-pengguna'
        }
    },
    
]
const router = new VueRouter({
    history: true,
    mode: 'history',
    routes,
    linkActiveClass: "active"
})

router.beforeEach((to, from, next) => {
    if (to.name !== 'login' && to.meta.auth == null){
        if (to.name == 'display'){
            next();

        }else{
            next({ name: 'login' })

        }
    } 
    else{

        if (to.fullPath == '/dashboard' && from.fullPath == '/login'){
            next();
            if (!localStorage.getItem('firstLoad')){
                localStorage['firstLoad'] = true;
                window.location.reload()
            }else{
                localStorage.removeItem('firstLoad');
            }
        }else{
            console.log(to.meta.auth)
            if (to.meta.auth === true){
                console.log(to.meta)
                console.log(JSON.parse(localStorage.user))
                var menusRole = to.meta.menus;
                if (jQuery.inArray(menusRole, JSON.parse(localStorage.user)) !== -1){
                    console.log("ada")
                    next();
                }else{
                    next({ name:'NotFound'});
                }

            }else{
                next();
            }
            
        }
        
    } 
})



export default router