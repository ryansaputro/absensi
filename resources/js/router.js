import VueRouter from 'vue-router'
// Pages
import Home from './pages/home'
import About from './pages/about'
import Register from './pages/Register'
import Login from './pages/login'
import Dashboard from './pages/user/Dashboard'
import AdminDashboard from './pages/admin/Dashboard'
import PenggunaRead from './pages/admin/master_user/Read'
import PenggunaCreate from './pages/admin/master_user/Create'
import PenggunaUpdate from './pages/admin/master_user/Update'
import Absensi from './pages/admin/absensi/Read'
import Lacak from './pages/admin/lacak/Read'
import Pantau from './pages/admin/pantau/Read'
import LaporanTerlambat from './pages/admin/laporan/LaporanTerlambat'
import LaporanDigantiSore from './pages/admin/laporan/LaporanDigantiSore'
import LaporanOvertime from './pages/admin/laporan/LaporanOvertime'
import LaporanSemua from './pages/admin/laporan/LaporanSemua'
// Routes
const routes = [
    {
        path: '/',
        name: 'login',
        component: Login,
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
            auth: true
        }
    },
    {
        path: '/absensi',
        name: 'Absensi',
        component: Absensi,
        meta: {
            auth: true
        }
    },
    {
        path: '/lacak',
        name: 'Lacak Personnel',
        component: Lacak,
        meta: {
            auth: true
        }
    },
    {
        path: '/pantau',
        name: 'Pantau Personnel',
        component: Pantau,
        meta: {
            auth: true
        }
    },
    {
        path: '/laporan-terlambat',
        name: 'Laporan Terlambat & PLA',
        component: LaporanTerlambat,
        meta: {
            auth: true
        }
    },
    {
        path: '/laporan-diganti-sore',
        name: 'Laporan Diganti Sore',
        component: LaporanDigantiSore,
        meta: {
            auth: true
        }
    },
    {
        path: '/laporan-overtime',
        name: 'Laporan Overtime',
        component: LaporanOvertime,
        meta: {
            auth: true
        }
    },
    {
        path: '/laporan-semua',
        name: 'Laporan Absensi',
        component: LaporanSemua,
        meta: {
            auth: true
        }
    },
    {
        path: '/pengguna',
        name: 'karyawan',
        component: PenggunaRead,
        meta: {
            auth: true
        }
    },
    {
        path: '/pengguna/create',
        name: 'karyawan baru',
        component: PenggunaCreate,
        meta: {
            auth: true
        }
    },
    {
        path: '/pengguna/:id',
        name: 'perbarui karyawan',
        component: PenggunaUpdate,
        meta: {
            auth: true
        }
    },
    
]
const router = new VueRouter({
    history: true,
    mode: 'history',
    routes,
    linkActiveClass: "active"
})
export default router