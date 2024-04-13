<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['state_id','name'];

    /**
     * Get State of City..
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(){
        return  $this->belongsTo('App\State');
    }

    /**
     * Get users of Country
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(){
        return $this->hasMany('App\User');
    }

    public static function getName($city_id){
        if(!empty($city_id))
        {

            if($city_id == 247)
                return 'GLOBAL';
            else
                return self::find($city_id)->name;
        }
        return '-';
    }
}
