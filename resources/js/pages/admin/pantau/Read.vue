<template>
    <div class="projects">
        <div class="loader" v-if="loading"></div>
        <div class="user-data m-b-30 p-3">
          <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="filterBy">Pencarian</label>
                    </div>
                    <div class="col-md-9">
                        <input class="input form-control input-sm" type="text" @input="filterTanggal()" v-model="search" placeholder="NIK, Nama">
                    </div>
                </div>
            </div>
          </div>
      </div>
      <div class="user-data m-b-30 p-3" style="height:700px;">
          <div class="row">
              <div class="col-md-5">
                    <div class="gambarGedung">
                        <img 
                            :src="currentImageLt3" 
                            @click="changeImageWhen('click')"
                            v-bind:class="{active: isActive}"
                            class="img-gedung3" 
                        /> 
                        <img 
                            :src="currentImageLt2" 
                            @click="changeImageWhen2('click')"
                            v-bind:class="{active: isActive2}"
                            class="img-gedung2" /> 
                        <img
                        :src="currentImageLt1" 
                            @click="changeImageWhen1('click')"
                            v-bind:class="{active: isActive1}"
                        class="img-gedung1" /> 
                    </div>
                </div>
          
              <div class="col-md-7" style="height:600px;overflow-y:scroll;">
                <!-- <div class="form-group">
                    <label for="email"><strong>Pencarian:</strong></label>
                    <input class="input form-control d-inline" style="width:81%;" type="text" v-model="search" placeholder="Nama, NIK"
                   @input="resetPagination()">
                </div> -->
                  
                <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
                    <tbody>
                        <tr v-if="paginated.length > 0" v-for="(project, index) in paginated" :key="project.id">
                            <td>{{project.tanggal}}</td>
                            <td>{{project.jam}}</td>
                            <td>{{ project.nik_pegawai }}</td>
                            <td>{{project.nama_lengkap}}</td>
                            <td>{{project.nama_gerbang}}</td>
                        </tr>
                        <tr v-if="paginated.length <= 0">
                            <td colspan="4" class="text-center">Data tidak tersedia</td>
                        </tr>
                    </tbody>
                </datatable>
                <pagination :pagination="pagination" :client="true" :filtered="filteredProjects"
                            @prev="--pagination.currentPage"
                            @next="++pagination.currentPage">
                </pagination>
              </div>
          </div>
      </div>
      <!-- <div class="user-data m-b-30 p-3">
        <div class="tableFilters m-b-30">
          <div class="row">
            <div class="col-md-6"> -->
              <!-- <input class="input form-control" type="text" v-model="search" placeholder="Search Table"
                   @input="resetPagination()"> -->
                    <!-- <date-range-picker
                            :date-format="dateFormat"
                            v-model="dateRange"
                    >
                    </date-range-picker> -->
                    <!-- <date-picker :placeholder="waterMark" v-model="time1" @change="filterTanggal()" valueType="format"></date-picker> -->
                    <!-- <date-picker v-model="time2" type="datetime"></date-picker>
                    <date-picker v-model="time3" range></date-picker> -->
   
            <!-- </div> -->
            <!-- <div class="col-md-6">
              <div class="control pull-right">
                <div class="select form-control">
                    <select v-model="length" @change="resetPagination()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
            </div> -->
          <!-- </div>
        </div> -->
        <!-- <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
                <tr v-for="(project, index) in paginated" :key="project.id">
                    <td>{{ doMath(index) }}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td>{{project.jam}}</td>
                    <td>{{project.nama_gerbang}}</td>
                </tr>
            </tbody>
        </datatable>
        <pagination :pagination="pagination" :client="true" :filtered="filteredProjects"
                    @prev="--pagination.currentPage"
                    @next="++pagination.currentPage">
        </pagination> -->
      <!-- </div> -->
    </div>
</template>

<script>
import Datatable from '../../../components/Datatables.vue';
import Pagination from '../../../components/Pagination.vue';
import DateRangePicker from 'vue2-daterange-picker'
// import Datepicker from 'vuejs-datepicker';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
//you need to import the CSS manually (in case you want to override it)
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    // name:{disabled_dates},
    components: { datatable: Datatable, pagination: Pagination, DatePicker },
    created() {
        this.getProjects();
    },
    data() {
        let sortOrders = {};
        let columns = [
            {width: '20%', label: 'Tanggal'},
            {width: '20%', label: 'Jam'},
            {width: '20%', label: 'NIK', name: 'nik_pegawai' },
            {width: '20%', label: 'Nama'},
            {width: '20%', label: 'Lokasi'},
        ];
        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            isActive: false,
            isActive2: false,
            isActive1: false,
            projects: [],
            columns: columns,
            loading: false,
            sortKey: 'first_name',
            sortOrders: sortOrders,
            length: 10,
            search: '',
            tableData: {
                client: true,
            },
            pagination: {
                currentPage: 1,
                total: '',
                nextPage: '',
                prevPage: '',
                from: '',
                to: ''
            },
            time1: null,
            waterMark : new Date().toISOString().slice(0,10),
            currentImageLt3: '/images/lantai3.png',
            currentImageLt2: '/images/lantai2.png',
            currentImageLt1: '/images/lantai1.png',

        }
    },
    methods: {
        dateFormat (classes, date) {
            if (!classes.disabled) {
            classes.disabled = date.getTime() < new Date()
            }
            return classes
        },
        changeImageWhen: function (state) {
            this.isActive = !this.isActive;
            var newEvent = this.isActive === true ? '/images/lantai3-sorot.png' : '/images/lantai3.png';
            this.isActive2 = false;
            var newEvent2 = this.isActive2 === true ? '/images/lantai2-sorot.png' : '/images/lantai2.png';
            this.isActive1 = false;
            var newEvent1 = this.isActive1 === true ? '/images/lantai1-sorot.png' : '/images/lantai1.png';
            // this.isActive2 = false;
            this.currentImageLt1 = newEvent1
            this.currentImageLt2 = newEvent2
            this.currentImageLt3 = newEvent

            if(this.isActive === true) {
                this.loading = true
                axios.get('pantau', 
                 {
                    params: {
                    lantai: "3"
                    }
                })
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                }); 
            }else{
                this.loading = true
                axios.get('pantau', 
                 {
                    params: this.tableData
                })
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });  

            }
        },
        changeImageWhen2: function (state) {
            // this.isActive = false;
            this.isActive2 = !this.isActive2;
            // this.isActive1 = false;
            var newEvent = this.isActive2 === true ? '/images/lantai2-sorot.png' : '/images/lantai2.png';
            this.isActive = false;
            var newEvent3 = this.isActive === true ? '/images/lantai3-sorot.png' : '/images/lantai3.png';
            this.isActive1 = false;
            var newEvent1 = this.isActive1 === true ? '/images/lantai1-sorot.png' : '/images/lantai1.png';
            // this.isActive2 = false;
            this.currentImageLt1 = newEvent1
            this.currentImageLt3 = newEvent3
            this.currentImageLt2 = newEvent
            if(this.isActive2 === true) {
                this.loading = true
                axios.get('pantau', 
                 {
                    params: {
                    lantai: "2"
                    }
                })
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });  
            }else{
                this.loading = true
                axios.get('pantau', 
                 {
                    params: this.tableData
                })
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });  

            }
        },
        changeImageWhen1: function (state) {
            this.isActive1 = !this.isActive1;
            var newEvent = this.isActive1 === true ? '/images/lantai1-sorot.png' : '/images/lantai1.png'
            this.isActive2 = false;
            var newEvent2 = this.isActive2 === true ? '/images/lantai2-sorot.png' : '/images/lantai2.png';
            this.isActive = false;
            var newEvent3 = this.isActive === true ? '/images/lantai3-sorot.png' : '/images/lantai3.png';
            this.currentImageLt1 = newEvent
            this.currentImageLt2 = newEvent2
            this.currentImageLt3 = newEvent3
            
            if(this.isActive1 === true) {
                this.loading = true
                axios.get('pantau', 
                 {
                    params: {
                    lantai: "1"
                    }
                })
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });  
            }else{
                this.loading = true
                axios.get('pantau', 
                 {
                    params: this.tableData
                })
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                }); 

            }
                
        },
        dateFormat (classes, date) {
            if (!classes.disabled) {
            classes.disabled = date.getTime() < new Date()
            }
            return classes
        },
      
      doMath: function (index) {
        return index+1
      },
      
        getProjects() {
            this.loading = true
            axios.get('pantau', {params: this.tableData})
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
        },
        filterTanggal() {
            this.loading = true
            axios.get('pantau', 
                {params: this.tableData}
            )
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
        },
        deleteData(id) {
            this.loading = true
        // delete data
          axios.delete("pengguna/" + id).then(response => {
            this.getProjects();
            // $swal function calls SweetAlert into the application with the specified configuration.
            this.$swal('Deleted', 'You successfully deleted this file', 'success');
          }).finally(() => {
            this.loading =  false
        });
        },
        paginate(array, length, pageNumber) {
            this.pagination.from = array.length ? ((pageNumber - 1) * length) + 1 : ' ';
            this.pagination.to = pageNumber * length > array.length ? array.length : pageNumber * length;
            this.pagination.prevPage = pageNumber > 1 ? pageNumber : '';
            this.pagination.nextPage = array.length > this.pagination.to ? pageNumber + 1 : '';
            return array.slice((pageNumber - 1) * length, pageNumber * length);
        },
        resetPagination() {
            this.pagination.currentPage = 1;
            this.pagination.prevPage = '';
            this.pagination.nextPage = '';
        },
        sortBy(key) {
            this.resetPagination();
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
    },
    computed: {
        classObject: function () {
            return {
            active: this.isActive && !this.error,
            'text-danger': this.error && this.error.type === 'fatal'
            }
        },
        filteredProjects() {
            let projects = this.projects;
            if (this.search) {
                projects = projects.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
                    })
                });
            }
            let sortKey = this.sortKey;
            let order = this.sortOrders[sortKey] || 1;
            if (sortKey) {
                projects = projects.slice().sort((a, b) => {
                    let index = this.getIndex(this.columns, 'name', sortKey);
                    a = String(a[sortKey]).toLowerCase();
                    b = String(b[sortKey]).toLowerCase();
                    // if (this.columns[index].type && this.columns[index].type === 'date') {
                    //     return (a === b ? 0 : new Date(a).getTime() > new Date(b).getTime() ? 1 : -1) * order;
                    // } else if (this.columns[index].type && this.columns[index].type === 'number') {
                    //     return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;
                    // } else {
                    //     return (a === b ? 0 : a > b ? 1 : -1) * order;
                    // }
                });
            }
            return projects;
        },
        paginated() {
            return this.paginate(this.filteredProjects, this.length, this.pagination.currentPage);
        }
    }
};
</script>