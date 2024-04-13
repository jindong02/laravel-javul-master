<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZcashWebhookData extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zcash_webhook_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['transaction_id','zcash_address','notification_status','notification_data','transaction_data'];


}
