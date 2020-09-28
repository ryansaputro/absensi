<template>
  <div>
    <form @submit.prevent="addData()">
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
                    v-model="form.no_ktp"
                    required
                  >
                </div>
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

              </div>
              <div class="col-md-6">
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

              </div>
            </div>
          </b-tab>
          <b-tab title="Status">
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
              <select class="form-control" @change="getKota()" v-model="form.status_karyawan" required>
                <option value="" disabled>Pilih Status Karyawan</option>
                <option value="kontrak">Kontrak</option>
                <option value="tetap">Tetap</option>
              </select>
            </div>
            <div class="form-group" v-if="form.status_karyawan === 'kontrak'">
              <label>Tanggal Akhir Kontrak</label>
              <date-picker :placeholder="waterMark" style="width:100%;" id="periode"  v-model="form.tgl_akhir_kontrak" @change="filterTanggal()" valueType="format"></date-picker>
            </div>

            <div class="form-group">
              <label>Tanggal Masuk</label>
              <date-picker :placeholder="waterMark" style="width:100%;" id="periode"  v-model="form.tgl_masuk" @change="filterTanggal()" valueType="format"></date-picker>
            </div>

          </b-tab>
          <b-tab title="Alamat">
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
                <option value="" disabled selected>Pilih Kelurahan</option>
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

          </b-tab>
        </b-tabs>
      </div>
    </form>
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
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
        divisi:'',
        jabatan:'',
        status_karyawan:'',
        tgl_akhir_kontrak:moment(new Date()).format('YYYY-M-D'),
        tgl_masuk: moment(new Date()).format('YYYY-M-D'),
      },
      waterMark : new Date().toISOString().slice(0,10),
      projects:[],
      kota:[],
      kecamatan:[],
      kelurahan:[],
      divisi:[],
      jabatan:[],

    }
  },
  components: { DatePicker},
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
          divisi: this.form.divisi,
          jabatan: this.form.jabatan
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
                    this.jabatan = response.data.jabatan
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