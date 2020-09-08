<template>
  <div>
    <form @submit.prevent="addData()">
      <div class="row">
        <div class="col-md-6">
          <h4>Identitas Karyawan</h4>
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
              <div class="form-group">
                <label>No KTP</label>
                <input
                  type="textfield"
                  class="form-control"
                  placeholder="Masukkan No. Ktp"
                  v-model="form.no_ktp"
                  required
                >
              </div>
              <div class="form-group">
                <label>Nama Lengkap</label>
                <input
                  type="textfield"
                  class="form-control"
                  placeholder="Masukkan nama lengkap"
                  v-model="form.nama_lengkap"
                  required
                >
              </div>
              <div class="form-group">
                <label>Golongan Darah</label>
                <select class="form-control" v-model="form.gol_darah" required>
                  <option value="A">A</option>
                  <option value="B">B</option>
                  <option value="O">O</option>
                  <option value="AB">AB</option>
                </select>
              </div>
              <div class="form-group">
                <label>No Telp</label>
                <input
                  type="textfield"
                  class="form-control"
                  placeholder="Masukkan No telp"
                  v-model="form.no_telp"
                  required
                >
              </div>
              <div class="form-group">
                <label>Email</label>
                <input
                  type="email"
                  class="form-control"
                  placeholder="Masukkan Email"
                  v-model="form.email"
                  required
                >
              </div>
              <div class="form-group">
                <label>No Tag RFID</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Masukkan ID Tag RFID"
                  v-model="form.id_epc_tag"
                  required
                >
              </div>
              <div class="form-group">
                <label>Divisi</label>
                <select class="form-control" v-model="form.divisi" required>
                  <option v-for="item in divisi" :value="item.id">
                    {{ item.nama_divisi }}
                  </option>
                </select>
              </div>
              <!-- <div class="form-group">
                <label>Password</label>
                <input
                  type="password"
                  class="form-control"
                  placeholder="Masukkan Password"
                  v-model="form.password"
                  required
                >
              </div> -->

          </div>
        </div>
        <div class="col-md-6">
          <h4>Alamat Karyawan</h4>
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
              <div class="form-group">
                <label>Provinsi</label>
                <select class="form-control" @change="getKota()" v-model="form.provinsi" required>
                  <option value="" disabled selected>Pilih Provinsi</option>
                  <option v-for="item in projects" :value="item.id">
                    {{ item.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kota</label>
                <select class="form-control" @change="getKecamatan()" v-model="form.kota" required>
                  <option value="" disabled selected>Pilih Provinsi</option>
                  <option v-for="item in kota" :value="item.id">
                    {{ item.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kecamatan</label>
                <select class="form-control" @change="getKelurahan()" v-model="form.kecamatan" required>
                  <option v-for="item in kecamatan" :value="item.id">
                    {{ item.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kelurahan</label>
                <select class="form-control" v-model="form.kelurahan" required>
                  <option v-for="item in kelurahan" :value="item.id">
                    {{ item.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kode Pos</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Masukkan Kode pos"
                  v-model="form.kode_pos"
                  required
                >
              </div>
              <div class="form-group">
                <label>RT</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Masukkan RT"
                  v-model="form.rt"
                  required
                >
              </div>
              <div class="form-group">
                <label>RW</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Masukkan RW"
                  v-model="form.rw"
                  required
                >
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <textarea 
                  v-model="form.alamat" 
                  class="form-control" 
                  placeholder="Masukkan Alamat"></textarea>
              </div>
              <router-link class="btn btn-danger" to="/pengguna">Kembali</router-link>
              <button class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data(){
    return{
      form:{
        no_ktp: '',
        nama_lengkap: '',
        no_telp: '',
        email: '',
        id_epc_tag: '',
        provinsi: '',
        kota: '',
        kecamatan: '',
        kelurahan: '',
        kode_pos: '',
        rt: '',
        rw: '',
        alamat: '',
        gol_darah:'',
        divisi:''
      },
      projects:[],
      kota:[],
      kecamatan:[],
      kelurahan:[],
      divisi:[],

    }
  },
  created() {
        this.getProjects();
        this.getDivisi();
  },
  methods: {
    addData() {
      // post data ke api menggunakan axios
      axios
        .post("pengguna/create", {
          no_ktp: this.form.no_ktp,
          nama_lengkap: this.form.nama_lengkap,
          no_telp: this.form.no_telp,
          email: this.form.email,
          id_epc_tag: this.form.id_epc_tag,
          provinsi: this.form.provinsi,
          kota: this.form.kota,
          kecamatan: this.form.kecamatan,
          kelurahan: this.form.kelurahan,
          kode_pos: this.form.kode_pos,
          rt: this.form.rt,
          rw: this.form.rw,
          alamat: this.form.alamat,
          gol_darah: this.form.gol_darah,
          divisi: this.form.divisi
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/pengguna");
          this.$swal('Created', 'You successfully Created this file', 'success');
        })
        .catch(errors => {
            console.log(errors);
        });
    },
      getDivisi() {
            axios.get('divisi')
                .then(response => {
                    this.divisi = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
      getProjects() {
            axios.get('provinsi')
                .then(response => {
                    this.projects = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
      getKecamatan() {
        axios.get('kecamatan', {
              params:{
                id:this.form.kota
              }
            })
                .then(response => {
                    this.kecamatan = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
      },  
      getKota() {
        axios.get('kota', {
              params:{
                id:this.form.provinsi
              }
            })
                .then(response => {
                    this.kota = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
      },  
      getKelurahan() {
        axios.get('kelurahan', {
              params:{
                id:this.form.kecamatan
              }
            })
                .then(response => {
                    this.kelurahan = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
      }  
  }
};
</script>