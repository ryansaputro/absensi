<template>
    <div class="row">
      <div class="top">
        <p class="now text-right">{{date}} {{now}}</p>
      </div>
      <div class="col-md-4 mt-2">
        <h3 class="text-center" style="margin-top:10px;" v-if="now >= jam_absen_masuk">Absen Keluar</h3>
        <h3 class="text-center" style="margin-top:10px;" v-else="jam >= jam_absen_masuk">Absen Masuk</h3>
        <div style="overflow-y:scroll;height:500px;padding-top:5px;">
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
              <tr v-if="jam >= jam_absen_masuk" v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai">
                  <td class="nik">{{project.nik_pegawai }}</td>
                  <td>{{project.nama_lengkap}}</td>
                  <td v-if="now >= jam_absen_masuk">{{project.keluar}}</td>
                  <td v-else>{{project.masuk}}</td>
                  <td v-if="now >= jam_absen_masuk">{{project.keluar >= '17:00' ? 'pulang' : 'pulang awal'}}</td>
                  <td v-else>{{project.masuk > '08:00' ? 'terlambat' : 'tepat'}}</td>
              </tr>
              <tr v-if="now >= jam_absen_masuk" v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai">
                  <td class="nik">{{project.nik_pegawai }}</td>
                  <td>{{project.nama_lengkap}}</td>
                  <td v-if="now >= jam_absen_masuk">{{project.keluar}}</td>
                  <td v-else>{{project.masuk}}</td>
                  <td v-if="now >= jam_absen_masuk">{{project.keluar >= '17:00' ? 'pulang' : 'pulang awal'}}</td>
                  <td v-else>{{project.keluar > '08:00' ? 'terlambat' : 'tepat'}}</td>
              </tr>
              <tr v-if="projects.length <= 0">
                  <td colspan="5" class="text-center nodata">Data tidak tersedia</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-8 mt-2">
        <h3 class="text-center" style="margin-top:10px;">Daftar Karyawan</h3>
          <div class="row mr-3"  style="overflow-y:scroll;height:500px;">
            <div v-for="(karyawan, index) in karyawans" :key="karyawan.id" class="col-md-2" style="background-image:url(http://placehold.it/150x150&text=);">
              <div class="detail belum-hadir" v-bind:id="karyawan.nik_pegawai">
                <span class="name">{{karyawan.nama_lengkap.substr(0, 15)}}</span>
                <span class="division">{{karyawan.bagian_divisi.substr(0, 20)}}</span>
                <span class="time">-</span>
              </div>
            </div>
          </div>
      </div>
    </div>
</template>
<style>
  .display {
    height: unset;
  }
  .col-md-2 {
    flex: 0 0 13.80% !important;
    max-width: 16.07% !important;
    margin: 2px;
    background-size: contain;
    background-repeat: no-repeat;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
  }
  .login-content {
    background: #fff;
  }
  .tgl {
    position: absolute;
    top: 50px;
    font-size: 75px;
  }
  .now {
    color:#fff;
    padding-right:20px;
    /* position: absolute;
    font-size: 16px;
    right: 120px;
    top: 135px; */
  }
  .top {
    background-color:green;
    width:100%;
    height:30px;
  }

  .image-absensi {
    padding:5px;
  }

  .name {
    padding-left: 10px;
    padding-right: 10px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    display: block;
  }

  .division {
    font-size: 12px;
    display: block;
    text-align: center;
  }

  .time {
    font-size: 10px;
    display: block;
    text-align: center;
  }

  .detail {
    width: 143px;
    left: 0px;
    bottom: 0px;
    margin-top: 115px;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    /* padding: 5px; */
    margin-left: -15px;
  }

.belum-hadir {background-color:#e5e5e5; text-transform: capitalize;}
.sakit {background-color:yellow; color:#000;text-transform: capitalize;}
.hadir {background-color:green; color:#fff;text-transform: capitalize;}
.ijin {background-color:blue; color:#fff;text-transform: capitalize;}
.alpa {background-color:red; color:#fff;text-transform: capitalize;}
.cuti {background-color:orangered; color:#fff;text-transform: capitalize;}

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
        jam_absen_masuk:'12:00:00',
        date: moment(new Date()).format('ddd, DD - MMM - YYYY'),
        projects:[],
        status_absen:[],
        load: false,
        karyawans: [],
        jam: moment(new Date()).format('kk:mm'),
        
      }
      // jam_absen_masuk:'12:00:00'
    },
    components: {
      Clock
    }, 
    methods: {
      getProjects(a) {
        var status_absens = typeof(a) !== 'undefined' ? a : this.now < '12:00:00' ? 'masuk' : 'keluar';
            axios.get('cek-absen', {params: {"status_absen" : status_absens}})
                .then(response => {
                    // $('#kehadiran tbody tr').remove();
                    this.projects = response.data.record;
                    this.karyawans = response.data.karyawan;
                    this.status_absen = response.data.status_absen;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },

    }, 
    created() {
        this.getProjects();
        var status = '';
        var channel = window.Echo.channel('my-channel');
        channel.listen('.my-event', function(data) {

          $('table tr:last td.nodata').remove();
          var nik = [];
          $.each($('table tr td.nik'), function(k,v){
              nik.push($(v).text());
          })
          
          var dataCek = $.inArray(data.message.nik, nik) !== -1;
          // console.log(nik)
          this.jam = moment(new Date()).format('kk:mm::ss');

          //absen masuk
          if(this.jam <= '12:00:00'){
            //cek jika karyawan sudah diinsert
            if(dataCek === false){
                status = data.message.status
                //append into table
                var datas = "<tr id='"+data.message.nik+"'>";
                datas += "<td class='nik'>"+data.message.nik+"</td>"
                datas += "<td>"+data.message.nama_lengkap+"</td>"
                datas += "<td>"+data.message.jam+"</td>"
                datas += "<td>"+data.message.status+"</td>"
                datas += "</tr>";
                $('#kehadiran').append(datas);
                $('table tr:last')[0].scrollIntoView();
                console.log(status)
                console.log("baca masuk");
              }

          }else{
            if(jQuery.inArray(data.message.nik, nik) !== -1){
              // if(this.status_absen == 'keluar'){
                console.log("ada")
                $('tr#'+data.message.nik).remove();
                // var status = data.message.status;
                var status = data.message.jam < '17:00' ? 'pulang awal' : 'pulang';
                var datas = "<tr id='"+data.message.nik+"'>";
                datas += "<td class='nik'>"+data.message.nik+"</td>"
                datas += "<td>"+data.message.nama_lengkap+"</td>"
                datas += "<td>"+data.message.jam+"</td>"
                datas += "<td>"+status+"</td>"
                datas += "</tr>";
                $('#kehadiran').append(datas);
                $('table tr:last')[0].scrollIntoView();

              // }

            }else{
              console.log("tidak ada")
              //append into table
              // var status = data.message.status;
              var status = data.message.jam < '17:00' ? 'pulang awal' : 'pulang';
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

          console.log(jQuery.inArray(data.message.nik, nik) !== -1);
          //give sign in grid model
          $('#'+data.message.nik+'.detail').removeClass('belum-hadir');
          $('#'+data.message.nik+'.detail').addClass('hadir');
          $('#'+data.message.nik+'.detail').find('.time').html(data.message.jam+' - '+status);

        });
    },
    mounted: function () {
        this.interval = setInterval(function () {
            var now = moment(new Date()).format('kk:mm:ss');
            this.now = now;
            if(now == '12:00:00'){
              var status = "keluar";
              // $('div.detail').removeClass('hadir');
              // $('div.detail').addClass('belum-hadir');
              $('div.detail').removeClass('hadir');
              $('div.detail').addClass('belum-hadir');
              $('div.detail').find('.time').html('-');
              $('#kehadiran tbody tr').remove();
              console.log("ae")
              this.getProjects(status);
            }else{
              var status = "masuk";
            }
        }.bind(this), 1000);

        // this.intervalRefresh = setInterval(function () {
        //     var now = moment(new Date()).format('kk:mm:ss');
        //     this.now = now;
        //     if(now > '12:00:00'){
        //       var status = "keluar";
        //     }else if(now == '12:00:00'){
        //       var status = "keluar";
        //       $('#kehadiran tbody tr').remove();
              // $('div.detail').removeClass('hadir');
              // $('div.detail').addClass('belum-hadir');
              // $('div.detail').find('.time').html('-');
        //       this.getProjects(status);
        //     }else if(now < '12:00:00'){
        //       var status = "masuk";
        //       // $('#kehadiran tbody tr').remove();
        //       // this.getProjects(status);
        //     }
            
        // }.bind(this), 36000);
        //36000
    },
    updated: function () {
    //give color to karyawan after rendering
    $.each(this.projects, function(k,v){
      var now = moment(new Date()).format('kk:mm:ss');
      if(now == '12:00:00'){
        var status = jam > '17:00' ? 'pulang' : 'pulang awal';
        var jam = v.keluar;
        $('div.detail').removeClass('hadir');
        $('div.detail').addClass('belum-hadir');
        $('div.detail').find('.time').html('-');
        $('#kehadiran tbody').remove();
      }else if(now > '12:00:00'){
        var jam = v.keluar;
        var status = jam > '17:00' ? 'pulang' : 'pulang awal';
        $('#'+v.nik_pegawai+'.detail').removeClass('belum-hadir');
        $('#'+v.nik_pegawai+'.detail').addClass('hadir');
        $('#'+v.nik_pegawai+'.detail').find('.time').html(jam+' - '+status);

      }else{
        var jam = v.masuk;
        var status = jam <= '08:00' ? 'tepat' : 'terlambat';
        $('#'+v.nik_pegawai+'.detail').removeClass('belum-hadir');
        $('#'+v.nik_pegawai+'.detail').addClass('hadir');
        $('#'+v.nik_pegawai+'.detail').find('.time').html(jam+' - '+status);
      }
        
    });

    $.each(this.status_absen, function(k,v){
      var ket = '';
      if(v == 'I'){ ket = 'ijin'; }else if(v == 'S'){ ket = 'sakit'; }else if(v == 'A'){ ket = 'alpa'; }else{ ket = 'cuti';}
      $('#'+k+'.detail').removeClass('belum-hadir');
      $('#'+k+'.detail').addClass(ket);
      $('#'+k+'.detail').find('.time').html(ket);
    });

  }
  }
</script>