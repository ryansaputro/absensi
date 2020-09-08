<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use App\User;
use App\Absensi;
use DB;
use Auth;

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
                        // ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                        ->groupBy('id_karyawan')
                        ->orderBy('tanggal', 'DESC');
                    })
                ->get();
                
        $grafikPerdivisi = DB::table('absensi')
                ->select(DB::raw('COUNT(DISTINCT(absensi.id_karyawan)) AS jumlah'), 'nama_divisi')
                ->join('users', 'users.id', '=', 'absensi.id_karyawan')
                ->join('divisi', 'divisi.id', '=', 'users.id_divisi')
                // ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
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

    public function listAbsensi(Request $request)
    {
        if ( $request->input('tanggal') ) {
            $a =  DB::table('absensi')
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

}
