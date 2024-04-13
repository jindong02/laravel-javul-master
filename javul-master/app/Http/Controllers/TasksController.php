<?php

namespace App\Http\Controllers;

use App\Models\ActivityPoint;
use App\Models\Alerts;
use App\Models\Fund;
use App\Models\JobSkill;
//use App\Library\Helpers;
use App\Models\Objective;
use App\Models\RewardAssignment;
use App\Models\SiteActivity;
use App\Models\SiteConfigs;
use App\Models\Task;
use App\Models\TaskAction;
use App\Models\TaskBidder;
use App\Models\TaskCancel;
use App\Models\TaskComplete;
use App\Models\TaskDocuments;
use App\Models\TaskEditor;
use App\Models\TaskHistory;
use App\Models\TaskRatings;
use App\Models\Unit;
use Hashids\Hashids;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Forum;
use App\Models\TasksRevision;
use Carbon\Carbon;
use App\Models\UserMessages;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    public $user_messages;
    public function __construct(){
//        $this->middleware('auth',['except'=>['index','view','get_tasks_paginate','lists','search_by_skills','search_by_status','search_tasks']]);
//        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));
//        view()->share('site_activity_text','Unit Activity Log');
//        $this->user_messages = new UserMessages;
    }

    public function revison($task_id){
        view()->share('task_id',$task_id);
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::with(['objective','task_documents'])->find($task_id);
                if(empty($taskObj))
                    return ('errors.404');

                $taskObj->unit=[];
                if(!empty($taskObj->objective))
                    $taskObj->unit = Unit::getUnitWithCategories($taskObj->unit_id);
                if(!empty($taskObj)){
                    view()->share('taskObj',$taskObj );

                    $flag =Task::isUnitAdminOfTask($task_id); // right now considered only unit admin can assigned task to bidder if they
                    // want task creator can also assign task to bidder then remove this and uncomment above lines
                    $taskBidders = [];
                    if($flag){
                        $taskBidders = TaskBidder::join('users','task_bidders.user_id','=','users.id')
                                        ->select(['users.first_name','users.last_name','users.id as user_id','task_bidders.*'])
                                        ->where('task_id',$task_id)->get();
                    }
                    view()->share('taskBidders',$taskBidders);
                    // end display listing of bidders

                    $availableFunds =Fund::getTaskDonatedFund($task_id);
                    $awardedFunds =Fund::getTaskAwardedFund($task_id);

                    view()->share('availableFunds',$availableFunds );
                    view()->share('awardedFunds',$awardedFunds );

                    $availableUnitFunds =Fund::getUnitDonatedFund($taskObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($taskObj->unit_id);

                    view()->share('availableUnitFunds',$availableUnitFunds );
                    view()->share('awardedUnitFunds',$awardedUnitFunds );


                    // Forum Object coading
                    view()->share("unit_id", $taskObj->unit_id);
                    view()->share("section_id", 2);
                    view()->share("object_id",$taskObj->id);

                    $site_activity = SiteActivity::where('unit_id',$taskObj->unit_id)
                                    ->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));

                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$taskObj->unit_id);

                    $revisions = TasksRevision::select(['tasks_revisions.user_id','tasks_revisions.id','tasks_revisions.unit_id','tasks_revisions.comment','tasks_revisions.size','tasks_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'tasks_revisions.user_id')
                            ->where("tasks_revisions.unit_id","=",$taskObj->unit_id)
                            ->where("tasks_revisions.task_id","=",$taskObj->id)
                            ->get();

                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

                    view()->share('userIDHashID', $userIDHashID);
                    view()->share('Carbon', new Carbon);
                    view()->share('revisions',$revisions );

                    return view('tasks.revison.view');
                }
            }
        }
        return view('errors.404');
    }

    public function revisonview($task_id,$revision_id){
        view()->share('task_id',$task_id);
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::with(['objective','task_documents'])->find($task_id);
                if(empty($taskObj))
                    return ('errors.404');

                $taskObj->unit=[];
                if(!empty($taskObj->objective))
                    $taskObj->unit = Unit::getUnitWithCategories($taskObj->unit_id);
                if(!empty($taskObj)){
                    view()->share('taskObj',$taskObj );

                    $flag =Task::isUnitAdminOfTask($task_id); // right now considered only unit admin can assigned task to bidder if they
                    // want task creator can also assign task to bidder then remove this and uncomment above lines
                    $taskBidders = [];
                    if($flag){
                        $taskBidders = TaskBidder::join('users','task_bidders.user_id','=','users.id')
                                        ->select(['users.first_name','users.last_name','users.id as user_id','task_bidders.*'])
                                        ->where('task_id',$task_id)->get();
                    }
                    view()->share('taskBidders',$taskBidders);
                    // end display listing of bidders

                    $availableFunds =Fund::getTaskDonatedFund($task_id);
                    $awardedFunds =Fund::getTaskAwardedFund($task_id);

                    view()->share('availableFunds',$availableFunds );
                    view()->share('awardedFunds',$awardedFunds );

                    $availableUnitFunds =Fund::getUnitDonatedFund($taskObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($taskObj->unit_id);

                    view()->share('availableUnitFunds',$availableUnitFunds );
                    view()->share('awardedUnitFunds',$awardedUnitFunds );


                    // Forum Object coading
                    view()->share("unit_id", $taskObj->unit_id);
                    view()->share("section_id", 2);
                    view()->share("object_id",$taskObj->id);

                    $site_activity = SiteActivity::where('unit_id',$taskObj->unit_id)
                                    ->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));

                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$taskObj->unit_id);

                    $revisions = TasksRevision::select(['tasks_revisions.user_id','tasks_revisions.id','tasks_revisions.description','tasks_revisions.unit_id','tasks_revisions.comment','tasks_revisions.size','tasks_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'tasks_revisions.user_id')
                            ->where("tasks_revisions.unit_id","=",$taskObj->unit_id)
                            ->where("tasks_revisions.task_id","=",$taskObj->id)
                            ->where("tasks_revisions.id","=",$revision_id)
                            ->get();

                    if($revisions->count()){

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

                        view()->share('userIDHashID', $userIDHashID);
                        view()->share('revisions',$revisions->first() );

                        return view('tasks.revison.view_revision');
                    }
                }
            }
        }
        return view('errors.404');
    }

    public function diff($task_id,$rev1,$rev2,Request $request){
        view()->share('task_id',$task_id);
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::with(['objective','task_documents'])->find($task_id);
                if(empty($taskObj))
                    return ('errors.404');

                $taskObj->unit=[];
                if(!empty($taskObj->objective))
                    $taskObj->unit = Unit::getUnitWithCategories($taskObj->unit_id);
                if(!empty($taskObj)){
                    view()->share('taskObj',$taskObj );

                    $flag =Task::isUnitAdminOfTask($task_id); // right now considered only unit admin can assigned task to bidder if they
                    // want task creator can also assign task to bidder then remove this and uncomment above lines
                    $taskBidders = [];
                    if($flag){
                        $taskBidders = TaskBidder::join('users','task_bidders.user_id','=','users.id')
                                        ->select(['users.first_name','users.last_name','users.id as user_id','task_bidders.*'])
                                        ->where('task_id',$task_id)->get();
                    }
                    view()->share('taskBidders',$taskBidders);
                    // end display listing of bidders

                    $availableFunds =Fund::getTaskDonatedFund($task_id);
                    $awardedFunds =Fund::getTaskAwardedFund($task_id);

                    view()->share('availableFunds',$availableFunds );
                    view()->share('awardedFunds',$awardedFunds );

                    $availableUnitFunds =Fund::getUnitDonatedFund($taskObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($taskObj->unit_id);

                    view()->share('availableUnitFunds',$availableUnitFunds );
                    view()->share('awardedUnitFunds',$awardedUnitFunds );


                    // Forum Object coading
                    view()->share("unit_id", $taskObj->unit_id);
                    view()->share("section_id", 2);
                    view()->share("object_id",$taskObj->id);

                    $forumID =  Forum::checkTopic(array(
                        'unit_id' => $taskObj->unit_id,
                        'section_id' => 2,
                        'object_id' => $taskObj->id,
                    ));

                    if(!empty($forumID)){
                        view()->share('addComments', url('forum/post/'. $forumID->topic_id .'/'. $forumID->slug ) );
                    }

                    $site_activity = SiteActivity::where('unit_id',$taskObj->unit_id)
                                    ->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));

                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$taskObj->unit_id);

                    $revisions = TasksRevision::select(['tasks_revisions.user_id','tasks_revisions.id','tasks_revisions.unit_id','tasks_revisions.comment','tasks_revisions.size','tasks_revisions.description','tasks_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'tasks_revisions.user_id')
                            ->where("tasks_revisions.unit_id","=",$taskObj->unit_id)
                            ->where("tasks_revisions.task_id","=",$taskObj->id)
                            ->whereIn("tasks_revisions.id",[ (int)$rev1, (int)$rev2 ])
                            ->get();

                    if($revisions->count() == 2){
                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));

                        view()->share('userIDHashID', $userIDHashID);
                        view()->share('Carbon', new Carbon);
                        view()->share('revisions',$revisions );

                        return view("tasks.revison.changes_difference");
                    }


                }
            }
        }
        return view('errors.404');
    }


    public function index(Request $request)
    {
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
        view()->share('site_activity_text','Global Activity Log');

        //\DB::enableQueryLog();
        $tasks = DB::table('tasks')
            ->join('objectives','tasks.objective_id','=','objectives.id')
            ->join('units','tasks.unit_id','=','units.id')
            ->join('users','tasks.user_id','=','users.id')
            ->select(['tasks.*','units.name as unit_name','users.first_name','users.last_name',
                'users.id as user_id','objectives.name as objective_name'])
            ->whereNull('tasks.deleted_at')
            ->orderBy('tasks.id','desc')
            ->paginate(5);
        //dd(\DB::getQueryLog());


        $site_activity = SiteActivity::orderBy('id',
            'desc')->paginate(Config::get('app.site_activity_page_limit'));


        view()->share('site_activity',$site_activity);
        view()->share('tasks',$tasks);
        return view('tasks.tasks');
    }


    public function create(Request $request)
    {

        $segments =$request->segments();

        $taskObjectiveObj = [];
        $task_unit_id = null;
        $task_objective_id = null;

        $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
        $objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
        $taskUnitObj= [];
        $availableUnitFunds='';
        $awardedUnitFunds='';

        if(count($segments) == 4)
        {

            $task_unit_id = $request->segment(2);
            $task_objective_id = $request->segment(3);

            if(empty($task_unit_id) || empty($task_objective_id))
                return view('errors.404');


            $task_unit_id = $unitIDHashID->decode($task_unit_id);


            $task_objective_id = $objectiveIDHashID->decode($task_objective_id);

            if(empty($task_unit_id) || empty($task_objective_id))
                return view('errors.404');

            $task_unit_id = $task_unit_id[0];
            $task_objective_id = $task_objective_id[0];

            $taskUnitObj = Unit::find($task_unit_id);
            $taskObjectiveObj = Objective::find($task_objective_id);

            if(empty($taskUnitObj) || empty($taskObjectiveObj))
                return view('errors.404');

            $availableUnitFunds =Fund::getUnitDonatedFund($task_unit_id);
            $awardedUnitFunds =Fund::getUnitAwardedFund($task_unit_id);

            $taskObjectiveObj = Objective::where('unit_id',$task_unit_id)->get();
        }
        else
        {
            //if add taks from unit view page then show unit info table as we implement in add objective on unit view page.
            $unit_id = $request->get('unit');
            if(!empty($unit_id)){
                $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
                $unit_id = $unitIDHashID->decode($unit_id);
                if(!empty($unit_id))
                {
                    $unit_id = $unit_id[0];
                    $taskUnitObj = Unit::find($unit_id);
                    if(!empty($taskUnitObj))
                    {
                        $availableUnitFunds =Fund::getUnitDonatedFund($unit_id);
                        $awardedUnitFunds =Fund::getUnitAwardedFund($unit_id);

                        $taskObjectiveObj = Objective::where('unit_id',$unit_id)->get();
                    }
                }
            }
        }
        // ********************* make selected unitid and objectiveid from url in "add" mode **************************
        view()->share('unitInfo',$taskUnitObj);
        view()->share('availableUnitFunds',$availableUnitFunds);
        view()->share('awardedUnitFunds',$awardedUnitFunds);
        view()->share('task_unit_id',$task_unit_id);
        view()->share('task_objective_id',$task_objective_id);
        view()->share('unit_activity_id',$task_objective_id);


        // ********************* end **************************

        $unitsObj = Unit::where('status','active')->pluck('name','id');
        $task_skills = JobSkill::pluck('skill_name','id');
        $assigned_toUsers = User::where('id','!=',Auth::user()->id)->where('role','!=','superadmin')->get();
        $assigned_toUsers= $assigned_toUsers->pluck('full_name','id');
        view()->share('assigned_toUsers',$assigned_toUsers);
        view()->share('task_skills',$task_skills );
        view()->share('unitsObj',$unitsObj);
        view()->share('objectiveObj',$taskObjectiveObj );
        view()->share('taskObj',[]);
        view()->share('taskDocumentsObj',[]);
        //view()->share('taskActionsObj',[]);
        view()->share('exploded_task_list',[]);
        view()->share('editFlag',false);
        view()->share('actionListFlag',false);
        if($request->isMethod('post')){

            $validator = Validator::make($request->all(), [
                'unit' => 'required',
                'objective' => 'required',
                'task_name' => 'required',
                'task_skills' => 'required',
                'estimated_completion_time_start' => 'required',
                'estimated_completion_time_end' => 'required',
                'description'=>'required'
            ]);

            if ($validator->fails())
                return redirect()->back()->withErrors($validator)->withInput();

            // check unit id exist in db
            $unit_id = $request->input('unit');
            $unitIDEncoded = $unit_id;
            $flag =Unit::checkUnitExist($unit_id ,true);
            if(!$flag)
                return redirect()->back()->withErrors(['unit'=>'Unit doesn\'t exist in database.'])->withInput();


            // check objective id exist in db
            $objective_id = $request->input('objective');
            $objectiveIDEncoded = $objective_id;
            $flag =Objective::checkObjectiveExist($objective_id ,true); // pass objective_id and true for decode the string
            if(!$flag)
                return redirect()->back()->withErrors(['objective'=>'Objective doesn\'t exist in database.'])->withInput();


            $unit_id = $unitIDHashID->decode($unit_id);
            $objective_id = $objectiveIDHashID->decode($objective_id);

            $start_date = '';
            $end_date = '';
            try {
                $start_date  = new \DateTime($request->input('estimated_completion_time_start'));
                $end_date     = new \DateTime($request->input('estimated_completion_time_end'));
            } catch (\Exception $e) {
                echo $e->getMessage();
                exit(1);
            }
            $start_date = $start_date->getTimestamp();
            $end_date  = $end_date->getTimestamp();

            // create task

            $slug=substr(str_replace(" ","_",strtolower($request->input('task_name'))),0,20);


            $task_skills =$request->input('task_skills');
            if(!empty($task_skills))
                $task_skills  = implode(",",$task_skills );
            $task_id = Task::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=>$unit_id[0],
                'objective_id'=>$objective_id[0],
                'name'=>$request->input('task_name'),
                'slug'=>$slug,
                'description'=>$request->input('description'),
                'summary'=>$request->input('summary'),
                'skills'=>$task_skills,
                'estimated_completion_time_start'=>date('Y-m-d h:i',$start_date),
                'estimated_completion_time_end'=>date('Y-m-d h:i',$end_date),
                'task_action'=>$request->input('action_items'),
                'compensation'=>$request->input('compensation'),
                'status'=>'editable'
            ])->id;


            $task_id_decoded= $task_id;
            $taskIDHashID= new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->encode($task_id);

            // insert action items of task.
            /*$action_items_ar = $request->input('action_items_array');
            if(!empty($action_items_ar)){
                foreach($action_items_ar as $item){
                    $item = strip_tags($item);
                    if(!empty($item)){
                        TaskAction::create([
                            'user_id'=>Auth::user()->id,
                            'task_id'=>$task_id_decoded,
                            'name'=>$item,
                            'description'=>'',
                            'status'=>'active'
                        ]);
                    }
                }
            }*/

            // upload documents of task.

            if($request->hasFile('documents')) {
                $files = $request->file('documents');
                if(count($files) > 0){
                    $totalAvailableDocs = TaskDocuments::where('task_id',$task_id_decoded)->get();
                    $totalAvailableDocs= count($totalAvailableDocs) + 1;
                    foreach($files as $index=>$file){
                        if(!empty($file)){
                            $rules = ['document' => 'required', 'extension' => 'required|in:doc,docx,pdf,txt,jpg,png,ppt,pptx,jpeg,doc,xls,xlsx'];
                            $fileData = ['document' => $file, 'extension' => strtolower($file->getClientOriginalExtension())];

                            // doing the validation, passing post data, rules and the messages
                            $validator = Validator::make($fileData, $rules);
                            if (!$validator->fails()) {
                               if ($file->isValid()) {
                                    $destinationPath = base_path().'/uploads/tasks/'.$task_id; // upload path
                                    if(!\File::exists($destinationPath)){
                                        $oldumask = umask(0);
                                        @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                                        umask($oldumask);
                                    }
                                    $file_name =$file->getClientOriginalName();
                                    $extension = $file->getClientOriginalExtension(); // getting image extension
                                    //$fileName = $task_id.'_'.$index . '.' . $extension; // renaming image
                                    $fileName = $task_id.'_'.$totalAvailableDocs . '.' . $extension; // renaming image
                                    $file->move($destinationPath, $fileName); // uploading file to given path

                                    // insert record into task_documents table
                                    $path = $destinationPath.'/'.$fileName;
                                    TaskDocuments::create([
                                        'task_id'=>$task_id_decoded,
                                        'file_name'=>$file_name,
                                        'file_path'=>'uploads/tasks/'.$task_id.'/'.$fileName
                                    ]);
                                   $totalAvailableDocs++;
                                }
                            }
                        }
                    }
                }
            }
            // add activity point for created task.

            ActivityPoint::create([
                'user_id'=>Auth::user()->id,
                'task_id'=>$task_id_decoded,
                'points'=>5,
                'comments'=>'Task Created',
                'type'=>'task'
            ]);

            // add site activity record for global statistics.


            $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);

            $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
            if(!empty(Auth::user()->username))
                $user_name =Auth::user()->username;

            // send alert to user(s) who has this unit in his/her watchlist

            $unit_id=$unit_id[0];
            $objective_id = $objective_id[0];
            $watchlistUserObj = \DB::table('my_watchlist')
                ->join('users','my_watchlist.user_id','=','users.id')
                ->where('my_watchlist.user_id','!=',Auth::user()->id)
                ->where(function ($query) use($objective_id,$unit_id)  {
                    $query->where('objective_id',$objective_id)->orWhere('unit_id',$unit_id);
                })->get();

            $objectiveObj = Objective::find($objective_id);

            $content = 'User <a href="'.url('userprofiles/'.$userIDHashID->encode(Auth::user()->id).'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'.strtolower(Auth::user()->first_name.' '.Auth::user()->last_name).'</a>' .
                ' created Task <a href="'.url('tasks/'.$task_id.'/'.$slug).'">'.$request->input('task_name')
                .'</a> in Objective <a href="'.url('objectives/'.$objectiveIDEncoded.'/'.$objectiveObj->slug).'">'.$objectiveObj->name.'</a>';

            $email_subject = 'User '.Auth::user()->first_name . ' ' . Auth::user()->last_name.' created task '.$request->input('task_name').
                ' in objective '.$objectiveObj->name;
            User::SendEmailAndOnSiteAlert($content,$email_subject,$watchlistUserObj,$onlyemail=false,'watched_items');

            SiteActivity::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=>$unit_id[0],
                'objective_id'=>$objective_id[0],
                'task_id'=>$task_id,
                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                    .$user_name.'</a>
                        created task <a href="'.url('tasks/'.$task_id.'/'.$slug).'">'.$request->input('task_name').'</a>'
            ]);

            // TODO: create forum entry when task is created : in PDF page no - 10

            // mail send if alert is on
            $alertObj = Alerts::where('user_id',Auth::user()->id)->first();
            if(!empty($alertObj) && $alertObj->task_management == 1) {
                $toEmail = Auth::user()->email;
                $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                $subject = 'Task created successfully. ';

                \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                    $message->to($toEmail, $toName)->subject($subject);
                    $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                });
            }


            // After Created Unit send mail to site admin
            $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
            $unitCreator = User::find(Auth::user()->id);

            $toEmail = $unitCreator->email;
            $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
            $subject="Task Created";

        //    \Mail::send('emails.registration', ['userObj'=> $unitCreator, 'report_concern' => false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
        //    {
        //        $message->to($toEmail,$toName)->subject($subject);
        //        if(!empty($siteAdminemails))
        //            $message->bcc($siteAdminemails,"Admin")->subject($subject);

        //        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
        //    });

            $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_CREATED')['text']);

            return redirect('tasks/'.$task_id.'/'.$slug);
        }

        return view('tasks.create');
    }


    public function edit(Request $request,$task_id,$change_status = false){
        if(!empty($task_id))
        {
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $task = Task::find($task_id);

                // if user submit the form then update the data.
                if($request->isMethod('post') && !empty($task)){
                    //change task status if user unitAdmin
                    if($change_status || (Task::isUnitAdminOfTask($task_id) && $task->status != "editable")){
                        Task::where('id',$task_id)->update([
                            'status'=> $request->input('task_status')
                        ]);
                        $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_UPDATED')['text']);
                        return redirect('tasks/'.$taskIDHashID->encode($task_id).'/'.$task->slug);
                    }
                    //end here
                    if($task->status == "awaiting_approval" || $task->status == "approval"){
                        return redirect()->back()->withErrors(['unit'=>'You can\'t edit task.'])->withInput();
                    }
                    $validator = Validator::make($request->all(), [
                        'unit' => 'required',
                        'objective' => 'required',
                        'task_name' => 'required',
                        'task_skills' => 'required',
                        'estimated_completion_time_start' => 'required',
                        'estimated_completion_time_end' => 'required',
                        'description'=>'required'
                    ]);

                    if ($validator->fails())
                       return redirect()->back()->withErrors($validator)->withInput();

                    // if user didn't change anything then just redirect to task listing page.
                    $updatedFields= $request->input('changed_items');

                    // check unit id exist in db
                    $unit_id = $request->input('unit');
                    $unitIDEncoded = $unit_id;
                    $flag =Unit::checkUnitExist($unit_id ,true);
                    if(!$flag)
                        return redirect()->back()->withErrors(['unit'=>'Unit doesn\'t exist in database.'])->withInput();


                    // check objective id exist in db
                    $objective_id = $request->input('objective');
                    $objectiveIDEncoded=$objective_id;
                    $flag =Objective::checkObjectiveExist($objective_id ,true); // pass objective_id and true for decode the string
                    if(!$flag)
                        return redirect()->back()->withErrors(['objective'=>'Objective doesn\'t exist in database.'])->withInput();

                    $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
                    $unit_id = $unitIDHashID->decode($unit_id);

                    $objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
                    $objective_id = $objectiveIDHashID->decode($objective_id);

                    $start_date = '';
                    $end_date = '';
                    try {
                        $start_date  = new \DateTime($request->input('estimated_completion_time_start'));
                        $end_date     = new \DateTime($request->input('estimated_completion_time_end'));
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                        exit(1);
                    }
                    $start_date = $start_date->getTimestamp();
                    $end_date  = $end_date->getTimestamp();

                    // update task
                    $slug=substr(str_replace(" ","_",strtolower($request->input('task_name'))),0,20);
                    $task_skills =$request->input('task_skills');
                    if(!empty($task_skills))
                        $task_skills  = implode(",",$task_skills );

                    /* Store old data Start */
                        $bytes = TasksRevision::strBytes( str_replace(' ', '', strip_tags($request->input('description'))) );
                        $oldBytes = TasksRevision::strBytes( str_replace(' ', '', strip_tags($task->description)) );

                        $TasksRevision = new TasksRevision;
                        $TasksRevision->user_id = $task->user_id;
                        $TasksRevision->unit_id = $task->unit_id;
                        $TasksRevision->objective_id = $task->objective_id;
                        $TasksRevision->name = $task->name;
                        $TasksRevision->description = $task->description;
                        $TasksRevision->task_action = $task->task_action;
                        $TasksRevision->summary = $task->summary;
                        $TasksRevision->skills = $task->skills;
                        $TasksRevision->estimated_completion_time_start = $task->estimated_completion_time_start;
                        $TasksRevision->estimated_completion_time_end = $task->estimated_completion_time_end;
                        $TasksRevision->compensation = $task->compensation;
                        $TasksRevision->assign_to = (int)$task->assign_to;
                        $TasksRevision->status = $task->status;
                        $TasksRevision->modified_by = Auth::user()->id;
                        $TasksRevision->created_at = date("Y-m-d H:i:s");;
                        $TasksRevision->updated_at = $task->created_at;
                        $TasksRevision->deleted_at = $task->created_at;
                        $TasksRevision->comment = $task->comment. " ";
                        $TasksRevision->task_id = $task->id;
                        $TasksRevision->size = (  $bytes - $oldBytes );

                        $TasksRevision->save();
                    /* Store old data End */


                    Task::where('id',$task_id)->update([
                        //'user_id'=>Auth::user()->id,
                        'unit_id'=>$unit_id[0],
                        'objective_id'=>$objective_id[0],
                        'name'=>$request->input('task_name'),
                        'slug'=>$slug,
                        'description'=>$request->input('description'),
                        'summary'=>$request->input('summary'),
                        'comment'=>$request->input('comment'),
                        'skills'=>$task_skills,
                        'estimated_completion_time_start'=>date('Y-m-d h:i',$start_date),
                        'estimated_completion_time_end'=>date('Y-m-d h:i',$end_date),
                        'task_action'=>trim($request->input('action_items')),
                        'compensation'=>$request->input('compensation'),
                        'status'=>'editable'
                    ]);
                    //Task admin can change task status
                    if(Task::isUnitAdminOfTask($task_id)){
                        Task::where('id',$task_id)->update([
                            'status'=> $request->input('task_status')
                        ]);
                    }

                    $task_id_decoded= $task_id;
                    $taskObjTemp = Task::find($task_id);
                    $taskIDHashID= new Hashids('task id hash',10,\Config::get('app.encode_chars'));
                    $task_id = $taskIDHashID->encode($task_id);

                    // upload documents of task.
                    $task_documents=[];
                    if($request->hasFile('documents')) {
                        $files = $request->file('documents');
                        if(count($files) > 0){
                           \DB::enableQueryLog();
                            $totalAvailableDocs = TaskDocuments::where('task_id',$task_id_decoded)->get();
                            $totalAvailableDocs= count($totalAvailableDocs) + 1;
                            foreach($files as $index=>$file){
                                if(!empty($file)){

                                    $rules = ['document' => 'required', 'extension' => 'required|in:doc,docx,pdf,txt,jpg,png,ppt,pptx,jpeg,doc,xls,xlsx'];
                                    $fileData = ['document' => $file, 'extension' => strtolower($file->getClientOriginalExtension())];

                                    // doing the validation, passing post data, rules and the messages
                                    $validator = \Validator::make($fileData, $rules);
                                    if (!$validator->fails()) {
                                        if ($file->isValid()) {
                                            $destinationPath = base_path().'/uploads/tasks/'.$task_id; // upload path
                                            if(!\File::exists($destinationPath)){
                                                $oldumask = umask(0);
                                                @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                                                umask($oldumask);
                                            }

                                            $file_name =$file->getClientOriginalName();
                                            $extension = $file->getClientOriginalExtension(); // getting image extension
                                            $fileName = $task_id.'_'.$totalAvailableDocs . '.' . $extension; // renaming image
                                            $file->move($destinationPath, $fileName); // uploading file to given path

                                            // insert record into task_documents table
                                            $task_documents[]=['task_id'=>$task_id_decoded,'file_name'=>$file_name,
                                                'file_path'=>'uploads/tasks/'.$task_id.'/'.$fileName];

                                            TaskDocuments::create([
                                                'task_id'=>$task_id_decoded,
                                                'file_name'=>$file_name,
                                                'file_path'=>'uploads/tasks/'.$task_id.'/'.$fileName
                                            ]);
                                            $totalAvailableDocs++;
                                        }
                                    }
                                }

                            }
                        }
                    }

                    // if user edited record second time or more than get updatedFields of last edited record and merge with new
                    // updatefields and store into new history. because we will display only updated value to unit admin.

                    $taskHistoryObj = TaskEditor::join('task_history','task_editors.task_history_id','=','task_history.id')
                        ->where('task_editors.user_id',Auth::user()->id)
                        ->where('task_id',$task_id_decoded)
                        ->orderBy('task_history.id','desc')
                        ->first();
                    if(!empty($taskHistoryObj)){
                        $oldUpdatedFields= json_decode($taskHistoryObj->updatedFields);
                        if(!empty($oldUpdatedFields) && !empty($updatedFields))
                            $updatedFields = array_merge($updatedFields,$oldUpdatedFields );

                    }

                    // add record into task_history for task history.
                    $task_history_id =TaskHistory::create([
                        'unit_id'=>$unit_id[0],
                        'objective_id'=>$objective_id[0],
                        'name'=>$request->input('task_name'),
                        'description'=>$request->input('description'),
                        'summary'=>$request->input('summary'),
                        'skills'=>implode(",",$request->input('task_skills')),
                        'estimated_completion_time_start'=>date('Y-m-d h:i',$start_date),
                        'estimated_completion_time_end'=>date('Y-m-d h:i',$end_date),
                        'task_action'=>trim($request->input('action_items')),
                        'task_documents'=>json_encode($task_documents),
                        'compensation'=>$request->input('compensation'),
                        'updatedFields'=>json_encode($updatedFields)
                    ])->id;

                    $taskEditorObj = TaskEditor::where('task_id',$task_id_decoded)->where('user_id',Auth::user()->id)->count();
                    if($taskEditorObj > 0){
                        TaskEditor::where('task_id',$task_id_decoded)->where('user_id',Auth::user()->id)->update([
                            'task_history_id'=>$task_history_id
                        ]);
                    }
                    else{
                        TaskEditor::create([
                            'task_id'=>$task_id_decoded,
                            'task_history_id'=>$task_history_id,
                            'user_id'=>Auth::user()->id,
                            'submit_for_approval'=>'not_submitted'
                        ]);
                    }

                    // add activity point for created task.

                    ActivityPoint::create([
                        'user_id'=>Auth::user()->id,
                        'task_id'=>$task_id_decoded,
                        'points'=>2,
                        'comments'=>'Task Updated',
                        'type'=>'task'
                    ]);

                    // add site activity record for global statistics.


                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    $objective_id = $objective_id[0];
                    $unit_id = $unit_id[0];
                    // send alert to user(s) who has this unit in his/her watchlist
                    $watchlistUserObj = \DB::table('my_watchlist')
                        ->join('users','my_watchlist.user_id','=','users.id')
                        ->where('my_watchlist.user_id','!=',Auth::user()->id)
                        ->where(function ($query) use($objective_id,$unit_id,$task_id_decoded) {
                            $query->where('objective_id',$objective_id)->orWhere('unit_id',$unit_id)->orWhere('task_id',$task_id_decoded);
                        })
                        ->get();

                    $objectiveObj = Objective::find($objective_id);

                    $content = 'User <a href="'.url('userprofiles/'.Auth::user()->id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name).'</a>' .
                        ' edited Task <a href="'.url('tasks/'.$task_id.'/'.$slug).'">'.$request->input('task_name')
                        .'</a> in Objective <a href="'.url('objectives/'.$objectiveIDEncoded.'/'.$objectiveObj->slug).'">'.$objectiveObj->name.'</a>';

                    $email_subject = 'User '.Auth::user()->first_name . ' ' . Auth::user()->last_name.' edited task '.$request->input('task_name').
                        ' in objective '.$objectiveObj->name;

                    \App\User::SendEmailAndOnSiteAlert($content,$email_subject,$watchlistUserObj,$onlyemail=false,'watched_items');

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$unit_id[0],
                        'objective_id'=>$objective_id[0],
                        'task_id'=>$task_id_decoded,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .$user_name.'</a>
                        updated task <a href="'.url('tasks/'.$task_id.'/'.$slug).'">'.$request->input('task_name').'</a>'
                    ]);

                    // mail send
                   /* $alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                    if(!empty($alertObj) && $alertObj->task_management == 1) {
                        $toEmail = Auth::user()->email;
                        $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                        $subject = 'Task updated successfully. ';

                        \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                            $message->to($toEmail, $toName)->subject($subject);
                            $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                        });
                    }*/


                    // After Created Unit send mail to site admin
                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find(Auth::user()->id);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Task Updated";

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator, 'report_concern' => false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                    });

                    $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_UPDATED')['text']);
                    return redirect('tasks/'.$taskIDHashID->encode($taskObjTemp->id).'/'.$taskObjTemp->slug);
                }

                $unitsObj = Unit::where('status','active')->pluck('name','id');
                $task_skills = JobSkill::pluck('skill_name','id');
                $assigned_toUsers = User::where('id','!=',Auth::user()->id)->where('role','!=','superadmin')->get();
                $assigned_toUsers= $assigned_toUsers->pluck('full_name','id');
                $taskObj = $task;
                $taskObj->task_action = str_replace(array("\r", "\n"), '', $taskObj->task_action);
                $objectiveObj = Objective::where('unit_id',$taskObj->unit_id)->get();
                $taskDocumentsObj = TaskDocuments::where('task_id',$task_id)->get();
                //$taskActionsObj = TaskAction::where('task_id',$task_id)->get();
                $taskEditor = TaskEditor::where('task_id',$task_id)->where('user_id',Auth::user()->id)->first();
                $otherRemainEditors = TaskEditor::where('task_id',$task_id)
                                    ->where('user_id','!=',Auth::user()->id)->where('submit_for_approval','not_submitted')->get();
                $otherEditorsDone = TaskEditor::where('task_id',$task_id)
                    ->where('user_id','!=',Auth::user()->id)->where('submit_for_approval','submitted')->get();

                $firstUserSubmitted = TaskEditor::where('task_id',$task_id)
                    ->where('user_id','!=',Auth::user()->id)->where('submit_for_approval','submitted')->where('first_user_to_submit','yes')
                    ->first();

                $availableDays ='';
                if(!empty($firstUserSubmitted)){
                    $submittedDate = strtotime($firstUserSubmitted->updated_at);
                    $availableDays = time() - $submittedDate;
                    $availableDays = 8 - (int)date('d',$availableDays );

                }

                view()->share('objectiveObj',$objectiveObj);
                view()->share('assigned_toUsers',$assigned_toUsers);
                view()->share('task_skills',$task_skills );
                view()->share('unitsObj',$unitsObj);
                view()->share('taskObj',$taskObj);
                view()->share('taskDocumentsObj',$taskDocumentsObj);
                view()->share('taskEditor',$taskEditor);
                view()->share('otherRemainEditors',$otherRemainEditors );
                view()->share('otherEditorsDone',$otherEditorsDone);
                view()->share('availableDays',$availableDays);

                //view()->share('taskActionsObj',$taskActionsObj);
                $taskObj->estimated_completion_time_start = date('Y/m/d h:i',strtotime($taskObj->estimated_completion_time_start));
                $taskObj->estimated_completion_time_end = date('Y/m/d h:i',strtotime($taskObj->estimated_completion_time_end));
                $exploded_task_list = explode(",",$taskObj->skills);


                $availableUnitFunds =Fund::getUnitDonatedFund($taskObj->unit_id);
                $awardedUnitFunds =Fund::getUnitAwardedFund($taskObj->unit_id);
                $unitObjForLeftBar = Unit::find($taskObj->unit_id);

                view()->share('availableUnitFunds',$availableUnitFunds );
                view()->share('awardedUnitFunds',$awardedUnitFunds );
                view()->share('unit_activity_id',$taskObj->unit_id);
                view()->share('unitObjForLeftBar',$unitObjForLeftBar);

                view()->share('exploded_task_list',$exploded_task_list );
                view()->share('editFlag',true);
                view()->share('actionListFlag',$taskObj->task_action);
                view()->share('change_task_status',$change_status);
                /*if(count($taskDocumentsObj) > 0)
                    view()->share('actionListFlag',true);
                else
                    view()->share('actionListFlag',false);*/
                return view('tasks.create');
            }
        }
        return view('errors.404');
    }

    /**
     * soft deleting the document of given task_id
     * @param Request $request
     * @return mixed
     */
    public function remove_task_documents(Request $request){

        $task_id = $request->input('task_id');
        $id = $request->input('id');
        $fromEdit = $request->input('fromEdit');

        $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
        $taskDocumentIDHashID = new Hashids('task document id hash',10,\Config::get('app.encode_chars'));

        $task_id = $taskIDHashID->decode($task_id);

        if(empty($task_id)){
            return \Response::json(['success'=>false]);
        }
        $task_id = $task_id[0];

        if($fromEdit  == "yes"){
            $taskHistoryObj = TaskEditor::join('task_history','task_editors.task_history_id','=','task_history.id')
                            ->where('task_id',$task_id)->where('user_id',Auth::user()->id)->orderBy('task_history.id','desc')->first();
            if(!empty($taskHistoryObj)){
                $taskDocuments = json_decode($taskHistoryObj->task_documents);
                if(!empty($taskDocuments)){
                    foreach($taskDocuments as $index=>$document)
                    {
                        if($id == $index){
                            if(file_exists($document->file_path))
                                unlink($document->file_path);
                            unset($taskDocuments[$index]);
                        }
                    }
                    TaskHistory::find($taskHistoryObj->id)->update(['task_documents'=>json_encode($taskDocuments)]);
                    return \Response::json(['success'=>true]);
                }
            }
        }
        else{
            $id = $taskDocumentIDHashID->decode($id);
            if(empty($id)){
                return \Response::json(['success'=>false]);
            }
            $id= $id[0];
            $taskDocumentObj = TaskDocuments::where('task_id',$task_id)->where('id',$id)->get();

            if(count($taskDocumentObj) > 0){
                TaskDocuments::where('task_id',$task_id)->where('id',$id)->delete();
                return \Response::json(['success'=>true]);
            }
        }
        return \Response::json(['success'=>false]);
    }

    /**
     * retrieve objective of selected unit_id
     * @param Request $request
     * @return mixed
     */
    public function get_objective(Request $request){
        $unit_id = $request->input('unit_id');
        if(!empty($unit_id)){
            $unitIDHashID = new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            $objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $unitObj = Unit::where('id',$unit_id)->get();
                if(count($unitObj) > 0){
                    $objectivesObj = Objective::where('unit_id',$unit_id)->pluck('name','id');
                    $return_arr = [];
                    if(count($objectivesObj) > 0){
                        foreach($objectivesObj as $id=>$val)
                            $return_arr[$objectiveIDHashID->encode($id)] = $val;
                    }
                    return \Response::json(['success'=>true,'objectives'=>$return_arr]);
                }
            }
        }
        return \Response::json(['success'=>false]);
    }

    public function get_tasks(Request $request){
        $obj_id = $request->input('obj_id');
        if(!empty($obj_id)){
            $objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
            $obj_id = $objectiveIDHashID->decode($obj_id);
            if(!empty($obj_id)){
                $obj_id = $obj_id[0];
                $objectiveObj = Objective::where('id',$obj_id)->get();
                if(count($objectiveObj) > 0){
                    $taskObj = Task::where('objective_id',$obj_id)->pluck('name','id');
                    $return_arr = [];
                    $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
                    if(count($taskObj) > 0){
                        foreach($taskObj as $id=>$val)
                            $return_arr[$taskIDHashID->encode($id)] = $val;
                    }
                    return \Response::json(['success'=>true,'tasks'=>$return_arr]);
                }
            }
        }
        return \Response::json(['success'=>false]);
    }

    /**
     * function is used to display task details.
     * @param $task_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($task_id,Request $request){
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::with(['objective','task_documents'])->find($task_id);
                if(empty($taskObj))
                    return ('errors.404');

                $taskObj->unit=[];
                if(!empty($taskObj->objective))
                    $taskObj->unit = Unit::getUnitWithCategories($taskObj->unit_id);
                if(!empty($taskObj)){

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

                    //calculate date for task completion time
                    $completion_time_start = date_create($taskObj->estimated_completion_time_start);
                    $completion_time_end = date_create($taskObj->estimated_completion_time_end);
                    $dateDifferent = date_diff($completion_time_start,$completion_time_end);
                    $taskObj->completionTime = $dateDifferent->format("%a days");
                    if($dateDifferent->format("%h") > 0)
                        $taskObj->completionTime = $taskObj->completionTime." ".$dateDifferent->format("%h hours");
                    //calculation end here

                    view()->share('msg_flag',$msg_flag);
                    view()->share('msg_val',$msg_val);
                    view()->share('msg_type',$msg_type);

                    view()->share('taskObj',$taskObj );

                    // to display listing of bidders to task creator or unit admin of this task.
                    /*$flag = Task::isTaskCreator($task_id);
                    if(!$flag ){
                        $flag =Task::isUnitAdminOfTask($task_id);
                    }*/

                    $flag =Task::isUnitAdminOfTask($task_id); // right now considered only unit admin can assigned task to bidder if they
                    // want task creator can also assign task to bidder then remove this and uncomment above lines
                    $taskBidders = [];
                    //show bid to all users 05-06-2018 if only UnitAdminOfTask can see bid then remove comments
                    //if($flag){
                        $taskBidders = TaskBidder::join('users','task_bidders.user_id','=','users.id')
                                        ->select(['users.first_name','users.last_name','users.id as user_id','task_bidders.*'])
                                        ->where('task_id',$task_id)->get();
                    //}
                    view()->share('taskBidders',$taskBidders);
                    view()->share('isUnitAdminOfTask',$flag);
                    // end display listing of bidders

                    $availableFunds =Fund::getTaskDonatedFund($task_id);
                    $awardedFunds =Fund::getTaskAwardedFund($task_id);

                    view()->share('availableFunds',$availableFunds );
                    view()->share('awardedFunds',$awardedFunds );

                    $availableUnitFunds =Fund::getUnitDonatedFund($taskObj->unit_id);
                    $awardedUnitFunds =Fund::getUnitAwardedFund($taskObj->unit_id);

                    view()->share('availableUnitFunds',$availableUnitFunds );
                    view()->share('awardedUnitFunds',$awardedUnitFunds );

                    /*$site_activity = SiteActivity::where(function($query) use($taskObj){
                            return $query->where('unit_id',$taskObj->unit_id)
                                    ->orWhere('objective_id',$taskObj->objective_id)
                                    ->orWhere('task_id',$taskObj->id);
                    })->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));*/

                    $site_activity = SiteActivity::where('unit_id',$taskObj->unit_id)
                                    ->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));

                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$taskObj->unit_id);
                    $skillNames = JobSkill::getSKillWithComma($taskObj->skills);

                    // Forum Object coading
                    view()->share("unit_id", $taskObj->unit_id);
                    view()->share("section_id", 2);
                    view()->share("object_id",$taskObj->id);


                    $forumID =  Forum::checkTopic(array(
                        'unit_id' => $taskObj->unit_id,
                        'section_id' => 2,
                        'object_id' => $taskObj->id,
                    ));

                    if(!empty($forumID)){
                        view()->share('addComments', url('forum/post/'. $forumID->topic_id .'/'. $forumID->slug ) );
                    }

                    view()->share('skill_names',$skillNames);
                    return view('tasks.view');
                }
            }
        }
        return view('errors.404');
    }

    /**
     * function will delete the task.
     * @param Request $request
     * @return mixed
     */
    public function delete_task(Request $request)
    {
        // remove task related data from all table. like task_documents and task_actions etc with task deletion
        $task_id = $request->input('id');
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                if(count($taskObj) > 0){
                    $tasktempObj = $taskObj;

                    // delete task documents, task action and task
                    Task::deleteTask($task_id);

                    // add activity points for task deletion
                    ActivityPoint::create([
                        'user_id'=>Auth::user()->id,
                        'objective_id'=>$task_id,
                        'points'=>1,
                        'comments'=>'Task Deleted',
                        'type'=>'task'
                    ]);

                    // add site activity record for global statistics.
                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$tasktempObj->unit_id,
                        'objective_id'=>$tasktempObj->objective_id,
                        'task_id'=>$task_id,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .$user_name.'</a>
                        deleted task '.$tasktempObj->name
                    ]);

                    // mail send
                    /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                    if(!empty($alertObj) && $alertObj->task_management == 1) {
                        $toEmail = Auth::user()->email;
                        $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                        $subject = 'Task updated successfully. ';

                        \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                            $message->to($toEmail, $toName)->subject($subject);
                            $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                        });
                    }*/

                    // After deleted task send mail to unit creator
                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find(Auth::user()->id);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Task Deleted";

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use ($toEmail,
                        $toName,$subject,
                        $siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                    });
                }
                return \Response::json(['success'=>true]);
            }
        }
        return \Response::json(['success'=>false]);
    }

    /**
     * when user click on Submit for Approval button after edit task.
     * @param Request $request
     * @return mixed
     */
    public function submit_for_approval(Request $request){
        $task_id = $request->input('task_id');
        $task_id_encoded=$task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                $taskEditor = TaskEditor::where('task_id',$task_id)->where('user_id',Auth::user()->id)->get();
                if(!empty($taskObj) && count($taskEditor) > 0){
                    $otherEditor = TaskEditor::where('task_id',$task_id)->where('user_id','!=',Auth::user()->id)
                                    ->where('submit_for_approval','submitted')->where('first_user_to_submit','yes')->get();

                    $first_user_to_submit ="yes";
                    if(count($otherEditor) > 0)
                        $first_user_to_submit ="no";

                    TaskEditor::where('task_id',$task_id)->where('user_id',Auth::user()->id)->update(['submit_for_approval'=>'submitted',
                        'first_user_to_submit'=>$first_user_to_submit]);

                    $taskEditorObj =  TaskEditor::where('task_id',$task_id)->where('submit_for_approval','not_submitted')->count();
                    if($taskEditorObj == 0){
                        //$taskObj = Task::find($task_id);
                        if(!empty($taskObj)){
                            //$taskObj->update(['status'=>'awaiting_approval']);
                            //$taskObj->update(['status'=>'approval']);
                            //update task status if only on editor of an task
                            $taskObj->update(['status'=>'open_for_bidding']);



                            // add activity point for submit for approval task.
                            ActivityPoint::create([
                                'user_id'=>Auth::user()->id,
                                'task_id'=>$task_id,
                                'points'=>2,
                                'comments'=>'Task Approved',
                                'type'=>'task'
                            ]);

                            $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                            $user_id = $userIDHashID->encode(Auth::user()->id);

                            $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                            if(!empty(Auth::user()->username))
                                $user_name =Auth::user()->username;

                            SiteActivity::create([
                                'user_id'=>Auth::user()->id,
                                'unit_id'=>$taskObj->unit_id,
                                'objective_id'=>$taskObj->objective_id,
                                'task_id'=>$task_id,
                                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                    .$user_name
                                    .'</a> submitted task approval <a href="'.url('tasks/'.$task_id_encoded.'/'.$taskObj->slug).'">'
                                    .$taskObj->name.'</a>'
                            ]);

                            // mail send
                            /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                            if(!empty($alertObj) && $alertObj->task_management == 1) {
                                $toEmail = Auth::user()->email;
                                $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                                $subject = 'Task updated successfully. ';

                                \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                                    $message->to($toEmail, $toName)->subject($subject);
                                    $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                                });
                            }*/

                            // After Created Unit send mail to site admin
                            $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                            $unitCreator = User::find(Auth::user()->id);

                            $toEmail = $unitCreator->email;
                            $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                            $subject="Task approval submitted by".Auth::user()->first_name.' '.Auth::user()->last_name;

                            \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use
                            ($toEmail,$toName,
                                $subject,$siteAdminemails)
                            {
                                $message->to($toEmail,$toName)->subject($subject);
                                if(!empty($siteAdminemails))
                                    $message->bcc($siteAdminemails,"Admin")->subject($subject);

                                $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                            });

                            return \Response::json(['success'=>true,'status'=>'awaiting_approval']);
                        }
                    }


                    return \Response::json(['success'=>true,'status'=>'']);
                }
            }
        }
        return \Response::json(['success'=>false]);
    }

    public function bid_now(Request $request,$task_id){
        if(!empty($task_id)){
            $task_id_encoded = $task_id;
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);

            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                if($taskObj->status != "open_for_bidding"){
                    return \Redirect::to('/tasks');
                }
                if(!empty($taskObj)){
                    if($request->isMethod('post')){

                        \Validator::extend('isCurrency', function($field,$value,$parameters){
                            //return true if field value is foo
                            return Helpers::isCurrency($value);
                        });

                        $validator = \Validator::make($request->all(), [
                            'amount' => 'required|isCurrency',
                            'comment' => 'required'
                        ],[
                            'amount.required' => 'This field is required.',
                            'amount.is_currency'=>'Please enter digits only'
                        ]);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInput();

                        $taskBidders = TaskBidder::where('task_id',$task_id)->where('first_to_bid','yes')->count();
                        $first_to_bid="no";
                        if($taskBidders == 0)
                           $first_to_bid ="yes";

                        $chargeType = $request->input('charge_type');
                        $chargeAmountType ="points";
                        if($chargeType == "on")
                            $chargeAmountType ="amount";

                        TaskBidder::create([
                           'task_id'=>$task_id,
                            'user_id'=>Auth::user()->id,
                            'amount'=>$request->input('amount'),
                            'comment'=>$request->input('comment'),
                            'first_to_bid'=>$first_to_bid,
                            'charge_type'=>$chargeAmountType
                        ]);

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                        $user_id = $userIDHashID->encode(Auth::user()->id);

                        $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                        if(!empty(Auth::user()->username))
                            $user_name =Auth::user()->username;


                        // send email and notification
                        $unitObj = Unit::find($taskObj->unit_id);
                        $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));

                        $content = 'You have bid Task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'.$taskObj->name.'</a>
                         at Unit <a href="'.url('units/'.$unitIDHashID->encode($unitObj->id).'/'.$unitObj->slug).'">'.$unitObj->name.'</a>';

                        $email_subject = 'You have bid Task '.$taskObj->name.' at Unit '.$unitObj->name;

                        \App\User::SendEmailAndOnSiteAlert($content,$email_subject,[Auth::user()],$onlyemail=false,'task_management');

                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'unit_id'=>$taskObj->unit_id,
                            'objective_id'=>$taskObj->objective_id,
                            'task_id'=>$task_id,
                            'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$user_name
                                .'</a> bid <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                                .$taskObj->name.'</a>'
                        ]);

                        $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                        $unitCreator = User::find(Auth::user()->id);

                        // mail send
                        /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                        if(!empty($alertObj) && $alertObj->task_management == 1) {
                            $toEmail = Auth::user()->email;
                            $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                            $subject = 'Task updated successfully. ';

                            \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                                $message->to($toEmail, $toName)->subject($subject);
                                $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                            });
                        }*/


                        $toEmail = $unitCreator->email;
                        $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                        $subject="Task bid by".Auth::user()->first_name.' '.Auth::user()->last_name;

                        \Mail::send('emails.registration', ['userObj'=> $unitCreator, 'report_concern' => false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                        {
                            $message->to($toEmail,$toName)->subject($subject);
                            if(!empty($siteAdminemails))
                                $message->bcc($siteAdminemails,"Admin")->subject($subject);

                            $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                        });

                        $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_BID')['text']);
                        return redirect('tasks');
                    }
                    else{
                        $taskBidder = TaskBidder::where('task_id',$task_id)->where('user_id',Auth::user()->id)->first();
                        $daysRemainingTobid = TaskBidder::getCountDown($task_id);

                        $site_activity = SiteActivity::where('unit_id',$taskObj->unit_id)
                            ->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));

                        $quality_of_work = TaskRatings::where('user_id',Auth::user()->id)->sum('quality_of_work');
                        $timeliness = TaskRatings::where('user_id',Auth::user()->id)->sum('timeliness');

                        $total_ratings =TaskRatings::where('user_id',Auth::user()->id)->count();

                        if($quality_of_work > 0)
                            $quality_of_work =$quality_of_work/$total_ratings;
                        if($timeliness > 0)
                            $timeliness =$timeliness/$total_ratings;

                        view()->share('timeliness',$timeliness);
                        view()->share('quality_of_work',$quality_of_work);

                        view()->share('site_activity',$site_activity);
                        view()->share('unit_activity_id',$taskObj->unit_id);

                        view()->share('daysRemainingTobid',$daysRemainingTobid);
                        view()->share('taskBidder',$taskBidder );
                        view()->share('taskObj',$taskObj);
                        return view('tasks.bid_now');
                    }
                }
            }
        }
        return view('errors.404');
    }

    public function assign_task(Request $request){
        $user_id = $request->input('uid');
        $task_id = $request->input('tid');
        $task_id_encoded =$task_id;
        if(!empty($task_id) && !empty($user_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);

            $userIDHashID = new Hashids('user id hash',10,\Config::get('app.encode_chars'));
            $user_id = $userIDHashID->decode($user_id);

            if(!empty($task_id) && !empty($user_id)){
                $task_id = $task_id[0];
                $user_id = $user_id[0];
                $taskObj = Task::find($task_id);
                $userObj = User::find($user_id);
                if(!empty($taskObj) && !empty($userObj)){
                    $taskBidderObj = TaskBidder::where('task_id',$task_id)->where('user_id',$user_id)->first();
                    if(!empty($taskBidderObj)){
                        $taskBidderObj->update(['status'=>'offer_sent']);
                        Task::where('id','=',$task_id)->update(['status'=>'assigned','assign_to'=>$user_id]);

                        // add activity point for submit for approval task.
                        ActivityPoint::create([
                            'user_id'=>Auth::user()->id,
                            'task_id'=>$task_id,
                            'points'=>3,
                            'comments'=>'Bid Selection',
                            'type'=>'task'
                        ]);

                        $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                        $unitCreator = User::find($user_id);

                        $toEmail = $unitCreator->email;
                        $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                        $subject="Task assigned to ".$unitCreator->first_name.' '.$unitCreator->last_name;

                        // send email and notification
                        $unitObj = Unit::find($taskObj->unit_id);
                        $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));

                        $content = 'Task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'.$taskObj->name.'</a> at Unit' .
                            '<a href="'.url('units/'.$unitIDHashID->encode($unitObj->id).'/'.$unitObj->slug).'">'.$unitObj->name.'</a> has
                             been assigned to you';

                        $email_subject = 'Task '.$taskObj->name.' has been assigned to you';

                        \App\User::SendEmailAndOnSiteAlert($content,$email_subject,[$unitCreator],$onlyemail=false,'task_management');

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                        $loggedin_user_id = $userIDHashID->encode(Auth::user()->id);
                        $user_id = $userIDHashID->encode($user_id);

                        $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                        if(!empty(Auth::user()->username))
                            $user_name =Auth::user()->username;

                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'unit_id'=>$taskObj->unit_id,
                            'objective_id'=>$taskObj->objective_id,
                            'task_id'=>$task_id,
                            'comment'=>'<a href="'.url('userprofiles/'.$loggedin_user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$user_name
                                .'</a> assigned <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                                .$taskObj->name.'</a> to <a href="'.url('userprofiles/'.$user_id .'/'.strtolower($userObj->first_name.'_'.$userObj->last_name)).'">'
                                .$userObj->first_name.' '.$userObj->last_name
                                .'</a>'
                        ]);

                        // mail send
                        /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                        if(!empty($alertObj) && $alertObj->task_management == 1) {
                            $toEmail = Auth::user()->email;
                            $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                            $subject = 'Task updated successfully. ';

                            \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                                $message->to($toEmail, $toName)->subject($subject);
                                $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                            });
                        }*/


                    }
                    return \Response::json(['success'=>true]);
                }
            }
        }
        return \Response::json(['success'=>false]);
    }

    public function check_assigned_task(){
        $taskBidderObj = TaskBidder::join('tasks','task_bidders.task_id','=','tasks.id')
                        ->whereIn('task_bidders.status',['offer_sent','re_assigned'])
                        ->where('task_bidders.user_id',Auth::user()->id)
                        ->select(['tasks.name','tasks.slug','task_bidders.*'])
                        ->first();

        if(!empty($taskBidderObj)){

            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->encode($taskBidderObj->task_id);

           /* $html = "Your bid has been selected and task (<a href='".url('tasks/'.$task_id.'/'.$taskBidderObj->slug)."'>".$taskBidderObj->name."</a>) " .
                "has been assigned to you.";*/

            if($taskBidderObj->status == "offer_sent"){
                $html = '<div class="alert alert-warning" style="padding:15px;margin-bottom:15px">'.
                          '<a href="#" class="close" data-dismiss="alert" aria-label="close" style="display:none;">&times;</a>'.
                          '<img src="'.url('assets/images/warning-icon.png').'"> <strong>'.$this->user_messages->getMessage('TASK_HAS_BEEN_ASSIGNED')['text'].'</strong> '.$this->user_messages->getMessage('TASK_HAS_BEEN_ASSIGNED')['continue_text'].'(<b>'.$taskBidderObj->name.'</b>) ' .
                          $this->user_messages->getMessage('TASK_HAS_BEEN_ASSIGNED')['continue_text_too'].
                        '<div class="pull-right">' .
                            '<a class="btn btn-success btn-xs offer" data-task_id="'.$task_id.'" style="margin-right:5px;">Accept</a>' .
                            '<a class="btn btn-danger btn-xs offer" data-task_id="'.$task_id.'">Reject</a></div>'.
                        '</div>';
            }
            else{
                $html = '<div class="alert alert-warning" style="padding:15px;margin-bottom:0px;margin-top:10px;">'.
                    '<img src="'.url('assets/images/warning-icon.png').'"> <strong>'.$this->user_messages->getMessage('TASK_ASSIGNED_BID_SELECTED')['text'].'</strong> '.$this->user_messages->getMessage('TASK_ASSIGNED_BID_SELECTED')['continue_text'].' (<b>'.$taskBidderObj->name.'</b>) '.$this->user_messages->getMessage('TASK_ASSIGNED_BID_SELECTED')['continue_text_too'].'' .
                    '<a href="#" class="close" data-dismiss="alert" aria-label="close" style="display:none;">&times;</a>'.
                    '<div class="pull-right">' .
                        '<a class="btn btn-success btn-xs re_assigned offer" data-task_id="'.$task_id.'" style="margin-right:5px;">Ok</a>' .
                        '<a class="btn btn-danger btn-xs re_assigned offer" data-task_id="'.$task_id.'">Cancel</a></div>'.
                    '</div>';
            }

            return \Response::json(['success'=>true,'html'=>$html,'task_id'=>$task_id]);

        }
        return \Response::json(['success'=>false]);
    }

    /**
     * function is used to accept the offer sent by unit admin
     * @param Request $request
     * @return mixed
     */
    public function accept_offer(Request $request){
        $task_id = $request->input('task_id');
        $task_id_encoded=$task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                if(!empty($taskObj)){
                    $taskObj->update(['status'=>'in_progress']);
                    $taskBidder = TaskBidder::where('task_id',$task_id)->where('user_id',Auth::user()->id)->first();
                    if(!empty($taskBidder)){
                        $taskBidder->update(['status'=>'offer_accepted']);
                    }
                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$taskObj->unit_id,
                        'objective_id'=>$taskObj->objective_id,
                        'task_id'=>$task_id,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .$user_name
                            .'</a> accept offer of task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                            .$taskObj->name.'</a>'
                    ]);


                    // mail send
                    /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                    if(!empty($alertObj) && $alertObj->task_management == 1) {
                        $toEmail = Auth::user()->email;
                        $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                        $subject = 'Task updated successfully. ';

                        \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                            $message->to($toEmail, $toName)->subject($subject);
                            $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                        });
                    }*/


                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find(Auth::user()->id);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Task accepted by ".$unitCreator->first_name.' '.$unitCreator->last_name;

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator, 'report_concern' => false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                    });
                    return \Response::json(['success'=>true]);
                }
            }
        }
        return \Response::json(['success'=>false]);
    }

    /**
     * Function is used to reject the offer sent by unit admin.
     * @param Request $request
     * @return mixed
     */
    public function reject_offer(Request $request){
        $task_id = $request->input('task_id');
        $task_id_encoded =$task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                if(!empty($taskObj)){
                    $taskObj->update(['assign_to'=>null,'status'=>'awaiting_assignment']);
                    $taskBidder = TaskBidder::where('task_id',$task_id)->where('user_id',Auth::user()->id)->first();
                    if(!empty($taskBidder)){
                        $taskBidder->update(['status'=>'offer_rejected']);
                    }
                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$taskObj->unit_id,
                        'objective_id'=>$taskObj->objective_id,
                        'task_id'=>$task_id,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .$user_name
                            .'</a> reject offer of task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                            .$taskObj->name.'</a>'
                    ]);

                    // mail send
                   /* $alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                    if(!empty($alertObj) && $alertObj->task_management == 1) {
                        $toEmail = Auth::user()->email;
                        $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                        $subject = 'Task updated successfully. ';

                        \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                            $message->to($toEmail, $toName)->subject($subject);
                            $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                        });
                    }*/

                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find(Auth::user()->id);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Task rejected by ".$unitCreator->first_name.' '.$unitCreator->last_name;

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator, 'report_concern' => false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                    });

                    return \Response::json(['success'=>true]);
                }
            }
        }
        return \Response::json(['success'=>false]);
    }

    /**
     * function is used to get bidding information.
     * @param Request $request
     * @return mixed
     */
    public function get_biding_details(Request $request){
        $id = $request->input('id');
        if(!empty($id)){
            $taskBidder = TaskBidder::find($id);
            if(!empty($taskBidder)){
                $html = '<div class="row "><div class="col-sm-12 form-group">' .
                        '<label class="control-label" style="width:100%;font-weight:bold">'.ucwords($taskBidder->charge_type).'</label><span>'.$taskBidder->amount.
                        '</div><div class="col-sm-12 form-group">' .
                        '<label class="control-label" style="width:100%;font-weight:bold">Charge Type</label><span>'.$taskBidder->charge_type.
                        '</div><div class="col-sm-12 form-group">' .
                        '<label class="control-label" style="width:100%;font-weight:bold">Comment</label><span>'.$taskBidder->comment.
                        '</div></div>';
                return \Response::json(['success'=>true,'html'=>$html]);
            }
        }
        return \Response::json(['success'=>false]);
    }

    public function complete_task(Request $request,$task_id){
        $task_id_encoded=$task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskCompleteObj  = TaskComplete::join('users','task_complete.user_id','=','users.id')
                    ->where('task_id',$task_id)
                    ->select(['task_complete.*','users.first_name','users.last_name'])
                    ->orderBy('id','asc')
                    ->get();

                if(Auth::user()->role == "superadmin")
                    $taskObj = Task::where('id','=',$task_id)->first();
                else
                    $taskObj = Task::where('id','=',$task_id)->where('assign_to',Auth::user()->id)->where('status','in_progress')->first();

                $taskEditors = RewardAssignment::where('task_id',$task_id)->get();
                $rewardAssigned=true;

                if(empty($taskEditors) || count($taskEditors) == 0){
                    $taskEditors = TaskEditor::where('task_id',$task_id)->where('user_id','!=',$taskObj->assign_to)->get();
                    $rewardAssigned=false;
                }

                if(!empty($taskObj)){
                    if($request->isMethod('post')){

                        $validator = \Validator::make($request->all(), [
                            'comment' => 'required'
                        ]);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInput();

                        // upload documents of task.
                        $task_documents=[];
                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                        $user_id_encoded = $userIDHashID->encode(Auth::user()->id);
                        if($request->hasFile('attachments')) {
                            $files = $request->file('attachments');
                            if(count($files) > 0){
                                $totalAvailableDocs = TaskComplete::where('task_id',$task_id)->get();
                                $totalAvailableDocs= count($totalAvailableDocs) + 1;
                                foreach($files as $index=>$file){
                                    if(!empty($file)){

                                        $rules = ['attachments' => 'required', 'extension' => 'required|in:doc,docx,pdf,txt,jpg,png,ppt,pptx,jpeg,doc,xls,xlsx'];
                                        $fileData = ['attachments' => $file, 'extension' => strtolower($file->getClientOriginalExtension())];
                                        // doing the validation, passing post data, rules and the messages
                                        $validator = \Validator::make($fileData, $rules);
                                        if (!$validator->fails()) {
                                            if ($file->isValid()) {
                                                $destinationPath = base_path().'/uploads/tasks/'.$task_id_encoded; // upload path
                                                if(!\File::exists($destinationPath)){
                                                    $oldumask = umask(0);
                                                    @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                                                    umask($oldumask);
                                                }

                                                $destinationPath =$destinationPath.'/completed_docs';
                                                if(!\File::exists($destinationPath)){
                                                    $oldumask = umask(0);
                                                    @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                                                    umask($oldumask);
                                                }

                                                $destinationPath =$destinationPath.'/'.$user_id_encoded;
                                                if(!\File::exists($destinationPath)){
                                                    $oldumask = umask(0);
                                                    @mkdir($destinationPath, 0775); // or even 01777 so you get the sticky bit set
                                                    umask($oldumask);
                                                }

                                                $file_name =$file->getClientOriginalName();
                                                $extension = $file->getClientOriginalExtension(); // getting image extension
                                                $fileName = $task_id_encoded.'_'.$totalAvailableDocs . '.' . $extension; // renaming image
                                                $file->move($destinationPath, $fileName); // uploading file to given path

                                                // insert record into task_documents table
                                                $task_documents[]=['file_name'=>$file_name,
                                                    'file_path'=>'uploads/tasks/'.$task_id_encoded.'/completed_docs/'.$user_id_encoded.'/'.$fileName];

                                                $totalAvailableDocs++;
                                            }
                                        }
                                    }

                                }
                            }
                        }

                        TaskComplete::create([
                            'user_id'=>Auth::user()->id,
                            'task_id'=>$task_id,
                            'attachments'=>json_encode($task_documents),
                            'comments'=>$request->input('comment')
                        ]);

                        $taskBidder = TaskBidder::where('task_id',$task_id)->where('user_id',Auth::user()->id)->where('task_bidders.status',
                            'offer_accepted')->first();
                        if(!empty($taskBidder))
                            $taskBidder->update(['status'=>'task_completed']);

                        Task::find($task_id)->update(['status'=>'completion_evaluation']);

                        // add activity point for submit for approval task.
                        ActivityPoint::create([
                            'user_id'=>Auth::user()->id,
                            'task_id'=>$task_id,
                            'points'=>50,
                            'comments'=>'Task Completed',
                            'type'=>'task'
                        ]);

                        $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                        if(!empty(Auth::user()->username))
                            $user_name =Auth::user()->username;

                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'unit_id'=>$taskObj->unit_id,
                            'objective_id'=>$taskObj->objective_id,
                            'task_id'=>$task_id,
                            'comment'=>'<a href="'.url('userprofiles/'.$user_id_encoded.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$user_name
                                .'</a> complete task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                                .$taskObj->name.'</a>'
                        ]);

                        // mail send
                        /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                        if(!empty($alertObj) && $alertObj->task_management == 1) {
                            $toEmail = Auth::user()->email;
                            $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                            $subject = 'Task updated successfully. ';

                            \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                                $message->to($toEmail, $toName)->subject($subject);
                                $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                            });
                        }*/


                        $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                        $unitCreator = User::find(Auth::user()->id);

                        $toEmail = $unitCreator->email;
                        $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                        $subject="Task completed by ".$unitCreator->first_name.' '.$unitCreator->last_name;

                        \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use
                        ($toEmail,
                            $toName,
                            $subject,$siteAdminemails)
                        {
                            $message->to($toEmail,$toName)->subject($subject);
                            if(!empty($siteAdminemails))
                                $message->bcc($siteAdminemails,"Admin")->subject($subject);

                            $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                        });

                        $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_COMPLETED')['text']);
                        return redirect('tasks');
                    }
                    else{
                        $taskObj->unit = Unit::getUnitWithCategories($taskObj->unit_id);
                        $availableUnitFunds =Fund::getUnitDonatedFund($taskObj->unit_id);
                        $awardedUnitFunds =Fund::getUnitAwardedFund($taskObj->unit_id);

                        view()->share('availableUnitFunds',$availableUnitFunds );
                        view()->share('awardedUnitFunds',$awardedUnitFunds );

                        $site_activity = SiteActivity::where('unit_id',$taskObj->unit_id)
                            ->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));

                        view()->share('site_activity',$site_activity);
                        view()->share('unit_activity_id',$taskObj->unit_id);

                        $skillNames = JobSkill::getSKillWithComma($taskObj->skills);
                        view()->share('skill_names',$skillNames);
                        view()->share('taskObj',$taskObj);
                        view()->share('taskCompleteObj',$taskCompleteObj);
                        view()->share('taskEditors',$taskEditors );
                        view()->share('rewardAssigned',$rewardAssigned);
                        return view('tasks.partials.complete_task');
                    }
                }
            }
        }
        return view('errors.404');
    }

    public function re_assign(Request $request,$task_id){
        $task_id_encoded = $task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                if(!empty($taskObj)){
                    Task::find($task_id)->update(['status'=>'in_progress']);
                    $taskBidderObj = TaskBidder::where('task_id',$task_id)->where('user_id',$taskObj->assign_to)->first();
                    if(!empty($taskBidderObj))
                        $taskBidderObj->update(['status'=>'re_assigned']);

                    $validator = \Validator::make($request->all(), [
                        'comment' => 'required'
                    ]);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInput();

                    TaskComplete::create([
                        'user_id'=>Auth::user()->id,
                        'task_id'=>$task_id,
                        'attachments'=>null,
                        'comments'=>$request->input('comment')
                    ]);

                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                    $user_id_encoded = $userIDHashID->encode(Auth::user()->id);

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$taskObj->unit_id,
                        'objective_id'=>$taskObj->objective_id,
                        'task_id'=>$taskObj->id,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id_encoded.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .$user_name
                            .'</a> re-assigned task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                            .$taskObj->name.'</a>'
                    ]);

                    /*$content = '<a href="'.url('userprofiles/'.$user_id_encoded.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                        .$user_name
                        .'</a> re-assigned task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                        .$taskObj->name.'</a>';

                    $email_subject = 'Task '.$taskObj->name.' has been re-assigned to you';

                    $taskAssigneeObj = User::find($taskObj->assign_to);
                    User::SendEmailAndOnSiteAlert($content,$email_subject,[$taskAssigneeObj],$onlyemail=false);*/

                    // mail send
                    /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                    if(!empty($alertObj) && $alertObj->task_management == 1) {
                        $toEmail = Auth::user()->email;
                        $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                        $subject = 'Task updated successfully. ';

                        \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                            $message->to($toEmail, $toName)->subject($subject);
                            $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                        });
                    }*/


                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find($taskObj->assign_to);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Task re-assigned to ".$unitCreator->first_name.' '.$unitCreator->last_name;

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator, 'report_concern' => false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                    });
                    $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_ASSIGNED')['text']);
                    return redirect('tasks');
                }
            }
        }
        $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_WHERE_NOT_FOUND')['text']);
        $request->session()->flash('msg_type', "danger");

        return redirect('tasks');
    }

    public function mark_as_complete(Request $request,$task_id){
        $task_id_encoded=$task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskObj = Task::find($task_id);
                if(!empty($taskObj)){
                    $taskEditors = RewardAssignment::where('task_id',$task_id)->get();

                    if(empty($taskEditors) || count($taskEditors) == 0)
                        $taskEditors = TaskEditor::where('task_id',$task_id)->where('user_id','!=',$taskObj->assign_to)->get();

                    // validate percentage split.
                    $percentageError = [];
                    $totalPercentage=0;
                    if(!empty($taskEditors) && count($taskEditors) > 0){
                        $allUsersRewardPercentage = $request->input('amount_percentage');
                        if(!empty($allUsersRewardPercentage)){
                            foreach($allUsersRewardPercentage  as $u_id=>$percentage){
                                $editorExist = TaskEditor::where('task_id',$task_id)->where('user_id',$u_id)->get();
                                if($taskObj->user_id != $u_id && (empty($editorExist) || count($editorExist) == 0))
                                    $percentageError['amount_percentage['.$u_id.']']="Please enter percentage";
                                else
                                    $totalPercentage+=intval($percentage);
                            }
                        }

                        if(!empty($percentageError))
                            return redirect()->back()->withErrors($percentageError)->withInput();

                        if($totalPercentage < 100 || $totalPercentage > 100)
                            return redirect()->back()->withErrors(['split_error'=>"Please split 100% among all users."])->withInput();
                    }

                    // insert task reward assignment into table. to use where transaction take place. to give % of amount to user.
                    if(!empty($taskEditors) && count($taskEditors) > 0 ){
                        $allUsersRewardPercentage = $request->input('amount_percentage');
                        if(!empty($allUsersRewardPercentage)){
                            foreach($allUsersRewardPercentage  as $u_id=>$percentage){
                                $rewardAssignedObj = RewardAssignment::where('task_id',$task_id)->where('user_id',$u_id)->first();
                                if(!empty($rewardAssignedObj) && count($rewardAssignedObj) > 0){
                                    $rewardAssignedObj->update([
                                        'reward_percentage'=>$percentage
                                    ]);
                                }
                                else{
                                    RewardAssignment::create([
                                        'task_id'=>$task_id,
                                        'user_id'=>$u_id,
                                        'reward_percentage'=>$percentage
                                    ]);
                                }
                            }
                        }
                    }

                    // Transfer rewards to all users
                    \App\User::transferRewards($task_id);

                    Task::find($task_id)->update(['status'=>'completed']);

                    $quality_of_work = $request->input('quality_of_work');
                    if(empty($quality_of_work))
                        $quality_of_work=null;

                    $timeliness = $request->input('timeliness');
                    if(empty($timeliness))
                        $timeliness=null;

                    TaskRatings::create([
                        'user_id'=>$taskObj->assign_to,
                        'task_id'=>$task_id,
                        'quality_of_work'=>$quality_of_work,
                        'timeliness'=>$timeliness
                    ]);

                    $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                    $user_id_encoded = $userIDHashID->encode(Auth::user()->id);

                    $taskBidderObj = TaskBidder::where('task_id',$taskObj->id)->where('user_id',$taskObj->assign_to)->where('charge_type','points')->first();
                    if(!empty($taskBidderObj) && count($taskBidderObj) > 0)
                    {
                        ActivityPoint::create([
                            'user_id'=>$taskObj->assign_to,
                            'task_id'=>$taskObj->id,
                            'points'=>$taskBidderObj->amount,
                            'comments'=>'Task Completed Points',
                            'type'=>'task'
                        ]);
                    }

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    $userObj = User::find($taskObj->assign_to);
                    // send email and notification
                    $unitObj = Unit::find($taskObj->unit_id);
                    $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));

                    $content = 'Task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'.$taskObj->name.'</a>
                         at Unit <a href="'.url('units/'.$unitIDHashID->encode($unitObj->id).'/'.$unitObj->slug).'">'.$unitObj->name.'</a>
                          has been mark as complete';

                    $email_subject = 'Task '.$taskObj->name.' at Unit '.$unitObj->name.' has been mark as complete';

                    \App\User::SendEmailAndOnSiteAlert($content,$email_subject,[$userObj],$onlyemail=false,'task_management');

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$taskObj->unit_id,
                        'objective_id'=>$taskObj->objective_id,
                        'task_id'=>$taskObj->id,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id_encoded.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                            .$user_name
                            .'</a> approved completed task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                            .$taskObj->name.'</a>'
                    ]);


                    // mail send
                    /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                    if(!empty($alertObj) && $alertObj->task_management == 1) {
                        $toEmail = Auth::user()->email;
                        $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                        $subject = 'Task updated successfully. ';

                        \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                            $message->to($toEmail, $toName)->subject($subject);
                            $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                        });
                    }*/

                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find($taskObj->assign_to);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Task completed by supperadmin ";

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use ($toEmail,
                        $toName,$subject,
                        $siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                    });

                    $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_COMPLETED')['text']);
                    return redirect('tasks');
                }
            }
        }
        return redirect('errors.404');
    }

    public function cancel_task(Request $request,$task_id){
        $task_id_encoded=$task_id;
        if(!empty($task_id)){
            $taskIDHashID = new Hashids('task id hash',10,\Config::get('app.encode_chars'));
            $task_id = $taskIDHashID->decode($task_id);
            if(!empty($task_id)){
                $task_id = $task_id[0];
                $taskCancelObj  = TaskCancel::join('users','task_cancel.user_id','=','users.id')
                    ->where('task_id',$task_id)
                    ->select(['task_cancel.*','users.first_name','users.last_name'])
                    ->orderBy('id','asc')
                    ->get();
                if(Auth::user()->role == "superadmin")
                    $taskObj = Task::where('id','=',$task_id)->first();
                else
                    $taskObj = Task::where('id','=',$task_id)->where('assign_to',Auth::user()->id)->where('status','in_progress')->first();
                if(!empty($taskObj)){
                    if($request->isMethod('post')){
                        $validator = \Validator::make($request->all(), [
                            'comment' => 'required'
                        ]);

                        if ($validator->fails()){
                            $request->session()->flash('msg_val', 'PLEASE_FILL_PROPER_DETAILS');
                            return redirect()->back()->withErrors($validator)->withInput();
                        }

                        TaskCancel::create([
                            'user_id'=>Auth::user()->id,
                            'task_id'=>$task_id,
                            'comments'=>$request->input('comment')
                        ]);

                        $taskBidder = TaskBidder::where('task_id',$task_id)->where('user_id',Auth::user()->id)->where('task_bidders.status',
                            'offer_accepted')->first();
                        if(!empty($taskBidder))
                            $taskBidder->update(['status'=>'task_canceled']);

                        Task::find($task_id)->update(['status'=>'cancelled']);

                        // add activity point for submit for cancel task.
                       /* ActivityPoint::create([
                            'user_id'=>Auth::user()->id,
                            'task_id'=>$task_id,
                            'points'=>50,
                            'comments'=>'Task Completed',
                            'type'=>'task'
                        ]);*/

                        $userIDHashID= new Hashids('user id hash',10,\Config::get('app.encode_chars'));
                        $user_id_encoded = $userIDHashID->encode(Auth::user()->id);

                        $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                        if(!empty(Auth::user()->username))
                            $user_name =Auth::user()->username;

                        SiteActivity::create([
                            'user_id'=>Auth::user()->id,
                            'unit_id'=>$taskObj->unit_id,
                            'objective_id'=>$taskObj->objective_id,
                            'task_id'=>$task_id,
                            'comment'=>'<a href="'.url('userprofiles/'.$user_id_encoded.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                                .$user_name
                                .'</a> cancelled task <a href="'.url('tasks/'.$task_id_encoded .'/'.$taskObj->slug).'">'
                                .$taskObj->name.'</a>'
                        ]);

                        // mail send
                        /*$alertObj = Alerts::where('user_id',Auth::user()->id)->first();
                        if(!empty($alertObj) && $alertObj->task_management == 1) {
                            $toEmail = Auth::user()->email;
                            $toName= Auth::user()->first_name.' '.Auth::user()->last_name;
                            $subject = 'Task updated successfully. ';

                            \Mail::send('emails.task_creation', ['userObj' => Auth::user(), 'taskObj' => Task::find($task_id)], function($message) use($toEmail,$toName,$subject) {
                                $message->to($toEmail, $toName)->subject($subject);
                                $message->from(\Config::get("app.support_email"), \Config::get("app.site_name"));
                            });
                        }*/

                        $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                        $unitCreator = User::find(Auth::user()->id);

                        $toEmail = $unitCreator->email;
                        $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                        $subject="Task cancelled by ".$toName;

                        \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use
                        ($toEmail,$toName,
                            $subject,$siteAdminemails)
                        {
                            $message->to($toEmail,$toName)->subject($subject);
                            if(!empty($siteAdminemails))
                                $message->bcc($siteAdminemails,"Admin")->subject($subject);

                            $message->from(\Config::get("app.notification_email"), \Config::get("app.site_name"));
                        });

                        $request->session()->flash('msg_val', $this->user_messages->getMessage('TASK_CANCELLED')['text']);
                        return redirect('tasks');
                    }
                    else{
                        view()->share('taskObj',$taskObj);
                        view()->share('taskCancelObj',$taskCancelObj);
                        return view('tasks.partials.cancel_task');
                    }
                }
            }
        }
    }

    public function pluck(Request $request)
    {
        $unit_id = $request->segment(2);
        $unit_id_encoded = $unit_id;
        if(!empty($unit_id)){
            $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id= $unit_id[0];
                $taskObj = Task::where('unit_id',$unit_id)->orderBy('tasks.id','desc')->paginate(\Config::get('app.page_limit'));
                $taskObj->unit = Unit::getUnitWithCategories($unit_id);
                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(\Config::get('app.site_activity_page_limit'));
                view()->share('taskObj',$taskObj);
                view()->share('site_activity',$site_activity);
                view()->share('unit_activity_id',$unit_id);
                view()->share('unit_id_encoded',$unit_id_encoded);
                return view('tasks.partials.list');
            }
        }
        return view('errors.404');
    }

    public function get_tasks_paginate(Request $request){
        $from_page = $request->input('from_page');
        $objective_id = $request->input('objective_id');
        $unit_id = $request->input('unit_id');
        $page_limit = \Config::get('app.page_limit');
        $taskObj = Task::orderBy('id', 'desc');
        if(!empty($unit_id )) {
            $unitIDHashID= new Hashids('unit id hash',10,\Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)) {
                $unit_id = $unit_id[0];
                $taskObj = $taskObj->where('unit_id', $unit_id);
            }
        }
        if(!empty($objective_id)) {
            $objectiveIDHashID= new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
            $objective_id = $objectiveIDHashID->decode($objective_id);
            if(!empty($objective_id)) {
                $objective_id = $objective_id[0];
                $taskObj = $taskObj->where('objective_id', $objective_id);
            }
        }
        $taskObj = $taskObj->paginate($page_limit);
        view()->share('tasks',$taskObj);
        view()->share('from_page',$from_page);
        view()->share('unit_id',$unit_id);
        view()->share('objective_id',$objective_id);
        $html = view('tasks.partials.more_tasks')->render();
        return \Response::json(['success'=>true,'html'=>$html]);

    }

    public  function search_by_skills(Request $request){
        $terms = trim($request->input('q'));
        $page = $request->input('page');
        if(!empty($terms)){
            if($page == 0)
                $obj = JobSkill::where('skill_name','like',$terms.'%')->get();
            else {
                $offset = ($page - 1) * 10;
                $obj = JobSkill::where('skill_name','like',$terms.'%')->skip($offset)->take(10)->get();
            }

            $names = [];
            if(!empty($obj) && count($obj) > 0){
                foreach($obj as $job_skill){
                    $names[]=['id'=>$job_skill->id,'text'=>$job_skill->skill_name];
                }

            }
            return \Response::json(['items'=>$names,'total_counts'=> JobSkill::where('skill_name','like',$terms.'%')->count()]);
        }
        return \Response::json([]);
    }

    public  function search_by_status(Request $request){
        $terms = trim($request->input('q'));
        $page = $request->input('page');
        $task_status_arr = SiteConfigs::task_status();
        $result = array_filter($task_status_arr, function ($item) use ($terms) {
            if (stripos($item, $terms) !== false) {
                return true;
            }
            return false;
        });
        if(!empty($result)){
            foreach($result as $index=>$status_name){
                $names[]=['id'=>$index,'text'=>$status_name];
            }

            return \Response::json(['items'=>$names,'total_counts'=> count($result)]);
        }

        return \Response::json(['items'=>[],'total_counts'=> 0]);
    }


    public function search_tasks(Request $request){
        $task_skill_search = $request->input('task_skill_search');
        $task_status_search = $request->input('task_status_search');

        /*$units = Unit::orderBy('id','desc')->paginate(\Config::get('app.page_limit'));
        view()->share('units',$units );*/

        $where = '';
        \DB::enableQueryLog();

        $taskObj = \DB::table('tasks');

        if(trim($task_skill_search) != ""){
            $where.='FIND_IN_SET('.$task_skill_search.',skills)';
        }
        if(trim($task_status_search) != ""){
            if(!empty($where))
                $where.=' AND status = "'.$task_status_search.'"';
            else
                $where.=' status = "'.$task_status_search.'"';
        }


        if(empty($where))
            $taskObj = [];
        else
            $taskObj = $taskObj->whereRaw($where)->get();


        view()->share('tasks',$taskObj);
        view()->share('from_page','task_search_view');
        view()->share('unit_id',null);
        view()->share('objective_id',null);
        $html = view('tasks.partials.more_tasks')->render();
        return \Response::json(['success'=>true,'html'=>$html]);
    }
}
