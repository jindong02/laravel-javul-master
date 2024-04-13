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
                @include('units.partials.unit_information_left_table',['unitObj'=>$objectiveObj->unit,'availableFunds'=>0,'awardedFunds'=>0])
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
                        <h4>OBJECTIVES</h4>
                    </div>
                    <div class="panel-body list-group loading_content_hide">
                        <div class="loading_dots objective_loading" style="position: absolute;top:0;left:43%;z-index: 9999;display:none;">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <table class="table table-striped objective-table">
                            <thead>
                            <tr>
                                <th>Objective Name</th>
                                <th>Support</th>
                                <th>In progress</th>
                                <th>Available</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($objectiveObj) > 0)
                                @foreach($objectiveObj as $obj)
                                    <tr>
                                        <td>
                                            <a href="{!! url('objectives/'.$objectiveIDHashID->encode($obj->id).'/'.$obj->slug) !!}"
                                               title="edit">
                                                {{$obj->name}}
                                            </a>
                                        </td>
                                        <td>{{\App\Task::getTaskCount('available',$obj->id)}}</td>
                                        <td>{{\App\Task::getTaskCount('in-progress',$obj->id)}}</td>
                                        <td>{{\App\Task::getTaskCount('completed',$obj->id)}}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">No record(s) found.</td>
                                </tr>
                            @endif

                            <tr style="background-color: #fff;text-align: right;">
                                <td colspan="5">
                                    <a class="btn black-btn" id="add_objective_btn" href="{!! url('objectives/'.$unitIDHashID->encode($unit_activity_id).'/add') !!}">
                                        <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_objective') !!}</span>
                                    </a>

                                    @if($objectiveObj->lastPage() > 1 && $objectiveObj->lastPage() != $objectiveObj->currentPage())
                                        <a href="#" data-url="{{$objectiveObj->url($objectiveObj->currentPage()+1) }}" data-unit_id="{{$unitIDHashID->encode($unit_activity_id)}}" class="btn
                                    more-black-btn more-objectives" data-from_page="unit_view" type="button">
                                            MORE OBJECTIVES <span class="more_dots">...</span>
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
@section('page-scripts')
@section('page-scripts')
    <script>
        $(function(){
            $(".unit_description").css("min-height",($(".both-div").height())+10+'px');
        })
    </script>
@endsection
