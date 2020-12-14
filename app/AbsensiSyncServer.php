<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbsensiSyncServer extends Model
{
    protected $connection = 'secondary';
    protected $table = 'absensi';
    protected $fillable = ['id', 'id_gate', 'id_karyawan', 'tanggal', 'created_at', 'updated_at'];
}
