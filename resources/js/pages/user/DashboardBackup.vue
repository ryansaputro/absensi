<template>
    <div class="container">
      <div class="jam pull-right" style="display:absolute;position: absolute;z-index: 9;right: 50px;top: 10px;">
          <clock size="200px"></clock>
      </div>
      <div class="mb-3">
        <h1 class="tgl">{{date}}</h1>
        <h1 class="now">{{now}}</h1>
      </div>
      <h3 class="text-center" style="margin-top:210px;" v-if="now >= jam_absen_masuk">Absen Keluar</h3>
      <h3 class="text-center" style="margin-top:210px;" v-else="jam >= jam_absen_masuk">Absen Masuk</h3>
      <div style="overflow-y:scroll;height:100px;position: absolute;width: 98%;left: 20px;right: 0px;top:255px;">
        <table class="table table-stripped" id="kehadiran">
          <thead>
            <tr>
              <!-- <th>Foto</th> -->
              <th>NIK</th>
              <th>Nama</th>
              <th>Jam</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai">
                <td class="nik">{{project.nik_pegawai }}</td>
                <td>{{project.nama_lengkap}}</td>
                <td v-if="now >= jam_absen_masuk">{{project.keluar}}</td>
                <td v-else>{{project.masuk}}</td>
                <td v-if="now >= jam_absen_masuk">{{project.keluar >= '17:00' ? '-' : 'pulang awal'}}</td>
                <td v-else>{{project.masuk > '08:00' ? 'terlambat' : 'tepat'}}</td>
            </tr>
            <tr v-if="projects.length <= 0">
                <td colspan="5" class="text-center nodata">Data tidak tersedia</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="text-center mb-2 mt-2" style="position:absolute;position: absolute;bottom: 10px;left: 45%;">
        <img src="/images/ncirfid.png">
      </div>
    </div>
</template>
<style>
  .login-content {
    background: #fff;
  }
  .tgl {
    position: absolute;
    top: 50px;
    font-size: 75px;
  }
  .display {
    height: 60vh;
  }

  .now {
    position: absolute;
    font-size: 16px;
    right: 120px;
    top: 135px;
  }
</style>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
import Clock from 'vue-clock2';
import Echo from 'laravel-echo';

  export default {
    data() {
      return {
        time: '08:00',
        status_absen:'',
        now:moment(new Date()).format('kk:mm:ss'),
        jam_absen_masuk:'16:29:30',
        date: moment(new Date()).format('ddd, DD - MMM - YYYY'),
        projects:[],
        load: false,
        commits: [],
        jam: moment(new Date()).format('kk:mm'),
        
      }
    },
    components: {
      Clock
    }, 
    methods: {
      getProjects(a) {
        console.log(this.now < this.jam_absen_masuk)
        console.log(this.now)
        console.log(this.jam_absen_masuk)
        var status_absen = typeof(a) !== 'undefined' ? a : this.now < this.jam_absen_masuk ? 'masuk' : 'keluar';
            axios.get('cek-absen', {params: {"status_absen" : status_absen}})
                .then(response => {
                    this.projects = response.data.absen;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },

    }, 
    created() {
       this.getProjects();
        var channel = window.Echo.channel('my-channel');
        channel.listen('.my-event', function(data) {
          $('table tr:last td.nodata').remove();
          var nik = [];
          $.each($('table tr td.nik'), function(k,v){
              nik.push($(v).text());
          })
          
          var dataCek = $.inArray(data.message.nik, nik) !== -1;
          // console.log(nik)
          if(this.jam <= this.jam_absen_masuk){
            //cek jika karyawan sudah diinsert
            if(dataCek === false){
                //append into table
                var datas = "<tr id='"+data.message.nik+"'>";
                datas += "<td class='nik'>"+data.message.nik+"</td>"
                datas += "<td>"+data.message.nama_lengkap+"</td>"
                datas += "<td>"+data.message.jam+"</td>"
                datas += "<td>"+data.message.status+"</td>"
                datas += "</tr>";
                $('#kehadiran').append(datas);
                $('table tr:last')[0].scrollIntoView();
              }

          }else{
            if(dataCek === false){
              //append into table
              // var status = data.message.status;
              var status = data.message.status == 'terlambat' ? 'pulang awal' : '-';
              var datas = "<tr id='"+data.message.nik+"'>";
              datas += "<td class='nik'>"+data.message.nik+"</td>"
              datas += "<td>"+data.message.nama_lengkap+"</td>"
              datas += "<td>"+data.message.jam+"</td>"
              datas += "<td>"+status+"</td>"
              datas += "</tr>";
              $('#kehadiran').append(datas);
              $('table tr:last')[0].scrollIntoView();
            }else{

              if(this.status_absen == 'keluar'){
                $('tr#'+data.message.nik).remove();
                // var status = data.message.status;
                var status = data.message.status == 'terlambat' ? 'pulang awal' : '-';
                var datas = "<tr id='"+data.message.nik+"'>";
                datas += "<td class='nik'>"+data.message.nik+"</td>"
                datas += "<td>"+data.message.nama_lengkap+"</td>"
                datas += "<td>"+data.message.jam+"</td>"
                datas += "<td>"+status+"</td>"
                datas += "</tr>";
                $('#kehadiran').append(datas);
                $('table tr:last')[0].scrollIntoView();

              }
            }
          }

        });
    },
    mounted: function () {
        this.interval = setInterval(function () {
            var now = moment(new Date()).format('kk:mm:ss');
            this.now = now;
            if(now == this.jam_absen_masuk){
              var status = "keluar";
              this.getProjects(status);
            }else{
              var status = "masuk";
            }
        }.bind(this), 1000);
        //36000
    },
    updated: function () {
    //scroll down logic here
    // this.getProjects();
  }
  }
</script>