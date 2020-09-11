<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use App\User;
use App\Absensi;
use DB;
use Auth;

class ReportControllerCopy extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function laporanTerlambat(Request $request)
    {
        if ( $request->input('tanggal') ) {
            $a = DB::table('view_absensi')
                    ->whereBetween('tanggal', $request->input('tanggal'))
                    ->where(function ($query) {
                        $query->where('masuk', '>', '08:00')
                            ->OrWhere('keluar', '<', '17:00');
                    })
                    ->get();
            return ['data' => $a, 'data2' => json_encode($a), 'draw' => $request->input('draw')];
    	}
        if ( $request->input('client') ) {
            $a = DB::table('view_absensi')
                    ->where('tanggal', date('Y-m-d'))
                    ->where(function ($query) {
                        $query->where('masuk', '>', '08:00')
                            ->OrWhere('keluar', '<', '17:00');
                    })
                    ->get();
            return ['data' => $a, 'data2' => json_encode($a), 'draw' => $request->input('draw')];
    	}

        $columns = ['absensi.id', 'tanggal', 'name'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('view_absensi')
                    ->where('tanggal', date('Y-m-d'))
                    ->where(function ($query) {
                        $query->where('masuk', '>', '08:00')
                            ->OrWhere('keluar', '<', '17:00');
                    })
                    ->get();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nama_lengkap', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'data2' => json_encode($projects), 'draw' => $request->input('draw')];
    }

    public function laporanOvertime(Request $request)
    {
        if ( $request->input('tanggal') ) {
            $a = DB::table('view_absensi')
                    ->whereBetween('tanggal', $request->input('tanggal'))
                    ->where(function ($query) {
                        $query->where('masuk', '<', '08:00')
                            ->where('keluar', '>', '17:00');
                    })
                    ->get();
            return $a;
    	}
        if ( $request->input('client') ) {
            $a = DB::table('view_absensi')
                    ->where('tanggal', date('Y-m-d'))
                    ->where(function ($query) {
                        $query->where('masuk', '<', '08:00')
                            ->where('keluar', '>', '17:00');
                    })
                    ->get();
            return $a;
    	}

        $columns = ['absensi.id', 'tanggal', 'name'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('view_absensi')
                    ->where('tanggal', date('Y-m-d'))
                    ->where(function ($query) {
                        $query->where('masuk', '<', '08:00')
                            ->where('keluar', '>', '17:00');
                    })
                    ->get();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nama_lengkap', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function laporanSemua(Request $request)
    {
        
        $dataAbsen = [];
        $dataKeluar = [];
        $searchValue = $request->input('search');
        $tgl = is_array($request->input('tanggal'));

        if ( $request->input('tanggal') ) {
            $absen = DB::table('view_absensi')
            ->select('users.no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
            ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap');

            if($tgl == TRUE){
                $absen->whereBetween('tanggal', $request->input('tanggal'));
            }else{
                $absen->where('tanggal', $request->input('tanggal'));
            }
            
            $karyawan = User::all();
            
            if ($searchValue) {
                $absen->where(function($absen) use ($searchValue) {
                    $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.no_ktp', 'like', '%' . $searchValue . '%');
                });

                $karyawan = User::where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('no_ktp', 'like', '%' . $searchValue . '%')->get();

            }

            $absen = $absen->get();
            
            foreach($absen AS $k => $v){
                $dataAbsen[$v->tgl][$v->id_user] = $v->masuk; 
                $dataKeluar[$v->tgl][$v->id_user] = $v->keluar; 
            }

            $a = DB::table('view_absensi')
                    // ->whereBetween('tanggal', $request->input('tanggal'))
                    ->get();


            return [$a, 'karyawan' => $karyawan,'absen'=> $dataAbsen, 'keluar'=> $dataKeluar];
        }
        

        if ( $request->input('client') || ($request->input('search'))) {

            $karyawan = User::all();
            $absen = DB::table('view_absensi')
                ->select('users.no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
                ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                ->where('tanggal', date('Y-m-d'))
                ->get();

            if ($searchValue) {
                $absen->where(function($absen) use ($searchValue) {
                    $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.no_ktp', 'like', '%' . $searchValue . '%');
                });

                $karyawan = User::where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('no_ktp', 'like', '%' . $searchValue . '%')->get();

            }
         
            foreach($absen AS $k => $v){
                $dataAbsen[$v->tgl][$v->id_user] = $v->masuk; 
                $dataKeluar[$v->tgl][$v->id_user] = $v->keluar; 
            }

            $a = DB::table('view_absensi')
                    ->where('tanggal', date('Y-m-d'))
                    ->get();

            return [$a, 'karyawan' => $karyawan,'absen'=> $dataAbsen, 'keluar'=> $dataKeluar];
    	}

        $columns = ['absensi.id', 'tanggal', 'name'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('view_absensi')
                    ->where('tanggal', date('Y-m-d'))
                    ->get();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nama_lengkap', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        
        return ['data' => $projects, 'karyawan' => $karyawan,'absen'=> $absen, 'draw' => $request->input('draw')];
    }

     /**
     * Dashboard telat
     *
     */
    public function telat(Request $request){
        $data = DB::table('absensi')
                ->select('users.name AS first_name', 'absensi.tanggal', 
                    DB::raw('DATE_FORMAT(tanggal, "%H.%i") AS jam'), 
                    DB::raw('DATE_FORMAT(TIMEDIFF(tanggal, CONCAT(CURDATE(), " 08:00:00")), "%H.%i") AS selisih_jam'),
                    DB::raw('CONCAT(MOD(HOUR(TIMEDIFF(tanggal,CONCAT(CURDATE(), " 08:00:00"))), 24), " Jam ",
                            MINUTE(TIMEDIFF(tanggal,CONCAT(CURDATE(), " 08:00:00"))), " Menit ") AS deskripsi')
                    )
                ->join('users', 'users.id', '=', 'absensi.id_karyawan')
                ->where(DB::raw('DATE_FORMAT(tanggal, "%H:%i")'), '>', '08:00')
                ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MIN(id)'))
                        ->from('absensi')
                        ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })
                ->get();
                
        $grafikPerdivisi = DB::table('absensi')
                ->select(DB::raw('COUNT(DISTINCT(absensi.id_karyawan)) AS jumlah'), 'nama_divisi')
                ->join('users', 'users.id', '=', 'absensi.id_karyawan')
                ->join('divisi', 'divisi.id', '=', 'users.id_divisi')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                ->groupBy('id_divisi')
                ->get();

        return ['data' => $data, 'data2' => $grafikPerdivisi];
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
            $id_user = User::where('id_epc_tag', $request->tag)->value('id');
            
            //cek jika melebihi waktu 8 detik dari data sebelumnya maka data dapat disimpan 
            $cek = Absensi::select(DB::raw('DATE_ADD(tanggal, INTERVAL 8 SECOND) AS next'))->where('id_gate', $request->id_gate)
                    ->where('id_karyawan', $id_user)
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->orderBy('tanggal', 'DESC')
                    ->first();
            
            if($cek !== null){
                if($request->date > $cek->next){
                    $create = Absensi::create([
                        'id_gate' => $request->id_gate,
                        'id_karyawan' => $id_user,
                        'tanggal' => $request->date,
                    ]);
                    $message = "absen berhasil";
                }else{
                    $message = "harap berdiri agak jauh dari gerbang";
                }
            }else{
                $create = Absensi::create([
                        'id_gate' => $request->id_gate,
                        'id_karyawan' => $id_user,
                        'tanggal' => $request->date,
                    ]);
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
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'name', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), $request->tanggal)->get();
    	}
        if ( $request->input('client') ) {
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'name', 'nama_gerbang')
                    ->join('gate','absensi.id_gate', '=', 'gate.id')
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->get();
    	}

        $columns = ['absensi.id', 'tanggal', 'name'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'name')
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function pantau(Request $request)
    {
        if ( $request->input('tanggal') ) {
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'name', 'nama_gerbang')
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
        if ( $request->input('client') ) {
            return DB::table('absensi')
                    ->select('absensi.id', DB::raw('DATE_FORMAT(tanggal, "%H:%i") AS jam'), 'name', 'nama_gerbang')
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

        $columns = ['absensi.id', 'tanggal', 'name'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'name')
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function laporanKehadiran(Request $request)
    {
        if ( $request->input('tanggal') ) {
            $a =  DB::table('absensis')
                    ->select('name', DB::raw("IF(HOUR(tanggal) < '12:00:00', 'MASUK', 'KELUAR') AS status_absen"),'id_karyawan', DB::raw('MIN(DATE_FORMAT(tanggal, "%H:%i")) AS jam'), DB::raw('MAX(DATE_FORMAT(tanggal, "%H:%i")) AS keluar'))
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), $request->tanggal)
                    ->groupBy('id_karyawan')
                    ->get();
            return $a;
    	}
        if ( $request->input('client') ) {
            $a = DB::table('absensi')
                    ->select('name', DB::raw("IF(HOUR(tanggal) < '12:00:00', 'MASUK', 'KELUAR') AS status_absen"),'id_karyawan', DB::raw('MIN(DATE_FORMAT(tanggal, "%H:%i")) AS jam'), DB::raw('MAX(DATE_FORMAT(tanggal, "%H:%i")) AS keluar'))
                    ->join('users', 'users.id', '=', 'id_karyawan')
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->groupBy('id_karyawan')
                    ->get();
            return $a;
    	}

        $columns = ['absensi.id', 'tanggal', 'name'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absensi')->select('absensi.id', 'tanggal', 'name', DB::raw("IF(HOUR(tanggal) < '12:00:00', 'MASUK', 'KELUAR') AS status_absen"))
                ->join('users', 'users.id', '=', 'id_karyawan')
                ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))->orderBy($columns[$column], $dir)
                ->whereIn('absensi.id', function($query){
                        $query->select(DB::raw('MAX(id)'))
                        ->from('absensi')
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })->get();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    if ( $request->input('tanggal') ) {
            $a =  DB::table('view_absensi')
                    ->select('view_absensi.*', 'users.nik_pegawai AS no_ktp')
                    ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                    ->whereBetween('tanggal', $request->input('tanggal'))
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
