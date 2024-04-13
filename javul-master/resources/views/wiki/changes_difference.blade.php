@extends('layout.default')
@section('page-meta')
<title>Comparing Revisions - Javul.org</title>
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
                    <h4><b>Comparing Revisions: <?= $difference['title'] ?></b></h4>
                    <div class="button pull-right small-a">
                        <a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}">+ New Page</a> | 
                        <a href="{!! url('wiki/recent_changes') !!}/{!! $unit_id !!}/{!! $slug !!}">Recent Changes</a> | 
                        <a href="{!! url('wiki/all_pages') !!}/{!! $unit_id !!}/{!! $slug !!}">List All Pages</a>
                    </div>
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-12">
                        <div class="col-md-6 hide">
                            <hr>
                            <div class="sub-content main-content">
                                {!! $difference['main']['page_content'] !!}
                            </div>
                        </div>
                        <div class="col-md-6 hide">
                            <hr>
                            <div class="sub-content compare-content">
                                {!! $difference['compare']['page_content'] !!}
                            </div>
                        </div>
                        <div class="viewType">
                            <input type="radio" name="_viewtype" id="sidebyside" onclick="diffUsingJS(0);" /> <label for="sidebyside">Side by Side Diff</label>
                            &nbsp; &nbsp;
                            <input type="radio" name="_viewtype" id="inline" onclick="diffUsingJS(1);" /> <label for="inline">Inline Diff</label>
                        </div>
                        <div id="diffoutput"></div>
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
<link href="{!! url('assets/plugins/jsdifflib-master/diffview.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! url('assets/plugins/jsdifflib-master/difflib.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/jsdifflib-master/diffview.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
diffUsingJS();
function diffUsingJS(viewType) {
    "use strict";
   
    var getContent = function (id) { 
        var oldHTML = $.trim($(id).html());
        return  oldHTML;
     },
    byId = function (id) { return document.getElementById(id); },
        base = difflib.stringAsLines(getContent(".compare-content")),
        newtxt = difflib.stringAsLines(getContent(".main-content")),
        sm = new difflib.SequenceMatcher(base, newtxt),
        opcodes = sm.get_opcodes(),
        diffoutputdiv = byId("diffoutput"),
        contextSize =  null;
    diffoutputdiv.innerHTML = "";
    diffoutputdiv.appendChild(diffview.buildView({
        baseTextLines: base,
        newTextLines: newtxt,
        opcodes: opcodes,
        baseTextName: "New : {!! date('d/m/Y ha',strtotime($difference['main']['time_stamp'])) !!}",
        newTextName: "Old : {!! date('d/m/Y ha',strtotime($difference['compare']['time_stamp'])) !!}",
        contextSize: contextSize,
        viewType: viewType
    }));
}
</script>
@endsection
