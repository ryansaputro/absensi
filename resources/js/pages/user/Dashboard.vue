<template>
    <div class="container">
      <div class="jam pull-right" style="display:absolute;position: absolute;z-index: 9;right: 50px;top: 10px;">
          <clock size="200px"></clock>
      </div>
      <div class="mb-3">
        <h1 class="tgl">{{date}}</h1>
      </div>
      <h3 class="text-center" v-if="jam >= '12:00'">Absen Keluar</h3>
      <h3 class="text-center" v-else="jam >= '12:00'">Absen Masuk</h3>
      <div style="overflow-y:scroll;height:300px;position: absolute;width: 98%;left: 20px;right: 0px;">
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
                <td v-if="jam >= '12:00'">{{project.keluar}}</td>
                <td v-else>{{project.masuk}}</td>
                <td v-if="jam >= '12:00'">{{project.keluar >= '17:00' ? '-' : 'Pulang Awal'}}</td>
                <td v-else>{{project.masuk >= '08:00' ? 'Terlambat' : 'Tepat'}}</td>
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
      getProjects() {
        var status_absen = this.jam >= '12:00' ? 'keluar' : 'masuk';
            axios.get('cek-absen', {params: {"status_absen" : status_absen}})
                .then(response => {
                    this.projects = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },

    }, 
    created() {
        var channel = window.Echo.channel('my-channel');
        channel.listen('.my-event', function(data) {
          $('table tr:last td.nodata').remove();
          if(this.jam <= '12:00'){
            //cek jika karyawan sudah diinsert
            if($('table tr:last td.nik').text() !== data.message.nik){
              //append into table
              var datas = "<tr id='"+data.message.nik+"'>";
              datas += "<td class='nik'>"+data.message.nik+"</td>"
              datas += "<td>"+data.message.nama+"</td>"
              datas += "<td>"+data.message.jam+"</td>"
              datas += "<td>"+data.message.status+"</td>"
              datas += "</tr>";
              $('#kehadiran').append(datas);
              $('table tr:last')[0].scrollIntoView();
            }
          }else{
            if($('table tr:last td.nik').text() !== data.message.nik){
              //append into table
              var status = data.message.status == 'terlambat' ? 'pulang awal' : '-';
              var datas = "<tr id='"+data.message.nik+"'>";
              datas += "<td class='nik'>"+data.message.nik+"</td>"
              datas += "<td>"+data.message.nama+"</td>"
              datas += "<td>"+data.message.jam+"</td>"
              datas += "<td>"+status+"</td>"
              datas += "</tr>";
              $('#kehadiran').append(datas);
              $('table tr:last')[0].scrollIntoView();
            }else{
              $('tr#'+data.message.nik).remove();
              $('tr#'+data.message.nik).remove();
              $('tr#'+data.message.nik).remove();
              console.log('tr#'+data.message.nik);
              var status = data.message.status == 'terlambat' ? 'pulang awal' : '-';
              var datas = "<tr id='"+data.message.nik+"'>";
              datas += "<td class='nik'>"+data.message.nik+"</td>"
              datas += "<td>"+data.message.nama+"</td>"
              datas += "<td>"+data.message.jam+"</td>"
              datas += "<td>"+status+"</td>"
              datas += "</tr>";
              $('#kehadiran').append(datas);
              $('table tr:last')[0].scrollIntoView();
            }
          }

        });
    },
    mounted: function () {
        this.getProjects();

        // this.interval = setInterval(function () {
        //     this.getProjects();
        //     $('table tr:last')[0].scrollIntoView();
        //     // $('table tr').removeAttr('style');
        //     // $('table tr:last').css("background-color", "yellow"); 
        //     // document.getElementById("a").scrollIntoView();
        // }.bind(this), 4000);
        //36000
    },
    updated: function () {
    //scroll down logic here
    // this.getProjects();
  }
  }
</script>