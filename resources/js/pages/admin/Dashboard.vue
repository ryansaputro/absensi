<template>
  <div class="content">
    <div class="container">
      <div class="user-data m-b-30 p-3" v-if="$can('dashboard-total-karyawan')">
        <div class="my-5">
          <h5 class="text-uppercase text-center">Total Karyawan</h5>
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
    <div class="row" v-if="$can('dashboard-kehadiran')">
      <div class="col-md-6">
            <div class="user-data m-b-30 p-3">
              <div class="my-5">
                <h5 class="text-uppercase text-center">Kehadiran Bandung</h5>
              </div>
              <div class="my-5">
                <!-- <div class="alert alert-info" v-show="loading">
                  Loading...
                </div> -->
                <div v-show="chart != null">
                  <canvas id="KehadiranBandung"></canvas>
                </div>
              </div>
          </div>
      </div>
      <div class="col-md-6">
            <div class="user-data m-b-30 p-3">
              <div class="my-5">
                <h5 class="text-uppercase text-center">Kehadiran Surabaya</h5>
              </div>
              <div class="my-5">
                <!-- <div class="alert alert-info" v-show="loading">
                  Loading...
                </div> -->
                <div v-show="chart != null">
                  <canvas id="KehadiranSurabaya"></canvas>
                </div>
              </div>
          </div>
      </div>
    </div>
    <div class="row" v-if="$can('dashboard-keterlambatan')">
      <div class="col-md-12">
        <div class="user-data m-b-30 p-3">
          <div class="my-5">
            <h5 class="text-uppercase text-center">Grafik keterlambat 30 Hr Terakhir (Bandung)</h5>
          </div>
          <div class="my-5">
            <div class="alert alert-info" v-show="loading">
              Loading...
            </div>
            <div v-show="chart != null">
              <canvas id="terlambat7hrBdg"></canvas>
            </div>
          </div>
      </div>

      </div>
      <div class="col-md-12">
        <div class="user-data m-b-30 p-3">
          <div class="my-5">
            <h5 class="text-uppercase text-center">Grafik keterlambat 30 Hr Terakhir (Surabaya)</h5>
          </div>
          <div class="my-5">
            <div class="alert alert-info" v-show="loading">
              Loading...
            </div>
            <div v-show="chart != null">
              <canvas id="terlambat7hrSby"></canvas>
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
        terlambat: [],
        value:[],
        label:[],
        karyawan:[],
        tepat:[],
        telat:[],
        karyawanSby:[],
        tepatSby:[],
        telatSby:[],
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

          var nli = [];
          $.each(response.data.data4, function(k, v){
            nli[k] = v;
          });

          this.kehadiran = nli;

          var terlambat7 = [];
          $.each(response.data.data5, function(k, v){
            terlambat7[k] = v;
            
          });

          var terlambat7hr = [];
          var label = [];
          $.each(response.data.data6, function(k, v){
            terlambat7hr[k] = v;
            label.push(k);

          });
            this.label = label;
          // this.terlambat = terlambat7;
          var karyawan = [];
          var tepat = [];
          var telat = [];
          var karyawanSby = [];
          var tepatSby = [];
          var telatSby = [];
          $.each(label, function(k,v){
            karyawan.push(terlambat7hr[v]['bandung']['jml_karyawan']);
            tepat.push(terlambat7hr[v]['bandung']['tepat']);
            telat.push(terlambat7hr[v]['bandung']['telat']);
            
            karyawanSby.push(terlambat7hr[v]['surabaya']['jml_karyawan']);
            tepatSby.push(terlambat7hr[v]['surabaya']['tepat']);
            telatSby.push(terlambat7hr[v]['surabaya']['telat']);
          })

            this.karyawan = karyawan;
            this.tepat = tepat;
            this.telat = telat;
            console.log(this.telat)

            this.karyawanSby = karyawanSby;
            this.tepatSby = tepatSby;
            this.telatSby = telatSby;

          var TelatDatangChart = document.getElementById('TelatDatangChart');
          var cty = document.getElementById('PulangAwalChart');
          var KehadiranBandung = document.getElementById('KehadiranBandung');
          var KehadiranSurabaya = document.getElementById('KehadiranSurabaya');
          var terlambat7hr = document.getElementById('terlambat7hrBdg');
          var terlambat7hrSby = document.getElementById('terlambat7hrSby');

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
                // scales: {
                //     xAxes: [{
                //         stacked: true,
                //     }],
                //     yAxes: [{
                //         stacked: true,
                        
                //     }]
                // }, 
                // rotation: -Math.PI,
                cutoutPercentage: 50,
                // circumference: Math.PI,
                // legend: {
                //   position: 'center'
                // },
                animation: {
                  animateRotate: true,
                  animateScale: true
                },
                
                // scales: {
                //   yAxes: [{
                //       ticks: {
                //           min: 0,
                //           stepSize: 1
                //       }
                //   }]
                // },

                // legend: {
                //   display: false
                // // },
                // // tooltips: {
                // //     callbacks: {
                // //       label: function(tooltipItem) {
                // //               return tooltipItem.yLabel;
                // //       }
                // //     }
                // // }
            }
          });

          var hadir = typeof(this.kehadiran['bandung']['kehadiran']) !== 'undefined' ? this.kehadiran['bandung']['kehadiran'] : 0;
          var sakit = typeof(this.kehadiran['bandung']['S']) !== 'undefined' ? this.kehadiran['bandung']['S'] : 0;
          var ijin = typeof(this.kehadiran['bandung']['I']) !== 'undefined' ? this.kehadiran['bandung']['I'] : 0;
          var alpa = typeof(this.kehadiran['bandung']['A']) !== 'undefined' ? this.kehadiran['bandung']['A'] : 0;
          var cuti = typeof(this.kehadiran['bandung']['C']) !== 'undefined' ? this.kehadiran['bandung']['C'] : 0;
          var lk = typeof(this.kehadiran['bandung']['LK']) !== 'undefined' ? this.kehadiran['bandung']['LK'] : 0;
          
          this.chart = new Chart(KehadiranBandung,{
            type: 'bar',
            data: {
              labels: ['kehadiran', 'sakit', 'ijin', 'alpha', 'cuti', 'luar kota'],
              datasets: [
                {
                  label: 'Data Kehadiran',
                  backgroundColor: ['rgb(0 123 255)', 'rgb(63 81 181)', 'rgb(220 53 69)', 'rgb(239 255 1)', 'rgb(214 96 27)'],
                  borderColor: 'rgb(54, 162, 235)',
                  hoverBackgroundColor: 'rgb(2 171 2 / 91%)',
                  hoverBorderColor:"white",
                  fill: true,
                  data: [hadir, sakit, ijin, alpa, cuti, lk]
                }
              ]
            },
            options: {
              legend: {
                  display: false
              },
              tooltips: {
                  callbacks: {
                    label: function(tooltipItem) {
                            return tooltipItem.yLabel + " Orang";
                    }
                  }
              },
              scales: {
                  yAxes: [{
                      ticks: {
                          min: 0,
                          stepSize: 1
                      },
                      scaleLabel: {
                        display: true,
                        labelString: 'orang'
                      }
                  }]
              },
            }
          });

          var hadir = typeof(this.kehadiran['surabaya']['kehadiran']) !== 'undefined' ? this.kehadiran['surabaya']['kehadiran'] : 0;
          var sakit = typeof(this.kehadiran['surabaya']['S']) !== 'undefined' ? this.kehadiran['surabaya']['S'] : 0;
          var ijin = typeof(this.kehadiran['surabaya']['I']) !== 'undefined' ? this.kehadiran['surabaya']['I'] : 0;
          var alpa = typeof(this.kehadiran['surabaya']['A']) !== 'undefined' ? this.kehadiran['surabaya']['A'] : 0;
          var cuti = typeof(this.kehadiran['surabaya']['C']) !== 'undefined' ? this.kehadiran['surabaya']['C'] : 0;
          
          this.chart = new Chart(KehadiranSurabaya,{
            type: 'bar',
            data: {
              labels: ['kehadiran', 'sakit', 'ijin', 'alpha', 'cuti'],
              datasets: [
                {
                  backgroundColor: ['rgb(0 123 255)', 'rgb(63 81 181)', 'rgb(220 53 69)', 'rgb(239 255 1)', 'rgb(214 96 27)'],
                  borderColor: 'rgb(54, 162, 235)',
                  hoverBackgroundColor: 'rgb(2 171 2 / 91%)',
                  hoverBorderColor:"white",
                  fill: true,
                  data: [hadir, sakit, ijin, alpa, cuti]
                }
              ]
            },
            options: {
              legend: {
                  display: false
              },
              tooltips: {
                  callbacks: {
                    label: function(tooltipItem) {
                            return tooltipItem.yLabel + " Orang";
                    }
                  }
              },
              scales: {
                  yAxes: [{
                      ticks: {
                          min: 0,
                          stepSize: 1
                      },
                      scaleLabel: {
                        display: true,
                        labelString: 'orang'
                      }
                  }]
              },
            }
          });


          this.chart = new Chart(terlambat7hr,{
            type: 'bar',
            data: {
              labels: this.label,
              datasets: [
                {
                  // backgroundColor: ['red', 'red', 'red', 'red', 'red', 'red', 'red' , 'red'],
                  borderColor: 'red',
                  borderWidth:3,
                  lineTension:0.1,
                  label: 'Terlambat',
                  data: this.telat,
                },

              ]
            },
            options: {
              // legend: {
              //     display: false
              // },
              tooltips: {
                  callbacks: {
                    label: function(tooltipItem) {
                            return tooltipItem.yLabel + " Orang";
                    }
                  }
              },
              scales: {
                  yAxes: [{
                      ticks: {
                          min: 0,
                          stepSize: 1
                      },
                      scaleLabel: {
                        display: true,
                        labelString: 'orang'
                      }
                  }]
              },
            }
          });

          this.chart = new Chart(terlambat7hrSby,{
            type: 'bar',
            data: {
              labels: this.label,
              datasets: [
                {
                  // backgroundColor: ['red', 'red', 'red', 'red', 'red', 'red', 'red' , 'red'],
                  borderColor: 'red',
                  borderWidth:3,
                  lineTension:0.1,
                  label: 'Terlambat',
                  data: this.telatSby,
                },

              ]
            },
            options: {
              // legend: {
              //     display: false
              // },
              // tooltips: {
              //     callbacks: {
              //       label: function(tooltipItem) {
              //               return tooltipItem.yLabel;
              //       }
              //     }
              // },
              scales: {
                  yAxes: [{
                      ticks: {
                          min: 0,
                          stepSize: 1
                      }, 
                      scaleLabel: {
                        display: true,
                        labelString: 'orang'
                      }
                  }]
              }
            }
          });

          
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