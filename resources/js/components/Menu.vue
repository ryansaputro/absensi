<template>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: #fff;height: 90px;color:#010;">
    <!-- <router-link :to="{name: 'home'}" class="navbar-brand text-uppercase">{{$route.name}}</router-link> -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto" v-if="$auth.check(1)">
          <li class="nav-item" v-for="(route, key) in routes.user" v-bind:key="route.path">
            <router-link :to="{ name : route.path }" :key="key" class="nav-link">{{route.name}}</router-link>
          </li>
      </ul>
      <ul class="navbar-nav mr-auto" v-if="$auth.check(2)">
          <li class="nav-item" v-for="(route, key) in routes.user" v-bind:key="route.path">
            <router-link :to="{ name : route.path }" :key="key" class="nav-link">{{route.name}}</router-link>
          </li>
      </ul>
      <ul class="navbar-nav ml-auto" v-if="!$auth.check()">
          <li class="nav-item" v-for="(route, key) in routes.unlogged" v-bind:key="route.path">
            <router-link :to="{ name : route.path }" :key="key" class="nav-link">{{route.name}}</router-link>
          </li>
      </ul>
      <ul class="navbar-nav ml-auto" v-if="$auth.check()">
        <li class="nav-item">
          <router-link to="/ganti-password" class="nav-link"><i class="fa fa-key" aria-hidden="true"></i> ganti password </router-link>
            
        </li>
        <li class="nav-item">
          <a class="nav-link" style="margin-right:0px;" href="#" @click="logout()"> <i class="fa fa-user" aria-hidden="true"></i> Keluar </a>
        </li>
      </ul>
    </div>
  </nav>
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
        }
      }
    },
    mounted() {
      //
    },
    methods: {
      logout(){
        if(this.$auth.logout()){
          // console.log("sukses")
          localStorage.removeItem('user');
          localStorage.removeItem('role');
          localStorage.removeItem('firstLoad');
        }
        //   axios.post('/auth/logout')
        // .then(res => {
        //   // this.$swal('Login Sukses', 'Anda akan dialihkan ke halaman dashboard', 'success');
        //   // app.success = true
        //   const redirectTo = 'login'
        //   this.$router.push({name: redirectTo})
        //   console.log("sukses keluar")
          // localStorage.removeItem('user');
          // localStorage.removeItem('auth_token_default');
        // })
        // .catch(error => {
        //   this.$swal('Login Gagal', 'Cek kembali username dan password', 'error');
        // })
      }
    },
    computed: {
      currentRouteName() {
          return this.$route.name;
      }
    }
  }
</script>
<style>
.navbar {
  margin-bottom: 30px;
}
</style>