<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use URL;
use App\User;
use App\AbsenTambahan;
use App\AbsensiSyncServer;
use File;
use App\Websocket\Client;


class AbsenSyncServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // protected $connection = 'secondary';
    // protected $connection = 'primary';

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function absenTap(Request $request)
    {
        // dd($request->all());
        // validate the data before processing
        $rules = [
            'tag' => 'required|string',
            'date' => 'required|date',
        ];

        $customMessages = [
            'required' => 'The :attribute field can not be blank.'
        ];

        $this->validate($request, $rules, $customMessages);

        $sCheckHost = 'www.google.com';
        $connection = (bool)@fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
        //declartion variable
        $txt = "";
        //make path to make folder
        $dir = public_path("/log/");
        if(!is_dir($dir)){$makeFolder = mkdir($dir , 0777, true);}
        // dd($request->all());
        DB::beginTransaction();
        try {

            date_default_timezone_set("Asia/Jakarta");
            //data save to local
            $id_user = User::where('id_epc_tag', $request->tag)->first();
            $cekAbsenOrNot = AbsenTambahan::where('id_karyawan', $id_user->id)->where(DB::raw('tanggal', date('Y-m-d')))->count();
            
            //cek jika melebihi waktu 100 detik dari data sebelumnya maka data dapat disimpan 
            $cek = DB::table('absensi')->select(DB::raw('DATE_ADD(tanggal, INTERVAL 100 SECOND) AS next'))->where('id_gate', $request->id_gate)
                    ->where('id_karyawan', $id_user->id)
                    ->where(DB::raw('DATE(tanggal)'), date('Y-m-d'))
                    ->orderBy('tanggal', 'DESC')
                    ->first();

            //jika data ada maka proses ini dijalankan
            if($cek !== null){

                //jika data yg terbaru waktunya lebih dari yg diinsert sebelumnya maka proses ini dijalankan
                if($request->date > $cek->next){

                    //cek jika dia ada cuti sakit ijin alpa atau luar kota
                    if($cekAbsenOrNot == 0) {

                        //proses simpan data di table absensi local
                        $create = DB::table('absensi')->insert([
                            'id_gate' => $request->id_gate,
                            'id_karyawan' => $id_user->id,
                            'tanggal' => $request->date,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        //message jika berhasil menambahkan record ke database
                        $message = "absen berhasil";
                    }else{
                        $message = "maaf anda sedang tidak berada di kantor";
                    }

                }else{
                    // logika jika data yg diinsert kan ke database dengan waktu yg sama maka akan meng-eksekusi message ini
                    $message = "data absensi anda sudah tercatat ke dalam sistem. harap berdiri agak jauh dari gerbang";
                }

            }else{

                 //cek jika dia ada cuti sakit ijin alpa atau luar kota
                if($cekAbsenOrNot == 0) {
                    
                    //jika data karyawan belum ada di table absensi maka menjalankan proses ini
                    $create = DB::table('absensi')->insert([
                            'id_gate' => $request->id_gate,
                            'id_karyawan' => $id_user->id,
                            'tanggal' => $request->date,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    //message jika berhasil menambahkan record ke database
                    $message = "absen berhasil";
                }else{
                    $message = "maaf anda sedang tidak berada di kantor";
                }
            }

            // websocket
            $client = new Client("ws://127.0.0.1:5000");

            // Convenience methods
            $client->text($request->tag.",".$request->id_gate.",". date("H:i", strtotime($request->date))); // Send an opcode=text message
            // $client->binary($binary_string); // Send an opcode=binary message
            $client->ping(); // Send an opcode=ping frame
            $client->pong(); // Send 

            // if there is connection do sync
            if($connection == true){
                // DB::connection('secondary')->table('divisi')->insert(['nama_divisi' => 'test']);
                $cek = AbsensiSyncServer::orderBy('id', 'DESC')->limit(5)->get();
                //get data from local data
                $db = DB::table('absensi')->select('*')->where('status_sync', 'not_yet')->limit(5)->get();
                if(count($db) > 0 ){
                    $txt .= "[".date('Y-m-d')."][ryansaputro52@gmail.com]\n";
                    
                    foreach($db AS $k => $v){ 
                        
                        //insert to table absensi on cloud server
                        $insert = AbsensiSyncServer::create([
                            'id_gate' => $v->id_gate,
                            'id_karyawan' => $v->id_karyawan,
                            'tanggal' => $v->tanggal,
                            ]);

                        if($insert){
                            //get data to save in log-absensi.text 
                            $txt .= "[".date('H:i:s')."] - ".$v->id.", ".$v->id_gate.", ".$v->id_karyawan.", ".$v->tanggal."\n";

                            //update db local change status sync after sync on cloud 
                            $update = DB::table('absensi')->where('id', $v->id)->update(['status_sync' => 'done', 'sync_at' => date('Y-m-d H:i:s')]);
                        }
                            
                    }

                    $data = $txt;
                    $filename = $dir.'log-absensi-'.date('Y-m-d').".txt";
    
                    if (file_exists($filename)) {
                        File::append($filename, $data);
                    }else{
                        File::put($filename, $data);
                    }
                }
            }
            
        } catch (\Illuminate\Database\QueryException $ex) {
            //throw $th;
            DB::rollback();
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage()], 500);
        }
        DB::commit();
        return response()->json(['status' => 'success', 'message' => $message,  'data' => $db], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
