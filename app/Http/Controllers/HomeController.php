<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-09-28 10:08:44
 * @modify date 2020-09-28 10:08:44
 * @desc menghandle request di dashboard
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use App\User;
use App\Absensi;
use DB;
use Auth;
use JWTAuth;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:absensi-list|absensi-create|absensi-edit|absensi-delete', ['only' => ['index','show']]);
        $this->middleware('permission:absensi-create', ['only' => ['create','store']]);
        $this->middleware('permission:absensi-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:absensi-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     /**
     * Dashboard telat
     *
     */
    public function telat(Request $request){
        // $kantor = ['bandung', 'surabaya'];
        // $data = User::select(DB::raw('COUNT(kantor) AS jumlah'), 'kantor')->groupBy('kantor')->where('id', '<>', '5')->get();//->pluck('jumlah', 'kantor')->toArray();
        
        //ambil data kantor yg aktif di tabel cabang
        $kantor = DB::table('cabang')->select('nama_cabang')->where('status', '=', '1')->pluck('nama_cabang')->toArray();
        
        //ambil jumlah karyawan di semua kantor group by cabang 
        $data = DB::table('users')
                ->select(DB::raw('COUNT(users_status_pegawai.id_cabang) AS jumlah'), 'nama_cabang AS kantor')
                ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'users.id')
                ->join('cabang', 'users_status_pegawai.id_cabang', '=', 'cabang.id')
                ->where('users.id', '<>', '5')
                ->groupBy('users_status_pegawai.id_cabang')
                ->get();
        
        // $grafikPerdivisi = DB::table('absensi')
        //         ->select(DB::raw('COUNT(DISTINCT(absensi.id_karyawan)) AS jumlah'), 'nama_cabang AS kantor')
        //         ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'absensi.id_karyawan')
        //         ->join('cabang', 'users_status_pegawai.id_cabang', '=', 'cabang.id')
        //         ->groupBy('kantor')
        //         ->get();
        
        //ambil data Ijin Sakit Alpa Cuti dr tabel absen_tambahan
        $isac = DB::table('absen_tambahan')
                ->select(DB::raw('COUNT(absen_tambahan.status) AS jml'), 'absen_tambahan.status', 'nama_cabang AS kantor')
                ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'absen_tambahan.id_karyawan')
                ->join('cabang', 'users_status_pegawai.id_cabang', '=', 'cabang.id')
                // ->where(DB::raw('DATE(tanggal)'), '2020-09-08')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                ->groupBy('status', 'kantor')
                ->get();
        
        //ambil data karyawan hadir di kantor group by per kantor dr table view_absensi
        $absensi = DB::table('view_absensi')
                ->select(DB::raw('COUNT(tanggal) AS kehadiran'), 'nama_cabang AS kantor')
                ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'view_absensi.id_karyawan')
                ->join('cabang', 'users_status_pegawai.id_cabang', '=', 'cabang.id')
                // ->where('tanggal', '2020-09-21')
                ->where('tanggal', date('Y-m-d'))
                ->groupBy('users_status_pegawai.id_cabang')
                ->pluck('kehadiran', 'kantor')
                ->toArray();
        
        //ambil data karyawan yg terlambat datang ke kantor selama 30 hari terakhir
        $telatKaryawan = DB::table('view_absensi')
                ->select(DB::raw('COUNT(masuk) AS jumlah_telat'), 'nama_cabang AS kantor', 'tanggal')
                ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'view_absensi.id_karyawan')
                ->join('cabang', 'users_status_pegawai.id_cabang', '=', 'cabang.id')
                ->where('masuk', '>', '08:00')
                ->where('tanggal', '>=', DB::raw('DATE(NOW()) - INTERVAL 30 DAY'))
                ->groupBy('tanggal', 'users_status_pegawai.id_cabang')
                ->get();
    
        //ambil data karyawan yg tepat waktu datang ke kantor
        $tepatKaryawan = DB::table('view_absensi')
                ->select(DB::raw('COUNT(masuk) AS jumlah_tepat'), 'nama_cabang AS kantor', 'tanggal')
                ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'view_absensi.id_karyawan')
                ->join('cabang', 'users_status_pegawai.id_cabang', '=', 'cabang.id')
                ->where('masuk', '<=', '08:00')
                ->where('tanggal', '>=', DB::raw('DATE(NOW()) - INTERVAL 30 DAY'))
                ->groupBy('tanggal', 'kantor')
                ->get();
        
        //data kantor di generate ke array yg didefinisikan
        $userArr = $data->pluck('jumlah', 'kantor')->toArray();
        
        //membuat daftar tanggal ke dlm array dr data 30 hr
        $date = date('Y-m-d', strtotime('-30 days'));
        $end_date = date('Y-m-d');
        $dates = [];
        while (strtotime($date) <= strtotime($end_date)) {
                    $dates[] = $date;
                    $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        //membuat array jumlah karyawan yg terlambat berdasarkan tanggal dan kantor 
        $telat = [];
        foreach($telatKaryawan AS $k => $v){
            $telat[$v->tanggal][$v->kantor] = $v->jumlah_telat;
        }

        //membuat array jumlah karyawan yg tepat berdasarkan tanggal dan kantor 
        $tepat = [];
        foreach($tepatKaryawan AS $k => $v){
            $tepat[$v->tanggal][$v->kantor] = $v->jumlah_tepat;
        }

        //membuat list tanggal dan isi dr jumlah karyawan yg terlambat dan tepat
        //list tanggal
        foreach($dates AS $kTgl => $vTgl){
            //list kantor
            foreach($kantor AS $k => $v){
                $terlambat[$vTgl][$v]['jml_karyawan'] = $userArr[$v];
                
                //cek jika tgl tsb ada data telat
                if(!empty($tepat[$vTgl])){
                    //cek jika kantor dan tgl tsb ada telat
                    if(!empty($tepat[$vTgl][$v])){
                        $terlambat[$vTgl][$v]['tepat'] = $tepat[$vTgl][$v];
                    }else{
                        $terlambat[$vTgl][$v]['tepat'] = 0;
                    }
                }else{
                    $terlambat[$vTgl][$v]['tepat'] = 0;
                }

                //cek jika tgl tsb ada data telat
                if(!empty($telat[$vTgl])){
                    //cek jika kantor dan tgl tsb ada telat
                    if(!empty($telat[$vTgl][$v])){
                        $terlambat[$vTgl][$v]['telat'] = $telat[$vTgl][$v];
                    }else{
                        $terlambat[$vTgl][$v]['telat'] = 0;
                    }
                }else{
                    $terlambat[$vTgl][$v]['telat'] = 0;
                }
            }
        }

        //membuat list array berdasarkan kantor dengan jumlah kehadiran karyawan 
        $jenis = [];
        $jmlTelat = [];
        foreach($kantor AS $k => $v){

            //untuk chart kehadiran
            if(array_key_exists($v, $absensi)){
                $jenis[$v]['kehadiran'] = $absensi[$v];
            }else{
                $jenis[$v]['kehadiran'] = 0;
            }

            //untuk chart terlambat
            if(array_key_exists($v, $userArr)){
                $jmlTelat[$v]['jml_karyawan'] = $userArr[$v];
            }else{
                $jmlTelat[$v]['jml_karyawan'] = 0;
            }

            if(array_key_exists($v, $tepatKaryawan)){
                $jmlTelat[$v]['tepat'] = $tepatKaryawan[$v];
            }else{
                $jmlTelat[$v]['tepat'] = 0;
            }

            if(array_key_exists($v, $telatKaryawan)){
                $jmlTelat[$v]['telat'] = $telatKaryawan[$v];
            }else{
                $jmlTelat[$v]['telat'] = 0;
            }
        }

        //membuat array jumlah ijin sakit alpa dan cuti berdasarkan kantor dan jenisnya
        if(count($isac) > 0){
            foreach($isac AS $k => $v){
                $jenis[$v->kantor][$v->status] = $v->jml;
            }
        }

        return ['data' => $data, 'data3' => $isac, 'data4' => $jenis, 'data5' => $jmlTelat, 'data6' => $terlambat];
    }

    public function getAbsen(){
        $data = DB::table('view_absensi')->get();
        $absen = [];
        foreach($data AS $k => $v){
            $absen[$v->nama_lengkap][$v->tanggal] = $v->masuk;
        }
        return response()->json(['data' => $absen]);
    }

    /**
     * Create the application absen.
     *
     */
     /**
     * Proses input karyawan ketika absen
     *
     */
    public function absen(Request $request)
    {
        //validate the data before processing
        $rules = [
            'tag' => 'required|string',
            'date' => 'required|date',
        ];

        $customMessages = [
            'required' => 'The :attribute field can not be blank.'
        ];

        $this->validate($request, $rules, $customMessages);

        DB::beginTransaction();
        try {
            
            //get id user from tag
            $id_user = User::where('id_epc_tag', $request->tag)->first();
            
            //cek jika melebihi waktu 100 detik dari data sebelumnya maka data dapat disimpan 
            $cek = Absensi::select(DB::raw('DATE_ADD(tanggal, INTERVAL 100 SECOND) AS next'))->where('id_gate', $request->id_gate)
                    ->where('id_karyawan', $id_user->id)
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->orderBy('tanggal', 'DESC')
                    ->first();

            //get nama gerbang dari data yg diinput
            $gate = DB::table('gate')->select('nama_gerbang')->where('id', $request->id_gate)->value('nama_gerbang');

            //jika data ada maka proses ini dijalankan
            if($cek !== null){

                //jika data yg terbaru waktunya lebih dari yg diinsert sebelumnya maka proses ini dijalankan
                if($request->date > $cek->next){

                    //proses simpan data di table absensi
                    $create = Absensi::create([
                        'id_gate' => $request->id_gate,
                        'id_karyawan' => $id_user->id,
                        'tanggal' => $request->date,
                    ]);

                    //push to pusher (websocket) 
                    // realtime notifikasi
                    $options = array(
                        'cluster' => 'ap1',
                        'useTLS' => true
                    );
                    $pusher = new Pusher(
                        "3de115e667294f63fe08",
                        "e6b3d2c8618a8560f8b3",
                        "1076418",
                        $options
                    );

                    //memberikan logika utk jam masuk dengan status
                    $status = date("H:i", strtotime($request->date)) > '08:00' ? 'terlambat' : 'tepat';

                    //data yg akan dipush di realtime notifikasi
                    $data['message'] = array("foto" => "image.jpg", "nik" => $id_user->nik_pegawai , "bagian_divisi" => $id_user->bagian_divisi, "nama_lokasi" => $gate, "nama_lengkap" => $id_user->nama_lengkap , "jam" => date("H:i", strtotime($request->date)), "status" => $status);
                    $pusher->trigger('my-channel', 'my-event', $data);

                    //message jika berhasil menambahkan record ke database
                    $message = "absen berhasil";
                }else{
                    // logika jika data yg diinsert kan ke database dengan waktu yg sama maka akan meng-eksekusi message ini
                    $message = "data absensi anda sudah tercatat ke dalam sistem. harap berdiri agak jauh dari gerbang";
                }
            }else{
                //jika data karyawan belum ada di table absensi maka menjalankan proses ini
                $create = Absensi::create([
                        'id_gate' => $request->id_gate,
                        'id_karyawan' => $id_user->id,
                        'tanggal' => $request->date,
                    ]);

                //push to pusher (websocket)
                // realtime notifikasi
                $options = array(
                    'cluster' => 'ap1',
                    'useTLS' => true
                );
                $pusher = new Pusher(
                    "3de115e667294f63fe08",
                    "e6b3d2c8618a8560f8b3",
                    "1076418",
                    $options
                );

                //memberikan logika utk jam masuk dengan status
                $status = date("H:i", strtotime($request->date)) > '08:00' ? 'terlambat' : 'tepat';

                //data yg akan dipush di realtime notifikasi
                $data['message'] = array("foto" => "image.jpg", "nik" => $id_user->nik_pegawai , "bagian_divisi" => $id_user->bagian_divisi, "nama_lokasi" => $gate, "nama_lengkap" => $id_user->nama_lengkap , "jam" => date("H:i", strtotime($request->date)), "status" => $status);
                $pusher->trigger('my-channel', 'my-event', $data);

                //message jika berhasil menambahkan record ke database
                $message = "absen berhasil";
            }


        } catch(\Illuminate\Database\QueryException $ex){ 
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success', 'message' => $message], 200);

    }

    public function lacak(Request $request)
    {
        //jika parameter tanggal diset maka akan menjalankan proses ini
        if ( $request->input('tanggal') ) {

            //proses akan me-return data absensi
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nama_lengkap', 'nik_pegawai', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), $request->tanggal)
                    ->orderBy('tanggal', 'DESC')
                    ->get();
        }
        
        //jika parameter client sama dengan true maka proses ini akan dijalankan
        if ( $request->input('client') ) {

            //proses ini akan menjalankan data absensi
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nama_lengkap', 'nik_pegawai', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->orderBy('tanggal', 'DESC')
                    ->get();
    	}

        //deklarasi variable
        $columns = ['absensi.id', 'tanggal', 'nama_lengkap'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'nik_pegawai', 'nama_lengkap')
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy('tanggal', 'DESC');

        //jika user melakukan pencarian maka perintah ini akan dieksekusi 
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik', 'like', '%' . $searchValue . '%');
            });
        }

        //data akan di bagi per halaman sesuai request user 
        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function pantau(Request $request)
    {
        //jika tanggal diset maka proses ini akan dijalankan 
        if ( $request->input('tanggal') ) {

            //proses akan me-return data absensi 
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nik_pegawai','nama_lengkap', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), $request->tanggal)
                    ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MAX(id)'))
                        ->from('absensi')
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })
                    ->get();
        }

        //jika parameter lantai di set maka akan memproses fungsi ini 
        if(isset($request->lantai)){

            //proses ini akan mengambil data dari absensi
            return DB::table('absensi')
                    ->select('absensi.id',DB::raw('DATE(tanggal) AS tanggal'), DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nik_pegawai','nama_lengkap', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where('gate.id', $request->lantai)
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MAX(id)'))
                        ->from('absensi')
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })
                    ->get();
        }

        //jika parameter client di set maka akan menjalankan proses ini 
        if ( $request->input('client') ) {

            //proses ini akan meng ekseskusi data absensi 
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE(tanggal) AS tanggal'), DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nama_lengkap', 'nik_pegawai','nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MAX(id)'))
                        ->from('absensi')
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })
                    ->get();
    	}

        //pendeklarasian variable 
        $columns = ['absensi.id', 'tanggal', 'nama_lengkap'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        //melakukan get data dari absensi 
        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'nama_lengkap')
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy($columns[$column], $dir);

        //jika user melakukan search maka fungsi ini akan dijalankan
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');
            });
        }

        //data absensi akan di buat page menggunakan perintah pagination 
        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    //fungsi untuk menampilkan list karyawan di display absensi dengan url ..../display 
    public function cekAbsen(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        //jam masuk
        $jam = DB::table('absensi_setting')->first();

        //get data absensi dari table absensi 
        $a = DB::table('view_absensi')
                ->select('view_absensi.*', 'users.id_epc_tag', 'users.nik_pegawai', 'users.nama_lengkap', 'nama_divisi AS bagian_divisi', 'c.nama_gerbang AS gerbang_masuk', 'd.nama_gerbang AS gerbang_keluar', DB::raw("IF(masuk > '08:00', 'telat', 'tepat') AS status"))
                ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->join('absensi AS a', 'a.id', '=', 'view_absensi.id_absen_masuk')
                ->join('absensi AS b', 'b.id', '=', 'view_absensi.id_absen_keluar')
                ->join('gate AS c', 'c.id', '=', 'a.id_gate')
                ->join('gate AS d', 'd.id', '=', 'b.id_gate');

        //logika jika request dari api dengan parameter keluar(jam keluar) maka perintah ini di eksekusi
        if(date("H:i") >= date("H:i", strtotime($jam->pergantian_jam))){
            $hadir = DB::table('view_absensi')->where('tanggal', date('Y-m-d'))->count();
            $a->where('keluar', '!=', '-')
            ->orderBy('view_absensi.keluar', 'ASC')
            ->where('keluar', '>', $jam->pergantian_jam);
        }else{
            $a->orderBy('view_absensi.masuk', 'ASC');
        }

        //data absensi 
        $a =  $a->where('view_absensi.tanggal', date('Y-m-d'))
                ->where('users.id', '<>', '5')
                ->get();

        //get data karyawan
        $karyawan = DB::table('users')
                ->select('nama_lengkap', 'nik_pegawai', 'nama_divisi AS bagian_divisi', 'foto', 'id_epc_tag')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->where('users.id', '<>', 5)
                ->orderBy('nama_lengkap', 'ASC')
                ->get();
        
        //get list absen tambahan seperti ijin sakit alpa dan cuti utk ditampilkan di display absensi
        $status_absen = DB::table('absen_tambahan')
                ->select('users.nik_pegawai', 'status')
                ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), '=', date('Y-m-d'))
                ->pluck('status','nik_pegawai')
                ->toArray();
        

        return ["record" => $a, "karyawan" => $karyawan, 'status_absen' => $status_absen, 'jam' => $jam, 'hadir' => !isset($hadir) ? 0 : $hadir];
    }

    public function listAbsensi(Request $request)
    {
        if ( $request->input('tanggal') ) {
            $a =  DB::table('view_absensi')
                    ->select('view_absensi.*', 'users.nik_pegawai AS no_ktp')
                    ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                    ->where('tanggal', $request->input('tanggal'))
                    ->where('users.id', '<>', '5')
                    ->orderBy('tanggal', 'DESC')
                    ->get();
            return $a;
    	}
        if ( $request->input('client') ) {
            $a = DB::table('view_absensi')
                    ->select('view_absensi.*', 'users.nik_pegawai AS no_ktp')
                    ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                    ->where('tanggal', date('Y-m-d'))
                    ->where('users.id', '<>', '5')
                    ->orderBy('tanggal', 'DESC')
                    ->get();
            return $a;
    	}

        $columns = ['absensi.id', 'tanggal', 'nama_lengkap'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'nama_lengkap', DB::raw("IF(HOUR(tanggal) < '12:00:00', 'MASUK', 'KELUAR') AS status_absen"))
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))//->orderBy($columns[$column], $dir)
                ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MAX(id)'))
                        ->from('absensi')
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })->get();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nama_lengkap', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function sync(Request $request)
    {
        // $status = "update";
        DB::beginTransaction();
        try {

            // get data from local
            $local = DB::table('log_data')->select('id_server')->pluck('id_server')->toArray();

            //get data from server 
            $data = DB::connection('secondary')
                    ->table('log_data')
                    ->select('*')
                    ->whereNotIn('id', $local)
                    ->limit(1)
                    ->first();
            
            //select data 
            $dataTable = DB::connection('secondary')->table($data->field)->where('id', $data->id_data)->first();
            
            //convert from array object to array
            $dataTable = (array)$dataTable;
            
            //jika statusnya update eksekusi command ini
            if($data->status == 'update'){
                //update ke localdata 
                $update = DB::table($data->field)->where('id', $data->id_data)->update($dataTable);
                $log = DB::table('log_data')->where('id_data', $data->id_data)->update(['id_server' => $data->id, 'status' => 'update']);
            }else if($data->status == 'create'){
                //create ke localdata 
                $create = DB::table($data->field)->insert($dataTable);
                $log = DB::table('log_data')->where('id_data', $data->id_data)->update(['id_server' => $data->id, 'status' => 'create']);
            }else if($data->status == 'delete'){
                //update ke localdata 
                $delete = DB::table($data->field)->where('id', $data->id_data)->delete();
                $log = DB::table('log_data')->where('id_data', $data->id_data)->update(['id_server' => $data->id, 'status' => 'delete']);
            }

            $datas = DB::connection('secondary')->table('divisi')->where('id', 1)->update(['nama_divisi' => 'test']);
            
            // dd($data->id);
            // if($update){
                //update ke log data
           
            // }
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        //  $datas = DB::connection('secondary')->table('log_data')->where('id', $data->id)->update(['sync_client_1' => '1', 'old_value' => "test"]);
        // if($datas) echo "sukses"; else "gagal";
        return response()->json(['status' => 'success', 'data' => $dataTable, 'status_update' => $datas], 200);

    }
}
