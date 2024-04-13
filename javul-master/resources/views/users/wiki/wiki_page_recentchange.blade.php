@extends('layout.default')
@section('page-css')
<link rel="stylesheet" type="text/css" href="{!! url('assets/css/wiki.css') !!}">
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
                        <h4 class="pull-left">User Wiki</h4>
                        <div class="user-wikihome-tool pull-right">
                           <div class="user-wikihome-tool pull-right small-a">
                           <a href="{{ route('user_wiki_newpage',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> + New Page </a> | 
                           <a href="{{ route('user_wiki_recent_changes',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> Recent Changes </a> |
                           <a href="{{ route('user_wiki_page_list',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash ])  }}"> List All Pages </a>
                        </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body table-inner table-responsive loading_content_hide">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Time</th>
                                        <th>Byte</th>
                                        <th>Username</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $prevKey = 0; ?>
                                    @foreach ($userWikiRev as $key=>$page)
                                        <?php
                                            $nextPageId = 0;
                                        ?>
                                        <tr>
                                            <td> 
                                                <a href="{{ route('user_wiki_rev_diff',[$slug,$user_id_hash,$page->id,$nextPageId ]) }}">Diff</a>|
                                                <a href="{{ route('user_wiki_history',[ str_replace(' ', '_', strtolower($userObj->first_name." ".$userObj->last_name) ),$user_id_hash, $userPageIDHashID->encode($page->page_id) ])  }}">Hist</a>
                                            </td>
                                            <td><a href="{{ route('user_wiki_view',[$slug, $userPageIDHashID->encode($page->id),$page->slug]) }}"> {{ $page->page_title }}</a></td>
                                            <td>{{ $Carbon::createFromFormat('Y-m-d H:i:s', $page->updated_at)->diffForHumans() }}</td>
                                            <td>{{ $page->size > 0 ? '+'.$page->size : '-'.$page->size }}</td>
                                            <td><a href="{{ url('userprofiles/'. $userIDHashID->encode($page->user_id) .'/'.strtolower($page->first_name."_".$page->last_name)) }}"> {{ $page->first_name . " ". $page->last_name }}</a> 
                                                ({{ $page->comment }})
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="3"> {{ $userWikiRev->links() }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
@section('page-scripts')
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    $('#input-3').rating({displayOnly: true, step: 0.1,size:'xs'});
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
    var loc ='{{ route("user_wiki_rev_diff",[$slug,$user_id_hash ]) }}';
    
    $(".btn-compare").click(function(){
        if($('input.single-checkbox:checked').length == 2) {
           var rev = $('input.single-checkbox:checked')[0].value;
           var comp = $('input.single-checkbox:checked')[1].value;
           location = loc + "/" + rev + "/" + comp;
        }
    })
</script>
@endsection