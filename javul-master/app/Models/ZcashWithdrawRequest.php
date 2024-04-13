<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZcashWithdrawRequest extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zcash_withdraw_request';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','user_transaction_id','transfer_transaction_id','amount','zcash_address','status','transaction_data'];


}
