<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unit_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','status','parent_id'];

    /**
     * Get Unit of Category...
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units(){
        return $this->hasMany('App\Unit','category_id');
    }

    public static function getName($id){
        if(!empty($id)){
            $tempid = explode(",",$id);
            if(count($tempid) > 1){
                $obj = self::whereIn('id',$tempid)->pluck('name')->all();
                return implode(",",$obj);
            }
            else{
                $obj = self::find($id);
                if($obj->count() > 0)
                    return $obj->name;
                return '-';
            }
        }
        return '-';
    }
    public static function getCategoryForBrowse($page,$id,$type){
        if (strpos($id, 'UCH') !== false) {
            if($type == "old") {
                $unit_category_history_id = str_replace("UCH","",$id);
                $id = UnitCategoryHistory::where('prefix_id', $id)->first()->unit_category_id;
            }
            else
                $id = UnitCategoryHistory::where('prefix_id',$id)->first()->id;
        }

        if($type == "new"){
            $dataObj=UnitCategoryHistory::where('parent_id',$id)->where('parent_id_belongs_to','new')->get();
        }
        else {

            if(\Auth::check())
                $where=" AND user_id=".\Auth::user()->id;

            if(!empty($unit_category_history_id))
                $where_last = "( parent_id =".$id." OR parent_id=".$unit_category_history_id.")";
            else
                $where_last = " parent_id =".$id;


            if($page == "site_admin"){

                $dataObj = \DB::select('SELECT
                                          unit_category.id AS id,
                                          NULL AS history_id,
                                          NULL AS job_id,
                                          unit_category.name AS name,
                                          NULL AS history_category_name,
                                          unit_category.parent_id AS parent_id,
                                          NULL AS history_parent_id,
                                          NULL AS action_type
                                        FROM
                                          `unit_category`,
                                          unit_category_history
                                        WHERE unit_category.parent_id = ' . $id . '
                                          AND unit_category.id != unit_category_history.unit_category_id
                                        UNION
                                        ALL
                                        SELECT
                                          unit_category.id AS id,
                                          history.history_id AS history_id,
                                          history.cat_id AS cat_id,
                                          unit_category.name AS name,
                                          history.history_category_name AS history_category_name,
                                          unit_category.parent_id AS parent_id,
                                          history.history_parent_id AS history_parent_id,
                                          history.action_type AS action_type
                                        FROM
                                          `unit_category`
                                          LEFT JOIN
                                            (SELECT
                                              unit_category_history.id AS history_id,
                                              unit_category_id AS cat_id,
                                              user_id,
                                              unit_category_history.`name` AS history_category_name,
                                              unit_category_history.parent_id AS history_parent_id,
                                              action_type
                                            FROM
                                              unit_category_history
                                            WHERE (
                                                parent_id_belongs_to = "old"
                                                OR parent_id_belongs_to IS NULL
                                              ) '.$where.') history
                                            ON unit_category.id = history.`cat_id`
                                        WHERE unit_category.parent_id = '.$id.'
                                        UNION
                                        ALL
                                        SELECT
                                          prefix_id AS id,
                                          prefix_id AS history_id,
                                          unit_category_id AS cat_id,
                                          `name`,
                                          `name` AS history_category_name,
                                          parent_id,
                                          parent_id AS history_parent_id,
                                          action_type
                                        FROM
                                          unit_category_history
                                        WHERE ' . $where_last . $where.'
                                          AND parent_id_belongs_to = "old"
                                          AND action_type != "delete"
                                        ORDER BY id ');
            }
            else
                $dataObj = \DB::select('SELECT
                                      unit_category.id AS id,
                                      NULL AS history_id,
                                      NULL AS cat_id,
                                      unit_category.name AS name,
                                      NULL AS history_category_name,
                                      unit_category.parent_id AS parent_id,
                                      NULL AS history_parent_id,
                                      NULL AS action_type
                                    FROM
                                      `unit_category`
                                    WHERE unit_category.parent_id = ' . $id . ' order by id');
        }
        return $dataObj;
    }


    public static function getHierarchy($value,$page=0){
        if($page == 0)
            $obj = self::where('name','like',$value.'%')->get();
        else {
            $offset = ($page - 1) * 10;
            $obj = self::where('name', 'like', $value . '%')->skip($offset)->take(10)->get();
        }

        $names = [];
        if(!empty($obj) && count($obj) > 0){
            foreach($obj as $category){
                if(!empty($category->parent_id)){

                    $str = self::getParent($category->parent_id,$category->name);
                }
                else{
                    $str = $category->name;
                }
                $names[]=['id'=>$category->id,'name'=>$str];
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
            return self::getParent($obj->parent_id,$obj->name);
        else
            $titles[] = $obj->name;

        $tempTitles = $titles;
        $titles = [];
        return $tempTitles ;
    }
}
