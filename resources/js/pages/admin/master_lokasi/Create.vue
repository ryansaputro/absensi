<template>
  <div>
    <form @submit.prevent="addData()">
      <div class="loader" v-if="loading"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
              <div class="form-group">
                <label>Nama Lokasi Antena</label>
                <input type="text" required name="role" v-model="form.nama_gerbang" class="form-control">
              </div>
              <div class="form-group">
                <label>Deskripsi</label>
                <textarea required name="role" v-model="form.deskripsi" class="form-control"></textarea>
              </div>
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" v-model="form.status">
                  <option value="" disabled>Pilih Status</option>
                  <option value="1">Aktif</option>
                  <option value="0">Tidak Aktif</option>
                </select>
              </div>
              <div class="form-group">
                <router-link class="btn btn-danger" to="/lokasi">Kembali</router-link>
                <button class="btn btn-primary">Simpan</button>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
//you need to import the CSS manually (in case you want to override it)
export default {
  data(){
    return{
      form:{
        nama_gerbang:'',
        deskripsi: '',
        status: '',
      },
      loading: false,
    }
  },
  created() {
    this.getPermissions();
  },
  methods: {
    addData() {
      this.loading = true
      // post data ke api menggunakan axios
      axios
        .post("lokasi/create", {
          nama_gerbang: this.form.nama_gerbang,
          deskripsi: this.form.deskripsi,
          status: this.form.status,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/lokasi");
          this.$swal('Berhasil', 'Lokasi Baru Berhasil dibuat', 'success');
        })
        .catch(errors => {
            // console.log(errors);
            // this.$swal('Failed', 'You failed Created this file', 'error');
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
        }).finally(() => {
            this.loading =  false
        });
    },
  }
};
</script>