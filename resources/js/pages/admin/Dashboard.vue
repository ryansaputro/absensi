<template>
  <div class="content">
    <div class="container">
      <div class="user-data m-b-30 p-3">
        <div class="my-5">
          <h3 class="text-uppercase text-center">Jumlah Karyawan</h3>
          <!-- <form v-on:submit.prevent="getData">
            <div class="row">
              <div class="col-md-6 offset-md-3">
                <h5>Enter A City:</h5>
                <div class="input-group">
                  <input type="text" class="form-control" v-model="city" />
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Submit</button>
                  </div>
                </div>
              </div>
            </div>
          </form> -->
        </div>
        <div class="my-5">
          <div class="alert alert-info" v-show="loading">
            Loading...
          </div>
          <div v-show="chart != null">
            <canvas id="TelatDatangChart"></canvas>
          </div>
        </div>
    </div>
      <!-- <div class="user-data m-b-30 p-3">
        <div class="my-5">
          <h3 class="text-uppercase text-center">mobilisasi personnel</h3> -->
          <!-- <form v-on:submit.prevent="getData">
            <div class="row">
              <div class="col-md-6 offset-md-3">
                <h5>Enter A City:</h5>
                <div class="input-group">
                  <input type="text" class="form-control" v-model="city" />
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Submit</button>
                  </div>
                </div>
              </div>
            </div>
          </form> -->
        <!-- </div>
        <div class="my-5">
          <div class="alert alert-info" v-show="loading">
            Loading...
          </div>
          <div v-show="chart != null">
            <canvas id="PulangAwalChart"></canvas>
          </div>
        </div>
    </div> -->
    <div class="row">
      <div class="col-md-6">
            <div class="user-data m-b-30 p-3">
              <div class="my-5">
                <h3 class="text-uppercase text-center">Kehadiran Bandung</h3>
              </div>
              <div class="my-5">
                <!-- <div class="alert alert-info" v-show="loading">
                  Loading...
                </div> -->
                <div v-show="chart != null">
                  <canvas id="ChartTelatPerDivisi"></canvas>
                </div>
              </div>
          </div>
      </div>
      <div class="col-md-6">
            <div class="user-data m-b-30 p-3">
              <div class="my-5">
                <h3 class="text-uppercase text-center">Kehadiran Surabaya</h3>
              </div>
              <div class="my-5">
                <!-- <div class="alert alert-info" v-show="loading">
                  Loading...
                </div> -->
                <div v-show="chart != null">
                  <canvas id="ChartTelatPerDivisi"></canvas>
                </div>
              </div>
          </div>
      </div>
    </div>
  </div>
  </div>
</template>
<script>
  import axios from 'axios'
  import LineChart from '../../components/Chart'
  export default {
    data() {
      return {
        chart: null,
        city: '',
        kantor: [],
        jumlah_karyawan: [],
        jumlah_jenis: [],
        kehadiran: [],
        value:[],
        loading: false,
        errored: false
      }},
    created() {
        this.getProjects();
    },
    methods: {
    getProjects: function() {
      
      // this.loading = true;

      // if (this.chart != null) {
      //   this.chart.destroy();
      // }
      axios
        .get('telat').then(response => {
          
          //jumlah karyawan
          this.kantor = response.data.data.map(data => {
            return (data.kantor);
          });

          this.jumlah_karyawan = response.data.data.map(data => {
            return data.jumlah;
          });

          // // telat per divisi
          this.jumlah_jenis = response.data.data3.map(data => {
            return (data.jml);
          });

          var nil = [];
          $.each(response.data.data4, function(k, v){
              nil.push(v);
          });
          this.kehadiran = nil;
          console.log(nil);
          // this.kehadiran = response.data.data4.map(data => {
          //   return data.kehadiran;
          // });
          //   // values = (this.kehadiran);
          //   console.log(this.kehadiran)
            // console.log(response.data.data4)

          var TelatDatangChart = document.getElementById('TelatDatangChart');
          var cty = document.getElementById('PulangAwalChart');
          var ChartTelatPerDivisi = document.getElementById('ChartTelatPerDivisi');

         var coloR = [];

          var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgb(" + r + "," + g + "," + b + ")";
          };

        


          for (var i in this.name) {
            coloR.push(dynamicColors());
          }
          
          //telat
          this.chart = new Chart(TelatDatangChart,{
            type: 'doughnut',
            data: {
              labels: this.kantor,
              datasets: [
                {
                  label: 'Jumlah ',
                  backgroundColor: ['rgb(0 128 0)', 'rgb(176 187 92)'],
                  borderColor: 'rgb(255 255 255)',
                  // fill: true,
                  data: this.jumlah_karyawan,
                  hoverBackgroundColor: 'rgb(2 171 2 / 91%)',
                  hoverBorderColor:"white"
                }
              ]
            },
            options: {
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true,
                        
                    }]
                }
            }
          });

          //telat perdivisi
          this.chart = new Chart(ChartTelatPerDivisi,{
            type: 'bar',
            data: {
              labels: ['kehadiran', 'sakit', 'ijin', 'alpha', 'cuti'],
              datasets: [
                {
                  // label: ['kehadiran', 'sakit', 'ijin', 'alpha', 'cuti'],
                  backgroundColor: ['rgb(0 123 255)', 'rgb(63 81 181)', 'rgb(220 53 69)', 'rgb(239 255 1)', 'rgb(214 96 27)'],
                  borderColor: 'rgb(54, 162, 235)',
                  hoverBackgroundColor: 'rgb(2 171 2 / 91%)',
                  hoverBorderColor:"white",
                  // fill: true,
                  data: this.kehadiran
                }
              ]
            },
            options: {
              // tooltips: {
              //   callbacks: {
              //     label: function(tooltipItem, data) {
              //       var label = data.datasets[tooltipItem.datasetIndex].label || '';
                    
              //       if (label) {
              //         label += ': ';
              //       }
              //       var $tooltips = tooltipItem.yLabel;
              //       var dt = tooltipItem.yLabel.toString().split('.');
              //       console.log(dt);
              //       var jam = dt[0] != 0 ? parseInt(dt[0]) +' Jam ': '';
              //       var menit = typeof dt[1] !== 'undefined' ? dt[1] != 0 ? dt[1].length == 1 ? dt[1]+ '0 Menit' : dt[1]+ ' Menit'  : '' : '';
              //       var labeldata = jam + menit
              //       label +=labeldata
                    
              //       return label;
              //     }
              //   }
              // },
              // legend: {
              //     display: true,
              //     labels: {
              //         fontColor: 'rgb(255, 99, 132)'
              //     }
              // },
              //   scales: {
              //       xAxes: [{
              //           stacked: true,
              //       }],
              //       yAxes: [{
              //           stacked: true,
                        
              //       }]
              //   }
            }
          });

          //mobilisasi personnel
          // this.chart = new Chart(cty,{
          //   type: 'pie',
          //   data: {
          //     labels: this.name,
          //     datasets: [
          //       {
          //         label: 'Terlambat ',
          //         // backgroundColor: 'rgba(42, 42, 46, 1)',
          //         // borderColor: 'rgb(54, 162, 235)',
          //         // fill: true,
          //         data: this.dates
          //       }
          //     ]
          //   },
          //   options: {
          //     tooltips: {
          //       callbacks: {
          //         label: function(tooltipItem, data) {
          //           var label = data.datasets[tooltipItem.datasetIndex].label || '';
                    
          //           if (label) {
          //             label += ': ';
          //           }
          //           var $tooltips = tooltipItem.yLabel;
          //           var dt = tooltipItem.yLabel.toString().split('.');
          //           var jam = dt[0] != 0 ? parseInt(dt[0]) +' Jam': '';
          //           var menit = typeof dt[1] !== 'undefined' ? dt[1] != 0 ? parseInt(dt[1])+ ' Menit' : '' : '';
          //           var labeldata = jam + menit
          //           label +=labeldata
                    
          //           return label;
          //         }
          //       }
          //     },
          //     legend: {
          //         display: true,
          //         labels: {
          //             fontColor: 'rgb(255, 99, 132)'
          //         }
          //     },
          //       scales: {
          //           xAxes: [{
          //               stacked: true,
          //           }],
          //           yAxes: [{
          //               stacked: true,
                        
          //           }]
          //       }
          //   }
          // });
          
        })
        .catch(error => {
          console.log(error);
          this.errored = true;
        })
        .finally(() => (this.loading = false));

    }
  }
  }
</script>