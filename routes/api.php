<?php

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

    Route::post('/absen','HomeController@absen');
    Route::get('/cek-absen','HomeController@cekAbsen');
    Route::group(['middleware' => ['auth']], function() {
        // menambahkan route untuk person
        Route::get('/dashboard','HomeController@index');
        Route::post('/get-absen','HomeController@getAbsen');
        Route::get('/lacak','HomeController@lacak');
        Route::get('/pantau','HomeController@pantau');
        Route::get('/telat','HomeController@telat');
        Route::get('/laporan-terlambat','ReportController@laporanTerlambat');
        Route::get('/laporan-overtime','ReportController@laporanOvertime');
        Route::get('/laporan-semua','ReportController@laporanSemua');
        Route::get('/rekap-keterlambatan','ReportController@rekapKeterlambatan');
        Route::get('/export-excel','ReportController@exportExcel');
        Route::get('/rekap-export-excel','ReportController@rekapKeterlambatanExcel');
    });

    Route::get('/laporan-kehadiran','ReportController@laporanKehadiran');
    Route::middleware('permission:read-absensi')->group(function () {
        Route::get('/list-absensi','HomeController@listAbsensi');
        
    });

    // Route::middleware('permission:read-pengguna|create-pengguna|edit-pengguna|delete-pengguna')->group(function () {
        Route::prefix('pengguna')->group(function() {
            Route::get('/','PersonController@all');
            Route::get('/{id}','PersonController@show');
            Route::post('/create','PersonController@store');
            Route::put('/{id}','PersonController@update');
            Route::delete('/{id}','PersonController@delete');
        });
            Route::get('/provinsi','PersonController@provinsi');
            Route::get('/divisi','PersonController@divisi');
            Route::get('/kota','PersonController@kota');
            Route::get('/kecamatan','PersonController@kecamatan');
            Route::get('/kelurahan','PersonController@kelurahan');

    // });

    Route::get('data-kehadiran/get-data-nik','DataKehadiranController@getDataNik');
    // middleware('permission:read-user|create-user|edit-user|delete-user')->
    // Route::group(function () {
        Route::prefix('data-kehadiran')->group(function() {
            Route::get('/','DataKehadiranController@all');
            Route::get('/get-nik','DataKehadiranController@getNik');
            Route::get('/{id}','DataKehadiranController@show');
            Route::post('/create','DataKehadiranController@store');
            Route::put('/{id}','DataKehadiranController@update');
            Route::delete('/{id}','DataKehadiranController@delete');
        });

    // });
});

