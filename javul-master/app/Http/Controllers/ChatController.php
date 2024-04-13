<?php
namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\Issue;
use App\Models\SiteActivity;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hashids\Hashids;
use App\Models\Chat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except'=>['index','view','get_units_paginate']]);
        view()->share('site_activity_text','Unit Activity Log');
    }

    public function smilyTable()
    {
    	$smileys = Chat::smilylist();
		$used = array();
		$link = array();
		$image_url = asset("assets/js/emoji")."/";
		foreach ($smileys as $key => $val)
		{
			$link[] = '<a href="javascript:void(0);" onclick="insert_smiley(\''.$key.'\')"><img src="'.$image_url. $val[0] .'" alt="'.$val[3].'" style="width: '.$val[1].'; height: '.$val[2].'; border: 0;" /></a>';
		}
		return $link;
    }

    public function parse_smileys($str = '')
    {
    	$image_url = asset("assets/js/emoji")."/";
    	$smileys = Chat::smilylist();
    	foreach ($smileys as $key => $val)
		{
			$str = str_replace($key, '<a href="javascript:void(0);" onclick="insert_smiley(\''.$key.'\')"><img src="'.$image_url. $val[0] .'" alt="'.$val[3].'" style="width: '.$val[1].'; height: '.$val[2].'; border: 0;" /></a>', $str);
		}
		return $str;
    }

    public function chatroom($roomId , Request $request)
    {
    	if($roomId)
    	{
    		view()->share("smily",$this->smilyTable());
    		view()->share("roomId",$roomId);
    		$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        	$roomId = $unitIDHashID->decode($roomId);
        	if(!empty($roomId)){
                $roomId = $roomId[0];
                $roomDetail = Chat::room($roomId);
                $unit_id = $roomDetail['unit_id'];
                $unit = Unit::getUnitWithCategories($unit_id);
                if(!empty($unit)){

                    $userAuth = Auth::user();
                    $availableFunds =Fund::getUnitDonatedFund($unit_id);
                    $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                    $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));

                    view()->share('user_id', Auth::user()->id );
                    view()->share('unitObj',$unit );
                    view()->share('availableFunds',$availableFunds );
                    view()->share('awardedFunds',$awardedFunds );
                   	view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$unit_id);
                    view()->share('roomDetail',$roomDetail);
                    $issuesObj = Issue::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.page_limit'));
                    view()->share('issuesObj',$issuesObj);
                }
                return view("chat.chatroom");
            }
    	}
    }

    public function loaduser(Request $request,$return = false)
    {
    	if ($request->isMethod('post')) {
    		$roomId = $request->input("roomId");
    		$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        	$roomId = $unitIDHashID->decode($roomId);
        	$json['members'] = array();
        	if(!empty($roomId)){
                $roomId = $roomId[0];
                $members = Chat::roomMember($roomId);
                $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                if(!empty($members)){
                    foreach ($members as $key => $member) {
                        $user_id = $userIDHashID->encode($member->user_id);

                    	$json['members'][] = array(
                    		'id' => $member->id,
    			            'room_id' => $member->room_id,
    			            'user_id' => $member->user_id,
                            'link'   =>  url('userprofiles/'.$user_id.'/'.strtolower($member->first_name.'_'.$member->last_name)),
    			            'join_time' => $member->join_time,
    			            'name' => $member->first_name . ' ' . $member->last_name,
                    	);
                    }
                }
            }
            if(!$return) {
                return  json_encode($json);
            }
            return  $json;
        }
    }

    public function sendmsg(Request $request)
    {
    	if ($request->isMethod('post')) {
    		$json = array();

    		$roomId = $request->input("roomId");
    		$message = $request->input("message");
    		$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        	$roomId = $unitIDHashID->decode($roomId);
        	$user_id = Auth::user()->id;
        	if(!empty($roomId)){
                $roomId = $roomId[0];
                $messageId = Chat::sendmsg($roomId,$user_id,$message);
                if($messageId){
                	$json['success'] = true;
                }
                else
                {
                	$json['error'] = true;
                }
            }
            echo json_encode($json);
        }
    }

    public function online(Request $request)
    {
    	if ($request->isMethod('post')) {

	    	$data = Chat::online($request->input('unit_id'));
	    	$json['online'] = $data[0]->online;
	    	echo json_encode($json);
	   	}
    }

    public function loadmsg(Request $request)
    {
    	if ($request->isMethod('post'))
    	{
    		$json = array();
    		$roomId = $request->input("roomId");
    		$lastId = $request->input("lastId");
    		$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        	$roomId = $unitIDHashID->decode($roomId);
        	$json['messages'] = array();
        	if(!empty($roomId))
        	{
        		$roomId = $roomId[0];
        		$messages = Chat::loadmsg($roomId,$lastId);
                $json = $this->loaduser($request,$request->input("roomId"));
                if($messages){
                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    foreach ($messages as $key => $message) {
                        $user_id = $userIDHashID->encode($message->user);
                        $json['messages'][] = array(
                            'id' => $message->id,
                            'body' => $this->parse_smileys($message->body),
                            'user' => $message->user,
                            'link' => url('userprofiles/'.$user_id.'/'.strtolower($message->first_name.'_'.$message->last_name)),
                            'name' => $message->first_name . ' ' . $message->last_name,
                            'time' => Carbon::createFromFormat('Y-m-d H:i:s', $message->datetime)->diffForHumans(),
                        );
                    }
                }
            }
        	echo json_encode($json);
        }
    }

    public function create_room(Request $request)
    {
    	if ($request->isMethod('post'))
    	{
    		$json = array();
    		$unit_id = $request->input("unit_id");
    		$unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
        	$unit_id = $unitIDHashID->decode($unit_id);
        	if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $roomId = Chat::createRoom($unit_id);
                $roomId = $unitIDHashID->encode($roomId);
                $json['location'] = url('chat')."/". $roomId;
                Chat::updateCount();
            }
            echo json_encode($json);
        }
    }
}
