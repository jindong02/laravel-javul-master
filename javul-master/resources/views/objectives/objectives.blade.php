@extends('layout.default')
@section('page-meta')
<title>Objectives - Javul.org</title>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'objectives'])
    </div>
    <div class="row form-group">
        <div class="col-md-8 col-md-push-4">
            <div class="panel panel-grey panel-default">
                <div class="panel-heading">
                    <h4>{!! trans('messages.objectives') !!}</h4>
                </div>
                <div class="panel-body table-inner table-responsive loading_content_hide">
                    <div class="loading_dots objective_loading" style="position: absolute;top:20%;left:43%;z-index: 9999;display:none;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <table class="table table-striped objective-table">
                        <thead>
                        <tr>
                            <th>Objective Name</th>
                            <th>Unit Name</th>
                            <th>Created By</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($objectives) > 0 )
                        @foreach($objectives as $objective)
                        <?php $unitslug =\App\Models\Unit::getSlug($objective->unit_id); ?>
                        <tr>
                            <td><a href="{!! url('objectives/'.$objectiveIDHashID->encode($objective->id).'/'.$objective->slug)!!}">{{$objective->name}}</a></td>
                            <td><a href="{!! url('units/'.$unitIDHashID->encode($objective->unit_id).'/'.$unitslug )!!}">{{$objective->unit_name}}</a></td>
                            <td><a href="{!! url('userprofiles/'.$userIDHashID->encode($objective->user_id).'/'.strtolower
                            ($objective->first_name.'_'.$objective->last_name))!!}">
                                    {{$objective->first_name.' '.$objective->last_name}}
                                </a></td>
                            <td>{{ \App\Models\Objective::objectiveStatus()[$objective->status]}}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">No record(s) found.</td>
                        </tr>
                        @endif
                        <tr style="background-color: #fff;text-align: right;">
                            <td colspan="5" >
                                <a href="{!! url('objectives/add')!!}"class="btn black-btn" id="add_objective_btn" type="button">
                                    <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_objective') !!}</span>
                                </a>

                                @if($objectives->lastPage() > 1 && $objectives->lastPage() != $objectives->currentPage())
                                    <a href="#" data-url="{{$objectives->url($objectives->currentPage()+1) }}" class="btn
                                    more-black-btn more-objectives" type="button">
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
        <div class="col-md-4 col-md-pull-8">
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
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script type="text/javascript">
    var msg_flag ='{{ $msg_flag }}';
    var msg_type ='{{ $msg_type }}';
    var msg_val ='{{ $msg_val }}';
</script>
<script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/objectives/delete_objective.js') !!}"></script>
@endsection
