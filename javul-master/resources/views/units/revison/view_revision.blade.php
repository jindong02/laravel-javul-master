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
                    <h4> View Revision: {!! $units->name !!} </h4>
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-12">
                        <h4 class="text-center">Previous revision</h4>
                        <h5 class="text-center"><?= date("d-m-Y h:A",strtotime($revisions->created_at)) ?>, Edited By User {!! $revisions->first_name .' '. $revisions->last_name !!}</h5>

                        <hr>
                        <div class="col-md-12 wiki-page-desc">{!! $revisions->description !!}</div>
                        <div class="clearfix"></div>
                        <hr>
                        &nbsp;&nbsp;&nbsp;<b> Comment : </b> <?= $revisions->comment ?>
                        <br><br>
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

@endsection
