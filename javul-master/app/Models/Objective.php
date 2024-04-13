<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hashids\Hashids;

class Objective extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','unit_id','name','description','status','parent_id','modified_by','slug'];

    /**
     * Get Parent Unit of Objective.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(){
        return $this->belongsTo('App\Unit');
    }

    public function watchlist(){
        return $this->belongsTo('App\Watchlist');
    }
    /**
     * Get Tasks of Objective..
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(){
        return $this->hasMany('App\Task');
    }

    /**
     * Get Issues of Objective...
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function issues(){
        return $this->hasManyThrough('App\Issue','App\Task');
    }

    public static function getObjectivesWithUnits($data = []){
        $objectives = [];
        if(empty($data)){
            $objectives = Objective::join('units','objectives.unit_id','=','units.id')->join('users','objectives.user_id','=',
                'users.id')->get();
        }
        return $objectives ;
    }

    /**
     * function will check whether unit_id is exist in unit table or not
     * @param $objective_id
     * @param bool $needToDecode
     * @return bool
     */
    public static function checkObjectiveExist($objective_id,$needToDecode=false)
    {
        if($needToDecode){
            $objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
            $objective_id = $objectiveIDHashID->decode($objective_id );

            if(empty($objective_id))
                return false;
            $objective_id = $objective_id[0];

            if(self::where('id',$objective_id)->count() == 0)
                return false;
            return true;
        }
        else{
            $cnt = self::where('id',$objective_id)->count();
            if($cnt == 0)
                return false;
            return true;
        }

    }

    public static function getObjectiveName($obj_id){
        if(self::where('id',$obj_id)->count())
            return self::find($obj_id)->name;
    }
    public static function getSlug($obj_id){
        if(self::where('id',$obj_id)->count())
            return self::find($obj_id)->slug;
    }

    public static function getObj($objective_id){
        $objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
        $objective_id = $objectiveIDHashID->decode($objective_id );

        if(empty($objective_id))
            return [];
        $objective_id = $objective_id[0];

        if(self::find($objective_id)->count() > 0)
            return self::find($objective_id);
        return [];
    }

    public static function objectiveStatus()
    {
        return [
            'in-progress'=>'In-Progress',
            'completed'=>'Completed',
            'archived'=>'Archived',
            'new'=>'New'
        ];
    }
}
