<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAlamat extends Model
{
    protected $table = 'users_alamat';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    public $fillable = ['id', 'id_karyawan', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'alamat'];
}
