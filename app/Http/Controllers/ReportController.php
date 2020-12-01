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
        //default dgn terlmabat plg banyak
        if ( $request->input('client')){

            // $date = date('Y-m-d');
            $date = "2020-11-24";

            //get keterlamabatan brp kali
            $keterlambatan = DB::table('view_absensi')->select('masuk', 'id_karyawan', DB::raw('COUNT(id_karyawan) AS terlambat'))->where('tanggal', $date)->where('masuk', '>', '08:05')->groupBy('id_karyawan')->pluck('terlambat', 'id_karyawan')->toArray();
            
            //get jumlah menit terlambat dalam menit
            $totalterlambat = DB::table('view_absensi')->select('view_absensi.*', DB::raw("SUBSTRING_INDEX(TIME_TO_SEC(DATE_FORMAT(TIMEDIFF(masuk, '08:05'), '%H:%i:%s'))/60, '.',1) AS selisih"))->where('tanggal', $date)->orderBy('selisih', 'ASC')->where('masuk', '>', '08:05')->pluck('selisih', 'id_karyawan')->toArray();
            
            //kehadiran
            $kehadiran = DB::table('view_absensi')->select(DB::raw('COUNT(id_karyawan) AS hadir'), 'id_karyawan')->where('tanggal', $date)->groupBy('id_karyawan')->pluck('hadir', 'id_karyawan')->toArray();
            
            $karyawanNik = DB::table('users')->where('id', '<>', 5)->pluck('nik_pegawai', 'id')->toArray();
            $karyawanNama = DB::table('users')->where('id', '<>', 5)->pluck('nama_lengkap', 'id')->toArray();
            
            //get list all karyawan tepat
            $karyawanI = DB::table('users')->where('id', '<>', 5)->whereNotIn('id', array_keys($keterlambatan))->orderBy('nama_lengkap', 'ASC')->get();

            // sort array DESC base on value of array
            arsort($totalterlambat);
            foreach($totalterlambat AS $k => $v){
                $data[] = (array('total_terlambat' => $v, 'keterlambatan' => !empty($keterlambatan[$k]) ? $keterlambatan[$k] : 0,"nik_pegawai" => $karyawanNik[$k], "nama_lengkap" => $karyawanNama[$k], "kehadiran" => !empty($kehadiran[$k]) ? $kehadiran[$k] : 0));
            }

            foreach($karyawanI AS $k => $v){
                $data[] = (array('keterlambatan' => !empty($keterlambatan[$v->id]) ?  $keterlambatan[$v->id] : 0, 'total_terlambat' => !empty($totalterlambat[$v->id]) ? $totalterlambat[$v->id] : 0,"nik_pegawai" => $v->nik_pegawai, "nama_lengkap" => $v->nama_lengkap, "kehadiran" => !empty($kehadiran[$v->id]) ? $kehadiran[$v->id] : 0));
            }
    
            return ["data" => $data];
        }

        if ( $request->input('filterby')){
            // $date = date('Y-m-d');
            $date = is_array($request->tanggal) ? $request->tanggal : array($request->tanggal, $request->tanggal) ;

            //get keterlamabatan brp kali
            $keterlambatan = DB::table('view_absensi')->select('masuk', 'id_karyawan', DB::raw('COUNT(id_karyawan) AS terlambat'))->whereBetween('tanggal', $date)->where('masuk', '>', '08:05')->groupBy('id_karyawan')->pluck('terlambat', 'id_karyawan')->toArray();
            
            //get jumlah menit terlambat dalam menit
            $totalterlambat = DB::table('view_absensi')->select('view_absensi.*', DB::raw("SUBSTRING_INDEX(TIME_TO_SEC(DATE_FORMAT(TIMEDIFF(masuk, '08:05'), '%H:%i:%s'))/60, '.',1) AS selisih"))->whereBetween('tanggal', $date)->orderBy('selisih', 'ASC')->where('masuk', '>', '08:05')->pluck('selisih', 'id_karyawan')->toArray();
            
            //kehadiran
            $kehadiran = DB::table('view_absensi')->select(DB::raw('COUNT(id_karyawan) AS hadir'), 'id_karyawan')->whereBetween('tanggal', $date)->groupBy('id_karyawan')->pluck('hadir', 'id_karyawan')->toArray();
            
            $karyawanNik = DB::table('users')->where('id', '<>', 5)->pluck('nik_pegawai', 'id')->toArray();
            $karyawanNama = DB::table('users')->where('id', '<>', 5)->pluck('nama_lengkap', 'id')->toArray();

            //get list all karyawan tepat
            $karyawanI = DB::table('users')->where('id', '<>', 5)->whereNotIn('id', array_keys($keterlambatan))->orderBy('nama_lengkap', 'ASC')->get();

        }
        
        // sort array DESC base on value of array
        if($request->input('filterby') == 'terlambat_paling_banyak'){
            arsort($keterlambatan);
            foreach($keterlambatan AS $k => $v){
                $data[] = (array('keterlambatan' => $v, 'total_terlambat' => !empty($totalterlambat[$k]) ? $totalterlambat[$k] : 0,"nik_pegawai" => $karyawanNik[$k], "nama_lengkap" => $karyawanNama[$k], "kehadiran" => !empty($kehadiran[$k]) ? $kehadiran[$k] : 0));
            }

            foreach($karyawanI AS $k => $v){
                $data[] = (array('keterlambatan' => !empty($keterlambatan[$v->id]) ?  $keterlambatan[$v->id] : 0, 'total_terlambat' => !empty($totalterlambat[$v->id]) ? $totalterlambat[$v->id] : 0,"nik_pegawai" => $v->nik_pegawai, "nama_lengkap" => $v->nama_lengkap, "kehadiran" => !empty($kehadiran[$v->id]) ? $kehadiran[$v->id] : 0));
            }
            
        }else  if($request->input('filterby') == 'terlambat_paling_sedikit'){
            krsort($keterlambatan);
            foreach($karyawanI AS $k => $v){
                $data[] = (array('keterlambatan' => !empty($keterlambatan[$v->id]) ?  $keterlambatan[$v->id] : 0, 'total_terlambat' => !empty($totalterlambat[$v->id]) ? $totalterlambat[$v->id] : 0,"nik_pegawai" => $v->nik_pegawai, "nama_lengkap" => $v->nama_lengkap, "kehadiran" => !empty($kehadiran[$v->id]) ? $kehadiran[$v->id] : 0));
            }

            foreach($keterlambatan AS $k => $v){
                $data[] = (array('keterlambatan' => $v, 'total_terlambat' => !empty($totalterlambat[$k]) ? $totalterlambat[$k] : 0,"nik_pegawai" => $karyawanNik[$k], "nama_lengkap" => $karyawanNama[$k], "kehadiran" => !empty($kehadiran[$k]) ? $kehadiran[$k] : 0));
            }


        }else  if($request->input('filterby') == 'total_terlambat_paling_banyak'){
            arsort($totalterlambat);
            foreach($totalterlambat AS $k => $v){
                $data[] = (array('total_terlambat' => $v, 'keterlambatan' => !empty($keterlambatan[$k]) ? $keterlambatan[$k] : 0,"nik_pegawai" => $karyawanNik[$k], "nama_lengkap" => $karyawanNama[$k], "kehadiran" => !empty($kehadiran[$k]) ? $kehadiran[$k] : 0));
            }

            foreach($karyawanI AS $k => $v){
                $data[] = (array('keterlambatan' => !empty($keterlambatan[$v->id]) ?  $keterlambatan[$v->id] : 0, 'total_terlambat' => !empty($totalterlambat[$v->id]) ? $totalterlambat[$v->id] : 0,"nik_pegawai" => $v->nik_pegawai, "nama_lengkap" => $v->nama_lengkap, "kehadiran" => !empty($kehadiran[$v->id]) ? $kehadiran[$v->id] : 0));
            }

        }else  if($request->input('filterby') == 'total_terlambat_paling_sedikit'){
            krsort($totalterlambat);
            foreach($karyawanI AS $k => $v){
                $data[] = (array('keterlambatan' => !empty($keterlambatan[$v->id]) ?  $keterlambatan[$v->id] : 0, 'total_terlambat' => !empty($totalterlambat[$v->id]) ? $totalterlambat[$v->id] : 0,"nik_pegawai" => $v->nik_pegawai, "nama_lengkap" => $v->nama_lengkap, "kehadiran" => !empty($kehadiran[$v->id]) ? $kehadiran[$v->id] : 0));
            }
            foreach($totalterlambat AS $k => $v){
                $data[] = (array('total_terlambat' => $v, 'keterlambatan' => !empty($keterlambatan[$k]) ? $keterlambatan[$k] : 0,"nik_pegawai" => $karyawanNik[$k], "nama_lengkap" => $karyawanNama[$k], "kehadiran" => !empty($kehadiran[$k]) ? $kehadiran[$k] : 0));
            }

        }
        return ["filter" => $request->input('filterby'), "data" => $data];
         
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
                $lk = !empty($statusMasuk[$v->id]['LK']) ? $statusMasuk[$v->id]['LK'] : 0;
                $jml_absen = !empty($absen[$v->id]) ? $absen[$v->id] : 0;
                $data[] = array("nama_lengkap" => $v->nama_lengkap, "nik_pegawai" => $v->nik_pegawai, "sakit" => $sakit, "ijin" => $ijin, "alasan" => $alpha, "cuti" => $cuti, "lk" => $lk, "kehadiran" => $jml_absen);
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
