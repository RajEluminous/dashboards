<?php

namespace App;

use App\Models\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_role_id', 'status', 'created_at', 'updated_at'
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

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_SUSPENDED = 'SUSPENDED';

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }


    /**
     * Load Status
     *
     * @return array
     */
    public static function LoadStatus($blnSelect = false)
    {
        $arrStatus = [];

        if ($blnSelect)
            $arrStatus[''] = 'Please Select';

        $arrStatus += [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SUSPENDED => 'Suspended',
        ];

        return $arrStatus;
    }
}
