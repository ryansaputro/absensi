<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-09-28 11:01:41
 * @modify date 2020-09-28 11:01:41
 * @desc menghandle request dr modul master lokasi
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// import file model Person
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use DB;

class KantorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

     // mengambil semua data
    public function index(Request $request)
    {
        //jika request dari api dan terdapat parameter client maka proses ini dieksekusi
        if ( $request->input('client') ) {
            return DB::table('cabang')->get();
    	}

        //data deklarasi variable 
        $columns = ['nama_cabang', 'lokasi', 'deskripsi', 'status'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        //get data dari absensi tambahan
        $query = DB::table('cabang')
                ->orderBy($columns[$column], $dir);

        //jika user melakukan pencarian maka proses ini akan dieksekusi
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_cabang', 'like', '%' . $searchValue . '%')
                ->orWhere('deskripsi', 'like', '%' . $searchValue . '%');
            });
        }

        //data dari query di buat perhalaman sesuai dengan jumlah halaman yg diklik oleh user
        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    // mengambil data by id
    public function show($id)
    {
        $data = DB::table('cabang')->where('id', $id)->first();
        return [$data];
    }

    // menambah data
    public function store(Request $request)
    {
        //validate the data before processing
        $rules = [
            "nama_cabang"=> "required|unique:cabang,nama_cabang",
            "lokasi" => "lokasi|",
            "deskripsi" => "required|",
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
            $data = DB::table('cabang')->insert([
                'nama_cabang'  => $request->nama_cabang,
                'lokasi'  => $request->lokasi,
                'deskripsi' => $request->deskripsi,
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
        $data = DB::table('cabang')->where('id', $id)->first();
        //validate the data before processing
        $rules = [
            "nama_cabang"=> "required|unique:cabang,nama_cabang,".$data->nama_cabang.',nama_cabang',
            "lokasi" => "required|",
            "deskripsi" => "required|",
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
            $data = DB::table('cabang')->where('id',$id)->update([
                'nama_cabang'  => $request->nama_cabang,
                'lokasi'  => $request->lokasi,
                'deskripsi' => $request->deskripsi,
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
        $person = DB::table('cabang')->where('id', $id)->delete();
        return response()->json(['status' => 'success']);
    }
}
