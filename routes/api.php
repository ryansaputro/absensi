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
        // Below mention routes are public, user can access those without any restriction.
        // Create New User
        Route::post('register', 'AuthController@register');
        // Login User
        Route::post('login', 'AuthController@login');
        // Below mention routes are available only for the authenticated users.
        // // Refresh the JWT Token
        Route::get('refresh', 'AuthController@refresh');
        
        Route::middleware('auth:api')->group(function () {
            // Get user info
            Route::get('user', 'AuthController@user');
            // Logout user from application
            Route::post('logout', 'AuthController@logout');

                    

        });

    });

    Route::group(['middleware' => ['auth']], function() {
        // menambahkan route untuk person
        Route::get('/dashboard','HomeController@index');
        Route::post('/absen','HomeController@absen');
        Route::get('/list-absensi','HomeController@listAbsensi');
        Route::get('/lacak','HomeController@lacak');
        Route::get('/pantau','HomeController@pantau');
        Route::get('/telat','HomeController@telat');
        Route::get('/provinsi','PersonController@provinsi');
        Route::get('/divisi','PersonController@divisi');
        Route::get('/kota','PersonController@kota');
        Route::get('/kecamatan','PersonController@kecamatan');
        Route::get('/kelurahan','PersonController@kelurahan');
    
        Route::get('/laporan-terlambat','ReportController@laporanTerlambat');
    
    
        Route::prefix('pengguna')->group(function () {
            Route::get('/','PersonController@all');
            Route::get('/{id}','PersonController@show');
            Route::post('/create','PersonController@store');
            Route::put('/{id}','PersonController@update');
            Route::delete('/{id}','PersonController@delete');
        });

    });
});

