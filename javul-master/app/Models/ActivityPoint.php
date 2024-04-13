<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPoint extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','unit_id','objective_id','task_id','issue_id','points','comments','type'];
}
