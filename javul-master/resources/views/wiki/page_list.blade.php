@extends('layout.default')
@section('page-meta')
<title>Listing All Wiki Pages - Javul.org</title>
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
                    <h4>Listing All Wiki Pages</h4>
                    <div class="button pull-right small-a">
                        <a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}">+ New Page</a> |
                        <a href="{!! url('wiki/recent_changes') !!}/{!! $unit_id !!}/{!! $slug !!}">Recent Changes</a> | 
                        <a href="{!! url('wiki/all_pages') !!}/{!! $unit_id !!}/{!! $slug !!}">List All Pages</a>
                         
                    </div>
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Last Edit</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($pages['pages'])){ ?>
                                    <tr>
                                        <td colspan="100%" class="text-center"> <h4>No any Pages Created yet..  </h4> </td>
                                    </tr>
                                <?php  } ?>
                                <?php foreach ($pages['pages'] as $key => $page) { ?>
                                    <tr>
                                        <td width="65%">
                                           <a href="{!! url('wiki').'/'.$unit_id.'/'. $page['wiki_page_id'] .'/'.$slug !!}"><?= $page['wiki_page_title'] ?></a>
                                        </td>
                                        <td><?= $page['time_stamp'] ?></td>
                                        <td><a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}/{!! $page['wiki_page_id'] !!}">Edit</a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot class="<?= $pages['links'] == '' ? 'hide' : '' ?>">
                                <tr>
                                    <td class="text-center" colspan="100%"><?= $pages['links'] ?></td>
                                </tr>
                            </tfoot>
                          </table>
                          <br>
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
@endsection
