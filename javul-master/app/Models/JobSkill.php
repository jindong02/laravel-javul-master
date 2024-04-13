<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hashids\Hashids;

class JobSkill extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = true;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_skills';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['skill_name','parent_id','status'];

    public static function getSkillNameLink($ids = ''){
        if(empty($ids))
            return '';

        $ids= explode(",",$ids);
        $html = '';
        if(count($ids) > 0){
            $jobSkillIDHashID = new Hashids('job skills id hash',10,\Config::get('app.encode_chars'));


            $i=0;
            foreach($ids as $id)
            {
                $skillObj =self::find($id);
                if($skillObj->count() > 0 )
                    $html.='<a href="'.url('job_skills/'.$jobSkillIDHashID->encode($skillObj->id )).'">'.$skillObj->skill_name.'</a>';

                if(count($ids) - 1 > $i)
                    $html.=", ";
                $i++;

            }
        }
        return $html;

    }

    public static function getSKillWithComma($ids){
        $obj = self::whereIn('id',explode(",",$ids))->pluck('skill_name')->all();

        if(!empty($obj) && count($obj) > 0)
            return $obj;
        return [];
    }
    public static function getName($id){
        $obj = self::find($id);
        if(!empty($obj))
            return $obj->skill_name;
    }

    public static function getHierarchy($value,$page=0){
        if($page == 0)
            $obj = self::where('skill_name','like',$value.'%')->get();
        else {
            $offset = ($page - 1) * 10;
            $obj = self::where('skill_name', 'like', $value . '%')->skip($offset)->take(10)->get();
        }

        $names = [];
        if(!empty($obj) && count($obj) > 0){
            foreach($obj as $skill){
               if(!empty($skill->parent_id)){

                    $str = self::getParent($skill->parent_id,$skill->skill_name);
               }
               else{
                    $str = $skill->skill_name;
               }
                $names[]=['id'=>$skill->id,'name'=>$str];
            }

        }
        return $names;
    }

    public static  $titles=[];
    public static function getParent($parent_id,$name){
        $obj = self::find($parent_id);
        global $titles;
        $titles[]=$name;
        if(!empty($obj) && !empty($obj->parent_id)) {
            //$titles[] =$obj->skill_name;
            return self::getParent($obj->parent_id,$obj->skill_name);
        }
        else {
            $titles[] = $obj->skill_name;
        }
        $tempTitles = $titles;
        $titles = [];
        return $tempTitles ;
    }

    public static function getSkillForBrowse($page,$id,$type){

        if (strpos($id, 'JBSH') !== false) {
            if($type == "old") {
                $job_skill_history_id = str_replace("JBSH","",$id);
                $id = JobSkillHistory::where('prefix_id', $id)->first()->job_skill_id;
            }
            else
                $id = JobSkillHistory::where('prefix_id',$id)->first()->id;
        }

        if($type == "new"){
            $dataObj=JobSkillHistory::where('parent_id',$id)->where('parent_id_belongs_to','new')->get();
        }
        else {

            $where=" AND user_id=".\Auth::user()->id;
            if(!empty($job_skill_history_id))
                $where_last = "( parent_id =".$id." OR parent_id=".$job_skill_history_id.")";
            else
                $where_last = " parent_id =".$id;


            if($page == "site_admin"){
                $dataObj = \DB::select('SELECT
                                      job_skills.id AS id,
                                      NULL AS history_id,
                                      NULL AS job_id,
                                      job_skills.skill_name AS skill_name,
                                      NULL AS history_skill_name,
                                      job_skills.parent_id AS parent_id,
                                      NULL AS history_parent_id,
                                      NULL AS action_type
                                    FROM
                                      `job_skills` , job_skills_history
                                    WHERE job_skills.parent_id = ' . $id . ' AND job_skills.id !=  job_skills_history.job_skill_id
                                    UNION ALL
                                    SELECT
                                      job_skills.id AS id,
                                      history.history_id AS history_id,
                                      history.job_id AS job_id,
                                      job_skills.skill_name AS skill_name,
                                      history.history_skill_name AS history_skill_name,
                                      job_skills.parent_id AS parent_id,
                                      history.history_parent_id AS history_parent_id,
                                      history.action_type AS action_type
                                    FROM
                                      `job_skills`
                                      LEFT JOIN
                                        (SELECT
                                          job_skills_history.id AS history_id,
                                          job_skill_id AS job_id,user_id,
                                          job_skills_history.`skill_name` AS history_skill_name,
                                          job_skills_history.parent_id AS history_parent_id,
                                          action_type FROM job_skills_history WHERE (parent_id_belongs_to="old" OR parent_id_belongs_to IS
                                           NULL)'.$where.') history
                                          ON job_skills.id = history.`job_id`
                                        WHERE job_skills.parent_id = '.$id.'
                                    UNION
                                    ALL
                                    SELECT
                                      prefix_id as id,
                                      prefix_id as history_id,
                                      job_skill_id AS job_id,
                                      skill_name,
                                      skill_name AS history_skill_name,
                                      parent_id,
                                      parent_id AS history_parent_id,
                                      action_type
                                    FROM
                                      job_skills_history
                                    WHERE ' . $where_last . $where.' AND parent_id_belongs_to="old" AND action_type != "delete" order by id');
            }
            else
                $dataObj = \DB::select('SELECT
                                      job_skills.id AS id,
                                      NULL AS history_id,
                                      NULL AS job_id,
                                      job_skills.skill_name AS skill_name,
                                      NULL AS history_skill_name,
                                      job_skills.parent_id AS parent_id,
                                      NULL AS history_parent_id,
                                      NULL AS action_type
                                    FROM
                                      `job_skills`
                                    WHERE job_skills.parent_id = ' . $id . ' order by id');
        }
        return $dataObj;


    }

    public static function hasSubOptions($id = false){
        if($id){
            $hasOptions = self::where('parent_id',$id)->count();
            if($hasOptions > 0)
                return true;
            else
                return false;
        }
        return false;
    }

}
