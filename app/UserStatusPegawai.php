<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatusPegawai extends Model
{
    protected $table = 'users_status_pegawai';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    public $fillable = ['id', 'id_karyawan', 'tgl_masuk', 'tgl_habis_kontrak', 'status_pegawai', 'masa_kerja', 'id_cabang'];
}
