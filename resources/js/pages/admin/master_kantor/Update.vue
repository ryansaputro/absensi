<template>
  <div>
    <form @submit.prevent="updateData()">
      <div class="loader" v-if="loading"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
              <div class="form-group">
                <label>Nama Cabang</label>
                <input type="text" required name="role" v-model="form.nama_cabang" class="form-control">
              </div>
              <div class="form-group">
                <label>Lokasi</label>
                <input type="text" required name="role" v-model="form.lokasi" class="form-control">
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
                <router-link class="btn btn-danger" to="/jabatan">Kembali</router-link>
                <button class="btn btn-primary">Simpan</button>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
export default {
  data() {
    return {
      form: {
        nama_cabang:'',
        lokasi:'',
        deskripsi: '',
        status: '',
      },
      loading: false,
    };
  },
  created() {
    // load data saat pertama kali halaman dibuka
    this.loadData();

  },
  methods: {
    loadData() {
      this.loading = true
      // load data berdasarkan id
      axios
        .get("kantor/" + this.$route.params.id)
        .then(response => {
          // post value yang dari response ke form
          this.form.nama_cabang = response.data[0].nama_cabang;
          this.form.lokasi = response.data[0].lokasi;
          this.form.deskripsi = response.data[0].deskripsi;
          this.form.status = response.data[0].status;
        }).finally(() => {
            this.loading =  false
        });
    },
    updateData() {
      this.loading = true
      // put data ke api menggunakan axios
      axios
        .put("kantor/" + this.$route.params.id, {
          nama_cabang: this.form.nama_cabang,
          lokasi: this.form.lokasi,
          deskripsi: this.form.deskripsi,
          status: this.form.status,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/kantor");
          this.$swal('Berhasil', 'Data Kantor Berhasil diperbarui', 'success');
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
        }).finally(() => {
            this.loading =  false
        });
    },
  }
};
</script>