@extends('layout.default')
@section('page-css')
    <link href="{!! url('assets/css/wiki.css') !!}" rel="stylesheet" type="text/css" />
    <style type="text/css">
    .btn-compare{
        line-height: 1 !important;
        text-transform: uppercase;
        padding: 5px 20px 8px;
        border-radius: 2px;
    }
    </style>
@endsection
@section('content')
<link rel="stylesheet" type="text/css" href="{!! url('assets/css/forum.css') !!}">
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            @include('units.partials.unit_information_left_table')
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
                
                <div class="panel-heading current_unit_heading featured_unit_heading">
                    <div class="featured_unit current_unit">
                        <i class="fa fa-stack-overflow"></i>
                    </div>
                    <h4> View History: {!! $units->name !!} </h4>
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Rev Link</th>
                                    <th>Time</th>
                                    <th>Username</th>
                                    <th>Edit Comment</th>
                                    <th>Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revisions as $key => $value) {
                                    $user_id = $userIDHashID->encode($value->user_id);
                                 ?>
                                    <tr>
                                        <td> <input type="checkbox" name="id" value="{{ $value['id'] }}" class="single-checkbox"> </td>
                                        <td><a href="{!! route('unit_revison_view',[$unit_id,$value['id']])  !!}">View</a> </td>
                                        <td>{{ $Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->diffForHumans() }}</td>
                                        <td> <a href="{{ url('userprofiles/'. $user_id .'/'.strtolower($value->first_name.'_'.$value->last_name)) }}"> {{ $value->first_name ." ".$value->last_name }} </a></td>
                                        <td>{{ $value->comment }} </td>
                                        <td>{{ $value->size }}</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                
                            </tfoot>
                          </table>
                          <br>
                          <div class="text-center">
                            <button class="btn  btn-compare">Compare Revisions</button>
                          </div>
                            <div class="clearfix"></div><br>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts') 
<script type="text/javascript">
    var limit = 3;
    $('input.single-checkbox').on('change', function(evt) {
       
        if($('input.single-checkbox:checked').length >= limit) {
            this.checked = false;
        }
        if($('input.single-checkbox:checked').length == 2) {
           $(".btn-compare").addClass("black-btn");
        }
        else
        {
           $(".btn-compare").removeClass("black-btn");
        }
    });
    var loc ='{!! url("units") !!}/{!! $unit_id !!}/diff';
    var slug ='';
 
    $(".btn-compare").click(function(){
        if($('input.single-checkbox:checked').length == 2) {
           var rev = $('input.single-checkbox:checked')[0].value;
           var comp = $('input.single-checkbox:checked')[1].value;
           console.log(loc + "/" + rev + "/" + comp);
           location.href = loc + "/" + rev + "/" + comp ;
        }
    })
</script>
@endsection
