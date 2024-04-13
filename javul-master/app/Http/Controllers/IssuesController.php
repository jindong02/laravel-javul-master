<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UserController;
use App\Models\ActivityPoint;
use App\Models\Fund;
use App\Models\ImportanceLevel;
use App\Models\Issue;
use App\Models\IssueDocuments;
use App\Models\Objective;
use App\Models\SiteActivity;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Forum;
use App\Models\IssuesRevision;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Mc;
use App\Models\UserMessages;
use GuzzleHttp\Client;

class IssuesController extends Controller
{
    public $user_messages;
    public function __construct(){
        $this->middleware('auth',['except'=>['index','lists','view','report_concern_email','reset_captcha_after_close']]);
        $this->user_messages = new UserMessages();
    }

    public function revison($issue_id ,Request $request){
        view()->share('issue_id',$issue_id);
        if(!empty($issue_id)){
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id = $issueIDHashID ->decode($issue_id);
            if(!empty($issue_id)){
                $issue_id = $issue_id[0];
                $issueObj = Issue::with(['issue_documents'])->find($issue_id);
                if(!empty($issueObj)){
                    $unitObj = Unit::find($issueObj->unit_id);
                    view()->share('unitObj',$unitObj);
                    view()->share('issueObj',$issueObj);
                    $site_activity = SiteActivity::where('unit_id',$issueObj->unit_id)->orderBy('id','desc')
                                    ->paginate(Config::get('app.site_activity_page_limit'));

                    $availableUnitFunds =Fund::getUnitDonatedFund($issueObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($issueObj->unit_id);

                    $upvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','+1')->count();
                    $downvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','-1')->count();

                    if($upvotedCnt == 0 && $downvotedCnt == 0)
                        $importancePercentage = 0;
                    else
                        $importancePercentage =  ($upvotedCnt * 100) / ($upvotedCnt + $downvotedCnt);

                    if(is_float($importancePercentage))
                        $importancePercentage = ceil($importancePercentage);

                    $status_class='';
                    if($issueObj->status=="unverified")
                        $status_class="text-danger";
                    elseif($issueObj->status=="verified")
                        $status_class="text-info";
                    elseif($issueObj->status == "resolved")
                        $status_class = "text-success";
                    view()->share('status_class',$status_class);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('upvotedCnt',$upvotedCnt);
                    view()->share('downvotedCnt',$downvotedCnt);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$issueObj->unit_id);
                    view()->share('site_activity_text','unit activity log');

                     // Forum Object coading
                    view()->share("unit_id", $issueObj->unit_id);
                    view()->share("section_id", 3);
                    view()->share("object_id",$issueObj->id);

                    $revisions = IssuesRevision::select(['issues_revisions.user_id','issues_revisions.id','issues_revisions.unit_id','issues_revisions.comment','issues_revisions.size','issues_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'issues_revisions.user_id')
                            ->where("issues_revisions.unit_id","=",$issueObj->unit_id)
                            ->where("issues_revisions.issues_id","=",$issueObj->id)
                            ->get();

                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

                    view()->share('userIDHashID', $userIDHashID);
                    view()->share('Carbon', new Carbon);
                    view()->share('revisions',$revisions );

                    // Forum Object coading End

                    return view('issues.revison.view');
                }
            }
        }
        return view('errors.404');
    }


    public function revisonview($issue_id ,$revision_id,Request $request){
        view()->share('issue_id',$issue_id);
        if(!empty($issue_id)){
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id = $issueIDHashID ->decode($issue_id);
            if(!empty($issue_id)){
                $issue_id = $issue_id[0];
                $issueObj = Issue::with(['issue_documents'])->find($issue_id);
                if(!empty($issueObj)  && count($issueObj) > 0){
                    $unitObj = Unit::find($issueObj->unit_id);
                    view()->share('unitObj',$unitObj);
                    view()->share('issueObj',$issueObj);
                    $site_activity = SiteActivity::where('unit_id',$issueObj->unit_id)->orderBy('id','desc')
                                    ->paginate(\Config::get('app.site_activity_page_limit'));

                    $availableUnitFunds =Fund::getUnitDonatedFund($issueObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($issueObj->unit_id);

                    $upvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','+1')->count();
                    $downvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','-1')->count();

                    if($upvotedCnt == 0 && $downvotedCnt == 0)
                        $importancePercentage = 0;
                    else
                        $importancePercentage =  ($upvotedCnt * 100) / ($upvotedCnt + $downvotedCnt);

                    if(is_float($importancePercentage))
                        $importancePercentage = ceil($importancePercentage);

                    $status_class='';
                    if($issueObj->status=="unverified")
                        $status_class="text-danger";
                    elseif($issueObj->status=="verified")
                        $status_class="text-info";
                    elseif($issueObj->status == "resolved")
                        $status_class = "text-success";
                    view()->share('status_class',$status_class);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('upvotedCnt',$upvotedCnt);
                    view()->share('downvotedCnt',$downvotedCnt);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$issueObj->unit_id);
                    view()->share('site_activity_text','unit activity log');

                     // Forum Object coading
                    view()->share("unit_id", $issueObj->unit_id);
                    view()->share("section_id", 3);
                    view()->share("object_id",$issueObj->id);

                    $revisions = IssuesRevision::select(['issues_revisions.user_id','issues_revisions.description','issues_revisions.id','issues_revisions.unit_id','issues_revisions.comment','issues_revisions.size','issues_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'issues_revisions.user_id')
                            ->where("issues_revisions.unit_id","=",$issueObj->unit_id)
                            ->where("issues_revisions.issues_id","=",$issueObj->id)
                            ->where("issues_revisions.id","=",$revision_id)
                            ->get();
                    if($revisions->count() == 1){

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

                        view()->share('userIDHashID', $userIDHashID);
                        view()->share('Carbon', new Carbon);
                        view()->share('revisions',$revisions->first() );

                        return view('issues.revison.view_revision');
                    }
                }
            }
        }
        return view('errors.404');
    }

    public function diff($issue_id,$rev1,$rev2,Request $request){
        view()->share('issue_id',$issue_id);
        if(!empty($issue_id)){
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id = $issueIDHashID ->decode($issue_id);
            if(!empty($issue_id)){
                $issue_id = $issue_id[0];
                $issueObj = Issue::with(['issue_documents'])->find($issue_id);
                if(!empty($issueObj)  && count($issueObj) > 0){
                    $unitObj = Unit::find($issueObj->unit_id);
                    view()->share('unitObj',$unitObj);
                    view()->share('issueObj',$issueObj);
                    $site_activity = SiteActivity::where('unit_id',$issueObj->unit_id)->orderBy('id','desc')
                                    ->paginate(\Config::get('app.site_activity_page_limit'));

                    $availableUnitFunds =Fund::getUnitDonatedFund($issueObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($issueObj->unit_id);

                    $upvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','+1')->count();
                    $downvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','-1')->count();

                    if($upvotedCnt == 0 && $downvotedCnt == 0)
                        $importancePercentage = 0;
                    else
                        $importancePercentage =  ($upvotedCnt * 100) / ($upvotedCnt + $downvotedCnt);

                    if(is_float($importancePercentage))
                        $importancePercentage = ceil($importancePercentage);

                    $status_class='';
                    if($issueObj->status=="unverified")
                        $status_class="text-danger";
                    elseif($issueObj->status=="verified")
                        $status_class="text-info";
                    elseif($issueObj->status == "resolved")
                        $status_class = "text-success";
                    view()->share('status_class',$status_class);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('upvotedCnt',$upvotedCnt);
                    view()->share('downvotedCnt',$downvotedCnt);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$issueObj->unit_id);
                    view()->share('site_activity_text','unit activity log');

                     // Forum Object coading
                    view()->share("unit_id", $issueObj->unit_id);
                    view()->share("section_id", 3);
                    view()->share("object_id",$issueObj->id);

                    $revisions = IssuesRevision::select(['issues_revisions.user_id','issues_revisions.id','issues_revisions.description','issues_revisions.unit_id','issues_revisions.comment','issues_revisions.size','issues_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'issues_revisions.user_id')
                            ->where("issues_revisions.unit_id","=",$issueObj->unit_id)
                            ->where("issues_revisions.issues_id","=",$issueObj->id)
                            ->whereIn("issues_revisions.id",[ (int)$rev1, (int)$rev2 ])
                            ->get();

                    if($revisions->count() == 2){
                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

                        view()->share('userIDHashID', $userIDHashID);
                        view()->share('Carbon', new Carbon);
                        view()->share('revisions',$revisions );

                        return view("issues.revison.changes_difference");
                    }

                }
            }
        }
        return view('errors.404');
    }


    public function index(Request $request){
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

        // get all issues for listing
        $issues = Issue::orderBy('id','desc')->paginate(\Config::get('app.page_limit'));
        $userController = new UserController;

        foreach ($issues as $issue){
            $issue->age = $userController->date_calculateToNow($issue->created_at);
        }

        view()->share('issues',$issues );
        $site_activity = SiteActivity::orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));
        view()->share('site_activity',$site_activity);
        view()->share('site_activity_text','Global Activity Log');
        return view('issues.issues');
    }
    public function get_issues_paginate(Request $request){
        $page_limit = \Config::get('app.page_limit');
        $issues = Issue::orderBy('id','desc')->paginate($page_limit);
        view()->share('issues',$issues);
        $html = view('issues.partials.more_issues')->render();
        return \Response::json(['success'=>true,'html'=>$html]);
    }
    /**
     * Create issue page
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request){
        $unit_id = $request->segment(2);
        $objective_id = $request->segment(4);
        $task_id = $request->segment(5);

        $unitIDEncoded = $unit_id;
        $objectiveIDEncoded = $objective_id;
        $taskIDEncoded=$task_id;

        $unitObj = [];
        $objectiveObj = [];
        $taskObj = [];
        if(!empty($unit_id))
        {
            $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $unitObj = Unit::find($unit_id);
                if(!empty($unitObj)){

                    if($request->isMethod('post')) {

                        $validator = \Validator::make($request->all(), [
                            'title' => 'required',
                            'description' => 'required'
                        ]);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInput();


                        // get selected objective_id
                        $selected_objective_id = Issue::getSelectedObjective($request);

                        //get all selected task_id
                        $selected_task_id_arr= Issue::getSelectedTask($request);

                        $issue_id = Issue::create([
                            'title'=>$request->input('title'),
                            'description'=>$request->input('description'),
                            'user_id'=>Auth::user()->id,
                            'status'=>'unverified',
                            'unit_id'=>$unit_id,
                            'objective_id'=>$selected_objective_id,
                            'task_id'=>$selected_task_id_arr
                        ])->id;

                        // upload issue documents
                        IssueDocuments::uploadDocuments($issue_id,$request);
                        // upload finish

                        ActivityPoint::create([
                            'user_id'=>Auth::user()->id,
                            'issue_id'=>$issue_id,
                            'points'=>2,
                            'comments'=>'Issue Created',
                            'type'=>'issue'
                        ]);

                        // add site activity record for global statistics.
                        $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
                        $issue_id_encoded = $issueIDHashID->encode($issue_id);

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                        $user_id = $userIDHashID->encode(Auth::user()->id);

                        $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                        if(!empty(Auth::user()->username))
                            $user_name =Auth::user()->username;

                        // send alert to user(s) who has this unit in his/her watchlist
                        $watchlistUserObj = \DB::table('my_watchlist')
                            ->join('users','my_watchlist.user_id','=','users.id')
                            ->where('my_watchlist.user_id','!=',Auth::user()->id)
                            ->where(function ($query) use($objective_id,$unit_id,$task_id) {
                                $query->where('objective_id',$objective_id)->orWhere('unit_id',$unit_id)->orWhere('task_id',$task_id);
                            })
                            ->get();

                        $unitObj = Unit::find($unit_id);

                        $content = 'User <a href="'.url('userprofiles/'.Auth::user()->id.'/'.strtolower(Auth::user()->first_name.'_'
                                    .Auth::user()->last_name)).'">'.strtolower(Auth::user()->first_name.' '.Auth::user()->last_name).'</a>' .
                            ' created Issue <a href="'.url('issues/'.$issue_id_encoded.'/view').'">'.$request->input('title').'</a> in
                            Unit <a href="'.url('units/'.$unitIDEncoded.'/'.$unitObj->slug).'">'.$unitObj->name.'</a>';

                        $email_subject  ='User '.Auth::user()->first_name.' '.Auth::user()->last_name.' created Issue '.$request->input('title').' in Unit '.$unitObj->name;
                        User::SendEmailAndOnSiteAlert($content,$email_subject,$watchlistUserObj,$onlyemail=false,'watched_items');

                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'unit_id'=>$unit_id,
                            'objective_id'=>$objective_id,
                            'task_id'=>$task_id,
                            'issue_id'=>$issue_id,
                            'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$user_name.'</a>
                        created issue <a href="'.url('issues/'.$issue_id_encoded.'/view').'">'.$request->input('title').'</a>'
                        ]);

                        $request->session()->flash('msg_val', $this->user_messages->getMessage('ISSUE_CREATED')['text']);
                        return redirect('issues/'.$unitIDHashID->encode($unitObj->id).'/lists');
                    }
                    $availableUnitFunds =Fund::getUnitDonatedFund($unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($unit_id);

                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);

                    $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(\Config::get('app
                    .site_activity_page_limit'));

                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$unit_id);
                    view()->share('site_activity_text','Unit activity log');
                    $objectiveObj = Objective::where('unit_id',$unitObj->id)->get();
                    view()->share('objectiveObj',$objectiveObj);


                    view()->share('unitObj',$unitObj);
                    view()->share('objectiveObj',$objectiveObj);
                    view()->share('taskObj',$taskObj);
                    view()->share('user_can_change_status',false);
                    view()->share('user_can_resolve_issue',false);
                    view()->share('taskObj',[]);
                    view()->share('issueDocumentsObj',[]);
                    view()->share('taskDocumentsObj',[]);

                    view()->share('action_method','add');
                    return view('issues.create');
                }
            }
        }
        return view('errors.404');
        /*if(!empty($objective_id) || !empty($unit_id))
        {
            $objectiveIDHashID= new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
            $objective_id= $objectiveIDHashID->decode($objective_id);
            if(!empty($objective_id)){
                $objective_id = $objective_id[0];
                $objectiveObj = Objective::where('id',$objective_id)->where('unit_id',$unit_id)->get();

            }
            else if(!empty($unit_id)){
                $objectiveObj = Objective::where('unit_id',$unit_id)->get();
            }
        }
        if(!empty($task_id) || !empty($objective_id))
        {
            $taskIDHashID= new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::where('id',$task_id)->where('objective_id',$objective_id)->where('unit_id',$unit_id)->get();
            }
            else if(!empty($objective_id))
                $taskObj = Task::where('objective_id',$objective_id)->get();
            else if(!empty($unit_id))
                $taskObj = Task::where('unit_id',$unit_id)->get();
        }*/


    }

    /**
     * Create issue page
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request){

        if($request->isMethod('post')) {
            $validator = \Validator::make($request->all(), [
                'title' => 'required',
                'unit_id'=>'required',
                // 'objective_id'=>'required',
                // 'task_id'=>'required',
                'description' => 'required'
            ]);

            if ($validator->fails())
                return redirect()->back()->withErrors($validator)->withInput();

            $unit_id = $request->input('unit_id');
            $objective_id = $request->input('objective_id');
            $task_id = $request->input('task_id');

            $unitIDEncoded= $unit_id;
            $objectiveIDEncoded=$objective_id;
            $taskIDEncoded=$task_id;

            $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(empty($unit_id))
                return redirect()->back()->withErrors(['errors'=>['Unit not found.']])->withInput();
            $unit_id = $unit_id[0];

            // get selected objective_id
            $selected_objective_id = Issue::getSelectedObjective($request);

            //get all selected task_id
            $selected_task_id_arr= Issue::getSelectedTask($request);

            $issue_id = Issue::create([
                'title'=>$request->input('title'),
                'description'=>$request->input('description'),
                'user_id'=>Auth::user()->id,
                'status'=>'unverified',
                'unit_id'=>$unit_id,
                'objective_id'=>$selected_objective_id,
                'task_id'=>$selected_task_id_arr
            ])->id;

            // upload issue documents
            IssueDocuments::uploadDocuments($issue_id,$request);
            // upload finish

            ActivityPoint::create([
                'user_id'=>Auth::user()->id,
                'issue_id'=>$issue_id,
                'points'=>2,
                'comments'=>'Issue Created',
                'type'=>'issue'
            ]);

            // add site activity record for global statistics.
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id_encoded = $issueIDHashID->encode($issue_id);

            $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);

            $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
            if(!empty(Auth::user()->username))
                $user_name =Auth::user()->username;

            // send alert to user(s) who has this unit in his/her watchlist
            $watchlistUserObj = \DB::table('my_watchlist')
                ->join('users','my_watchlist.user_id','=','users.id')
                ->where('my_watchlist.user_id','!=',Auth::user()->id)
                ->where(function ($query) use($objective_id,$unit_id,$task_id) {
                    $query->where('objective_id',$objective_id)->orWhere('unit_id',$unit_id)->orWhere('task_id',$task_id);
                })
                ->get();

            $unitObj = Unit::find($unit_id);

            $content = 'User <a href="'.url('userprofiles/'.Auth::user()->id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name).'</a>' .
                ' created Issue <a href="'.url('issues/'.$issue_id_encoded.'/view').'">'.$request->input('title').'</a> in
                            Unit <a href="'.url('units/'.$unitIDEncoded.'/'.$unitObj->slug).'">'.$unitObj->name.'</a>';

            $email_subject  ='User '.Auth::user()->first_name.' '.Auth::user()->last_name.' created Issue '.$request->input('title').' in Unit '.$unitObj->name;
            User::SendEmailAndOnSiteAlert($content,$email_subject,$watchlistUserObj,$onlyemail=false,'watched_items');


            SiteActivity::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=>$unit_id,
                'objective_id'=>null,
                'task_id'=>null,
                'issue_id'=>$issue_id,
                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                    .$user_name.'</a>
                        created issue <a href="'.url('issues/'.$issue_id_encoded.'/view').'">'.$request->input('title').'</a>'
            ]);

            $request->session()->flash('msg_val', $this->user_messages->getMessage('ISSUE_CREATED')['text']);
            return redirect('issues');
        }
        view()->share('site_activity',[]);
        view()->share('unitObj',Unit::all());
        view()->share('objectiveObj',[]);
        view()->share('taskObj',[]);
        view()->share('user_can_change_status',false);
        view()->share('user_can_resolve_issue',false);
        view()->share('taskObj',[]);
        view()->share('issueDocumentsObj',[]);
        view()->share('taskDocumentsObj',[]);

        view()->share('action_method','add');
        return view('issues.create');
    }

    public function edit(Request $request,$issue_id){
        $unit_id = $request->segment(2);
        $unitIDEncoded=$unit_id;
        if(!empty($issue_id)){
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id_encoded = $issue_id;
            $issue_id = $issueIDHashID->decode($issue_id);
            if(!empty($issue_id)){
                $issue_id = $issue_id[0];
                $issueObj = Issue::find($issue_id);
                if(!empty($issueObj)){

                    // check if issue resolved then redirect to view not edit mode
                    //if($issueObj->status == "resolved")
                      //  return redirect('issues/'.$issue_id_encoded.'/view');
                    //display update page to user

                    if($request->isMethod('post')) {
                        $validator = \Validator::make($request->all(), [
                            'title' => 'required',
                            'description' => 'required'
                        ]);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInput();

                        foreach($issueObj->getAttributes() as $key => $value) {
                            if(!$value) {
                                $issueObj->$key = '';
                            }
                        }

                        // get selected objective_id
                        $selected_objective_id = Issue::getSelectedObjective($request);

                        //get all selected task_id
                        $selected_task_id_arr= Issue::getSelectedTask($request);
                        $status = $request->input('status');

                        if(empty($status))
                            $status = $issueObj->status;

                        /* Store old data Start */
                            $bytes = IssuesRevision::strBytes( str_replace(' ', '', strip_tags($request->input('description'))) );
                            $oldBytes = IssuesRevision::strBytes( str_replace(' ', '', strip_tags($issueObj->description)) );

                            $IssuesRevision = new IssuesRevision;
                            $IssuesRevision->modified_by = Auth::user()->id;
                            $IssuesRevision->size = (  $bytes - $oldBytes );
                            $IssuesRevision->user_id = $issueObj->user_id;
                            $IssuesRevision->unit_id = $issueObj->unit_id;

                            if($selected_objective_id) {
                                $IssuesRevision->objective_id = $selected_objective_id;
                            }

                            if(!empty($selected_task_id_arr)) {
                                $IssuesRevision->task_id = $selected_task_id_arr;
                            }

                            $IssuesRevision->title = $issueObj->title;
                            $IssuesRevision->description = $issueObj->description;
                            $IssuesRevision->file_attachments = $issueObj->file_attachments;
                            $IssuesRevision->status = $issueObj->status;
                            $IssuesRevision->resolution = $issueObj->resolution;
                            $IssuesRevision->created_at = date("Y-m-d H:i:s");
                            $IssuesRevision->updated_at = $issueObj->created_at;
                            $IssuesRevision->deleted_at = $issueObj->created_at;
                            $IssuesRevision->issues_id = $issueObj->id;
                            $IssuesRevision->comment = $issueObj->comment." ";

                            $IssuesRevision->save();
                        /* Store old data End */


                        $updateArr = [
                            'comment'=>$request->input('comment'),
                            'title'=>$request->input('title'),
                            'description'=>$request->input('description'),
                            'status'=>$status,
                            'objective_id'=>$selected_objective_id,
                            'task_id'=>$selected_task_id_arr
                        ];

                        if($status == "verified" && empty($issueObj->verified_by))
                            $updateArr['verified_by']=Auth::user()->id;
                        else if($status == "resolved") {
                            $updateArr['resolved_by'] = Auth::user()->id;
                            $updateArr['resolution']=$request->input('resolution');
                        }

                        $issueObj->update($updateArr);

                        // upload issue documents
                        IssueDocuments::uploadDocuments($issueObj->id,$request);
                        // upload finish

                        if($status == "verified") {
                            ActivityPoint::create([
                                'user_id' => Auth::user()->id,
                                'issue_id' => $issueObj->id,
                                'points' => 2,
                                'comments' => 'Issue Verified',
                                'type' => 'issue'
                            ]);
                        }
                        else{
                            ActivityPoint::create([
                                'user_id' => Auth::user()->id,
                                'issue_id' => $issueObj->id,
                                'points' => 1,
                                'comments' => 'Issue Updated',
                                'type' => 'issue'
                            ]);
                        }

                        // add site activity record for global statistics.
                        $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
                        $issue_id_encoded = $issueIDHashID->encode($issueObj->id);

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                        $user_id = $userIDHashID->encode(Auth::user()->id);

                        $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                        if(!empty(Auth::user()->username))
                            $user_name =Auth::user()->username;

                        // send alert to user(s) who has this unit in his/her watchlist
                        $unit_id =$issueObj->unit_id;
                        $watchlistUserObj = \DB::table('my_watchlist')
                            ->join('users','my_watchlist.user_id','=','users.id')
                            ->where('my_watchlist.user_id','!=',Auth::user()->id)
                            ->where(function ($query) use($unit_id) {
                                $query->where('unit_id',$unit_id);
                            })
                            ->get();

                        $unitObj = Unit::find($unit_id);

                        $content = 'User <a href="'.url('userprofiles/'.Auth::user()->id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name).'</a>' .
                            ' edited Issue <a href="'.url('issues/'.$issue_id_encoded.'/view').'">'.$request->input('title').'</a> in
                            Unit <a href="'.url('units/'.$unitIDEncoded.'/'.$unitObj->slug).'">'.$unitObj->name.'</a>';

                        $email_subject  ='User '.Auth::user()->first_name.' '.Auth::user()->last_name.' edited Issue '.$request->input('title').' in Unit '.$unitObj->name;
                        User::SendEmailAndOnSiteAlert($content,$email_subject,$watchlistUserObj,$onlyemail=false,'watched_items');

                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'unit_id'=>$issueObj->unit_id,
                            'issue_id'=>$issueObj->id,
                            'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$user_name.'</a>
                        updated issue <a href="'.url('issues/'.$issue_id_encoded.'/view').'">'.$request->input('title').'</a>'
                        ]);

                        $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
                        $request->session()->flash('msg_val', $this->user_messages->getMessage('ISSUE_UPDATED')['text']);
                        return redirect('issues/'.$issueIDHashID->encode($issueObj->id).'/view');
                    }
                    $user_can_change_status = true;
                    $user_can_resolve_issue = true;
                    if($issueObj->user_id == Auth::user()->id) {
                        $user_can_change_status = true;
                        $user_can_resolve_issue = true;
                    }else {
                        $unitAdmin = \App\Task::checkUnitAdmin($issueObj->unit_id);
                        if (Auth::user()->role == "superadmin" || Auth::user()->id == $unitAdmin) {
                            $user_can_change_status = true;
                            $user_can_resolve_issue = true;
                        }
                    }

                    $unitObj = Unit::find($issueObj->unit_id);
                    $objectiveObj = Objective::where('unit_id',$issueObj->unit_id)->get();

                    $site_activity = SiteActivity::where('unit_id',$issueObj->unit_id)->orderBy('id','desc')->paginate(\Config::get('app
                    .site_activity_page_limit'));

                    $availableUnitFunds =Fund::getUnitDonatedFund($issueObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($issueObj->unit_id);

                    $taskObj = Task::where('objective_id',$issueObj->objective_id)->get();
                    $issueDocumentsObj = IssueDocuments::where('issue_id',$issue_id)->get();
                    view()->share('issueDocumentsObj',$issueDocumentsObj);
                    view()->share('taskObj',$taskObj);
                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('unitObj',$unitObj);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$issueObj->unit_id);
                    view()->share('site_activity_text','Unit activity log');
                    view()->share('objectiveObj',$objectiveObj);
                    view()->share('issueObj',$issueObj);
                    view()->share('user_can_change_status',$user_can_change_status);
                    view()->share('user_can_resolve_issue',$user_can_resolve_issue);
                    view()->share('action_method','edit');
                    view()->share('taskDocumentsObj',[]);
                    return view('issues.create');
                }
            }
        }
        return view('errors.404');
    }

    /**
     * Issue details page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Request $request){
        $issue_id  = $request->segment(2);
        if(!empty($issue_id)){
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id = $issueIDHashID ->decode($issue_id);
            if(!empty($issue_id)){
                $issue_id = $issue_id[0];
                $issueObj = Issue::with(['issue_documents'])->find($issue_id);
                if(!empty($issueObj)  && $issueObj->count() > 0){
                    $unitObj = Unit::find($issueObj->unit_id);
                    view()->share('unitObj',$unitObj);
                    view()->share('issueObj',$issueObj);
                    $site_activity = SiteActivity::where('unit_id',$issueObj->unit_id)->orderBy('id','desc')
                                    ->paginate(\Config::get('app.site_activity_page_limit'));

                    $availableUnitFunds =Fund::getUnitDonatedFund($issueObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($issueObj->unit_id);

                    $upvotedCnt = 0;
                    $downvotedCnt = 0;
                    if(Auth::check()){
                        $upvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('user_id',Auth::user()->id)->where('importance_level','+1')->count();
                        $downvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('user_id',Auth::user()->id)->where('importance_level','-1')->count();
                    }


                    if($upvotedCnt == 0 && $downvotedCnt == 0)
                        $importancePercentage = 0;
                    else
                        $importancePercentage =  ($upvotedCnt * 100) / ($upvotedCnt + $downvotedCnt);

                    if(is_float($importancePercentage))
                        $importancePercentage = ceil($importancePercentage);

                    $status_class='';
                    if($issueObj->status=="unverified")
                        $status_class="text-danger";
                    elseif($issueObj->status=="verified")
                        $status_class="text-info";
                    elseif($issueObj->status == "resolved")
                        $status_class = "text-success";
                    view()->share('status_class',$status_class);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('upvotedCnt',$upvotedCnt);
                    view()->share('downvotedCnt',$downvotedCnt);
                    view()->share('importancePercentage',$importancePercentage);
                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('site_activity',$site_activity);
                    view()->share('site_activity_text','unit activity log');

                    view()->share("unit_id", $issueObj->unit_id);
                    view()->share("section_id", 3);
                    view()->share("object_id",$issueObj->id);
                     view()->share('unit_activity_id',$issueObj->unit_id);

                    $forumID =  Forum::checkTopic(array(
                        'unit_id' => $issueObj->unit_id,
                        'section_id' => 3,
                        'object_id' => $issueObj->id,
                    ));

                    if(!empty($forumID)){
                        view()->share('addComments', url('forum/post/'. $forumID->topic_id .'/'. $forumID->slug ) );
                    }
                    $add_wl = session()->get('add_to_wl');
                    if( $add_wl  != null ){
                        $add_to_watchlist = session()->get('add_to_wl');
                        $arr = [];
                        if(is_array($add_to_watchlist) || is_object($add_to_watchlist)){
                            foreach ( $add_to_watchlist as $key => $add){
                                $arr[$key] = $add;
                            }
                        }
                        session()->put('add_to_wl', 'null');
                        view()->share('add_to_watch',$arr);
                    }

                    return view('issues.view');
                }
            }
        }
        return view('errors.404');
    }

    /**
     * Issue listing
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pluck(Request $request){
        $unit_id = $request->segment(2);
        if(!empty($unit_id))
        {
            $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $unitObj = Unit::find($unit_id);
                if(!empty($unitObj)){
                    view()->share('unitObj',$unitObj);
                    $issuesObj = Issue::leftJoin('importance_level','issues.id','=','importance_level.issue_id')->where('unit_id',
                        $unit_id)
                    ->orderBy('id','desc')
                    ->select(['issues.*'])
                    ->paginate(\Config::get
                    ('app
                    .page_limit'));
                    view()->share('issuesObj',$issuesObj);
                    $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')
                        ->paginate(\Config::get('app.site_activity_page_limit'));

                    $availableUnitFunds =Fund::getUnitDonatedFund($unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($unit_id);

                    view()->share('availableUnitFunds',$availableUnitFunds);
                    view()->share('awardedUnitFunds',$awardedUnitFunds);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$unit_id);
                    view()->share('site_activity_text','Unit activity log');

                    $msg_flag = false;
                    $msg_val = '';
                    $msg_type = '';
                    if($request->session()->has('msg_val')){
                        $msg_val =  $request->session()->get('msg_val');
                        $request->session()->forget('msg_val');
                        $msg_flag = true;
                        $msg_type = "success";
                        if($request->session()->has('msg_type')){
                            $msg_type = $request->session()->get('msg_type');
                            $request->session()->forget('msg_type');
                        }
                    }
                    view()->share('msg_flag',$msg_flag);
                    view()->share('msg_val',$msg_val);
                    view()->share('msg_type',$msg_type);


                    return view('issues.list');
                }

            }
        }
        return view('errors.404');
    }

    /**
     * soft deleting the document of given issue_id
     * @param Request $request
     * @return mixed
     */
    public function remove_document(Request $request){
        $issue_id = $request->input('issue_id');
        $id = $request->input('id');

        $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
        $issueDocumentIDHashID = new Hashids('issue document id hash',10,\Config::get('app.encode_chars'));

        $issue_id = $issueIDHashID->decode($issue_id);

        if(empty($issue_id))
            return \Response::json(['success'=>false]);

        $issue_id = $issue_id[0];
        $issueObj = Issue::find($issue_id);
        if(empty($issueObj))
            return \Response::json(['success'=>false]);

        $id = $issueDocumentIDHashID->decode($id);
        if(empty($id))
            return \Response::json(['success'=>false]);

        $id= $id[0];
        $issueDocumentObj = IssueDocuments::where('issue_id',$issue_id)->where('id',$id)->get();

        if(count($issueDocumentObj ) > 0){
            IssueDocuments::where('issue_id',$issue_id)->where('id',$id)->delete();
            return \Response::json(['success'=>true]);
        }

        return \Response::json(['success'=>false]);
    }

    public function add_importance(Request $request){
        $issue_id = $request->input('id');
        $issue_idEncoded = $issue_id;
        $type = $request->input('type');
        if(!empty($issue_id)){
            $issueIDHashID = new Hashids('issue id hash',10,\Config::get('app.encode_chars'));
            $issue_id = $issueIDHashID->decode($issue_id);
            if(!empty($issue_id)){
                $issue_id = $issue_id[0];
                $issueObj = Issue::find($issue_id);
                if(!empty($issueObj)){
                    $importanceLevelObj = ImportanceLevel::where('issue_id',$issue_id)->where('user_id',Auth::user()->id)->first();
                    $site_activity_text = '';
                    if($type == "up"){
                        $levelValue = "+1";
                        if(count($importanceLevelObj) > 0) {
                            if ($importanceLevelObj->importance_level == '+1')
                                $levelValue = '0';
                            else
                                $levelValue = '+1';
                        }
                        $site_activity_text =" upvote objective ";
                    }
                    else{
                        $levelValue = "-1";
                        if(count($importanceLevelObj) > 0) {
                            if ($importanceLevelObj->importance_level == '-1')
                                $levelValue = '0';
                            else
                                $levelValue = '-1';
                        }
                        $site_activity_text =" downvote objective ";
                    }
                    if(count($importanceLevelObj) > 0)
                        $importanceLevelObj->update(['importance_level'=>$levelValue]);
                    else{
                        ImportanceLevel::create([
                            'user_id'=>Auth::user()->id,
                            'issue_id'=>$issue_id,
                            'importance_level'=>$levelValue,
                            'type'=>'Issue'
                        ]);
                    }

                    $upvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','+1')->count();
                    $downvotedCnt = ImportanceLevel::where('issue_id',$issue_id)->where('importance_level','-1')->count();

                    if($levelValue == '0')
                        $importancePercentage =0;
                    else
                        $importancePercentage =  ($upvotedCnt * 100) / ($upvotedCnt + $downvotedCnt);


                    if(is_float($importancePercentage))
                        $importancePercentage = ceil($importancePercentage);
                    view()->share('upvotedCnt',$upvotedCnt);
                    view()->share('downvotedCnt',$downvotedCnt);
                    view()->share('importancePercentage',$importancePercentage);

                    $importance_level_html = view('issues.partials.importance_level',['issue_id'=>$issue_id])->render();

                    return \Response::json(['success'=>true,'html'=>$importance_level_html]);
                }
            }
        }
        return \Response::json(['success'=>false]);

    }

    public function sort_issues(Request $request){
        $unit_id = $request->input('unit_id');
        $order_by = $request->input('order_by');
        if(!empty($unit_id))
        {
            $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $unitObj = Unit::find($unit_id);
                if(!empty($unitObj)){
                    view()->share('unitObj',$unitObj);
                    if($order_by == "older")
                        $order_by = "asc";
                    else
                        $order_by = "desc";

                    $issuesObj = Issue::where('unit_id',$unit_id)->orderBy('id',$order_by)->paginate(\Config::get('app.page_limit'));
                    view()->share('issuesObj',$issuesObj);
                    view()->share('unit_activity_id',$unit_id);
                    $html = view('issues.partials.issue_listing')->render();
                    return \Response::json(['success'=>true,'html'=>$html ]);
                }

            }
        }
        return \Response::json(['success'=>false]);
    }

    public function report_concern_email(Request $email){

        if(!\Auth::check()){

            $auth_check = 0;
            if( empty($email->captcha_value) ){
                return \Response::json(['success'=>false,'errors'=>'Captcha Required'] );
            }
            $validator = \Validator::make($email->all(), [
                'message' => 'required',
                'captcha_value' => 'required'
            ]);
            $token = $email->captcha_value;

            $client = new Client();

            $options = [
                'verify' => false,
                 'header'  => "Content-type: application/x-www-form-urlencoded",
            ];
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify?secret=6LfDyawUAAAAAK-Q7p4-h1WsQc2EdQ-SIQUkmJ7V&response=' .$token, $options);

            $result = json_decode($response->getBody()->getContents());


            if($result->success){
                $flag=true;
            }else{
                $flag=false;
            }

            $name = "Anonymous";
            $email_id=null;
        }else{
            $auth_check = 1;
            $validator = \Validator::make($email->all(), [
                'message' => 'required',
            ]);

            $name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            if(!empty(Auth::user()->username))
                $name = Auth::user()->username;

            $email_id = Auth::user()->email;
            $flag=true;
        }

        if ($validator->fails()){
            return \Response::json(['success'=>false,'errors'=>$validator->messages()->toArray(), 'auth_check'=>$auth_check]);
        }

        if($flag){
            $visit_url=$email->get('visit_url');
            $message=$email->get('message');
            $data=array('name' => $name, 'messages' => $message,'email'=>$email_id,'url'=>$visit_url);
            $mail_sent = Mail::send('emails/report_concern', $data, function ($message){
               $message->from('javul.org@gmail.com','javul.org');
               $message->to('javul.org@gmail.com','Administrator');
               $message->subject('Webform:Report a concern');
            });
            return \Response::json(['success'=>true,'mail_sent'=>$mail_sent]);
        }else{
            return \Response::json(['success'=>false,'errors'=>'You have problems']);
        }

    }

    public function reset_captcha_after_close(Request $reset_captcha){
        Mc::putMcData();
        $question=Mc::getMcQuestion();
        return \Response::json(['success'=>true,'captcha_value'=>$question]);
    }
}
