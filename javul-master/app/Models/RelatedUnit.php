<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedUnit extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'related_units';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['unit_id','related_to'];

    /**
     * function will return the array of related unit names.
     * @param $unit_id
     * @return array
     */
    public static function getRelatedUnitName($unit_id){
        $relatedObjs = self::where('unit_id',$unit_id)->first();
        $related_unit = [];
//        count($relatedObjs) > 0
        if(!empty($relatedObjs)){
            $related_to = explode(",",$relatedObjs->related_to);
            if(count($related_to) > 0){
                foreach($related_to as $val)
                    $related_unit[$val]=Unit::getUnitName($val);
            }
        }
        return $related_unit;
    }
}
