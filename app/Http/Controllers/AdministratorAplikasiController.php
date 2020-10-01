<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-10-01 13:25:15
 * @modify date 2020-10-01 13:25:15
 * @desc handle form modul admin aplikasi
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Role;
use App\User;
use App\UserStatusPegawai;
use App\UserAlamat;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use DB;
use File;

class AdministratorAplikasiController extends Controller
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
    public function userLogin(Request $request)
    {

        if ( $request->input('client') ) {
    	    return DB::table('users')
                ->select('users.id','nama_lengkap', 'nik_pegawai AS no_ktp', 'name')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('nik_pegawai')
                ->get();
    	}

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('users')
                ->select('users.id','nama_lengkap', 'nik_pegawai AS no_ktp', 'name')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
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

    }

    // menambah data
    public function userLoginCreate(Request $request)
    {
        //validate the data before processing
        $rules = [
            "nik" => "required|unique:model_has_roles,model_id",
            "roles" => "required|"
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.',
            'unique' => ':attribute sudah terdaftar sebagai pengguna aplikasi.',
        ];

        $this->validate($request, $rules, $customMessages);
        DB::beginTransaction();
        try {
            //proses menyimpan data ke database...
            $data = DB::table('model_has_roles')->insert([
                'role_id' => $request->roles,
                'model_type' => 'App\User',
                'model_id' => $request->nik,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success'], 200);
    }

    public function userLoginShow($id)
    {
        $data = DB::table('model_has_roles')->where('model_id', $id)->first();
        return [$data];
    }
    
    // menambah data
    public function userLoginUpdate(Request $request)
    {
        //validate the data before processing
        $rules = [
            "nik" => "required|unique:model_has_roles,model_id,".$request->nik.',model_id',
            "roles" => "required|"
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.',
            'unique' => ':attribute sudah terdaftar sebagai pengguna aplikasi.',
        ];

        $this->validate($request, $rules, $customMessages);
        DB::beginTransaction();
        try {
            //proses menyimpan data ke database...
            $data = DB::table('model_has_roles')->where('model_id', $request->nik)->update([
                'role_id' => $request->roles,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success'], 200);
    }

    public function userLoginDelete($id)
    {
        $data = DB::table('model_has_roles')->where('model_id',$id)->delete();
        return response()->json(['status' => 'success']);
    }

     // mengambil semua data
    public function role(Request $request)
    {

        if ( $request->input('client') ) {
    	    return DB::table('roles')
                ->select('roles.name AS roles', DB::raw('GROUP_CONCAT(permissions.name) AS permissions'), 'roles.id')
                ->leftJoin('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
                ->leftJoin('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->groupBy('roles.id')
                ->orderBy('roles.id')
                ->get();
    	}

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('roles')
                ->select('roles.name AS roles', DB::raw('GROUP_CONCAT(permissions.name) AS permissions'), 'roles.id')
                ->leftJoin('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
                ->leftJoin('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->groupBy('roles.id')
                ->orderBy('roles.id');

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];

    }

    // menambah data
    public function roleCreate(Request $request)
    {
        //validate the data before processing
        $rules = [
            "permissions" => "required|",
            "roles" => "required|unique:roles,name"
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.',
            'unique' => ':attribute sudah terdaftar sebagai pengguna aplikasi.',
        ];

        $this->validate($request, $rules, $customMessages);
        DB::beginTransaction();
        try {
            //proses menyimpan data ke database...
            $data = Role::create([
                'name' => $request->roles,
                'guard_name' => 'web',
            ]);

            //save permission in table role has permissions
            $permissions = $request->permissions;
            foreach($permissions AS $k => $v){
                $data2 = DB::table('role_has_permissions')->insert([
                    'permission_id' => $v['value'],
                    'role_id' =>  $data->id
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

    public function roleShow($id)
    {
        $data = DB::table('roles')->select('name')->find($id);
        $permissions = DB::table('role_has_permissions')
                ->select('id AS value', 'name AS text')->where('role_id', $id)
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->get();
        return ['roles' => $data, 'permissions' => $permissions];
    }
    
    // menambah data
    public function roleUpdate(Request $request, $id)
    {
        $data = Role::find($id);
        
        //validate the data before processing
        $rules = [
            "permissions" => "required|",
            // "permissions" => "required|unique:model_has_roles,model_id,".$request->nik.',model_id',
            "roles" => "required|unique:roles,name,".$data->name.',name'
        ];

        $customMessages = [
            'required' => 'Isian :attribute tidak boleh kosong.',
            'numeric' => 'Isian :attribute harus berupa angka.',
            'digits_between' => 'Isian :attribute harus berupa angka dengan minimal 10 karakter dan maksimal 15.',
            'digits' => 'Isian :attribute harus berupa angka dengan 5 karakter.',
            'size' => 'Isian :attribute harus 3 karakter.',
            'unique' => ':attribute sudah terdaftar sebagai pengguna aplikasi.',
        ];

        $this->validate($request, $rules, $customMessages);
        DB::beginTransaction();
        try {
            //proses menyimpan data ke database...
            $data = Role::find($id)->update([
                'name' => $request->roles,
            ]);

            $deletedata =  DB::table('role_has_permissions')->where('role_id', $id)->delete();
            //save permission in table role has permissions
            $permissions = $request->permissions;
            foreach($permissions AS $k => $v){
                $data2 = DB::table('role_has_permissions')->insert([
                    'permission_id' => $v['value'],
                    'role_id' =>  $id
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

    public function roleDelete($id)
    {
        DB::beginTransaction();
        try {
                $data = DB::table('role_has_permissions')->where('role_id',$id)->delete();
                $role = Role::find($id)->delete();

            } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success']);
    }


    public function permission()
    {
        $data = DB::table('permissions')->select('name AS text', 'id AS value')->get();
        return $data;
    }


    



  
}
