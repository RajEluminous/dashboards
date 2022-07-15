<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerMaster extends Model
{
    protected $table = 'partner_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
