<template>
    <div class="projects">
      <div class="user-data m-b-30 p-3">
        <div class="tableFilters m-b-30">
          <div class="row">
            <div class="col-md-6">
              <!-- <input class="input form-control" type="text" v-model="search" placeholder="Search Table"
                   @input="resetPagination()"> -->
                    <!-- <date-range-picker
                            :date-format="dateFormat"
                            v-model="dateRange"
                    >
                    </date-range-picker> -->
                    <date-picker :placeholder="waterMark" v-model="time1" @change="filterTanggal()" range  valueType="format"></date-picker>
                    <!-- <date-picker v-model="time2" type="datetime"></date-picker>
                    <date-picker v-model="time3" range></date-picker> -->
   
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
        <!-- <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
                <tr v-for="(project, index) in paginated" :key="project.id">
                    <td>{{ project.tanggal }}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td>{{project.masuk}}</td>
                    <td>{{project.keluar}}</td>
                    <td>{{doMath(project.jml_kerja)}}</td>
                </tr>
            </tbody>
        </datatable>
        <pagination :pagination="pagination" :client="true" :filtered="filteredProjects"
                    @prev="--pagination.currentPage"
                    @next="++pagination.currentPage">
        </pagination> -->
        <div style="overflow-x:auto;">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th style="min-width:100px;font-size:12px;" v-for="(tgl, index) in tanggal">{{tgl}}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(project, index) in projects" :key="project.id">
                    <td>
                        {{project.nama_lengkap}}
                    </td>
                    <td v-for="(tgl, index) in tanggal">
                        <span style="color:green;font-size:12px;">IN : </span> <br> <span style="color:red;font-size:12px;">OUT :17:00</span>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
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
        // this.getIn();
        // this.karyawan();
    },
    data() {
        let sortOrders = {};
        let columns = [
            {width: '20%', label: 'Tanggal', name: 'tanggal' },
            {width: '20%', label: 'Nama'},
            {width: '20%', label: 'Absen Masuk'},
            {width: '20%', label: 'Absen Keluar'},
            {width: '20%', label: 'Durasi Kerja'},
        ];
        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            tanggal: [],
            projects: [],
            absen:[],
            jamMasuk:[],
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
        }
    },
    methods: {
      dateFormat (classes, date) {
        if (!classes.disabled) {
          classes.disabled = date.getTime() < new Date()
        }
        return classes
      },
      
      doMath: function (jml_kerja) {
        var m1  = jml_kerja.toString();
        var waktu1 = m1.split(":");
        var jam = parseInt(waktu1[0]) > 0 ? parseInt(waktu1[0]) + ' jam ' : '';
        var menit = parseInt(waktu1[1]) > 0 ? parseInt(waktu1[1]) + ' menit' : '';
        var tot = jam + menit;
        // jam = menit >= 60 ? parseInt(jam)+1 +' jam' : jam+ ' jam';
        // menit = menit >= 60 ? parseInt(menit)-60 == 0 ? '' : parseInt(menit)-60 + ' menit' : menit + ' menit' ;
        // var tot = '- '+jam+' '+menit;
        return tot;
      },
      getIn: function(nama, tgl){
          console.log(nama)
      },
      
      statusMasuk: function (status){
          if(status == 'Terlambat')
          return "danger"
          else
          return "success"
      },
        getProjects() {
            axios.get('laporan-semua', {params: this.tableData})
                .then(response => {
                    this.projects = response.data.karyawan;
                    this.absen = response.data.absen;
                    var now = new Date();
                    var start = new Date('2020-09-01'),
                    end = new Date('2020-09-20'),
                    year = start.getFullYear(),
                    month = start.getMonth(),
                    day = start.getDate(),
                    dates = [start];
                    var newDate = ['2020-09-01'];

                while(dates[dates.length-1] < end) {
                dates.push(new Date(year, month, ++day));
                    newDate.push(year+'-'+month+'-'+day)
                }

                var absenMasuk = [];
                $.each(response.data.absen, function(k,v){
                    $.each(v, function(x,y){
                        var idx = x.replace('-', '');
                        idx = idx.replace('-', '');
                        absenMasuk[k][idx] = 'a';
                        // console.log(idx)

                    })

                })
                // this.jamMasuk = absenMasuk;
                console.log(absenMasuk)
                
                this.tanggal = newDate;
                    
                    // this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        filterTanggal() {
            axios.get('laporan-semua', 
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
                });
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