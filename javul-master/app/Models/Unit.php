<?php

namespace App\Models;

//use ___PHPSTORM_HELPERS\object;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Hashids\Hashids;

class Unit extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = true;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'units';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','category_id','name','description','credibility','country_id','state_id','city_id','status',
        'parent_id','modified_by','slug','featured_unit','state_id_for_city_not_exits'];

    /**
     * Get UnitCategory of Unit
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(){
        return $this->belongsTo('App\UnitCategory');
    }

    public function watchlist(){
        return $this->belongsTo('App\Watchlist');
    }
    /**
     * Get Objectives of Unit..
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function objectives(){
        return $this->hasMany('App\Objective');
    }

    /**
     * Get Tasks of Unit..
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function tasks(){
        return $this->hasManyThrough('App\Task','App\Objective');
    }


    /**
     * function will return unit with it's category name. if unit having multiple category then it will return all category name with unit.
     * @param string $unit_id
     * @return mixed
     */
//    public static function getUnitWithCategories($unit_id=''){
//        $where= ' WHERE units.deleted_at IS NULL ';
//        if(!empty($unit_id))
//            $where .= " and units.id='".$unit_id."' ";
//
//        $unitsObj = \DB::select( DB::raw("SELECT cr.total_member,units.*,GROUP_CONCAT(unit_category.name SEPARATOR ', ') as category_name,
//            (SELECT count(*) FROM forum_topic WHERE unit_id = units.id ) as totaltopic
//            FROM units INNER JOIN unit_category ON  (units.category_id IS NOT NULL and FIND_IN_SET(unit_category.id,units.category_id) > 0  )
//            LEFT JOIN chat_room cr ON (cr.unit_id = units.id )
//            $where GROUP BY units.id") );
//
//        $extraWhere = array();
//        $extraWhere[] = array("wiki_pages.unit_id","=",$unit_id);
//        $extraWhere[] = array("wiki_pages.is_wikihome","=",3);
//
//
//        $wiki = DB::table("wiki_pages")
//                        ->select("page_content")
//                        ->where($extraWhere)
//                        ->get();
//
//        if(!empty($wiki) && $wiki->count() > 0){
//            $wiki[0]->page_content = Wiki::parse($wiki[0]->page_content);
//        }
//
//        if(count($unitsObj) == 1){
//            $unitsObjTmp = $unitsObj[0];
//            if(!empty($unit_id)){
//
//                $unitsObjTmp->other_menulink = $wiki->count() == 0 ? "Other Link" : $wiki[0]->page_content;
//                return $unitsObjTmp;
//            }
//            $unitsObj= array_filter((array)$unitsObj[0]);
//            if(!empty($unitsObj)){
//                $temp[] =(object)$unitsObjTmp ;
//                return $temp;
//            }
//        }
//
//        return $unitsObj;
//    }


    public static function getUnitWithCategories($unit_id = '')
    {
        $query = DB::table('units')
            ->select(
                'units.*',
                'cr.total_member',
                DB::raw('GROUP_CONCAT(unit_category.name SEPARATOR ", ") as category_name'),
                DB::raw('(SELECT COUNT(*) FROM forum_topic WHERE unit_id = units.id) as totaltopic')
            )
            ->leftJoin('chat_room as cr', 'cr.unit_id', '=', 'units.id')
            ->leftJoin('unit_category', function ($join) {
                $join->on('units.category_id', 'IS', DB::raw('NOT NULL'))
                    ->whereRaw('FIND_IN_SET(unit_category.id, units.category_id) > 0');
            })
            ->whereNull('units.deleted_at');

        if (!empty($unit_id)) {
            $query->where('units.id', $unit_id);
        }

        $query->groupBy('units.id');

        $unitsObj = $query->get();

        if (count($unitsObj) == 1) {
            $unitsObjTmp = $unitsObj[0];
            if (!empty($unit_id)) {
                $extraWhere = [
                    ['wiki_pages.unit_id', '=', $unit_id],
                    ['wiki_pages.is_wikihome', '=', 3]
                ];

                $wiki = DB::table('wiki_pages')
                    ->select('page_content')
                    ->where($extraWhere)
                    ->first();

                if (!empty($wiki)) {
                    $unitsObjTmp->other_menulink = Wiki::parse($wiki->page_content);
                } else {
                    $unitsObjTmp->other_menulink = "Other Link";
                }

                return $unitsObjTmp;
            }

            $unitsObj = array_filter((array) $unitsObj[0]);
            if (!empty($unitsObj)) {
                $temp[] = (object) $unitsObjTmp;
                return $temp;
            }
        }

        if (count($unitsObj) > 0) {
            return $unitsObj[0];
        }

        return $unitsObj;
    }

    /**
     * function will check whether unit_id is exist in unit table or not
     * @param $unit_id
     * @param bool $needToDecode
     * @return bool
     */
    public static function checkUnitExist($unit_id,$needToDecode=false)
    {
        if($needToDecode){
            $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id );

            if(empty($unit_id))
                return false;
            $unit_id = $unit_id[0];

            if(Unit::find($unit_id)->count() == 0)
                return false;
            return true;
        }

    }

    public static function getAllCountryWithFrequent(){
        $top10MostCountries = self::join('countries','units.country_id','=','countries.id')
            ->groupBy('country_id')
            ->orderBy('units_id', 'desc')
            ->selectRaw('max(countries.id) as countriesid , max(countries.name) as countriesname, max(units.id) as units_id')
            ->limit(10)
            ->pluck('countriesname','countriesid')
            ->all();





        $top10MostCountries['dash_line']='dash_line';
        $countries_id = array_keys($top10MostCountries);
        $otherCountries = Country::whereNotIn('id',$countries_id)->where('id','!=','247')->pluck('name','id')->all();

        $all=['247'=>'Global','dash_line1'=>'dash_line1']+($top10MostCountries + $otherCountries );

        return $all;
    }

    public static function getUnitName($unit_id){
        $obj = self::withTrashed()->find($unit_id);
        if(!empty($obj))
            return $obj->name;
        else
            return false;
    }
    public static function getSlug($unit_id){
        $obj = self::withTrashed()->find($unit_id);
        if(!empty($obj))
            return $obj->slug;
        else
            return false;

    }
    public static function getCategoryNames($category_id)
    {
        $categoryObj = UnitCategory::whereIn('id',explode(",",$category_id))->pluck('name')->all();
        if(!empty($categoryObj))
            return implode(", ",$categoryObj);
        return "-";
    }

    public static function getObj($unit_id){
        $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id );

        if(empty($unit_id))
            return [];
        $unit_id = $unit_id[0];

        if(Unit::find($unit_id)->count() > 0)
            return Unit::find($unit_id);
        return [];
    }
}
