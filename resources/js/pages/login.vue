<template>
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12">
        <div class="card card-default login-style">
          <!-- <div class="card-header">Login</div> -->
          <!-- <div class="circleBase type3"></div> -->
          <div class="card-body login-body">
            <h5 class="text-center mb-3 mt-3" style="color:#fff;">USER LOGIN</h5>
            <div class="alert alert-danger" v-if="has_error && !success">
              <p v-if="error == 'login_error'">Validation Errors.</p>
              <p v-else>Error, unable to connect with these credentials.</p>
            </div>

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
              <button type="submit" class="button-login pull-right">Masuk</button>
            </form>
          </div>
        </div>
            <img v-bind:src="'/images/ncirfid.png'" class="img-vendor" /> 
            <p class="powered">Powered by NCI</p>
      </div>
    </div>
  </div>
</template>
<style>
.page-content--bge5 {
  background: #fff !important;
}

.page-wrapper {
  background: #fff !important;
  padding-bottom: 0vh !important;
}

.login-content {
  background-color: #e5e5e5 !important;
}

.img-vendor {
  position: absolute;
  right: 10px;
  top: 47px;
}

.login-container {
  margin-left: 20px !important;
  padding-top: 30px !important;
}

.sidebar-footer {
  display: none !important;
}

.footer {
  display:none !important;
}
</style>
<script>
    export default {
    data(){
      return {
        email: null,
        password: null,
        success: false,
        has_error: false,
        error: ''
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
        //   // this.$swal('Login Sukses', 'Anda akan dialihkan ke halaman dashboard', 'success');
        //   app.success = true
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
          success: function() {
            // handle redirection
            app.success = true
            const redirectTo = 'dashboard'
            this.$router.push({name: redirectTo})
            
          },
          error: function() {
            // console.log("error")
            app.has_error = true
            app.error = res.response.data.error
          },
          rememberMe: true,
          fetchUser: true
        }).then(res => {
          localStorage.setItem('role', res.data.role);
          if(localStorage.setItem('user', res.data.data)){
            location.reload(true);
            console.log(localStorage.getItem('role'))
          }
          console.log(localStorage.getItem('role'))

          // console.log(localStorage)
        })
        .catch(error => {
          this.$swal('Login Gagal', 'Cek kembali username dan password', 'error');
        })
        // try {
        //     this.$auth.login({
        //       data: {
        //         email: app.email,
        //         password: app.password
        //       }, 
        //         rememberMe: true,
        //         fetchUser: true
        //   });
        //   const redirectTo = 'dashboard'
        //   this.$router.push({name: redirectTo})
        // }
        // catch (err) {
        //     alert(`Error: ${err}`)
        // }
      }
    },
    created() {
      localStorage.removeItem('user');
      localStorage.removeItem('auth_token_default');
      console.log(localStorage)
    }
    
  } 
</script>