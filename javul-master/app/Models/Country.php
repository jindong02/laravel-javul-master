<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['short_name','name'];

    /**
     * Get States for Country..
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states(){
        return $this->hasMany('App\State');
    }

    /**
     * Get Cities of Country..
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cities(){
        return $this->hasManyThrough('App\City','App\State');
    }

    /**
     * Get users of Country
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(){
        return $this->hasMany('App\User');
    }

    public static function getName($country_id){
        if(!empty($country_id))
            return self::find($country_id)->name;
    }
}
