<template>
  <div>
    <form @submit.prevent="addData()">
      <div class="loader" v-if="loading"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData()  -->
              <div class="form-group">
                <label>Periode</label>
                <date-picker ref="datePicker" :placeholder="waterMark"  v-model="form.tanggal" @change="getDataNik()" style="width:100%;"  valueType="format"></date-picker>
              </div>
              <div class="form-group">
                <label>NIK/Nama Karyawan</label>
                <model-select :options="options"
                                v-model="form.nik"
                                placeholder="Pilih NIK Karyawan"
                                @input="getDataNik()"
                                 >
                </model-select>
              </div>
              <div class="form-group">
                <label>Jenis Absensi</label>
                <select class="form-control" v-model="form.status" required placeholder="pilih jenis absen">
                  <option value="" disabled>Pilih Jenis Absen</option>
                  <option value="I">Izin</option>
                  <option value="S">Sakit</option>
                  <option value="A">Alpha</option>
                  <option value="C">Cuti</option>
                  <option value="LK">Luar Kota</option>
                </select>
              </div>
              <div class="form-group">
                <router-link class="btn btn-danger" to="/data-kehadiran">Kembali</router-link>
                <button class="btn btn-primary">Simpan</button>
              </div>
          </div>
        </div>
      </div>
    </form>
      <div class="row mt-5">
          <div class="col-md-12">
            <div class="user-data p-3">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>NO</th>
                    <th>TANGGAL</th>
                    <th>I</th>
                    <th>S</th>
                    <th>A</th>
                    <th>C</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in lastData" :value="item.id">
                    <td>{{item.id}}</td>
                    <td>{{item.tanggal}}</td>
                    <td>{{item.I}}</td>
                    <td>{{item.S}}</td>
                    <td>{{item.A}}</td>
                    <td>{{item.C}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
  </div>
</template>

<script>
// import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import 'vue-search-select/dist/VueSearchSelect.css'
import { ModelSelect } from 'vue-search-select'
//you need to import the CSS manually (in case you want to override it)
export default {
  components: {DatePicker, ModelSelect },
  data(){
    return{
      selectedDate1: "",
      options: [],
      form:{
        tanggal: moment(new Date()).format('YYYY-M-D'),
        nik: '',
        status: '',
      },
      loading: false,
      projects:[],
      lastData:[],
      time1:  moment(new Date()).format('YYYY-M-D'),
      waterMark : new Date().toISOString().slice(0,10),
    }
  },
  props: {
    value: {
      type: String,
      default: moment().format('DD-MM-YYYY')
    }
  },
  created() {
    this.getNik();
    this.time1 = moment(new Date()).format('YYYY-M-D');
  },
  mounted() {
      this.time1 = moment(new Date()).format('YYYY-M-D');
      this.$refs.datePicker.currentValue = [new Date()];
 },
  methods: {
    defaultValue(){
      this.form.tanggal =  moment(new Date()).format('YYYY-M-D')
    },
      selectFromParentComponent1 () {
        // select option from parent component
        this.form.nik = this.options[0]
      },
    
    getNik() {
      this.loading = true
      axios.get('data-kehadiran/get-nik')
            .then(response => {
                this.options = response.data.data;
               
            })
            .catch(errors => {
                console.log(errors);
            }).finally(() => {
                this.loading =  false
            });
    },

    addData() {
      // post data ke api menggunakan axios
      this.loading = true
      axios
        .post("data-kehadiran/create", {
          tanggal: this.form.tanggal,
          nik: this.form.nik,
          status: this.form.status,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/data-kehadiran");
          this.$swal('Created', 'You successfully Created this file', 'success');
        })
        .catch(errors => {
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
      getDataNik() {
        this.loading = true
        axios.get('data-kehadiran/get-data-nik', {
              params:{
                tanggal:this.form.tanggal,
                nik:this.form.nik
              }
            })
                .then(response => {
                    this.lastData = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
      },  
  }
};
</script>