<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// import file model Person
use App\Person;
use App\User;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use DB;

class PersonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

     // mengambil semua data
    public function all(Request $request)
    {

        if ( $request->input('client') ) {
    	    return User::select('id', 'nama_lengkap', 'nik_pegawai AS no_ktp', 'created_at', 'jabatan', 'bagian_divisi', 'tgl_masuk', 'masa_kerja')->where('id', '<>', '5')->get();
    	}

        $columns = ['nama_lengkap', 'nik_pegawai', 'created_at', 'jabatan', 'bagian_divisi', 'tgl_masuk', 'masa_kerja'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = User::select('id', 'nama_lengkap', 'nik_pegawai AS no_ktp', 'created_at', 'jabatan', 'bagian_divisi', 'tgl_masuk', 'masa_kerja')->where('id', '<>', '5')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('no_ktp', 'like', '%' . $searchValue . '%');
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

    public function divisi()
    {
        $data = DB::table('divisi')->where('status', '1')->get();
        return ['data' => $data];
    }

    public function provinsi()
    {
        $response = Curl::to('https://portalsepeda.com/portalsepeda/wp-content/uploads/data-indonesia-master/propinsi.json')->get();
        $data = json_decode($response, true);
        return ['data' => $data];
    }

    public function kota(Request $request)
    {
        $respKota = Curl::to('https://portalsepeda.com/portalsepeda/wp-content/uploads/data-indonesia-master/kabupaten/'.$request->id.'.json')->get();
        $data = json_decode($respKota, true);
        return ['data' => $data];
    }

    public function kecamatan(Request $request)
    {
        $respKota = Curl::to('https://portalsepeda.com/portalsepeda/wp-content/uploads/data-indonesia-master/kecamatan/'.$request->id.'.json')->get();
        $data = json_decode($respKota, true);
        return ['data' => $data];
    }

    public function kelurahan(Request $request)
    {
        $respKota = Curl::to('https://portalsepeda.com/portalsepeda/wp-content/uploads/data-indonesia-master/kelurahan/'.$request->id.'.json')->get();
        $data = json_decode($respKota, true);
        return ['data' => $data];
    }

    // mengambil data by id
    public function show($id)
    {
        return User::find($id);
    }

    // menambah data
    public function store(Request $request)
    {
        //validate the data before processing
        
        $rules = [
            "no_ktp" => "required|numeric|digits:16",
            "nama_lengkap" => "required|string",
            "no_telp" => "required|numeric|digits_between:10,15",
            "email" => "required|email:rfc,dns",
            "id_epc_tag" => "required|string",
            "provinsi" => "required|numeric",
            "kota" => "required|numeric",
            "kecamatan" => "required|numeric",
            "kelurahan" => "required|numeric",
            "kode_pos" => "required|numeric|digits:5",
            "rt" => "required|string|size:3",
            "rw" => "required|string|size:3",
            "alamat" => "required|string",
            "gol_darah" => "required|string",
            "divisi" => "required|numeric",
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
            $data = User::create([
                'id_epc_tag'  => $request->tag,
                'no_ktp' => $request->no_ktp,
                'nama_lengkap' => $request->nama_lengkap,
                'name' => $request->nama_lengkap,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'id_epc_tag' => $request->id_epc_tag,
                'provinsi' => $request->provinsi,
                'kota' => $request->kota,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'alamat' => $request->alamat,
                'gol_darah' => $request->gol_darah,
                'id_divisi' => $request->divisi,
                'password' =>  bcrypt('12345678')
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
            "no_ktp" => "required|numeric|digits:16",
            "nama_lengkap" => "required|string",
            "no_telp" => "required|numeric|digits_between:10,15",
            "email" => "required|email:rfc,dns",
            "id_epc_tag" => "required|string",
            "provinsi" => "required|numeric",
            "kota" => "required|numeric",
            "kecamatan" => "required|numeric",
            "kelurahan" => "required|numeric",
            "kode_pos" => "required|numeric|digits:5",
            "rt" => "required|string|size:3",
            "rw" => "required|string|size:3",
            "alamat" => "required|string",
            "gol_darah" => "required|string",
            "divisi" => "required|",
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.'
        ];

        $this->validate($request, $rules, $customMessages);
        // dd($request->all());
        DB::beginTransaction();
        try {
            //code...
            $data = User::find($id)->update([
                'id_epc_tag'  => $request->tag,
                'no_ktp' => $request->no_ktp,
                'nama_lengkap' => $request->nama_lengkap,
                'name' => $request->nama_lengkap,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'id_epc_tag' => $request->id_epc_tag,
                'provinsi' => $request->provinsi,
                'kota' => $request->kota,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'alamat' => $request->alamat,
                'gol_darah' => $request->gol_darah,
                'id_divisi' => $request->divisi,
                'password' =>  bcrypt('12345678')
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
        $person = User::find($id);
        $person->delete();
        return response()->json(['status' => 'success']);
    }
}
