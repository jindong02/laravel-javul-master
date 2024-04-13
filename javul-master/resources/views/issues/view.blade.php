@extends('layout.default')
@section('page-meta')
<title>Issue: {{$issueObj->title}} - Javul.org</title>
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
                    <div class="panel-heading current_issue_heading featured_unit_heading">
                        <div class="featured_unit current_issue">
                            <i class="fa fa-bug" style="font-size:18px"></i>
                        </div>
                        <h4>ISSUE INFORMATION</h4>
                    </div>
                    <div style="padding: 0px;" class="panel-body current_unit_body list-group">
                        <div class="list-group-item" style="padding-top:0px;padding-bottom:0px;">
                            <div class="row" style="border-bottom:1px solid #ddd;">
                                <div class="col-sm-7 featured_heading">
                                    <h4 class="colorIssue">{{$issueObj->title}}</h4>
                                </div>
                                <div class="col-sm-5 featured_heading text-right colorLightBlue">
                                    <div class="row">

                                        <div class="col-xs-3 text-center">
                                            <a class="add_to_my_watchlist" data-type="issue" data-id="{{$issueIDHashID->encode($issueObj->id)}}" data-redirect="{{url()->current()}}" >
                                                <i class="fa fa-eye" style="margin-right:2px"></i>
                                                <i class="fa fa-plus plus"></i>
                                            </a>
                                        </div>
                                        <div class="col-xs-2 text-center">
                                            <a href="{!! url('issues/'.$issueIDHashID->encode($issueObj->id).'/edit')!!}">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        </div>
                                        <div class=" col-xs-7 text-center">
                                            <a href="{!! route('issues_revison',[$issueIDHashID->encode($issueObj->id)]) !!}"><i class="fa fa-history"></i> REVISION HISTORY</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-7 featured_heading" style="min-height: 150px">
                                    {!! $issueObj->description !!}
                                </div>
                                <div class="col-xs-5 featured_heading text-right obj_info_div">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <label class="control-label upper" style="width: 100%;">
                                                <span class="fund_icon">FUNDS</span>
                                                <span class="text-right pull-right">
                                                    <a href="{!! url('funds/donate/issue/'.$issueIDHashID->encode($issueObj->id)) !!}">
                                                    <div class="fund_paid">
                                                        <i class="fa fa-plus plus"></i>
                                                    </div>
                                                    </a>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-xs-8 text-left borderLFT" style="padding-top:3px; ">
                                            <div>
                                                <label class="control-label">
                                                    Available
                                                </label>
                                                <label class="control-label label-value pull-right">{{number_format
                                                (0,2)}} $</label>
                                            </div>
                                            <div>
                                                <label class="control-label">
                                                    Awarded
                                                </label>
                                                <label class="control-label label-value
                                            pull-right">{{number_format(0,2)}} $</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row borderBTM lnht30">
                                        <div class="col-xs-4 text-left">
                                            <label class="control-label upper">Status</label>
                                        </div>
                                        <div class="col-xs-8 borderLFT text-left">
                                            <label class="control-label {{$status_class}}">
                                                <?php $verified_by ='';?>
                                                @if($issueObj->status == "verified")
                                                    <?php $verified_by = " (by ".App\Models\User::getUserName($issueObj->verified_by).')';?>
                                                @endif
                                                {{ucfirst($issueObj->status. $verified_by )}}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row borderBTM lnht30">
                                        <div class="col-xs-4 text-left">
                                            <label class="control-label upper">SUPPORT</label>
                                        </div>
                                        <div class="col-xs-8 borderLFT">
                                            <div class="importance-div">
                                                @include('issues.partials.importance_level',['issue_id'=>$issueObj->id])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                    <div class="panel-heading">
                        File Attachments
                    </div>
                    <div class="panel-body list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-sm-12 file_list" style="padding-left: 0px;">
                                    @if(!empty($issueObj->issue_documents) && count($issueObj->issue_documents) > 0)
                                        <ul style="list-style-type: decimal; padding-left:30px;">
                                            @foreach($issueObj->issue_documents as $index=>$document)
                                                <?php $extension = pathinfo($document->file_path, PATHINFO_EXTENSION); ?>
                                                @if($extension == "pdf") <?php $extension="pdf"; ?>
                                                @elseif($extension == "doc" || $extension == "docx") <?php $extension="docx"; ?>
                                                @elseif($extension == "jpg" || $extension == "jpeg") <?php $extension="jpeg"; ?>
                                                @elseif($extension == "ppt" || $extension == "pptx") <?php $extension="pptx"; ?>
                                                @else <?php $extension="file"; ?> @endif
                                                <li>
                                                    <a class="files_image" href="{!! url($document->file_path) !!}" target="_blank">
                                            <span style="display:block">
                                                @if(empty($document->file_name))
                                                    &nbsp;
                                                @else
                                                    {{$document->file_name}}
                                                @endif
                                            </span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <ul style="list-style-type: none; padding-left:15px;margin-bottom:0px">
                                            <li>No document found(s).</li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($issueObj->status == "resolved")
                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading">
                            Resolution
                        </div>
                        <div class="panel-body list-group">
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! $issueObj->resolution !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>RELATION to Objective and Task</h4>
                    </div>
                    <div class="panel-body list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label">
                                        Associated Objective
                                    </label>
                                    <label class="control-label colorLightBlue form-control label-value">
                                        <?php $objSlug = \App\Models\Objective::getSlug($issueObj->objective_id); ?>
                                        <a style="font-weight: normal;" class="no-decoration" href="{!! url('objectives/'
                                    .$objectiveIDHashID->encode
                                    ($issueObj->objective_id).'/'.$objSlug ) !!}">
                                            <span class="badge">{{\App\Models\Objective::getObjectiveName($issueObj->objective_id)}}</span>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label">
                                        Associated Tasks
                                    </label>
                                    <label class="control-label colorLightGreen form-control label-value">
                                        <?php $task_ids = explode(",",$issueObj->task_id); $i=1;?>
                                        @if(count($task_ids) > 0)
                                            @foreach($task_ids as $t_id)
                                                <a style="font-weight: normal;" class="no-decoration" href="{!! url('tasks/'
                                                    .$taskIDHashID->encode($t_id).'/'.\App\Models\Task::getSlug($t_id) ) !!}">
                                                    <span class="badge">{{\App\Models\Task::getName($t_id)}}</span>
                                                </a>
                                                <?php $i++; ?>
                                            @endforeach
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('forum.element.objective')

            </div>
            <div class="col-md-4 col-md-pull-8">
                @include('units.partials.unit_information_left_table',['unitObj'=>$unitObj,'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
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
                var flag = true;
                //if(type == "up")
                   // var flag =!$parentDiv.hasClass('success-upvote');
                //else if(type=="down")
                   // var flag =!$parentDiv.hasClass('success-downvote');
                //else
                    //return false;
                if(flag){
                    var that = $(this);
                    var id=$(this).attr('data-id');
                    if($.trim(id) != ""){
                        $.ajax({
                            type:'post',
                            url:siteURL+'/issues/importance',
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
