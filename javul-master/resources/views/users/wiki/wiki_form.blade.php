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
                        <form action="" method="post" id="wiki_forum" role="form" enctype="multipart/form-data">
                            <div class="clearfix"></div><br>
                            <?php 
                                $pageType = isset($userWiki) ? $userWiki->page_type : 1;
                            ?>
                            
                            @if($pageType == 1)
                            <div class="col-sm-12 form-group">
                                <label>Page Title</label>
                                <input class="form-control" name="title" value="<?= isset($userWiki) ? $userWiki->page_title : '' ?>">
                            </div>
                            <br>
                            @endif
                            
                            <div class="col-sm-12 form-group">
                                <label>Page Content</label>
                                <textarea class="form-control old_value hide" ></textarea>
                                <textarea class="form-control summernote" name="description"><?= isset($userWiki) ? $userWiki->page_content : '' ?></textarea>
                            </div>
                            <br>
                            @if($pageType == 1)
                            <div class="col-sm-12 form-group">
                                <label>Privacy</label>
                                
                                <select class="form-control " name="private">
                                    <option <?= isset($userWiki) ? ($userWiki->private == 0 ? 'selected' : '' ) : '' ?> value="0">Public</option>
                                    <option <?= isset($userWiki) ? ($userWiki->private == 1 ? 'selected' : '' ) : '' ?> value="1">Private</option>
                                </select>
                            </div>
                            <br>
                            @endif
                            @if($pageType == 1)
                            <div class="col-sm-12 form-group">
                                <label>Edit Comment</label>
                                <input class="form-control" name="edit_comment" value="">
                            </div>
                            <br>
                            @endif
                            <input class="form-control" type="hidden" name="id" value="<?= isset($userWiki) ? $userWiki->id : '' ?>">
                            <input class="form-control" type="hidden" name="slug" value="{{ $slug }}">
                            
                            {!! csrf_field() !!}
                            <div class=" col-sm-12 form-group">
                                <input type="button" class="btn pull-left black-btn cancel-edit" value="Cancel">
                                <button class="btn pull-right black-btn">Save Page</button>
                            </div>
                            <div class="clearfix"></div><br>
                        </form>
                    <div class="clearfix"></div>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
@section('page-scripts')
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{!! url('assets/plugins/editor/sets/wiki/style.css') !!}">
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script type="text/javascript" src="{!! url('assets/plugins/editor/sets/wiki/set.js') !!}"></script>
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    $('#input-3').rating({displayOnly: true, step: 0.1,size:'xs'});
    $('.summernote').ckeditor();

    CKEDITOR.on('instanceReady', function(){
        $.each( CKEDITOR.instances, function(instance) {
            CKEDITOR.instances[instance].on("change", function(e) {
                for ( instance in CKEDITOR.instances )
                    CKEDITOR.instances[instance].updateElement();
            });
        });
    });

    $(".cancel-edit").click(function(){
        var oldValue = $(".old_value").val();
        var newValue = $(".summernote").val();
        if(oldValue != newValue){
            if (window.confirm('Content Was Changed. Cancel Edit ?')) {
                history.back();
            } 
        }
        else
        {
            history.back();
        }
       
    });
    var xhr;
    $("#wiki_forum").submit(function(){
        if(xhr && xhr.readyState != 4){
            xhr.abort();
        }
        $("#wiki_forum").find(".alert").remove();
        xhr = $.ajax({
            type:'post',
            url:'{{ route("user_wiki_save_page",[$user_id_hash]) }}',
            data:$(this).serialize(),
            dataType:'json',
            beforeSend:function(){
                $("#wiki_forum button").button("loading");
            },
            error:function(){
                
            },
            complete:function(){
                $("#wiki_forum button").button("reset");
            },
            success:function(json){
                if(json['errors']){
                    $.each(json['errors'],function(i,j){
                        $("[name='"+ i +"']").after("<div class='alert alert-danger'> "+ j +" </div>");
                    })
                }
                if(json['success']){
                    toastr['success'](json['success'], '');
                    setTimeout(function(){ location = json['location'] },1000);
                }
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
        return false;
    })
   
</script>
@endsection