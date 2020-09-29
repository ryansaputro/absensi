<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Auth;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'nik_pegawai', 'nik_ktp', 'id_divisi', 'id_jabatan', 'nama_lengkap', 'gol_darah', 'no_telp', 'email', 'email_verified_at', 'password', 'id_epc_tag', 'foto', 'remember_token', 'created_at', 'updated_at' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getAllPermissionsAttribute() {
    $permissions = [];
        foreach (Permission::all() as $permission) {
        if (Auth::user()->can($permission->name)) {
            $permissions[] = $permission->name;
        }
        }
        return $permissions;
    }
    // public function role() {
    //     return $this->belongsTo(Role::class);
    // }

    // public function hasPermission($permission) {
    //     return $this->role->permissions()->where('name', $permission)->first() ?: false;
    // }
}
