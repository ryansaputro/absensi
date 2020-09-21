<?php

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
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /**
     * Get authenticated user
     */
    public function checkUser(Request $request)
    {
        dd(JWTAuth::user());
        $user = User::find(Auth::user()->id);
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
    public function index(Request $request)
    {
        if ( $request->input('client') ) {
    	    return Person::select('id', 'first_name', 'last_name', 'created_at', 'dt_txt', 'temp')->get();
    	}

        $columns = ['first_name', 'last_name', 'created_at', 'dt_txt', 'temp'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        // $query = Person::select('id', 'first_name', 'last_name', 'created_at')->orderBy($columns[$column], $dir);
        $query = Person::select('id', 'first_name', 'last_name', 'created_at', 'dt_txt', 'temp');

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('last_name', 'like', '%' . $searchValue . '%')
                ->orWhere('first_name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->get();//->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

     /**
     * Dashboard telat
     *
     */
    public function telat(Request $request){
        // $data = DB::table('absensi')
        //         ->select('users.nama_lengkap', 'absensi.tanggal', 
        //             DB::raw('DATE_FORMAT(tanggal, "%H.%i") AS jam'), 
        //             DB::raw('DATE_FORMAT(TIMEDIFF(tanggal, CONCAT(CURDATE(), " 08:00:00")), "%H.%i") AS selisih_jam'),
        //             DB::raw('CONCAT(MOD(HOUR(TIMEDIFF(tanggal,CONCAT(CURDATE(), " 08:00:00"))), 24), " Jam ",
        //                     MINUTE(TIMEDIFF(tanggal,CONCAT(CURDATE(), " 08:00:00"))), " Menit ") AS deskripsi')
        //             )
        //         ->join('users', 'users.id', '=', 'absensi.id_karyawan')
        //         ->where(DB::raw('DATE_FORMAT(tanggal, "%H:%i")'), '>', '08:00')
        //         ->whereIn('absensi.id', function($query){
        //                 $query->select(DB::raw('MIN(id)'))
        //                 ->from('absensi')
        //                 // ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
        //                 ->groupBy('id_karyawan')
        //                 ->orderBy('tanggal', 'DESC');
        //             })
        //         ->get();
        
        // $data = DB::table('view_absensi')
        //         ->select(DB::raw('COUNT(masuk) AS jumlah'), 
        //                 'users.kantor')
        //         ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
        //         ->groupBy('users.kantor')
        //         ->get();
        $kantor = ['bandung', 'surabaya'];
        $data = User::select(DB::raw('COUNT(kantor) AS jumlah'), 'kantor')->groupBy('kantor')->where('id', '<>', '5')->get();//->pluck('jumlah', 'kantor')->toArray();
        
        $grafikPerdivisi = DB::table('absensi')
                ->select(DB::raw('COUNT(DISTINCT(absensi.id_karyawan)) AS jumlah'), 'kantor')
                ->join('users', 'users.id', '=', 'absensi.id_karyawan')
                // ->join('divisi', 'divisi.id', '=', 'users.id_divisi')
                // ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                ->groupBy('kantor')
                ->get();

        $isac = DB::table('absen_tambahan')
                ->select(
                    DB::raw('COUNT(status) AS jml'),
                    'status', 'kantor'
                    )
                ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), '2020-09-08')
                // ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                ->groupBy('status', 'kantor')
                ->get();

        $absensi = DB::table('view_absensi')
                ->select(DB::raw('COUNT(tanggal) AS kehadiran'), 'kantor')
                ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                ->where('tanggal', '2020-09-10')
                // ->where('tanggal', date('Y-m-d'))
                ->groupBy('kantor')
                ->pluck('kehadiran', 'kantor')
                ->toArray();

        $telatKaryawan = DB::table('view_absensi')
            ->select(DB::raw('COUNT(masuk) AS jumlah_telat'), 'kantor', 'tanggal')
            ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
            ->where('masuk', '>', '08:00')
            ->where('tanggal', '>=', DB::raw('DATE(NOW()) - INTERVAL 7 DAY'))
            ->groupBy('tanggal', 'kantor')
            // ->pluck('jumlah_telat', 'tanggal')
            // ->toArray();
            ->get();
        
        $tepatKaryawan = DB::table('view_absensi')
            ->select(DB::raw('COUNT(masuk) AS jumlah_tepat'), 'kantor', 'tanggal')
            ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
            ->where('masuk', '<=', '08:00')
            ->where('tanggal', '>=', DB::raw('DATE(NOW()) - INTERVAL 30 DAY'))
            ->groupBy('tanggal', 'kantor')
            // ->pluck('jumlah_tepat', 'kantor')
            ->get();
        
        $userArr = $data->pluck('jumlah', 'kantor')->toArray();
        
        $date = date('Y-m-d', strtotime('-30 days'));
        $end_date = date('Y-m-d');
        // End date
        $dates = [];
        while (strtotime($date) <= strtotime($end_date)) {
                    $dates[] = $date;
                    $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        //list date
        //$dates
        $telat = [];
        foreach($telatKaryawan AS $k => $v){
            $telat[$v->tanggal][$v->kantor] = $v->jumlah_telat;
        }

        $tepat = [];
        foreach($tepatKaryawan AS $k => $v){
            $tepat[$v->tanggal][$v->kantor] = $v->jumlah_tepat;
        }

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

        if(count($isac) > 0){
            foreach($isac AS $k => $v){
                $jenis[$v->kantor][$v->status] = $v->jml;
            }
        }

            
        return ['data' => $data, 'data2' => $grafikPerdivisi, 'data3' => $isac, 'data4' => $jenis, 'data5' => $jmlTelat, 'data6' => $terlambat];
    }

    public function getAbsen(){
        $data = DB::table('view_absensi')->get();
        $absen = [];
        foreach($data AS $k => $v){
            $absen[$v->nama_lengkap][$v->tanggal] = $v->masuk;
        }
        return response()->json(['data' => $absen]);
    }

    public function absen(Request $request){
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
            
            //cek jika melebihi waktu 8 detik dari data sebelumnya maka data dapat disimpan 
            $cek = Absensi::select(DB::raw('DATE_ADD(tanggal, INTERVAL 100 SECOND) AS next'))->where('id_gate', $request->id_gate)
                    ->where('id_karyawan', $id_user->id)
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->orderBy('tanggal', 'DESC')
                    ->first();
            
            if($cek !== null){
                if($request->date > $cek->next){
                    $create = Absensi::create([
                        'id_gate' => $request->id_gate,
                        'id_karyawan' => $id_user->id,
                        'tanggal' => $request->date,
                    ]);

                    //push to pusher (websocket)
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

                    $status = date("H:i", strtotime($request->date)) > '16:00' ? 'terlambat' : 'tepat';
                    
                    $data['message'] = array("foto" => "hallo", "nik" => $id_user->nik_pegawai , "nama" => $id_user->nama_lengkap , "jam" => date("H:i", strtotime($request->date)), "status" => $status);
                    $pusher->trigger('my-channel', 'my-event', $data);

                    $message = "absen berhasil";
                }else{
                    $message = "data absensi anda sudah tercatat ke dalam sistem. harap berdiri agak jauh dari gerbang";
                }
            }else{
                $create = Absensi::create([
                        'id_gate' => $request->id_gate,
                        'id_karyawan' => $id_user->id,
                        'tanggal' => $request->date,
                    ]);

                //push to pusher (websocket)
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

                $status = date("H:i", strtotime($request->date)) > '12:00' ? 'terlambat' : 'tepat';
                
                $data['message'] = array("foto" => "hallo", "nik" => $id_user->nik_pegawai , "nama" => $id_user->nama_lengkap , "jam" => date("H:i", strtotime($request->date)), "status" => $status);
                $pusher->trigger('my-channel', 'my-event', $data);

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
        if ( $request->input('tanggal') ) {
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nama_lengkap', 'nik_pegawai', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), $request->tanggal)->get();
    	}
        if ( $request->input('client') ) {
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nama_lengkap', 'nik_pegawai', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->get();
    	}

        $columns = ['absensi.id', 'tanggal', 'nama_lengkap'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'nik_pegawai', 'nama_lengkap')
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nama_lengkap', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function pantau(Request $request)
    {
        if ( $request->input('tanggal') ) {
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
        if(isset($request->lantai)){
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nik_pegawai','nama_lengkap', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where('gate.id', $request->lantai)
                    ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MAX(id)'))
                        ->from('absensi')
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })
                    ->get();
        }
        if ( $request->input('client') ) {
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'nama_lengkap', 'nik_pegawai','nama_gerbang')
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

        $columns = ['absensi.id', 'tanggal', 'nama_lengkap'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'nama_lengkap')
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function cekAbsen(Request $request) {
        $a = DB::table('view_absensi')
                    ->select('view_absensi.*', 'users.nik_pegawai', 'users.nama_lengkap')
                    ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap');

        if($request->status_absen == 'keluar'){
            $a->where('keluar', '!=', '-');
        }
            $a =  $a->where('tanggal', date('Y-m-d'))
                    ->where('users.id', '<>', '5')
                    ->orderBy('tanggal', 'DESC')
                    ->get();
            return $a;
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

}
