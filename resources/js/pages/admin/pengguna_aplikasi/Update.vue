<template>
  <div>
    <form @submit.prevent="updateData()">
      <div class="loader" v-if="loading"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
              <div class="form-group">
                <label>NIK</label>
                <model-select :options="options"
                                v-model="form.nik"
                                placeholder="Pilih NIK Karyawan"
                                isDisabled
                                 >
                </model-select>
              </div>
              <div class="form-group">
                <label>Roles</label>
                <select class="form-control" v-model="form.id_roles" required>
                    <option value="" disabled selected>Pilih Roles</option>
                    <option v-for="item in roles" :value="item.id">
                      {{ item.name }}
                    </option>
                </select>
              </div>
              <div class="form-group">
                <router-link class="btn btn-danger" to="/user-login">Kembali</router-link>
                <button class="btn btn-primary">Simpan</button>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import 'vue-search-select/dist/VueSearchSelect.css'
import { ModelSelect } from 'vue-search-select'
//you need to import the CSS manually (in case you want to override it)
export default {
  components: {DatePicker, ModelSelect },
  data(){
    return{
      options: [],
      roles:[],
      form:{
        nik: '',
        id_roles: '',
      },
      loading: false,

    }
  },
  created() {
    this.getNik();
    this.loadData();
  },
  methods: {
      selectFromParentComponent1 () {
        // select option from parent component
        this.form.nik = this.options[0]
      },
    
    getNik() {
      axios.get('data-kehadiran/get-nik', {params:{from:'user-login'}})
            .then(response => {
                this.options = response.data.data;
                this.roles = response.data.roles;
               
            })
            .catch(errors => {
                console.log(errors);
            }).finally(() => {
                this.loading =  false
            });
    },
    loadData() {
      // load data berdasarkan id
      axios
        .get("user-login/" + this.$route.params.id)
        .then(response => {
          // post value yang dari response ke form
          this.form.nik = response.data[0].model_id;
          this.form.id_roles = response.data[0].role_id;
        }).finally(() => {
          this.loading =  false
        });
    },

    updateData() {
      // put data ke api menggunakan axios
      axios
        .put("user-login/" + this.$route.params.id, {
          nik: this.form.nik,
          roles: this.form.id_roles,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/user-login");
          this.$swal('Berhasil', 'Data Pengguna Aplikasi Berhasil diperbarui', 'success');
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