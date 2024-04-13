<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Hashids\Hashids;

class TaskRatings extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_ratings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','task_id','quality_of_work','timeliness'];



}
