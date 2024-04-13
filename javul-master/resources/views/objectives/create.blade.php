@extends('layout.default')
@section('page-meta')
<title>@if(empty($objectiveObj)) Create Objective @else Update Objective @endif - Javul.org</title>
@endsection
@section('page-css')
    <link href="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
    <style>
        .hide-native-select .btn-group, .hide-native-select .btn-group .multiselect, .hide-native-select .btn-group.multiselect-container
        {width:100% !important;}
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row form-group" style="margin-bottom:15px;">
            @include('elements.user-menu',['page'=>'objectives'])
        </div>
        {{--<div class="row form-group">
            <div class="col-sm-12 ">
                <div class="col-sm-6 grey-bg unit_grey_screen_height">
                    <h1 class="unit-heading create_unit_heading">
                        <span class="glyphicon glyphicon-list-alt"></span>
                        @if(empty($objectiveObj))
                            Create Objective
                        @else
                            Update Objective
                        @endif
                    </h1><br /><br />
                </div>
                <div class="col-sm-6 grey-bg unit_grey_screen_height">
                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="panel form-group marginTop10">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <strong>Objective Information</strong>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">Total Objectives</div>
                                        <div class="col-xs-6 text-right">{{$totalObjectives}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">{!! trans('messages.total_fund_available') !!}</div>
                                        <div class="col-xs-6 text-right">XXX $</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">{!! trans('messages.total_fund_rewarded') !!}</div>
                                        <div class="col-xs-6 text-right">XXXX $</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="row">
            <div @if(!empty($unitInfo) || (!empty($objectiveObj) && !empty($objectiveObj->unit))) class="col-md-8 col-md-push-4" @else class="col-sm-12" @endif>
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        @if(empty($objectiveObj))
                            <h4>Create Objective</h4>
                        @else
                            <h4>Update Objective</h4>
                        @endif
                    </div>
                    <div class="panel-body list-group">
                        <div class="list-group-item">
                            <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                <div class="row">
                                    <div class="col-sm-4 form-group" @if(!empty($unitInfo)) style="display:none;" @endif>
                                        <label class="control-label">Unit</label>
                                        <div class="input-icon right">
                                            <i class="fa select-error"></i>
                                            <select name="unit" class="form-control">
                                                <option value="">Select</option>
                                                @if(count($unitsObj) > 0)
                                                    @foreach($unitsObj as $unit_id=>$unit)
{{--                                                        <option value="{{$unitIDHashID->encode($unit_id)}}"--}}
                                                        <option value=""
                                                                @if(!empty($objectiveObj) && $objectiveObj->unit_id == $unit_id)
                                                                selected=selected
                                                                @elseif(empty($objectiveObj) && !empty($objectives_unit_id) && $unit_id == $objectives_unit_id )
                                                                selected=selected
                                                                @endif>{{$unit}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 form-group">
                                        <label class="control-label">Objective Name</label>
                                        <div class="input-icon right">
                                            <i class="fa "></i>
                                            <input type="text" name="objective_name" value="{{ (!empty($objectiveObj))? $objectiveObj->name : old('objective_name') }}"
                                                   class="form-control"
                                                   placeholder="Objective Name"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 form-group">
                                        <label class="control-label">Parent objective</label>
                                        <div class="input-icon right">
                                            <i class="fa select-error"></i>
                                            <select class="form-control" name="parent_objective" id="parent_objective">
                                                <option value="">{!! trans('messages.select') !!}</option>
                                                @if(count($parentObjectivesObj) > 0)
                                                    @foreach($parentObjectivesObj as $objective_id=>$parentObjective)
                                                        <option value="{{$objectiveIDHashID->encode($objective_id)}}" @if(!empty($objectiveObj) &&
                                                                $objectiveObj->parent_id == $objective_id) selected=selected @endif>{{$parentObjective}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                <!--<div class="row">
                                <div class="col-sm-4 form-group">
                                    <label class="control-label" style="width: 100%;">Status</label>
                                    <input data-toggle="toggle" data-on="Active" data-off="Disabled" type="checkbox" name="status" @if(!empty($objectiveObj) &&
                                    $objectiveObj->status == "active") checked @endif>
                                </div>
                            </div>-->

                                @if(\Request::route()->getName() == 'objectives_edit')
                                <div class="col-sm-4 form-group">
                                    <label class="control-label">Status</label>
                                    <select class="form-control" name="status">
                                    @foreach(\App\Models\Objective::objectiveStatus() as $index=>$status)
                                        <option value="{{$index}}"
                                                @if(!empty($objectiveObj) && $objectiveObj->status == $index) selected=selected
                                                @elseif(empty($objectiveObj) && $index != "in-progress") disabled="disabled" @endif>
                                            {{$status}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Objective Description</label>
                                <textarea class="form-control summernote" name="description">
                                    @if(!empty($objectiveObj)) {{$objectiveObj->description}} @endif
                                </textarea>
                                </div>


                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Comment</label>
                                    <input class="form-control" name="comment">
                                </div>

                            </div>
                            <div class="row form-group">
                                <div class="col-sm-12 ">
                                    <button class="btn black-btn" id="create_objective" type="submit">
                                        @if(!empty($objectiveObj))
                                            <span class="glyphicon glyphicon-edit"></span> Update objective
                                        @else
                                            <i class="fa fa-plus plus"></i> <span class="plus_text">Create Objective</span>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
            @if(!empty($unitInfo) || !empty($objectiveObj))
                <div class="col-md-4 col-md-pull-8">
                    @include('units.partials.unit_information_left_table',['unitObj'=>(!empty($objectiveObj)?$objectiveObj->unit:$unitInfo),'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
                </div>
            @endif
        </div>
    </div>
    @include('elements.footer')
@stop
@section('page-scripts')
    <script>
        var edit_objective_flag = '{{(!empty($objectiveObj)?true:false)}}';

    </script>
    <script src="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/objectives/objectives.js') !!}"></script>
@endsection
