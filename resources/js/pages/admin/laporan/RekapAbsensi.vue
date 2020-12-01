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
                            <b-dropdown-item href="#" v-if="$can('export-pdf-rekap-absensi')"><button type="button" class="btn" @click="downloadWithCSS">PDF</button></b-dropdown-item>
                            <b-dropdown-item href="#" v-if="$can('export-xls-rekap-absensi')">
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
                    <th style="width:15%;">NIKs</th>
                    <th style="width:50%;">Nama</th>
                    <th>Kehadiran</th>
                    <th>I</th>
                    <th>S</th>
                    <th>A</th>
                    <th>C</th>
                    <th>LK</th>
                    <!-- <th>Jml Hari</th> -->
                </tr>
            </thead>
            <tbody>
                <tr v-for="(project, index) in projects" :key="project.id">
                    <td>
                        {{project.nik_pegawai}}
                    </td>
                    <td>
                        {{project.nama_lengkap}}
                    </td>
                    <td>{{typeof(kehadiran[project.id]) === 'undefined' ? '0' : kehadiran[project.id] }}</td>
                    <td>{{typeof(statusMasuk[project.id]) !== 'undefined' ?  typeof(statusMasuk[project.id]['I']) !== 'undefined' ? statusMasuk[project.id]['I'] : '0'  : '0'}}</td>
                    <td>{{typeof(statusMasuk[project.id]) !== 'undefined' ?  typeof(statusMasuk[project.id]['S']) !== 'undefined' ? statusMasuk[project.id]['S'] : '0'  : '0'}}</td>
                    <td>{{typeof(statusMasuk[project.id]) !== 'undefined' ?  typeof(statusMasuk[project.id]['A']) !== 'undefined' ? statusMasuk[project.id]['A'] : '0'  : '0'}}</td>
                    <td>{{typeof(statusMasuk[project.id]) !== 'undefined' ?  typeof(statusMasuk[project.id]['C']) !== 'undefined' ? statusMasuk[project.id]['C'] : '0'  : '0'}}</td>
                    <td>{{typeof(statusMasuk[project.id]) !== 'undefined' ?  typeof(statusMasuk[project.id]['LK']) !== 'undefined' ? statusMasuk[project.id]['LK'] : '0'  : '0'}}</td>
                    <!-- <td>{{jmlKerja}} Hari</td> -->
                </tr>
            </tbody>
            <!-- <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th>1200</th>
                    <th>20</th>
                    <th>15</th>
                    <th>10</th>
                    <th>10</th>
                </tr>
            </tfoot> -->
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
            statusMasuk: [],
            kehadiran: [],
            tanggal: [],
            projects: [],
            absen:[],
            jamMasuk:[],
            jamKeluar:[],
            columns: columns,
            sortKey: 'first_name',
            sortOrders: sortOrders,
            length: 10,
            loading: false,
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
                "NIK": "nik_pegawai", //Normal field
                NAMA: "nama_lengkap", //Supports nested properties
                KEHADIRAN: "kehadiran", //Supports nested properties
                "IZIN": "ijin", //Supports nested properties
                "SAKIT": "sakit", //Supports nested properties
                "ALPA": "alasan", //Supports nested properties
                "CUTI": "cuti", //Supports nested properties
                "LUAR KOTA": "lk", //Supports nested properties
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
            doc.text("Rekap Absensi", data.settings.margin.left, 10);
        };

        autoTable(doc, {didDrawPage : header, html: '#my-table'});
        // autoTable(doc, { html: '#my-table' })
        doc.save('laporan-absensi.pdf')
        },
        async fetchData(){
        const response = await axios.get('export-excel', 
             {
                params: {
                tanggal: this.time1
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
      
        getProjects() {
            this.loading = true
            axios.get('laporan-semua', {params: this.tableData})
                .then(response => {
                    var waktu = this.time1;
                    var cekArr = Array.isArray(waktu);

                    this.projects = response.data.karyawan;
                    this.absen = response.data.absen;

                    // var now = new Date();
                    var start = cekArr == true ? new Date(waktu[0]) : new Date();
                    var end = cekArr == true ? new Date(waktu[1]) : new Date();
                   
                    // var dateArray = [];
                    // var currentDate = moment(start);
                    // var stopDate = moment(end);
                    // while (currentDate <= stopDate) {
                    //     dateArray.push( moment(currentDate).format('YYYY-M-D') )
                    //     currentDate = moment(currentDate).add(1, 'days');
                    // }

                    // var absenMasuk = [];
                    // var absenKeluar = [];
                    // var idx = [];
                    // var idy = [];

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

                    // $.each(response.data.absen, function(k,v){
                    //     $.each(v, function(x,y){
                    //         idx[x] = y;
                    //         absenMasuk[k] = new Array(2);
                    //         absenMasuk[k] = idx;
                    //     })
                    // })

                    // $.each(response.data.keluar, function(k,v){
                    //     $.each(v, function(x,y){
                    //         idy[x] = y;
                    //         absenKeluar[k] = new Array(2);
                    //         absenKeluar[k] = idy;
                    //     })
                    // })
                    //     this.jamMasuk = absenMasuk;
                    //     this.jamKeluar = absenKeluar;
                    //     this.tanggal = dateArray;
                        
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
        },
        filterTanggal() {
            this.loading = true
            axios.get('laporan-semua', 
             {
                params: {
                tanggal: this.time1,
                search: this.search
                }
            })
                .then(response => {
                    var waktu = this.time1;
                    var cekArr = Array.isArray(waktu);

                    this.projects = response.data.karyawan;
                    this.absen = response.data.absen;

                    // var now = new Date();
                    var start = cekArr == true ? new Date(waktu[0]) : new Date();
                    var end = cekArr == true ? new Date(waktu[1]) : new Date();
                   
                    // var dateArray = [];
                    // var currentDate = moment(start);
                    // var stopDate = moment(end);
                    // while (currentDate <= stopDate) {
                    //     dateArray.push( moment(currentDate).format('YYYY-M-D') )
                    //     currentDate = moment(currentDate).add(1, 'days');
                    // }

                    // var absenMasuk = [];
                    // var absenKeluar = [];
                    // var idx = [];
                    // var idy = [];

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
                    this.jmlKerja = parseInt(Difference_In_Days)+1;

                    // $.each(response.data.absen, function(k,v){
                    //     $.each(v, function(x,y){
                    //         idx[x] = y;
                    //         absenMasuk[k] = new Array(2);
                    //         absenMasuk[k] = idx;
                    //     })
                    // })

                    // $.each(response.data.keluar, function(k,v){
                    //     $.each(v, function(x,y){
                    //         idy[x] = y;
                    //         absenKeluar[k] = new Array(2);
                    //         absenKeluar[k] = idy;
                    //     })
                    // })
                    //     this.jamMasuk = absenMasuk;
                    //     this.jamKeluar = absenKeluar;
                    //     this.tanggal = dateArray;
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