<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
	 /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_alert_notification';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','content','message_read'];

}
