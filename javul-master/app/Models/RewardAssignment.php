<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardAssignment extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_reward_assignment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['task_id','user_id','reward_percentage'];

    /**
     * Get Parent Objective of Tasks..
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tasks(){
        return  $this->belongsTo('App\Task');
    }

    public function users(){
        return  $this->hasMany('App\User','id','user_id');
    }
}
