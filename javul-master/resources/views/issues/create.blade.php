@extends('layout.default')
@section('page-meta')
<title>@if(empty($issueObj)) Create Issue @else Update Issue @endif - Javul.org</title>
@endsection
@section('page-css')
    <link href="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
    <!-- <link href="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css') !!}" rel="stylesheet" type="text/css" /> -->
    <link src="{!! url('assets/plugins/kartik-bootstrap-fileinput/themes/explorer-fa/theme.css') !!}" />
    <link href="{!! url('assets/plugins/kartik-bootstrap-fileinput/css/fileinput.min.css') !!}" media="all" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />

    <style>
        .hide-native-select .btn-group, .hide-native-select .btn-group .multiselect, .hide-native-select .btn-group.multiselect-container
        {width:100% !important;}
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
            <div class="@if(empty($site_activity)) col-sm-12 @else col-md-8 col-md-push-4 @endif">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        @if(empty($issueObj))
                            <h4>Create Issue</h4>
                        @else
                            <h4>Update Issue</h4>
                        @endif
                    </div>
                    <div class="panel-body list-group">
                        <div class="list-group-item">
                            <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data"
                                  @if(!empty($site_activity)) action="{!! url('issues/'.\Request::segment(2).'/'.$action_method) !!}"
                                  @else action="{!! url('issues/'.$action_method) !!}" @endif>
                                {!! csrf_field() !!}
                                <div class="row">
                                    @if(!empty($site_activity))
                                    <input type="hidden" name="unit" value="{{$unitIDHashID->encode($unitObj->id)}}"/>
                                    @endif
                                    <div class="col-sm-4 form-group {{ $errors->has('issue_title') ? ' has-error' : '' }}">
                                        <label class="control-label">Issue Title</label>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" name="title" value="{{ (!empty($issueObj))? $issueObj->title : old('title') }}"
                                                   class="form-control"
                                                   placeholder="Issue Name"/>
                                            @if ($errors->has('title'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if(empty($site_activity))
                                        <div class="col-sm-4 form-group">
                                            <label class="control-label">Select Unit</label>
                                            <div class="input-icon right">
                                                <i class="fa select-error"></i>
                                                <select name="unit_id" id="unit_id" class="form-control">
                                                    <option value="">Select</option>
                                                    @if(count($unitObj) > 0)
                                                        @foreach($unitObj as $unit)
                                                            <option value="{{$unitIDHashID->encode($unit->id)}}"
                                                                    @if(!empty($issueObj) && $unit->id == $issueObj->unit_id)
                                                                    selected=selected
                                                                    @endif>{{$unit->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-sm-4 form-group">
                                        <label class="control-label">Select Objective</label>
                                        <div class="input-icon right">
                                            <i class="fa select-error"></i>
                                            <select name="objective_id" id="objective_id" class="form-control">
                                                <option value="">Select</option>
                                                @if(count($objectiveObj) > 0)
                                                    @foreach($objectiveObj as $objective)
                                                        <option value="{{$objectiveIDHashID->encode($objective->id)}}"
                                                                @if(!empty($issueObj) && $objective->id == $issueObj->objective_id)
                                                                selected=selected
                                                                @endif>{{$objective->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    @if(!empty($issueObj) && $user_can_change_status)
                                        <div class="col-sm-4 form-group">
                                            <label class="control-label">Select Status</label>
                                            <div class="input-icon right">
                                                <i class="fa select-error"></i>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="unverified" @if(!empty($issueObj) &&
                                                $issueObj->status=="unverified") selected="selected" @endif>Unverified</option>
                                                    <option value="verified" @if(!empty($issueObj) &&
                                                $issueObj->status=="verified") selected="selected" @endif>Verified</option>
                                                    <option value="resolved" @if(!empty($issueObj) &&
                                                $issueObj->status=="resolved") selected="selected" @endif>Resolved</option>

                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <label class="control-label">Select Task</label>
                                        <div class="input-icon right">
                                            <i class="fa select-error"></i>
                                            <select name="task_id[]" id="task_id" class="form-control" multiple>
                                                <option value="">Select</option>
                                                @if(!empty($taskObj))
                                                    <?php $task_ids = explode(",",$issueObj->task_id); ?>
                                                    @foreach($taskObj as $task)
                                                        <option value="{{$taskIDHashID->encode($task->id)}}" @if(in_array($task->id,
                                                        $task_ids)) selected @endif>{{$task->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <div class="document_listing_div">
                                            <div class="table-responsive">
                                                <table class="documents table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th style="border:0px;font-weight:normal;">Documents</th>
                                                        <th style="border:0px;"></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @if(!empty($issueDocumentsObj))
                                                        <?php $i=1; ?>
                                                        @foreach($issueDocumentsObj as $document)
                                                            @include('issues.partials.issue_document_listing',['document'=>$document,'issueObj'=>$issueObj,'fromEdit'=>'no'])
                                                        @endforeach
                                                        @if($issueObj->status != "resolved")
                                                            @include('tasks.partials.document_upload')
                                                        @endif
                                                    @else
                                                        @include('tasks.partials.document_upload')
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <label class="control-label">Description</label>
                                        <textarea class="form-control summernote" name="description">@if(!empty($issueObj)) {{$issueObj->description}} @endif</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <label class="control-label">Resolution</label>
                                        <textarea class="form-control summernote_resolution" name="resolution">@if(!empty($issueObj))
                                                {{$issueObj->resolution}} @endif</textarea>
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
                                        <button id="create_objective" type="submit"  class="btn black-btn">
                                            @if(!empty($issueObj))
                                                <span class="glyphicon glyphicon-edit"></span> Update Issue
                                            @else
                                                <i class="fa fa-plus plus"></i> <span class="plus_text">Create Issue</span>
                                            @endif
                                        </button>

                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @if(!empty($site_activity))
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
            @endif
        </div>
    </div>
    @include('elements.footer')
@stop
@section('page-scripts')
    <script>

        var editTask = '{{(!empty($issueObj)?true:false)}}';
        var can_res = '{{$user_can_resolve_issue}}';
        var can_chnge_status = '{{$user_can_change_status}}';
        var issue_status = '{{(!empty($issueObj)?$issueObj->status:false)}}';
        var csrf_token = '{{csrf_token()}}';
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
    <script src="{!! url('assets/js/issues/issues.js') !!}"></script>
    <script>
        $(function(){
            $(".file_input").fileinput({
                'theme': 'explorer-fa',
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
                dropZoneEnabled: false,
            });
        });
    </script>
@endsection
