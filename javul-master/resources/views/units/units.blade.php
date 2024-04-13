@extends('layout.default')
@section('page-meta')
<title>Units - Javul.org</title>
@endsection
@section('page-css')
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
        <div class="row form-group" style="margin-bottom:15px;">
            @include('elements.user-menu',['page'=>'units'])
        </div>
        <div class="row form-group">
            <div class="col-md-8 col-md-push-4">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>{!! trans('messages.units') !!}</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive loading_content_hide">
                        <div class="loading_dots unit_loading" style="position: absolute;top:20%;left:43%;z-index: 9999;display:none;">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <table class="table table-striped unit-table" style="overflow: hidden">
                            <thead>
                                <tr>
                                    <td colspan=3 class="unit_search_td">
                                        <div class="row form-group">
                                            <div class="col-sm-12">
                                                <div class="input-icon right">
                                                    <div class="input-group">
                                                        <i class="fa select-error"></i>
                                                        <select class="form-control" name="unit_category[]" id="unit_category" multiple="multiple">
                                                            @if(count($unit_category_arr) > 0)
                                                                @foreach($unit_category_arr as $id=>$val)
                                                                    <option @if(isset($category_search) && $category_search->id == $id) selected @endif value="{{$id}}" @if(!empty($edit_unit_category) && in_array($id,$edit_unit_category)) selected=selected @endif>{{$val}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <a href="" class="browse-categories input-group-addon" style="border-radius: 0px;
                                                    color:#333;text-decoration: none;">Browse</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                                        <!--<select name="location_search" class="form-control" id="location_search" style="display:none;
                                        "></select>-->
                                        <div class="row form-group">
                                            <div class="col-sm-4 form-group">
                                                <div class="input-icon right">
                                                    <i class="fa select-error"></i>
                                                    <select class="form-control" name="country" id="country">
                                                        <option value="">{!! trans('messages.select') !!}</option>
                                                        @if(count($countries) > 0)
                                                            @foreach($countries as $id=>$val)
                                                                @if($val == "dash_line" || $val == "dash_line1")
                                                                    <option value="{{$id}}" disabled></option>
                                                                @else
                                                                    <option value="{{$id}}">{{$val}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 form-group">
                                                <div class="input-icon right">
                                                    <i class="fa select-error"></i>
                                                    <select class="form-control" name="state" id="state">
                                                        <option value="">{!! trans('messages.select') !!}</option>
                                                    </select>
                                                    <span class="states_loader location_loader" style="display: none">
                                                        <img src="{!! url('assets/images/small_loader.gif') !!}"/>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 form-group">
                                                <div class="input-icon right">
                                                    <i class="fa select-error"></i>
                                                    <select class="form-control" name="city" id="city" >
                                                        <option value="">{!! trans('messages.select') !!}</option>
                                                    </select>
                                                    <span class="cities_loader location_loader" style="display: none">
                                                        <img src="{!! url('assets/images/small_loader.gif') !!}"/>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display:inline-block;position:relative;top:-6px">
                                            <a class="btn black-btn form-control search_unit" data-token="{{csrf_token()}}">Search</a>

                                            <a class="btn black-btn form-control reset_unit_search" href="{!! url('units') !!}"
                                               style="display:none">Reset</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('messages.unit_name') !!}</th>
                                    <th>{!! trans('messages.unit_category') !!}</th>
                                    <th>{!! trans('messages.description') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($units) > 0 )
                                    @foreach($units as $unit)
                                        <?php $category_ids = $unit->category_id;

                                        $category_names = \App\Models\UnitCategory::getName($category_ids);
                                        $category_ids = explode(",",$category_ids);
                                        $category_names  = explode(",",$category_names );
                                        ?>
                                        <tr>
{{--                                            <td><a href="{!! url('units/'.$unitIDHashID->encode($unit->id).'/'.$unit->slug) !!}">{{$unit->name}}</a></td>--}}
                                            <td>
                                                @if(count($category_ids) > 0 )
                                                    @foreach($category_ids as $index=>$category)
                                                        <a href="{!! url('units/category='.strtolower($category_names[$index])) !!}">{{$category_names[$index]}}</a>
                                                        @if(count($category_ids) > 1 && $index != count($category_ids) -1)
                                                            <span>&#44;</span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text_wraps" data-toggle="tooltip" data-placement="top"  title="{{ trim(strip_tags($unit->description)) }}">
                                                    <span class="ellipsis_text">{!!trim($unit->description)!!}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">No record(s) found.</td>
                                    </tr>
                                @endif
                                <tr style="background-color: #fff;text-align: right;">
                                    <td colspan="4">
                                        <a href="{!! url('units/add')!!}"class="btn black-btn" id="add_unit_btn"
                                           type="button">
                                            <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_unit')
                                                !!}</span>
                                        </a>
                                        @if($units->lastPage() > 1 && $units->lastPage() != $units->currentPage())
                                            <a href="#" data-url="{{$units->url($units->currentPage()+1) }}" class="btn more-black-btn more-units" id="add_unit_btn"
                                               type="button">
                                                MORE UNITS <span class="more_dots">...</span>
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
<script src="{!! url('assets/plugins/jquery.ThreeDots.min.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    var msg_flag ='{{ $msg_flag }}';
    var msg_type ='{{ $msg_type }}';
    var msg_val ='{{ $msg_val }}';
    var page='unit';
    var browse_category_box='';
    var selected_categories_id= new Array();
    $(function(){
        var the_obj = $('.text_wraps').ThreeDots({
            max_rows: 1
        });
        /*$('[data-toggle="tooltip"]').tooltip({
            container: 'body'
        });*/
    })
</script>
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/units/delete_unit.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/units/unit_location.js') !!}"></script>
<script src="{!! url('assets/js/units/units.js') !!}"></script>
<script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/admin/category_browse.js') !!}" type="text/javascript"></script>
@endsection
