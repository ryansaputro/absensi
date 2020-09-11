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
            <div class="col-md-3">
                <button @click="downloadWithCSS" class="btn btn-sm btn-primary">Download PDF</button>
            </div>
            <div class="col-md-3">
              <div class="control pull-right">
                <div>
                    <select class="select form-control" v-model="length" @change="resetPagination()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
            </div>
          </div>
        </div>
        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy" id="my-table">
            <tbody>
                <tr v-for="(project, index) in paginated" :key="project.id">
                    <td>{{ project.tanggal }}</td>
                    <td>{{project.nama_lengkap}}</td>
                    <td>{{project.masuk}}</td>
                    <td>{{project.keluar}}</td>
                    <td style="color:red;">{{doMath(project.masuk)}}</td>
                    <td style="color:red;">{{doKeluar(project.keluar)}}</td>
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

export default {
    // name:{disabled_dates},
    components: { datatable: Datatable, pagination: Pagination, DatePicker },
    created() {
        this.getProjects();
    },
    data() {
        let sortOrders = {};
        let columns = [
            {width: '20%', label: 'Tanggal', name: 'tanggal' },
            {width: '20%', label: 'Nama'},
            {width: '15%', label: 'Absen Masuk'},
            {width: '15%', label: 'Absen Keluar'},
            {width: '15%', label: 'Terlambat'},
            {width: '15%', label: 'Pulang Awal'},
        ];
        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            projects: [],
            exportPDF: [],
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
            toolbarOptions: ['PdfExport'],
        }
    },
    methods: {
    // toolbarClick(args) {
    //     if (args.item.id === 'Grid_pdfexport') { // 'Grid_pdfexport' -> Grid component id + _ + toolbar item name
    //         this.$refs.grid.pdfExport();
    //     }
    // },
    // dateFormat (classes, date) {
    //     if (!classes.disabled) {
    //       classes.disabled = date.getTime() < new Date()
    //     }
    //     return classes
    // },
      
    doMath: function (masuk) {
        var m1  = masuk.toString();
        var waktu1 = m1.split(":");
        var jamMasuk = waktu1[0]-8 < 0 ? 0 : waktu1[0]-8;
        var menitMasuk = parseInt(waktu1[1]);
        var tot = jamMasuk+ ' Jam '+ menitMasuk +' Menit';
        tot = jamMasuk == 0 ? '0 menit' : tot;
        return tot;
      },
      
    doKeluar: function (keluar) {
        // var tot = ( new Date("1970-1-1 17:00") - new Date("1970-1-1 16:54") ) / 1000 / 60 / 60 ;
        var date1 = new Date("1970-1-1 17:00");
        var date2 = new Date("1970-1-1 "+keluar)
        var res = Math.abs(date1 - date2) / 1000

        // get hours        
         var hours = Math.floor(res / 3600) % 24;        
        //  document.write("<br>Difference (Hours): "+hours);  
         
         // get minutes
        var minutes = Math.floor(res / 60) % 60;
        hours = isNaN(hours) ? '' : hours <= 0 ? '' : hours+ ' Jam '
        minutes = isNaN(minutes) ? '' : minutes + ' Menit';
        var tot = hours + minutes ;
        return tot;
      },
      
    getProjects() {
            axios.get('laporan-terlambat', {params: this.tableData})
                .then(response => {
                    this.projects = response.data.data;
                    this.exportPDF = response.data.data2;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
    filterTanggal() {
            axios.get('laporan-terlambat', 
             {
                params: {
                tanggal: this.time1
                }
            })
                .then(response => {
                    this.projects = response.data.data;
                    this.exportPDF = response.data.data2;
                    this.pagination.total = this.projects.length;
                })
                .catch(errors => {
                    console.log(errors);
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
    download() {
            const doc = new jsPDF();
            const contentHtml = this.$refs.content.innerHTML;
            doc.fromHTML(contentHtml, 15, 15, {
                width: 170
            });
            doc.save("sample.pdf");
        },
    downloadWithCSS() {
        var datas = JSON.parse(this.exportPDF);
        var datas = JSON.stringify(datas);
        const doc = new jsPDF()
        var header = function (data) {
            doc.text("Laporan Terlambat & PLA", data.settings.margin.left, 10);
        };

        autoTable(doc, {didDrawPage : header, html: '#my-table'});
        // autoTable(doc, { html: '#my-table' })
        doc.save('laporan-terlambat-dan-pla.pdf')
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
<style>
@import url("https://cdn.syncfusion.com/ej2/material.css");
</style>
