<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-09-28 11:34:55
 * @modify date 2020-09-28 11:34:55
 * @desc menghandle request semua laporan
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use App\User;
use App\Absensi;
use DB;
use Auth;

class ReportController extends Controller
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

        if ( $request->input('tanggal')[0] != null && $request->input('tanggal')[1] != null ) {
            $absen = DB::table('view_absensi')
            ->select(DB::raw('COUNT(masuk) AS kehadiran'), 'users.nik_pegawai AS no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
            ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
            ->where('users.id', '<>', '5')
            ->groupBy('users.id');

            $status = DB::table('absen_tambahan')
            ->select(DB::raw('COUNT(id_karyawan) AS jml'), 'id_karyawan', 'status')
            ->groupBy('status','id_karyawan');

            if($tgl == TRUE){
                $absen->whereBetween('tanggal', $request->input('tanggal'));
                $status->whereBetween(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }else{
                $absen->where('tanggal', $request->input('tanggal'));
                $status->where(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }
            
            $karyawan = User::where('users.id', '<>', '5')->orderby('nama_lengkap', 'ASC')->get();
            
            if ($searchValue) {
                $absen->where(function($absen) use ($searchValue) {
                    $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
                });

                $karyawan = User::where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%')->get();

            }

            $absen = $absen->get();
            $status = $status->get();

            if(count($status) > 0){
                foreach($status AS $k => $v){
                    $statusMasuk[$v->id_karyawan][$v->status] = $v->jml;  
                }
            }else{
                    $statusMasuk[][] = '';
            }

            $jml = [];
            foreach($absen AS $k => $v){
                $dataAbsen[$v->tgl][$v->id_user] = $v->masuk; 
                $dataKeluar[$v->tgl][$v->id_user] = $v->keluar; 
                $jml[$v->id_user] = $v->kehadiran;
            }


            return ['statusMasuk' => $statusMasuk, 'karyawan' => $karyawan,'absen'=> $dataAbsen, 'keluar'=> $dataKeluar, 'kehadiran' => $jml];
        }
        

        if ( $request->input('client') || ($request->input('search'))) {

            $karyawan = User::where('users.id', '<>', '5')->orderby('nama_lengkap', 'ASC')->get();

            $absen = DB::table('view_absensi')->select(DB::raw('COUNT(masuk) AS kehadiran'), 'users.nik_pegawai AS no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
            ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
            ->where('tanggal', date('Y-m-d'))
            ->orderby('nama_lengkap', 'ASC')
            // ->where('users.id', '<>', '5')
            ->groupBy('users.id');
            
            $status = DB::table('absen_tambahan')
            ->select(DB::raw('COUNT(id_karyawan) AS jml'), 'id_karyawan', 'status')
            ->groupBy('status','id_karyawan');

            if($tgl == TRUE){
                // $absen->whereBetween('tanggal', $request->input('tanggal'));
                $status->whereBetween(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }else{
                // $absen->where('tanggal', $request->input('tanggal'));
                $status->where(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }


            if ($searchValue) {
                $absen->where(function($absen) use ($searchValue) {
                    $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
                });

                $karyawan = User::where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%')->orderby('nama_lengkap', 'ASC')->get();

            }

            $absen = $absen->get();
            $status = $status->get();
            $jml = [];

            if(count($status) > 0){
                foreach($status AS $k => $v){
                    $statusMasuk[$v->id_karyawan][$v->status] = $v->jml;  
                }
            }else{
                    $statusMasuk[][] = '';
            }

            foreach($absen AS $k => $v){
                $dataAbsen[$v->tgl][$v->id_user] = $v->masuk; 
                $dataKeluar[$v->tgl][$v->id_user] = $v->keluar; 
                $jml[$v->id_user] = $v->kehadiran;
            }

            return ['statusMasuk' => $statusMasuk, 'karyawan' => $karyawan,'absen'=> $dataAbsen, 'keluar'=> $dataKeluar, 'kehadiran' => $jml];
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

    public function rekapKeterlambatan(Request $request)
    {
        //deklarasi variable
        $dataAbsen = [];
        $dataKeluar = [];
        $searchValue = $request->input('search');

        //cek jika tanggal adalah array maka jika benar bernilai true 
        $tgl = is_array($request->input('tanggal'));
        //jika tanggal array tidak sama dengan null maka akan men eksekusi proses ini 
        if ( $request->input('tanggal') ) {
            
            //get data kehadiran dari table view_kehadiran
            $absen = DB::table('view_absensi')
                ->select(DB::raw('COUNT(masuk) AS kehadiran'), 'users.nik_pegawai AS no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
                ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                ->where('users.id', '<>', '5')
                ->groupBy('users.id');

            //get data jumlah menit telat per kryawan dan jumlah telat
            $status = DB::table('view_absensi')
                ->select(
                    DB::raw('IF(telat != "00:00", COUNT(id_karyawan), 0) AS jml_telat'),
                    DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `telat` ) ) ) AS menit_telat'), 
                    DB::raw("SUM(EXTRACT(HOUR FROM (SEC_TO_TIME(TIME_TO_SEC(telat) % 86400)))) hours"), 
                    DB::raw("SUM(EXTRACT(MINUTE FROM (SEC_TO_TIME(TIME_TO_SEC(telat) % 86400)))) minutes"), 
                    'id_karyawan', 'view_absensi.nama_lengkap', 'nik_pegawai'
                )
                ->join('users', 'users.id', 'view_absensi.id_karyawan')
                ->where('telat', '!=', '00:00')
                ->groupBy('id_karyawan');
                
                //filter user 
                if($request->input('filterby')){
                    if($request->filterby == 'terlambat_paling_banyak'){
                        $status->orderBy('jml_telat', 'DESC')->orderBy('nama_lengkap', 'ASC');
                    }elseif($request->filterby == 'terlambat_paling_sedikit'){
                        $status->orderBy('jml_telat', 'ASC')->orderBy('nama_lengkap', 'ASC');
                    }elseif($request->filterby == 'total_terlambat_paling_banyak'){
                        $status->orderBy('menit_telat', 'DESC')->orderBy('nama_lengkap', 'ASC');
                    }elseif($request->filterby == 'total_terlambat_paling_sedikit'){
                        $status->orderBy('menit_telat', 'ASC')->orderBy('nama_lengkap', 'ASC');
                    }
                }else{
                    $status->orderBy('jml_telat', 'DESC');
                }
            
            //jika $tgl nya berupa array maka akan dieksekusi perintah ini
            if($tgl == TRUE){
                $absen->whereBetween('tanggal', $request->input('tanggal'));
                $status->whereBetween('tanggal', $request->input('tanggal'));
            }else{
                $absen->where('tanggal', $request->input('tanggal'));
                $status->where('tanggal', $request->input('tanggal'));
            }
            
            //jika user melakukan pencarian maka akan di eksekusi perintah ini
            if ($searchValue) {
                $absen->where(function($absen) use ($searchValue) {
                    $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
                });
                $status->where(function($status) use ($searchValue) {
                    $status->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
                });
            }

            //data di get dengan bentuk array
            $absen = $absen->get();
            $status = $status->get();

            //mendeklarsikan variable menjadi array 
            $jml = [];
            $id_karyawan = [];
            $dataKehadiranTerlambat = [];
            $jml = [];


            //looping utk absen dan membuat variable baru utk di passing ke view
            foreach($absen AS $k => $v){
                
                //membuat data jml absen pertanggal dan per karyawan
                $dataAbsen[$v->tgl][$v->id_user] = $v->masuk; 

                //membuat data jml yg sudah melakukan absen yg keluar
                $dataKeluar[$v->tgl][$v->id_user] = $v->keluar; 

                //membuat jumlah data kehadiran
                $jml[$v->id_user] = $v->kehadiran;
                

            }

            //jika variable $status jumlahnya lebih dari 0 maka akan di eksekusi perintah ini 
            if(count($status) > 0){
                foreach($status AS $k => $v){
                    $jmlTelat[$v->id_karyawan] = $v->jml_telat;  
                    $jmlMenit[$v->id_karyawan] = ($v->hours*60) + $v->minutes. " menit";//$v->hours. " jam ". $v->minutes. " menit";//substr($v->menit_telat,0,8);  
                    $id_karyawan[] = $v->id_karyawan;
                    $dataKehadiranTerlambat[] = array("nama_lengkap" => $v->nama_lengkap, "nik_pegawai" => $v->nik_pegawai, "kehadiran" => $jml[$v->id_karyawan],  "terlambat" => $v->jml_telat, "menit_terlambat" => number_format(($v->hours*60) + $v->minutes,0,",","."). " menit");

                }
            }else{
                    $jmlTelat[][] = '';
                    $jmlMenit[][] = '';
                    $id_karyawan[] = '';
                    $dataKehadiranTerlambat = [];
            }

            //get data karyawan yg tidak absen 
            $karyawanNotAbsen = User::where('users.id', '<>', '5')->whereNotIn('id', $id_karyawan)->orderBy('nama_lengkap', 'ASC');
            
            //get data karyawan yg melakukan absen 
            $karyawanAbsen = User::where('users.id', '<>', '5')->whereIn('id', $id_karyawan)->orderBy('nama_lengkap', 'ASC');
            
            //jika user melakukan pencarian maka akan dieksekusi proses ini 
            if($searchValue){
                $karyawanNotAbsen = $karyawanNotAbsen->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');//->get();
                $karyawanAbsen = $karyawanAbsen->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');//->get();

            }

            //data karyawan dibentuk menjadi array 
            $karyawanNotAbsen = $karyawanNotAbsen->get();
            $karyawanAbsen = $karyawanAbsen->get();

            return ['dataKehadiranTerlambat' => $dataKehadiranTerlambat, 'status' => $status, 'karyawanNotAbsen' => $karyawanNotAbsen, 'karyawanAbsen' => $karyawanAbsen, 'jmlTelat' =>  $jmlTelat, 'jmlMenit' => $jmlMenit,'absen'=> $dataAbsen, 'keluar'=> $dataKeluar, 'kehadiran' => $jml];
        }
        
        //jika api request mempunyai parameter client ataupun user melakukan serach maka akn meng eksekusi perintah ini 
        if ( $request->input('client') || ($request->input('search')) || $request->tanggal[0] === "null") {

            //get data absen dari table view absensi
            $absen = DB::table('view_absensi')->select(DB::raw('COUNT(masuk) AS kehadiran'), 'users.nik_pegawai AS no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
                ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                ->where('tanggal', date('Y-m-d'))
                ->orderBy('nama_lengkap', 'ASC')
                // ->where('users.id', '<>', '5')
                ->groupBy('users.id');
            
            //get data status jumlah telat dan telat dalam menit 
            $status = DB::table('view_absensi')
                ->select(
                    DB::raw('IF(telat != "00:00", COUNT(id_karyawan), 0) AS jml_telat'),
                    DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `telat` ) ) ) AS menit_telat'), 
                    DB::raw("SUM(EXTRACT(HOUR FROM (SEC_TO_TIME(TIME_TO_SEC(telat) % 86400)))) hours"), 
                    DB::raw("SUM(EXTRACT(MINUTE FROM (SEC_TO_TIME(TIME_TO_SEC(telat) % 86400)))) minutes"), 
                    'id_karyawan', 'view_absensi.nama_lengkap', 'nik_pegawai'
                )
                ->join('users', 'users.id', 'view_absensi.id_karyawan')
                ->where('telat', '!=', '00:00')
                ->where('tanggal', date('Y-m-d'))
                ->groupBy('id_karyawan')
                ->orderBy('jml_telat', 'DESC');
                    
            // if($tgl == TRUE){
            //     // $absen->whereBetween('tanggal', $request->input('tanggal'));
            //     $status->whereBetween('tanggal', $request->input('tanggal'));
            // }else{
            //     // $absen->where('tanggal', $request->input('tanggal'));
            //     $status->where('tanggal', $request->input('tanggal'));
            // }

            //jika user malakukan serach atapun pencarian maka akan mengeksekusi perintah ini 
            if ($searchValue) {
                $absen->where(function($absen) use ($searchValue) {
                    $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
                });

            }

           //data di get dengan bentuk array
            $absen = $absen->get();
            $status = $status->get();

            //mendeklarsikan variable menjadi array 
            $jml = [];
            $id_karyawan = [];

            //get data absen karyawan yg hadir            
            foreach($absen AS $k => $v){
                $dataAbsen[$v->tgl][$v->id_user] = $v->masuk; 
                $dataKeluar[$v->tgl][$v->id_user] = $v->keluar; 
                $jml[$v->id_user] = $v->kehadiran;
            }

            //jika jml status lebih dari 0 maka akan meng eksekusi perintah ini 
            if(count($status) > 0){

                //looping status
                foreach($status AS $k => $v){
                    //membuat variable  utk jumlah telat  berdasarkan karyawan
                    $jmlTelat[$v->id_karyawan] = $v->jml_telat;  

                    //membuat variable utk jumlah menit telat berdasarkan karyawan 
                    $jmlMenit[$v->id_karyawan] = ($v->hours*60) + $v->minutes. " menit";//$v->hours. " jam ". $v->minutes. " menit";//substr($v->menit_telat,0,8); 

                    //membuat variable id karyawan ke dalam array utk digunakan sebagai filter karyawan yg hadir terlambat paling banyak 
                    $id_karyawan[] = $v->id_karyawan;

                    //data karyawan terlambat order by jumlah terlambat
                    $dataKehadiranTerlambat[] = array("nama_lengkap" => $v->nama_lengkap, "nik_pegawai" => $v->nik_pegawai, "kehadiran" => $jml[$v->id_karyawan],  "terlambat" => $v->jml_telat, "menit_terlambat" => number_format(($v->hours*60) + $v->minutes,0,",","."). " menit");

                }
            }else{
                    $jmlTelat[] = '';
                    $jmlMenit[] = '';
                    $id_karyawan[] = '';
                    $dataKehadiranTerlambat = [];
            }

            //get karyawan yg tidak absen
            $karyawanNotAbsen = User::where('users.id', '<>', '5')->whereNotIn('id', $id_karyawan)->orderBy('nama_lengkap', 'ASC');

            //get karyawan yg absen
            $karyawanAbsen = User::where('users.id', '<>', '5')->whereIn('id', $id_karyawan)->orderBy('nama_lengkap', 'ASC');
            
            //jika user melakukan pencarian maka fungsi ini akan diproses 
            if($searchValue){
                $karyawanNotAbsen = $karyawanNotAbsen->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');//->get();
                $karyawanAbsen = $karyawanAbsen->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                    ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');//->get();

            }

            $karyawanNotAbsen = $karyawanNotAbsen->get();
            $karyawanAbsen = $karyawanAbsen->get();

            return ['dataKehadiranTerlambat' => $dataKehadiranTerlambat,'karyawanNotAbsen' => $karyawanNotAbsen, 'karyawanAbsen' => $karyawanAbsen, 'jmlTelat' =>  $jmlTelat, 'jmlMenit' => $jmlMenit, 'absen'=> $dataAbsen, 'keluar'=> $dataKeluar, 'kehadiran' => $jml];
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

    public function rekapKeterlambatanExcel(Request $request)
    {
        $searchValue = $request->input('search');
        $tgl = is_array($request->input('tanggal'));
        // $karyawan = User::where('users.id', '<>', '5')->get();

        $absen = DB::table('view_absensi')->select(DB::raw('COUNT(masuk) AS kehadiran'), 'users.nik_pegawai AS no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
        ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
        ->where('users.id', '<>', '5')
        ->groupBy('users.id');
        
        $status = DB::table('view_absensi')
        ->select(
            DB::raw('IF(telat != "00:00", COUNT(id_karyawan), 0) AS jml_telat'),
            DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `telat` ) ) ) AS menit_telat'), 
            'id_karyawan', 'nama_lengkap'
        )
        ->where('telat', '!=', '00:00')
        // ->where('tanggal', date('Y-m-d'))
        ->groupBy('id_karyawan')
        ->orderBy('jml_telat', 'DESC');

        if($tgl == TRUE){
            $absen->whereBetween('tanggal', $request->input('tanggal'));
            $status->whereBetween('tanggal', $request->input('tanggal'));
        }else{
            $absen->where('tanggal', $request->input('tanggal'));
            $status->where('tanggal', $request->input('tanggal'));
        }


        // if ($searchValue) {
        //     $absen->where(function($absen) use ($searchValue) {
        //         $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
        //         ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
        //     });

        //     $karyawan = User::where('nama_lengkap', 'like', '%' . $searchValue . '%')
        //         ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%')->get();

        // }

        $absen = $absen->pluck('kehadiran', 'id_user')->toArray();
        $status = $status->get();
        $data = [];

        if(count($status) > 0){
            foreach($status AS $k => $v){
                $jmlTelat[$v->id_karyawan] = $v->jml_telat;  
                $jmlMenit[$v->id_karyawan] = substr($v->menit_telat,0,8);  
                $id_karyawan[] = $v->id_karyawan; 
            }
        }else{
                $jmlTelat[] = '';  
                $jmlMenit[] = '';  
                $id_karyawan[] = ''; 
        }

        $karyawanNotAbsen = User::where('users.id', '<>', '5')->whereNotIn('id', $id_karyawan)->orderBy('nama_lengkap', 'ASC');//->get();
        $karyawanAbsen = User::where('users.id', '<>', '5')->whereIn('id', $id_karyawan)->orderBy('nama_lengkap', 'ASC');//->get();
        
        if($searchValue){
            $karyawanNotAbsen = $karyawanNotAbsen->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');//->get();
            $karyawanAbsen = $karyawanAbsen->where('nama_lengkap', 'like', '%' . $searchValue . '%')
                ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%');//->get();

        }

        $karyawanNotAbsen = $karyawanNotAbsen->get();
        $karyawanAbsen = $karyawanAbsen->get();

        if(count($karyawanNotAbsen) > 0){
            foreach($karyawanNotAbsen AS $k => $v){
                $jmlTelats = !empty($jmlTelat[$v->id]) ? $jmlTelat[$v->id] : 0;
                $jmlMenits = !empty($jmlMenit[$v->id]) ? substr($jmlMenit[$v->id],0,8) : 0;
                $data1[] = array("nama_lengkap" => $v->nama_lengkap, "nik_pegawai" => $v->nik_pegawai, "jumlah_telat" => $jmlTelats, "jumlah_menit" => substr($jmlMenits,0,8));
            }

        }else{
            $data1 = [];
        }
        if(count($karyawanAbsen) > 0){
            foreach($karyawanAbsen AS $k => $v){
                $jmlTelats = !empty($jmlTelat[$v->id]) ? $jmlTelat[$v->id] : 0;
                $jmlMenits = !empty($jmlMenit[$v->id]) ? substr($jmlMenit[$v->id],0,8) : 0;
                $data2[] = array("nama_lengkap" => $v->nama_lengkap, "nik_pegawai" => $v->nik_pegawai, "jumlah_telat" => $jmlTelats, "jumlah_menit" => substr($jmlMenits,0,8));
            }

        }else{
            $data2 = [];
        }
        $data = array_merge($data2,$data1);

        return response()->json($data);
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

    // public function listAbsensi(Request $request)
    // {
    //     if ( $request->input('tanggal') ) {
    //         // $a =  DB::table('absensi')
    //         //         ->select(DB::raw('DATE(tanggal) AS tanggal'), 'name', DB::raw("IF(HOUR(tanggal) < '12:00:00', 'MASUK', 'KELUAR') AS status_absen"),'id_karyawan', DB::raw('MIN(DATE_FORMAT(tanggal, "%H:%i")) AS jam'), DB::raw('MAX(DATE_FORMAT(tanggal, "%H:%i")) AS keluar'))
    //         //         ->join('users', 'users.id', '=', 'id_karyawan')
    //         //         ->where(DB::raw('DATE(tanggal)'), $request->tanggal)
    //         //         ->groupBy('id_karyawan')
    //         //         ->get();

    //         $a =   DB::table('view_absensi')->whereIn('tanggal', $request->tanggal)->where('nama_lengkap', '<>', 'agus')->get();

    //         return $a;
    // 	}
    //     if ( $request->input('client') ) {
    //         $a = DB::table('view_absensi')->where('tanggal', date('Y-m-d'))->where('nama_lengkap', '<>', 'agus')
    //                 ->get();
    //         return $a;
    // 	}

    //     $columns = ['absensi.id', 'tanggal', 'name'];

    //     $length = $request->input('length');
    //     $column = $request->input('column'); //Index
    //     $dir = $request->input('dir');
    //     $searchValue = $request->input('search');

    //     $query = DB::table('view_absensi')->where('tanggal', date('Y-m-d'))->where('nama_lengkap', '<>', 'agus')->get();

    //     if ($searchValue) {
    //         $query->where(function($query) use ($searchValue) {
    //             $query->where('name', 'like', '%' . $searchValue . '%')
    //             ->orWhere('name', 'like', '%' . $searchValue . '%');
    //         });
    //     }

    //     $projects = $query->paginate($length);
    //     return ['data' => $projects, 'draw' => $request->input('draw')];
    // }

    public function exportExcel(Request $request)
    {
            $tgl = is_array($request->input('tanggal'));
            $karyawan = User::where('users.id', '<>', '5')->orderBy('nama_lengkap', 'ASC')->get();

            $absen = DB::table('view_absensi')->select(DB::raw('COUNT(masuk) AS kehadiran'), 'users.nik_pegawai AS no_ktp', 'users.id AS id_user','view_absensi.nama_lengkap', 'masuk', 'keluar', 'tanggal', DB::raw('DATE_FORMAT(tanggal, "%Y-%c-%e") as tgl'))
            ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
            ->where('users.id', '<>', '5')
            ->groupBy('users.id');
            
            $status = DB::table('absen_tambahan')
            ->select(DB::raw('COUNT(id_karyawan) AS jml'), 'id_karyawan', 'status')
            ->groupBy('status','id_karyawan');

            if($tgl == TRUE){
                $absen->whereBetween('tanggal', $request->input('tanggal'));
                $status->whereBetween(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }else{
                $absen->where('tanggal', $request->input('tanggal'));
                $status->where(DB::raw('DATE(tanggal)'), $request->input('tanggal'));
            }


            // if ($searchValue) {
            //     $absen->where(function($absen) use ($searchValue) {
            //         $absen->where('view_absensi.nama_lengkap', 'like', '%' . $searchValue . '%')
            //         ->orWhere('users.nik_pegawai', 'like', '%' . $searchValue . '%');
            //     });

            //     $karyawan = User::where('nama_lengkap', 'like', '%' . $searchValue . '%')
            //         ->orWhere('nik_pegawai', 'like', '%' . $searchValue . '%')->get();

            // }

            $absen = $absen->pluck('kehadiran', 'id_user')->toArray();
            $status = $status->get();
            $data = [];

            if(count($status) > 0){
                foreach($status AS $k => $v){
                    $statusMasuk[$v->id_karyawan][$v->status] = $v->jml;  
                }
            }else{
                    $statusMasuk[][] = '';
            }

            foreach($karyawan AS $k => $v){
                $ijin = !empty($statusMasuk[$v->id]['I']) ? $statusMasuk[$v->id]['I'] : 0;
                $sakit = !empty($statusMasuk[$v->id]['S']) ? $statusMasuk[$v->id]['S'] : 0;
                $alpha = !empty($statusMasuk[$v->id]['A']) ? $statusMasuk[$v->id]['A'] : 0;
                $cuti = !empty($statusMasuk[$v->id]['C']) ? $statusMasuk[$v->id]['C'] : 0;
                $jml_absen = !empty($absen[$v->id]) ? $absen[$v->id] : 0;
                $data[] = array("nama_lengkap" => $v->nama_lengkap, "nik_pegawai" => $v->nik_pegawai, "sakit" => $sakit, "ijin" => $ijin, "alasan" => $alpha, "cuti" => $cuti, "kehadiran" => $jml_absen);
            }


        return response()->json($data);
    }

    public function laporanKehadiran(Request $request){
        if ( $request->input('tanggal') ) {
            $a =  DB::table('view_absensi')
                    ->select('view_absensi.*', 'users.nik_pegawai AS no_ktp')
                    ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                    ->whereBetween('tanggal', $request->input('tanggal'))
                    ->where('users.id', '<>', '5')
                    ->orderBy('tanggal', 'ASC')
                    ->orderBy('nama_lengkap', 'ASC')
                    ->get();
            return $a;
    	}
        if ( $request->input('client') ) {
            $a = DB::table('view_absensi')
                    ->select('view_absensi.*', 'users.nik_pegawai AS no_ktp')
                    ->join('users', 'users.nama_lengkap', '=', 'view_absensi.nama_lengkap')
                    ->where('tanggal', date('Y-m-d'))
                    ->where('users.id', '<>', '5')
                    ->orderBy('tanggal', 'ASC')
                    ->orderBy('nama_lengkap', 'ASC')
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
