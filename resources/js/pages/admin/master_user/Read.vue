<template>
    <div class="projects">
      <div class="user-data m-b-30 p-3">
        <div class="tableFilters m-b-30">
          <div class="row">
            <div class="col-md-2">
              <router-link v-if="$can('read-absensi')" class="btn btn-primary w-100" to="pengguna/create">+ Tambah</router-link>
            </div>
            <div class="col-md-4">
              <input class="input form-control" type="text" v-model="search" placeholder="Search Table"
                   @input="resetPagination()">
            </div>
            <div class="col-md-6">
              <div class="control pull-right">
                <div class="select form-control">
                    <select v-model="length" @change="resetPagination()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
            </div>
          </div>
        </div>
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
                <tr v-for="(project, index) in paginated" :key="project.id">
                    <td>{{ doMath(index) }}</td>
                    <td>{{project.no_ktp}}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td>
                      <router-link class="btn btn-primary btn-xs" :to="'/pengguna/'+project.id">Edit</router-link>
                      <button class="btn btn-danger btn-xs" v-on:click="deleteData(project.id)">Delete</button>
                    </td>
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
export default {
    components: { datatable: Datatable, pagination: Pagination },
    created() {
        this.getProjects();
    },
    data() {
        let sortOrders = {};
        let columns = [
            {width: '10%', label: '#', name: 'no' },
            {width: '30%', label: 'KTP', name: 'ktp'},
            {width: '30%', label: 'Nama Lengkap', name: 'nama_lengkap' },
            {width: '30%', label: 'Aksi', name: 'action'}
        ];
        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            projects: [],
            columns: columns,
            sortKey: 'nama_lengkap',
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
        }
    },
    methods: {
      doMath: function (index) {
        return index+1
      },
        getProjects(url = '/person') {
            axios.get('pengguna', {params: this.tableData})
                .then(response => {
                    this.projects = response.data;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        deleteData(id) {
        // delete data
          axios.delete("pengguna/" + id).then(response => {
            this.getProjects();
            // $swal function calls SweetAlert into the application with the specified configuration.
            this.$swal('Deleted', 'You successfully deleted this file', 'success');
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