@extends('layout.default')
@section('page-meta')
<title>@if(empty($unitObj)) Create Unit @else Update Unit @endif - Javul.org</title>
@endsection
@section('page-css')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jodit/3.1.39/jodit.min.css">
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/select2/css/select2-bootstrap.min.css') !!}" rel="stylesheet" type="text/css" />
<style>
    .select2-results {
        max-height: 300px;
        padding: 0 0 0 4px;
        margin: 4px 4px 4px 0;
        position: relative;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }
    .hierarchy_parent{margin-left: 10px;}
    .add_edit_categories div:first-child{margin-top:0px;padding-left:5px;padding-right:5px;}
    .new_box,#category_firstbox{min-width: 12.5em;width:100%;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    {{--<div class="row form-group">
        <div class="col-sm-12 ">
            <div class="col-sm-6 grey-bg unit_grey_screen_height">
                <h1 class="unit-heading create_unit_heading">
                    <span class="glyphicon glyphicon-list-alt"></span>
                    @if(empty($unitObj))
                        Create Unit
                    @else
                        Update Unit
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
                                        <strong>{!! trans('messages.unit_information')!!}</strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">{!! trans('messages.total_units') !!}</div>
                                    <div class="col-xs-6 text-right">{{$totalUnits}}</div>
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
    <div class="panel panel-grey panel-default">
        <div class="panel-heading">
            @if(empty($unitObj))
                <h4>Create Unit</h4>
            @else
                <h4>Update Unit</h4>
            @endif
        </div>
        <div class="panel-body list-group">
            <div class="list-group-item">
                <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label class="control-label">{!! trans('messages.unit_name')!!}</label>
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" name="unit_name" value="{{ (!empty($unitObj))? $unitObj->name : old('unit_name') }}"
                                       class="form-control"
                                       placeholder="{!! trans('messages.unit_name') !!}"/>
                            </div>
                        </div>
                        <?php
                        $edit_unit_category = [];
                        if(!empty($unitObj))
                            $edit_unit_category = explode(",",$unitObj->category_id);
                        ?>
                        <div class="col-sm-4 form-group">
                            <label class="control-label">{!! trans('messages.unit_category') !!}</label>
                            <div class="input-icon right">
                                <div class="input-group">
                                    <i class="fa select-error"></i>
                                    <select class="form-control" name="unit_category[]" id="unit_category" multiple="multiple">
                                        @if(count($unit_category_arr) > 0)
                                            @foreach($unit_category_arr as $id=>$val)
                                                <option value="{{$id}}" @if(!empty($edit_unit_category) && in_array($id,$edit_unit_category)) selected=selected @endif>{{$val}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <a href="" class="browse-categories input-group-addon" style="border-radius: 0px;
                                                    color:#333;text-decoration: none;">Browse</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label class="control-label">Country</label>
                            <div class="input-icon right">
                                <i class="fa select-error"></i>
                                <select class="form-control" name="country" id="country">
                                    <option value="">{!! trans('messages.select') !!}</option>
                                    @if(count($countries) > 0)
                                        @foreach($countries as $id=>$val)
                                            @if($val == "dash_line" || $val == "dash_line1")
                                                <option value="{{$id}}" disabled></option>
                                            @else
                                                <option value="{{$id}}" @if(!empty($unitObj) && $unitObj->country_id == $id)
                                                selected=selected @endif>{{$val}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label class="control-label">State</label>
                            <div class="input-icon right">
                                <i class="fa select-error"></i>
                                <select class="form-control" name="state" id="state" @if(!empty($unitObj) && $unitObj->country_id == "global")
                                disabled @endif>
                                    @if(!empty($unitObj))
                                        @foreach($states as $id=>$val)
                                            <option value="{{$id}}" @if(!empty($unitObj) && $unitObj->state_id == $id)
                                            selected=selected @endif>{{$val}}</option>
                                        @endforeach
                                    @else
                                        <option value="">{!! trans('messages.select') !!}</option>
                                    @endif
                                </select>
                                <span class="states_loader location_loader" style="display: none">
                                    <img src="{!! url('assets/images/small_loader.gif') !!}"/>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label class="control-label">City</label>
                            <div class="input-icon right">
                                <i class="fa select-error"></i>
                                <select class="form-control" name="city" id="city" @if(!empty($unitObj) && $unitObj->country_id == "global")
                                disabled @endif>
                                    @if(!empty($unitObj))
                                        @if(!empty($state_name_as_city_for_field))
                                            <option value="{{$state_name_as_city_for_field->id}}" selected>{{$state_name_as_city_for_field->name}}</option>
                                        @else
                                            @foreach($cities as $cid=>$val)
                                                <option value="{{$cid}}" @if(!empty($unitObj) && $unitObj->city_id == $cid)
                                                selected=selected @endif>{{$val}}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        <option value="">{!! trans('messages.select') !!}</option>
                                    @endif
                                </select>
                                <input type="hidden" name="empty_city_state_name" id="empty_city_state_name"
                                       @if(!empty($state_name_as_city_for_field)) value="{{$unitObj->state_id_for_city_not_exits}}" @endif/>
                                <span class="cities_loader location_loader" style="display: none">
                                    <img src="{!! url('assets/images/small_loader.gif') !!}"/>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label class="control-label">{!! trans('messages.unit_credibility') !!}</label>
                            <div class="input-icon right">
                                <i class="fa select-error"></i>
                                <select class="form-control" name="credibility">
                                    <option value="">{!! trans('messages.select') !!}</option>
                                    @if(count($unit_credibility_arr) > 0)
                                        @foreach($unit_credibility_arr as $id=>$val)
                                             <option value="{{$id}}" @if(!empty($unitObj) && $unitObj->credibility == $id)
                                             selected=selected @endif>{{$val}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

{{--                    <div class="row">--}}
{{--                        <div class="col-sm-12 form-group">--}}
{{--                            <label class="control-label">Unit Description</label>--}}
{{--                            <textarea class="form-control summernote" name="description">@if(!empty($unitObj)) {!! $unitObj->description !!} @endif</textarea>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    Unit Description new editor testing--}}

                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <label class="control-label">Unit Description</label>
                            <textarea class="form-control" id="editor" name="description">@if(!empty($unitObj)) {!! $unitObj->description !!} @endif</textarea>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <label class="control-label">Comment</label>
                            <input class="form-control" name="comment" @if(!empty($unitObj) && !empty($unitObj->comment))
                            value="{{$unitObj->comment}}" @endif>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-4 form-group">
                            <label class="control-label">Related To</label>
                            <div class="input-icon right">
                                <i class="fa select-error"></i>
                                <select class="form-control" name="related_to[]" id="related_to" multiple="multiple">
                                    @if(count($relatedUnitsObj) > 0 )
                                        @foreach($relatedUnitsObj as $id=>$relate)
                                            <option value="{{$id}}" @if(!empty($unitObj) && !empty($relatedUnitsofUnitObj) &&
                        					in_array($id,$relatedUnitsofUnitObj)) selected=selected @endif>{{$relate}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label class="control-label">Parent Unit</label>
                            <div class="input-icon right">
                                <i class="fa select-error"></i>
                                <select class="form-control" name="parent_unit" id="parent_unit" >
                                    <option value="">Select</option>
                                    @if(count($parentUnitsObj) > 0 )
                                        @foreach($parentUnitsObj as $id=>$parent)
                                            <option value="{{$id}}" @if(!empty($unitObj) && $id == $unitObj->parent_id)
                                            selected=selected @endif>{{$parent}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        @if(!empty($unitObj) && $authUserObj->role == "superadmin")
                        <div class="col-sm-4 form-group">
                            <label class="control-label" style="width: 100%;">Status</label>
                            <input data-toggle="toggle" data-on="Active" data-off="Disabled" type="checkbox" name="status" @if(!empty($unitObj) &&
                            $unitObj->status == "active") checked @elseif(empty($unitObj)) checked @endif>
                        </div>
                        @endif
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12 ">
                            <button class="btn black-btn" id="create_unit" type="submit">
                                @if(!empty($unitObj))
                                    <span class="glyphicon glyphicon-edit"></span> Update Unit
                                @else
                                    <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.create_unit') !!}</span>
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
    <script>
        var page='unit';
        var browse_category_box='';
        var selected_categories_id= new Array();
        var edit_unit_flag ='{{!empty($unitObj)?true:false}}';

        $(function(){
            if(edit_unit_flag == 1){
                var country_id_temp = $("#country").val()
                if(country_id_temp == 247){
                    $("#state").prop('disabled',true);
                    $("#city").prop('disabled',true);
                }
            }
        })
    </script>


    <script src="//cdnjs.cloudflare.com/ajax/libs/jodit/3.1.92/jodit.min.js"></script>
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/units/units.js') !!}"></script>
<script src="{!! url('assets/js/units/unit_location.js') !!}"></script>
<script src="{!! url('assets/js/admin/category_browse.js') !!}" type="text/javascript"></script>


    <script>

        var editor = new Jodit('#editor', {
            textIcons: false,
            iframe: false,

            height: 300,

            observer: {
                timeout: 100
            },
            uploader: {
                "insertImageAsBase64URI": true
            },


        });

    </script>
@endsection