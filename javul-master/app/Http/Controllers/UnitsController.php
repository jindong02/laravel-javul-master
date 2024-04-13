<?php

namespace App\Http\Controllers;

use App\Models\ActivityPoint;
use App\Models\City;
use App\Models\Country;
use App\Models\Fund;
use App\Models\Issue;
use App\Models\Objective;
use App\Models\RelatedUnit;
use App\Models\SiteActivity;
use App\Models\SiteConfigs;
use App\Models\State;
use App\Models\Task;
use App\Models\Unit;
use App\Models\UnitRevision;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hashids\Hashids;
use Carbon\Carbon;
use App\Models\UserMessages;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public $user_messages;
    public function __construct(){
        $this->middleware('auth',['except'=>['index','view','get_units_paginate','search_by_category','search_units','get_state',
            'get_city', 'categoryView']]);
        view()->share('site_activity_text','Unit Activity Log');
        $this->user_messages = new UserMessages();
    }

    public function diff($unit_id,$rev1,$rev2,Request $request){
        if(!empty($unit_id))
        {
            view()->share('unit_id',$unit_id );
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $units = Unit::getUnitWithCategories($unit_id);

                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);

                view()->share('unitObj',$units );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $revisions = UnitRevision::select(['unit_revisions.user_id','unit_revisions.description','unit_revisions.id','unit_revisions.unit_id','unit_revisions.comment','unit_revisions.size','unit_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'unit_revisions.user_id')
                            ->where("unit_revisions.unit_id","=",$unit_id)
                            ->whereIn("unit_revisions.id",[ (int)$rev1, (int)$rev2 ])
                            ->get();

                if($revisions->count() == 2){
                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));

                    view()->share('userIDHashID', $userIDHashID);
                    view()->share('Carbon', new Carbon);
                    view()->share('revisions',$revisions );

                    return view("units.revison.changes_difference");
                }
            }

        }
        return view('errors.404');
    }

    public function revison($unit_id,Request $request)
    {
        if(!empty($unit_id))
        {
            view()->share('unit_id',$unit_id );
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $units = Unit::getUnitWithCategories($unit_id);

                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);

                view()->share('unitObj',$units );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $revisions = UnitRevision::select(['unit_revisions.user_id','unit_revisions.id','unit_revisions.unit_id','unit_revisions.comment','unit_revisions.size','unit_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'unit_revisions.user_id')
                            ->where("unit_revisions.unit_id","=",$unit_id)
                            ->get();


                //Carbon::createFromFormat('Y-m-d H:i:s', $pageChanges->time_stamp)->diffForHumans();

                $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));

                view()->share('units', $units);
                view()->share('userIDHashID', $userIDHashID);
                view()->share('Carbon', new Carbon);
                view()->share('revisions',$revisions );

                return view("units.revison.view");

            }

        }
        return view('errors.404');
    }

    public function revisonview($unit_id,$revision_id,Request $request)
    {
        if(!empty($unit_id))
        {
            view()->share('unit_id',$unit_id );
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $units = Unit::getUnitWithCategories($unit_id);

                $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                $availableFunds =Fund::getUnitDonatedFund($unit_id);
                $awardedFunds =Fund::getUnitAwardedFund($unit_id);

                view()->share('unitObj',$units );
                view()->share('unit_activity_id',$unit_id);
                view()->share('availableFunds',$availableFunds );
                view()->share('awardedFunds',$awardedFunds );
                view()->share('site_activity',$site_activity);

                $revisions = UnitRevision::select(['unit_revisions.user_id','unit_revisions.id','unit_revisions.description','unit_revisions.unit_id','unit_revisions.comment','unit_revisions.size','unit_revisions.created_at','users.first_name','users.last_name',])
                            ->join('users', 'users.id', '=', 'unit_revisions.user_id')
                            ->where("unit_revisions.unit_id","=",$unit_id)
                            ->where("unit_revisions.id","=",$revision_id)
                            ->get();

                if($revisions->count()==1)
                {
                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));

                    view()->share('units', $units);
                    view()->share('userIDHashID', $userIDHashID);
                    view()->share('Carbon', new Carbon);
                    view()->share('revisions',$revisions->first());

                    return view("units.revison.view_revision");
                }

            }

        }
        return view('errors.404');
    }


    public function index(Request $request, $category_search = false) {
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

        // get all units for listing
        if($category_search) {
            $units = Unit::whereRaw('FIND_IN_SET(?, category_id)', [$category_search->id])->orderBy('id','desc')->paginate(\Config::get('app.page_limit'));
        } else {
            $units = Unit::orderBy('id','desc')->paginate(Config::get('app.page_limit'));
        }

        view()->share('units',$units );
        $site_activity = SiteActivity::orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
        view()->share('site_activity',$site_activity);
        view()->share('site_activity_text','Global Activity Log');

        $countries = Unit::getAllCountryWithFrequent();
        view()->share('countries',$countries);

        $unit_category_arr = UnitCategory::where('status','approved')->pluck('name','id');
        view()->share('unit_category_arr',$unit_category_arr);

        if($category_search) {
            view()->share('category_search', $category_search);
        }

        return view('units.units');
    }

    /**
     * Function is used to retrieve states from country id
     * @param Request $request
     * @return mixed
     */
    public function get_state(Request $request){
        $country_id = $request->input('country_id');

        $states = State::where('country_id',$country_id)->pluck('name','id');
        return response()->json(['success'=>true,'states'=>$states]);
    }

    /**
     * function is used to retrieve cities from state id
     * @param Request $request
     * @return mixed
     */
    public function get_city(Request $request){
        $state_id = $request->input('state_id');
        $cities = City::where('state_id',$state_id)->pluck('name','id');
        $state_name = null;
        if(empty($cities) || count($cities) == 0) {
            $state_name = State::getName($state_id);
        }
        return response()->json(['success'=>true,'cities'=>$cities,'state_name'=>$state_name]);
    }


    public function create(Request $request)
    {
        $unit_category_arr = UnitCategory::where('status','approved')->pluck('name','id');
        $unit_credibility_arr= SiteConfigs::getUnitCredibilityTypes();
        $countries = Unit::getAllCountryWithFrequent();
        $unitsObj = Unit::pluck('name','id');

        view()->share('totalUnits',count($unitsObj));
        view()->share('relatedUnitsObj',$unitsObj);
        view()->share('parentUnitsObj',$unitsObj);
        view()->share('countries',$countries);
        view()->share('unit_category_arr',$unit_category_arr);
        view()->share('unit_credibility_arr',$unit_credibility_arr);
        view()->share('unitObj',[] );
        view()->share('states',[]);
        view()->share('cities',[]);
        view()->share('relatedUnitsofUnitObj',[]);
        //if page is submitted
        if($request->isMethod('post')){
            $validator = Validator::make($request->all(), [
                'unit_name' => 'required',
                'unit_category' => 'required',
                'credibility' => 'required',
                'country' => 'required'
            ]);

            if ($validator->fails())
                return redirect()->back()->withErrors($validator)->withInput();


            // insert record into units table.
            $status = $request->input('status');
            if(empty($status))
                $status="disabled";
            else
                $status="active";

            $slug=substr(str_replace(" ","_",strtolower($request->input('unit_name'))),0,20);
            $empty_city_state_name = $request->input('empty_city_state_name');

            /*if(!empty($empty_city_state_name)) {
                $empty_city_state_name = json_decode($empty_city_state_name);
                if (count($empty_city_state_name) > 0)
                    $empty_city_state_name = array_shift($empty_city_state_name);
                dd($empty_city_state_name);
            }*/
            $city = $request->input('city');
            if(!empty($empty_city_state_name)) {
                $city = null;
            }

            $unitID = Unit::create([
                'user_id'=>Auth::user()->id,
                'name'=>$request->input('unit_name'),
                'slug'=>$slug,
                'category_id'=>implode(",",$request->input('unit_category')),
                'description'=>trim($request->input('description')),
                'credibility'=>$request->input('credibility'),
                'country_id'=>$request->input('country'),
                'state_id'=>$request->input('state'),
                'city_id'=>$city,
                'status'=>'active',
                'parent_id'=>$request->input('parent_unit'),
                'state_id_for_city_not_exits'=>$empty_city_state_name
            ])->id;


            // After Created Unit send mail to site admin
            $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
            $unitCreator = User::find(Auth::user()->id);

            $toEmail = $unitCreator->email;
            $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
            $subject="Unit Created";

//            \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
//            {
//                $message->to($toEmail,$toName)->subject($subject);
//                if(!empty($siteAdminemails))
//                    $message->bcc($siteAdminemails,"Admin")->subject($subject);
//
//                $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
//            });

            //if user selected related to unit then insert record to related_units table
            $related_unit = $request->input('related_to');
            if(!empty($related_unit)){
                RelatedUnit::create([
                    'unit_id'=>$unitID,
                    'related_to'=>implode(",",$related_unit)
                ]);
            }
            // add activity point for created unit and user.
            ActivityPoint::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=>$unitID,
                'points'=>2,
                'comments'=>'Unit Created',
                'type'=>'unit'
            ]);
            // add site activity record for global statistics.
            $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
            $user_id = $userIDHashID->encode(Auth::user()->id);

            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->encode($unitID);

            $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
            if(!empty(Auth::user()->username))
                $user_name =Auth::user()->username;

            SiteActivity::create([
                'user_id'=>Auth::user()->id,
                'unit_id'=>$unitID,
                'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'
                    .$user_name.'</a>
                created
                 unit <a href="'.url('units/'.$unit_id.'/'.$slug).'">'.$request->input('unit_name').'</a>'
            ]);

            $request->session()->flash('msg_val', $this->user_messages->getMessage('UNIT_CREATED')['text']);
            return redirect('units');
        }


        return view('units.create');
    }


    public function edit($unit_id,Request $request)
    {
        if(!empty($unit_id))
        {
            $unitIDEncoded = $unit_id;
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $units = Unit::getUnitWithCategories($unit_id);
                $totalUnits = Unit::count();
                view()->share('totalUnits',$totalUnits );
                //dd($request->all());
                if(!empty($units) && $request->isMethod('post')){
                    //update unit and redirect to units page
                    $validator = Validator::make($request->all(), [
                        'unit_name' => 'required',
                        'unit_category' => 'required',
                        'credibility' => 'required',
                        'country' => 'required'
                    ]);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInput();

                    // insert record into units table.
                    $status = $request->input('status');
                    if(empty($status))
                        $status="disabled";
                    else
                        $status="active";

                    if(Auth::user()->role != "superadmin")
                        $status="active";

                    $slug=substr(str_replace(" ","_",strtolower($request->input('unit_name'))),0,20);

                    // store old revision data start

                        $bytes = UnitRevision::strBytes( str_replace(' ', '', strip_tags($request->input('description'))) );
                        $oldBytes = UnitRevision::strBytes( str_replace(' ', '', strip_tags($units->description)) );

                        $UnitRevision = new UnitRevision();
                        $UnitRevision->unit_id  = $units->id;
                        $UnitRevision->user_id  = $units->user_id;
                        $UnitRevision->category_id  = $units->category_id;
                        $UnitRevision->name  = $units->name;
                        $UnitRevision->description  = $units->description;
                        $UnitRevision->comment  = $request->input('comment');
                        $UnitRevision->credibility  = $units->credibility;
                        $UnitRevision->country_id  = (int)$units->country_id;
                        $UnitRevision->state_id  = (int)$units->state_id;
                        $UnitRevision->city_id  = (int)$units->city_id;
                        $UnitRevision->status  = $units->status;
                        $UnitRevision->parent_id  = $units->parent_id;
                        $UnitRevision->modified_by  = Auth::user()->id;
                        $UnitRevision->size  = (  $bytes - $oldBytes );
                        $UnitRevision->created_at  = date("Y-m-d H:i:s");

                        $UnitRevision->save();

                    // store old revision data end

                    $empty_city_state_name = $request->input('empty_city_state_name');

                    /*if(!empty($empty_city_state_name)) {
                        $empty_city_state_name = json_decode($empty_city_state_name);
                        if (count($empty_city_state_name) > 0)
                            $empty_city_state_name = array_shift($empty_city_state_name);
                        dd($empty_city_state_name);
                    }*/
                    $city = $request->input('city');
                    if(!empty($empty_city_state_name)) {
                        $city = null;
                    }
                    // update unit data.
                    Unit::where('id',$unit_id)->update([
                        'name'=>$request->input('unit_name'),
                        'slug'=>$slug,
                        'category_id'=>implode(",",$request->input('unit_category')),
                        'description'=>trim($request->input('description')),
                        'credibility'=>$request->input('credibility'),
                        'country_id'=>$request->input('country'),
                        'comment'=>$request->input('comment'),
                        'state_id'=>$request->input('state'),
                        'city_id'=>$city,
                        'status'=>$status,
                        'parent_id'=>$request->input('parent_unit'),
                        'state_id_for_city_not_exits'=>$empty_city_state_name,
                        'modified_by'=>Auth::user()->id
                    ]);

                    //if user selected related to unit then insert record to related_units table
                    $related_unit = $request->input('related_to');
                    if(!empty($related_unit)){
                        $relatedUnitExist = RelatedUnit::where('unit_id',$unit_id)->count();
                        if($relatedUnitExist > 0){
                            RelatedUnit::where('unit_id',$unit_id)->update([
                                'related_to'=>implode(",",$related_unit)
                            ]);
                        }
                        else{
                            RelatedUnit::create([
                                'unit_id'=>$unit_id,
                                'related_to'=>implode(",",$related_unit)
                            ]);
                        }
                    }
                    else
                    {
                        $cnt = RelatedUnit::where('unit_id',$unit_id)->count();
                        if($cnt > 0)
                            RelatedUnit::where('unit_id',$unit_id)->forceDelete();
                    }
                    // add activity point for created unit and user.
                    ActivityPoint::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$unit_id,
                        'points'=>1,
                        'comments'=>'Unit Edited',
                        'type'=>'unit'
                    ]);
                    // add site activity record for global statistics.
                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
                    $tempUnitID= $unit_id;
                    $unit_id = $unitIDHashID->encode($unit_id);

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$tempUnitID,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name))
                            .'">'.$user_name.'</a>
                        updated unit <a href="'.url('units/'.$unit_id.'/'.$slug).'">'.$request->input('unit_name').'</a>'
                    ]);

                    // send alert to user(s) who has this unit in his/her watchlist
                    $watchlistUserObj = \DB::table('my_watchlist')
                                        ->join('users','my_watchlist.user_id','=','users.id')
                                        ->where('my_watchlist.user_id','!=',Auth::user()->id)
                                        ->where('unit_id',$tempUnitID)
                                        ->get();

                    $content = 'User <a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'.$user_name.'</a>' .
                        ' edited Unit <a href="'.url('units/'.$unit_id.'/'.$slug).'">'.$request->input('unit_name').'</a>';

                    $email_subject  ='User '.Auth::user()->first_name.' '.Auth::user()->last_name.' edited Unit '.$request->input('unit_name');
                    User::SendEmailAndOnSiteAlert($content,$email_subject,$watchlistUserObj,$onlyemail=false,'watched_items');

                    // After Created Unit send mail to site admin
                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find(Auth::user()->id);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Unit Updated";

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
                    });

                    $request->session()->flash('msg_val', 'UNIT_UPDATED');
                    return redirect('units/'.$unitIDEncoded.'/'.$slug);

                }
                elseif(!empty($units)){

                    //redirect to edit page
                    //$units = array_shift($units);
                    $unit_category_arr = UnitCategory::where('status','approved')->pluck('name','id');
                    $unit_credibility_arr= SiteConfigs::getUnitCredibilityTypes();
                    $countries = Country::pluck('name','id');
                    $countries = Unit::getAllCountryWithFrequent();
                    $states = State::where('country_id',$units->country_id)->pluck('name','id');
                    $cities = City::where('state_id',$units->state_id)->pluck('name','id');
                    $unitsObj = Unit::where('id','!=',$unit_id)->pluck('name','id');

                    $relatedUnitsofUnitObj = RelatedUnit::where('unit_id',$unit_id)->first();
                    if(!empty($relatedUnitsofUnitObj))
                        $relatedUnitsofUnitObj = explode(",",$relatedUnitsofUnitObj->related_to);
                    else
                        $relatedUnitsofUnitObj  = [];

                    view()->share('relatedUnitsObj',$unitsObj);
                    view()->share('relatedUnitsofUnitObj',$relatedUnitsofUnitObj);

                    view()->share('parentUnitsObj',$unitsObj);
                    view()->share('countries',$countries);
                    view()->share('states',$states);
                    view()->share('cities',$cities);

                    view()->share('unit_category_arr',$unit_category_arr);
                    view()->share('unit_credibility_arr',$unit_credibility_arr);

                    $state_name_as_city_for_field = null;
                    if(empty($units->city_id))
                    {
                        $state_name_as_city = (!empty($units->state_id_for_city_not_exits)?json_decode($units->state_id_for_city_not_exits):'');
                        //dd($unit->state_id_for_city_not_exits);
                        if(!empty($state_name_as_city)){
                            $state_name_as_city = array_shift($state_name_as_city);
                            $state_name_as_city_for_field = $state_name_as_city;
                        }
                    }
                    view()->share('state_name_as_city_for_field',$state_name_as_city_for_field );

                    view()->share('unitObj',$units );
                    return view('units.create');
                }
            }

        }
        return view('errors.404');
    }

    /**
     * Display Unit information only.
     * @param $unit_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($unit_id){
        if(!empty($unit_id))
        {
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];

                $unit = Unit::getUnitWithCategories($unit_id);

                if(!empty($unit)){
                    $objectives = Objective::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.page_limit'));
                    $related_units = RelatedUnit::getRelatedUnitName($unit_id);
                    $taskForBidding = Task::where('unit_id', '=', $unit_id)->where('status', '=', "approval")->count();
                    $userAuth = Auth::user();
                    $taskBidders = [];
                    if(!empty($userAuth))
                        $taskBidders = Task::join('task_bidders','tasks.id','=','task_bidders.task_id')->where('task_bidders.user_id',
                            Auth::user()->id)->where('unit_id',$unit_id)->where('tasks.status', '=', "approval")->count();

                    if($taskForBidding > 0 && Auth::check())
                        $taskForBidding = $taskForBidding - $taskBidders;

                    $state_name_as_city_for_field = null;
//                    dd($unit->country_id);
                    if($unit->country_id == 247)
                        $cityName = "Global";
                    elseif(!empty($unit->city_id))
                        $cityName = City::find($unit->city_id)->name;
                    else{
                        $state_name_as_city = (!empty($unit->state_id_for_city_not_exits)?json_decode($unit->state_id_for_city_not_exits):'');
                        //dd($unit->state_id_for_city_not_exits);
                        if(!empty($state_name_as_city)){
                            $state_name_as_city = array_shift($state_name_as_city);
                            $state_name_as_city_for_field = $state_name_as_city;
                            if(!empty($state_name_as_city))
                                $cityName = $state_name_as_city->name;
                        }
                    }


                    view()->share('taskForBidding',$taskForBidding);
                    view()->share('cityName',$cityName);
                    view()->share('related_units',$related_units);
                    view()->share('unitObj',$unit );
                    view()->share('objectivesObj',$objectives );

                    $availableFunds =Fund::getUnitDonatedFund($unit_id);
                    $awardedFunds =Fund::getUnitAwardedFund($unit_id);

                    view()->share('availableFunds',$availableFunds );
                    view()->share('awardedFunds',$awardedFunds );

                    $site_activity = SiteActivity::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.site_activity_page_limit'));
                    $taskObj = Task::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.page_limit'));
                    view()->share('taskObj',$taskObj);
                    view()->share('site_activity',$site_activity);
                    view()->share('unit_activity_id',$unit_id);

                    $issuesObj = Issue::where('unit_id',$unit_id)->orderBy('id','desc')->paginate(Config::get('app.page_limit'));
                    view()->share('issuesObj',$issuesObj);

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


                    return view('units.view');
                }
            }
        }
        return view('errors.404');
    }

    public function delete_unit(Request $request){
        $unitID = $request->input('id');
        if(!empty($unitID)){
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unitID = $unitIDHashID->decode($unitID);
            if(!empty($unitID)){
                $unitID = $unitID[0];
                $unitObj = Unit::find($unitID);
                $unitTemp = $unitObj;
                if(count($unitObj) > 0){
                    $objectiveObj = Objective::where('unit_id',$unitID)->get();
                    if(!empty($objectiveObj)){
                        foreach($objectiveObj as $objective){
                            $tasksObj = Task::where('objective_id',$objective->id)->get();
                            if(count($tasksObj) > 0){
                                foreach($tasksObj  as $task)
                                    Task::deleteTask($task->id);
                            }
                            Objective::find($objective->id)->delete();
                        }
                    }
                    $unitObj->delete();
                    // add activity point for created unit and user.
                    ActivityPoint::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$unitID,
                        'points'=>1,
                        'comments'=>'Unit deleted',
                        'type'=>'unit'
                    ]);

                    // add site activity record for global statistics.
                    $userIDHashID= new Hashids('user id hash',10,Config::get('app.encode_chars'));
                    $user_id = $userIDHashID->encode(Auth::user()->id);

                    /*$objectiveIDHashID = new Hashids('objective id hash',10,\Config::get('app.encode_chars'));
                    $objectiveId = $objectiveIDHashID->encode($objectiveID);*/

                    $user_name=Auth::user()->first_name.' '.Auth::user()->last_name;
                    if(!empty(Auth::user()->username))
                        $user_name =Auth::user()->username;

                    SiteActivity::create([
                        'user_id'=>Auth::user()->id,
                        'unit_id'=>$unitID,
                        'comment'=>'<a href="'.url('userprofiles/'.$user_id.'/'.strtolower(Auth::user()->first_name.'_'.Auth::user()->last_name)).'">'.$user_name
                            .'</a>
                        deleted unit '.$unitTemp->name
                    ]);

                    // After Created Unit send mail to site admin
                    $siteAdminemails = User::where('role','superadmin')->pluck('email')->all();
                    $unitCreator = User::find(Auth::user()->id);

                    $toEmail = $unitCreator->email;
                    $toName= $unitCreator->first_name.' '.$unitCreator->last_name;
                    $subject="Unit Deleted";

                    \Mail::send('emails.registration', ['userObj'=> $unitCreator,'report_concern'=>false ], function($message) use ($toEmail,$toName,$subject,$siteAdminemails)
                    {
                        $message->to($toEmail,$toName)->subject($subject);
                        if(!empty($siteAdminemails))
                            $message->bcc($siteAdminemails,"Admin")->subject($subject);

                        $message->from(Config::get("app.notification_email"), Config::get("app.site_name"));
                    });

                    return response()->json(['success'=>true]);
                }
            }
        }
        return response()->json(['success'=>false]);
    }


    public function available_bids($unit_id){
        if(!empty($unit_id)){
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($unit_id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $unitObj = Unit::find($unit_id);
                if(!empty($unitObj)){
                    $taskObj = Task::where('unit_id', '=', $unit_id)->where('status', '=', "approval")->get();
                    view()->share('taskObj',$taskObj);
                    return view('tasks.available_for_bid');
                }
            }
        }
        return view('errors.404');
    }

    public function get_units_paginate(Request $request)
    {
        $page_limit = Config::get('app.page_limit');
        $units = Unit::orderBy('id','desc')->paginate($page_limit);
        view()->share('units',$units);
        $html = view('units.partials.more_units')->render();
        return response()->json(['success'=>true,'html'=>$html]);

    }

    public function get_featured_unit(Request $request){
        $terms = $request->input('term');
        if(!empty($terms)){
            $obj = Unit::where('name','like',$terms.'%')->get();
            $names = [];
            if(!empty($obj) && count($obj) > 0){
                foreach($obj as $unit){
                    $names[]=['id'=>$unit->id,'text'=>$unit->name];
                }

            }
            return response()->json(['items'=>$names,'total_counts'=>$obj = Unit::where('name','like',$terms.'%')->get()]);
        }
        return response()->json([]);

    }

    public function set_featured_unit(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        if(!empty($id) && $type == "set"){
            $unitIDHashID = new Hashids('unit id hash',10,Config::get('app.encode_chars'));
            $unit_id = $unitIDHashID->decode($id);
            if(!empty($unit_id)){
                $unit_id = $unit_id[0];
                $unitObj = Unit::find($unit_id);
                if(!empty($unitObj)){
                    $featuredUnits = Unit::where('featured_unit',1)->get();
                    if(!empty($featuredUnits) && count($featuredUnits) > 0){
                        foreach($featuredUnits as $fUnit){
                            Unit::find($fUnit->id)->update(['featured_unit'=>0]);
                        }
                    }
                    $unitObj->update(['featured_unit'=>1]);
                    return response()->json(['success'=>true]);
                }
            }
        }
        if($type == "delete")
        {
            $featuredUnits = Unit::where('featured_unit',1)->get();
            if(!empty($featuredUnits) && count($featuredUnits) > 0){
                foreach($featuredUnits as $fUnit){
                    Unit::find($fUnit->id)->update(['featured_unit'=>0]);
                }
            }
            return response()->json(['success'=>true]);
        }
        return response()->json(['success'=>false,'msg'=>'Something goes wrong. Please try again later.']);
    }
    public function search_units(Request $request){
        $by_category_type = $request->input('category');

        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');

        /*$units = Unit::orderBy('id','desc')->paginate(\Config::get('app.page_limit'));
        view()->share('units',$units );*/

        $where = '';
        DB::enableQueryLog();
        $unitObj = DB::table('units');

        if(!empty($by_category_type)){
            foreach($by_category_type as $index=>$cate_id){
                if($index == 0)
                    $where.='FIND_IN_SET('.$cate_id.',category_id)';
                else
                    $where.=' AND FIND_IN_SET('.$cate_id.',category_id)';
            }
            //$where.='FIND_IN_SET('.$by_category_type.',category_id)';
        }
        if(trim($country) != "" || trim($state) != "" || trim($city) != ""){
            // country
            if(trim($country) != "") {
                if (!empty($where)) {
                    if(empty($state) && empty($city))
                        $where .= ' and (country_id = ' . $country.')';
                    else
                        $where .= ' and (country_id = ' . $country;
                }
                else
                    $where .= ' country_id = '.$country;
            }

            //state
            if(trim($state) != "") {
                if (!empty($where)) {
                    if(trim($country) != "") {
                        if(empty($city) && !empty($by_category_type))
                            $where .= ' AND state_id = ' . $state.')';
                        else
                            $where .= ' AND state_id = ' . $state;
                    }
                    else
                        $where .= ' OR state_id = ' . $state;
                }
                else {
                    if(trim($country) != "")
                        $where .= ' AND state_id = ' . $state;
                    else
                        $where .= ' state_id = ' . $state;
                }
            }

            //city
            if(trim($city) != "") {
                if (!empty($where)) {
                    if(trim($country) != "" && trim($state) != "") {
                        if(!empty($by_category_type))
                            $where .= ' AND city_id = ' . $city . ')';
                        else
                            $where .= ' AND city_id = ' . $city;
                    }
                    else
                        $where .= ' OR city_id = ' . $city;
                }
                else {
                    if(trim($country) != "" && trim($state) != "")
                        $where .= ' AND city_id = ' . $city;
                    else
                        $where .= ' city_id = ' . $city;
                }
            }
        }

        $unitObj = $unitObj->whereRaw($where)->paginate(Config::get('app.page_limit'));

        view()->share('units',$unitObj);
        $html = view('units.partials.more_units')->render();
        if(empty($html))
            $html = "<tr><td colspan='3'>No record(s) found.</td></tr>";
        return response()->json(['success'=>true,'html'=>$html]);
    }

    public function search_by_location(Request $request){
        $terms = $request->input('term');
        $page = $request->input('page');
        if(!empty($terms)){
            if($page == 0)
                $obj = UnitCategory::where('name','like',$terms.'%')->get();
            else {
                $offset = ($page - 1) * 10;
                $obj = UnitCategory::where('name','like',$terms.'%')->skip($offset)->take(10)->get();
            }

            $names = [];
            if(!empty($obj) && count($obj) > 0){
                foreach($obj as $category){
                    $names[]=['id'=>$category->id,'name'=>$category->name];
                }

            }
            return response()->json(['items'=>$names,'total_counts'=> UnitCategory::where('name','like',$terms.'%')->count()]);
        }
        return response()->json([]);
    }
    public function search_by_category(Request $request){
        $terms = $request->input('q');
        $page = $request->input('page');
        if(!empty($terms)){
            if($page == 0)
                $obj = UnitCategory::where('name','like',$terms.'%')->get();
            else {
                $offset = ($page - 1) * 10;
                $obj = UnitCategory::where('name','like',$terms.'%')->skip($offset)->take(10)->get();
            }

            $names = [];
            if(!empty($obj) && count($obj) > 0){
                foreach($obj as $category){
                    $names[]=['id'=>$category->id,'text'=>$category->name];
                }

            }
            return response()->json(['items'=>$names,'total_counts'=> UnitCategory::where('name','like',$terms.'%')->count()]);
        }
        return response()->json([]);
    }

    public function show()
    {

    }

    public function categoryView(Request $request, $type)
    {
        $category = UnitCategory::where('name', $type)->first();

        return $this->index($request, $category);
    }
}
