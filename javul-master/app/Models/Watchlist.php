<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
	 /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'my_watchlist';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','unit_id','objective_id','task_id','issue_id'];

    public function units(){
        return $this->hasMany('App\Unit','id','unit_id');
    }

    public function objectives(){
        return $this->hasMany('App\Objective','id','objective_id');
    }

    public function tasks(){
        return $this->hasMany('App\Task','id','task_id');
    }
}
