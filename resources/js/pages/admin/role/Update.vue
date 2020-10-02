<template>
  <div>
    <form @submit.prevent="updateData()">
      <div class="loader" v-if="loading"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="user-data p-3">
            <!-- prevent form submit untuk reload halaman, kemudian memanggil function addData() -->
             <div class="form-group">
                <label>Nama Role</label>
                <input type="text" required name="role" v-model="form.roles" class="form-control">
              </div>
              <div class="form-group">
                <label>Menu Permission</label>
                <multi-list-select
                    v-model="form.permissions"
                    :list="options"
                    option-value="value"
                    option-text="text"
                    id="mySelectId"
                    name="mySelectName"
                    :selected-items="items"
                    placeholder="Pilih Permission"
                    @select="onSelect"
                    @searchchange="printSearchText"
                  >
              </multi-list-select>
              </div>
              <div class="form-group">
                <router-link class="btn btn-danger" to="/role">Kembali</router-link>
                <button class="btn btn-primary">Simpan</button>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
import 'vue-search-select/dist/VueSearchSelect.css'
import { ModelListSelect, MultiListSelect  } from 'vue-search-select'
import _ from 'lodash'
//you need to import the CSS manually (in case you want to override it)
export default {
  components: { ModelListSelect, MultiListSelect  },
  data(){
    return{
      options: [],
      form:{
        permissions: [],
        roles: '',
      },
      items:[],
      lastSelectItem: {},
      searchText: '',
      loading: false,

    }
  },
  created() {
    this.getPermissions();
    this.loadData();
  },
  methods: {
    onSelect (items, lastSelectItem) {
      this.items = items
      this.form.permissions = this.items;
      this.lastSelectItem = lastSelectItem
    },
    reset () {
      this.items = [] // reset
    },
    selectItem () {
      this.items = unionWith(this.items, [this.someList[0]], isEqual)
    },
    printSearchText (searchText) {
      this.searchText = searchText
    },    
    getPermissions() {
      this.loading = true
      axios.get('/role/get-permissions')
            .then(response => {
                this.options = response.data;
            })
            .catch(errors => {
                console.log(errors);
            }).finally(() => {
                this.loading =  false
            });
    },
    loadData() {
      // load data berdasarkan id
      this.loading = true
      axios
        .get("role/" + this.$route.params.id)
        .then(response => {
          // post value yang dari response ke form
          this.form.roles = response.data.roles.name;
          this.form.permissions = response.data.permissions;
          this.items = response.data.permissions;
        }).finally(() => {
            this.loading =  false
        });
    },

    updateData() {
      // post data ke api menggunakan axios
      this.loading = true
      axios
        .put("role/" + this.$route.params.id, {
          permissions: this.form.permissions,
          roles: this.form.roles,
        })
        .then(response => {
          // push router ke read data
          this.$router.push("/role");
          this.$swal('Berhasil', 'Role Berhasil diperbarui', 'success');
        })
        .catch(errors => {
            // console.log(errors);
            // this.$swal('Failed', 'You failed Created this file', 'error');
            if (errors.response) {
                var data = '';
                $.each(errors.response.data.errors, function(k,v){
                  data += v[0]+"\n";
                });
                console.log(data);
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