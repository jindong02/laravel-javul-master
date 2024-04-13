@extends('layout.default')
@section('page-css')
    <link href="{!! url('assets/plugins/select2/css/select2-bootstrap.min.css') !!}" rel="stylesheet" type="text/css" />
    <style>
        .hierarchy{margin-left:10px;margin-top: 10px;}
    </style>
@endsection
@section('content')

    <div class="container">
        <div class="row form-group" style="margin-bottom: 15px;">
            @include('elements.user-menu',array('page'=>'home'))
        </div>
        <div class="row form-group" >
            <div class="col-md-4">
                <div class="left">
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
                <div class="row form-group">
                    @if(Auth::user()->role == "superadmin")
                        <div class="col-sm-12">
                            <div class="panel panel-grey panel-default">
                                <div class="panel-heading">
                                    Featured Unit
                                </div>
                                <div class="panel-body list-group">
                                    <div class="list-group-item col-xs-6" style="padding: 7px 10px;border:0px;">
                                        <label class="control-label">Search unit by name or category</label>
                                        <select name="featured_unit" class="form-control" id="featured_unit" multiple="multiple">
                                            @if(count($unitList) > 0 && !empty($unitList))
                                                @foreach($unitList as $unit)
                                                    <option value="{{$unitIDHashID->encode($unit->id)}}" @if($unit->featured_unit == 1)
                                                    selected="selected" @endif>{{$unit->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    {{--<div class="list-group-item col-xs-6" style="padding: 7px 10px;border:0px;">
                                        <label class="control-label">Currently Featured Unit:</label>
                                        @if(!empty($featuredUnit))
                                            <input type="text" name="unit" class="form-control featured_unit_admin"
                                                   disabled value="{{$featuredUnit->name}}">
                                        @else
                                            <input type="text" name="unit" class="form-control featured_unit_admin" value="-" disabled>
                                        @endif
                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="panel panel-grey panel-default">
                            <div class="panel-heading">
                                <h4>Job Skills</h4>
                            </div>
                            <div class="panel-body table-inner loading_content_hide list-group ">
                                @if(!empty($authUserObj) && $authUserObj->role == "superadmin" && !empty($need_approve_skills) && count($need_approve_skills) > 0)
                                    <div class="row form-group skill-approve-panel">
                                        <div class="col-sm-6">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Skill Name</th>
                                                    <th>Status</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($need_approve_skills as $p_skill)
                                                    <tr>
                                                        <td>
                                                            @if($p_skill->action_type == "delete")
                                                                {{\App\JobSkill::getName($p_skill->job_skill_id)}}
                                                            @else
                                                                {{$p_skill->skill_name}}
                                                            @endif
                                                        </td>
                                                        <td>{{ucfirst($p_skill->action_type)}}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-xs btn-success mark-skill-approve"
                                                               data-id="{{$p_skill->prefix_id}}">Mark as
                                                                Approve</a>

                                                            <a href="#" class="btn btn-xs btn-danger discard-change"
                                                               data-id="{{$p_skill->prefix_id}}">Discard</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        @include('admin.partials.skill_browse',['from'=>'site_admin'])
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12">
                        <div class="panel panel-grey panel-default ">
                            <div class="panel-heading">
                                <h4>Unit Categories</h4>
                            </div>
                            <div class="panel-body table-inner loading_content_hide list-group ">
                                @if(!empty($authUserObj) && $authUserObj->role == "superadmin" && !empty($need_approve_categories) && count($need_approve_categories) > 0)
                                    <div class="row form-group skill-approve-panel">
                                        <div class="col-sm-6">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Category Name</th>
                                                    <th>Status</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($need_approve_categories as $p_category)
                                                    <tr>
                                                        <td>
                                                            @if($p_category->action_type == "delete")
                                                                {{\App\UnitCategory::getName($p_category->unit_category_id)}}
                                                            @else
                                                                {{$p_category->name}}
                                                            @endif
                                                        </td>
                                                        <td>{{ucfirst($p_category->action_type)}}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-xs btn-success mark-category-approve"
                                                               data-id="{{$p_category->prefix_id}}">Mark as
                                                                Approve</a>

                                                            <a href="#" class="btn btn-xs btn-danger discard-category-change"
                                                               data-id="{{$p_category->prefix_id}}">Discard</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        @include('admin.partials.unit_category_browse',['from'=>'site_admin'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-grey panel-default ">
                            <div class="panel-heading">
                                <h4>Area of interest</h4>
                            </div>
                            <div class="panel-body table-inner loading_content_hide list-group ">
                                @if(!empty($authUserObj) && $authUserObj->role == "superadmin" && !empty($need_approve_areaOfInterest) && count($need_approve_areaOfInterest) > 0)
                                    <div class="row form-group skill-approve-panel">
                                        <div class="col-sm-6">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Area Of Interest</th>
                                                    <th>Status</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($need_approve_areaOfInterest as $areaOfInterest)
                                                    <tr>
                                                        <td>
                                                            @if($areaOfInterest->action_type == "delete")
                                                                {{\App\AreaOfInterest::getName($areaOfInterest->area_of_interest_id)}}
                                                            @else
                                                                {{$areaOfInterest->title}}
                                                            @endif
                                                        </td>
                                                        <td>{{ucfirst($areaOfInterest->action_type)}}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-xs btn-success mark-area-of-interest-approve"
                                                               data-id="{{$areaOfInterest->prefix_id}}">Mark as
                                                                Approve</a>

                                                            <a href="#" class="btn btn-xs btn-danger discard-area-of-interest-change"
                                                               data-id="{{$areaOfInterest->prefix_id}}">Discard</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        @include('admin.partials.area_of_interest_browse',['from'=>'site_admin'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('elements.footer')
@endsection
@section('page-scripts')
    <script type="text/javascript">
        var msg_flag ='{{ $msg_flag }}';
        var msg_type ='{{ $msg_type }}';
        var msg_val ='{{ $msg_val }}';
        var _token = '{{csrf_token()}}';
        var page='site_admin';
    </script>
    <script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/admin/site_admin.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/admin/skill_browse.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/admin/category_browse.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/admin/category_op.js') !!}" type="text/javascript"></script>

    <script src="{!! url('assets/js/admin/area_of_interest_browse.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/admin/area_of_interest_op.js') !!}" type="text/javascript"></script>
@endsection