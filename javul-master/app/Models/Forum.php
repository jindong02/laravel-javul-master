<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Hashids\Hashids;
use App\Models\ActivityPoint;



class Forum extends Model
{
    public static function getTopics($unit_id,$filter){
        $extraWhere = array();
        if(isset($filter['section_id'])){
            $extraWhere[] = array("forum_topic.section_id","=",$filter['section_id']);
        }
        if(Auth::check()){
            $topics = DB::table("forum_topic")
                        ->select([
                            "forum_topic_updown.value as updownstatus",
                            "forum_topic.topic_id",
                            "forum_topic.unit_id",
                            "forum_topic.title",
                            "forum_topic.user_id",
                            "forum_topic.slug",
                            "forum_topic.created_time",
                            "forum_topic.modify_time",
                            "users.first_name",
                            "users.last_name",
                            DB::raw("(SELECT count(*) FROM forum_post as temp WHERE forum_topic.topic_id=temp.topic_id ) as post"),
                            DB::raw("(SELECT sum(value) FROM forum_topic_updown as tempud WHERE tempud.topic_id = forum_topic.topic_id AND tempud.user_id = ". Auth::user()->id ." ) as votecount"),
                            DB::raw("(SELECT CONCAT(users.first_name, ' ' ,users.last_name,':',id) FROM users WHERE users.id = (SELECT user_id FROM forum_post as temp_forum_post WHERE temp_forum_post.topic_id =  forum_topic.topic_id ORDER BY post_id DESC LIMIT 1  ) ) as lastReply"),

                        ])
                        ->join('users', 'users.id', '=', 'forum_topic.user_id')
                        ->leftJoin('forum_topic_updown', function($join){
                            $join->on('forum_topic_updown.user_id', '=', DB::raw(Auth::user()->id) );
                            $join->on('forum_topic_updown.topic_id', '=', 'forum_topic.topic_id' );
                        })
                        ->where($extraWhere)
                        ->where("forum_topic.unit_id","=",$unit_id)
                        ->orderBy("votecount","DESC")
                        ->orderBy("forum_topic.topic_id","DESC");
            }else{
                $topics = DB::table("forum_topic")
                ->select([
                    "forum_topic_updown.value as updownstatus",
                    "forum_topic.topic_id",
                    "forum_topic.unit_id",
                    "forum_topic.title",
                    "forum_topic.user_id",
                    "forum_topic.slug",
                    "forum_topic.created_time",
                    "forum_topic.modify_time",
                    "users.first_name",
                    "users.last_name",
                    DB::raw("(SELECT count(*) FROM forum_post as temp WHERE forum_topic.topic_id=temp.topic_id ) as post"),
                    DB::raw("(SELECT sum(value) FROM forum_topic_updown as tempud WHERE tempud.topic_id = forum_topic.topic_id ) as votecount"),
                    DB::raw("(SELECT CONCAT(users.first_name, ' ' ,users.last_name,':',id) FROM users WHERE users.id = (SELECT user_id FROM forum_post as temp_forum_post WHERE temp_forum_post.topic_id =  forum_topic.topic_id ORDER BY post_id DESC LIMIT 1  ) ) as lastReply"),

                ])
                ->join('users', 'users.id', '=', 'forum_topic.user_id')
                ->leftJoin('forum_topic_updown', function($join){
                    $join->on('forum_topic_updown.topic_id', '=', 'forum_topic.topic_id' );
                })
                ->where($extraWhere)
                ->where("forum_topic.unit_id","=",$unit_id)
                ->orderBy("votecount","DESC")
                ->orderBy("forum_topic.topic_id","DESC");
            }
         if(isset($filter['limit'])){
            $topics =  $topics->limit($filter['limit']);
            $topics =  $topics->get();
         }
         else
         {
    		$topics =  $topics->paginate(10);
         }
    	return $topics;
    }
    public static function checkTopic($filter = array()){
        $extraWhere = array();
        if(isset($filter['unit_id'])){
            $extraWhere[] = array("forum_topic.unit_id","=",$filter['unit_id']);
        }
        if(isset($filter['section_id'])){
            $extraWhere[] = array("forum_topic.section_id","=",$filter['section_id']);
        }
        if(isset($filter['object_id'])){
            $extraWhere[] = array("forum_topic.object_id","=",$filter['object_id']);
        }
        $topic =   DB::table("forum_topic")
                    ->select(['topic_id','slug'])
                    ->where($extraWhere)
                    ->get();

        if(count($topic) && !empty($topic)){
            return $topic[0];
        }
        return array();
    }
    /*public static function getTopic($topic_id){
    	$topics = DB::table("forum_topic")
    				->select(["forum_topic_updown.value as updownstatus","forum_topic.*","users.first_name","users.last_name" ,
                        DB::raw("(SELECT sum(value) FROM forum_updown WHERE post_id IN (
                            (SELECT post_id FROM forum_post WHERE  topic_id = forum_topic.topic_id
                            ) ) ) as updownpoint "),
                        DB::raw("(SELECT count(*) FROM forum_ideapoint WHERE post_id IN ( (SELECT post_id FROM forum_post WHERE topic_id = forum_topic.topic_id ) ) ) as ideascore "),
                        DB::raw("(SELECT sum(value) FROM forum_topic_updown as tempud WHERE tempud.topic_id = forum_topic.topic_id AND tempud.user_id = ". Auth::user()->id ." ) as votecount"),
                        ])
                    ->leftJoin('forum_topic_updown', function($join){
                        $join->on('forum_topic_updown.user_id', '=', DB::raw(Auth::user()->id) );
                        $join->on('forum_topic_updown.topic_id', '=', 'forum_topic.topic_id' );
                     })
    				->join('users', 'users.id', '=', 'forum_topic.user_id')
    				->where("forum_topic.topic_id","=",$topic_id)
    				->get();
    	return $topics;
    }*/
    public static function getTopic($topic_id){
        $topics = DB::table("forum_topic")
                    ->select(["forum_topic_updown.value as updownstatus","forum_post_ideapoint.value as topicideapointstatus","forum_topic.*","users.first_name","users.last_name" ,
                        DB::raw("(SELECT sum(value) FROM forum_topic_updown as tempud WHERE tempud.topic_id = forum_topic.topic_id AND tempud.user_id = ". Auth::user()->id ." ) as votecount"),
                        DB::raw("(SELECT sum(value) FROM forum_post_ideapoint as tempud WHERE tempud.topic_id = forum_topic.topic_id AND tempud.user_id = ". Auth::user()->id ." ) as idepointcount"),
                        ])
                    ->leftJoin('forum_topic_updown', function($join){
                        $join->on('forum_topic_updown.user_id', '=', DB::raw(Auth::user()->id) );
                        $join->on('forum_topic_updown.topic_id', '=', 'forum_topic.topic_id' );
                     })
                    ->leftJoin('forum_post_ideapoint', function($join){
                        $join->on('forum_post_ideapoint.user_id', '=', DB::raw(Auth::user()->id) );
                        $join->on('forum_post_ideapoint.topic_id', '=', 'forum_topic.topic_id' );
                     })
                    ->join('users', 'users.id', '=', 'forum_topic.user_id')
                    ->where("forum_topic.topic_id","=",$topic_id)
                    ->get();
        return $topics;
    }
    public static function getPost($filter,$paginate = false){
    	$limit = $filter['limit'];
    	$extraWhere = array();
        if(isset($filter['unit_id'])){
            $unit =  DB::table("forum_topic")
                ->select("topic_id")
                ->where("unit_id","=",$filter['unit_id'])
                ->where("section_id","=",$filter['section_id'])
                ->where("object_id","=",$filter['object_id'])
                ->get();

            if(empty($unit)){
                return array("items"=>array(),"topic_id" => 0,"slug"=>'');
            }
            $filter['topic_id'] = $unit[0]->topic_id;
            $topics_['topic_id'] = $unit[0]->topic_id;
        }
        if(isset($filter['postId'])){
            $extraWhere[] = array("forum_post.post_id","=",$filter['postId']);
        }
        if(isset($filter['topic_id'])){
            $extraWhere[] = array("forum_post.topic_id","=",$filter['topic_id']);
        }
        if(isset($filter['parent'])){
            $extraWhere[] = array("forum_post.reply_id","=",$filter['parent']);
        }

    	$topics = DB::table("forum_post")
    				->select(["forum_ideapoint.value as ideapoint","forum_updown.value as updown","forum_post.*","users.first_name","users.last_name",
    					DB::raw("(SELECT count(*) FROM forum_post as temp WHERE forum_post.post_id=temp.reply_id ) as reply"),
    					DB::raw("(SELECT count(*) FROM forum_ideapoint as tempip WHERE forum_post.post_id=tempip.post_id ) as ideascore"),
    					DB::raw("IFNULL((SELECT sum(value) FROM forum_updown as tempfup WHERE  tempfup.post_id = forum_post.post_id ),0) as updownpoint")
    				])
    				->join('users', 'users.id', '=', 'forum_post.user_id')
    				->leftJoin('forum_updown', function($join){
    					$join->on('forum_updown.user_id', '=', DB::raw(Auth::user()->id) );
    					$join->on('forum_updown.post_id', '=', 'forum_post.post_id' );
					 })
    				->leftJoin('forum_ideapoint', function($join){
    					$join->on('forum_ideapoint.user_id', '=', DB::raw(Auth::user()->id) );
    					$join->on('forum_ideapoint.post_id', '=', 'forum_post.post_id' );
					 })
    				->where($extraWhere)
    				->orderBy("forum_post.post_id", isset($filter['orderBy']) ? $filter['orderBy'] : "ASC");
        $topics_['items'] = array();
        if($paginate){
            $topics = $topics->get();
        }
        else
        {
            $topics = $topics->offset( ($filter['page']-1) * $limit)->limit($limit)->get();
            if(isset($filter['topic_id'])){
                $slug = DB::table("forum_topic")->select("slug")->where("topic_id","=",$filter['topic_id'])->get();
                $topics_['slug'] = $slug[0]->slug;
            }
        }
        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

        foreach ($topics as $key => $post) {
            $filter['parent'] = $post->post_id;
            $user_id = $userIDHashID->encode($post->user_id);
            $topics_['items'][] = array(
                'post_id'       => $post->post_id,
                'post'          => isset($filter['htmldecode']) ? strip_tags($post->post) :  $post->post,
                'user_id'       => $post->user_id,
                'topic_id'      => $post->topic_id,
                'created_time'  => Carbon::createFromFormat('Y-m-d H:i:s', $post->created_time)->diffForHumans(),
                'modify_time'   => $post->modify_time,
                'first_name'    => $post->first_name,
                'last_name'     => $post->last_name,
                'reply'        => $post->reply,
                'updown'        => $post->updown,
                'updownpoint'   => $post->updownpoint,
                'reply_id'     => $post->reply_id,
                'ideapoint'     => $post->ideapoint,
                'ideascore'     => $post->ideascore,
                'link'          => url('userprofiles/'. $user_id .'/'.strtolower($post->first_name.'_'.$post->last_name)),
                'child' =>   isset($filter['noChild']) ? '' : ($post->reply ? Forum::getPost($filter,true) : array()),
            );
        }

    	return $topics_;
    }
    public static function getPostCount($filter){
    	return  DB::table("forum_post")
    				->where("forum_post.reply_id","=",$filter['parent'])
    				->where("forum_post.topic_id","=",$filter['topic_id'])
    				->count();
    }
    public static function getUnitId($topic_id){
        $data =  DB::table("forum_topic")
                    ->select(['forum_topic.unit_id','forum_topic.slug','forum_topic.title','units.name','users.first_name','users.last_name','forum_topic.user_id'])
                    ->join("units", "units.id" ,"=", "forum_topic.unit_id" )
                    ->join("users", "users.id" ,"=", "forum_topic.user_id" )
                    ->where("forum_topic.topic_id","=",$topic_id)
                    ->get();
        if(!empty($data)){
            return (array)$data[0];
        }
        return  0;
    }
    public static function submit($data)
    {
    	$inputData = array(
    		'title' => $data['title'],
		    'desc' => $data['desc'],
		    'user_id' => Auth::user()->id,
		    'unit_id' => $data['unit_id'],
            'slug' => $data['slug'],
            'object_id' => isset($data['object_id']) ? $data['object_id']  : 0,
		    'section_id' => $data['section_id'],
		    'created_time' => date("Y-m-d H:i:s"),
		    'modify_time' => date("Y-m-d H:i:s"),
    	);
    	$topicId = DB::table('forum_topic')->insertGetId($inputData);

    	return $topicId;
    }
    public static function postSubmit($data)
    {
    	$inputData = array(
		    'post' => $data['post'],
		    'user_id' => Auth::user()->id,
		    'topic_id' => $data['topic_id'],
		    'reply_id' => $data['reply_id'],
		    'created_time' => date("Y-m-d H:i:s"),
		    'modify_time' => date("Y-m-d H:i:s"),
    	);
    	$postId = DB::table('forum_post')->insertGetId($inputData);
    	return $postId;
    }
    public static function postUpDownCount($data){
        return  DB::table("forum_updown")
                    ->where("forum_updown.post_id","=",$data['post_id'])
                    ->sum("value");
    }
    public static function postUpDown($data)
    {
    	DB::table('forum_updown')
    		->where('post_id', '=', $data['post_id'])
    		->where('user_id', '=', Auth::user()->id)
    		->delete();
    	if($data['didIt'] == 'false'){
	    	$inputData = array(
			    'post_id' => $data['post_id'],
			    'user_id' => Auth::user()->id,
			    'value' => (int)$data['val'] == 0 ? -1 : 1,
			    'datetime' => date("Y-m-d H:i:s"),
	    	);
            $postUser = DB::table("forum_post")->select(["user_id"])->where("post_id","=",$data['post_id'])->get();
            if(!empty($postUser)){
              // add activity point for created unit and user.
                ActivityPoint::create([
                    'user_id'=>Auth::user()->id,
                    'unit_id'=>0,
                    'points'=>(int)$data['val'] == 0 ? -1 : 1,
                    'comments'=>'Forum comments',
                    'type'=>'forum'
                ]);
            }
	    	$updownId = DB::table('forum_updown')->insertGetId($inputData);
	    }
    	return true;
    }
    public static function topicUpDownCount($topic_id){
        return DB::table('forum_topic_updown')
            ->where('topic_id', '=', $topic_id)
            ->sum('value');
    }
    public static function topicUpDown($data)
    {
    	DB::table('forum_topic_updown')
    		->where('topic_id', '=', $data['topic_id'])
    		->where('user_id', '=', Auth::user()->id)
    		->delete();
    	if($data['didIt'] == 'false'){
	    	$inputData = array(
			    'topic_id' => $data['topic_id'],
			    'user_id' => Auth::user()->id,
			    'value' => (int)$data['val'] == 0 ? -1 : 1,
			    'datetime' => date("Y-m-d H:i:s"),
	    	);

	    	$updownId = DB::table('forum_topic_updown')->insertGetId($inputData);
	    }
    	return true;
    }

    public static function ideapoint($data)
    {
        DB::table('forum_ideapoint')
            ->where('post_id', '=', $data['post_id'])
            ->where('user_id', '=', Auth::user()->id)
            ->delete();
        if((int)$data['val'] == 0){
            $inputData = array(
                'post_id' => $data['post_id'],
                'user_id' => Auth::user()->id,
                'value' => 1,
                'datetime' => date("Y-m-d H:i:s"),
            );
            DB::table('forum_ideapoint')->insertGetId($inputData);
        }
        $postUser = DB::table("forum_post")->select(["user_id"])->where("post_id","=",$data['post_id'])->get();
        if(!empty($postUser)){
            ActivityPoint::create([
                'user_id'=> $postUser[0]->user_id,
                'unit_id'=>0,
                'points'=>(int)$data['val'] == 0 ? 1 : -1,
                'comments'=>'Forum Idea Point',
                'type'=>'forum'
            ]);
        }
        return true;
    }
    public static function post_ideapoint($data)
    {
    	DB::table('forum_post_ideapoint')
    		->where('topic_id', '=', $data['topic_id'])
    		->where('user_id', '=', Auth::user()->id)
    		->delete();
    	if((int)$data['val'] == 0){
	    	$inputData = array(
			    'topic_id' => $data['topic_id'],
			    'user_id' => Auth::user()->id,
			    'value' => 1,
			    'datetime' => date("Y-m-d H:i:s"),
	    	);
	    	DB::table('forum_post_ideapoint')->insertGetId($inputData);
	    }
        $postUser = DB::table("forum_topic")->select(["user_id"])->where("topic_id","=",$data['topic_id'])->get();
        if(!empty($postUser)){
            ActivityPoint::create([
                'user_id'=> $postUser[0]->user_id,
                'unit_id'=>0,
                'points'=>(int)$data['val'] == 0 ? 1 : -1,
                'comments'=>'forum_topic',
                'type'=>'forum'
            ]);
        }
    	return true;
    }

    public static function getUserOfReply($reply_id){
        $data =  DB::table("forum_post")
            ->select(['users.first_name','users.last_name','users.email','users.id'])
            ->join("users", "users.id" ,"=", "forum_post.user_id" )
            ->where("forum_post.post_id","=",$reply_id)
            ->get();
        if(!empty($data)){
            return $data[0];
        }
        return  0;
    }
}
