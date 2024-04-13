@extends('layout.default')
@section('page-meta')
<title>@if(empty($taskObj)) Create Task @else Update Task @endif - Javul.org</title>
@endsection
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<!-- <link href="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css') !!}" rel="stylesheet" type="text/css" /> -->
<link src="{!! url('assets/plugins/kartik-bootstrap-fileinput/themes/explorer-fa/theme.css') !!}" />
<link href="{!! url('assets/plugins/kartik-bootstrap-fileinput/css/fileinput.min.css') !!}" media="all" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/select2/css/select2-bootstrap.min.css') !!}" rel="stylesheet" type="text/css" />

<style>
    .hide-native-select .btn-group, .hide-native-select .btn-group .multiselect, .hide-native-select .btn-group.multiselect-container
    {width:100% !important;}
    .select2-container--bootstrap .select2-results > .select2-results__options{max-height: 200px;overflow-y:auto}
    .option.load-more{padding:5px;}
    .hierarchy_parent{margin-left: 10px;}
    .add_edit_skills div:first-child{margin-top:0px;padding-left:5px;padding-right:5px;}
    .new_box,#skill_firstbox{min-width: 12.5em;width:100%;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',['page'=>'tasks'])
    </div>
    {{--<div class="row form-group">
        <div class="col-sm-12 ">
            <div class="col-sm-6 grey-bg unit_grey_screen_height">
                <h1 class="unit-heading create_unit_heading">
                    <span class="glyphicon glyphicon-list-alt"></span>
                    @if(empty($taskObj))
                    Create Task
                    @else
                    Update Task
                    @endif
                </h1><br /><br />
            </div>
            @include('tasks.partials.task_information')
        </div>
    </div>--}}
    <div class="row">
        <div @if(!empty($unitInfo) || !empty($taskObj)) class="col-md-8 col-md-push-4" @else class="col-sm-12" @endif>
            <div class="panel panel-grey panel-default">
                <div class="panel-heading">
                    @if(empty($taskObj))
                        <h4>Create Task</h4>
                    @else
                        <h4>Update Task</h4>
                    @endif
                </div>
                <div class="panel-body list-group">
                    <div class="list-group-item">
                        <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-sm-4 form-group {{ $errors->has('unit') ? ' has-error' : '' }}" @if(!empty($unitInfo))
                                style="display:none;" @endif>
                                    <label class="control-label">Select Unit</label>
                                    <div class="input-icon right">
                                        <i class="fa select-error"></i>
                                        @if(!empty($unitInfo) && empty($task_objective_id))
                                            <input type="hidden" name="unit" value="{{$unitIDHashID->encode($unitInfo->id)}}"/>
                                        @else
                                            <select name="unit" id="unit" class="form-control">
                                                <option value="">Select</option>
                                                @if(count($unitsObj) > 0)
                                                    @foreach($unitsObj as $unit_id=>$unit)
                                                        <option value="{{$unitIDHashID->encode($unit_id)}}"
                                                                @if(!empty($taskObj) && $taskObj->unit_id == $unit_id)
                                                                selected=selected
                                                                @elseif(empty($taskObj) && $unit_id == $task_unit_id)
                                                                selected=selected
                                                                @endif>{{$unit}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @endif
                                        @if ($errors->has('unit'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('unit') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4 form-group {{ $errors->has('objective') ? ' has-error' : '' }}">
                                    <label class="control-label">Select Objective</label>
                                    <div class="input-icon right">
                                        <i class="fa select-error"></i>
                                        <select @if(!empty($unitInfo) && !empty($task_objective_id)) name="objective_disabled" @else name="objective" @endif id="objective"
                                                class="form-control">
                                            <option value="">Select</option>
                                            @if(count($objectiveObj) > 0)
                                                @foreach($objectiveObj as $objective)
                                                    <option value="{{$objectiveIDHashID->encode($objective->id)}}"
                                                            @if(!empty($taskObj) && $objective->id == $taskObj->objective_id)
                                                            selected=selected
                                                            @elseif(empty($taskObj) && $objective->id == $task_objective_id)
                                                            selected=selected
                                                            @endif>{{$objective->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if(!empty($unitInfo) && !empty($task_objective_id))
                                            <input type="hidden" name="objective" value="{{$objectiveIDHashID->encode($task_objective_id)}}"/>
                                        @endif
                                        <span class="objective_loader location_loader" style="display: none">
                                            <img src="{!! url('assets/images/small_loader.gif') !!}"/>
                                        </span>
                                        @if ($errors->has('objective'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('objective') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4 form-group {{ $errors->has('task_name') ? ' has-error' : '' }}">
                                    <label class="control-label">Task Name</label>
                                    <div class="input-icon right">
                                        <i class="fa"></i>
                                        <input type="text" name="task_name" id="task_name" value="{{ (!empty($taskObj))? $taskObj->name : old('task_name') }}"
                                               class="form-control"
                                               placeholder="Task Name"/>
                                        @if ($errors->has('task_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('task_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-8 form-group {{ $errors->has('task_skills') ? ' has-error' : '' }}">
                                    <label class="control-label">Task Skills</label>
                                    <div class="input-icon right">
                                        <i class="fa select-error"></i>
                                        <div class="input-group">
                                            <select name="task_skills[]" class="form-control" id="task_skills" multiple="multiple">
                                                <option value="">Select</option>
                                                @if(!empty($task_skills))
                                                    @foreach($task_skills as $skill_id=>$skill)
                                                        <option value="{{$skill_id}}" @if(!empty($exploded_task_list) && in_array($skill_id,
                                                                $exploded_task_list)) selected=selected @endif>{{$skill}}</option>
                                                    @endforeach
                                                @endif
                                            </select>

                                            <a href="" class="browse-skills input-group-addon" style="border-radius: 0px;
                                                color:#333;text-decoration: none;">Browse</a>

                                        </div>
                                        @if ($errors->has('task_skills'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('task_skills') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-4 form-group {{ $errors->has('estimated_completion_time_start') ? ' has-error' : '' }}">
                                    <label class="control-label">Estimated Completion Time From</label>
                                    <div class="input-group date" id='datetimepicker1'>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" id="estimated_completion_time_start" name="estimated_completion_time_start" value="{{ (!empty($taskObj))?
                                                    $taskObj->estimated_completion_time_start :
                                                    old('estimated_completion_time_start') }}" class="form-control" placeholder="Estimated Completion Time From"/>
                                        </div>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        @if ($errors->has('estimated_completion_time_start'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('estimated_completion_time_start') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4 form-group {{ $errors->has('estimated_completion_time_end') ? ' has-error' : '' }}">
                                    <label class="control-label">Estimated Completion Time To</label>
                                    <div class="input-group date" id='datetimepicker2'>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" id="estimated_completion_time_end" name="estimated_completion_time_end" value="{{ (!empty($taskObj))?
                                                    $taskObj->estimated_completion_time_end :
                                                    old('estimated_completion_time_end') }}" class="form-control" placeholder="Estimated Completion Time To"/>
                                        </div>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        @if ($errors->has('estimated_completion_time_end'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('estimated_completion_time_end') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!--<div class="col-sm-4 form-group">
                                        <label class="control-label">Assigned To</label>
                                        <div class="input-icon right">
                                            <i class="fa select-error"></i>
                                            <select name="assigned_to" class="form-control" id="assigned_to">
                                                <option value="">Select</option>
                                                {{--if(!empty($assigned_toUsers))
                                                        @foreach($assigned_toUsers as $user_id=>$user_name)
                                                                <option value="{{$userIDHashID->encode($user_id)}}">{{$user_name}}</option>
                                                @endforeach
                                                        @endif--}}
                                        </select>
                                    </div>
                                </div>-->
                                <div class="col-sm-4 form-group">
                                    <label class="control-label">Compensation</label>
                                    <div class="input-group">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" id="compensation" name="compensation" value="{{ (!empty($taskObj))? $taskObj->compensation : old('compensation') }}"
                                                   class="form-control border-radius-0 onlyDigits"
                                                   placeholder="Compensation"/>
                                        </div>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-usd"></span>
                                        </span>
                                    </div>
                                </div>
                                @if(!empty($taskObj))
                                    <div class="col-sm-4 form-group">
                                        <label class="control-label">Status</label>
                                        <div class="input-icon right">
                                            @if(!empty($change_task_status) || \App\Task::isUnitAdminOfTask($taskObj->id))
                                                <select name="task_status" class="form-control" id="task_status">
                                                    @foreach(\App\SiteConfigs::task_status() as $index=>$status)
                                                        <option @if($taskObj->status == $index) selected=selected @endif value="{{$index}}">{{ $status }}</option>                                                        
                                                    @endforeach
                                                </select>
                                            @else
                                            <!--<span class="label label-default" style="line-height: 33px;padding:7px 6px;">-->
                                            <span>
                                                {{\App\SiteConfigs::task_status($taskObj->status)}}
                                                        <!--</span>-->
                                                @if($taskObj->status == "editable" && !empty($taskEditor) && $taskEditor->submit_for_approval == "not_submitted")
                                                    @if(count($otherEditorsDone) > 0)
                                                        ({{count($otherEditorsDone).' task editor submitted this task for Approval'}}
                                                        @if(!empty($availableDays))
                                                            {{"Time left for editing: ".$availableDays." days."}})
                                                        @endif
                                                    @endif
                                                    (<a href="#" class="submit_for_approval"  data-task_id="{{$taskIDHashID->encode($taskObj->id)}}">Submit for Approval</a>)

                                                @elseif($taskObj->status == "editable" && count($taskEditor) > 0 && $taskEditor->submit_for_approval == "submitted")
                                                    ( You changed this task status to "Awaiting Approval". Waiting for {{count($otherRemainEditors)}}
                                                    other editors to do the same)
                                                @endif
                                            @endif
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Summary</label>
                                    <textarea class="form-control" id="taskSummary" name="summary">@if(!empty($taskObj)) {{$taskObj->summary}} @endif</textarea>
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Description <span id="desc-error"></span></label>
                                    <textarea class="form-control summernote" id="description" name="description">@if(!empty($taskObj)) {{$taskObj->description}} @endif</textarea>
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Action Items</label>
                                    <textarea class="form-control" name="action_items" id="action_items">
                                        @if(!empty($taskObj))
                                            {!! $taskObj->task_action !!}
                                        @endif
                                    </textarea>
                                    <!-- insert each task action into task_actions table. -->
                                    <!--<div class="all_action_items">
                                        <input type="hidden" name="action_items_array[]" id="action_items_array" class="action_items_class"/>
                                    </div>-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <div class="document_listing_div">
                                        <div class="table-responsive ofhidden">
                                            <table class="documents table table-striped">
                                                <thead>
                                                <tr>
                                                    <th style="border:0px;font-weight:normal;">Documents</th>
                                                    <th style="border:0px;"></th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @if(!empty($taskDocumentsObj))
                                                    <?php $i=1; ?>
                                                    @foreach($taskDocumentsObj as $document)
                                                        @include('tasks.partials.task_document_listing',['document'=>$document,'taskObj'=>$taskObj,'taskDocumentIDHashID'=>$taskDocumentIDHashID,'fromEdit'=>'no'])
                                                    @endforeach
                                                    @if(empty($taskObj) || ($taskObj->status == "editable"))
                                                        @include('tasks.partials.document_upload')
                                                    @endif
                                                @else
                                                    @if(empty($taskObj) || ($taskObj->status == "editable"))
                                                        @include('tasks.partials.document_upload')
                                                    @endif
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Comment</label>
                                    <input class="form-control" name="comment">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-12 ">

                                    <button id="create_objective" type="submit"  @if(!empty($taskObj) && ($taskObj->status !="editable"))
                                    class="btn" disabled="disabled" style="background-color:#e1672c;"@else class="btn black-btn" @endif>
                                        @if(!empty($taskObj))
                                            <span class="glyphicon glyphicon-edit"></span> Update Task
                                        @else
                                            <i class="fa fa-plus plus"></i> <span class="plus_text">Create Task</span>
                                        @endif
                                    </button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($unitInfo) || !empty($taskObj))
            <div class="col-md-4 col-md-pull-8">
                @include('units.partials.unit_information_left_table',['unitObj'=>(!empty($taskObj)?$unitObjForLeftBar:$unitInfo),'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
            </div>
        @endif
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script>
    var editTask = '{{$editFlag}}';
    var actionListFlag = "<?=$actionListFlag?>";
    var from_unit = '{{(!empty($unitInfo) && !empty($task_objective_id) ?true:false)}}';
    var page='task';
    var browse_skill_box='';
    var selected_skill_id= new Array();
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
<script src="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.js') !!}" type="text/javascript"></script>
<!-- <script src="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js') !!}" type="text/javascript"></script> -->
<script src="{!! url('assets/plugins/kartik-bootstrap-fileinput/js/fileinput.min.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/kartik-bootstrap-fileinput/js/plugins/piexif.min.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/kartik-bootstrap-fileinput/themes/explorer-fa/theme.js') !!}" type="text/javascript"></script>

<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/admin/skill_browse.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/tasks/tasks.js') !!}"></script>
<script>
    $(function(){
        var uploaded_files = '{!!(!is_array($taskDocumentsObj))?$taskDocumentsObj:''!!}';
        if($.trim(uploaded_files) !== '')
            uploaded_files = JSON.parse(uploaded_files);
        var max_file_allow = 10;
        if(uploaded_files && uploaded_files.length > 0)
            max_file_allow = max_file_allow - uploaded_files.length;

        $(".file_input").fileinput({
            'theme': 'explorer-fa',
            maxFileCount: 10,
            validateInitialCount: true,
            overwriteInitial: false,
            showClose: true,
            showCaption: true,
            showBrowse: true,
            browseOnZoneClick: true,
            removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
            showRemove:false,
            showUpload:false,
            removeTitle: 'Cancel or reset changes',
            elErrorContainer: '#kv-error-2',
            msgErrorClass: 'alert alert-block alert-danger',
            uploadAsync: false,
            uploadUrl: window.location.href, // your upload server url
            uploadExtraData:{_token:'{{csrf_token()}}'},
            fileActionSettings : {'showUpload':false},
            allowedFileExtensions: ["doc","docx","pdf","txt","jpg","png","ppt","pptx","jpeg","doc","xls","xlsx"],
            dropZoneEnabled: false
        });
    });
</script>
@endsection