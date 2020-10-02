<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-09-28 11:01:41
 * @modify date 2020-09-28 11:01:41
 * @desc menghandle request dr modul data kehadiran
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// import file model Person
use App\Person;
use App\User;
use App\AbsenTambahan;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use DB;

class DataKehadiranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:product-create', ['only' => ['create','store']]);
        // $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

     // mengambil semua data
    public function all(Request $request)
    {
        //jika request dari api dan terdapat parameter client maka proses ini dieksekusi
        if ( $request->input('client') ) {
            return DB::table('absen_tambahan')
                ->select(DB::raw('DATE(tanggal) AS tanggal'), 'users.nik_pegawai AS no_ktp', 'absen_tambahan.id', 'id_karyawan', 'status', 'keterangan', 'nama_lengkap')
                ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
                ->orderBy(DB::raw('DATE(tanggal)'), 'DESC')
                ->get();
    	}

        //data deklarasi variable 
        $columns = ['tanggal', 'id_karyawan', 'status'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        //get data dari absensi tambahan
        $query = DB::table('absen_tambahan')
                ->select(DB::raw('DATE(tanggal) AS tanggal'), 'users.nik_pegawai AS no_ktp', 'absen_tambahan.id', 'id_karyawan', 'status', 'keterangan')
                ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
                ->orderBy($columns[$column], $dir);

        //jika user melakukan pencarian maka proses ini akan dieksekusi
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai AS no_ktp', 'like', '%' . $searchValue . '%');
            });
        }

        //data dari query di buat perhalaman sesuai dengan jumlah halaman yg diklik oleh user
        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function getNik(Request $request)
    {
        //get data nik semua karyawan menggunakan axios get
        $data = DB::table('users')->select(DB::raw('CONCAT(nik_pegawai, " - ", nama_lengkap) AS text'), 'id AS value')->where('id', '<>', '5')->orderBy('nama_lengkap', 'ASC')->get();

        //get data request dari menu pengguna aplikasi utk create
        if(isset($request->from)){

            //get roles di menu roles
            $roles = DB::table('roles')->select('name', 'id')->get();
            return ['data' => $data, 'roles' => $roles];
        }

        return ['data' => $data];
    }

    public function getDataNik(Request $request)
    {
        //cek jika inputan tanggalnya berbentu array
        $tgl = is_array($request->input('tanggal'));

        //get data dari absen tambahan dan datanya dipecah sesuai dengan jenis absennya
        $data = DB::table('absen_tambahan')
            ->select(DB::raw('DATE(tanggal) AS tanggal'), 
                DB::raw('COUNT(status) AS jml'),  
                DB::raw("IF(status='S',  COUNT(status), '0') AS S "),  
                DB::raw("IF(status='I',  COUNT(status), '0') AS I "),  
                DB::raw("IF(status='A',  COUNT(status), '0') AS A "),  
                DB::raw("IF(status='C',  COUNT(status), '0') AS C "),  
                'users.nik_pegawai AS no_ktp', 'absen_tambahan.id', 'id_karyawan', 'status', 'keterangan', 'nama_lengkap')
                ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
                ->groupBy('status', DB::raw('DATE(tanggal)'))
                ->orderBy(DB::raw('DATE(tanggal)'));
            
        //jika $tgk nya berbentuk array maka akan mengekseskusi peroses ini
        if($tgl == TRUE){
            $data->whereBetween(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
        }else{
            $data->where(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
        }

        //cek jika nik nya tidak boleh null
        if($request->nik != null){
            $data = $data->where('id_karyawan', $request->nik);
        }

        //get data absen tambahan
        $data = $data->get();

        return ['data' => $data];
    }

    // mengambil data by id
    public function show($id)
    {
        return AbsenTambahan::select(DB::raw('DATE(tanggal) AS tanggal'), 'id', 'id_karyawan', 'status')->find($id);
    }

    // menambah data
    public function store(Request $request)
    {
        //validate the data before processing
        $rules = [
            "tanggal"=> "required|date",
            "nik" => "required|",
            "status" => "required|",
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.'
        ];

        $this->validate($request, $rules, $customMessages);
        DB::beginTransaction();
        try {
            //proses menyimpan data ke database...
            $data = DB::table('absen_tambahan')->insert([
                'tanggal'  => $request->tanggal,
                'id_karyawan' => $request->nik,
                'status' => $request->status,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success'], 200);
    }

    // mengubah data
    public function update($id, Request $request)
    {
        $rules = [
            "tanggal"=> "required|date",
            "nik" => "required|",
            "status" => "required|",
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.'
        ];

        $this->validate($request, $rules, $customMessages);

        DB::beginTransaction();
        try {
            //proses mengupdate data ke database...
            $data = AbsenTambahan::find($id)->update([
                'tanggal'  => $request->tanggal,
                'id_karyawan' => $request->nik,
                'status' => $request->status,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success'], 200);
    }

    // menghapus data
    public function delete($id)
    {
        $person = AbsenTambahan::find($id);
        $person->delete();
        return response()->json(['status' => 'success']);
    }
}
