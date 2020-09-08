<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <br>
        <br>
        <h4>Create new datas</h4>
        <br>
        <div class="user-data m-b-30 p-3">
          <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
          <form @submit.prevent="addData()">
            <div class="form-group">
              <label>First name</label>
              <input
                type="textfield"
                class="form-control"
                placeholder="Input your first name"
                v-model="form.firstName"
                required
              >
            </div>
            <div class="form-group">
              <label>Last name</label>
              <input
                type="textfield"
                class="form-control"
                placeholder="Input your last name"
                v-model="form.lastName"
                required
              >
            </div>
            <button class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return{
      form:{
        firstName: '',
        lastName: ''
      }
    }
  },
  methods: {
    addData() {
      // post data ke api menggunakan axios
      axios
        .post("http://localhost:8000/api/pengguna/create", {
          first_name: this.form.firstName,
          last_name: this.form.lastName
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/pengguna");
          this.$swal('Created', 'You successfully Created this file', 'success');
        });
    }
  }
};
</script>