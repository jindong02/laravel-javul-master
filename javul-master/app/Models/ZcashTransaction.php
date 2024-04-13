<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZcashTransaction extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zcash_transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_id','user_transaction_id','transaction_id','amount','zcash_address','status','qr_code'];


}
