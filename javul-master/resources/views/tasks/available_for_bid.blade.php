@extends('layout.default')
@section('page-meta')
<title>Task Bidding - Javul.org</title>
@endsection
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
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
    @include('elements.user-menu',['page'=>'units'])
</div>
<div class="row form-group">
    <div class="col-sm-12 ">
        <div class="col-sm-6 grey-bg unit_grey_screen_height">
            <h1 class="unit-heading create_unit_heading">
                <span class="glyphicon glyphicon-list-alt"></span>
                Task Bidding
            </h1><br /><br />
        </div>
        @include('tasks.partials.task_information')
    </div>
</div>
<form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-default panel-dark-grey">
                <div class="panel-heading">
                    <h4>Tasks</h4>
                </div>
                <div class="panel-body table-inner table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Objective Name</th>
                            <th>Unit Name</th>
                            <th>Skills</th>
                            <th>Assigned to</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($taskObj) > 0 )
                            @foreach($taskObj as $task)
                                @include('tasks.partials.task_listing',['task'=>$task])
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">No record(s) found.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            @include('elements.site_activities',['ajax'=>false])
        </div>
    </div>
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
<script src="{!! url('assets/js/tasks/task_bid.js') !!}"></script>
@endsection