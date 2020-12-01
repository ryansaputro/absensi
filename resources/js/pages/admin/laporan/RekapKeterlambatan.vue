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
                        <date-picker :placeholder="waterMark" style="width:100%;" id="periode"  v-model="time1" @change="filterWith()" range  valueType="format"></date-picker>
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
                        <input class="input form-control input-sm" type="text" v-model="search" placeholder="NIK, Nama">
                        <!-- <input class="input form-control input-sm" type="text" @input="filterTanggal()" v-model="search" placeholder="NIK, Nama"> -->
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
        <table class="table table-bordered table-hover xxxnx" id="my-table">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th class="text-right">Kehadiran</th>
                    <th class="text-right">Terlambat</th>
                    <th class="text-right">Total Durasi Terlambat (Menit)</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(data, index) in AllData" :key="data.id">
                    <td style="width:15%;">{{data.nik_pegawai}}</td>
                    <td>{{data.nama_lengkap}}</td>
                    <td class="text-right">{{data.kehadiran}}</td>
                    <td class="text-right">{{data.keterlambatan != 0 ? data.keterlambatan : 0}} kali</td>
                    <td class="text-right">{{data.total_terlambat}} menit</td>
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
    components: { datatable: Datatable, pagination: Pagination, DatePicker,downloadexcel:JsonExcel  },
    created() {
        this.getProjects();
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
            loading: false,
            search: '',
            datas: [],
            tableData: {
                client: true,
            },
            time1: moment(new Date()).format('YYYY-M-D'),
            waterMark : new Date().toISOString().slice(0,10),
            json_fields: {
                NIK: "nik_pegawai", //Normal field
                NAMA: "nama_lengkap", //Supports nested properties
                KEHADIRAN: "kehadiran", //Supports nested properties
                TERLAMBAT: "keterlambatan", //Supports nested properties
                "TOTAL TERLAMBAT (Menit)": "total_terlambat", //Supports nested properties
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

        // export pdf
       downloadWithCSS() {
            const doc = new jsPDF()
            var header = function (data) {
                doc.text("Rekap Keterlambatan", data.settings.margin.left, 10);
            };

            autoTable(doc, {didDrawPage : header, html: '#my-table'});
            // autoTable(doc, { html: '#my-table' })
            doc.save('rekap-keterlambatan.pdf')
        },

        // export excel
        async fetchData(){
        const response = await axios.get('rekap-keterlambatan', 
             {
                params: {
                    tanggal: this.time1,
                    search: this.search,
                    filterby: this.filterBy
                }
            });
        return response.data.data;
        },
        startDownload(){
            this.loading = true
        },
        finishDownload(){
            this.loading = false
        },
      
        //get data for the first time
        getProjects() {
            this.loading = true
            axios.get('rekap-keterlambatan', {params: this.tableData})
                .then(response => {
                    this.datas = response.data.data;
                })
                .catch(errors => {
                    console.log(errors);
                }).finally(() => {
                    this.loading =  false
                });
        },

        //filter by date or anything 
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
                    this.datas = response.data.data;
                    var waktu = this.time1;
                    var cekArr = Array.isArray(waktu);
                    var start = cekArr == true ? new Date(waktu[0]) : new Date();
                    var end = cekArr == true ? new Date(waktu[1]) : new Date();
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
            let projects = this.datas;
            if (this.search) {
                projects = projects.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
                    })
                });
            }

            return projects;
        },
        AllData() {
            return this.filteredProjects;
        }
    }
};
</script>