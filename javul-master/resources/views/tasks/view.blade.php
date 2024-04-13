@extends('layout.default')
@section('page-meta')
<title>Task: {{$taskObj->name}} - Javul.org</title>
@endsection
@section('page-css')
<style>
    hr, p{margin:0 0 10px !important;}
    .files_image:hover{text-decoration: none;}
    .file_documents{display: inline-block;padding: 10px;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom:15px">
        @include('elements.user-menu',['page'=>'tasks'])
    </div>
    <div class="row form-group">
        <div class="col-md-8 col-md-push-4">
            <div class="panel panel-grey panel-default">
                <div class="panel-heading current_task_heading featured_unit_heading">
                    <div class="featured_unit current_task">
                        <i class="fa fa-pencil-square-o"></i>
                    </div>
                    <h4>TASK INFORMATION</h4>
                </div>
                <div style="padding: 0px;" class="panel-body current_unit_body list-group form-group">
                    <div class="list-group-item" style="padding-top:0px;padding-bottom:0px;">

                        <div class="row" style="border-bottom:1px solid #ddd;">
                            <div class="col-sm-7 featured_heading">
                                <h4 class="colorLightGreen">{{$taskObj->name}}</h4>
                            </div>
                            <div class="col-sm-5 featured_heading text-right colorLightBlue">
                                <div class="row">
                                    <div class="col-xs-3 text-center">
                                        <a class="add_to_my_watchlist" data-type="task" data-id="{{$taskIDHashID->encode($taskObj->id)}}">
                                            <i class="fa fa-eye" style="margin-right:2px"></i>
                                            <i class="fa fa-plus plus"></i>
                                        </a>
                                    </div>
                                    <div class="col-xs-2 text-center">
                                        @if($taskObj->status == "editable")
                                        <a title="Edit Task" href="{!! url('tasks/'.$taskIDHashID->encode($taskObj->id).'/edit')!!}">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="col-xs-7 text-center">
                                        <a href="{!! route('tasks_revison',[$taskIDHashID->encode($taskObj->id)]) !!}"><i class="fa fa-history"></i> REVISION HISTORY</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-7 featured_heading" style="min-height: 233px">
                                {!! $taskObj->description !!}
                            </div>
                            <div class="col-xs-5 featured_heading text-right colorLightBlue" style="margin-top:0px;padding-top:0px;
                            padding-bottom: 0px;">
                                <div class="row borderBTM lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">Status</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        <label class="control-label colorLightGreen">{{\App\SiteConfigs::task_status($taskObj->status)}}</label>
                                        @if($taskObj->status == "open_for_bidding" && Auth::check())
                                            @if(\App\TaskBidder::checkBid($taskObj->id))
                                                <a title="bid now" href="{!! url('tasks/bid_now/'.$taskIDHashID->encode($taskObj->id)).'#bid_now' !!}" class="btn btn-xs btn-primary" style="color:#fff !important;">
                                                    Bid now
                                                </a>
                                            @else
                                                <a title="applied bid" class="btn btn-xs btn-warning" style="color:#fff !important;">
                                                    Applied Bid
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="row borderBTM lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">skills</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        @if(!empty($skill_names) && count($skill_names) > 0)
                                            @foreach($skill_names as $skil)
                                                <label class="control-label ft_size13 form-control text-label-value">{{$skil}}</label>
                                            @endforeach
                                        @else
                                            <label class="control-label form-control text-label-value">-</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="row borderBTM lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">Award</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        <label class="control-label">${{ $taskObj->compensation }}</label>
                                    </div>
                                </div>
                                <div class="row borderBTM lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">Completion</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        <label class="control-label">{{ $taskObj->completionTime }}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-4">
                                        {{--<label class="control-label upper" style="width: 100%;">--}}
                                            {{--<span class="fund_icon">FUNDS</span>--}}
                                            {{--<span class="text-right pull-right">--}}
                                                {{--<a href="{!! url('funds/donate/task/'.$taskIDHashID->encode($taskObj->id))!!}"><div class="fund_paid"><i class="fa fa-plus plus"></i></div></a>--}}
                                            {{--</span>--}}
                                        {{--</label>--}}
                                    </div>
                                    <div class="col-xs-8 text-left borderLFT" style="padding-top:3px; ">
                                        <div>
                                            <label class="control-label">
                                                Available
                                            </label>
                                            <label class="control-label label-value pull-right">{{number_format($availableFunds,2)}} $</label>
                                        </div>
                                        <div>
                                            <label class="control-label">
                                                Awarded
                                            </label>
                                            <label class="control-label label-value pull-right">{{number_format($availableFunds,2)}} $</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div style="padding:5px 15px;background-color: #f9f9f9;margin-top:0px">
                            <h4 style="font-weight: 500;">Summary :</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="padding:20px 20px 10px 30px;">
                        {!! $taskObj->summary!!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 action_list" style="padding-right: 0px;">
                        <h4 style="padding:10px 15px;background-color: #f9f9f9;margin-top:0px;font-weight: 500;">Action Items</h4>
                        <div class="list_item_div">
                            {!! $taskObj->task_action !!}
                        </div>
                    </div>
                    <div class="col-sm-6 file_list" style="padding-left: 0px;">
                        <h4 style="padding:10px 15px;background-color: #f9f9f9;margin-top:0px;font-weight: 500;">File Attachments</h4>
                        @if(!empty($taskObj->task_documents))
                        <ul style="list-style-type: decimal; padding-left:30px;">
                            @foreach($taskObj->task_documents as $index=>$document)
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
                        @endif
                    </div>
                </div>

                <div style="height: 10px"></div>

                <div class="row">
                    <div class="col-sm-12">
                        <div style="padding:5px 15px;background-color: #f9f9f9;margin-top:0px">
                            <h4 style="font-weight: 500;">Objective: <span class="colorOrange">{{$taskObj->objective->name}}</span></h4>
                        </div>

                    </div>
                </div>

                <div style="height: 10px"></div>
                <!--<div class="row">
                    <div class="col-sm-12" style="padding:20px 20px 10px 30px;">
                        {!! $taskObj->objective->description!!}
                    </div>
                </div>-->

                @include('forum.element.objective')

                @include('tasks.partials.task_bidders_list',['taskBidders'=>$taskBidders,'taskObj'=>$taskObj])
            </div>

        </div>
        <div class="col-md-4 col-md-pull-8">
            @include('units.partials.unit_information_left_table',['unitObj'=>$taskObj->unit,'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
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
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>
<script type="text/javascript">
    var msg_flag ='{{ $msg_flag }}';
    var msg_type ='{{ $msg_type }}';
    var msg_val ='{{ $msg_val }}';
    $(function(){
        $('#tabs').tab();

        if($(".action_list").height() >= $(".file_list").height()){
            $(".action_list").css('border-right','1px solid #ddd')
        }
        if($(".action_list").height() <= $(".file_list").height()){
            $(".file_list").css('border-left','1px solid #ddd');
        }

        $(".assign_now").on('click',function(){
            var uid = $(this).attr('data-uid');
            var tid = $(this).attr('data-tid');
            if($.trim(uid) != "" && $.trim(tid) != ""){
                $.ajax({
                    type:'get',
                    url:siteURL+'/tasks/assign',
                    data:{uid:uid,tid:tid },
                    dataType:'json',
                    success:function(resp){
                        if(resp.success){
                            showToastMessage('TASK_ASSIGN_SUCCESSFULLY');
                            window.location.reload(true);
                        }
                    }
                })
            }
        });
    })
</script>
<script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
@endsection