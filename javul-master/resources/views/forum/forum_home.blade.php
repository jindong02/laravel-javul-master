@extends('layout.default')
@section('page-meta')
<title>Forum - Javul.org</title>
@endsection
@section('page-css')
<style type="text/css">
    .topic-list{
        margin: 0;
        padding:0;
    }
    .topic-list li{
        list-style: none;
        padding-left: 55px;
        position: relative;
        border: solid 1px #ddd;
        /*margin-bottom: 5px;: relative;*/
    }
    .topic-list .silent{
        font-size: 11px;
        color: gray;
        margin-bottom: 4px;
    }
    .topic-list .counter{
        position: absolute;
        left: 0;
        height: 100%;
        background: #e7ecf1;
        width: 32px;
        line-height: 100%;
        padding-top: 17px;
        text-align: center;
        color: gray;
        border-right: solid 1px #ddd;
    }
    .topic-list .heading{
        font-size: 15px;
        text-transform: capitalize;
        margin-bottom: 11px;
    }
    .topic-list .up-down {
        position: absolute;
        left: 0px;
        background: #eee;
        height: 100%;
        width: 44px;
        text-align: center;
    }
    .topic-list .up-down i {
        display: block;
        display: block;
        color: gray;
        margin-bottom: 4px;
        text-align: center;
        width: 100%;
        cursor: pointer;
    }
    .topic-list .up-down i.count {
        font-size: 11px;
        margin : 0px;
        cursor: default;
    }
    .topic-list .up-down i.active {
        color: #ff8b60;
    }
</style>
@endsection
@section('content')
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
                <div class="panel-heading">
                    <h4><i class="fa fa-quote-right"></i> Forum</h4>
                </div>
                <div class="panel-body list-group" style="padding: 10px 7px;">
                
                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading current_unit_heading featured_unit_heading">                   
                            <h4 style="width: 100%;line-height: 31px;">
                            <a href="{!! url('forum').'/'.$unit_id.'/'.'objectives' !!}" > Objectives </a>
                              <a class="pull-right black-btn " href="{!! url('forum/create').'/'.$unit_id.'/'.'objectives' !!}"> Create New Topic </a>
                             <a class=" all-topics pull-right black-btn mrgnrt30" href="{!! url('forum/'. $unit_id .'/objectives') !!}"> All Topics </a>
                            </h4>
                        </div>
                        <div class="panel-body current_unit_body" style="padding-top:0px">
                            <br>
                            <ul class="topic-list">
                                <?php if(!isset($topics[1])){ ?>
                                    <h4 class="text-center">No forum topics found</h4>
                                <?php } else { ?>
                                <?php foreach ($topics[1] as $key => $topic) { ?>
                                    <li data-id="{!! $topic['topic_id'] !!}">
                                        <div class="up-down">
                                            @if(Auth::check())<i data-value="1" class="glyphicon <?= $topic['updownstatus'] == 1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-up"></i>@endif
                                            <i class="count" @if(!Auth::check()) style="padding-top: calc(100% - 25px);" @endif>{!! $topic['votecount'] !!}</i>
                                            @if(Auth::check())<i data-value="0" class="glyphicon <?= $topic['updownstatus'] == -1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-down"></i>@endif
                                        </div>
                                        <h4 class="heading"><a href="{!! url('forum/post').'/'.$topic['topic_id'].'/'.$topic['slug'] !!}"> <?= $topic['title'] ?> </a></h4>
                                        <div class="silent">
                                            <b>Submitted by</b> <a href="{!! $topic['link_user'] !!}"> <?= $topic['first_name'] ." ". $topic['last_name'] ?> </a>  <?= $topic['created_time'] ?> hour ago. <?= $topic['post']   ?> 
                                                replies.
                                            <?php if($topic['post']) { ?>
                                                 (<b>last reply</b> by <a href="{!! $topic['link_reply'] !!}"> <?= $topic['lastReply'] ?> </a>)
                                            <?php } ?>
                                        </div>
                                    </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading current_unit_heading featured_unit_heading">                   
                            <h4 style="width: 100%;line-height: 31px;"> 
                            <a href="{!! url('forum').'/'.$unit_id.'/'.'tasks' !!}" > Tasks</a>
                              <a class="pull-right black-btn " href="{!! url('forum/create').'/'.$unit_id.'/'.'tasks' !!}"> Create New Topic </a>
                             <a class=" all-topics pull-right black-btn mrgnrt30" href="{!! url('forum/'. $unit_id .'/tasks') !!}"> All Topics </a>
                            </h4>
                        </div>
                        <div class="panel-body current_unit_body" style="padding-top:0px">
                            <br>
                            <ul class="topic-list">
                                <?php if(!isset($topics[2])){ ?>
                                    <h4 class="text-center">No forum topics found</h4>
                                <?php } else { ?>
                                <?php foreach ($topics[2] as $key => $topic) { ?>
                                    <li data-id="{!! $topic['topic_id'] !!}">
                                        <div class="up-down">
                                            @if(Auth::check())<i data-value="1" class="glyphicon <?= $topic['updownstatus'] == 1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-up"></i>@endif
                                            <i class="count" @if(!Auth::check()) style="padding-top: calc(100% - 25px);" @endif>{!! $topic['votecount'] !!}</i>
                                            @if(Auth::check())<i data-value="0" class="glyphicon <?= $topic['updownstatus'] == -1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-down"></i>@endif
                                        </div>
                                        <h4 class="heading"><a href="{!! url('forum/post').'/'.$topic['topic_id'].'/'.$topic['slug'] !!}"> <?= $topic['title'] ?> </a></h4>
                                        <div class="silent">
                                            <b>Submitted by</b> <a href="{!! $topic['link_user'] !!}"> <?= $topic['first_name'] ." ". $topic['last_name'] ?> </a>  <?= $topic['created_time'] ?> hour ago. <?= $topic['post']   ?> 
                                                replies.
                                            <?php if($topic['post']) { ?>
                                                 (<b>last reply</b> by <a href="{!! $topic['link_reply'] !!}"> <?= $topic['lastReply'] ?> </a>)
                                            <?php } ?>
                                        </div>
                                    </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading current_unit_heading featured_unit_heading">                   
                            <h4 style="width: 100%;line-height: 31px;">
                            <a href="{!! url('forum').'/'.$unit_id.'/'.'issues' !!}"> Issues  </a>
                              <a class="pull-right black-btn " href="{!! url('forum/create').'/'.$unit_id.'/'.'issues' !!}"> Create New Topic </a>
                             <a class=" all-topics pull-right black-btn mrgnrt30" href="{!! url('forum/'. $unit_id .'/issues') !!}"> All Topics </a>
                            </h4>
                        </div>
                        <div class="panel-body current_unit_body" style="padding-top:0px">
                            <br>
                            <ul class="topic-list">
                                <?php if(!isset($topics[3])){ ?>
                                    <h4 class="text-center">No forum topics found</h4>
                                <?php } else { ?>
                                <?php foreach ($topics[3] as $key => $topic) { ?>
                                    <li data-id="{!! $topic['topic_id'] !!}">
                                        <div class="up-down">
                                            @if(Auth::check())<i data-value="1" class="glyphicon <?= $topic['updownstatus'] == 1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-up"></i>@endif
                                            <i class="count" @if(!Auth::check()) style="padding-top: calc(100% - 25px);" @endif>{!! $topic['votecount'] !!}</i>
                                            @if(Auth::check())<i data-value="0" class="glyphicon <?= $topic['updownstatus'] == -1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-down"></i>@endif
                                        </div>
                                        <h4 class="heading"><a href="{!! url('forum/post').'/'.$topic['topic_id'].'/'.$topic['slug'] !!}"> <?= $topic['title'] ?> </a></h4>
                                        <div class="silent">
                                            <b>Submitted by</b> <a href="{!! $topic['link_user'] !!}"> <?= $topic['first_name'] ." ". $topic['last_name'] ?> </a>  <?= $topic['created_time'] ?> hour ago. <?= $topic['post']   ?> 
                                                replies.
                                            <?php if($topic['post']) { ?>
                                                 (<b>last reply</b> by <a href="{!! $topic['link_reply'] !!}"> <?= $topic['lastReply'] ?> </a>)
                                            <?php } ?>
                                        </div>
                                    </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="panel panel-grey panel-default" style="margin-bottom: 30px;">
                        <div class="panel-heading current_unit_heading featured_unit_heading">                   
                            <h4 style="width: 100%;line-height: 31px;">
                            <a href="{!! url('forum').'/'.$unit_id.'/'.'other_discussions' !!}">Other Discussions</a>
                              <a class="pull-right black-btn " href="{!! url('forum/create').'/'.$unit_id.'/'.'other_discussions' !!}"> Create New Topic </a>
                             <a class=" all-topics pull-right black-btn mrgnrt30" href="{!! url('forum/'. $unit_id .'/other_discussions') !!}"> All Topics </a>
                            </h4>
                        </div>
                        <div class="panel-body current_unit_body" style="padding-top:0px">
                            <br>
                            <ul class="topic-list">
                                <?php if(!isset($topics[4])){ ?>
                                    <h4 class="text-center">No forum topics found</h4>
                                <?php } else { ?>
                                <?php foreach ($topics[4] as $key => $topic) { ?>
                                    <li data-id="{!! $topic['topic_id'] !!}">
                                        <div class="up-down">
                                            @if(Auth::check())<i data-value="1" class="glyphicon <?= $topic['updownstatus'] == 1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-up"></i>@endif
                                            <i class="count" @if(!Auth::check()) style="padding-top: calc(100% - 25px);" @endif>{!! $topic['votecount'] !!}</i>
                                            @if(Auth::check())<i data-value="0" class="glyphicon <?= $topic['updownstatus'] == -1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-down"></i>@endif
                                        </div>
                                        <h4 class="heading"><a href="{!! url('forum/post').'/'.$topic['topic_id'].'/'.$topic['slug'] !!}"> <?= $topic['title'] ?> </a></h4>
                                        <div class="silent">
                                            <b>Submitted by</b> <a href="{!! $topic['link_user'] !!}"> <?= $topic['first_name'] ." ". $topic['last_name'] ?> </a>  <?= $topic['created_time'] ?> hour ago. <?= $topic['post']   ?> 
                                                replies.
                                            <?php if($topic['post']) { ?>
                                                 (<b>last reply</b> by <a href="{!! $topic['link_reply'] !!}"> <?= $topic['lastReply'] ?> </a>)
                                            <?php } ?>
                                        </div>
                                    </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>
                    
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
     $(".topic-list").delegate(".up-down-vote","click",function(){
        $this = $(this);
        var topic_id  = $this.parents("li:first").attr("data-id");
        var val  = $this.attr("data-value");
        $.ajax({
            type:'post',
            url:'{!! url('forum/topicUpDown') !!}',
            data:{
                _token : '{{csrf_token()}}',
                val : val,
                topic_id : topic_id,
                didIt : $this.hasClass("active"),
            },
            dataType:'json',
            beforeSend:function(){
                if($this.hasClass("active")){
                    $this.parents(".up-down").find(".active").removeClass("active");
                }
                else
                {
                    $this.parents(".up-down").find(".active").removeClass("active");
                    $this.addClass("active");
                }
            },
            success:function(json){
                if(json['success']){
                    $this.parents(".up-down").find(".count").html(json['count']);
                }
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
    });
</script>
@endsection
