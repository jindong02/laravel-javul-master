@extends('layout.default')
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
            <div class="col-sm-4">
                <div class="left" style="position: relative;">
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
            <div class="col-sm-8">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        @if(empty($areaOfInterestObj))
                            <h4>Create Area of Interest</h4>
                        @else
                            <h4>Update Area of Interest</h4>
                        @endif
                    </div>
                    <div class="panel-body list-group">
                        <div class="list-group-item">
                            <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" action="{!! url($method) !!}">
                                {!! csrf_field() !!}
                                <div class="row form-group">
                                    <div class="col-sm-4 form-group {{ $errors->has('skill_name') ? ' has-error' : '' }}">
                                        <label class="control-label">Title</label>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" name="title" value="{{ (!empty($areaOfInterestObj))? $areaOfInterestObj->title : old
                                            ('title')}}"
                                                   class="form-control"
                                                   placeholder="Title"/>
                                            @if ($errors->has('title'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-4 form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                        <label class="control-label">Parent Area of Interest</label>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <select name="parent_id" id="parent_id" class="form-control">
                                                @if(count($parent_area_of_interest) > 0)
                                                    <option value=""></option>
                                                    @foreach($parent_area_of_interest as $id=>$area_of_interest)
                                                        <option value="{{$id}}" @if(!empty($areaOfInterestObj) && $areaOfInterestObj->parent_id == $id)
                                                        selected="selected" @endif>{{$area_of_interest}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-12 ">
                                        <button id="create_category" type="submit"  class="btn black-btn">
                                            @if(!empty($areaOfInterestObj))
                                                <span class="glyphicon glyphicon-edit"></span> Update Area of Interest
                                            @else
                                                <i class="fa fa-plus plus"></i> <span class="plus_text">Create Area of Interest</span>
                                            @endif
                                        </button>

                                    </div>
                                </div>
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
    <script>
        var editTask = '{{(!empty($issueObj)?true:false)}}';
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
    <script src="{!! url('assets/js/admin/area_of_interest.js') !!}"></script>
@endsection