<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskHistory extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_history';

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['unit_id','objective_id','name','description','skills','compensation',
        'estimated_completion_time_start','estimated_completion_time_end','task_action','task_documents','summary','updatedFields'];


    public function task_editors(){
        return $this->belongsTo('App\TaskEditor');
    }

}
