<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAward extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','leadership','task_completion','ingenuity','mediator_facilitator','accountibility_award'];
}
