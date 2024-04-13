@extends('layout.default')
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-star-rating-master/css/star-rating.css') !!}" media="all" rel="stylesheet" type="text/css" />
<style>
    span.tags{padding:0 6px;}
    .text-danger{color:#ed6b75 !important;}
    .navbar-nav > li.active{background-color: #e7e7e7;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',array('page'=>'home'))
    </div>
    @include('users.user-profile')
    <div class="row">
        <div class="col-sm-4">
            <div class="left" style="position: relative;margin-top: 30px;">
                <div class="site_activity_loading loading_dots" style="position: absolute;top:20%;left:43%;z-index: 9999;display: none;">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="site_activity_list">
                    @include('elements.site_activities_user',['site_activity'=>$site_activities])
                </div>
            </div>
        </div>
        <div class="col-md-8">
                <div class="panel panel-grey panel-default" style="margin-top:29px ">
                    <div class="panel-heading">
                        <h4 class="pull-left">Page Diffrence</h4>
                        <div class="user-wikihome-tool pull-right small-a">
                           <a href="{{ route('user_wiki_newpage',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> + New Page </a> | 
                           <a href="{{ route('user_wiki_recent_changes',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> Recent Changes </a> |
                           <a href="{{ route('user_wiki_page_list',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> List All Pages </a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body table-inner table-responsive loading_content_hide">
                        <div class="col-md-12">
                            <div class="col-md-6 hide">
                                <hr>
                                <div class="sub-content main-content">
                                    {!! $userWikiRev['0']['page_content'] !!}
                                </div>
                            </div>
                            <div class="col-md-6 hide">
                                <hr>
                                <div class="sub-content compare-content">
                                    {!! $userWikiRev['1']['page_content'] !!}
                                </div>
                            </div>
                            <div class="viewType">
                                <input type="radio" name="_viewtype" id="sidebyside" onclick="diffUsingJS(0);" /> <label for="sidebyside">Side by Side Diff</label>
                                &nbsp; &nbsp;
                                <input type="radio" name="_viewtype" id="inline" onclick="diffUsingJS(1);" /> <label for="inline">Inline Diff</label>
                            </div>
                            <div id="diffoutput"></div>
                        </div>
                        <div class="clearfix"></div><br>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
@section('page-scripts')
<link rel="stylesheet" type="text/css" href="{!! url('assets/css/wiki.css') !!}">
<link href="{!! url('assets/plugins/jsdifflib-master/diffview.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! url('assets/plugins/jsdifflib-master/difflib.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/jsdifflib-master/diffview.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
    $('#input-3').rating({displayOnly: true, step: 0.1,size:'xs'});
});
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
        baseTextName: "New : {!! date('d/m/Y ha',strtotime($userWikiRev['0']['created_at'])) !!}",
        newTextName: "Old : {!! date('d/m/Y ha',strtotime($userWikiRev['0']['created_at'])) !!}",
        contextSize: contextSize,
        viewType: viewType
    }));
}
</script>
@endsection