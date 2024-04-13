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
            <div class="col-md-4">
                @include('units.partials.unit_information_left_table',['unitObj'=>$taskObj->unit,'availableFunds'=>0,'awardedFunds'=>0])
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

                            <tr style="background-color: #fff;text-align: right;">
                                <td colspan="5">
                                    <a class="btn black-btn" id="add_objective_btn" href="{!! url('tasks/add?unit='.$unit_id_encoded) !!}">
                                        <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_task') !!}</span>
                                    </a>

                                    @if($taskObj->lastPage() > 1 && $taskObj->lastPage() != $taskObj->currentPage())
                                        <a href="#" data-url="{{$taskObj->url($taskObj->currentPage()+1) }}" data-unit_id="{{$unitIDHashID->encode($unit_activity_id)}}"
                                           class="btn more-black-btn more-tasks" data-from_page="unit_view" type="button">
                                            MORE TASKS <span class="more_dots">...</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('elements.footer')
@stop
