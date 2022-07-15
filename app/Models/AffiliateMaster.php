<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateMaster extends Model
{
    protected $table = 'affiliate_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
