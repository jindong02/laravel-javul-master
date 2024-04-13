<?php
namespace App\Http\Controllers;
use App\Models\TaskRatings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserWiki;
use App\Models\User;
use App\Models\Unit;
use App\Models\Objective;
use App\Models\Task;
use App\Models\ActivityPoint;
use App\Models\SiteActivity;
use Hashids\Hashids;
use Carbon\Carbon;
use App\Models\Wiki;
use App\Models\UserwikiRevisions;
use App\Models\JobSkill;
use App\Models\AreaOfInterest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class UserWikiController extends Controller
{
    public function page_history(Request $request,$slug,$user_id,$page_id = false)
    {
    	view()->share('user_id_hash',$user_id);
    	view()->share('slug',$slug);
        if(!empty($user_id)){
            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->decode($user_id);
            if(!empty($user_id)){
                $user_id = $user_id [0];
                $userObj = User::find($user_id);
                $unitsObj = Unit::with(['objectives','tasks'])->where('units.user_id',$user_id)->get();
                $objectivesObj = Objective::where('user_id',$user_id)->get();
                $tasksObj = Task::where('user_id',$user_id)->get();
                $activityPoints = ActivityPoint::where('user_id',$user_id)->sum('points');
                $activityPoints_forum = ActivityPoint::where('user_id',$user_id)->where('type','forum')->sum('points');
                $rating_points = TaskRatings::where('user_id',$user_id)->sum('quality_of_work');

                $site_activities = SiteActivity::where('user_id',$user_id)->take(10)->orderBy('created_at','desc')->get();
                $skills = [];
                if(!empty($userObj->job_skills))
                    $skills = JobSkill::whereIn('id',explode(",",$userObj->job_skills))->get();
                $interestObj = [];
                if(!empty($userObj->job_skills))
                    $interestObj = AreaOfInterest::whereIn('id',explode(",",$userObj->area_of_interest))->get();
               	if($page_id){
               		$userPageIDHashID= new Hashids('userpage id hash',10,Config::get('app.encode_chars'));
    				$page_id = $userPageIDHashID->decode($page_id);
    				if(!empty($page_id)){
    					$page_id = $page_id[0];
		                $userWikiRev = UserwikiRevisions::select(['userwiki_revisions.*','users.first_name','users.last_name'])
		                					->join('users', 'users.id', '=', 'userwiki_revisions.user_id')
		                                    ->where("userwiki_revisions.page_id","=",$page_id)
		                                    ->paginate(15);

                		view()->share('userPageIDHashID',$userPageIDHashID);
                		view()->share('userIDHashID',$userIDHashID);
                		view()->share('userWikiRev',$userWikiRev);
                		view()->share('Carbon',new Carbon);
                		view()->share('objectivesObj',$objectivesObj);
		                view()->share('tasksObj',$tasksObj);
		                view()->share('interestObj',$interestObj);
		                view()->share('skills',$skills);
		                view()->share('site_activities',$site_activities );
		                view()->share('activityPoints',$activityPoints);
		                view()->share('activityPoints_forum',$activityPoints_forum);
		                view()->share('userObj',$userObj);
		                view()->share('unitsObj',$unitsObj);
                        view()->share('rating_points',$rating_points);
		                return view('users.wiki.wiki_page_history');

	                }
               	}
            }
        }
        return view('errors.404');
    }
    public function recent_changes(Request $request,$slug,$user_id)
    {
    	view()->share('user_id_hash',$user_id);
    	view()->share('slug',$slug);
        if(!empty($user_id)){
            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->decode($user_id);
            if(!empty($user_id)){
                $user_id = $user_id [0];
                $userObj = User::find($user_id);
                $unitsObj = Unit::with(['objectives','tasks'])->where('units.user_id',$user_id)->get();
                $objectivesObj = Objective::where('user_id',$user_id)->get();
                $tasksObj = Task::where('user_id',$user_id)->get();
                $activityPoints = ActivityPoint::where('user_id',$user_id)->sum('points');
                $activityPoints_forum = ActivityPoint::where('user_id',$user_id)->where('type','forum')->sum('points');
                $rating_points = TaskRatings::where('user_id',$user_id)->sum('quality_of_work');

                $site_activities = SiteActivity::where('user_id',$user_id)->take(10)->orderBy('created_at','desc')->get();
                $skills = [];
                if(!empty($userObj->job_skills))
                    $skills = JobSkill::whereIn('id',explode(",",$userObj->job_skills))->get();
                $interestObj = [];
                if(!empty($userObj->job_skills))
                    $interestObj = AreaOfInterest::whereIn('id',explode(",",$userObj->area_of_interest))->get();

                $userWikiRev = UserwikiRevisions::select(['userwiki_revisions.*','users.first_name','users.last_name'])
                					->join('users', 'users.id', '=', 'userwiki_revisions.user_id')

                                    ->paginate(15);
                $userPageIDHashID= new Hashids('userpage id hash',10,Config::get('app.encode_chars'));

        		view()->share('userPageIDHashID',$userPageIDHashID);
        		view()->share('userIDHashID',$userIDHashID);
        		view()->share('userWikiRev',$userWikiRev);
        		view()->share('Carbon',new Carbon);
        		view()->share('objectivesObj',$objectivesObj);
                view()->share('tasksObj',$tasksObj);
                view()->share('interestObj',$interestObj);
                view()->share('skills',$skills);
                view()->share('site_activities',$site_activities );
                view()->share('activityPoints',$activityPoints);
                view()->share('activityPoints_forum',$activityPoints_forum);
                view()->share('userObj',$userObj);
                view()->share('unitsObj',$unitsObj);
                view()->share('rating_points',$rating_points);
                return view('users.wiki.wiki_page_recentchange');

            }
        }
        return view('errors.404');
    }
    public function page_diff(Request $request,$slug,$user_id,$rev1 = false , $rev2 = false )
    {
    	view()->share('user_id_hash',$user_id);
    	view()->share('slug',$slug);
        if(!empty($user_id)){
            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->decode($user_id);
            if(!empty($user_id)){
                $user_id = $user_id [0];
                $userObj = User::find($user_id);
                $unitsObj = Unit::with(['objectives','tasks'])->where('units.user_id',$user_id)->get();
                $objectivesObj = Objective::where('user_id',$user_id)->get();
                $tasksObj = Task::where('user_id',$user_id)->get();
                $activityPoints = ActivityPoint::where('user_id',$user_id)->sum('points');
                $activityPoints_forum = ActivityPoint::where('user_id',$user_id)->where('type','forum')->sum('points');
                $rating_points = TaskRatings::where('user_id',$user_id)->sum('quality_of_work');
                view()->share('rating_points',$rating_points);

                $site_activities = SiteActivity::where('user_id',$user_id)->take(10)->orderBy('created_at','desc')->get();
                $skills = [];
                if(!empty($userObj->job_skills))
                    $skills = JobSkill::whereIn('id',explode(",",$userObj->job_skills))->get();
                $interestObj = [];
                if(!empty($userObj->job_skills))
                    $interestObj = AreaOfInterest::whereIn('id',explode(",",$userObj->area_of_interest))->get();
               	if($rev1 ){

	                $userWikiRev = UserwikiRevisions::select(['userwiki_revisions.*'])
	                                    ->wherein("userwiki_revisions.id",[(int)$rev1])
	                                    ->get();
                    if($userWikiRev->count() == 1){
                        $userWikiRev2 = UserwikiRevisions::select(['userwiki_revisions.*'])
                                        ->where("userwiki_revisions.id",'<',(int)$rev1)
                                        ->where("userwiki_revisions.page_id",'=',(int)$rev1)
                                        ->get();
                        if($userWikiRev2->count() == 0){
                            $userWikiRev2 = UserwikiRevisions::select(['userwiki_revisions.*'])
                                        ->where("userwiki_revisions.id",'<',(int)$rev1)
                                        ->where("userwiki_revisions.page_id",'=',(int)$rev1)
                                        ->get();
                        }
                        else{
                            $userWikiRev[1] = $userWikiRev2[0];
                        }
                        if($userWikiRev2->count() == 0){
                            $userWikiRev[1] = $userWikiRev[0];
                        }
                        else
                        {
                            $userWikiRev[1] = $userWikiRev2[0];
                        }

                		view()->share('userWikiRev',$userWikiRev);
                		view()->share('objectivesObj',$objectivesObj);
		                view()->share('tasksObj',$tasksObj);
		                view()->share('interestObj',$interestObj);
		                view()->share('skills',$skills);
		                view()->share('site_activities',$site_activities );
		                view()->share('activityPoints',$activityPoints);
		                view()->share('activityPoints_forum',$activityPoints_forum);
		                view()->share('userObj',$userObj);
		                view()->share('unitsObj',$unitsObj);
		                return view('users.wiki.wiki_page_diff');
		            }
                    else if($userWikiRev->count() == 2){
                    }


               	}
                else if($rev1 && $rev2){

                    $userWikiRev = UserwikiRevisions::select(['userwiki_revisions.*'])
                                        ->wherein("userwiki_revisions.page_id",[(int)$rev1,(int)$rev2])
                                        ->get();
                    if($userWikiRev->count() == 2){

                        view()->share('userWikiRev',$userWikiRev);

                        view()->share('objectivesObj',$objectivesObj);
                        view()->share('tasksObj',$tasksObj);
                        view()->share('interestObj',$interestObj);
                        view()->share('skills',$skills);
                        view()->share('site_activities',$site_activities );
                        view()->share('activityPoints',$activityPoints);
                        view()->share('activityPoints_forum',$activityPoints_forum);
                        view()->share('userObj',$userObj);
                        view()->share('unitsObj',$unitsObj);
                        return view('users.wiki.wiki_page_diff');
                    }
                    else if($userWikiRev->count() == 1){
                    }


                }
            }
        }
        return view('errors.404');
    }
    public function page_create(Request $request,$slug,$user_id,$page_id = false)
    {
    	view()->share('user_id_hash',$user_id);
    	view()->share('slug',$slug);
        if(!empty($user_id)){
            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->decode($user_id);
            if(!empty($user_id)){
                $user_id = $user_id [0];
                $userObj = User::find($user_id);
                $unitsObj = Unit::with(['objectives','tasks'])->where('units.user_id',$user_id)->get();
                $objectivesObj = Objective::where('user_id',$user_id)->get();
                $tasksObj = Task::where('user_id',$user_id)->get();
                $activityPoints = ActivityPoint::where('user_id',$user_id)->sum('points');
                $activityPoints_forum = ActivityPoint::where('user_id',$user_id)->where('type','forum')->sum('points');
                $rating_points = TaskRatings::where('user_id',$user_id)->sum('quality_of_work');

                $site_activities = SiteActivity::where('user_id',$user_id)->take(10)->orderBy('created_at','desc')->get();
                $skills = [];
                if(!empty($userObj->job_skills))
                    $skills = JobSkill::whereIn('id',explode(",",$userObj->job_skills))->get();
                $interestObj = [];
                if(!empty($userObj->job_skills))
                    $interestObj = AreaOfInterest::whereIn('id',explode(",",$userObj->area_of_interest))->get();
               	if($page_id){
               		$userPageIDHashID= new Hashids('userpage id hash',10,Config::get('app.encode_chars'));
    				$page_id = $userPageIDHashID->decode($page_id);
    				if(!empty($page_id)){
    					$page_id = $page_id[0];
		                $userWiki = UserWiki::select(['*'])
		                                    ->where("user_id","=",$user_id)
		                                    ->where("id","=",$page_id)
		                                    ->get();
		                if($userWiki->count()){
                            if($userWiki[0]->user_id == Auth::user()->id){
                                view()->share('userWiki',$userWiki[0]);
                            }
	                	}
	                }
               	}

                view()->share('objectivesObj',$objectivesObj);
                view()->share('tasksObj',$tasksObj);
                view()->share('interestObj',$interestObj);
                view()->share('skills',$skills);
                view()->share('site_activities',$site_activities );
                view()->share('activityPoints',$activityPoints);
                view()->share('activityPoints_forum',$activityPoints_forum);
                view()->share('userObj',$userObj);
                view()->share('unitsObj',$unitsObj);
                view()->share('rating_points',$rating_points);
                return view('users.wiki.wiki_form');
            }
        }
        return view('errors.404');
    }
    public function save_pagedata(Request $request,$user_id)
    {
    	$json = array();
    	$inputData = $request->all();
    	if((int)$inputData['id'] > 0){
    		$wikipage = UserWiki::find($inputData['id']);
    		if($wikipage->user_id == Auth::user()->id ){
                if($wikipage->page_type == 1){
        			$validation_rule['description'] = 'required';
    		    	$validation_rule['title'] = 'required';
    		    	$validation_rule['id'] = 'required';

    		    	$validator = Validator::make($inputData, $validation_rule );
    		    	if ($validator->fails()){
    		            return json_encode(array(
    			            'errors' => $validator->getMessageBag()->toArray()
    			        ), 200);
    			    }
    			    /* Store Old Data Start */
    			    $bytes = Wiki::strBytes( str_replace(' ', '', strip_tags($inputData['description'])) );
                	$oldBytes = Wiki::strBytes( str_replace(' ', '', strip_tags($wikipage->page_content)) );
    			    $UserwikiRevisions = new UserwikiRevisions;
    			    $UserwikiRevisions->page_id =  $wikipage->id;
    			    $UserwikiRevisions->user_id =  $wikipage->user_id;
    			    $UserwikiRevisions->page_title =  $wikipage->page_title;
    			    $UserwikiRevisions->page_content =  $wikipage->page_content;
    			    $UserwikiRevisions->comment =  $wikipage->comment;
    			    $UserwikiRevisions->slug =  $wikipage->slug;
    			    $UserwikiRevisions->private =  $wikipage->private;
    			    $UserwikiRevisions->size = (  $bytes - $oldBytes ) ;
    			    $UserwikiRevisions->modify_by =  Auth::user()->id;
    			    $UserwikiRevisions->page_type =  $wikipage->page_type;
    			    $UserwikiRevisions->save();
    			    /* Store Old Data End */
        			$wikipage->page_content = $inputData['description'];
    			    $wikipage->page_title = $inputData['title'];
    			    $wikipage->comment = $inputData['edit_comment'];
    			    $wikipage->private = (int)$inputData['private'];
    			    $wikipage->page_type = 1;
    			    $wikipage->slug = substr(str_replace(" ","-",strtolower($inputData['title'])),0,30);
    			    $wikipage->save();
                    $userPageIDHashID= new Hashids('userpage id hash',10,Config::get('app.encode_chars'));
                    $page_id = $userPageIDHashID->encode($inputData['id']);
                    $json['location'] = route("user_wiki_view",[$inputData['slug'],$page_id,$wikipage->slug]);
                }
                else if($wikipage->page_type == 2){
                    $validation_rule['description'] = 'required';
                    $validation_rule['id'] = 'required';
                    $validator = Validator::make($inputData, $validation_rule );
                    if ($validator->fails()){
                        return json_encode(array(
                            'errors' => $validator->getMessageBag()->toArray()
                        ), 200);
                    }
                    $wikipage->page_content = $inputData['description'];
                    $wikipage->private = 1;
                    $wikipage->save();
                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);
                    $json['location'] = url("userprofiles")."/".$user_id."/".$inputData['slug'];
                }
			    $json['success'] = 'User Wiki Creatd Successfully ..';
    		}
    		else
    		{
    			$json['error'] = "Not allow to edit this page";
    		}
    	}
    	else {
	    	$validation_rule['description'] = 'required';
	    	$validation_rule['title'] = 'required';
	    	$validator = Validator::make($inputData, $validation_rule );
	    	if ($validator->fails()){
	            return json_encode(array(
		            'errors' => $validator->getMessageBag()->toArray()
		        ), 200);
		    }
		    $wikipage =  new UserWiki;
		    $wikipage->page_content = $inputData['description'];
		    $wikipage->page_title = $inputData['title'];
		    $wikipage->comment = $inputData['edit_comment'];
		    $wikipage->private = (int)$inputData['private'];
		    $wikipage->page_type = 1;
		    $wikipage->slug = substr(str_replace(" ","-",strtolower($inputData['title'])),0,30);
		    $wikipage->user_id = Auth::user()->id;
		    $wikipage->save();

		    $json['success'] = 'User Wiki Creatd Successfully ..';
		    $json['location'] = route("user_wiki_page_list",[$inputData['slug'],$user_id]);
		}
	    echo json_encode($json);
    }

    public function pagelist(Request $request,$slug,$user_id)
    {
    	view()->share('user_id_hash',$user_id);
    	view()->share('slug',$slug);
        if(!empty($user_id)){
            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->decode($user_id);
            if(!empty($user_id)){
                $user_id = $user_id [0];
                $userObj = User::find($user_id);
                $unitsObj = Unit::with(['objectives','tasks'])->where('units.user_id',$user_id)->get();
                $objectivesObj = Objective::where('user_id',$user_id)->get();
                $tasksObj = Task::where('user_id',$user_id)->get();
                $activityPoints = ActivityPoint::where('user_id',$user_id)->sum('points');
                $activityPoints_forum = ActivityPoint::where('user_id',$user_id)->where('type','forum')->sum('points');
                $rating_points = TaskRatings::where('user_id',$user_id)->sum('quality_of_work');

                $site_activities = SiteActivity::where('user_id',$user_id)->take(10)->orderBy('created_at','desc')->get();
                $skills = [];
                if(!empty($userObj->job_skills))
                    $skills = JobSkill::whereIn('id',explode(",",$userObj->job_skills))->get();
                $interestObj = [];
                if(!empty($userObj->job_skills))
                    $interestObj = AreaOfInterest::whereIn('id',explode(",",$userObj->area_of_interest))->get();
                if(Auth::check() && $user_id == Auth::user()->id){
                    $userWikiPage = UserWiki::select(['page_title','id','updated_at','slug'])
                                    ->where("user_id","=",$user_id)
                                    ->where("page_type","=","1")
                                    //->toSql();
                                    ->paginate(15);
                }
                else
                {
                    $userWikiPage = UserWiki::select(['page_title','id','updated_at','slug'])
                                    ->where("user_id","=",$user_id)
                                    ->where("page_type","=","1")
                                    ->where("private","=","0")
                                    //->toSql();
                                    ->paginate(15);
                }

                $userPageIDHashID= new Hashids('userpage id hash',10,Config::get('app.encode_chars'));
                view()->share('userPageIDHashID',$userPageIDHashID);
                view()->share('userWikiPage',$userWikiPage);
                view()->share('Carbon',new Carbon);

                view()->share('objectivesObj',$objectivesObj);
                view()->share('tasksObj',$tasksObj);
                view()->share('interestObj',$interestObj);
                view()->share('skills',$skills);
                view()->share('site_activities',$site_activities );
                view()->share('activityPoints',$activityPoints);
                view()->share('activityPoints_forum',$activityPoints_forum);
                view()->share('userObj',$userObj);
                view()->share('unitsObj',$unitsObj);
                view()->share('rating_points',$rating_points);
                return view('users.wiki.wiki_page_list');
            }
        }
        return view('errors.404');
    }
    public function view(Request $request,$user_slug,$page_id,$page_slug){

    	view()->share('slug',$user_slug);
    	view()->share('page_id_hase',$page_id);
    	$userPageIDHashID= new Hashids('userpage id hash',10,Config::get('app.encode_chars'));
    	$page_id = $userPageIDHashID->decode($page_id);
    	if(!empty($page_id)){
    		$page_id = $page_id[0];
    		$userWikiPage = UserWiki::select(['private','page_title','page_content','id','updated_at','slug','user_id'])
                                    	->where("id","=",$page_id)
                                    	->where("page_type","=","1")
                                    	->get();

			if($userWikiPage->count()){
				$pageObj=$userWikiPage[0];
				$user_id = $pageObj->user_id;

				$userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        		$user_id_hash = $userIDHashID->encode($user_id);
        		view()->share('user_id_hash',$user_id_hash);

				$userObj = User::find($user_id);
                $unitsObj = Unit::with(['objectives','tasks'])->where('units.user_id',$user_id)->get();
                $objectivesObj = Objective::where('user_id',$user_id)->get();
                $tasksObj = Task::where('user_id',$user_id)->get();
                $activityPoints = ActivityPoint::where('user_id',$user_id)->sum('points');
                $activityPoints_forum = ActivityPoint::where('user_id',$user_id)->where('type','forum')->sum('points');
                $rating_points = TaskRatings::where('user_id',$user_id)->sum('quality_of_work');

                $site_activities = SiteActivity::where('user_id',$user_id)->take(10)->orderBy('created_at','desc')->get();
                $skills = [];
                if(!empty($userObj->job_skills))
                    $skills = JobSkill::whereIn('id',explode(",",$userObj->job_skills))->get();
                $interestObj = [];
                if(!empty($userObj->job_skills))
                    $interestObj = AreaOfInterest::whereIn('id',explode(",",$userObj->area_of_interest))->get();
               	$pageObj->page_content =  html_entity_decode(Wiki::parse($pageObj->page_content), ENT_QUOTES, 'UTF-8') ;
                view()->share('pageObj',$pageObj);
                //view()->share('Carbon',new Carbon);

                view()->share('objectivesObj',$objectivesObj);
                view()->share('tasksObj',$tasksObj);
                view()->share('interestObj',$interestObj);
                view()->share('skills',$skills);
                view()->share('site_activities',$site_activities );
                view()->share('activityPoints',$activityPoints);
                view()->share('activityPoints_forum',$activityPoints_forum);
                view()->share('userObj',$userObj);
                view()->share('unitsObj',$unitsObj);
                view()->share('rating_points',$rating_points);
                if(Auth::check() && $user_id != Auth::user()->id && $pageObj->private == 1 ){
                    return view("users.wiki.private");
                }
                return view('users.wiki.wiki_page_view');
			}

    	}

        return view('errors.404');
    }
    public function home()
    {

    }
}
