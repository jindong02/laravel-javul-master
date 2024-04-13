<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteActivity extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','unit_id','objective_id','task_id','issue_id','comment'];
}
