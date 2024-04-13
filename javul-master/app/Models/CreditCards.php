<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCards extends Model
{
	 /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'credit_cards';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','card_id'];


    public static function getCardName($customer_id){

    }

    /**
     * Function will insert record in credit_card table.
     * @param $user_id
     * @param $card_id
     */
    public static function insert($user_id,$card_id){
        self::create([
            'user_id'=>$user_id,
            'card_id'=>$card_id
        ]);
    }
}
