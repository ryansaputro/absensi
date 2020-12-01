<template>
    <div class="projects">
        <div class="loader" v-if="loading"></div>
      <div class="user-data m-b-30 p-3">
          <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="periode">Periode</label>
                    </div>
                    <div class="col-md-6">
                        <date-picker :placeholder="waterMark" style="width:100%;" id="periode"  v-model="time1" @change="filterTanggal()" range  valueType="format"></date-picker>
                    </div>
                    <div class="col-md-6">
                        <label for="filterBy">Filter</label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <select v-model="filterBy" @change="filterWith()" id="filterBy" name="filterBy" class="form-control">
                            <option value="terlambat_paling_banyak" selected>Terlambat Paling Banyak</option>
                            <option value="terlambat_paling_sedikit">Terlambat Paling Sedikit</option>
                            <option value="total_terlambat_paling_banyak">Total Terlambat Paling Banyak</option>
                            <option value="total_terlambat_paling_sedikit">Total Terlambat Paling Sedikit</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="filterBy">Pencarian</label>
                    </div>
                    <div class="col-md-6">
                        <input class="input form-control input-sm" type="text" @input="filterTanggal()" v-model="search" placeholder="NIK, Nama">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                        <b-dropdown text="Export" variant="primary" class="pull-right">
                        <b-dropdown-item href="#" v-if="$can('export-pdf-rekap-keterlambatan')"><button type="button" class="btn" @click="downloadWithCSS">PDF</button></b-dropdown-item>
                        <b-dropdown-item href="#" v-if="$can('export-xls-rekap-keterlambatan')">
                            <downloadexcel
                                class = "btn"
                                :fetch   = "fetchData"
                                :fields = "json_fields"
                                :before-generate = "startDownload"
                                :before-finish = "finishDownload"
                                type    = "xls">
                                Excel
                            </downloadexcel>
                        </b-dropdown-item>
                    </b-dropdown>
                    </div>
                </div>
            </div>
          </div>
      </div>
      <div class="user-data m-b-30 p-3">
        <div style="overflow-x:auto;">
        <span>Filter : {{jmlKerja+" Hari"}}</span>
        <table class="table table-bordered table-hover" id="my-table">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th class="text-right">Kehadiran</th>
                    <th class="text-right">Terlambat</th>
                    <th class="text-right">Total Durasi Terlambat</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(project, index) in karyawanAbsen" :key="project.id" v-if="filterBy === 'terlambat_paling_banyak' || filterBy === 'total_terlambat_paling_banyak' ">
                    <td>{{project.nik_pegawai}}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td class="text-right">{{project.kehadiran}}</td>
                    <td class="text-right">{{project.terlambat}} kali</td>
                    <td class="text-right">{{project.menit_terlambat}}</td>
                </tr>
                <tr v-for="(project, index) in karyawanNotAbsen" :key="project.id">
                    <td>{{project.nik_pegawai}}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td class="text-right">{{typeof(kehadiran[project.id]) === 'undefined' ? '0' : kehadiran[project.id] }}</td>
                    <td class="text-right">{{typeof(jmlTelat[project.id]) !== 'undefined' ?   jmlTelat[project.id] : '0'}} kali</td>
                    <td class="text-right">{{typeof(jmlMenit[project.id]) !== 'undefined' ?   jmlMenit[project.id] : '0'}}</td>
                </tr>
                <tr v-for="(project, index) in karyawanAbsen" :key="project.id" v-if="filterBy === 'terlambat_paling_sedikit' || filterBy === 'total_terlambat_paling_sedikit' ">
                    <td>{{project.nik_pegawai}}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td class="text-right">{{project.kehadiran}}</td>
                    <td class="text-right">{{project.terlambat}} kali</td>
                    <td class="text-right">{{project.menit_terlambat}}</td>
                </tr>
            </tbody>
        </table>
        </div>
      </div>
    </div>
</template>
<script src="https://unpkg.com/jspdf-autotable@3.5.12/dist/jspdf.plugin.autotable.js"></script>
<script>
import Datatable from '../../../components/Datatables.vue';
import Pagination from '../../../components/Pagination.vue';
import DateRangePicker from 'vue2-daterange-picker'
// import Datepicker from 'vuejs-datepicker';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
//you need to import the CSS manually (in case you want to override it)
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import jsPDF from 'jspdf' 
import 'jspdf-autotable'
import autoTable from 'jspdf-autotable'
import JsonExcel from 'vue-json-excel'

export default {
    // name:{disabled_dates},
    components: { datatable: Datatable, pagination: Pagination, DatePicker,downloadexcel:JsonExcel  },
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
            jmlKerja: '1',
            filterBy:'terlambat_paling_banyak',
            statusMasuk: [],
            jmlMenit: [],
            jmlTelat: [],
            loading: false,
            kehadiran: [],
            tanggal: [],
            karyawanAbsen: [],
            karyawanNotAbsen: [],
            absen:[],
            jamMasuk:[],
            jamKeluar:[],
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
            // time1: null,
            time1: moment(new Date()).format('YYYY-M-D'),
            waterMark : new Date().toISOString().slice(0,10),
            json_fields: {
                NIK: "nik_pegawai", //Normal field
                NAMA: "nama_lengkap", //Supports nested properties
                KEHADIRAN: "kehadiran", //Supports nested properties
                TERLAMBAT: "jumlah_telat", //Supports nested properties
                "TOTAL TERLAMBAT": "jumlah_menit", //Supports nested properties
            },

        }
    },
    methods: {
      dateFormat (classes, date) {
        if (!classes.disabled) {
          classes.disabled = date.getTime() < new Date()
        }
        return classes
      },
       downloadWithCSS() {
        const doc = new jsPDF()
        var header = function (data) {
            doc.text("Rekap Keterlambatan", data.settings.margin.left, 10);
        };

        autoTable(doc, {didDrawPage : header, html: '#my-table'});
        // autoTable(doc, { html: '#my-table' })
        doc.save('rekap-keterlambatan.pdf')
        },
        async fetchData(){
        const response = await axios.get('rekap-export-excel', 
             {
                params: {
                tanggal: this.time1,
                search: this.search
                }
            });
        console.log(response.data);
        return response.data;
        },
        startDownload(){
            console.log('show loading');
        },
        finishDownload(){
            console.log('hide loading');
        },
      
        getProjects() {
            this.loading = true
            axios.get('rekap-keterlambatan', {params: this.tableData})
                .then(response => {
                    var waktu = this.time1;
                    var cekArr = Array.isArray(waktu);

                    this.karyawanAbsen = response.data.dataKehadiranTerlambat;
                    this.karyawanNotAbsen = response.data.karyawanNotAbsen;
                    this.jmlMenit = response.data.jmlMenit;
                    this.jmlTelat = response.data.jmlTelat;
                    this.absen = response.data.absen;

                    // var now = new Date();
                    var start = cekArr == true ? new Date(waktu[0]) : new Date();
                    var end = cekArr == true ? new Date(waktu[1]) : new Date();
                   

                    var jmlKehadiran= [];
                    $.each(response.data.kehadiran, function(k,v){
                            jmlKehadiran[k] = v;
                    })
                    this.kehadiran = jmlKehadiran;

                    var StatusKehadiran= [];
                    $.each(response.data.statusMasuk, function(k,v){
                        StatusKehadiran[k] = new Array(2);
                        $.each(v, function(x,y){
                            
                            StatusKehadiran[k][x] = new Array(2);
                            StatusKehadiran[k][x] = y;
                        })

                    })
                    
                    this.statusMasuk = StatusKehadiran;
                     // To set two dates to two variables 
                    var date1 = start; 
                    var date2 = end; 
                    
                    // To calculate the time difference of two dates 
                    var Difference_In_Time = date2.getTime() - date1.getTime(); 
                    
                    // To calculate the no. of days between two dates 
                    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
                    // console.log(Difference_In_Days)
                    this.jmlKerja = (parseInt(Difference_In_Days)+1);

                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
        },
        filterTanggal() {
            this.loading = true
            axios.get('rekap-keterlambatan', 
             {
                params: {
                tanggal: this.time1,
                search: this.search,
                filterby: this.filterBy
                }
            })
                .then(response => {
                    var waktu = this.time1;
                    var cekArr = Array.isArray(waktu);

                    this.karyawanAbsen = response.data.dataKehadiranTerlambat;
                    this.karyawanNotAbsen = response.data.karyawanNotAbsen;
                    this.jmlMenit = response.data.jmlMenit;
                    this.jmlTelat = response.data.jmlTelat;
                    this.absen = response.data.absen;

                    var start = cekArr == true ? new Date(waktu[0]) : new Date();
                    var end = cekArr == true ? new Date(waktu[1]) : new Date();
                   
                    var jmlKehadiran= [];
                    $.each(response.data.kehadiran, function(k,v){
                            jmlKehadiran[k] = v;
                    })
                    this.kehadiran = jmlKehadiran;

                    var StatusKehadiran= [];
                    $.each(response.data.statusMasuk, function(k,v){
                        StatusKehadiran[k] = new Array(2);
                        $.each(v, function(x,y){
                            
                            StatusKehadiran[k][x] = new Array(2);
                            StatusKehadiran[k][x] = y;
                        })

                    })
                    
                    this.statusMasuk = StatusKehadiran;
                     // To set two dates to two variables 
                    var date1 = start; 
                    var date2 = end; 
                    
                    // To calculate the time difference of two dates 
                    var Difference_In_Time = date2.getTime() - date1.getTime(); 
                    
                    // To calculate the no. of days between two dates 
                    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
                    this.jmlKerja = parseInt(Difference_In_Days)+1;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
        },
        filterWith() {
            this.loading = true
            axios.get('rekap-keterlambatan', 
             {
                params: {
                tanggal: this.time1,
                search: this.search,
                filterby: this.filterBy
                }
            })
                .then(response => {
                    var waktu = this.time1;
                    var cekArr = Array.isArray(waktu);

                    this.karyawanAbsen = response.data.dataKehadiranTerlambat;
                    this.karyawanNotAbsen = response.data.karyawanNotAbsen;
                    this.jmlMenit = response.data.jmlMenit;
                    this.jmlTelat = response.data.jmlTelat;
                    this.absen = response.data.absen;

                    var start = cekArr == true ? new Date(waktu[0]) : new Date();
                    var end = cekArr == true ? new Date(waktu[1]) : new Date();
                   
                    var jmlKehadiran= [];
                    $.each(response.data.kehadiran, function(k,v){
                            jmlKehadiran[k] = v;
                    })
                    this.kehadiran = jmlKehadiran;

                    var StatusKehadiran= [];
                    $.each(response.data.statusMasuk, function(k,v){
                        StatusKehadiran[k] = new Array(2);
                        $.each(v, function(x,y){
                            
                            StatusKehadiran[k][x] = new Array(2);
                            StatusKehadiran[k][x] = y;
                        })

                    })
                    
                    this.statusMasuk = StatusKehadiran;
                     // To set two dates to two variables 
                    var date1 = start; 
                    var date2 = end; 
                    
                    // To calculate the time difference of two dates 
                    var Difference_In_Time = date2.getTime() - date1.getTime(); 
                    
                    // To calculate the no. of days between two dates 
                    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
                    this.jmlKerja = parseInt(Difference_In_Days)+1;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
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