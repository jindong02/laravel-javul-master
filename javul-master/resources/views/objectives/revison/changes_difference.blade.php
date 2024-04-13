@extends('layout.default')
@section('page-css')
<style>.related_para{margin:0 0 10px;}</style>
@endsection
@section('content')
<link rel="stylesheet" type="text/css" href="{!! url('assets/css/forum.css') !!}">
<div class="container">
    <div class="row form-group" style="margin-bottom:15px">
        @include('elements.user-menu',['page'=>'objectives'])
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            @include('units.partials.unit_information_left_table',['unitObj'=>$objectiveObj->unit,'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
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


                <div class="panel-heading current_objective_heading featured_unit_heading">
                    <div class="featured_unit current_objective">
                        <i class="fa fa-bullseye" style="font-size:18px"></i>
                    </div>
                    <h4>Comparing Revisions</h4>
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-12">
                        <div class="col-md-6 hide">
                            <hr>
                            <div class="sub-content main-content">
                                {!! strip_tags($revisions[0]['description']) !!}
                            </div>
                        </div>
                        <div class="col-md-6 hide">
                            <hr>
                            <div class="sub-content compare-content">
                                {!! strip_tags($revisions[1]['description']) !!}
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
@endsection
@section('page-scripts')
<link href="{!! url('assets/plugins/jsdifflib-master/diffview.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! url('assets/plugins/jsdifflib-master/difflib.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/plugins/jsdifflib-master/diffview.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
diffUsingJS();
function diffUsingJS(viewType)
{
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
        baseTextName: "New : {!! date('d/m/Y ha',strtotime($revisions[0]['created_at'])) !!}",
        newTextName: "Old : {!! date('d/m/Y ha',strtotime($revisions[1]['created_at'])) !!}",
        contextSize: contextSize,
        viewType: viewType
    }));
}
</script>
@endsection
