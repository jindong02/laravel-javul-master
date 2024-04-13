<?php
namespace App\Http\Controllers;
use App\Models\Alerts;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\Models\Forum;
use Hashids\Hashids;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Fund;
use App\Models\SiteActivity;
use App\Models\ActivityPoint;
use App\Models\Issue;
use App\Models\Objective;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except'=>['index']]);
        view()->share('site_activity_text','Unit Activity Log');
    }

    public function index($unit_id = 0)
    {
    	view()->share("unit_id",$unit_id);
    	$unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id))
        {
        	$unit_id = $unit_id[0];
	        $unit = Unit::getUnitWithCategories($unit_id);
	        if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
	        	$topics[1] = Forum::getTopics($unit_id,array("section_id" => 1, "limit" => 5));
	        	$topics[2] = Forum::getTopics($unit_id,array("section_id" => 2, "limit" => 5));
	        	$topics[3] = Forum::getTopics($unit_id,array("section_id" => 3, "limit" => 5));
	        	$topics[4] = Forum::getTopics($unit_id,array("section_id" => 4, "limit" => 5));
    			$allTopics  = array();
                $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
    			foreach ($topics as $keyTop => $valueTop) {
	    			foreach ($valueTop as $key => $value) {
	                    $user_id = $userIDHashID->encode($value->user_id);
	                    $lastReply = substr($value->lastReply, 0, strpos($value->lastReply, ':'));
	                    $reuser_id = $userIDHashID->encode(substr($value->lastReply,  (strpos($value->lastReply, ':') + 1) ));
	    				$allTopics[$keyTop][] = array(
		    				'topic_id' => $value->topic_id,
				            'title' => $value->title,
				            'user_id' => $value->user_id,
	                        'unit_id' => $value->unit_id,
	                        'slug' => $value->slug,
	                        'first_name' => $value->first_name ,
				            'last_name' => $value->last_name ,
				            'created_time' => Carbon::createFromFormat('Y-m-d H:i:s', $value->created_time)->diffForHumans(),
	                        'link_user' => url('userprofiles/'. $user_id .'/'.strtolower($value->first_name.'_'.$value->last_name)),
	                        'link_reply' => url('userprofiles/'. $reuser_id .'/'.str_replace(' ', '_', $lastReply)),
	                        'post' => $value->post,
	                        'updownstatus' => $value->updownstatus,
	                        'lastReply' => substr($value->lastReply, 0, strpos($value->lastReply, ':')),
				            'votecount' => (int)$value->votecount,
				        );
	    			}
    			}

                $start = 0;
                if(isset($_GET['page']))
                {
                    $start = ( ((int)$_GET['page'] -1) * 10);
                }
                view()->share("start",$start);
                view()->share("topics",$allTopics);

    			view()->share("unit",$unit);

    			return view("forum.forum_home");
	        }
        }
        return view("errors.404");
    }
    public function view($unit_id,$section_name)
    {
    	view()->share("unit_id",$unit_id);
    	$section_id= 0;
        if($section_name == 'objectives'){
        	$section_id = 1;
        }
        else if($section_name == 'tasks'){
        	$section_id = 2;
        }
        else if($section_name == 'issues'){
        	$section_id = 3;
        }
        else if($section_name == 'other_discussions'){
        	$section_id = 4;
        }
        view()->share("section_id",$section_id);
    	view()->share("section_name",$section_name);
    	$unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
        	$unit_id = $unit_id[0];
	        $unit = Unit::getUnitWithCategories($unit_id);
	        if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
	        	$topics = Forum::getTopics($unit_id,array("section_id" => $section_id));
    			$allTopics  = array();
                $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
    			foreach ($topics->items() as $key => $value) {
                    $user_id = $userIDHashID->encode($value->user_id);
                    $lastReply = substr($value->lastReply, 0, strpos($value->lastReply, ':'));
                    $reuser_id = $userIDHashID->encode(substr($value->lastReply,  (strpos($value->lastReply, ':') + 1) ));
    				$allTopics[] = array(
	    				'topic_id' => $value->topic_id,
			            'title' => $value->title,
			            'user_id' => $value->user_id,
                        'unit_id' => $value->unit_id,
                        'slug' => $value->slug,
                        'first_name' => $value->first_name ,
			            'last_name' => $value->last_name ,
			            'created_time' => Carbon::createFromFormat('Y-m-d H:i:s', $value->created_time)->diffForHumans(),
                        'link_user' => url('userprofiles/'. $user_id .'/'.strtolower($value->first_name.'_'.$value->last_name)),
                        'link_reply' => url('userprofiles/'. $reuser_id .'/'.str_replace(' ', '_', $lastReply)),
                        'post' => $value->post,
                        'updownstatus' => $value->updownstatus,
                        'lastReply' => substr($value->lastReply, 0, strpos($value->lastReply, ':')),
			            'votecount' => (int)$value->votecount,
			        );
    			}
                $start = 0;
                if(isset($_GET['page']))
                {
                    $start = ( ((int)$_GET['page'] -1) * 10);
                }
                view()->share("start",$start);
                view()->share("topics",$allTopics);
                view()->share("section_name",$section_name);
                view()->share("pagination",$topics->links());
    			view()->share("unit",$unit);

    			return view("forum.topic_list");
	        }
        }
    }
    public function loadObjectiveComment(Request $request)
    {
        $inputData = $request->all();

        $filter = array(
            'limit' => 5,
            'page' => 1,
            'noChild' => 1,
            'htmldecode' => 1,
            'unit_id' => $inputData['unit_id'],
            'section_id' => $inputData['section_id'],
            'object_id' => $inputData['object_id'],
            'orderBy' => 'desc',
        );
        $json['comments'] = Forum::getPost($filter);
        echo json_encode($json);
    }
    public function submitauto(Request $request)
    {
        $inputData = $request->all();
        $validator = Validator::make($inputData, [
            'object_id'=> 'required',
            'desc'=> 'required',
            'unit_id'=> 'required',
            'section_id'=> 'required',
        ]);
        if ($validator->fails()){
            return json_encode(array(
                'error' => 'PLEASE_FILL_PROPER_DETAILS'
            ), 200);
        }
        $forumID =  Forum::checkTopic(array(
            'unit_id' => $inputData['unit_id'],
            'section_id' => $inputData['section_id'],
            'object_id' => $inputData['object_id'],
        ));


        $json = array();
        if(empty($forumID))
        {
            $forumData = array();
            if($inputData['section_id'] == 3){
                $issueObj = Issue::with(['issue_documents'])->find($inputData['object_id']);
                $forumData['title'] = $issueObj->title;
                $forumData['unit_id'] = $issueObj->unit_id;
                $forumData['desc'] = $issueObj->description;
                $forumData['slug'] = substr(str_replace(" ","_",strtolower( $issueObj->title )),0,20);
                $forumData['section_id'] = $inputData['section_id'];
                $forumData['object_id'] = $inputData['object_id'];
            }
            else if($inputData['section_id'] == 2){
                $taskObj = Task::with(['objective','task_documents'])->find($inputData['object_id']);
                $forumData['title'] = $taskObj->name;
                $forumData['unit_id'] = $taskObj->unit_id;
                $forumData['desc'] = $taskObj->description;
                $forumData['slug'] = $taskObj->slug != '' ? $taskObj->slug : substr(str_replace(" ","_",strtolower( $taskObj->name )),0,20);
                $forumData['section_id'] = $inputData['section_id'];
                $forumData['object_id'] = $inputData['object_id'];
            }
            else if($inputData['section_id'] == 1){
                $Obj = Objective::where('id',$inputData['object_id'])->first();
                $forumData['title'] = $Obj->name;
                $forumData['unit_id'] = $Obj->unit_id;
                $forumData['desc'] = $Obj->description;
                $forumData['slug'] = $Obj->slug != '' ? $Obj->slug : substr(str_replace(" ","_",strtolower( $Obj->name )),0,20);
                $forumData['section_id'] = $inputData['section_id'];
                $forumData['object_id'] = $inputData['object_id'];
            }
            if(!empty($forumData)){
                $forumID = Forum::submit($forumData);
            }
            else
            {
                $json['error'] = "Something wrong try again..";
            }
        }
        else
        {
            $forumID = $forumID->topic_id;
        }

        if($forumID){
            $commmentData = array(
                'post' => $inputData['desc'],
                'topic_id' => $forumID,
                'reply_id' => 0,
            );
            $commmentId = Forum::postSubmit($commmentData);
            if($commmentId){
                $json['success'] = 'Comment submitted';
            }
            else
            {
                $json['error'] = "Something wrong try again..";
            }
        }
        else
        {
            $json['error'] = "Something wrong try again..";
        }

        echo json_encode($json);die;
    }
    public function submit(Request $request)
    {
    	$inputData = $request->all();
        $inputData['slug']=substr(str_replace(" ","_",strtolower($inputData['title'])),0,20);
        $validator = Validator::make($inputData, [
            'title'=> 'required'
        ],[
            'title.required'=>'Please enter title',
        ]);

        if ($validator->fails()){
            return json_encode(array(
				'errors' => $validator->getMessageBag()->toArray()
			), 200);
        }
        $topicId = Forum::submit($inputData);
        $json = array();
        if($topicId){
            $unit = Unit::getUnitWithCategories($inputData['unit_id']);
            $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->encode($inputData['unit_id']);
            $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);

            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
            if(!empty(Auth::user()->username))
                $loggedinUsername = Auth::user()->username;

            SiteActivity::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=>$inputData['unit_id'],
                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                    .$loggedinUsername.'</a>
                created forum thread <a href="'. url('forum/post/'.$topicId.'/' .$inputData['slug']) .'">'. $inputData['title'] .'</a> for unit  <a href="'.url('units/'.$unit_id.'/'.$unit->slug).'">'.$unit->name.'</a> '
            ]);
            $json['success'] = "Topic created successfully";
        	$json['location'] = url('forum/post/'.$topicId.'/' .$inputData['slug']);
        }
        else
        {
        	$json['error'] = "Error in creating topic";
        }
       	return json_encode($json);
    }
    public function postSubmit(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'post'=> 'required'
        ],[
            'post.required'=>'Please enter comment',
        ]);

        if ($validator->fails()){
            return json_encode(array(
				'errors' => $validator->getMessageBag()->toArray()
			), 200);
        }
        $postId = Forum::postSubmit($inputData);
        if($postId)
        {
            $json['success'] = "Post created successfully";
            $filter = array(
                'parent' => $inputData['reply_id'],
                'page' => 0,
                'limit' => 15,
                'postId' => $postId,
                'topic_id' => $inputData['topic_id'],
            );
        	$json['post'] = Forum::getPost($filter);

            /* Add Site Activity */
                $unitData = Forum::getUnitId($inputData['topic_id']);
                $unitId =  $unitData['unit_id'];
                $unit = Unit::getUnitWithCategories($unitId);
                $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
                $unit_id = $unitIDHashID->encode($unitId);
                $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                $user_id = $userIDHashID->encode($unitData['user_id']);

                $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                if(!empty(Auth::user()->username))
                    $loggedinUsername = Auth::user()->username;

                $unitUsername = strtolower($unitData['first_name'].'_'.$unitData['last_name']);
                if(!empty($unitData['username']))
                    $unitUsername = $unitData['username'];

                if($inputData['reply_id'] > 0){
                    $fromuser_id = $userIDHashID->encode($unitData['user_id']);
                    $commment = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">' .$loggedinUsername.'</a>
                     replied to <a href="'.url('userprofiles/'.$fromuser_id.'/'.strtolower($unitData['first_name'].'_'.$unitData['last_name'])).'">' .$unitUsername.'</a>
                     in forum thread <a href="'.url('forum/post/'.$inputData['topic_id'].'/' . $unitData['slug'] ).'"> '. $unitData['title'] .' </a>
                     for Unit <a href="'.url('units/'.$unit_id.'/'.$unit->slug).'">'.$unit->name.'</a> ';
                }
                else
                {
                    $commment = '
                        <a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">' .$loggedinUsername.'</a>
                        posted a comment in forum thread  <a href="'.url('forum/post/'.$inputData['topic_id'].'/' . $unitData['slug'] ).'"> '. $unitData['title'] .' </a>
                        for Unit <a href="'.url('units/'.$unit_id.'/'.$unit->slug).'">'.$unit->name.'</a>';
                }

                // if someone reply to forum then add record in alert_notification table for creator
                if(Auth::user()->id != $unitData['user_id']) {
                    $thread_creator_obj = User::find($unitData['user_id']);
                    $unit_link = '<b><a style="text-decoration:none;" href="' . url('units/' . $unit_id . '/' . $unit->slug) . '">' . $unit->name . '</a></b>';

                    $content = 'User <b><a style="text-decoration:none;" href="' . url('userprofiles/' . $user_id . '/' . strtolower(Auth::user()->first_name . '_' . Auth::user()->last_name)) . '">' . $loggedinUsername . '</a></b> ' .
                        'replied to your forum thread <b><a style="text-decoration:none;" href="' . url('forum/post/' . $inputData['topic_id'] . '/' . $unitData['slug']) . '">' . $unitData['title'] . ' </a>' .
                        '</b>(Unit:' . $unit_link . ')';

                    $email_subject = 'User ' . Auth::user()->first_name . ' ' .Auth::user()->last_name . ' replied to your forum thread '.$unitData['title'];
                    User::SendEmailAndOnSiteAlert($content,$email_subject,[$thread_creator_obj],$onlyemail=false,'forum_replies');

                }


                SiteActivity::create([
                    'user_id'=>Auth::user()->id,
                    'unit_id'=>$unitId,
                    'comment'=> $commment
                ]);

                if(Auth::user()->id == $unitData['user_id']){
                    if($inputData['reply_id'] > 0){

                        $userReplyObj = Forum::getUserOfReply($inputData['reply_id']);
                        if(!empty($userReplyObj)) {

                            $content = 'User <b><a style="text-decoration:none;" href="' . url('userprofiles/' . $user_id . '/' . strtolower(Auth::user()->first_name . '_' . Auth::user()->last_name)) . '">' . $loggedinUsername . '</a></b>' .
                                ' replied to your comment <b><a style="text-decoration:none;" href="' . url('forum/post/' . $inputData['topic_id'] . '/' . $unitData['slug']) . '">' . $unitData['title'] . ' </a></b>';

                            $email_subject = 'User ' . Auth::user()->first_name . ' ' .Auth::user()->last_name . ' replied to your comment under thread '.$unitData['title'];

                            User::SendEmailAndOnSiteAlert($content,$email_subject,[$userReplyObj],$onlyemail=false,'forum_replies');

                        }

                    }
                }

            /* Add Site Activity */

        }
        else
        {
        	$json['error'] = "Error in creating post";
        }
       	return json_encode($json);
    }

    public function postLoad(Request $request)
    {
    	$inputData = $request->all();
    	$json = array();
        $inputData['limit'] = $limit = 5;
      //  $inputData['page'] = 5;

        $posts = Forum::getPost($inputData);
    	$json['total'] = Forum::getPostCount($inputData);
        //$json['left']= ($json['total']-( ($inputData['page'] +1) *  $limit ));
        //$json['left'] = $json['left'] > 0 ? $json['left'] : 0;
        $json['post'] = $posts['items'];
        $paginator = new Paginator($json['post'], $json['total'], $limit , $inputData['page'], [
            'path'  => url('forum/post')."/". $inputData['topic_id'] ."/".$posts['slug'],
        ]);
         $json['paginate'] =  $paginator->toHtml();
       // $json['paginate'] = str_replace('forum/postLoad', 'forum/post/'. $inputData['topic_id'] ."/" . $posts['slug'] , $posts['paginate']->toHtml() );

    	echo json_encode($json);
    }

    public function post($topic_id)
    {
        $topic = Forum::getTopic($topic_id);
        if(!empty($topic)){
            $unit_id = $topic[0]->unit_id;
            $unit = Unit::getUnitWithCategories($unit_id);
            if(!empty($topic)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
                $topicDetail = $topic[0];
                $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                $user_id = $userIDHashID->encode($topicDetail->user_id);
                $topicDetail->link =  url('userprofiles/'. $user_id .'/'.strtolower($topicDetail->first_name.'_'.$topicDetail->last_name));
                $topicDetail->created_time =  Carbon::createFromFormat('Y-m-d H:i:s', $topicDetail->created_time)->diffForHumans();
                if($topicDetail->object_id > 0){

                    if($topicDetail->section_id == 3){
                        $issueObj = Issue::with(['issue_documents'])->find($topicDetail->object_id);
                        $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
                        $issue_id = $issueIDHashID->encode($issueObj->unit_id);
                        $topicDetail->objectLink  = url('issues')."/".$issue_id."/view";
                        $topicDetail->objectLinkText  = "VIEW ISSUE";

                    }
                    else if($topicDetail->section_id == 2){
                        $taskObj = Task::with(['objective','task_documents'])->find($topicDetail->object_id);
                        $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
                        $task_id = $taskIDHashID->encode($taskObj->id);
                        $topicDetail->objectLink  = url('tasks')."/".$task_id."/".$taskObj->slug;
                        $topicDetail->objectLinkText  = "VIEW TASK";

                    }
                    else if($topicDetail->section_id == 1){
                        $Obj = Objective::where('id',$topicDetail->object_id)->first();
                        $unitIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
                        $unit_id = $unitIDHashID->encode($Obj->id);
                        $topicDetail->objectLink  = url('objectives')."/".$unit_id."/".$Obj->slug;
                        $topicDetail->objectLinkText  = "VIEW OBJECTIVES";
                    }
                }

                view()->share("topic",$topicDetail);
                view()->share("topic_id",$topic_id);
                return view("forum.post");
            }
        }
        return view("errors.404");
    }

    public function postUpDown(Request $request)
    {
        $inputData = $request->all();
        $validator = Validator::make($inputData, [
            'val'=> 'required',
            'topic_id'=> 'topic_id',
        ]);
        if ($validator->fails()){
            return json_encode(array(
                'error' => "Something wrong"
            ), 200);
        }
        $updownId = Forum::postUpDown($inputData);
        $json['point'] = Forum::postUpDownCount($inputData);
        if($updownId){
            $json['success'] = true;
        }
        else
        {
            $json['error'] = true;
        }
        echo json_encode($json);
    }

    public function topicUpDown(Request $request)
    {
        $inputData = $request->all();
        $validator = Validator::make($inputData, [
            'val'=> 'required',
            'topic_id'=> 'required',
        ]);

        if ($validator->fails()){
            return json_encode(array(
                'error' => "Something wrong"
            ), 200);
        }
        $updownId = Forum::topicUpDown($inputData);
        if($updownId){
            $json['success'] = true;
            $json['count'] = Forum::topicUpDownCount($inputData['topic_id']);
        }
        else
        {
            $json['error'] = true;
        }
        echo json_encode($json);
    }

    public function ideapoint(Request $request)
    {
        $inputData = $request->all();
        $validator = Validator::make($inputData, [
            'val'=> 'required',
            'post_id'=> 'required',
        ]);

        if ($validator->fails()){
            return json_encode(array(
                'error' => "Something wrong"
            ), 200);
        }
        $ideapointId = Forum::ideapoint($inputData);
        if($ideapointId){
            $json['success'] = true;
            $json['val'] = $inputData['val'] == 1 ? 0 : 1;
        }
        else
        {
            $json['error'] = true;
        }
        echo json_encode($json);
    }

    public function post_ideapoint(Request $request)
    {
        $inputData = $request->all();
        $validator = Validator::make($inputData, [
            'val'=> 'required',
            'topic_id'=> 'required',
        ]);
        if ($validator->fails()){
            return json_encode(array(
                'error' => "Something wrong"
            ), 200);
        }
        $ideapointId = Forum::post_ideapoint($inputData);
        if($ideapointId){
            $json['success'] = true;
            $json['val'] = $inputData['val'] == 1 ? 0 : 1;
        }
        else
        {
            $json['error'] = true;
        }
        echo json_encode($json);
    }

    public function create($unit_id,$section_name)
    {
	    view()->share("unitid",$unit_id);
    	$unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
        $unit_id = $unitIDHashID->decode($unit_id);
        if(!empty($unit_id)){
        	$unit_id = $unit_id[0];
	        $unit = Unit::getUnitWithCategories($unit_id);
	        if(!empty($unit)){
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);
                $section_id= 0;
                if($section_name == 'objectives'){
                	$section_id = 1;
                }
                else if($section_name == 'tasks'){
                	$section_id = 2;
                }
                else if($section_name == 'issues'){
                	$section_id = 3;
                }
                else if($section_name == 'other_discussions'){
                	$section_id = 4;
                }
                view()->share('section_id',$section_id );
                view()->share('unitObj',$unit );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);
	        	view()->share("unit_id",$unit->id);
    			return view("forum.forum_create");
	        }
        }
    }
}
