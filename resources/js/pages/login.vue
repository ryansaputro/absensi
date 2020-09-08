<template>
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12">
        <div class="card card-default">
          <div class="card-header">Login</div>
          <div class="card-body">
            <div class="alert alert-danger" v-if="has_error && !success">
              <p v-if="error == 'login_error'">Validation Errors.</p>
              <p v-else>Error, unable to connect with these credentials.</p>
            </div>
            <form autocomplete="off" @submit.prevent="login" method="post">
              <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" class="form-control" placeholder="user@example.com" v-model="email" required>
                <p class="error" v-if="error.email">{{ errors.email }}</p>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" class="form-control" v-model="password" required>
                <p class="error" v-if="error.password">{{ errors.password }}</p>
              </div>
              <button type="submit" class="btn btn-primary">Signin</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    export default {
    data(){
      return {
        email: null,
        password: null,
        error: false,
        has_error: false
      }
    },
    methods: {
      login(){
        // var app = this
        // axios.post('/auth/login', {
        //   email: app.email,
        //   password: app.password,
        // })
        // .then(res => {
        //   this.$swal('Login Sukses', 'Anda akan dialihkan ke halaman dashboard', 'success');
        //   const redirectTo = 'dashboard'
        //   this.$router.push({name: redirectTo})
        // })
        // .catch(error => {
        //   this.$swal('Login Gagal', 'Cek kembali username dan password', 'error');
        // })
        var redirect = this.$auth.redirect()
        var app = this
        this.$auth.login({
          data: {
            email: app.email,
            password: app.password
          },
          success: function(data) {
            // handle redirection
            app.success = true
            window.Permissions = ["read-absensi","read-outlets","create-outlets","edit-outlets","read-user","create-user","edit-user","delete-user"];
            const redirectTo = 'dashboard'
            this.$swal('Login Sukses', 'Anda akan dialihkan ke halaman dashboard', 'success');
            this.$router.push({name: redirectTo})
          },
          error: function(error) {
            this.$swal('Login Gagal', 'Cek kembali username dan password', 'error');
            app.has_error = true
            app.error = res.response.data.error
          },
          catch: function() {
            this.$swal('Login Gagal', 'Cek kembali username dan password', 'error');
          },
          rememberMe: true,
          fetchUser: true
        })
      }
    },
    
  } 
</script>