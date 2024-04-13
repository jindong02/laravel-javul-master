@extends('layout.default')
@section('page-meta')
<title>User: {{$userObj->first_name}} {{$userObj->last_name}} - Javul.org</title>
@endsection
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-star-rating-master/css/star-rating.css') !!}" media="all" rel="stylesheet" type="text/css" />
<style>
    span.tags{padding:0 6px;}
    .text-danger{color:#ed6b75 !important;}
    .navbar-nav > li.active{background-color: #e7e7e7;}
    .panel-body.list-group>.list-group-item>div.row>.col-xs-12>div:last-child>.border-main>.last-site-activity{ display:none !important; }
    .panel-body.list-group>.list-group-item>div.row>.col-xs-12>div:last-child>.border-main>div{ height: 71px; }

</style>
@endsection
@section('content')

<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',array('page'=>'home'))
    </div>
    @include('users.user-profile')
    <div class="row">
        <div class="col-sm-4">
            <div class="left" style="position: relative;margin-top: 30px;">
                <div class="site_activity_loading loading_dots" style="position: absolute;top:20%;left:43%;z-index: 9999;display: none;">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="site_activity_list">
                    @include('elements.site_activities_user',['site_activity'=>$site_activities])
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <h3 style="display: inline-block;width: 70%;">Total Activity Points : {{$activityPoints}} | Idea Points : {{$activityPoints_forum}}</h3>
            @if($userObj->paypal_email)
            <a class="btn black-btn btn-sm" id="add_funds_btn" href="{!! url('funds/donate/user/'.$userIDHashID->encode($userObj->id)) !!}"
               style="display: inline-block;float:right;margin-top:10px">
                <i class="fa fa-plus plus"></i>
                {!! trans('messages.add_funds')!!}
            </a>
            @endif
            <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                <li class="active"><a href="#unit_details" data-toggle="tab">Units Details</a></li>
                <li><a href="#objectives_details" data-toggle="tab">Objectives Details</a></li>
                <li><a href="#tasks_details" data-toggle="tab">Tasks Details</a></li>
            </ul>
            <div id="my-tab-content" class="tab-content">
                <div class="list-group tab-pane active table-responsive" id="unit_details">
                    <div style="border:1px solid #ddd; ">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <thead>
                            <tr>
                                <th>Unit Name</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($unitsObj) && count($unitsObj) > 0)
                                    @foreach($unitsObj as $unit)
                                        <tr>
                                            <td>
                                                <a href="{!! url('units/'.$unitIDHashID->encode($unit->id).'/edit') !!}">
                                                    {{$unit->name}}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="colorLightGreen">{{$unit->status}}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">No record(s) found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="list-group tab-pane table-responsive" id="objectives_details">
                    <div style="border:1px solid #ddd;">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <thead>
                            <tr>
                                <th>Objective Name</th>
                                <th>Unit Name</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($objectivesObj) && count($objectivesObj) > 0)
                                    @foreach($objectivesObj as $objective)
                                        <tr>
                                            <td>
                                                <a href="{!! url('objectives/'.$objectiveIDHashID->encode($objective->id).'/edit') !!}">
                                                    {{$objective->name}}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{!! url('units/'.$unitIDHashID->encode($objective->unit_id).'/edit') !!}">
                                                    {{\App\Unit::getUnitName($objective->unit_id)}}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">No record(s) found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="list-group tab-pane table-responsive" id="tasks_details">
                    <div style="border:1px solid #ddd;">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Objective Name</th>
                                <th>Unit Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($tasksObj) && count($tasksObj) > 0)
                                @foreach($tasksObj as $task)
                                    <tr>
                                        <td>
                                            <a href="{!! url('tasks/'.$taskIDHashID->encode($task->id).'/edit') !!}">
                                                {{$task->name}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{!! url('objectives/'.$objectiveIDHashID->encode($task->objective_id).'/edit') !!}">
                                                {{\App\Objective::getObjectiveName($task->objective_id)}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{!! url('units/'.$unitIDHashID->encode($task->unit_id).'/edit') !!}">
                                                {{\App\Unit::getUnitName($task->unit_id)}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No record(s) found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel panel-grey panel-default" style="margin-top:29px ">
                <div class="panel-heading">
                    <h4 class="pull-left">User Wiki</h4>
                    <div class="user-wikihome-tool pull-right">
                       <div class="user-wikihome-tool pull-right small-a">
                       <a href="{{ route('user_wiki_newpage',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> + New Page </a> | 
                       <a href="{{ route('user_wiki_recent_changes',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> Recent Changes </a> |
                       <a href="{{ route('user_wiki_page_list',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> List All Pages </a>
                    </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body table-inner table-responsive loading_content_hide">
                    <div class="wiki-home" style="padding:5px">
                        <div class="pull-right small-a">
                            <a href="{{ route('user_wiki_editpage',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash, $page_id_hase ])  }}">Edit</a>
                            <a href="{{ route('user_wiki_history',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash, $page_id_hase ])  }}">View History</a>
                            
                        </div>
                        <div class="clearfix"></div>
                            <?= $userWiki[0]->page_content ?>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('page-scripts')
<!-- important mandatory libraries -->
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $('#input-3').rating({displayOnly: true, step: 0.1,size:'xs'});
        $('#tabs').tab();
    })
</script>
@endsection