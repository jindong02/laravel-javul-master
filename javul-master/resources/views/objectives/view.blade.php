@extends('layout.default')
@section('page-meta')
<title>Objective: {{$objectiveObj->name}} - Javul.org</title>
@endsection
@section('page-css')
<style>.related_para{margin:0 0 10px;}</style>
@endsection
@section('content')

<div class="container">
    <div class="row form-group" style="margin-bottom:15px">
        @include('elements.user-menu',['page'=>'objectives'])
    </div>
    <div class="row form-group">
        <div class="col-md-8 col-md-push-4">
            <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                <div class="panel-heading current_objective_heading featured_unit_heading">
                    <div class="featured_unit current_objective">
                        <i class="fa fa-bullseye" style="font-size:18px"></i>
                    </div>
                    <h4>OBJECTIVE INFORMATION</h4>
                </div>
                <div style="padding: 0px;" class="panel-body current_unit_body list-group">
                    <div class="list-group-item" style="padding-top:0px;padding-bottom:0px;">
                        <div class="row" style="border-bottom:1px solid #ddd;">
                            <div class="col-sm-7 featured_heading">
                                <h4 class="colorOrange">{{$objectiveObj->name}}</h4>
                            </div>
                            <div class="col-sm-5 featured_heading text-right colorLightBlue">
                                <div class="row">
                                    <div class="col-xs-3 text-center">
                                        <a class="add_to_my_watchlist" data-type="objective" data-id="{{$objectiveIDHashID->encode($objectiveObj->id)}}" data-redirect="{{url()->current()}}" >
                                            <i class="fa fa-eye" style="margin-right:2px"></i>
                                            <i class="fa fa-plus plus"></i>
                                        </a>
                                    </div>
                                    <div class="col-xs-2 text-center">
                                        <a href="{!! url('objectives/'.$objectiveIDHashID->encode($objectiveObj->id).'/edit')!!}">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </div>
                                    <div class="col-xs-7 text-center">
                                        <a href="{!! route('objectives_revison',[$objectiveIDHashID->encode($objectiveObj->id)]) !!}"><i class="fa fa-history"></i> REVISION HISTORY</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-7 featured_heading" style="min-height: 150px">
                                {!! $objectiveObj->description !!}
                            </div>
                            <div class="col-xs-5 featured_heading text-right obj_info_div">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <label class="control-label upper" style="width: 100%;">
                                            <span class="fund_icon">FUNDS</span>
                                            <span class="text-right pull-right">
                                                <a href="{!! url('funds/donate/objective/'.$objectiveIDHashID->encode($objectiveObj->id))!!}"><div class="fund_paid"><i class="fa fa-plus plus"></i></div></a>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-xs-8 text-left borderLFT" style="padding-top:3px; ">
                                        <div>
                                            <label class="control-label">
                                                Available
                                            </label>
                                            <label class="control-label label-value pull-right">{{number_format
                                                ($availableObjFunds,2)}} $</label>
                                        </div>
                                        <div>
                                            <label class="control-label">
                                                Awarded
                                            </label>
                                            <label class="control-label label-value
                                            pull-right">{{number_format($awardedObjFunds,2)}} $</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row borderBTM lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">Status</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        <label class="control-label">{{ \App\Objective::objectiveStatus()[$objectiveObj->status] }}</label>
                                    </div>
                                </div>
                                @if(\Auth::check())
                                    <div class="row borderBTM lnht30">
                                        <div class="col-xs-4 text-left">
                                            <label class="control-label upper">SUPPORT</label>
                                        </div>
                                        <div class="col-xs-8 borderLFT">
                                            <div class="importance-div">
                                                @include('objectives.partials.importance_level',['objective_id'=>$objectiveObj->id])
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                <div class="panel-heading">
                    <h4>TASKS</h4>
                </div>
                <div class="panel-body list-group loading_content_hide" style="position: relative;">
                    <div class="loading_dots task_loading" style="position: absolute;top:0%;left:43%;z-index: 9999;display: none;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <table class="table table-striped tasks-table">
                        <thead>
                        <tr>
                            <th>Task Name</th>
                            <th class="text-center">Status</th>
                            <th class="text-center"><i class="fa fa-trophy"></i></th>
                            <th class="text-center"><i class="fa fa-clock-o"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($objectiveObj->tasks) > 0)
                        @foreach($objectiveObj->tasks as $obj)
                        <tr>
                            <td>
                                <a href="{!! url('tasks/'.$taskIDHashID->encode($obj->id).'/'.$obj->slug) !!}"
                                   title="edit">
                                    {{$obj->name}}
                                </a>
                            </td>
                            <td class="text-center">
                                @if($obj->status == "editable")
                                    <span class="colorLightGreen">{{\App\SiteConfigs::task_status($obj->status)}}</span>
                                @else
                                    <span class="colorLightGreen">{{\App\SiteConfigs::task_status($obj->status)}}</span>
                                @endif
                            </td>
                            <td class="text-center">{{\App\Task::getTaskCount('in-progress',$obj->id)}}</td>
                            <td class="text-center">{{\App\Task::getTaskCount('completed',$obj->id)}}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">No record(s) found.</td>
                        </tr>
                        @endif
                        <tr style="background-color: #fff;text-align: right;">
                            <td colspan="5">
                                <a class="btn black-btn" id="add_task_btn" href="{!! url('tasks/'.$unitIDHashID->encode($objectiveObj->unit_id)
                                .'/'.$objectiveIDHashID->encode($objectiveObj->id).'/add') !!}">
                                    <i class="fa fa-plus plus"></i> <span class="plus_text">ADD TASK</span>
                                </a>

                                @if($objectiveObj->tasks->lastPage() > 1 && $objectiveObj->tasks->lastPage() != $objectiveObj->tasks->currentPage())
                                    <a href="#" data-url="{{$objectiveObj->tasks->url($objectiveObj->tasks->currentPage()+1) }}"
                                       data-objective_id="{{$objectiveIDHashID->encode($objectiveObj->id)}}" class="btn
                                    more-black-btn more-tasks" data-from_page="unit_view" type="button">
                                        MORE TASKS <span class="more_dots">...</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="panel panel-grey panel-default">
                <div class="panel-heading">
                    <h4>RELATION TO OTHER OBJECTIVES</h4>
                </div>
                <div class="panel-body list-group">
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label">
                                    Parent Objective
                                </label>
                                <label class="control-label colorLightBlue form-control label-value">
                                    <?php $objSlug = \App\Objective::getSlug($objectiveObj->parent_id); ?>
                                    <a style="font-weight: normal;" class="no-decoration" href="{!! url('objectives/'
                                    .$objectiveIDHashID->encode
                                    ($objectiveObj->parent_id).'/'.$objSlug ) !!}">
                                        {{\App\Objective::getObjectiveName($objectiveObj->parent_id)}}
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">
                                    Child Objective
                                </label>
                                <label class="control-label colorLightGreen form-control label-value">
                                    -
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('forum.element.objective')

        </div>
        <div class="col-md-4 col-md-pull-8">
            @include('units.partials.unit_information_left_table',['unitObj'=>$objectiveObj->unit,'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
            <div class="left" style="position: relative;margin-top: 30px;">
                <div class="site_activity_loading loading_dots" style="position: absolute;top:20%;left:43%;z-index: 9999;display: none;">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="site_activity_list">
                    @include('elements.site_activities',['ajax'=>false])
                </div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@endsection
@section('page-scripts')
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/forumObjective.js') !!}" type="text/javascript"></script>
<script>

    $(document).ready(function(){
                @if(!empty($add_to_watch['id']))
        var session_id = '{{$add_to_watch['id']}}';
        $("[data-id='" + session_id + "']").click();
        @endif
    });

    $(function(){
        $(".both-div").css("min-height",($(".objective-desc").height())+10+'px');

        $(document).off('click','.vote').on('click',".vote",function(){

            var type = $(this).attr('data-type');
            $parentDiv = $(this).parent('div');
            var flag= true;
            //if(type == "up")
            //    var flag =!$parentDiv.hasClass('success-upvote');
            //else if(type=="down")
            //    var flag =!$parentDiv.hasClass('success-downvote');
            //else
              //  return false;
            //alert(flag);
            if(flag){
                var that = $(this);
                var id=$(this).attr('data-id');
                if($.trim(id) != ""){
                    $.ajax({
                        type:'post',
                        url:siteURL+'/objectives/importance',
                        data:{_token:'{!! csrf_token() !!}',id:id,type:type},
                        dataType:'json',
                        success:function(resp){
                            if(resp.success){
                                $(".importance-div").html(resp.html);
                                if(type == "up")
                                {
                                    that.removeClass('text-success');
                                    $parentDiv.addClass('success-upvote');
                                    $(".downvote[data-id='" + id + "']").removeClass('success-downvote');
                                }
                                else{
                                    that.removeClass('text-danger');
                                    $parentDiv.addClass('success-downvote');
                                    $(".upvote[data-id='" + id + "']").removeClass('success-upvote');
                                }
                            }

                        }
                    })
                }
            }
            return false;
        });
    })
</script>
@endsection
