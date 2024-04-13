@extends('layout.default')
@section('page-css')
    <style>
        .related_para{margin:0 0 10px;}
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row form-group" style="margin-bottom: 15px;">
            @include('elements.user-menu',['page'=>'units'])
        </div>
        <div class="row form-group">
            <div class="col-md-4 left">
                <div class="site_activity_list">
                    <div class="site_activity_loading loading_dots" style="position: absolute;top:20%;left:43%;z-index: 9999;display: none;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    @include('elements.site_activities',['ajax'=>false])
                </div>
            </div>
            <div class="col-md-8">
                @if(!empty($search_word))
                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading search_unit_heading featured_unit_heading">
                            <div class="featured_unit search_unit">
                                <i class="fa fa-search "></i>
                            </div>
                            <h4>Search Results For  <span style="text-transform: none;">"{{ \Request::get('search_term') }}"</h4>
                        </div>
                    </div>

                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading">
                            <h4>UNITS</h4>
                        </div>
                        <div class="panel-body list-group">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Categories</th>
                                    <th>Location</th>
                                </tr>
                                </thead>
                                <tbody>

                                @if(!empty($unitObj) && count($unitObj) > 0)
                                    @foreach($unitObj as $unit)
                                        <?php $categories = \App\Unit::getCategoryNames($unit->category_id); ?>
                                        <tr>
                                            <td width="70%">
                                                <a href="{!! url('units/'.$unitIDHashID->encode($unit->id).'/'.$unit->slug) !!}"
                                                   class="colorLightBlue" >
                                                    {{$unit->name}}
                                                </a>
                                            </td>
                                            <td width="15%">
                                                <a href="#">{{$categories}}</a>
                                            </td>
                                            <td>
                                                @if(empty($unit->city_id) && $unit->country_id == 247)
                                                    GLOBAL
                                                @else
                                                    {{\App\City::getName($unit->city_id)}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">No unit found.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading">
                            <h4>OBJECTIVES</h4>
                        </div>
                        <div class="panel-body list-group loading_content_hide" style="position:relative;">
                            <div class="loading_dots objective_loading" style="position: absolute;top:0;left:43%;z-index: 9999;display:none;">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <table class="table table-striped objective-table">
                                <thead>
                                <tr>
                                    <th>Objective Name</th>
                                    <th class="text-center">Support</th>
                                    <th class="text-center">In progress</th>
                                    <th class="text-center">Available</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($objectivesObj) > 0)
                                    @foreach($objectivesObj as $obj)
                                        <tr>
                                            <td>
                                                <a href="{!! url('objectives/'.$objectiveIDHashID->encode($obj->id).'/'.$obj->slug) !!}" title="edit">
                                                    {{$obj->name}}
                                                </a>
                                            </td>
                                            <td  class="text-center">{{\App\Task::getTaskCount('available',$obj->id)}}</td>
                                            <td  class="text-center">{{\App\Task::getTaskCount('in-progress',$obj->id)}}</td>
                                            <td  class="text-center">{{\App\Task::getTaskCount('completed',$obj->id)}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">No record(s) found.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading">
                            <h4>TASKS</h4>
                        </div>
                        <div class="panel-body list-group loading_content_hide" style="position: relative;">
                            <div class="loading_dots task_loading" style="position: absolute;top:0%;left:43%;z-index: 9999;display: none;">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <table class="table table-striped tasks-table">
                                <thead>
                                <tr>
                                    <th>Task Name</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center"><i class="fa fa-trophy"></i></th>
                                    <th class="text-center"><i class="fa fa-clock-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($taskObj) > 0)
                                    @foreach($taskObj as $obj)
                                        <tr>
                                            <td width="60%">
                                                <a href="{!! url('tasks/'.$taskIDHashID->encode($obj->id).'/'.$obj->slug) !!}"
                                                   title="edit">
                                                    {{$obj->name}}
                                                </a>
                                            </td>
                                            <td width="20%" class="text-center">
                                                @if($obj->status == "editable")
                                                    <span class="colorLightGreen">{{\App\SiteConfigs::task_status($obj->status)}}</span>
                                                @else
                                                    <span class="colorLightGreen">{{\App\SiteConfigs::task_status($obj->status)}}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{\App\Task::getTaskCount('in-progress',$obj->id)}}</td>
                                            <td class="text-center">{{\App\Task::getTaskCount('completed',$obj->id)}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">No record(s) found.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="panel panel-grey panel-default">
                                <div class="panel-heading">
                                    <h4>Issues</h4>
                                </div>
                                <div class="panel-body list-group">
                                    <table class="table table-striped unit-table">
                                        <thead>
                                        <tr>
                                            <th>Creation Date</th>
                                            <th>Issue Title</th>
                                            <th>Unit Name</th>
                                            <th>Issue Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($issueObj) > 0 )
                                            @foreach($issueObj as $issue)

                                                <tr>
                                                    <td>{!! \App\Library\Helpers::timetostr($issue->created_at) !!}</td>
                                                    <td>
                                                        <a href="{!! url('issues/'.$issueIDHashID->encode($issue->id).'/view') !!}">
                                                            {{$issue->title}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{!! url('units/'.$unitIDHashID->encode($issue->unit_id).'/'.\App\Unit::getSlug($issue->unit_id)) !!}">
                                                            {{\App\Unit::getUnitName($issue->unit_id)}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{$issue->status}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">No record(s) found.</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading search_unit_heading featured_unit_heading">
                            <div class="featured_unit search_unit">
                                <i class="fa fa-search "></i>
                            </div>
                            <h4>Search Results</h4>
                        </div>
                        <div class="panel-body current_unit_body" style="padding:0px;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>No search term was specified</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @include('elements.footer')
@stop
@section('page-scripts')
    <script src="{!! url('assets/plugins/jquery.ThreeDots.min.js') !!}" type="text/javascript"></script>
    <script>
        $(function(){
            var the_obj = $('.text_wraps').ThreeDots({
                max_rows: 1
            });
        })
    </script>
@endsection
