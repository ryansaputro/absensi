<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = ['id', 'id_gate', 'id_karyawan', 'tanggal', 'created_at', 'updated_at'];
}
