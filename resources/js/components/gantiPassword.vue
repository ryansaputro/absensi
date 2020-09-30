<template>
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-6">
        <div class="user-data m-b-30 p-3">
          <div class="card-body">
            <div class="alert alert-danger" v-if="has_error && !success">
                <p v-if="error == 'registration_validation_error'">Validation Errors.</p>
                <p v-else>Error, can not register at the moment. If the problem persists, please contact an administrator.</p>
            </div>
            <form autocomplete="off" @submit.prevent="updatePassword" v-if="!success" method="post">
                <div class="form-group" v-bind:class="{ 'has-error': has_error && errors.password }">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" v-model="password">
                    <span class="help-block" v-if="has_error && errors.password">{{ errors.password }}</span>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': has_error && errors.password }">
                    <label for="password_confirmation">Password confirmation</label>
                    <input type="password" id="password_confirmation" class="form-control" v-model="password_confirmation">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    data() {
      return {
        password: '',
        password_confirmation: '',
        has_error: false,
        error: '',
        errors: {},
        success: false
      }
    },
    methods: {
      updatePassword() {
        // post data ke api menggunakan axios
        axios
          .post("update-password", {
            password: this.password,
            password_confirmation: this.password_confirmation,
          })
          .then(response => {
            // push router ke read data
            this.$router.go(-1);
            this.$swal('Berhasil', 'Password berhasil di perbarui.', 'success');
          })
          .catch(errors => {
              if (errors.response) {
                  var data = '';
                  $.each(errors.response.data.errors, function(k,v){
                    data += v[0]+"\n";
                  });
                  this.$swal('Gagal', data, 'error');
                // client received an error response (5xx, 4xx)
              } else if (errors.request) {
                  console.log(errors.request);
                  console.log("request never left")
                // client never received a response, or request never left
              } else {
                console.log("lainnya")
              }
          });
      }
    }
  }
</script>