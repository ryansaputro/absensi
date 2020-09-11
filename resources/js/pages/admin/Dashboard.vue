<template>
  <div class="content">
    <div class="container">
      <div class="user-data m-b-30 p-3">
        <div class="my-5">
          <h3 class="text-uppercase text-center">telat datang</h3>
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
      <div class="user-data m-b-30 p-3">
        <div class="my-5">
          <h3 class="text-uppercase text-center">Grafik Keterlambatan Per Divisi</h3>
          <!-- <form v-on:submit.prevent="getProjects">
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
</template>
<script>
  import axios from 'axios'
  import LineChart from '../../components/Chart'
  export default {
    data() {
      return {
        chart: null,
        city: '',
        dates: [],
        name: [],
        divisi: [],
        jumlah: [],
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
          //telat
          this.dates = response.data.data.map(data => {
            return (data.selisih_jam);
          });

          this.name = response.data.data.map(data => {
            return data.nama_lengkap;
          });

          // // telat per divisi
          this.divisi = response.data.data2.map(data => {
            return (data.nama_divisi);
          });

          this.jumlah = response.data.data2.map(data => {
            return data.jumlah;
          });
          var TelatDatangChart = document.getElementById('TelatDatangChart');
          var cty = document.getElementById('PulangAwalChart');
          var ChartTelatPerDivisi = document.getElementById('ChartTelatPerDivisi');
          
          //telat
          this.chart = new Chart(TelatDatangChart,{
            type: 'bar',
            data: {
              labels: this.name,
              datasets: [
                {
                  label: 'Terlambat ',
                  backgroundColor: 'rgba(42, 42, 46, 1)',
                  borderColor: 'rgb(54, 162, 235)',
                  // fill: true,
                  data: this.dates
                }
              ]
            },
            options: {
              tooltips: {
                callbacks: {
                  label: function(tooltipItem, data) {
                    var label = data.datasets[tooltipItem.datasetIndex].label || '';
                    
                    if (label) {
                      label += ': ';
                    }
                    var $tooltips = tooltipItem.yLabel;
                    var dt = tooltipItem.yLabel.toString().split('.');
                    var jam = dt[0] != 0 ? parseInt(dt[0]) +' Jam ': '';
                    var menit = typeof dt[1] !== 'undefined' ? dt[1] != 0 ? dt[1].length == 1 ? dt[1]+ '0 Menit' : dt[1]+ ' Menit'  : '' : '';
                    var labeldata = jam + menit
                    label +=labeldata
                    
                    return label;
                  }
                }
              },
              legend: {
                  display: true,
                  labels: {
                      fontColor: 'rgb(255, 99, 132)'
                  }
              },
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
              labels: this.divisi,
              datasets: [
                {
                  label: 'Terlambat ',
                  backgroundColor: 'rgba(42, 42, 46, 1)',
                  borderColor: 'rgb(54, 162, 235)',
                  // fill: true,
                  data: this.jumlah
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