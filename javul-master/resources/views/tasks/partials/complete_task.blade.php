@extends('layout.default')
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-star-rating-master/css/star-rating.css') !!}" rel="stylesheet" type="text/css" />
<style>
    .hide-native-select .btn-group, .hide-native-select .btn-group .multiselect, .hide-native-select .btn-group.multiselect-container
    {width:100% !important;}
    .smallText .time_digit{top:0px;}
    .smallText .time_text{width: auto;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',['page'=>'tasks'])
    </div>
    <div class="row form-group">
        <div class="col-md-4">
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
        <div class="col-md-8">
            <div class="panel panel-grey panel-default">
                <div class="panel-heading current_task_heading featured_unit_heading">
                    <div class="featured_unit current_task">
                        <i class="fa fa-pencil-square-o"></i>
                    </div>
                    <h4>TASK MANAGEMENT: Complete Task</h4>
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
                            <div class="col-xs-7 featured_heading" style="min-height: 156px">
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
                                    </div>
                                </div>
                                <div class="row borderBTM lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">skills</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        @if(!empty($skill_names) && count($skill_names) > 0)
                                            @foreach($skill_names as $skil)
                                                <label class="control-label form-control text-label-value">{{$skil}}</label>
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
                                        <label class="control-label">$60</label>
                                    </div>
                                </div>
                                <div class="row lnht30">
                                    <div class="col-xs-4 text-left">
                                        <label class="control-label upper">Completion</label>
                                    </div>
                                    <div class="col-xs-8 borderLFT text-left">
                                        <label class="control-label">30 days</label>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        @if(!empty($taskObj->task_documents) && count($taskObj->task_documents) > 0)
                            {{count($taskObj->task_documents)}}
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
                        @else
                            <ul style="list-style-type: none; padding-left:20px;">
                                <li>No file(s) found.</li>
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div style="padding:5px 15px;background-color: #f9f9f9;margin-top:0px">
                            <h4 style="font-weight: 500;">Objective: <span class="colorOrange">{{$taskObj->objective->name}}</span></h4>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="padding:20px 20px 10px 30px;">
                        <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            @if(!empty($taskCompleteObj) && count($taskCompleteObj) > 0)
                                <?php $i=1; ?>
                                @foreach($taskCompleteObj as $completeObj)
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <img src="{!! url('assets/images/user.png') !!}" style="border: 1px solid;height:50px;vertical-align: top;"/>
                                            <div style="display: inline-block;padding-left: 10px;">
                                                <a href="{!! url('userprofiles/'.$userIDHashID->encode($completeObj->user_id).'/'.
                                                        strtolower($completeObj->first_name.'_'.$completeObj->last_name)) !!}">
                                                    {{$completeObj->first_name.' '.$completeObj->last_name}}
                                                </a>
                                                 <span>
                                                    comments on task
                                                </span>
                                                <br/>
                                                <span class="smallText">&nbsp;({!! \App\Library\Helpers::timetostr($completeObj->created_at) !!})</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            {!! $completeObj->comments !!}
                                        </div>
                                        <?php $taskCompleteDocs = $completeObj->attachments;
                                        if(!empty($taskCompleteDocs))
                                            $taskCompleteDocs = json_decode($taskCompleteDocs);
                                        ?>

                                        @if(!empty($taskCompleteDocs) && count($taskCompleteDocs) > 0)
                                            <div class="col-sm-12" >
                                                @foreach($taskCompleteDocs as $doc)
                                                    <span>
                                                        <a href="{!! url($doc->file_path) !!}" target="_blank">
                                                            {{$doc->file_name}}
                                                        </a>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if($i <= (count($taskCompleteObj) - 1))
                                        <hr/>
                                    @endif
                                    <?php $i++; ?>
                                @endforeach
                            @endif
                            <hr/>
                            @if($authUserObj->role == "superadmin")
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label" style="margin-bottom:0px">Quality of Work</label>
                                        <div><input value="0" name="quality_of_work" type="number" class="rating_user" min=0 max=5 step=0.5
                                                    data-size="xs"></div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label" style="margin-bottom:0px">Timeliness</label>
                                        <div><input value="0" type="number" name="timeliness"  class="rating_user" min=0 max=5 step=0.5
                                                    data-size="xs" ></div>
                                    </div>
                                </div>
                                @include('tasks.partials.complete_evaluation')
                            @else
                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <div class="attachment_listing_div">
                                            <div class="table-responsive">
                                                <table class="complete_task_attachment table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Documents</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td style="width:90%;">
                                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                                <div class="form-control" data-trigger="fileinput">
                                                                    <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                                    <span class="fileinput-filename"></span>
                                                                </div>
                                                                <span class="input-group-addon btn btn-default btn-file">
                                                                    <span class="fileinput-new">Select file</span>
                                                                    <span class="fileinput-exists">Change</span>
                                                                    <input type="file" name="attachments[]">
                                                                </span>
                                                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span>
                                                                <a href="#" class="remove-row text-danger hide" >
                                                                    <i class="fa fa-remove"></i>
                                                                </a>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                                <a href="#" class="addMoreDocument">
                                                                    <i class="fa fa-plus plus"></i>
                                                                </a>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <label class="control-label">Comments</label>
                                        <textarea class="form-control summernote" name="comment" id="comment"></textarea>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12 ">
                                        <button id="create_objective" type="submit"  class="btn orange-bg">
                                            <span class="glyphicon glyphicon-ok"></span> Complete Task
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}"></script>
<script>
    var tp = '{{($authUserObj->role == "superadmin") ? true : false}}';
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
    };
    $(function(){
        $(".rating_user").rating({
            starCaptions: {
                0.5: '0.5',
                1: '1',
                1.5: '1.5',
                2: '2',
                2.5: '2.5',
                3: '3',
                3.5: '3.5',
                4: '4',
                4.5: '4.5',
                5: '5'
            }

        });
    });
</script>
<script src="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/tasks/complete_tasks.js') !!}"></script>
@endsection