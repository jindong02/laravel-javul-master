<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','donated_by_user_id','unit_id','objective_id','task_id','issue_id','amount','transaction_type','payment_method','payment_id','status','fund_type'];


    /**
     * Function will return total donated fund to unit
     * @param $unit_id
     * @return int
     */
    public static function getUnitDonatedFund($unit_id=''){
        if(!empty($unit_id)){
            return self::where('unit_id','=',$unit_id)->where('transaction_type','=','donated')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total donated fund to objective
     * @param $objective_id
     * @return int
     */
    public static function getObjectiveDonatedFund($objective_id=''){
        if(!empty($objective_id)){
            return self::where('objective_id','=',$objective_id)->where('transaction_type','=','donated')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total donated funds to task.
     * @param $task_id
     * @return int
     */

    public static function getTaskDonatedFund($task_id=''){
        if(!empty($task_id)){
            return self::where('task_id','=',$task_id)->where('transaction_type','=','donated')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total donated funds to issue.
     * @param $issue_id
     * @return int
     */

    public static function getIssueDonatedFund($issue_id=''){
        if(!empty($issue_id)){
            return self::where('issue_id','=',$issue_id)->where('transaction_type','=','donated')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total awarded fund to unit
     * @param $unit_id
     * @return int
     */
    public static function getUnitAwardedFund($unit_id=''){
        if(!empty($unit_id)){
            return self::where('unit_id','=',$unit_id)->where('transaction_type','=','awarded')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total awarded fund to objective
     * @param $objective_id
     * @return int
     */
    public static function getObjectiveAwardedFund($objective_id=''){
        if(!empty($objective_id)){
            return self::where('objective_id','=',$objective_id)->where('transaction_type','=','awarded')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total awarded funds to task.
     * @param $task_id
     * @return int
     */

    public static function getTaskAwardedFund($task_id=''){
        if(!empty($task_id)){
            return self::where('task_id','=',$task_id)->where('transaction_type','=','awarded')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    /**
     * Function will return total awarded funds to task.
     * @param $issue_id
     * @return int
     */

    public static function getIssueAwardedFund($issue_id=''){
        if(!empty($issue_id)){
            return self::where('issue_id','=',$issue_id)->where('transaction_type','=','awarded')->where('status','approved')->sum('amount');
        }
        return 0;
    }


    public static function getUserDonatedFund($user_id=''){
        if(!empty($user_id)){
            if(env('PAYMENT_METHOD') == "Zcash")
                return Transaction::where('user_id','=',$user_id)->where('trans_type','=','credit_zcash')->where('status','approved')->sum('amount');
            else
                return Transaction::where('user_id','=',$user_id)->where('trans_type','=','credit')->where('status','approved')->sum('amount');
        }
        return 0;
    }

    public static function getUserAwardedFund($user_id=''){
        if(!empty($user_id)){
            if(env('PAYMENT_METHOD') == "Zcash")
                return Transaction::where('user_id','=',$user_id)->where('trans_type','=','debit_zcash')->where('status','=','withdrawal')->sum('amount');
            else
                return Transaction::where('user_id','=',$user_id)->where('trans_type','=','debit')->where('status','approved')->orWhere('status', 'completed')->sum('amount');
        }
        return 0;
    }

    public static function transferFromUnit($obj, $amount)
    {
        $unit = $obj->unit;

        $availableFunds = Fund::getUnitDonatedFund($unit->id);

        if($availableFunds >= $amount) {
            Fund::create([
                'user_id' => \Auth::user()->id,
                'unit_id' => $unit->id,
                'amount' => $amount * -1,
                'transaction_type' => 'donated',
                'status' => 'approved',
                'fund_type' => 'unit'
            ]);

            Fund::create([
                'user_id' => \Auth::user()->id,
                'objective_id' => $obj->id,
                'amount' => $amount,
                'transaction_type' => 'donated',
                'status' => 'approved',
                'fund_type' => 'objective'
            ]);
            return ['success' => true];
        } else {
            return ['error' => 'Unit has no that amount of funds available'];
        }
    }

}
