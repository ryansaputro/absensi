<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class AbsenTambahan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'tanggal', 'id_karyawan', 'status', 'keterangan'
    ];
    protected $table = 'absen_tambahan';
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

}
