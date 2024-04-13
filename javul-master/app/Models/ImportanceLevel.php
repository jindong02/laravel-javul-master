<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportanceLevel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'importance_level';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','objective_id','issue_id','importance_level','importance_upvote','importance_downvote','type'];

    public static function checkImportanceLevel($id,$whereField)
    {
        if(!empty($id))
        {
            $obj = self::where($whereField,$id)->where('user_id',\Auth::user()->id)->first();
            if(!empty($obj)){
                if(empty($obj->importance_level))
                    return null;
                else
                    return $obj->importance_level;
            }
        }
        return null;
    }
}
