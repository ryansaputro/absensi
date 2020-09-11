<template>
  <div>
    <form @submit.prevent="updateData()">
      <div class="row">
        <div class="col-md-12">
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
              <div class="form-group">
                <label>Periode</label>
                <!-- <date-picker 
                      ref="datePicker" 
                      v-model="tanggal" 
                      @change="getDataNik()" 
                      range
                      valueType="format"
                      range
                      format='DD-MM-YYYY'
                      confirm
                      width="100%"
                      input-class="form-control"
                  >
                </date-picker> -->
                <date-picker
                    @change="getDataNik()" 
                    v-model="form.periode" 
                    ref="datePicker"
                    name="contract_period"
                    format='DD-MM-YYYY'
                    valueType="format"
                    confirm
                    width="100%"
                    style="width:100%;"
                    input-class="form-control"
                ></date-picker>
              </div>
              <div class="form-group">
                <label>NIK</label>
                <model-select :options="options"
                                ref="valNik"
                                v-model="form.nik"
                                placeholder="Pilih NIK Karyawan"
                                @input="getDataNik()"
                                 >
                </model-select>
              </div>
              <div class="form-group">
                <label>Jenis Absensi</label>
                <select class="form-control" v-model="form.status" required placeholder="pilih jenis absen">
                  <option value="I">Izin</option>
                  <option value="S">Sakit</option>
                  <option value="A">Alasan</option>
                  <option value="C">Cuti</option>
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
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import 'vue-search-select/dist/VueSearchSelect.css'
import { ModelSelect } from 'vue-search-select'
export default {
  components: {DatePicker, ModelSelect },
  data() {
    return {
      options: [],
      form:{
        tanggal: '',
        nik: '',
        status: '',
      },
      projects:[],
      lastData:[],
    }
  },
  created() {
    // load data saat pertama kali halaman dibuka
    this.getNik();
    this.loadData();
  },
   beforeUpdate () {
    this.$refs.datePicker.currentValue = new Date(String(this.form.tanggal));
  },
  methods: {
    selectFromParentComponent1 () {
        // select option from parent component
        this.form.nik = this.options[0]
      },
    
    getNik() {
      axios.get('data-kehadiran/get-nik')
            .then(response => {
                this.options = response.data.data;
               
            })
            .catch(errors => {
                console.log(errors);
            });
    },
     getDataNik() {
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
                });
      },  
    loadData() {
      // load data berdasarkan id
      axios
        .get("data-kehadiran/" + this.$route.params.id)
        .then(response => {          
          // post value yang dari response ke form
          this.form.tanggal = response.data.tanggal;
          this.form.nik = response.data.id_karyawan;
          this.form.status = response.data.status;

          this.getDataNik();
        });
    },
    updateData() {
      // put data ke api menggunakan axios
      axios
        .put("data-kehadiran/" + this.$route.params.id, {
          tanggal: this.form.tanggal,
          nik: this.form.nik,
          status: this.form.status,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/data-kehadiran");
          this.$swal('Updated', 'You successfully Updated this file', 'success');
        })
        .catch(errors => {
              console.log(errors);
              this.$swal('Fail', 'You failed Updated this file', 'error');
        });
    },
    
  }
};
</script>