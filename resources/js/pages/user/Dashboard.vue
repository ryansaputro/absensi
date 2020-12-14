<template>
    <div class="row dashboard-user">
      <div class="top" style="height:85px;">
        <img src="https://nuansa.com/2019/wp-content/uploads/2020/09/cropped-logo-whhite-01.png" style="width:15%;position:absolute;padding-top:8px;padding-left:20px;"> 
        <h1 style="color:#fff;float:left;padding:15px 15px 15px 37%;">Display Absensi RFID </h1>
        <p class="now text-right" style="font-size:25px;padding-top:23px;">{{date}} {{now}}</p>
      </div>
      <div class="col-md-4 mt-2">
        <h3 class="text-center" style="margin-top:10px;" v-if="now >= pergantian_jam">Absen Keluar</h3>
        <h3 class="text-center" style="margin-top:10px;" v-else>Absen Masuk</h3>
        <div style="overflow-y:hidden;padding:15px;">
          <div class="row">
            <div class="header">
              <div class="col-md-3 head-datas">NIK</div>
              <div class="col-md-4 head-datas">NAMA</div>
              <div class="col-md-2 head-datas">JAM</div>
              <div class="col-md-3 head-datas">STATUS</div>
            </div>
            <div class="data-body">
              <div v-for="(project, index) in projects" :key="project.id" v-bind:id="project.nik_pegawai" class="data-row hadir" v-bind:class="project.id_epc_tag">
                <div class="col-md-3 datas niks">{{project.nik_pegawai }}</div>
                <div class="col-md-4 datas names">{{project.nama_lengkap}}</div>
                <div class="col-md-2 datas times" v-if="now >= pergantian_jam">{{project.keluar}}</div>
                <div class="col-md-2 datas times" v-else>{{project.masuk}}</div>
                <div class="col-md-2 datas statuses" v-if="now >= pergantian_jam">
                    <div v-if="project.keluar > toleransi_jam_masuk && project.keluar < toleransi_jam_keluar">sdg keluar</div>
                    <div v-else-if="project.keluar >= toleransi_jam_keluar && project.keluar < jam_keluar">pulang awal</div>
                    <div v-else>pulang</div>  
                </div>
                <div class="col-md-2 datas statuses" v-else>
                    <div v-if="project.masuk <= toleransi_jam_masuk && project.masuk > jam_masuk">telat</div>
                    <div v-else-if="project.masuk < jam_masuk">tepat</div>
                </div>
                
              </div>
            </div>
          </div>
          <div class="row">
                  <table class="table table-ket">
                    <tr>
                      <th style="color:green;">Hadir</th>
                      <th style="color:#bfbf06;">Sakit</th>
                      <th style="color:blue;">Ijin</th>
                      <th style="color:red;">Alpa</th>
                      <th style="color:orangered;">Cuti</th>
                      <th style="color:rgb(19, 206, 196);">Luar Kota</th>
                    </tr>
                    <tr>
                      <th style="color:green;" class="jml_hadir">0/62</th>
                      <th style="color:#bfbf06;" class="jml_sakit">0</th>
                      <th style="color:blue;" class="jml_ijin">0</th>
                      <th style="color:red;" class="jml_alpa">0</th>
                      <th style="color:orangered;" class="jml_cuti">0</th>
                      <th style="color:rgb(19, 206, 196);" class="jml_luar_kota">0</th>
                    </tr>
                  </table>
          </div>
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

  body {
    background-color: #e5e5e5 !important;
    height:max-content !important;
  }

.belum-hadir {background-color:#e5e5e5; text-transform: capitalize;}
.sakit {background-color:yellow; color:#000;text-transform: capitalize;}
.hadir {background-color:green; color:#fff;text-transform: capitalize;}
.ijin {background-color:blue; color:#fff;text-transform: capitalize;}
.alpa {background-color:red; color:#fff;text-transform: capitalize;}
.cuti {background-color:orangered; color:#fff;text-transform: capitalize;}
.lk {background-color:rgb(19, 206, 196); color:#fff;text-transform: capitalize;}

</style>
<!-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
<script>
// import Clock from 'vue-clock2';
// import Echo from 'laravel-echo';

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
        jml_hadir:'',
        toleransi_jam_masuk:'',
        toleransi_jam_keluar:'',
        jam_masuk:'',
        jam_keluar:'',
      }
      // jam_absen_masuk:'12:00:00'
    },
    components: {
    }, 
    methods: {
      async getProjects(a) {
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
                        // status = v.keluar < response.data.jam.jam_masuk ? 'pulang awal' : 'pulang';
                      }else{
                        jam = v.masuk;
                        // status = v.masuk > response.data.jam.jam_keluar ? 'terlambat' : 'tepat';
                      }
                      var status = $('#'+v.nik_pegawai).find('.statuses').text();
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
            var mulai_masuk = "09:35:00";
            console.log("Now :"+now)
            console.log("Pergantian :"+pergantian)
            console.log("jam_masuk :"+jam_masuk)
            console.log("toleransi_jam_masuk :"+toleransi_jam_masuk)
            if(now == pergantian){
              var status = "keluar";
              $('div.row').find('div.detail').removeClass('hadir');
              $('div.row').find('div.detail').addClass('belum-hadir');
              $('div.row').find('div.detail').find('.time').html('-');
              $('.data-body').find('div').remove();
              this.getProjects(status);
              localStorage['changeToAbsen'] = 'keluar';
              console.log($('div.detail').removeClass('hadir'));
              console.log("hapus");
            }else if(now == mulai_masuk){
                var status = "masuk";
                $('div.detail').removeClass('hadir');
                $('div.detail').addClass('belum-hadir');
                $('div.detail').find('.time').html('-');
                $('.data-body').find('div').remove();
                this.getProjects(status);
                localStorage['changeToAbsen'] = 'masuk';
            }else{
              // localStorage.removeItem('changeToAbsen');
              var status = "masuk";
            }

            //kalo layar display mati
            if(now >= pergantian && localStorage['changeToAbsen'] == 'masuk' && now < mulai_masuk){
                var status = "keluar";
                $('div.detail').removeClass('hadir');
                $('div.detail').addClass('belum-hadir');
                $('div.detail').find('.time').html('-');
                $('.data-body').find('div').remove();
                this.getProjects(status);
                localStorage['changeToAbsen'] = 'keluar';
            }else if(now >= mulai_masuk && localStorage['changeToAbsen'] == 'keluar' && now <pergantian){
                var status = "masuk";
                $('div.detail').removeClass('hadir');
                $('div.detail').addClass('belum-hadir');
                $('div.detail').find('.time').html('-');
                $('.data-body').find('div').remove();
                this.getProjects(status);
                localStorage['changeToAbsen'] = 'masuk';
            }
            
            // !localStorage.getItem('changeToAbsen')
            console.log(localStorage.getItem('changeToAbsen'))
        }.bind(this), 1000);

        //websocket from python
        // Create WebSocket connection.
        // const socket = new WebSocket('ws://192.168.0.157:5000');
        const socket = new WebSocket('ws://127.0.0.1:5000');
        var modalSwal = this;
        // Listen for messages
        socket.addEventListener('message', function (event, $swal, modalSwal) {
            var datas = event.data;
            var datasArr = datas.split(',');
            var id = $('.'+datasArr[0]).attr('id');
            console.log(datasArr);

            if(now <= toleransi_jam_masuk  && now > jam_masuk){
                var status = "telat";
            }else if(now < jam_masuk){
                var status = "tepat";
            }else if(now > toleransi_jam_masuk && now < toleransi_jam_keluar){
                var status = "sdg keluar";
            }else if(now >= toleransi_jam_keluar && now < jam_keluar){
                var status = "pulang awal";
            }else{
                var status = "pulang";
            }
            console.log("jam_masuk : "+jam_masuk);
            console.log("toleransi_jam_masuk : "+toleransi_jam_masuk);
            console.log("jam_keluar : "+jam_keluar);
            console.log("toleransi_jam_keluar : "+toleransi_jam_keluar);
            console.log("status : "+status);

            //cek if exist on list
            var cekData = $('.data-row').hasClass(datasArr[0]);

              // jam pada absen keluar akan mengupdate last time
              if(status == "sdg keluar"){
                //grid update time
                $('.'+datasArr[0]).find('.time').html(datasArr[2]+' - '+status); 

              }else if(status == "pulang awal" || status == "pulang"){
                //remove old data 
                $('.data-body').find('.'+datasArr[0]).remove();

                // add new data
                var nik = $('.'+datasArr[0]).attr('id');
                var nama = $('.'+datasArr[0]).find('.name').text();
                var datasrow = '<div id="'+nik+'" class="data-row '+datasArr[0]+'">';                
                    datasrow += '<div class="col-md-3 datas niks">'+id+'</div>';                
                    datasrow += '<div class="col-md-4 datas names">'+nama+'</div>';                
                    datasrow += '<div class="col-md-2 datas times">'+datasArr[2]+'</div>';                
                    datasrow += '<div class="col-md-3 datas statuses">'+status+'</div>';                
                    datasrow += '</div>';
                
                    $('.data-body').append(datasrow);
                    $('.'+datasArr[0]).find('.time').html(datasArr[2]+' - '+status);               
                    $('#'+nik)[0].scrollIntoView(true);

              }else if(status == 'telat' || status == 'tepat'){
                var nik = $('.'+datasArr[0]).attr('id');
                var nama = $('.'+datasArr[0]).find('.name').text();
                var datasrow = '<div id="'+nik+'" class="data-row '+datasArr[0]+'">';                
                    datasrow += '<div class="col-md-3 datas niks">'+id+'</div>';                
                    datasrow += '<div class="col-md-4 datas names">'+nama+'</div>';                
                    datasrow += '<div class="col-md-2 datas times">'+datasArr[2]+'</div>';                
                    datasrow += '<div class="col-md-3 datas statuses">'+status+'</div>';                
                    datasrow += '</div>';

                //check if data not exists 
                if(cekData == false){
                  $('.data-body').append(datasrow);
                  $('.'+datasArr[0]).find('.time').html(datasArr[2]+' - '+status);               
                }
                
                  $('#'+nik)[0].scrollIntoView(true);
              }

            //give sign masuk in grid mode 
            $('.'+datasArr[0]).removeClass('belum-hadir');
            $('.'+datasArr[0]).addClass('hadir');
            $('#'+id+'S')[0].scrollIntoView(true);
            
        });


    },
    updated: function () {
      //jika di refresh
        $.each(this.projects, function(k,v){
          $('.'+v.id_epc_tag).addClass('hadir');
          $('.'+v.id_epc_tag).removeClass('belum-hadir');
        });

        //update keterangan
          var luarkt =  $('.lk').length
          var ijn = $('.ijin').length;
          var ct = $('.cuti').length;
          var alp = $('.alpa').length;
          var skt = $('.sakit').length;
          var hdr = $('.detail.hadir').length;
          var krywn = $('.detail').length;

          $('.jml_sakit').html(skt);
          $('.jml_ijin').html(ijn);
          $('.jml_alpa').html(alp);
          $('.jml_cuti').html(ct);
          $('.jml_luar_kota').html(luarkt);
          $('.jml_hadir').html(hdr+'/'+krywn); 
        
        //show data kehadiran
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