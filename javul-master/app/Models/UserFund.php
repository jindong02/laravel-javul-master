<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFund extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['create_by','user_from_id','user_to_id','amount','type'];
}
