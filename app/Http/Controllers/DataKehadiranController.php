<?php

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

        if ( $request->input('client') ) {
            return DB::table('absen_tambahan')
            ->select(DB::raw('DATE(tanggal) AS tanggal'), 'users.nik_pegawai AS no_ktp', 'absen_tambahan.id', 'id_karyawan', 'status', 'keterangan', 'nama_lengkap')
            ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
            ->get();
    	}

        $columns = ['tanggal', 'id_karyawan', 'status'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('absen_tambahan')
        ->select(DB::raw('DATE(tanggal) AS tanggal'), 'users.nik_pegawai AS no_ktp', 'absen_tambahan.id', 'id_karyawan', 'status', 'keterangan')
        ->join('users', 'users.id', '=', 'absen_tambahan.id_karyawan')
        ->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai AS no_ktp', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    // }

        // $data = Person::all();
        // // $data = array_push($data, Auth::user()->id);
        // return $data;

        //ORDER BY NYA BERDASARKAN PARAMETER YANG DIKIRIM DARI VUEJS
        //SEHINGGA PENGURUTAN DATANYA SESUAI DENGAN KOLOM YG DIINGINKAN OLEH USER
        // $posts = Person::orderBy(request()->sortby, request()->sortbydesc)
        //     //JIKA Q ATAU PARAMETER PENCARIAN INI TIDAK KOSONG
        //     ->when(request()->q, function($posts) {
        //         //MAKA FUNGSI FILTER AKAN DIJALANKAN
        //         $posts = $posts->where('first_name', 'LIKE', '%' . request()->q . '%')
        //             ->orWhere('last_name', 'LIKE', '%' . request()->q . '%');
        // })->paginate(request()->per_page); //KEMUDIAN LOAD PAGINATIONNYA BERDASARKAN LOAD PER_PAGE YANG DIINGINKAN OLEH USER
        // return response()->json(['status' => 'success', 'data' => $posts]);

    }

    public function getNik()
    {
        $data = DB::table('users')->select('nik_pegawai AS text', 'id AS value')->get();
        return ['data' => $data];
    }

    public function getDataNik(Request $request)
    {
        $tgl = is_array($request->input('tanggal'));
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
            
            if($tgl == TRUE){
                $data->whereBetween(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }else{
                $data->where(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }
            // ->whereBetween(DB::raw('DATE(tanggal)'), $request->input('tanggal'))

        if($request->nik != null){
            $data = $data->where('id_karyawan', $request->nik);
        }
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
            //code...
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
            //code...
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
