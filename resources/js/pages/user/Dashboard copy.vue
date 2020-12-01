<template>
    <div class="row">
      <div class="top" style="height:85px;">
        <img src="https://nuansa.com/2019/wp-content/uploads/2020/09/cropped-logo-whhite-01.png" style="width:15%;position:absolute;padding-top:8px;padding-left:20px;"> 
        <h1 style="color:#fff;float:left;padding:15px 15px 15px 37%;">Display Absensi RFID </h1>
        <p class="now text-right" style="font-size:25px;padding-top:23px;">{{date}} {{now}}</p>
      </div>
      <div class="col-md-4 mt-2">
        <h3 class="text-center" style="margin-top:10px;" v-if="now >= pergantian_jam">Absen Keluar</h3>
        <h3 class="text-center" style="margin-top:10px;" v-else>Absen Masuk</h3>
        <div style="overflow-y:hidden;padding-top:5px;">
          <table class="table table-striped" id="kehadiran">
            <!-- <thead style="display: block;">
              <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jam</th>
                <th>Status</th>
              </tr>
            </thead> -->
            <thead style="display: block;"><tr>
              <th style="width: 80px;">NIK</th> 
              <th style="width: 200px;">Nama</th> 
              <th style="width: 50px;">Jam</th> 
              <th style="width: 90px;">Status</th>
              </tr>
            </thead>
            <tbody style="display: block;height: 483px;overflow: auto;width: 100%;">
              <!-- <tr v-if="jam >= pergantian_jam" v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai">
                  <td class="nik">{{project.nik_pegawai }}</td>
                  <td>{{project.nama_lengkap}}</td>
                  <td v-if="now >= pergantian_jam">{{project.keluar}}</td>
                  <td v-else>{{project.masuk}}</td>
                  <td v-if="now >= pergantian_jam">{{project.keluar >= '17:00' ? 'pulang' : 'pulang awal'}}</td>
                  <td v-else>{{project.masuk > '08:00' ? 'terlambat' : 'tepat'}}</td>
              </tr> -->
              <tr v-if="now < pergantian_jam" v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai">
                  <td class="nik">{{project.nik_pegawai }}</td>
                  <td>{{project.nama_lengkap}}</td>
                  <td v-if="now >= pergantian_jam">{{project.keluar}}</td>
                  <td v-else>{{project.masuk}}</td>
                  <td v-if="now >= pergantian_jam">{{project.keluar >= '17:00' ? 'pulang' : 'pulang awal'}}</td>
                  <td v-else>{{project.masuk > '08:00' ? 'terlambat' : 'tepat'}}</td>
              </tr>
              <tr v-if="now > pergantian_jam" v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai">
                  <td class="nik">{{project.nik_pegawai }}</td>
                  <td>{{project.nama_lengkap}}</td>
                  <td v-if="now >= pergantian_jam">{{project.keluar}}</td>
                  <td v-else>{{project.masuk}}</td>
                  <td v-if="now >= pergantian_jam">{{project.keluar >= '17:00' ? 'pulang' : 'pulang awal'}}</td>
                  <td v-else>{{project.masuk > '08:00' ? 'terlambat' : 'tepat'}}</td>
              </tr>
              <tr v-if="projects.length <= 0">
                  <td colspan="5" style="width:420px;" class="text-center nodata">Data tidak tersedia</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-8 mt-2">
        <h3 class="text-center" style="margin-top:10px;">Daftar Karyawan</h3>
          <div class="row mr-3"  style="overflow-y:scroll;height:515px;">
            <div class="row" style="border:1px solid;border-radius:5px; margin: 2px;flex: 0 0 16%;display: flex;flex-wrap: wrap;" v-bind:id="karyawan.nik_pegawai+'S'" v-for="(karyawan, index) in karyawans" :key="karyawan.id">
              <div class="col-md-12" v-bind:style="{ 'background-image': 'url(/images/karyawan/' + karyawan.foto + '?'+jam+'), url(/images/unnamed.png?'+jam+')' }" style="height:150px;background-size:cover;border-top-right-radius: 5px;border-top-left-radius: 5px;"></div>
              <div class="detail belum-hadir col-md-12" style="border-bottom-left-radius:5px;border-bottom-right-radius:5px;" v-bind:class="karyawan.id_epc_tag" v-bind:id="karyawan.nik_pegawai">
                <span class="name">{{karyawan.nama_lengkap.substr(0, 15)}}</span>
                <span class="division">{{karyawan.bagian_divisi.substr(0, 20)}}</span>
                <span class="time">-</span>
              </div>
            </div>
          </div>
      </div>
      <!-- <div class="col-md-12">
        <table class="table" style="background: white;z-index: 999;font-size: 12px;position: fixed;bottom: -20px;box-shadow: 0 -4px 5px -2px #969896;">
            <tr>
              <th style="color:green;">Hadir</th>
              <th>:</th>
              <th style="color:green;" class="jml_hadir">2/62</th>
              <th>|</th>
              <th style="color:#bfbf06;">Sakit</th>
              <th>:</th>
              <th style="color:#bfbf06;" class="jml_sakit">2</th>
              <th>|</th>
              <th style="color:blue;">Ijin</th>
              <th>:</th>
              <th style="color:blue;" class="jml_ijin">2</th>
              <th>|</th>
              <th style="color:red;">Alpa</th>
              <th>:</th>
              <th style="color:red;" class="jml_alpa">2</th>
              <th>|</th>
              <th style="color:orangered;">Cuti</th>
              <th>:</th>
              <th style="color:orangered;" class="jml_cuti">2</th>
              <th>|</th>
              <th style="color:rgb(19, 206, 196);">Luar Kota</th>
              <th>:</th>
              <th style="color:rgb(19, 206, 196);" class="jml_luar_kota">2</th>
            </tr>

        </table>
      </div> -->
      <div class="keterangan" style="width:100%;">
          <table class="table" style="text-align:center; left:0px; background: white;z-index: 999;font-size: 12px;position: fixed;bottom: -20px;box-shadow: 0 -4px 5px -2px #969896;">
            <tr>
              <th style="color:green;">Hadir</th>
              <th style="color:#bfbf06;">Sakit</th>
              <th style="color:blue;">Ijin</th>
              <th style="color:red;">Alpa</th>
              <th style="color:orangered;">Cuti</th>
              <th style="color:rgb(19, 206, 196);">Luar Kota</th>
            </tr>
            <tr>
              <th style="color:green;" class="jml_hadir">2/62</th>
              <th style="color:#bfbf06;" class="jml_sakit">2</th>
              <th style="color:blue;" class="jml_ijin">2</th>
              <th style="color:red;" class="jml_alpa">2</th>
              <th style="color:orangered;" class="jml_cuti">2</th>
              <th style="color:rgb(19, 206, 196);" class="jml_luar_kota">2</th>
            </tr>
          </table>
      </div>
    <button v-on:click="sendMsg()">Kirim</button>
    </div>
</template>
<style>
  tbody tr td:nth-child(1) {
      width: 80px;
  }
  tbody tr td:nth-child(2) {
      width: 200px;
  }
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
    padding-top:23px;
    /* position: absolute;
    font-size: 16px;
    right: 120px;
    top: 135px; */
  }
  .top {
    background-color:green;
    width:100%;
    height:85px;
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
    /* width: 145px;
    left: 0px;
    bottom: 0px;
    margin-top: 115px;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px; */
    /* padding: 5px; */
    /* margin-left: -15px; */
  }

  .footer{
    display:none !important;
  }

.belum-hadir {background-color:#e5e5e5; text-transform: capitalize;}
.sakit {background-color:yellow; color:#000;text-transform: capitalize;}
.hadir {background-color:green; color:#fff;text-transform: capitalize;}
.ijin {background-color:blue; color:#fff;text-transform: capitalize;}
.alpa {background-color:red; color:#fff;text-transform: capitalize;}
.cuti {background-color:orangered; color:#fff;text-transform: capitalize;}
.lk {background-color:rgb(19, 206, 196); color:#fff;text-transform: capitalize;}

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
        pergantian_jam:'',
        update_jam:'',
        jml_hadir:''
      }
      // jam_absen_masuk:'12:00:00'
    },
    components: {
      Clock
    }, 
    methods: {
      getProjects(a) {
            axios.get('cek-absen')
                .then(response => {
                    // $('#kehadiran tbody tr').remove();
                    this.projects = response.data.record;
                    this.karyawans = response.data.karyawan;
                    this.status_absen = response.data.status_absen;
                    this.pergantian_jam = response.data.jam.pergantian_jam;
                    this.toleransi_jam_masuk = response.data.jam.toleransi_jam_masuk;
                    this.toleransi_jam_keluar = response.data.jam.toleransi_jam_keluar;
                    this.jam_masuk = response.data.jam.jam_masuk;
                    this.jam_keluar = response.data.jam.jam_keluar;
                    this.jml_hadir = response.data.hadir;
                    var now = moment(new Date()).format('kk:mm:ss');
                    var jam = "";
                    var status = "";
                    $.each(this.projects, function(k,v){
                      if(v.keluar > response.data.jam.pergantian_jam){
                        jam = v.keluar;
                        status = v.keluar < response.data.jam.jam_masuk ? 'pulang awal' : 'pulang';
                      }else{
                        jam = v.masuk
                        status = v.masuk > response.data.jam.jam_keluar ? 'terlambat' : 'tepat';
                      }
                      $('#'+v.nik_pegawai+'.detail').find('.time').html(jam+' - '+status);
                      console.log("masuk sis")
                      console.log(response.data.jam.pergantian_jam)
                    });

                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        

    }, 
    created() {
        this.getProjects();
    },
    mounted: function () {
        var pergantian = "";
        var now = "";
        var toleransi_jam_masuk = "";
        var toleransi_jam_keluar = "";
        var jam_masuk = "";
        var jam_keluar = "";
        this.getProjects();
        this.interval = setInterval(function () {
            
            now = moment(new Date()).format('kk:mm:ss');
            pergantian = this.pergantian_jam.toString();
            toleransi_jam_masuk = this.toleransi_jam_masuk.toString();
            toleransi_jam_keluar = this.toleransi_jam_keluar.toString();
            jam_masuk = this.jam_masuk.toString();
            jam_keluar = this.jam_keluar.toString();

            this.now = now;
            var jml_hadir = this.jml_hadir.toString();
            var hdr = $('.hadir').length;
            var krywn = $('.detail').length;
            $('.jml_hadir').html(jml_hadir+'/'+krywn)
            if(now == pergantian){
              var status = "keluar";
              $('div.detail').removeClass('hadir');
              $('div.detail').addClass('belum-hadir');
              $('div.detail').find('.time').html('-');
              $('#kehadiran tbody tr').remove();
              this.getProjects(status);
            // }else if(now == '00:00:00'){
            }else{
              var status = "masuk";
            }

        }.bind(this), 1000);

        //websocket from python
        // Create WebSocket connection.
        const socket = new WebSocket('ws://localhost:5000');
        var modalSwal = this;
        // Listen for messages
        socket.addEventListener('message', function (event, $swal, modalSwal) {
            var datas = event.data;
            var datasArr = datas.split(',');
            var id = $('.'+datasArr[0]).attr('id');
            console.log(datasArr);

            if(now <= toleransi_jam_masuk){
                var status = "telat";
            }else if(now < jam_masuk){
                var status = "tepat";
            }else if(now > toleransi_jam_masuk && now < toleransi_jam_keluar){
                var status = "lagi keluar";
            }else if(now >= toleransi_jam_keluar && now < jam_keluar){
                var status = "pulang awal";
            }else{
                var status = "pulang";
            }

            //give sign masuk in grid mode 
            $('.'+datasArr[0]).removeClass('belum-hadir');
            $('.'+datasArr[0]).addClass('hadir');
            $('.'+datasArr[0]).find('.time').html(datasArr[2]+' - '+status);
            
            $('#'+id+'S')[0].scrollIntoView(true); 
            
        });

    },
    updated: function () {

    var luarkt =  $('.lk').length
    var ijn = $('.ijin').length;
    var ct = $('.cuti').length;
    var alp = $('.alpa').length;
    var skt = $('.sakit').length;
    var hdr = $('.hadir').length;
    var krywn = $('.detail').length;

    $('.jml_sakit').html(skt)
    $('.jml_ijin').html(ijn)
    $('.jml_alpa').html(alp)
    $('.jml_cuti').html(ct)
    $('.jml_luar_kota').html(luarkt)

    var pergantian = this.pergantian_jam.toString();
    // var jml_hadir = this.jml_hadir.toString();
    var now = moment(new Date()).format('kk:mm:ss');

    //websocket 
    var status = '';
        var channel = window.Echo.channel('my-channel');
        // channel.listen('.my-event', function(data) {
          
        //   $('table tr:last td.nodata').remove();
        //   var nik = [];
        //   $.each($('table tr td.nik'), function(k,v){
        //       nik.push($(v).text());
        //   });

        //   this.update_jam = data.message.jam;
          
        //   var dataCek = $.inArray(data.message.nik, nik) !== -1;
        //   this.jam = moment(new Date()).format('kk:mm::ss');

        //   //absen masuk
        //   if(this.jam < pergantian){

        //       //cek jika karyawan sudah diinsert
        //       if(dataCek === false){

        //         // status = data.message.status
        //         if(data.message.jam > pergantian){
        //           var status = data.message.jam < '17:00' ? 'pulang awal' : 'pulang';
        //         }else{
        //           var status = data.message.status;
        //         }
        //         //append into table
        //         var datas = "<tr id='"+data.message.nik+"'>";
        //         datas += "<td class='nik'>"+data.message.nik+"</td>"
        //         datas += "<td>"+data.message.nama_lengkap+"</td>"
        //         datas += "<td>"+data.message.jam+"</td>"
        //         datas += "<td>"+status+"</td>"
        //         datas += "</tr>";
        //         $('#kehadiran').append(datas);
        //         $('table tr:last')[0].scrollIntoView();

        //         //give sign in grid model
        //         $('#'+data.message.nik+'.detail').removeClass('belum-hadir');
        //         $('#'+data.message.nik+'.detail').addClass('hadir');
        //         $('#'+data.message.nik+'.detail').find('.time').html(data.message.jam+' - '+status);
        //         $('#'+data.message.nik+'S')[0].scrollIntoView(true);   

        //       }

        //   }else{
        //     if(jQuery.inArray(data.message.nik, nik) !== -1){

        //         $('tr#'+data.message.nik).remove();
        //         if(data.message.jam > pergantian){
        //           var status = data.message.jam < '17:00' ? 'pulang awal' : 'pulang';
        //         }else{
        //           var status = data.message.status;
        //         }
                
        //         var datas = "<tr id='"+data.message.nik+"'>";
        //         datas += "<td class='nik'>"+data.message.nik+"</td>"
        //         datas += "<td>"+data.message.nama_lengkap+"</td>"
        //         datas += "<td>"+data.message.jam+"</td>"
        //         datas += "<td>"+status+"</td>"
        //         datas += "</tr>";
        //         $('#kehadiran').append(datas);
        //         $('table tr:last')[0].scrollIntoView();

        //     }else{
        //       if(data.message.jam > pergantian){
        //         var status = data.message.jam < '17:00' ? 'pulang awal' : 'pulang';
        //       }else{
        //         var status = data.message.status;
        //       }
              
        //       var datas = "<tr id='"+data.message.nik+"'>";
        //       datas += "<td class='nik'>"+data.message.nik+"</td>"
        //       datas += "<td>"+data.message.nama_lengkap+"</td>"
        //       datas += "<td>"+data.message.jam+"</td>"
        //       datas += "<td>"+status+"</td>"
        //       datas += "</tr>";
        //       $('#kehadiran').append(datas);
        //       $('table tr:last')[0].scrollIntoView();

        //     }
        //       //give sign in grid model
        //       $('#'+data.message.nik+'.detail').removeClass('belum-hadir');
        //       $('#'+data.message.nik+'.detail').addClass('hadir');
        //       $('#'+data.message.nik+'.detail').find('.time').html(data.message.jam+' - '+status);
        //       $('#'+data.message.nik+'S')[0].scrollIntoView(true);
        //   }
            
        // });
    //endwebsocket 
    // looping for karyawan
    $.each(this.projects, function(k,v){
      
      if(now == pergantian){
        var status = jam > '17:00' ? 'pulang' : 'pulang awal';
        var jam = v.keluar;
        $('div.detail').removeClass('hadir');
        $('div.detail').addClass('belum-hadir');
        $('div.detail').find('.time').html('-');
        $('#kehadiran tbody').remove();

      }else if(now > pergantian){
        var jam = v.keluar;
        var status = jam > '17:00' ? 'pulang' : 'pulang awal';
        //jika pulang eksekusi ini 
        $('#'+v.nik_pegawai+'.detail').removeClass('belum-hadir');
        $('#'+v.nik_pegawai+'.detail').addClass('hadir');

      }else{
        var jam = v.masuk;
        var status = jam <= '08:00' ? 'tepat' : 'terlambat';
        $('#'+v.nik_pegawai+'.detail').removeClass('belum-hadir');
        $('#'+v.nik_pegawai+'.detail').addClass('hadir');
        $('.jml_hadir').html(hdr+'/'+krywn)
      }
        
    });

    $.each(this.status_absen, function(k,v){
      var ket = '';
      if(v == 'I'){ ket = 'ijin'; }else if(v == 'S'){ ket = 'sakit'; }else if(v == 'A'){ ket = 'alpa'; }else if(v == 'LK'){ ket = 'lk'; }else{ ket = 'cuti';}
      $('#'+k+'.detail').removeClass('belum-hadir');
      $('#'+k+'.detail').addClass(ket);
      $('#'+k+'.detail').find('.time').html(ket);
    });

  }
  }
</script>