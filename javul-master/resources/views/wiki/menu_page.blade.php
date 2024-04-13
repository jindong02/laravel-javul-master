@extends('layout.default')
@section('page-meta')
<title>Unit Menu: Other Links - Javul.org</title>
@endsection
@section('page-css')
    <link href="{!! url('assets/css/wiki.css') !!}" rel="stylesheet" type="text/css" />
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
                <div class="panel-heading current_task_heading  current_task_heading_red featured_unit_heading">
                    <div class="featured_unit current_task red">
                        <i class="fa fa-book"></i>
                    </div>
                    <h4> Unit Menu: Other Links </h4>
                    <div class="button pull-right small-a">
                        <a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}">+ New Page</a> | 
                        <a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}/{!! $wiki_page['wiki_page_id'] !!}">Edit</a> | 
                        <a href="{!! url('wiki/recent_changes') !!}/{!! $unit_id !!}/{!! $slug !!}">Recent Changes</a> | 
                        <a href="{!! url('wiki/all_pages') !!}/{!! $unit_id !!}/{!! $slug !!}">List All Pages</a>
                    </div>
                </div>

                <div class="panel-body list-group">
                    <div class="col-md-12 wiki-page-desc">@if($wiki_page['page_content'] === 'Edit these links <br/>') <a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}/{!! $wiki_page['wiki_page_id'] !!}"><i class="fa fa-pencil"></i></a> @else {!! $wiki_page['page_content'] !!} @endif</div>
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
