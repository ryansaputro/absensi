<template>
    <div class="projects">
        <div class="loader" v-if="loading"></div>
        <div class="user-data m-b-30 p-3">
          <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="periode">Tanggal</label>
                    </div>
                    <div class="col-md-6">
                        <date-picker :placeholder="waterMark" style="width:100%;" v-model="time1" @change="filterTanggal()" valueType="format"></date-picker>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="filterBy">Pencarian</label>
                    </div>
                    <div class="col-md-6">
                        <input class="input form-control input-sm" type="text" @input="filterTanggal()" v-model="search" placeholder="NIK, Nama">
                    </div>
                    <div class="col-md-6">
                        <label for="filterBy">Data/Halaman</label>
                    </div>
                    <div class="col-md-6">
                        <select class="select form-control" v-model="length" @change="resetPagination()">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
          </div>
      </div>

      <div class="user-data m-b-30 p-3">
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
                <tr v-for="(project, index) in paginated" :key="project.id">
                    <td>{{ project.no_ktp }}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td>{{project.masuk}}</td>
                    <!-- <td>{{project.jam <= '12:00' ? project.jam : '-'}}</td> -->
                    <!-- <td>{{project.keluar >= '12.01' ? project.keluar : '-'}}</td> -->
                    <td>{{project.keluar}}</td>
                    <td>{{project.masuk > '08:05' ? 'Terlambat' : 'Tepat'}}</td>
                </tr>
                <tr v-if="paginated.length <= 0">
                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                </tr>
            </tbody>
        </datatable>
        <pagination :pagination="pagination" :client="true" :filtered="filteredProjects"
                    @prev="--pagination.currentPage"
                    @next="++pagination.currentPage">
        </pagination>
      </div>
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
            {width: '20%', label: 'NIK', name: 'no' },
            {width: '20%', label: 'Nama'},
            {width: '20%', label: 'Jam Masuk'},
            {width: '20%', label: 'Jam Keluar'},
            {width: '20%', label: 'Status'},
        ];
        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            projects: [],
            columns: columns,
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
            loading: false,
        }
    },
    methods: {
      dateFormat (classes, date) {
        if (!classes.disabled) {
          classes.disabled = date.getTime() < new Date()
        }
        return classes
      },
      
        getProjects() {
        this.loading = true
            axios.get('list-absensi', {params: this.tableData})
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
            axios.get('list-absensi', 
             {
                params: {
                tanggal: this.time1
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