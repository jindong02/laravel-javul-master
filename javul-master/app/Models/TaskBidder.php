<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBidder extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_bidders';

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['task_id','user_id','amount','comment','first_to_bid','status','charge_type'];

    public function task(){
        return $this->belongsTo('App\Task');
    }

    public static function checkBid($task_id){
        $user = \Auth::user();
        if(empty($user))
            return false;

        $cnt = self::where('task_id',$task_id)->where('user_id',\Auth::user()->id)->count();
        if($cnt > 0)
            return false;

        return true;
    }

    public static function getCountDown($task_id){
        $firstUserSubmitted = Task::join('task_bidders','tasks.id','=','task_bidders.task_id')
                            ->where('task_id',$task_id)->where('task_bidders.user_id','!=',\Auth::user()->id)
                            ->where('first_to_bid','yes')->where('tasks.status','!=','awaiting_assignment')
                            ->select(['task_bidders.*'])
                            ->first();

        $availableDays ='';
        if(count($firstUserSubmitted) > 0){
            $submittedDate = strtotime($firstUserSubmitted->created_at);
            $availableDays = time() - $submittedDate;
            $availableDays = 8 - (int)date('d',$availableDays );

        }
        return $availableDays;
    }
}
