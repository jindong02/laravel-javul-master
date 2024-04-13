<?php

namespace App\Http\Controllers;

use App\Models\AreaOfInterest;
use App\Models\AreaOfInterestHistory;
use App\Models\Issue;
use App\Models\JobSkill;
use App\Models\JobSkillHistory;
use App\Models\Objective;
use App\Models\SiteActivity;
use App\Models\Task;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitCategoryHistory;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;
//use PayPal\Service\AdaptivePaymentsService;
//use PayPal\Types\AP\PaymentDetailsRequest;
//use PayPal\Types\AP\PayRequest;
//use PayPal\Types\AP\Receiver;
//use PayPal\Types\AP\ReceiverList;
//use PayPal\Types\Common\RequestEnvelope;
use App\Models\UserMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',['except'=>['index','add_to_watchlist','get_unit_site_activity_paginate','get_site_activity_paginate',
            'global_activities','get_categories','browse_categories','get_next_level_categories','global_search','check_username','check_email','skill_view']]);
    }

    public function index()
    {
        $recentUnits = Unit::take(5)->orderBy('created_at','desc')->get();
        $recentObjectives = Objective::take(5)->orderBy('created_at','desc')->get();
        $recentTasks= Task::take(5)->orderBy('created_at','desc')->get();
        $recentUsers= User::take(5)->orderBy('created_at','desc')->get();

        view()->share('recentUnits',$recentUnits);
        view()->share('recentObjectives',$recentObjectives);
        view()->share('recentTasks',$recentTasks);
        view()->share('recentUsers',$recentUsers);
        view()->share('site_activity_text','Global Activity Log');

        $featured_unit = Unit::where('featured_unit',1)->first();
        view()->share('featured_unit',$featured_unit);

        $site_activity = [];
//        $site_activity = SiteActivity::orderBy('created_at','desc')->paginate(Config::get('app.site_activity_page_limit'));
        view()->share('site_activity',$site_activity);

        return view('home');
    }

    public function global_activities(){
        $activities = SiteActivity::orderBy('id','desc')->paginate(Config::get('app.global_site_activity_page'));
        view()->share('site_activity',$activities);
        view()->share('site_activity_text','Global Activity Log');
        return view('global_activities',['type'=>'activities']);
    }

    public function global_search(Request $request)
    {

        $search_word = trim($request->input('search_term'));
        if(!empty(trim($search_word))){
            $unitObj = Unit::where('name','like', '%'.$search_word.'%')->get();
            view()->share('unitObj',$unitObj);

            $objectiveObj = Objective::where('name','like', '%'.$search_word.'%')->get();
            view()->share('objectivesObj',$objectiveObj);

            $taskObj = Task::where('name','like', '%'.$search_word.'%')->get();
            view()->share('taskObj',$taskObj);

            $issueObj = Issue::where('title','like', '%'.$search_word.'%')->get();
            view()->share('issueObj',$issueObj);

            view()->share('site_activity_text','Global Activity Log');
            $site_activity = SiteActivity::orderBy('created_at','desc')->paginate(Config::get('app.site_activity_page_limit'));
            view()->share('site_activity',$site_activity);

            view()->share('search_word',$search_word);
            return view('global_search');
        }else{
            $request->session()->flash('msg_val', 'PLEASE_ENTER_VALID_SEARCH_TERM');
            return redirect()->back();
        }
    }

	public function check_username(Request $check)
    {
        $name=$check->get('check');
        $user_count = User::where('username',$name)->count();

        $string=preg_match("/[\s^]*(admin|site|javul|administration)/i",$name);
        if($string || $user_count > 0)
			return response()->json(['success'=>true]);

        return response()->json(['success'=>false]);
    }

    /**
     * Validate email address
     */
    public function check_email(Request $request)
    {
        if($request->has('email'))
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users,email,'
            ]);
            if ($validator->fails())
                return response()->json(['success'=>true,'error'=>$validator->errors(['email'])->all()]);
            else
                return response()->json(array('success'=>false));
        }
    }

    public function get_unit_site_activity_paginate(Request $request)
    {
        $unit_id = $request->input('unit_id');

        if(!empty($unit_id)){
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                view()->share('site_activity',$site_activity);
                view()->share('site_activity_text','Unit Activity Log');
                view()->share('unit_activity_id',$unit_id);
                view()->share('ajax',true);
                $html = view('elements.site_activities')->render();
                return response()->json(['success'=>true,'html'=>$html]);

            }
        }
        return response()->json(['success'=>false]);
    }

    public function get_site_activity_paginate(Request $request){
        $page_limit = Config::get('app.site_activity_page_limit');
        $site_activity_text= "Global Activity Log";
        if($request->has('from_page')){
            $page = $request->input('from_page');
            if($page == "global_activity") {
                $page_limit = Config::get('app.global_site_activity_page');
                $site_activity = SiteActivity::orderBy('id','desc')->paginate($page_limit);
            }else{
                $page_limit = Config::get('app.global_site_activity_page');
                $site_activity = SiteActivity::where('user_id',Auth::user()->id)->orderBy('id','desc')->paginate($page_limit);
                $site_activity_text= "Unit Activity Log";
                view()->share('unit_activity_id','');
            }
        }
        else{
            $site_activity = SiteActivity::orderBy('id','desc')->paginate($page_limit);
        }

        view()->share('site_activity',$site_activity);
        view()->share('site_activity_text',$site_activity_text);
        view()->share('ajax',true);
        $html = view('elements.site_activities')->render();
        return Response::json(['success'=>true,'html'=>$html]);
    }

    public function add_to_watchlist(Request $request)
    {
        if($request->method('ajax')) {
            $redirect_to = $request->input('sessionUrl');
            $request->session()->put('url.intended', $redirect_to);
            $data = array('type' => $request->input('type'), 'id' => $request->input('id'));
            $request->session()->put('add_to_wl', $data);

            if (Auth::check()) {
                $type = $request->input('type');
                $id = $request->input('id');
                $hashID = '';
                $obj = [];
                switch ($type) {
                    case 'unit':
                        $hashID = new Hashids('unit id hash', 10, Config::get('app.encode_chars'));
                        $id = $hashID->decode($id);
                        if (!empty($id)) {
                            $id = $id[0];
                            $obj = Unit::find($id);
                        }
                        break;
                    case 'objective':
                        $hashID = new Hashids('objective id hash', 10, Config::get('app.encode_chars'));
                        $id = $hashID->decode($id);
                        if (!empty($id)) {
                            $id = $id[0];
                            $obj = Objective::find($id);
                        }
                        break;
                    case 'task':
                        $hashID = new Hashids('task id hash', 10, Config::get('app.encode_chars'));
                        $id = $hashID->decode($id);
                        if (!empty($id)) {
                            $id = $id[0];
                            $obj = Task::find($id);
                        }
                        break;
                    case 'issue':
                        $hashID = new Hashids('issue id hash', 10, Config::get('app.encode_chars'));
                        $id = $hashID->decode($id);
                        if (!empty($id)) {
                            $id = $id[0];
                            $obj = Issue::find($id);
                        }
                        break;
                }
                if (!empty($obj)) {
                    $exist = Watchlist::where(strtolower($type) . '_id', $id)->where('user_id', Auth::user()->id)->get();
                    if (empty($exist) || count($exist) == 0) {
                        Watchlist::create([
                            'user_id' => Auth::user()->id,
                            strtolower($type) . '_id' => $id
                        ]);
                        return Response::json(['success'=>true,'msg'=>ucfirst($type).' added to watchlist.']);
                        $request->session()->forget('add_to_wl');
                    } else
                      return Response::json(['success'=>false,'msg'=>ucfirst($type).' already added to watchlist.']);
                }
                return Response::json(['success'=>false,'msg'=>ucfirst($type).' not found in database.']);
            }
            return Response::json(['success'=>false,'msg'=>'Please login to continue.']);
        }
        return view('errors.404');
    }

    public function my_watchlist(Request $request){
        $watchedUnits = Watchlist::join('units','my_watchlist.unit_id','=','units.id')
                        ->where('my_watchlist.user_id',Auth::user()->id)
                        ->whereNotNull('unit_id')->select(['units.*'])->get();
        $watchedObjectives = Watchlist::join('objectives','my_watchlist.objective_id','=','objectives.id')
                            ->where('my_watchlist.user_id',Auth::user()->id)
                            ->whereNotNull('objective_id')->select(['objectives.*'])->get();
        $watchedTasks = Watchlist::join('tasks','my_watchlist.task_id','=','tasks.id')
                        ->where('my_watchlist.user_id',Auth::user()->id)
                        ->whereNotNull('task_id')->select(['tasks.*'])->get();


        $watchedIssues = Watchlist::join( 'issues','my_watchlist.issue_id','=','issues.id')
            ->where('my_watchlist.user_id',Auth::user()->id)
            ->whereNotNull('issue_id')->select(['issues.*'])->get();



//        dd($watchedIssues);
        view()->share('watchedUnits',$watchedUnits);
        view()->share('watchedObjectives',$watchedObjectives);
        view()->share('watchedTasks',$watchedTasks);
        view()->share('watchedIssues',$watchedIssues);
        return view('users.my_watchlist');
    }

    public function my_alerts(Request $request){
        $notifications= UserNotification::where('user_id',Auth::user()->id)->orderBy('message_read')->orderBy('created_at','desc')->paginate(Config::get('app.global_site_activity_page'));
        view()->share('site_activity',$notifications);
        view()->share('site_activity_text','Notifications');
        return view('global_activities',['type'=>'notifications']);
    }

    public function paypal(Request $request){

    }

    public function site_admin(Request $request){
        if(!Auth::check())
//            return \Redirect::to(url(''));
            return redirect()->to('');
        $featuredUnit = [];
        $unitList = [];
        $where = '';
        if(Auth::user()->role != "superadmin")
            $where=" AND user_id=".Auth::user()->id;
        else {
            //$featuredUnit = Unit::where('featured_unit', 1)->first();
            $unitList  = Unit::all();
        }

        view()->share('unitList',$unitList);
        view()->share('featuredUnit',$featuredUnit);


        //get skills
        $jobSkillsObj = DB::select('SELECT c.id, IF(ISNULL(c.parent_id), 0, c.parent_id) AS parent_id,c.skill_name,   p.skill_name AS Parentskill_name,IF(ISNULL(job_skills_history.`skill_name`),NULL,job_skills_history.`skill_name`) AS history_skill_name
                                    ,IF(ISNULL(job_skills_history.`prefix_id`),NULL,job_skills_history.`prefix_id`) AS prefix_id,IF(ISNULL(job_skills_history.`user_id`),NULL,job_skills_history.`user_id`) AS user_id
                                    FROM job_skills c LEFT JOIN job_skills p ON (c.parent_id = p.id) LEFT JOIN job_skills_history ON
                                    c.id=job_skills_history.`job_skill_id`'.$where.' WHERE IF(c.parent_id IS NULL, 0, c
                                    .parent_id) = 0 AND c.id <> 0 ORDER BY  c.id');

        $firstBox_skills = [];
        $need_approve_skills = [];
        if(count($jobSkillsObj) > 0 && !empty($jobSkillsObj)){
            foreach($jobSkillsObj as $skill){
                if(!empty($skill->history_skill_name) && $skill->user_id == Auth::user()->id)
                    $firstBox_skills[$skill->prefix_id]=['type'=>'old','name'=>$skill->history_skill_name];
                else
                    $firstBox_skills[$skill->id]=['type'=>'old','name'=>$skill->skill_name];
            }
        }

        //get unit categories
        $unitCategoriesObj = DB::select('SELECT c.id, IF(ISNULL(c.parent_id), 0, c.parent_id) AS parent_id, c.name, p.name AS Parentcategory_name,
                                          IF(ISNULL(unit_category_history.`name`),NULL,unit_category_history.`name`) AS history_category_name,
                                          IF(ISNULL(unit_category_history.`prefix_id`),NULL,unit_category_history.`prefix_id`) AS prefix_id,
                                          IF(ISNULL(unit_category_history.`user_id`),NULL,unit_category_history.`user_id`) AS user_id
                                          FROM  unit_category c LEFT JOIN unit_category p ON (c.parent_id = p.id)
                                          LEFT JOIN unit_category_history ON c.id = unit_category_history.`unit_category_id` '.$where.'
                                          WHERE IF(c.parent_id IS NULL, 0, c.parent_id) = 0  AND c.id <> 0 ORDER BY c.id ');

        $firstBox_category = [];
        $need_approve_categories = [];
        if(count($unitCategoriesObj) > 0 && !empty($unitCategoriesObj)){
            foreach($unitCategoriesObj as $category){
                if(!empty($category->history_category_name) && $category->user_id == Auth::user()->id)
                    $firstBox_category[$category->prefix_id]=['type'=>'old','name'=>$category->history_category_name];
                else
                    $firstBox_category[$category->id]=['type'=>'old','name'=>$category->name];
            }
        }


        //get area of interest
        $area_of_interestObj= DB::select('SELECT c.id, IF(ISNULL(c.parent_id), 0, c.parent_id) AS parent_id, c.title, p.title AS Parenttitle,
                                          IF(ISNULL(area_of_interest_history.`title`),NULL,area_of_interest_history.`title`) AS
                                          history_area_of_interest_name,
                                          IF(ISNULL(area_of_interest_history.`prefix_id`),NULL,area_of_interest_history.`prefix_id`) AS prefix_id,
                                          IF(ISNULL(area_of_interest_history.`user_id`),NULL,area_of_interest_history.`user_id`) AS user_id
                                          FROM  area_of_interest c LEFT JOIN area_of_interest p ON (c.parent_id = p.id)
                                          LEFT JOIN area_of_interest_history ON c.id = area_of_interest_history.`area_of_interest_id`'.$where.'
                                          WHERE IF(c.parent_id IS NULL, 0, c.parent_id) = 0  AND c.id <> 0 ORDER BY c.id ');

        $firstBox_areaOfInterest = [];
        $need_approve_areaOfInterest = [];
        if(count($area_of_interestObj) > 0 && !empty($area_of_interestObj)){
            foreach($area_of_interestObj as $area_of_interest){
                if(!empty($area_of_interest->history_area_of_interest_name) && $area_of_interest->user_id == Auth::user()->id)
                    $firstBox_areaOfInterest[$area_of_interest->prefix_id]=['type'=>'old','name'=>$area_of_interest->history_area_of_interest_name];
                else
                    $firstBox_areaOfInterest[$area_of_interest->id]=['type'=>'old','name'=>$area_of_interest->title];
            }
        }

        // also list the skill he added but yet not approved by siteadmin.
        if(Auth::user()->role != "superadmin") {
            //get pending skills of current user
            $pending_skills = JobSkillHistory::where('user_id', Auth::user()->id)->where('parent_id',0)->pluck('skill_name','prefix_id')
                ->all();
            if(count($pending_skills) > 0){
                foreach($pending_skills as $index=>$skl_nm)
                    $firstBox_skills[$index]=['type'=>'new','name'=>$skl_nm];
            }

            //get pending categories of current user
            $pending_categories = UnitCategoryHistory::where('user_id', Auth::user()->id)->where('parent_id',0)->pluck('name','prefix_id')->all();
            if(count($pending_categories) > 0){
                foreach($pending_categories as $index=>$cat_nm)
                    $firstBox_category[$index]=['type'=>'new','name'=>$cat_nm];
            }

            //get pending area of interest of current user
            $pending_areaofInterest = AreaOfInterestHistory::where('user_id', Auth::user()->id)->where('parent_id',0)->pluck('title','prefix_id')->all();
            if(count($pending_areaofInterest) > 0){
                foreach($pending_areaofInterest as $index=>$area_nm)
                    $firstBox_areaOfInterest[$index]=['type'=>'new','name'=>$area_nm];
            }
        }
        else {
            $need_approve_skills = JobSkillHistory::orderBy('action_type')->get();
            $need_approve_categories = UnitCategoryHistory::orderBy('action_type')->get();
            $need_approve_areaOfInterest= AreaOfInterestHistory::orderBy('action_type')->get();
        }

        view()->share('need_approve_skills',$need_approve_skills);
        view()->share('firstBox_skills',$firstBox_skills);

        view()->share('need_approve_categories',$need_approve_categories);
        view()->share('firstBox_category',$firstBox_category);

        view()->share('need_approve_areaOfInterest',$need_approve_areaOfInterest);
        view()->share('firstBox_areaOfInterest',$firstBox_areaOfInterest);

        $msg_flag = false;
        $msg_val = '';
        $msg_type = '';
        if($request->session()->has('msg_val')){
            $msg_val =  $request->session()->get('msg_val');
            $request->session()->forget('msg_val');
            $msg_flag = true;
            $msg_type = "success";
        }
        view()->share('msg_flag',$msg_flag);
        view()->share('msg_val',$msg_val);
        view()->share('msg_type',$msg_type);


        //view()->share('jobSkillsObj',$jobSkillsObj);
        //view()->share('categoriesObj',$categoriesObj);
        //view()->share('area_of_interestObj',$area_of_interestObj);

        $site_activity = SiteActivity::orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
        view()->share('site_activity',$site_activity);
        view()->share('site_activity_text','Global Activity Log');

        return view('admin.site_admin');
    }

    public function get_area_of_interest_paginate(Request $request){
        if(!Auth::check())
            return response()->json(['success'=>true,'html'=>'']);

        $page_limit = Config::get('app.page_limit');
        $areaOfInterestObj = AreaOfInterest::paginate($page_limit);
        view()->share('areaOfInterestObj',$areaOfInterestObj);
        $html = view('admin.partials.more_area_of_interest')->render();
        return response()->json(['success'=>true,'html'=>$html]);
    }
    public function get_skill_paginate(Request $request){
        if(!Auth::check())
            return response()->json(['success'=>true,'html'=>'']);

        $page_limit = Config::get('app.page_limit');
        $jobSkillObj = JobSkill::paginate($page_limit);
        view()->share('jobSkillObj',$jobSkillObj);
        $html = view('admin.partials.more_skills')->render();
        return response()->json(['success'=>true,'html'=>$html]);
    }

    public function category_add(Request $request)
    {
        if(!$request->ajax())
            return view('errors.404');

        if(!Auth::check())
            return response()->json(['success'=>false,'errors'=>['You are not authorized person to perform this action.']]);

        $validator = Validator::make($request->all(), [
            'category_name' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false,'errors'=>$validator->messages()]);

        $categoryExist = UnitCategory::whereRaw('LOWER(name) = "'.strtolower($request->input('category_name').'"'))->count();

        if($categoryExist > 0)
            return redirect()->back()->withErrors(['name'=>'Unit category already exists.'])->withInput();


        $parent_id = $request->input('parent_id');
        $temp_parent_id = $parent_id;

        if(empty($parent_id))
            $parent_id=0;
        else
            $parent_id = str_replace("UCH","",$parent_id);


        $data  = [
            'name'=>$request->input('category_name'),
            'parent_id'=>$parent_id
        ];

        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);


        $path_text = $request->input('path_text');
        $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
        if(!empty(Auth::user()->username))
            $loggedinUsername = Auth::user()->username;

        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
            .$loggedinUsername.'</a> added category <a href="'.url('site_admin').'">'
            .$data['name'].'</a>';
        if(!empty($path_text)){
            $html.=' to the <a href="'.url('site_admin').'">'.$path_text.'</a>';
        }


        if(Auth::user()->role =="superadmin") {
            $data['status'] = "approved";
            $category_id =UnitCategory::create($data)->id;
        }
        else{
            $type = $request->input('tbl_type');
            if($type == "null")
                $type =null;
            elseif($type == "old"){
                $unitCategoryHistoryObj = UnitCategoryHistory::where('prefix_id',$temp_parent_id)->first();
                if(!empty($unitCategoryHistoryObj) && count($unitCategoryHistoryObj) > 0 && !empty($unitCategoryHistoryObj->unit_category_id))
                    $data['parent_id'] = $unitCategoryHistoryObj->unit_category_id;
            }
            $data['user_id']=Auth::user()->id;
            $data['action_type']='add';
            $data['parent_id_belongs_to'] =$type;
            $data['category_hierarchy']=$path_text;
            $category_id = UnitCategoryHistory::create($data)->id;
        }
        SiteActivity::create([
            'user_id'=>Auth::user()->id,
            'comment'=>$html
        ]);
        return response()->json(['success'=>true,'category_id'=>$category_id,'category_name'=>$data['name']]);
    }

    /**
     * Showing selected skill with task list
     */
    public function skill_view(Request $request,$skill_id = false){
        if($skill_id){
            $jobSkillIDHashID = new Hashids('job skills id hash',10,Config::get('app.encode_chars'));
            $skill_id = $jobSkillIDHashID->decode($skill_id);
            if(!empty($skill_id)){
                $skill_id = $skill_id[0];
                $tasks = DB::table('tasks')
                        ->join('objectives','tasks.objective_id','=','objectives.id')
                        ->join('units','tasks.unit_id','=','units.id')
                        ->join('users','tasks.user_id','=','users.id')
                        ->select(['tasks.*','units.name as unit_name','users.first_name','users.last_name','users.id as user_id','objectives.name as objective_name'])
                        ->whereNull('tasks.deleted_at')
                        ->whereRaw('FIND_IN_SET(?,skills)',[$skill_id])
                        ->orderBy('tasks.id','desc')
                        ->paginate(Config::get('app.page_limit'));

                $site_activity = SiteActivity::orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));

                view()->share('msg_flag',false);
                view()->share('msg_val','');
                view()->share('msg_type','');
                view()->share('site_activity_text','Global Activity Log');

                view()->share('site_activity',$site_activity);
                view()->share('tasks',$tasks);
                return view('tasks.tasks');
            }
        }
        return view('errors.404');
    }

    public function skill_add(Request $request){
        if(!Auth::check() || !$request->ajax())
            return response()->json(['success'=>false,'errors'=>['You are not authorized person to perform this action.']]);

        $validator = Validator::make($request->all(), [
            'skill_name' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false,'errors'=>$validator->messages()]);

        $skillExist = JobSkill::whereRaw('LOWER(skill_name) = "'.strtolower($request->input('skill_name').'"'))->count();

        if($skillExist> 0)
            return response()->json(['success'=>false,'errors'=>['skill_name'=>'Skill name already exists']]);

        $parent_id = $request->input('parent_id');
        $temp_parent_id = $parent_id;

        if(empty($parent_id))
            $parent_id=0;
        else
            $parent_id = str_replace("JBSH","",$parent_id);


        $data  = [
            'skill_name'=>$request->input('skill_name'),
            'parent_id'=>$parent_id
        ];

        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);

        $skill_id = '';

        $path_text = $request->input('path_text');
        $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
        if(!empty(Auth::user()->username))
            $loggedinUsername = Auth::user()->username;
        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
            .$loggedinUsername.'</a> added skill <a href="'.url('site_admin').'">'
            .$data['skill_name'].'</a>';
        if(!empty($path_text)){
            $html.=' to the <a href="'.url('site_admin').'">'.$path_text.'</a>';
        }


        if(Auth::user()->role =="superadmin") {
            $data['status'] = "approved";
            $skill_id =JobSkill::create($data)->id;
        }
        else{
            $type = $request->input('tbl_type');
            if($type == "null")
                $type =null;
            elseif($type == "old"){
                $jobSkillHistoryObj = JobSkillHistory::where('prefix_id',$temp_parent_id)->first();
                if(!empty($jobSkillHistoryObj) && count($jobSkillHistoryObj) > 0 && !empty($jobSkillHistoryObj->job_skill_id))
                    $data['parent_id'] = $jobSkillHistoryObj->job_skill_id;
            }
            $data['user_id']=Auth::user()->id;
            $data['skill_name']=$request->input('skill_name');
            $data['action_type']='add';
            $data['parent_id_belongs_to'] =$type;
            $data['skill_hierarchy']=$path_text;
            $skill_id = JobSkillHistory::create($data)->id;
        }
        SiteActivity::create([
            'user_id'=>Auth::user()->id,
            'comment'=>$html
        ]);
        return response()->json(['success'=>true,'skill_id'=>$skill_id,'skill_name'=>$data['skill_name']]);
    }
    public function skill_edit(Request $request)
    {

        if (!Auth::check() || !$request->ajax())
            return response()->json(['success' => false, 'errors' => ['You are not authorized person to perform this action.']]);

        $validator = Validator::make($request->all(), [
            'skill_name' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()]);

        $selected_id = $request->input('selected_id');
        if (empty($selected_id))
            return response()->json(['success' => false, 'errors' => ['Something goes wrong please try again later.']]);

        $skill_name = $request->input('skill_name');
        $skillExist = JobSkill::whereRaw('LOWER(skill_name) = "'.$skill_name.'" and id !='.$selected_id)->count();

        if ($skillExist > 0)
            return response()->json(['success' => false, 'errors' => ['skill_name' => 'Skill name already exists']]);

        $type = $request->input('tbl_type');
        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);

        $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
        if(!empty(Auth::user()->username))
            $loggedinUsername = Auth::user()->username;

        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
            .$loggedinUsername.'</a> edited skill <a href="'.url('site_admin').'">'
            .$request->input('skill_name').'</a>';

        if(Auth::user()->role == "superadmin") {
            $job_skill_id= $selected_id;
            if(strpos($selected_id,"JBSH") !== false) {
                $job_skill_id_temp = JobSkillHistory::where('prefix_id', $selected_id)->first();
                if(!empty($job_skill_id_temp) && count($job_skill_id_temp) > 0 && !empty($job_skill_id_temp->job_skill_id))
                    $job_skill_id =$job_skill_id_temp->job_skill_id;
            }

            $jobSkillObj= JobSkill::find($job_skill_id );
            if(!empty($jobSkillObj) && count($jobSkillObj) > 0) {
                $jobSkillObj->update(['skill_name' => $request->input('skill_name')]);
                $html.=$jobSkillObj->skill_name.'</a>';
                if(!empty($path_text)){
                    $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                }
                SiteActivity::create([
                    'user_id'=>Auth::user()->id,
                    'comment'=>$html
                ]);
                return response()->json(['success'=>true,'skill_id'=>$jobSkillObj->id,'type'=>'old','skill_name'=>$request->input('skill_name')]);
            }
            return response()->json(['success'=>true,'msg'=>'Something goes wrong. Please try again later.']);
        }
        else {
            if ($type == "old") {
                $job_skill_id= $selected_id;
                if(strpos($selected_id,"JBSH") !== false) {
                    $job_skill_id = JobSkillHistory::where('prefix_id', $selected_id)->first();
                    if(!empty($job_skill_id) && count($job_skill_id) > 0)
                        $job_skill_id =$job_skill_id->job_skill_id;
                    $selected_id = str_replace("JBSH","",$selected_id);
                }
                $path_text = $request->input('path_text');

                $jobSkillObj= JobSkill::find($job_skill_id );
                if(count($jobSkillObj) > 0 && !empty($jobSkillObj)) {
                    $obj = JobSkillHistory::where('job_skill_id', $jobSkillObj->id)->where('user_id', Auth::user()->id)->first();
                    if (!empty($obj) && count($obj) > 0) {
                        $obj->delete();
                    }

                    $data['parent_id_belongs_to'] = null;
                    $data['job_skill_id'] = $selected_id;
                    $data['user_id'] = Auth::user()->id;
                    $data['skill_name'] = $request->input('skill_name');
                    $data['action_type'] = 'edit';
                    $data['skill_hierarchy']=$path_text;
                    $skill_id = JobSkillHistory::create($data)->id;

                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'skill_id'=>$jobSkillObj->id,'type'=>$type,'skill_name'=>$data['skill_name'] ]);
                }

            } else {
                $obj = JobSkillHistory::find($selected_id);
                $path_text = $request->input('path_text');
                if(!empty($obj) && count($obj) > 0) {
                    $data['skill_name']= $request->input('skill_name');
                    $data['skill_hierarchy']=$path_text;
                    $obj->update($data);

                    $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                    if(!empty(Auth::user()->username))
                        $loggedinUsername = Auth::user()->username;

                    $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                        .$loggedinUsername.'</a> edited skill <a href="'.url('site_admin').'">'
                        .$request->input('skill_name').'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'skill_id'=>$obj->id,'type'=>$type,'skill_name'=>$request->input('skill_name')]);
                }
            }
        }
        return response()->json(['success'=>false,'errors'=>['Something goes wrong please try again later.']]);
    }
    public function skill_delete(Request $request){
        if(Auth::check() && $request->ajax()){
            $id = $request->input('id');
            $type = $request->input('type');
            $path_text = $request->input('path_text');

            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);


            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
            if(!empty(Auth::user()->username))
                $loggedinUsername = Auth::user()->username;

            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                .$loggedinUsername.'</a> deleted skill <a href="'.url('site_admin').'">';

            if(Auth::user()->role == "superadmin"){
                $job_skill_id= $id;
                if(strpos($id,"JBSH") !== false) {
                    $job_skill_id_temp = JobSkillHistory::where('prefix_id', $id)->first();
                    if(!empty($job_skill_id_temp) && count($job_skill_id_temp) > 0 && !empty($job_skill_id_temp->job_skill_id))
                        $job_skill_id =$job_skill_id_temp->job_skill_id;
                }

                $jobSkillObj= JobSkill::find($job_skill_id );
                if(!empty($jobSkillObj) && count($jobSkillObj) > 0) {
                    $taskObj = DB::select('SELECT * FROM tasks WHERE FIND_IN_SET('.$job_skill_id.',skills)');
                    if(!empty($taskObj) && count($taskObj) > 0)
                        return response()->json(['success'=>false,'msg'=>'You can not delete this skill. Currently it is used in task.']);

                    $html.=$jobSkillObj->skill_name.'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }
                    $jobSkillObj->delete();
                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'msg'=>'Skill deleted successfully']);
                }
                return response()->json(['success'=>true,'msg'=>'Something goes wrong. Please try again later.']);
            }
            else{
                if($type == "new"){
                    if(strpos($id,"JBSH") !== false)
                        $obj = JobSkillHistory::where('prefix_id',$id)->where('user_id',Auth::user()->id)->first();
                    else
                        $obj = JobSkillHistory::where('id',$id)->where('user_id',Auth::user()->id)->first();


                    if(!empty($obj) && count($obj) > 0) {
                        $html.=$obj->skill_name.'</a>';
                        if(!empty($path_text)){
                            $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                        }
                        $obj->delete();
                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'comment'=>$html
                        ]);
                        return response()->json(['success'=>true,'msg'=>'Skill deleted successfully']);
                    }
                }
                else{
                    $temp_id = $id;
                    if(strpos($temp_id ,"JBSH") !== false)
                        $temp_id = str_replace("JBSH","",$temp_id );
                    $taskObj = DB::select('SELECT * FROM tasks WHERE FIND_IN_SET('.$temp_id.',skills)');
                    if(!empty($taskObj) && count($taskObj) > 0)
                        return response()->json(['success'=>false,'msg'=>'You can not delete this skill. Currently it is used in task.']);

                    $data['parent_id_belongs_to'] = null;
                    $data['job_skill_id'] = $id;
                    $data['user_id'] = Auth::user()->id;
                    $data['action_type'] = 'delete';
                    $data['skill_hierarchy']=$path_text;
                    JobSkillHistory::create($data);

                    $jobObj = JobSkill::find($id);
                    if(count($jobObj) > 0 && !empty($jobObj))
                        $html.=$jobObj->skill_name.'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }
                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);

                    return response()->json(['success'=>true]);
                }
            }
        }
        return response()->json(['success'=>false,'msg'=>'You are not authorized person to perform this action.']);
    }

    public function category_edit(Request $request){

        if(!$request->ajax())
            return view('errors.404');

        if (!Auth::check())
            return response()->json(['success' => false, 'errors' => ['You are not authorized person to perform this action.']]);

        $validator = Validator::make($request->all(), [
            'category_name' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()]);

        $selected_id = $request->input('selected_id');
        if (empty($selected_id))
            return response()->json(['success' => false, 'errors' => ['Something goes wrong please try again later.']]);

        $category_name = $request->input('category_name');
        $category_nameExist = UnitCategory::whereRaw('LOWER(name) = "'.$category_name.'" and id !='.$selected_id)->count();

        if ($category_nameExist > 0)
            return response()->json(['success' => false, 'errors' => ['category_name' => 'Unit category name already exists']]);

        $type = $request->input('tbl_type');
        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);

        $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
        if(!empty(Auth::user()->username))
            $loggedinUsername = Auth::user()->username;

        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
            .$loggedinUsername.'</a> edited category <a href="'.url('site_admin').'">'
            .$request->input('category_name').'</a>';

        if(Auth::user()->role == "superadmin") {
            $unit_category_id= $selected_id;
            if(strpos($selected_id,"JBSH") !== false) {
                $unit_category_id_temp = UnitCategoryHistory::where('prefix_id', $selected_id)->first();
                if(!empty($unit_category_id_temp) && count($unit_category_id_temp) > 0 && !empty($unit_category_id_temp->unit_category_id))
                    $unit_category_id =$unit_category_id_temp->job_skill_id;
            }

            $unitCategoryObj= UnitCategory::find($unit_category_id );
            if(!empty($unitCategoryObj) && count($unitCategoryObj) > 0) {
                $unitCategoryObj->update(['name' => $request->input('category_name')]);
                $html.=$unitCategoryObj->name.'</a>';
                if(!empty($path_text)){
                    $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                }

                SiteActivity::create([
                    'user_id'=>Auth::user()->id,
                    'comment'=>$html
                ]);
                return response()->json(['success'=>true,'category_id'=>$unitCategoryObj->id,'type'=>'old','category_name'=>$category_name]);
            }
            return response()->json(['success'=>true,'msg'=>'Something goes wrong. Please try again later.']);
        }
        else {
            if ($type == "old") {
                $unit_category_id= $selected_id;
                if(strpos($selected_id,"UCH") !== false) {
                    $unit_category_id_temp = UnitCategoryHistory::where('prefix_id', $selected_id)->first();
                    if(!empty($unit_category_id_temp) && count($unit_category_id_temp) > 0)
                        $unit_category_id =$unit_category_id_temp->unit_category_id;
                    $selected_id = str_replace("UCH","",$selected_id);
                }
                $path_text = $request->input('path_text');

                $unitCategoryObj= UnitCategory::find($unit_category_id );
                if(count($unitCategoryObj) > 0 && !empty($unitCategoryObj)) {
                    $obj = UnitCategoryHistory::where('unit_category_id', $unitCategoryObj->id)->where('user_id', Auth::user()->id)->first();
                    if (!empty($obj) && count($obj) > 0) {
                        $obj->delete();
                    }

                    $data['parent_id_belongs_to'] = null;
                    $data['unit_category_id'] = $selected_id;
                    $data['user_id'] = Auth::user()->id;
                    $data['name'] = $request->input('category_name');
                    $data['action_type'] = 'edit';
                    $data['category_hierarchy']=$path_text;
                    $category_id = UnitCategoryHistory::create($data)->id;

                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'category_id'=>$unitCategoryObj->id,'type'=>$type,
                        'category_name'=>$data['name'] ]);
                }

            } else {
                $obj = UnitCategoryHistory::find($selected_id);
                $path_text = $request->input('path_text');
                if(!empty($obj) && count($obj) > 0) {
                    $data['name']= $request->input('category_name');
                    $data['category_hierarchy']=$path_text;
                    $obj->update($data);
                    $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                    if(!empty(Auth::user()->username))
                        $loggedinUsername = Auth::user()->username;
                    $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                        .$loggedinUsername.'</a> edited category <a href="'.url('site_admin').'">'
                        .$data['name'].'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'category_id'=>$obj->id,'type'=>$type,'category_name'=>$data['name']]);
                }
            }
        }
        return response()->json(['success'=>false,'errors'=>['Something goes wrong please try again later.']]);

    }

    public function category_delete(Request $request){
        if(!$request->ajax())
            return view('errors.404');

        if(Auth::check()){

            $id = $request->input('id');
            $type = $request->input('type');
            $path_text = $request->input('path_text');

            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);

            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
            if(!empty(Auth::user()->username))
                $loggedinUsername = Auth::user()->username;

            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                .$loggedinUsername.'</a> deleted category <a href="'.url('site_admin').'">';

            if(Auth::user()->role == "superadmin"){
                $unit_category_id= $id;
                if(strpos($id,"UCH") !== false) {
                    $unit_category_id_temp = UnitCategoryHistory::where('prefix_id', $id)->first();
                    if(!empty($unit_category_id_temp) && count($unit_category_id_temp) > 0 && !empty($unit_category_id_temp->unit_category_id))
                        $unit_category_id =$unit_category_id_temp->unit_category_id;
                }

                $unitCategoryObj= UnitCatergory::find($unit_category_id);
                if(!empty($unitCategoryObj) && count($unitCategoryObj) > 0) {

                    $taskObj = DB::select('SELECT * FROM units WHERE FIND_IN_SET('.$unit_category_id.',category_id)');
                    if(!empty($taskObj) && count($taskObj) > 0)
                        return response()->json(['success'=>false,'msg'=>'You can not delete this category. Currently it is used in unit.']);


                    $html.=$unitCategoryObj->name.'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }
                    $unitCategoryObj->delete();
                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'msg'=>'Unit category deleted successfully']);
                }
                return response()->json(['success'=>true,'msg'=>'Something goes wrong. Please try again later.']);
            }
            else{
                if($type == "new"){
                    if(strpos($id,"UCH") !== false)
                        $obj = UnitCategoryHistory::where('prefix_id', $id)->where('user_id',Auth::user()->id)->first();
                    else
                        $obj = UnitCategoryHistory::where('id',$id)->where('user_id',Auth::user()->id)->first();

                    if(!empty($obj) && count($obj) > 0) {
                        $html.=$obj->name.'</a>';
                        if(!empty($path_text)){
                            $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                        }
                        $obj->delete();
                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'comment'=>$html
                        ]);
                        return response()->json(['success'=>true,'msg'=>'Unit category deleted successfully']);
                    }
                    return response()->json(['success'=>false,'msg'=>'No category found. Please try again later.']);
                }
                else{
                    $temp_id = $id;
                    if(strpos($temp_id ,"UCH") !== false)
                        $temp_id = str_replace("UCH","",$temp_id );
                    $taskObj = DB::select('SELECT * FROM units WHERE FIND_IN_SET('.$temp_id.',category_id)');
                    if(!empty($taskObj) && count($taskObj) > 0)
                        return response()->json(['success'=>false,'msg'=>'You can not delete this category. Currently it is used in unit.']);

                    $data['parent_id_belongs_to'] = null;
                    $data['unit_category_id'] = $id;
                    $data['user_id'] = Auth::user()->id;
                    $data['action_type'] = 'delete';
                    $data['category_hierarchy']=$path_text;
                    UnitCategoryHistory::create($data);

                    $jobObj = UnitCategory::find($id);
                    if(count($jobObj) > 0 && !empty($jobObj))
                        $html.=$jobObj->name.'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }
                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);

                    return response()->json(['success'=>true]);
                }
            }
        }
        return response()->json(['success'=>false,'msg'=>'You are not authorized person to perform this action.']);
    }

    public function area_of_interest_add(Request $request){
        if(!$request->ajax())
            return view('errors.404');

        if(!Auth::check())
            return response()->json(['success'=>false,'errors'=>['You are not authorized person to perform this action.']]);

        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success'=>false,'errors'=>$validator->messages()]);

        $areaofInterestExist = AreaOfInterest::whereRaw('LOWER(title) = "'.strtolower($request->input('title').'"'))->count();

        if($areaofInterestExist > 0)
            return redirect()->back()->withErrors(['name'=>'Area of interest already exists.'])->withInput();


        $parent_id = $request->input('parent_id');
        $temp_parent_id = $parent_id;

        if(empty($parent_id))
            $parent_id=0;
        else
            $parent_id = str_replace("AOIH","",$parent_id);


        $data  = [
            'title'=>$request->input('title'),
            'parent_id'=>$parent_id
        ];

        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);

        $path_text = $request->input('path_text');

        $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
        if(!empty(Auth::user()->username))
            $loggedinUsername = Auth::user()->username;

        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
            .$loggedinUsername.'</a> added area of interest <a href="'.url('site_admin').'">'
            .$data['title'].'</a>';
        if(!empty($path_text)){
            $html.=' to the <a href="'.url('site_admin').'">'.$path_text.'</a>';
        }


        if(Auth::user()->role =="superadmin") {
            $data['status'] = "approved";
            $area_id =AreaOfInterest::create($data)->id;
        }
        else{
            $type = $request->input('tbl_type');
            if($type == "null")
                $type =null;
            elseif($type == "old"){
                $unitCategoryHistoryObj = UnitCategoryHistory::where('prefix_id',$temp_parent_id)->first();
                if(!empty($unitCategoryHistoryObj) && count($unitCategoryHistoryObj) > 0 && !empty($unitCategoryHistoryObj->unit_category_id))
                    $data['parent_id'] = $unitCategoryHistoryObj->unit_category_id;
            }
            $data['user_id']=Auth::user()->id;
            $data['action_type']='add';
            $data['parent_id_belongs_to'] =$type;
            $data['area_of_interest_hierarchy']=$path_text;
            $area_id = AreaOfInterestHistory::create($data)->id;
        }
        SiteActivity::create([
            'user_id'=>Auth::user()->id,
            'comment'=>$html
        ]);
        return response()->json(['success'=>true,'area_of_interest_id'=>$area_id,'title'=>$data['title']]);
    }

    public function area_of_interest_edit(Request $request){
        if(!$request->ajax())
            return view('errors.404');

        if (!Auth::check())
            return response()->json(['success' => false, 'errors' => ['You are not authorized person to perform this action.']]);

        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->messages()]);

        $selected_id = $request->input('selected_id');
        if (empty($selected_id))
            return response()->json(['success' => false, 'errors' => ['Something goes wrong please try again later.']]);

        $title = $request->input('title');
        if(strpos($selected_id,"AOIH") !== false)
            $temp_id = str_replace("AOIH","",$selected_id);
        else
            $temp_id = $selected_id;
        $areaofInterest_nameExist = AreaOfInterest::whereRaw('LOWER(title) = "'.$title.'" and id !='.$temp_id )->count();

        if ($areaofInterest_nameExist > 0)
            return response()->json(['success' => false, 'errors' => ['title' => 'Area of interest already exists']]);

        $type = $request->input('tbl_type');
        $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
        $user_id = $userIDHashID->encode(Auth::user()->id);

        $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
        if(!empty(Auth::user()->username))
            $loggedinUsername = Auth::user()->username;

        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
            .$loggedinUsername.'</a> edited area of interest <a href="'.url('site_admin').'">'
            .$title.'</a>';

        if(Auth::user()->role == "superadmin") {
            $area_of_interest_id= $selected_id;
            if(strpos($selected_id,"AOIH") !== false) {
                $area_of_interest_id_temp = AreaOfInterestHistory::where('prefix_id', $selected_id)->first();
                if(!empty($area_of_interest_id_temp) && count($area_of_interest_id_temp) > 0 && !empty
                    ($area_of_interest_id_temp->area_of_interest_id))
                    $area_of_interest_id =$area_of_interest_id_temp->area_of_interest_id;
            }

            $areaOfInterestObj= AreaOfInterest::find($area_of_interest_id );
            if(!empty($areaOfInterestObj) && count($areaOfInterestObj) > 0) {
                $areaOfInterestObj->update(['title' => $title]);
                $html.=$areaOfInterestObj->name.'</a>';
                if(!empty($path_text)){
                    $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                }

                SiteActivity::create([
                    'user_id'=>Auth::user()->id,
                    'comment'=>$html
                ]);
                return response()->json(['success'=>true,'area_of_interest_id'=>$areaOfInterestObj->id,'type'=>'old','title'=>$title]);
            }
            return response()->json(['success'=>true,'msg'=>'Something goes wrong. Please try again later.']);
        }
        else {
            if ($type == "old") {
                $area_of_interest_id= $selected_id;
                if(strpos($selected_id,"AOIH") !== false) {
                    $area_of_interest_id_temp = AreaOfInterestHistory::where('prefix_id', $selected_id)->first();
                    if(!empty($area_of_interest_id_temp) && count($area_of_interest_id_temp) > 0)
                        $area_of_interest_id =$area_of_interest_id_temp->area_of_interest_id;
                    $selected_id = str_replace("AOIH","",$selected_id);
                }
                $path_text = $request->input('path_text');

                $areaOfInterestObj= AreaOfInterest::find($area_of_interest_id );

                if(count($areaOfInterestObj) > 0 && !empty($areaOfInterestObj)) {
                    $obj = AreaOfInterestHistory::where('area_of_interest_id', $areaOfInterestObj->id)->where('user_id', Auth::user()->id)->first();
                    if (!empty($obj) && count($obj) > 0) {
                        $obj->delete();
                    }

                    $data['parent_id_belongs_to'] = null;
                    $data['area_of_interest_id'] = $selected_id;
                    $data['user_id'] = Auth::user()->id;
                    $data['title'] = $request->input('title');
                    $data['action_type'] = 'edit';
                    $data['area_of_interest_hierarchy']=$path_text;
                    $area_of_interest_id = AreaOfInterestHistory::create($data)->id;

                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'area_of_interest_id'=>$areaOfInterestObj->id,'type'=>$type,
                        'title'=>$data['title'] ]);
                }

            } else {
                if(strpos($selected_id,"AOIH") !== false)
                    $temp_id = str_replace("AOIH","",$selected_id);
                else
                    $temp_id = $selected_id;
                $obj = AreaOfInterestHistory::find($temp_id);
                $path_text = $request->input('path_text');
                if(!empty($obj) && count($obj) > 0) {
                    $data['title']= $request->input('title');
                    $data['area_of_interest_hierarchy']=$path_text;
                    $obj->update($data);

                    $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                    if(!empty(Auth::user()->username))
                        $loggedinUsername = Auth::user()->username;

                    $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                        .$loggedinUsername.'</a> edited area of interest <a href="'.url('site_admin').'">'
                        .$data['title'].'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'area_of_interest_id'=>$obj->id,'type'=>$type,'title'=>$data['title']]);
                }
            }
        }
        return response()->json(['success'=>false,'errors'=>['Something goes wrong please try again later.']]);
    }

    public function area_of_interest_delete(Request $request){
        if(!$request->ajax())
            return view('errors.404');

        if(Auth::check()){

            $id = $request->input('id');
            $type = $request->input('type');
            $path_text = $request->input('path_text');

            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);

            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
            if(!empty(Auth::user()->username))
                $loggedinUsername = Auth::user()->username;

            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                .$loggedinUsername.'</a> deleted area of interest <a href="'.url('site_admin').'">';

            if(Auth::user()->role == "superadmin"){
                $area_of_interest_id= $id;
                if(strpos($id,"AOIH") !== false) {
                    $area_of_interest_id_temp = AreaOfInterestHistory::where('prefix_id', $id)->first();
                    if(!empty($area_of_interest_id_temp) && count($area_of_interest_id_temp) > 0 && !empty($area_of_interest_id_temp->area_of_interest_id))
                        $area_of_interest_id =$area_of_interest_id_temp->area_of_interest_id;
                }

                $areaOfInterestObj= AreaOfInterest::find($area_of_interest_id);
                if(!empty($areaOfInterestObj) && count($areaOfInterestObj) > 0) {
                    $taskObj = DB::select('SELECT * FROM users WHERE FIND_IN_SET('.$area_of_interest_id.',area_of_interest)');
                    if(!empty($taskObj) && count($taskObj) > 0)
                        return response()->json(['success'=>false,'msg'=>'You can not delete this area of interest. Currently it is used by
                        some user.']);


                    $html.=$areaOfInterestObj->title.'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }
                    $areaOfInterestObj->delete();
                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);
                    return response()->json(['success'=>true,'msg'=>'Area of interest deleted successfully']);
                }
                return response()->json(['success'=>true,'msg'=>'Something goes wrong. Please try again later.']);
            }
            else{
                if($type == "new"){
                    if(strpos($id,"AOIH") !== false)
                        $obj = AreaOfInterestHistory::where('prefix_id', $id)->where('user_id',Auth::user()->id)->first();
                    else
                        $obj = AreaOfInterestHistory::where('id',$id)->where('user_id',Auth::user()->id)->first();

                    if(!empty($obj) && count($obj) > 0) {
                        $html.=$obj->title.'</a>';
                        if(!empty($path_text)){
                            $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                        }
                        $obj->delete();
                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'comment'=>$html
                        ]);
                        return response()->json(['success'=>true,'msg'=>'Area of interest deleted successfully']);
                    }
                    return response()->json(['success'=>false,'msg'=>'No category found. Please try again later.']);
                }
                else{
                    $temp_id = $id;
                    if(strpos($temp_id ,"AOIH") !== false)
                        $temp_id = str_replace("AOIH","",$temp_id );

                    $taskObj = \DB::select('SELECT * FROM users WHERE FIND_IN_SET('.$temp_id.',area_of_interest)');
                    if(!empty($taskObj) && count($taskObj) > 0)
                        return response()->json(['success'=>false,'msg'=>'You can not delete this area of interest. Currently it is used by
                         some users.']);

                    $data['parent_id_belongs_to'] = null;
                    $data['area_of_interest_id'] = $id;
                    $data['user_id'] = Auth::user()->id;
                    $data['action_type'] = 'delete';
                    $data['area_of_interest_hierarchy']=$path_text;
                    AreaOfInterestHistory::create($data);

                    $jobObj = AreaOfInterest::find($id);
                    if(count($jobObj) > 0 && !empty($jobObj))
                        $html.=$jobObj->title.'</a>';
                    if(!empty($path_text)){
                        $html.=' in the <a href="'.url('site_admin').'">'.$path_text.'</a>';
                    }
                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'comment'=>$html
                    ]);

                    return response()->json(['success'=>true]);
                }
            }
        }
        return response()->json(['success'=>false,'msg'=>'You are not authorized person to perform this action.']);
    }

    public function get_skills(Request $request){
        $terms = $request->input('term');
        $page = $request->input('page');
        if(!empty($terms)){
            if($page == 0 || empty($page))
                $page =0;
            $str = JobSkill::getHierarchy($terms,$page );

            if(!empty($str)){
                foreach($str as $index=>$s){
                    if(is_array($s['name'])){
                        $str[$index]['name']=implode(" > ",array_reverse($s['name']));
                    }

                }
                return response()->json(['items'=>$str,'total_counts'=>$obj = JobSkill::where('skill_name','like',$terms.'%')->count()]);
            }
        }
        return response()->json([]);

    }

    public function get_next_level_skills(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $job_skill_history_id = null;
        $page = $request->input('page');

        $dataObj = JobSkill::getSkillForBrowse($page,$id,$type);

        $skills =  [];
        $deleted_ids = [];
        if(!empty($dataObj)){
            foreach($dataObj as $skillObj){
                if(in_array($skillObj->id,$deleted_ids))
                    continue;
                if($skillObj->action_type == "delete") {
                    $deleted_ids[]=$skillObj->id;
                    if(isset($skills[$skillObj->id])) {
                        unset($skills[$skillObj->id]);
                    }
                    continue;
                }
                if($type == "new"){
                    $skills[$skillObj->id] = ['type' => 'new', 'name' => $skillObj->skill_name, 'hasSubOption' => JobSkill::hasSubOptions($skillObj->id)];
                }
                else {
                    if (!empty($skillObj->action_type) && $skillObj->action_type == "edit")
                        $skills[$skillObj->id] = ['type' => 'old', 'name' => $skillObj->history_skill_name,'hasSubOption' => JobSkill::hasSubOptions($skillObj->id)];
                    elseif (!empty($skillObj->action_type) && $skillObj->action_type == "add")
                        $skills[$skillObj->history_id] = ['type' => 'new', 'name' => $skillObj->history_skill_name, 'hasSubOption' => JobSkill::hasSubOptions($skillObj->id)];
                    else
                        $skills[$skillObj->id] = ['type' => 'old', 'name' => $skillObj->skill_name, 'hasSubOption' => JobSkill::hasSubOptions($skillObj->id)];
                }
            }
        }

       /* dd($dataObj);
        if($type  == "old")
            $skills = JobSkill::where('parent_id',$id)->pluck('skill_name','id')->all();
        else
            $skills=JobSkillHistory::where('parent_id',$id)->pluck('skill_name','id')->all();*/
        return response()->json(['success'=>true,'data'=>$skills]);
    }

    public function approveSkill(Request $request){
        if($request->ajax() && Auth::check()){
            if(Auth::user()->role=="superadmin"){
                $prefix_id = $request->input('id');

                $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                $user_id = $userIDHashID->encode(Auth::user()->id);

                if(!empty($prefix_id)){
                    $jobSkillHistory = JobSkillHistory::where('prefix_id',$prefix_id)->first();
                    if(!empty($jobSkillHistory) && count($jobSkillHistory) > 0){

                       /* $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                        $user_id = $userIDHashID->encode(Auth::user()->id);

                        $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .Auth::user()->first_name.' '.Auth::user()->last_name.'</a> approved skill <a href="'.url('site_admin').'">';*/

                        $data['skill_name']=$jobSkillHistory->skill_name;


                        if($jobSkillHistory->action_type == "add"){
                            $data['parent_id']=$jobSkillHistory->parent_id;
                            $data['status']='approved';

                            $new_skill_id = JobSkill::create($data)->id;

                            // find it's child and update with job_skill's table record : $new_skill_id
                            $children = JobSkillHistory::where('parent_id',$jobSkillHistory->id)->where('parent_id_belongs_to','new')
                                ->where('action_type','add')->get();
                            if(!empty($children) && count($children) > 0){
                                foreach($children as $child){
                                    $ch = JobSkillHistory::find($child->id);
                                    if(!empty($ch) && count($ch) > 0){
                                        $ch->update(['parent_id_belongs_to'=>'old','parent_id'=>$new_skill_id]);
                                    }
                                }
                            }
                            $jobSkillHistoryTemp = $jobSkillHistory;

                            $jobSkillHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved addition of skill <a href="'.url('site_admin').'">';

                            $html.=$jobSkillHistoryTemp->skill_name.'</a>';
                            if(!empty($jobSkillHistoryTemp->skill_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$jobSkillHistoryTemp->skill_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);


                            return response()->json(['success'=>true]);

                        }
                        elseif($jobSkillHistory->action_type == "edit"){
                            $jobSkillObj = JobSkill::where('id',$jobSkillHistory->job_skill_id)->first();
                            if(!empty($jobSkillObj) && count($jobSkillObj) > 0){
                                $jobSkillObj->update(['skill_name'=>$jobSkillHistory->skill_name]);
                            }

                            $jobSkillHistoryTemp = $jobSkillHistory;
                            $jobSkillHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved edition of skill <a href="'.url('site_admin').'">';

                            $html.=$jobSkillHistoryTemp->skill_name.'</a>';
                            if(!empty($jobSkillHistoryTemp->skill_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$jobSkillHistoryTemp->skill_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);

                            return response()->json(['success'=>true]);
                        }
                        elseif($jobSkillHistory->action_type=="delete"){
                            $childrenExist = JobSkill::where('parent_id',$jobSkillHistory->job_skill_id)->get();
                            if(!empty($childrenExist) && count($childrenExist) > 0){
                                return response()->json(['success'=>false,'msg'=>'You can\t delete the parent skill.']);
                            }
                            $taskObj = \DB::select('SELECT * from tasks WHERE FIND_IN_SET('.$jobSkillHistory->job_skill_id.',skills)');
                            if(!empty($taskObj) && count($taskObj) > 0){
                                return response()->json(['success'=>false,'msg'=>'This skill currently assigned to task.']);
                            }
                            $jobSkillObj =JobSkill::where('id',$jobSkillHistory->job_skill_id)->first();
                            if(!empty($jobSkillObj) && count($jobSkillObj) > 0)
                                $jobSkillObj->forceDelete();

                            $jobSkillHistoryTemp = $jobSkillHistory;

                            $jobSkillHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved deletion of skill <a href="'.url('site_admin').'">';

                            $html.=$jobSkillHistoryTemp->skill_name.'</a>';
                            if(!empty($jobSkillHistoryTemp->skill_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$jobSkillHistoryTemp->skill_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);


                            return response()->json(['success'=>true]);
                        }
                    }
                }
            }
        }
        return response()->json(['success'=>false]);

    }

    public function approve_category(Request $request){
        if($request->ajax() && Auth::check()){
            if(Auth::user()->role=="superadmin"){
                $prefix_id = $request->input('id');

                $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                $user_id = $userIDHashID->encode(Auth::user()->id);

                if(!empty($prefix_id)){
                    $unitCategoryHistory = UnitCategoryHistory::where('prefix_id',$prefix_id)->first();
                    if(!empty($unitCategoryHistory) && count($unitCategoryHistory) > 0){

                        $data['name']=$unitCategoryHistory->name;

                        if($unitCategoryHistory->action_type == "add"){
                            $data['parent_id']=$unitCategoryHistory->parent_id;
                            $data['status']='approved';

                            $new_category_id = UnitCategory::create($data)->id;

                            // find it's child and update with job_skill's table record : $new_skill_id
                            $children = UnitCategoryHistory::where('parent_id',$unitCategoryHistory->id)->where('parent_id_belongs_to','new')
                                ->where('action_type','add')->get();
                            if(!empty($children) && count($children) > 0){
                                foreach($children as $child){
                                    $ch = UnitCategoryHistory::find($child->id);
                                    if(!empty($ch) && count($ch) > 0){
                                        $ch->update(['parent_id_belongs_to'=>'old','parent_id'=>$new_category_id]);
                                    }
                                }
                            }
                            $unitCategoryHistoryTemp = $unitCategoryHistory;

                            $unitCategoryHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved addition of category <a href="'.url
                                ('site_admin').'">';

                            $html.=$unitCategoryHistoryTemp->name.'</a>';
                            if(!empty($unitCategoryHistoryTemp->category_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$unitCategoryHistoryTemp->category_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);


                            return response()->json(['success'=>true]);

                        }
                        elseif($unitCategoryHistory->action_type == "edit"){
                            $unitCategoryObj = UnitCategory::where('id',$unitCategoryHistory->unit_category_id)->first();
                            if(!empty($unitCategoryObj) && count($unitCategoryObj) > 0){
                                $unitCategoryObj->update(['name'=>$unitCategoryHistory->name]);

                                $unitCategoryHistoryTemp = $unitCategoryHistory;
                                $unitCategoryHistory->delete();

                                $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                                if(!empty(Auth::user()->username))
                                    $loggedinUsername = Auth::user()->username;

                                $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                    .$loggedinUsername.'</a> approved edition of category <a href="'.url('site_admin').'">';

                                $html.=$unitCategoryHistoryTemp->name.'</a>';
                                if(!empty($unitCategoryHistoryTemp->category_hierarchy)){
                                    $html.=' in the <a href="'.url('site_admin').'">'.$unitCategoryHistoryTemp->category_hierarchy.'</a>';
                                }

                                SiteActivity::create([
                                    'user_id'=>Auth::user()->id,
                                    'comment'=>$html
                                ]);

                                return response()->json(['success'=>true]);
                            }
                            return response()->json(['success'=>false,'msg'=>'Something goes wrong. Please try again later.']);

                        }
                        elseif($unitCategoryHistory->action_type=="delete"){
                            $childrenExist = UnitCategory::where('parent_id',$unitCategoryHistory->unit_category_id)->get();
                            if(!empty($childrenExist) && count($childrenExist) > 0){
                                return response()->json(['success'=>false,'msg'=>'You can\t delete the parent category.']);
                            }
                            $taskObj = \DB::select('SELECT * from units WHERE FIND_IN_SET('.$unitCategoryHistory->unit_category_id.',category_id)');
                            if(!empty($taskObj) && count($taskObj) > 0){
                                return response()->json(['success'=>false,'msg'=>'This category currently assigned to unit. You can not
                                delete this category.']);
                            }
                            $unitCategoryObj =UnitCategory::where('id',$unitCategoryHistory->unit_category_id)->first();
                            if(!empty($unitCategoryObj) && count($unitCategoryObj) > 0)
                                $unitCategoryObj->forceDelete();

                            $unitCategoryHistoryTemp = $unitCategoryHistory;

                            $unitCategoryHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved deletion of category <a href="'.url
                                ('site_admin').'">';

                            $html.=$unitCategoryHistoryTemp->name.'</a>';
                            if(!empty($unitCategoryHistoryTemp->category_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$unitCategoryHistoryTemp->category_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);


                            return response()->json(['success'=>true]);
                        }
                    }
                }
            }
        }
        return response()->json(['success'=>false]);

    }

    public function approve_area_of_interest(Request $request){
        if($request->ajax() && Auth::check()){
            if(Auth::user()->role=="superadmin"){
                $prefix_id = $request->input('id');

                $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                $user_id = $userIDHashID->encode(Auth::user()->id);

                if(!empty($prefix_id)){
                    $areaOfInterestHistory = AreaOfInterestHistory::where('prefix_id',$prefix_id)->first();

                    if(!empty($areaOfInterestHistory) && count($areaOfInterestHistory) > 0){

                        $data['title']=$areaOfInterestHistory->title;

                        if($areaOfInterestHistory->action_type == "add"){
                            $data['parent_id']=$areaOfInterestHistory->parent_id;
                            $data['status']='approved';

                            $new_area_of_interest_id = AreaOfInterest::create($data)->id;

                            // find it's child and update with job_skill's table record : $new_skill_id
                            $children = AreaOfInterestHistory::where('parent_id',$areaOfInterestHistory->id)->where('parent_id_belongs_to','new')
                                ->where('action_type','add')->get();
                            if(!empty($children) && count($children) > 0){
                                foreach($children as $child){
                                    $ch = AreaOfInterestHistory::find($child->id);
                                    if(!empty($ch) && count($ch) > 0){
                                        $ch->update(['parent_id_belongs_to'=>'old','parent_id'=>$new_area_of_interest_id]);
                                    }
                                }
                            }
                            $areaOfInterestHistoryTemp = $areaOfInterestHistory;

                            $areaOfInterestHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved addition of area of interest <a href="'.url('site_admin').'">';

                            $html.=$areaOfInterestHistoryTemp->title.'</a>';
                            if(!empty($areaOfInterestHistoryTemp->area_of_interest_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$areaOfInterestHistoryTemp->area_of_interest_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);


                            return response()->json(['success'=>true]);

                        }
                        elseif($areaOfInterestHistory->action_type == "edit"){
                            $areaOfInterestObj = AreaOfInterest::where('id',$areaOfInterestHistory->area_of_interest_id)->first();
                            if(!empty($areaOfInterestObj) && count($areaOfInterestObj) > 0){
                                $areaOfInterestObj->update(['title'=>$areaOfInterestHistory->title]);

                                $areaOfInterestHistoryTemp = $areaOfInterestHistory;
                                $areaOfInterestHistory->delete();

                                $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                                if(!empty(Auth::user()->username))
                                    $loggedinUsername = Auth::user()->username;

                                $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                    .$loggedinUsername.'</a> approved edition of area of interest <a href="'.url('site_admin').'">';

                                $html.=$areaOfInterestHistoryTemp->title.'</a>';
                                if(!empty($areaOfInterestHistoryTemp->area_of_interest_hierarchy)){
                                    $html.=' in the <a href="'.url('site_admin').'">'.$areaOfInterestHistoryTemp->area_of_interest_hierarchy.'</a>';
                                }

                                SiteActivity::create([
                                    'user_id'=>Auth::user()->id,
                                    'comment'=>$html
                                ]);

                                return response()->json(['success'=>true]);
                            }
                            return response()->json(['success'=>false,'msg'=>'Something goes wrong. Please try again later.']);

                        }
                        elseif($areaOfInterestHistory->action_type=="delete"){
                            $childrenExist = AreaOfInterest::where('parent_id',$areaOfInterestHistory->area_of_interest_id)->get();
                            if(!empty($childrenExist) && count($childrenExist) > 0){
                                return response()->json(['success'=>false,'msg'=>'You can\t delete the parent area of interest.']);
                            }
                            $taskObj = \DB::select('SELECT * from users WHERE FIND_IN_SET('.$areaOfInterestHistory->area_of_interest_id.',area_of_interest)');
                            if(!empty($taskObj) && count($taskObj) > 0){
                                return response()->json(['success'=>false,'msg'=>'This area of interest currently assigned to some users.
                                 You can not delete this area of interest.']);
                            }
                            $areaOfInterestObj =AreaOfInterest::where('id',$areaOfInterestHistory->area_of_interest_id)->first();
                            if(!empty($areaOfInterestObj) && count($areaOfInterestObj) > 0)
                                $areaOfInterestObj->forceDelete();

                            $areaOfInterestHistoryTemp = $areaOfInterestHistory;

                            $areaOfInterestHistory->delete();

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> approved deletion of area of interest <a href="'.url('site_admin').'">';

                            $html.=$areaOfInterestHistoryTemp->title.'</a>';
                            if(!empty($areaOfInterestHistoryTemp->area_of_interest_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$areaOfInterestHistoryTemp->area_of_interest_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);


                            return response()->json(['success'=>true]);
                        }
                    }
                }
            }
        }
        return response()->json(['success'=>false]);

    }

    public function discard_skill_change(Request $request){
        if($request->ajax() ){
            if(Auth::check()) {
                if (Auth::user()->role == "superadmin") {
                    $prefix_id = $request->input('id');

                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    if (!empty($prefix_id)) {
                        $jobSkillHistory = JobSkillHistory::where('prefix_id', $prefix_id)->first();
                        if (!empty($jobSkillHistory) && count($jobSkillHistory) > 0) {
                            $jobSkillHistoryTemp = $jobSkillHistory;

                            $jobSkillHistory->delete();

                            $op_type = '';
                            if($jobSkillHistoryTemp->action_type == "add")
                                $op_type ="addition";
                            elseif($jobSkillHistoryTemp->action_type == "edit")
                                $op_type ="edition";
                            elseif($jobSkillHistoryTemp->action_type == "delete")
                                $op_type ="deletion";

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> rejected '.$op_type.' of skill <a href="'.url
                                ('site_admin').'">';

                            $html.=$jobSkillHistoryTemp->skill_name.'</a>';
                            if(!empty($jobSkillHistoryTemp->skill_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$jobSkillHistoryTemp->skill_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);

                            return response()->json(['success' => true]);
                        } else
                            return response()->json(['success' => false, 'msg' => 'Something goes wrong. Please try again later.']);
                    } else
                        return response()->json(['success' => false, 'msg' => 'Something goes wrong. Please try again later.']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'You are not authorized person to perform this action.']);

        }
        return view('errors.404');
    }

    public function discard_category_changes(Request $request){
        if($request->ajax() ){
            if(Auth::check()) {
                if (Auth::user()->role == "superadmin") {
                    $prefix_id = $request->input('id');

                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    if (!empty($prefix_id)) {
                        $unitCategoryHistory = UnitCategoryHistory::where('prefix_id', $prefix_id)->first();
                        if (!empty($unitCategoryHistory) && count($unitCategoryHistory) > 0) {
                            $unitCategoryHistoryTemp = $unitCategoryHistory;

                            $unitCategoryHistory->delete();

                            $op_type = '';
                            if($unitCategoryHistoryTemp->action_type == "add")
                                $op_type ="addition";
                            elseif($unitCategoryHistoryTemp->action_type == "edit")
                                $op_type ="edition";
                            elseif($unitCategoryHistoryTemp->action_type == "delete")
                                $op_type ="deletion";

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> rejected '.$op_type.' of category <a href="'.url
                                ('site_admin').'">';

                            $html.=$unitCategoryHistoryTemp->name.'</a>';
                            if(!empty($unitCategoryHistoryTemp->category_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$unitCategoryHistoryTemp->category_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);

                            return response()->json(['success' => true]);
                        } else
                            return response()->json(['success' => false, 'msg' => 'Something goes wrong. Please try again later.']);
                    } else
                        return response()->json(['success' => false, 'msg' => 'Something goes wrong. Please try again later.']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'You are not authorized person to perform this action.']);

        }
        return view('errors.404');
    }

    public function discard_area_of_interest_changes(Request $request){
        if($request->ajax() ){
            if(Auth::check()) {
                if (Auth::user()->role == "superadmin") {
                    $prefix_id = $request->input('id');

                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    if (!empty($prefix_id)) {
                        $areaOfInterestHistory = AreaOfInterestHistory::where('prefix_id', $prefix_id)->first();
                        if (!empty($areaOfInterestHistory) && count($areaOfInterestHistory) > 0) {
                            $areaOfInterestHistoryTemp = $areaOfInterestHistory;

                            $areaOfInterestHistory->delete();

                            $op_type = '';
                            if($areaOfInterestHistoryTemp->action_type == "add")
                                $op_type ="addition";
                            elseif($areaOfInterestHistoryTemp->action_type == "edit")
                                $op_type ="edition";
                            elseif($areaOfInterestHistoryTemp->action_type == "delete")
                                $op_type ="deletion";

                            $loggedinUsername = strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name);
                            if(!empty(Auth::user()->username))
                                $loggedinUsername = Auth::user()->username;

                            $html = '<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$loggedinUsername.'</a> rejected '.$op_type.' of area of interest <a href="'.url('site_admin').'">';

                            $html.=$areaOfInterestHistoryTemp->title.'</a>';
                            if(!empty($areaOfInterestHistoryTemp->area_of_interest_hierarchy)){
                                $html.=' in the <a href="'.url('site_admin').'">'.$areaOfInterestHistoryTemp->area_of_interest_hierarchy.'</a>';
                            }

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'comment'=>$html
                            ]);

                            return response()->json(['success' => true]);
                        } else
                            return response()->json(['success' => false, 'msg' => 'Something goes wrong. Please try again later.']);
                    } else
                        return response()->json(['success' => false, 'msg' => 'Something goes wrong. Please try again later.']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'You are not authorized person to perform this action.']);

        }
        return view('errors.404');
    }

    public function browse_skills(Request $request){
        if($request->ajax()){
            if(Auth::check()){
                $jobSkillsObj = \DB::select('SELECT  c.id, IF(ISNULL(c.parent_id), 0, c.parent_id) AS parent_id, c.skill_name, p.skill_name AS Parentskill_name
                                              FROM job_skills c LEFT JOIN job_skills p ON (c.parent_id = p.id) WHERE IF(c.parent_id IS
                                              NULL, 0, c.parent_id) = 0 AND c.id <> 0 ORDER BY c.id ');

                $firstBox_skills = [];
                if(count($jobSkillsObj) > 0 && !empty($jobSkillsObj)){
                    foreach($jobSkillsObj as $skill){
                        $firstBox_skills[$skill->id]=['type'=>'old','name'=>$skill->skill_name];
                    }
                }
                view()->share('firstBox_skills',$firstBox_skills);
                $selected_skills = []; $job_skill_list = JobSkill::pluck('skill_name','id')->all();
                if(!empty($request->input('from')) && $request->input('from') == "account"){
                    $selected_skills = explode(",",\Auth::user()->job_skills);
                }
                view()->share('request_from',$request->input('from'));
                view()->share('selected_skills',$selected_skills);
                view()->share('job_skill_list',$job_skill_list);
                $html = view('admin.partials.skill_browse',['from'=>'task'])->render();
                return response()->json(['success'=>true,'html'=>$html]);
            }
            return response()->json(['success'=>false,'msg'=>'You are not authorized person to perform this action.']);

        }
        return view('errors.404');
    }

    /**
     * Update current user job skill
     * My Account Screen
     */
    public function update_user_skill(Request $request){
        if($request->isMethod('POST')){
            $user_skill = '';
            if($request->has('update_skill') && $request->input('update_skill')){
                $user_skill = $request->input('selected_skill_id');
                if(!empty($user_skill))
                    $job_skills = implode(",",$user_skill);
                else
                    $job_skills = '';
            }

            if($request->has('delete_skill') && $request->input('delete_skill')){
                $user_skill = explode(",",\Auth::user()->job_skills);
                unset($user_skill[array_search($request->input('id'),$user_skill)]);
                if(!empty($user_skill))
                    $job_skills = implode(",",$user_skill);
                else
                    $job_skills='';
            }
            Auth::user()->job_skills = $job_skills;
            Auth::user()->save();
            return response()->json(['success'=>true]);
        }else{
            return response()->json(['success'=>false]);
        }
    }

    public function browse_categories(Request $request){
        if($request->ajax()){
            //if(Auth::check()){
                $unitCategoriesObj = \DB::select('SELECT  c.id, IF(ISNULL(c.parent_id), 0, c.parent_id) AS parent_id, c.name, p.name AS
                                              Parentcategory_name
                                              FROM unit_category c LEFT JOIN unit_category p ON (c.parent_id = p.id) WHERE IF(c.parent_id IS
                                              NULL, 0, c.parent_id) = 0 AND c.id <> 0 ORDER BY c.id ');

                $firstBox_category = [];
                if(count($unitCategoriesObj) > 0 && !empty($unitCategoriesObj)){
                    foreach($unitCategoriesObj as $category){
                        $firstBox_category[$category->id]=['type'=>'old','name'=>$category->name];
                    }
                }
                view()->share('firstBox_category',$firstBox_category);
                $html = view('admin.partials.unit_category_browse',['from'=>'unit'])->render();
                return response()->json(['success'=>true,'html'=>$html]);
            //}
            //return response()->json(['success'=>false,'msg'=>'You are not authorized person to perform this action.']);

        }
        return view('errors.404');
    }

    public function browse_area_of_interest(Request $request){
        if($request->ajax()){
            if(Auth::check()){
                $areaOfInterestObj = \DB::select('SELECT  c.id, IF(ISNULL(c.parent_id), 0, c.parent_id) AS parent_id, c.title, p.title AS
                                              Parenttitle
                                              FROM area_of_interest c LEFT JOIN area_of_interest p ON (c.parent_id = p.id) WHERE IF(c
                                              .parent_id IS
                                              NULL, 0, c.parent_id) = 0 AND c.id <> 0 ORDER BY c.id ');

                $firstBox_areaOfInterest = [];
                if(count($areaOfInterestObj) > 0 && !empty($areaOfInterestObj)){
                    foreach($areaOfInterestObj as $are_of_interest){
                        $firstBox_areaOfInterest[$are_of_interest->id]=['type'=>'old','name'=>$are_of_interest->title];
                    }
                }
                view()->share('firstBox_areaOfInterest',$firstBox_areaOfInterest);
                $html = view('admin.partials.area_of_interest_browse',['from'=>'account'])->render();
                return response()->json(['success'=>true,'html'=>$html]);
            }
            return response()->json(['success'=>false,'msg'=>'You are not authorized person to perform this action.']);

        }
        return view('errors.404');
    }

    public function get_next_level_categories(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $unit_category_history_id = null;
        $page = $request->input('page');

        $dataObj = UnitCategory::getCategoryForBrowse($page,$id,$type);
        //dd($dataObj);

        $categories =  [];
        $deleted_ids = [];
        if(!empty($dataObj)){
            foreach($dataObj as $categoryObj){
                if(in_array($categoryObj->id,$deleted_ids))
                    continue;
                if($categoryObj->action_type == "delete") {
                    $deleted_ids[]=$categoryObj->id;
                    if(isset($categories[$categoryObj->id])) {
                        unset($categories[$categoryObj->id]);
                    }
                    continue;
                }
                if($type == "new"){
                    $categories[$categoryObj->id] = ['type' => 'new', 'name' => $categoryObj->name];
                }
                else {
                    if (!empty($categoryObj->action_type) && $categoryObj->action_type == "edit")
                        $categories[$categoryObj->id] = ['type' => 'old', 'name' => $categoryObj->history_category_name];
                    elseif (!empty($categoryObj->action_type) && $categoryObj->action_type == "add")
                        $categories[$categoryObj->history_id] = ['type' => 'new', 'name' => $categoryObj->history_category_name];
                    else
                        $categories[$categoryObj->id] = ['type' => 'old', 'name' => $categoryObj->name];
                }
            }
        }
        return response()->json(['success'=>true,'data'=>$categories]);
    }

    public function get_categories(Request $request){
        $terms = $request->input('term');
        $page = $request->input('page');
        if(!empty($terms)){
            if($page == 0 || empty($page))
                $page =0;
            $str = UnitCategory::getHierarchy($terms,$page );

            if(!empty($str)){
                foreach($str as $index=>$s){
                    if(is_array($s['name'])){
                        $str[$index]['name']=implode(" > ",array_reverse($s['name']));
                    }

                }
                return response()->json(['items'=>$str,'total_counts'=>UnitCategory::where('name','like',$terms.'%')->count()]);
            }
        }
        return response()->json([]);

    }

    public function get_area_of_interest(Request $request){
        $terms = $request->input('term');
        $page = $request->input('page');
        if(!empty($terms)){
            if($page == 0 || empty($page))
                $page =0;
            $str = AreaOfInterest::getHierarchy($terms,$page );

            if(!empty($str)){
                foreach($str as $index=>$s){
                    if(is_array($s['name'])){
                        $str[$index]['name']=implode(" > ",array_reverse($s['name']));
                    }

                }
                return response()->json(['items'=>$str,'total_counts'=>AreaOfInterest::where('title','like',$terms.'%')->count()]);
            }
        }
        return response()->json([]);

    }


    public function get_next_level_area_of_interest(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $unit_category_history_id = null;
        $page = $request->input('page');

        $dataObj = AreaOfInterest::getAreaOFInterestForBrowse($page,$id,$type);

        $areaOfInterests =  [];
        $deleted_ids = [];
        if(!empty($dataObj)){
            foreach($dataObj as $areaOfInterestObj){
                if(in_array($areaOfInterestObj->id,$deleted_ids))
                    continue;
                if($areaOfInterestObj->action_type == "delete") {
                    $deleted_ids[]=$areaOfInterestObj->id;
                    if(isset($areaOfInterests[$areaOfInterestObj->id])) {
                        unset($areaOfInterests[$areaOfInterestObj->id]);
                    }
                    continue;
                }
                if($type == "new"){
                    $areaOfInterests[$areaOfInterestObj->id] = ['type' => 'new', 'name' => $areaOfInterestObj->title];
                }
                else {
                    if (!empty($areaOfInterestObj->action_type) && $areaOfInterestObj->action_type == "edit")
                        $areaOfInterests[$areaOfInterestObj->id] = ['type' => 'old', 'name' => $areaOfInterestObj->history_area_of_interest_name];
                    elseif (!empty($areaOfInterestObj->action_type) && $areaOfInterestObj->action_type == "add")
                        $areaOfInterests[$areaOfInterestObj->history_id] = ['type' => 'new', 'name' => $areaOfInterestObj->history_area_of_interest_name];
                    else
                        $areaOfInterests[$areaOfInterestObj->id] = ['type' => 'old', 'name' => $areaOfInterestObj->title];
                }
            }
        }
        return response()->json(['success'=>true,'data'=>$areaOfInterests]);
    }

    public function remove_from_watchlist(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $flag = true;
        $obj = [];
        if(!empty($id) && !empty($type)){
            switch($type){
                case 'unit':
                    $hashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
                    $id = $hashID->decode($id);
                    if(!empty($id)) {
                        $id = $id[0];
                        $obj = Watchlist::where('user_id',Auth::user()->id)->where('unit_id',$id)->first();
                    }
                    break;
                case'objective':
                    $hashID = new Hashids('objective id hash',10,Config::get('app.encode_chars'));
                    $id = $hashID->decode($id);
                    if(!empty($id)) {
                        $id = $id[0];
                        $obj = Watchlist::where('user_id',Auth::user()->id)->where('objective_id',$id)->first();
                    }
                    break;
                case 'task':
                    $hashID = new Hashids('task id hash',10,Config::get('app.encode_chars'));
                    $id = $hashID->decode($id);
                    if(!empty($id)) {
                        $id = $id[0];
                        $obj = Watchlist::where('user_id',Auth::user()->id)->where('task_id',$id)->first();
                    }
                    break;
                case 'issue':
                    $hashID = new Hashids('issue id hash',10,Config::get('app.encode_chars'));
                    $id = $hashID->decode($id);
                    if(!empty($id)) {
                        $id = $id[0];
                        $obj = Watchlist::where('user_id',Auth::user()->id)->where('issue_id',$id)->first();
                    }
                    break;
                default:
                    $flag = false;
                    break;
            }
            if($flag && !empty($obj)){
                $obj->delete();
                return response()->json(['success'=>true]);
            }
        }
        return response()->json(['success'=>false]);

    }
}
