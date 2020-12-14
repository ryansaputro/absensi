<?php
/**
 * @author ryan saputro
 * @email ryansaputro52@gmail.com
 * @create date 2020-10-01 13:24:39
 * @modify date 2020-10-01 13:24:39
 * @desc handle url to request
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//auth
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('checkUser', 'HomeController@checkUser');

        // Below mention routes are public, user can access those without any restriction.
        // Create New User
        Route::post('register', 'AuthController@register');
        // Login User
        Route::post('login', 'AuthController@login');
        // Below mention routes are available only for the authenticated users.
        // // Refresh the JWT Token
        Route::get('refresh', 'AuthController@refresh');
        
        Route::middleware(['auth:api', 'jwt.verify'])->group(function () {
            Route::get('users', 'UserController@getAuthenticatedUser');
            // Get user info
            Route::get('user', 'AuthController@user');
            // Logout user from application
            Route::post('logout', 'AuthController@logout');

                    

        });

    });

    //utk update password pengguna aplikasi
    Route::post('/update-password','AuthController@updatePassword');

    //url utk display
    // Route::post('/absen','HomeController@absen');
    Route::post('/android-mapping-tag','PersonController@androidMappingTag');
    Route::get('/cek-absen','HomeController@cekAbsen');
    //sync
    Route::get('/sync', 'HomeController@sync'); 
    Route::post('/absen', 'AbsenSyncServerController@absenTap');


    Route::group(['middleware' => ['auth']], function() {

        //menu absensi, lacak, pantau
        Route::get('/dashboard','HomeController@index');
        Route::get('/lacak','HomeController@lacak');
        Route::get('/pantau','HomeController@pantau');
        Route::get('/telat','HomeController@telat');
        
        //post data dari alat (RFID READER/ANTENA)
        Route::post('/get-absen','HomeController@getAbsen');

        //utk report
        Route::get('/laporan-terlambat','ReportController@laporanTerlambat');
        Route::get('/laporan-overtime','ReportController@laporanOvertime');
        Route::get('/laporan-semua','ReportController@laporanSemua');
        Route::get('/laporan-kehadiran','ReportController@laporanKehadiran');
        Route::get('/rekap-keterlambatan','ReportController@rekapKeterlambatan');
        Route::get('/export-excel','ReportController@exportExcel');
        Route::get('/rekap-export-excel','ReportController@rekapKeterlambatanExcel');
    });

    Route::middleware('permission:read-absensi')->group(function () {
        Route::get('/list-absensi','HomeController@listAbsensi');
        
    });

    //master data > karyawan 
    Route::prefix('karyawan')->group(function() {
        Route::get('/','PersonController@all');
        Route::get('/{id}','PersonController@show');
        Route::post('/create','PersonController@store');
        Route::put('/{id}','PersonController@update');
        Route::delete('/{id}','PersonController@delete');
    });

        //data utk karyawan
        Route::get('/provinsi','PersonController@provinsi');
        Route::get('/get-divisi','PersonController@divisi');
        Route::get('/kota','PersonController@kota');
        Route::get('/kecamatan','PersonController@kecamatan');
        Route::get('/kelurahan','PersonController@kelurahan');
            
    
    //admin aplikasi > pengguna aplikasi 
    Route::prefix('user-login')->group(function() {
        Route::get('/','AdministratorAplikasiController@userLogin');
        Route::post('/create','AdministratorAplikasiController@userLoginCreate');
        Route::get('/{id}','AdministratorAplikasiController@userLoginShow');
        Route::put('/{id}','AdministratorAplikasiController@userLoginUpdate');
        Route::delete('/{id}','AdministratorAplikasiController@userLoginDelete');
    });

    //admin aplikasi > role 
    Route::prefix('role')->group(function() {
        Route::get('/','AdministratorAplikasiController@role');
        Route::get('/get-permissions','AdministratorAplikasiController@permission');
        Route::post('/create','AdministratorAplikasiController@roleCreate');
        Route::get('/{id}','AdministratorAplikasiController@roleShow');
        Route::put('/{id}','AdministratorAplikasiController@roleUpdate');
        Route::delete('/{id}','AdministratorAplikasiController@roleDelete');
    });

    //data kehadiran 
    Route::prefix('data-kehadiran')->group(function() {
        Route::get('/get-data-nik','DataKehadiranController@getDataNik');
        Route::get('/','DataKehadiranController@all');
        Route::get('/get-nik','DataKehadiranController@getNik');
        Route::get('/{id}','DataKehadiranController@show');
        Route::post('/create','DataKehadiranController@store');
        Route::put('/{id}','DataKehadiranController@update');
        Route::delete('/{id}','DataKehadiranController@delete');
    });

    //master data > lokasi
    Route::prefix('lokasi')->group(function() {
        Route::get('/','LokasiController@index');
        Route::post('/create','LokasiController@store');
        Route::get('/{id}','LokasiController@show');
        Route::put('/{id}','LokasiController@update');
        Route::delete('/{id}','LokasiController@delete');
    });

    //master data > kantor
    Route::prefix('kantor')->group(function() {
        Route::get('/','KantorController@index');
        Route::post('/create','KantorController@store');
        Route::get('/{id}','KantorController@show');
        Route::put('/{id}','KantorController@update');
        Route::delete('/{id}','KantorController@delete');
    });

    //master data > divisi
    Route::prefix('divisi')->group(function() {
        Route::get('/','DivisiController@index');
        Route::post('/create','DivisiController@store');
        Route::get('/{id}','DivisiController@show');
        Route::put('/{id}','DivisiController@update');
        Route::delete('/{id}','DivisiController@delete');
    });

    //master data > jabatan
    Route::prefix('jabatan')->group(function() {
        Route::get('/','JabatanController@index');
        Route::post('/create','JabatanController@store');
        Route::get('/{id}','JabatanController@show');
        Route::put('/{id}','JabatanController@update');
        Route::delete('/{id}','JabatanController@delete');
    });

});

