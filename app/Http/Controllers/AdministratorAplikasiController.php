<?php

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

     // mengambil semua data
    public function role(Request $request)
    {

        if ( $request->input('client') ) {
    	    return DB::table('roles')
                ->select('name')
                ->orderBy('id')
                ->get();
    	}

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('roles')
                ->select('name')
                ->orderBy('id');

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];

    }

     // mengambil semua data
    public function permission(Request $request)
    {

        if ( $request->input('client') ) {
    	    return DB::table('permissions')
                ->select('name')
                ->orderBy('id')
                ->get();
    	}

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = DB::table('permissions')
                ->select('name')
                ->orderBy('id');

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];

    }

  
}
