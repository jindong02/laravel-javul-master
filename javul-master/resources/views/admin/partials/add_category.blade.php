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
                        @if(empty($categoryObj))
                            <h4>Create Unit Category</h4>
                        @else
                            <h4>Update Unit Category</h4>
                        @endif
                    </div>
                    <div class="panel-body list-group">
                        <div class="list-group-item">
                            <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" action="{!! url($method) !!}">
                                {!! csrf_field() !!}
                                <div class="row form-group">
                                    <div class="col-sm-4 form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label class="control-label">Category Name</label>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" name="name" value="{{ (!empty($categoryObj))? $categoryObj->name : old('name')}}"
                                                   class="form-control"
                                                   placeholder="Category Name"/>
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($authUserObj->role == "superadmin")
                                    <div class="col-sm-4 form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                                        <label class="control-label">Status</label>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <select name="status" class="form-control">
                                                <option value="pending" @if(!empty($categoryObj) && $categoryObj->status == "pending")
                                                selected @endif
                                                    >Pending</option>
                                                <option value="approved" @if(!empty($categoryObj) && $categoryObj->status == "approved")
                                                selected @endif>Approved</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-sm-4 form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                        <label class="control-label">Parent Category</label>
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <select name="parent_id" id="parent_id" class="form-control">
                                                @if(count($parent_categories) > 0)
                                                    <option value=""></option>
                                                    @foreach($parent_categories as $id=>$p_category)
                                                        <option value="{{$id}}" @if(!empty($categoryObj) && $categoryObj->parent_id ==
                                                        $id) selected="selected" @endif>{{$p_category}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-12 ">
                                        <button id="create_category" type="submit"  class="btn black-btn">
                                            @if(!empty($categoryObj))
                                                <span class="glyphicon glyphicon-edit"></span> Update Category
                                            @else
                                                <i class="fa fa-plus plus"></i> <span class="plus_text">Create Category</span>
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
        var editTask = '{{(!empty($categoryObj)?true:false)}}';
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
    <script src="{!! url('assets/js/admin/categories.js') !!}"></script>
@endsection