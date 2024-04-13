<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Hashids\Hashids;

class Task extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','unit_id','objective_id','name','description','skills','compensation',
                           'assign_to','status','estimated_completion_time_start','estimated_completion_time_end',
                           'modified_by','task_action','summary','slug'];


    /**
     * get documents of tasks.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function task_documents(){
        return $this->hasMany('App\TaskDocuments');
    }
    /**
     * Get Parent Objective of Tasks..
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objective(){
        return  $this->belongsTo('App\Objective');
    }

    public function watchlist(){
        return $this->belongsTo('App\Watchlist');
    }

    /**
     * Get Issues of Task..
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(){
        return $this->hasMany('App\Issue');
    }

    public function task_editors(){
        return $this->hasMany('App\TaskEditor');
    }

    public function task_complete(){
        return $this->hasMany('App\TaskComplete');
    }
    /**
     * return tasks of specified conditions
     * @param string $task_type
     * @param string $objective_id
     * @return mixed
     */
    public static function getTasks($task_type='',$objective_id=''){
        $tasksObj = self::select(['tasks.*']);
        if(!empty($task_type))
            $tasksObj = $tasksObj->where('status',$task_type);

        if(!empty($objective_id))
            $tasksObj = $tasksObj->where('objective_id',$objective_id);

        $tasksObj = $tasksObj->get();

        return $tasksObj;
    }

    /**
     * return count of tasks of specified condition.
     * @param string $task_type
     * @param string $objective_id
     * @return int
     */
    public static function getTaskCount($task_type='',$objective_id=''){
        $tasksObj = self::select(['tasks.*']);
        if(!empty($task_type))
            $tasksObj = $tasksObj->where('status',$task_type);

        if(!empty($objective_id))
            $tasksObj = $tasksObj->where('objective_id',$objective_id);

        $tasksObj = $tasksObj->get();

        return count($tasksObj);
    }

    public static function checkUnitAdmin($unit_id)
    {
        return Unit::find($unit_id)->user_id;
    }

    /**
     * function will delete task with it's associate documents and task actions
     * @param $task_id
     */
    public static function deleteTask($task_id)
    {
        // delete all document attached to task
        TaskDocuments::where('task_id',$task_id)->delete();

        // delete all action points attached to task
        TaskAction::where('task_id',$task_id)->delete();

        // delete Task
        self::find($task_id)->delete();
    }


    /**
     * function will check the loggedin user is creator of task or not.
     * @param $task_id
     * @return bool
     */
    public static function isTaskCreator($task_id){
        $user = Auth::user();
        if(empty($user ))
            return false;

        $taskObj = self::find($task_id);
        if(!empty($taskObj)){
            if($taskObj->user_id == Auth::user()->id)
                return true;
        }
        return false;
    }

    /**
     * function will check whether loggedin user is creator of task's unit or not
     * @param $task_id
     * @return bool
     */
    public static function isUnitAdminOfTask($task_id){
        $user = Auth::user();
        if(empty($user ))
            return false;
        $taskObj = self::find($task_id);
        if(!empty($taskObj)){
            $unitObj = Unit::where('id',$taskObj->unit_id)->where('units.user_id','=',Auth::user()->id)->count();
            if($unitObj  > 0)
                return true;
        }
        return false;
    }

    public static function checkUnitExist($task_id,$needToDecode=false)
    {
        if($needToDecode){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);

            if(empty($task_id))
                return false;
            $task_id = $task_id[0];

            if(self::find($task_id)->count() == 0)
                return false;
            return true;
        }

    }

    public static function getObj($task_id){
        $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
        $task_id = $taskIDHashID->decode($task_id );

        if(empty($task_id))
            return [];
        $task_id = $task_id[0];

        if(self::find($task_id)->count() > 0)
            return self::find($task_id);
        return [];
    }

    public static function getName($task_id){
        $task = self::find($task_id);
        if(!empty($task) && $task->count() > 0)
            return $task->name;
        return null;
    }

    public static function getSlug($task_id){
        $task = self::find($task_id);
        if(!empty($task) && $task->count() > 0)
            return $task->slug;
        return null;
    }
}
