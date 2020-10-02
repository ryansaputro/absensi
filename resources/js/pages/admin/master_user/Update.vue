<template>
   <div>
    <!-- <div class="alert alert-info" style="position: absolute;width: 91%;z-index: 9;top: 30%;"  v-show="loading">
        Loading...
    </div> -->
    <form @submit.prevent="updateData()">
      <div class="loader" v-if="loading"></div>
      <div>
        <b-tabs content-class="mt-3">
          <b-tab title="Data" active>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>NIK Pegawai</label>
                  <input
                    type="textfield"
                    class="form-control"
                    placeholder="Masukkan NIK Pegawai"
                    v-model="form.nik_pegawai"
                    required
                  >
                </div>
                <div class="form-group">
                  <label>No KTP</label>
                  <input
                    type="textfield"
                    class="form-control"
                    placeholder="Masukkan No. Ktp"
                    v-model="form.nik_ktp"
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

              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Foto</label>
                    <div v-if="!form.foto">
                      <input type="file" @change="onFileChange">
                    </div>
                    <div v-else>
                      <img :src="form.foto" />
                      <button class="btn btn-danger btn-sm" @click="removeImage">Hapus Foto</button>
                    </div>
                </div>
                <div class="form-group">
                  <label>Golongan Darah</label>
                  <select class="form-control" v-model="form.gol_darah" required>
                    <option value="" disabled>Pilih Golongan Darah</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="O">O</option>
                    <option value="AB">AB</option>
                  </select>
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

              </div>
            </div>
          </b-tab>
          <b-tab title="Status">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Divisi</label>
                  <select class="form-control" v-model="form.divisi" required>
                    <option value="" disabled>Pilih Divisi</option>
                    <option v-for="item in divisi" :value="item.id">
                      {{ item.nama_divisi }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Jabatan</label>
                  <select class="form-control" v-model="form.jabatan" required>
                    <option value="" disabled>Pilih Jabatan</option>
                    <option v-for="item in jabatan" :value="item.id">
                      {{ item.nama_jabatan }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Status Karyawan</label>
                  <select class="form-control" v-model="form.status_karyawan" required>
                    <option value="" disabled>Pilih Status Karyawan</option>
                    <option value="kontrak">Kontrak</option>
                    <option value="tetap">Tetap</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Tanggal Masuk</label>
                  <date-picker :placeholder="waterMark" style="width:100%;" id="periode"  v-model="form.tgl_masuk" valueType="format"></date-picker>
                </div>

                <div class="form-group" v-if="form.status_karyawan === 'kontrak'">
                  <label>Tanggal Akhir Kontrak</label>
                  <date-picker :placeholder="waterMark" style="width:100%;" id="periode"  v-model="form.tgl_akhir_kontrak" valueType="format"></date-picker>
                </div>

                <div class="form-group">
                  <label>Kantor</label>
                  <select class="form-control" v-model="form.kantor" required>
                    <option value="" disabled>Pilih Kantor</option>
                    <option v-for="item in kantor" :value="item.id">
                      {{ item.nama_cabang }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

          </b-tab>
          <b-tab title="Alamat">
            <div class="row">
              <div class="col-md-6">
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
                    <option value="" disabled selected>Pilih Kota</option>
                    <option v-for="item in kota" :value="item.id">
                      {{ item.nama }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Kecamatan</label>
                  <select class="form-control" @change="getKelurahan()" v-model="form.kecamatan" required>
                    <option value="" disabled selected>Pilih Kecamatan</option>
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

              </div>
              <div class="col-md-6">
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

              </div>
            </div>
          </b-tab>
        </b-tabs>
        <div class="form-group pull-right">
          <router-link class="btn btn-danger" to="/karyawan">Kembali</router-link>
          <button class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</template>
<style>
img {
  width: 50%;
  margin: auto;
  display: block;
  margin-bottom: 10px;
}
</style>
<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
export default {
  data() {
    return {
      form: {
        nik_pegawai: '',
        no_ktp: '',
        nama_lengkap: '',
        no_telp: '',
        email: '',
        foto: '',
        gol_darah:'',
        id_epc_tag:'',
        divisi: '',
        jabatan: '',
        status_karyawan: '',
        tgl_akhir_kontrak:'',
        tgl_masuk:'',
        kantor:'',
        provinsi: '',
        kota: '',
        kecamatan: '',
        kelurahan:'',
        kode_pos:'',
        rt:'',
        rw:'',
        alamat:'',
      },
      loading: false,
      waterMark : new Date().toISOString().slice(0,10),
      projects:[],
      kota:[],
      kecamatan:[],
      kelurahan:[],
      divisi:[],
      jabatan:[],

    };
  },
  components: { DatePicker},
  created() {
    // load data saat pertama kali halaman dibuka
    this.loadData();
    this.getDivisi();
    this.getProjects();

  },
  methods: {
    loadData() {
      // load data berdasarkan id
      this.loading = true
      axios
        .get("karyawan/" + this.$route.params.id)
        .then(response => {
          // post value yang dari response ke form
          this.form.nik_pegawai = response.data[0].nik_pegawai;
          this.form.nik_ktp = response.data[0].nik_ktp;
          this.form.nama_lengkap = response.data[0].nama_lengkap;
          this.form.no_telp = response.data[0].no_telp;
          this.form.email = response.data[0].email;
          this.form.foto = '/images/karyawan/'+response.data[0].foto;
          this.form.gol_darah = response.data[0].gol_darah;
          this.form.id_epc_tag = response.data[0].id_epc_tag;
          this.form.divisi = response.data[0].id_divisi;
          this.form.jabatan = response.data[0].id_jabatan;
          this.form.status_karyawan = response.data[0].status_pegawai;
          this.form.tgl_akhir_kontrak = response.data[0].tgl_habis_kontrak;
          this.form.tgl_masuk = response.data[0].tgl_masuk;
          this.form.kantor = response.data[0].id_cabang;
          this.form.provinsi = response.data[0].provinsi;
          this.form.kota = response.data[0].kota;
          this.form.kecamatan = response.data[0].kecamatan;
          this.form.kelurahan = response.data[0].kelurahan;
          this.form.kode_pos = response.data[0].kode_pos;
          this.form.rt = response.data[0].rt;
          this.form.rw = response.data[0].rw;
          this.form.alamat = response.data[0].alamat;
        }).finally(() => {
            this.loading =  false
        });
    },
    onFileChange(e) {
          var files = e.target.files || e.dataTransfer.files;
          if (!files.length)
            return;
          this.createImage(files[0]);
    },
    createImage(file) {
          var image = new Image();
          var reader = new FileReader();
          var vm = this;

          reader.onload = (e) => {
            vm.form.foto = e.target.result;
          };
          reader.readAsDataURL(file);
    },
    removeImage: function (e) {
          this.form.foto = '';
    },
    updateData() {
      // put data ke api menggunakan axios
      this.loading = true
      axios
        .put("karyawan/" + this.$route.params.id, {
          nik_pegawai: this.form.nik_pegawai,
          nik_ktp: this.form.nik_ktp,
          nama_lengkap: this.form.nama_lengkap,
          no_telp: this.form.no_telp,
          email: this.form.email,
          foto: this.form.foto,
          gol_darah: this.form.gol_darah,
          id_epc_tag: this.form.id_epc_tag,
          divisi: this.form.divisi,
          jabatan: this.form.jabatan,
          status_karyawan: this.form.status_karyawan,
          tgl_akhir_kontrak: this.form.tgl_akhir_kontrak,
          tgl_masuk: this.form.tgl_masuk,
          kantor: this.form.kantor,
          provinsi: this.form.provinsi,
          kota: this.form.kota,
          kecamatan: this.form.kecamatan,
          kelurahan: this.form.kelurahan,
          kode_pos: this.form.kode_pos,
          rt: this.form.rt,
          rw: this.form.rw,
          alamat: this.form.alamat,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/karyawan");
          this.$swal('Berhasil', 'Data Karyawan Berhasil diperbarui', 'success');
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
    getDivisi() {
      this.loading = true
      axios.get('get-divisi')
          .then(response => {
              this.divisi = response.data.data;
              this.jabatan = response.data.jabatan;
              this.kantor = response.data.kantor;
          })
          .catch(errors => {
              console.log(errors);
          }).finally(() => {
                this.loading =  false
          });
    },
    getProjects() {
      this.loading = true;
      axios.get('provinsi')
          .then(response => {
              this.projects = response.data.data;
              this.getKota();
          })
          .catch(errors => {
              console.log(errors);
          }).finally(() => (this.loading = false));
    },
      getKecamatan() {
        this.loading = true
        axios.get('kecamatan', {
              params:{
                id:this.form.kota
              }
            }).then(response => {
                this.kecamatan = response.data.data;
                this.getKelurahan();

            })
            .catch(errors => {
                console.log(errors);
            }).finally(() => {
                this.loading =  false
            });
      },  
      getKota() {
        this.loading = true
        axios.get('kota', {
              params:{
                id:this.form.provinsi
              }
            }).then(response => {
                this.kota = response.data.data;
                this.getKecamatan();

            })
            .catch(errors => {
                console.log(errors);
            }).finally(() => {
                this.loading =  false
            });
      },  
      getKelurahan() {
        this.loading = true
        axios.get('kelurahan', {
              params:{
                id:this.form.kecamatan
              }
            }).then(response => {
                this.kelurahan = response.data.data;
            })
            .catch(errors => {
                console.log(errors);
            }).finally(() => {
                this.loading =  false
            });
      } 
  }
};
</script>