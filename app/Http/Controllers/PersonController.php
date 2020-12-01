<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-10-01 13:26:03
 * @modify date 2020-10-01 13:26:03
 * @desc handle master data karyawan
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// import file model Person
use App\Person;
use App\User;
use App\UserStatusPegawai;
use App\UserAlamat;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use DB;
use File;

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
        $this->middleware('permission:read-pengguna|create-pengguna|edit-pengguna|delete-pengguna', ['only' => ['index','show']]);
        $this->middleware('permission:create-pengguna', ['only' => ['create','store']]);
        $this->middleware('permission:edit-pengguna', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-pengguna', ['only' => ['destroy']]);
        $this->path = public_path('images/karyawan');;
        $this->dimensions = ['245', '300', '500'];
    }

     // mengambil semua data
    public function all(Request $request)
    {

        if ( $request->input('client') ) {
    	    return DB::table('users')
                ->select('users.id','nama_lengkap', 'nik_pegawai AS no_ktp', 'nama_jabatan AS jabatan', 'nama_divisi AS bagian_divisi',
                DB::raw('DATE(tgl_masuk) AS tgl_masuk'), DB::raw('DATE(tgl_habis_kontrak) AS tgl_habis_kontrak'))
                ->join('users_status_pegawai', 'users.id', '=', 'users_status_pegawai.id_karyawan')
                ->join('jabatan', 'users.id_jabatan', '=', 'jabatan.id')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->where('users.id', '<>', '5')
                ->orderBy('nik_pegawai')
                ->get();
    	}

        // $columns = ['nama_lengkap', 'nik_pegawai', 'created_at', 'jabatan', 'bagian_divisi', 'tgl_masuk', 'masa_kerja'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('users')
                ->select('users.id','nama_lengkap', 'nik_pegawai AS no_ktp', 'nama_jabatan AS jabatan', 'nama_divisi AS bagian_divisi', 
                        DB::raw('DATE(tgl_masuk) AS tgl_masuk'), DB::raw('DATE(tgl_habis_kontrak) AS tgl_habis_kontrak'))
                ->join('users_status_pegawai', 'users.id', '=', 'users_status_pegawai.id_karyawan')
                ->join('jabatan', 'users.id_jabatan', '=', 'jabatan.id')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->where('users.id', '<>', '5')
                ->orderBy('nik_pegawai');

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('no_ktp', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');
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
        $jabatan = DB::table('jabatan')->where('status', '1')->get();
        $kantor = DB::table('cabang')->where('status', '1')->get();
        return ['data' => $data, 'jabatan' => $jabatan, 'kantor' => $kantor];
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
        $data = DB::table('users')
                ->select('nik_pegawai', 'nik_ktp', 'id_divisi', 'id_jabatan', 'nama_lengkap', 'gol_darah', 'no_telp', 'email', 'id_epc_tag', 'foto', 'tgl_masuk', 'tgl_habis_kontrak', 'status_pegawai', 'masa_kerja', 'id_cabang', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'alamat')
                ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'users.id')
                ->leftJoin('users_alamat', 'users_alamat.id_karyawan', '=', 'users.id')
                ->where('users.id', $id)
                ->first();

        return [$data];
    }

    // menambah data
    public function store(Request $request)
    {
        $path = $this->path; //. '/' . date('Y') . '/' . date('m');
        if (!File::isDirectory($path)) {$folder = File::makeDirectory($path, 0777, true);}
        if (!File::isDirectory($this->path)) {File::makeDirectory($this->path, 0777, true);}

        //validate the data before processing
        $rules = [
            "no_ktp" => "required|numeric|unique:users|digits_between:15,17",
            "nama_lengkap" => "required|string",
            "no_telp" => "required|numeric|digits_between:10,15",
            "email" => "required|email:rfc,dns",
            "id_epc_tag" => "required|unique:users|string",
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
            "foto" => "required|",
            "nik_pegawai" => "required|unique:users|",
            
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'kode_pos.digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'no_ktp.digits' => 'Isian :attribute harus berupa angka dengan 16 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.'
        ];

        $this->validate($request, $rules, $customMessages);
        DB::beginTransaction();
        try {
            //proses simpan foto
            $img = $request->foto;  // your base64 encoded
            $imageName = strtoupper(str_replace(' ', '_',$request->nama_lengkap));
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $file = $path.'/' . $imageName . '.'.$image_type;

            if(file_put_contents($file, $image_base64)){
                //proses simpan data
                $data = User::create([
                    'nik_pegawai'  => $request->nik_pegawai,
                    'nik_ktp' => $request->no_ktp,
                    'id_divisi' => $request->divisi,
                    'id_jabatan' => $request->jabatan,
                    'nama_lengkap' => $request->nama_lengkap,
                    'gol_darah' => $request->gol_darah,
                    'no_telp' => $request->no_telp,
                    'email' => $request->email,
                    'id_epc_tag' => $request->id_epc_tag,
                    'foto' => $imageName.'.'.$image_type
                ]);

                $data2 = UserStatusPegawai::create([
                    'id_karyawan'  => $data->id,
                    'tgl_masuk' => $request->tgl_masuk,
                    'tgl_habis_kontrak' => $request->tgl_akhir_kontrak,
                    'status_pegawai' => $request->status_karyawan,
                    'masa_kerja' => '-',
                    'id_cabang' => $request->kantor
                ]);

                $data3 = UserAlamat::create([
                    'id_karyawan'  => $data->id,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'kelurahan' => $request->kelurahan,
                    'kecamatan' => $request->kecamatan,
                    'kota' => $request->kota,
                    'provinsi' => $request->provinsi,
                    'kode_pos' => $request->kode_pos,
                    'alamat' => $request->alamat
                ]);
            }


            
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
        //definiskan path sesuai dengan global variable
        $path = $this->path;

        //cek jika folder belum ada maka akan menjalankan fungsi ini untuk membuat folder
        if (!File::isDirectory($path)) {$folder = File::makeDirectory($path, 0777, true);}
        if (!File::isDirectory($this->path)) {File::makeDirectory($this->path, 0777, true);}

        //get data dari ID yg di passing
        $data = DB::table('users')
            ->select('nik_pegawai', 'nik_ktp', 'id_divisi', 'id_jabatan', 'nama_lengkap', 'gol_darah', 'no_telp', 'email', 'id_epc_tag', 'foto', 'tgl_masuk', 'tgl_habis_kontrak', 'status_pegawai', 'masa_kerja', 'id_cabang', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'alamat')
            ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'users.id')
            ->leftJoin('users_alamat', 'users_alamat.id_karyawan', '=', 'users.id')
            ->where('users.id', $id)
            ->first(); 

        $rules = [
            "nik_pegawai" => "required|string|unique:users,nik_pegawai,".$data->nik_pegawai.',nik_pegawai',
            "nik_ktp" => "required|numeric|digits_between:15,17|unique:users,nik_ktp,".$data->nik_ktp.',nik_ktp',
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
            "foto" => "required|",
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.',
            'unique' => ':attribute tidak unik atau sudah dipakai.'
        ];

        $this->validate($request, $rules, $customMessages);

        $kelurahan = (int)$request->kelurahan;

        DB::beginTransaction();
        try {

            //cek jika ada foto maka lakukan proses ini
            if($data->foto != ""){

                //cek jika gambar ada di folder images/karyawan maka akan di lakukan proses penghapusan
                if(file_exists($path . '/' . $data->foto)){
                    //delete file exist
                    unlink($path . '/' . $data->foto);
                }
            }

                //jika foto diset maka proses ini akan dijalankan
                if($request->foto !== '/images/karyawan/'.$data->foto || $data->foto == ''){

                    //proses simpan foto
                    $img = $request->foto;  // your base64 encoded
                    $imageName = strtoupper(str_replace(' ', '_',$request->nama_lengkap));
                    $image_parts = explode(";base64,", $img);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $file = $path.'/' . $imageName . '.'.$image_type;
                    file_put_contents($file, $image_base64);
                    $data = User::where('id', $id)->update(['foto' => $imageName . '.'.$image_type]);
                }

                //proses simpan data
                $data = User::where('id', $id)->update([
                    'nik_pegawai'  => $request->nik_pegawai,
                    'nik_ktp' => $request->nik_ktp,
                    'id_divisi' => $request->divisi,
                    'id_jabatan' => $request->jabatan,
                    'nama_lengkap' => $request->nama_lengkap,
                    'gol_darah' => $request->gol_darah,
                    'no_telp' => $request->no_telp,
                    'email' => $request->email,
                    'id_epc_tag' => $request->id_epc_tag,
                ]);



                $data2 = UserStatusPegawai::updateOrCreate([
                    'id_karyawan' => $id],[
                    'tgl_masuk' => $request->tgl_masuk,
                    'tgl_habis_kontrak' => $request->tgl_akhir_kontrak,
                    'status_pegawai' => $request->status_karyawan,
                    'masa_kerja' => '-',
                    'id_cabang' => $request->kantor
                ]);

                $data3 = UserAlamat::updateOrCreate([
                    'id_karyawan' => $id],[
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'kelurahan' => $kelurahan,
                    'kecamatan' => $request->kecamatan,
                    'kota' => $request->kota,
                    'provinsi' => $request->provinsi,
                    'kode_pos' => $request->kode_pos,
                    'alamat' => $request->alamat
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
        //path utk foto karyawan
        $path = $this->path;

        DB::beginTransaction();
        try {

            //get data karyawan 
            $person = User::where('id', $id);

            //hapus data karyawan di table users status pegawai
            $person1 = UserStatusPegawai::where('id_karyawan', $id)->delete();

            //hapus data karyawan di table users alamat
            $person2 = UserAlamat::where('id_karyawan', $id)->delete();
            
            $data = $person->first();

            //cek jika gambar ada di folder images/karyawan maka akan di lakukan proses penghapusan
            if(file_exists($path . '/' . $data->foto)){
                //delete file exist
                unlink($path . '/' . $data->foto);
            }

            //delete karyawan dari table user
            $person->delete(); 

        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success'], 200);
    }

    public function androidMappingTag(Request $request){
         $rules = [
            "nik_pegawai" => "required|string|unique:users",
            "id_epc_tag" => "required|string|unique:users"
         ];
         $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.',
            'unique' => ':attribute tidak unik atau sudah dipakai.'
        ];

        $this->validate($request, $rules, $customMessages);
        $data = DB::table('users')
            ->select('nik_pegawai', 'nik_ktp', 'id_divisi', 'id_jabatan', 'nama_lengkap', 'gol_darah', 'no_telp', 'email', 'id_epc_tag', 'foto', 'tgl_masuk', 'tgl_habis_kontrak', 'status_pegawai', 'masa_kerja', 'id_cabang', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'alamat')
            ->join('users_status_pegawai', 'users_status_pegawai.id_karyawan', '=', 'users.id')
            ->leftJoin('users_alamat', 'users_alamat.id_karyawan', '=', 'users.id')
            ->where('users.nik_pegawai', $request->nik_pegawai)
            ->first(); 
            
        
        DB::beginTransaction();
        try {
           if($data->id_epc_tag == $request->id_epc_tag){
                return response()->json(['status' => 'failed'], 400);
           }
           
            $end = $request->id_epc_tag;
            //Split string into an array.  Each element is 2 chars
            $chunks = str_split($end, 2);
            //Convert array to string.  Each element separated by the given separator.
            krsort($chunks);
            $hasil = implode("",$chunks);
            
            $data = User::where('nik_pegawai', $request->nik_pegawai)->update(['id_epc_tag' => $hasil]);
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success'], 200);
    }
}
