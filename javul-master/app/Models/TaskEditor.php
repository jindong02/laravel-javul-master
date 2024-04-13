<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskEditor extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_editors';

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['task_id','task_history_id','user_id','submit_for_approval','first_user_to_submit'];

    public function task(){
        return $this->belongsTo('App\Task');
    }

    public function task_history(){
        return $this->hasMany('App\TaskHistory');
    }

}
