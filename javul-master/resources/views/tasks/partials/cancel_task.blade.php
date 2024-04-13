@extends('layout.default')
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />

<style>
    .hide-native-select .btn-group, .hide-native-select .btn-group .multiselect, .hide-native-select .btn-group.multiselect-container
    {width:100% !important;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        @include('elements.user-menu',['page'=>'tasks'])
    </div>
    <div class="row form-group margin-top-15">
        <div class="col-sm-12 ">
            <div class="col-sm-6 grey-bg unit_grey_screen_height">
                <h1 class="unit-heading create_unit_heading">
                    <span class="glyphicon glyphicon-list-alt"></span>
                    Cancel Task
                </h1>
            <span style="font-size: 14px;padding-left:50px">
                <b>{{$taskObj->name}}</b>
            </span><br /><br />
            </div>
            @include('tasks.partials.task_information')
        </div>
    </div>
    <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
        {!! csrf_field() !!}
        @if(!empty($taskCancelObj) && count($taskCancelObj) > 0)
            <?php $i=1; ?>
            @foreach($taskCancelObj as $cancelObj)
                <div class="row">
                    <div class="col-sm-12">
                        <img src="{!! url('assets/images/user.png') !!}" style="border: 1px solid;height:50px;vertical-align: top;"/>
                        <div style="display: inline-block;padding-left: 10px;">
                            <a href="{!! url('userprofiles/'.$userIDHashID->encode($cancelObj->user_id).'/'.
                                                                strtolower($cancelObj->first_name.'_'.$cancelObj->last_name)) !!}">
                                {{$cancelObj->first_name.' '.$cancelObj->last_name}}
                            </a>
                                     <span>
                                        comments on task
                                    </span>
                            <br/>
                            <span class="smallText">&nbsp;({{\App\Library\Helpers::timetostr($cancelObj->created_at)}})</span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        {!! $cancelObj->comments !!}
                    </div>
                </div>
                @if($i <= (count($taskCancelObj) - 1))
                    <hr/>
                @endif
            <?php $i++; ?>
            @endforeach
        @endif
        <hr/>
        @if($authUserObj->role == "superadmin")
            @include('tasks.partials.cancel_task_superadmin')
        @else
            <div class="row">
                <div class="col-sm-12 form-group">
                    <label class="control-label">Comments</label>
                    <textarea class="form-control summernote" name="comment" id="comment"></textarea>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12 ">
                    <button id="cancel_task" type="submit"  class="btn btn-danger">
                        <span class="glyphicon glyphicon-cancel"></span> Cancel Task
                    </button>
                </div>
            </div>
        @endif

    </form>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
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
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/tasks/cancel_task.js') !!}"></script>
@endsection