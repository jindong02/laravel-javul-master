<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaOfInterest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'area_of_interest';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title','parent_id'];

    public static function getName($id){
        $obj = self::find($id);
        if(!empty($obj))
            return $obj->title;
        return '-';
    }


    public static function getAreaOFInterestForBrowse($page,$id,$type){
        $area_of_interest_history_id = '';
        if (strpos($id, 'AOIH') !== false) {
            if($type == "old") {
                $area_of_interest_history_id = str_replace("AOIH","",$id);
                $id = AreaOfInterestHistory::where('prefix_id', $id)->first()->area_of_interest_id;
            }
            else
                $id = AreaOfInterestHistory::where('prefix_id',$id)->first()->id;
        }

        if($type == "new"){
            $dataObj=UnitCategoryHistory::where('parent_id',$id)->where('parent_id_belongs_to','new')->get();
        }
        else {

            $where=" AND user_id=".\Auth::user()->id;
            if(!empty($area_of_interest_history_id))
                $where_last = "( parent_id =".$id." OR parent_id=".$area_of_interest_history_id.")";
            else
                $where_last = " parent_id =".$id;


            if($page == "site_admin"){

                $dataObj = \DB::select('SELECT
                                          area_of_interest.id AS id,
                                          NULL AS history_id,
                                          NULL AS area_id,
                                          area_of_interest.title AS title,
                                          NULL AS history_area_of_interest_name,
                                          area_of_interest.parent_id AS parent_id,
                                          NULL AS history_parent_id,
                                          NULL AS action_type
                                        FROM
                                          `area_of_interest`,
                                          area_of_interest_history
                                        WHERE area_of_interest.parent_id = ' . $id . '
                                          AND area_of_interest.id != area_of_interest_history.area_of_interest_id
                                        UNION
                                        ALL
                                        SELECT
                                          area_of_interest.id AS id,
                                          history.history_id AS history_id,
                                          history.area_id AS area_id,
                                          area_of_interest.title AS title,
                                          history.history_area_of_interest_name AS history_area_of_interest_name,
                                          area_of_interest.parent_id AS parent_id,
                                          history.history_parent_id AS history_parent_id,
                                          history.action_type AS action_type
                                        FROM
                                          `area_of_interest`
                                          LEFT JOIN
                                            (SELECT
                                              area_of_interest_history.id AS history_id,
                                              area_of_interest_id AS area_id,
                                              user_id,
                                              area_of_interest_history.`title` AS history_area_of_interest_name,
                                              area_of_interest_history.parent_id AS history_parent_id,
                                              action_type
                                            FROM
                                              area_of_interest_history
                                            WHERE (
                                                parent_id_belongs_to = "old"
                                                OR parent_id_belongs_to IS NULL
                                              ) '.$where.') history
                                            ON area_of_interest.id = history.`area_id`
                                        WHERE area_of_interest.parent_id = '.$id.'
                                        UNION
                                        ALL
                                        SELECT
                                          prefix_id AS id,
                                          prefix_id AS history_id,
                                          area_of_interest_id AS area_id,
                                          `title`,
                                          `title` AS history_area_of_interest_name,
                                          parent_id,
                                          parent_id AS history_parent_id,
                                          action_type
                                        FROM
                                          area_of_interest_history
                                        WHERE ' . $where_last . $where.'
                                          AND parent_id_belongs_to = "old"
                                          AND action_type != "delete"
                                        ORDER BY id ');
            }
            else
                $dataObj = \DB::select('SELECT
                                      area_of_interest.id AS id,
                                      NULL AS history_id,
                                      NULL AS area_id,
                                      area_of_interest.title AS title,
                                      NULL AS history_area_of_interest_name,
                                      area_of_interest.parent_id AS parent_id,
                                      NULL AS history_parent_id,
                                      NULL AS action_type
                                    FROM
                                      `area_of_interest`
                                    WHERE area_of_interest.parent_id = ' . $id . ' order by id');
        }
        return $dataObj;
    }


    public static function getHierarchy($value,$page=0){
        if($page == 0)
            $obj = self::where('title','like',$value.'%')->get();
        else {
            $offset = ($page - 1) * 10;
            $obj = self::where('title', 'like', $value . '%')->skip($offset)->take(10)->get();
        }

        $names = [];
        if(!empty($obj) && count($obj) > 0){
            foreach($obj as $are_of_interest){
                if(!empty($are_of_interest->parent_id)){

                    $str = self::getParent($are_of_interest->parent_id,$are_of_interest->title);
                }
                else{
                    $str = $are_of_interest->title;
                }
                $names[]=['id'=>$are_of_interest->id,'name'=>$str];
            }

        }
        return $names;
    }

    public static  $titles=[];
    public static function getParent($parent_id,$name){
        $obj = self::find($parent_id);
        global $titles;
        $titles[]=$name;
        if(!empty($obj) && !empty($obj->parent_id))
            return self::getParent($obj->parent_id,$obj->title);
        else
            $titles[] = $obj->title;

        $tempTitles = $titles;
        $titles = [];
        return $tempTitles ;
    }

}
