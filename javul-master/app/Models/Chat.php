<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Chat extends Model
{

    public function __construct(){
        DB::delete("DELETE FROM chat_conversation  WHERE datetime <= (DATE_SUB(NOW(), INTERVAL 90 DAY))");

    }
    public static function smilylist(){
    	return array(
			':-)'			=>	array('grin.gif',			'19',	'19',	'grin'),
			':lol:'			=>	array('lol.gif',			'19',	'19',	'LOL'),
			':cheese:'		=>	array('cheese.gif',			'19',	'19',	'cheese'),
			':)'			=>	array('smile.gif',			'19',	'19',	'smile'),
			';-)'			=>	array('wink.gif',			'19',	'19',	'wink'),
			';)'			=>	array('wink.gif',			'19',	'19',	'wink'),
			':smirk:'		=>	array('smirk.gif',			'19',	'19',	'smirk'),
			':roll:'		=>	array('rolleyes.gif',		'19',	'19',	'rolleyes'),
			':-S'			=>	array('confused.gif',		'19',	'19',	'confused'),
			':wow:'			=>	array('surprise.gif',		'19',	'19',	'surprised'),
			':bug:'			=>	array('bigsurprise.gif',	'19',	'19',	'big surprise'),
			':-P'			=>	array('tongue_laugh.gif',	'19',	'19',	'tongue laugh'),
			'%-P'			=>	array('tongue_rolleye.gif',	'19',	'19',	'tongue rolleye'),
			';-P'			=>	array('tongue_wink.gif',	'19',	'19',	'tongue wink'),
			':blank:'		=>	array('blank.gif',			'19',	'19',	'blank stare'),
			':long:'		=>	array('longface.gif',		'19',	'19',	'long face'),
			':ohh:'			=>	array('ohh.gif',			'19',	'19',	'ohh'),
			':grrr:'		=>	array('grrr.gif',			'19',	'19',	'grrr'),
			':gulp:'		=>	array('gulp.gif',			'19',	'19',	'gulp'),
			'8-/'			=>	array('ohoh.gif',			'19',	'19',	'oh oh'),
			':down:'		=>	array('downer.gif',			'19',	'19',	'downer'),
			':red:'			=>	array('embarrassed.gif',	'19',	'19',	'red face'),
			':sick:'		=>	array('sick.gif',			'19',	'19',	'sick'),
			':shut:'		=>	array('shuteye.gif',		'19',	'19',	'shut eye'),
			':-/'			=>	array('hmm.gif',			'19',	'19',	'hmmm'),
			'>:('			=>	array('mad.gif',			'19',	'19',	'mad'),
			':mad:'			=>	array('mad.gif',			'19',	'19',	'mad'),
			'>:-('			=>	array('angry.gif',			'19',	'19',	'angry'),
			':angry:'		=>	array('angry.gif',			'19',	'19',	'angry'),
			':zip:'			=>	array('zip.gif',			'19',	'19',	'zipper'),
			':kiss:'		=>	array('kiss.gif',			'19',	'19',	'kiss'),
			':ahhh:'		=>	array('shock.gif',			'19',	'19',	'shock'),
			':coolsmile:'	=>	array('shade_smile.gif',	'19',	'19',	'cool smile'),
			':coolsmirk:'	=>	array('shade_smirk.gif',	'19',	'19',	'cool smirk'),
			':coolgrin:'	=>	array('shade_grin.gif',		'19',	'19',	'cool grin'),
			':coolhmm:'		=>	array('shade_hmm.gif',		'19',	'19',	'cool hmm'),
			':coolmad:'		=>	array('shade_mad.gif',		'19',	'19',	'cool mad'),
			':coolcheese:'	=>	array('shade_cheese.gif',	'19',	'19',	'cool cheese'),
			':vampire:'		=>	array('vampire.gif',		'19',	'19',	'vampire'),
			':snake:'		=>	array('snake.gif',			'19',	'19',	'snake'),
			':exclaim:'		=>	array('exclaim.gif',		'19',	'19',	'exclaim'),
			':question:'	=>	array('question.gif',		'19',	'19',	'question')
		);
    }
    public static function createRoom($unit_id=''){
        if(!empty($unit_id)){
            $user_id = Auth::user()->id;
            $check = DB::table("chat_room")->where("unit_id",'=',$unit_id)->get();
            if(!empty($check)){
            	$room_id = $check[0]->room_id;
            	$checkMember = DB::table("chat_room_member")->where("user_id",'=',$user_id)->where("room_id",'=',$room_id)->get();
            	if(!empty($checkMember)){
            		return $check[0]->room_id;
            	}
            	else
            	{
            		$member = array(
            			'room_id' => $room_id,
            			'user_id' => $user_id,
            			'join_time' => date("Y-m-d H:i:s"),
            		);
            		DB::table('chat_room_member')->insertGetId($member);
            		return $check[0]->room_id;
            	}
            }
            $chatroom = array(
            	'name' => 'test',
            	'unit_id' => $unit_id,
            	'created_datetime' => date("Y-m-d H:i:s"),
            );
            $room_id = DB::table('chat_room')->insertGetId($chatroom);
            $member = array(
    			'room_id' => $room_id,
    			'user_id' => $user_id,
    			'join_time' => date("Y-m-d H:i:s"),
    		);
    		DB::table('chat_room_member')->insertGetId($member);
            return $room_id;
        }
        return false;
    }
    public static function updateCount()
    {
        DB::update("UPDATE `chat_room` c SET total_member = (SELECT count(*) FROM chat_room_member WHERE room_id=c.room_id)");
    }
    public static function online( $unit_id ){
    	return  DB::select("SELECT count(*) as online FROM `chat_room_member` WHERE room_id = (SELECT room_id FROM chat_room WHERE unit_id= ". (int)$unit_id ." ) AND lastSeen >= '". date('Y-m-d H:i:s', strtotime('-15 seconds')) ."' ");
    }
    public static function room($room_id=''){
        if(!empty($room_id)){

            $check = DB::table("chat_room")->where("room_id",'=',$room_id)->get();
            if(!empty($check)){
            	return (array)$check[0];
            }
        }
        return false;
    }
    public static function loadmsg($roomId,$lastId = 0){
    	$conversation =  DB::table("chat_conversation")
            	->select(['chat_conversation.*','users.first_name','users.last_name'])
            	->where("chat_conversation.room_id",'=',$roomId)
            	->where("chat_conversation.id",'>',$lastId)
            	->join('users', 'users.id', '=', 'chat_conversation.user')
            	->orderBy('datetime', 'asc')
            	->get();
         DB::table('chat_room_member')
            ->where('room_id', $roomId)
            ->where('user_id',  Auth::user()->id)
            ->update(['lastSeen' => date("Y-m-d H:i:s")]);
            if(!empty($conversation)){
            	return (array)$conversation;
            }
    }
    public static function sendmsg($roomId,$user_id,$message){
    	$chat = array(
    		'room_id' => $roomId,
    		'body' => $message,
    		'user' => $user_id,
    		'datetime' => date("Y-m-d H:i:s"),
    	);
    	return DB::table('chat_conversation')->insertGetId($chat);
    }
    public static function roomMember($room_id=''){
        if(!empty($room_id)){

            $member =  DB::table("chat_room_member")
            	->select(['chat_room_member.*','users.first_name','users.last_name'])
                ->where("chat_room_member.room_id",'=',$room_id)
            	->where("chat_room_member.lastSeen",'>=', DB::raw("'".date('Y-m-d H:i:s', strtotime('-15 seconds')) ."'") )
            	->join('users', 'users.id', '=', 'chat_room_member.user_id')
            	->get();
            if(!empty($member)){
            	return (array)$member;
            }
        }
        return false;
    }
}
