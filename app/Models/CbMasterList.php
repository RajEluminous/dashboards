<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbMasterList extends Model
{
    protected $table = 'cb_affiliate_partner_list';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
		'partner_id'
    ];
}

 