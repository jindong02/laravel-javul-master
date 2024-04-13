@extends('layout.default')
@section('page-meta')
<title>Create New Wiki Page - Javul.org</title>
@endsection
@section('page-css')
    <link href="{!! url('assets/css/wiki.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
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
                    <h4>Create New Wiki Page</h4>
                    <div class="button pull-right small-a">
                        <a href="{!! url('wiki/edit') !!}/{!! $unit_id !!}/{!! $slug !!}">+ New Page</a> |
                        <a href="{!! url('wiki/recent_changes') !!}/{!! $unit_id !!}/{!! $slug !!}">Recent Changes</a> | 
                        <a href="{!! url('wiki/all_pages') !!}/{!! $unit_id !!}/{!! $slug !!}">List All Pages</a>
                    </div>
                </div>
                <div class="panel-body list-group">
                    <form action="" method="post" id="wiki_forum" role="form" enctype="multipart/form-data">
                        <div class="clearfix"></div><br>
                        <?php if(isset($wiki_page_rev_id)) { ?>
                            <div class="col-md-10 text-center col-md-offset-1 alert alert-danger">You are editing older revision of <?= date("d/m/Y ha",strtotime($wiki_page['time_stamp'])) ?> </div>
                        <?php } ?>
                        <?php if( (isset($wiki_page) && !$wiki_page['is_wikihome']) || !isset($wiki_page) )  { ?>
                        <div class="col-sm-12 form-group">
                            <label>Page Title</label>
                            <input class="form-control" name="title" value="<?=  isset($wiki_page) ? $wiki_page['wiki_page_title'] : '' ?>">
                        </div>
                        <br>
                        <?php } else{ ?>
                        <input class="form-control" type="hidden" name="title" value="">
                        <?php } ?>
                        <div class="col-sm-12 form-group">
                            <label>Page Content</label>
                            <textarea class="form-control old_value hide" ><?=  isset($wiki_page) ? $wiki_page['page_content'] : '' ?></textarea>
                            <textarea class="form-control summernote" name="description"><?=  isset($wiki_page) ? $wiki_page['page_content'] : '' ?></textarea>
                        </div>
                        <br>
                        <?php if( isset($wiki_page) )  { ?>
                        <div class="col-sm-12 form-group">
                            <label>Edit Comment</label>
                            <input class="form-control" name="edit_comment" value="">
                        </div>
                        <br>
                        <?php } ?>
                        <input type="hidden" name="id" value="<?=  isset($wiki_page) ? $wiki_page['wiki_page_id'] : '0' ?>">
                        <input type="hidden" name="is_wikihome" value="<?=  isset($wiki_page) ? $wiki_page['is_wikihome'] : '0' ?>">
                        <input type="hidden" name="wiki_page_rev_id" value="<?=  isset($wiki_page_rev_id) ? $wiki_page_rev_id : '0' ?>">
                        {!! csrf_field() !!}
                        <div class=" col-sm-12 form-group">
                            <input type="button" class="btn pull-left black-btn cancel-edit" value="Cancel">
                            <button class="btn pull-right black-btn">Save Page</button>
                        </div>
                        <div class="clearfix"></div><br>
                    </form>
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

<link rel="stylesheet" type="text/css" href="{!! url('assets/plugins/editor/skins/markitup/style.css') !!}">
<link rel="stylesheet" type="text/css" href="{!! url('assets/plugins/editor/sets/wiki/style.css') !!}">
<script type="text/javascript" src="{!! url('assets/plugins/editor/jquery.markitup.js') !!}"></script>
<script type="text/javascript" src="{!! url('assets/plugins/editor/sets/wiki/set.js') !!}"></script>
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>

<script type="text/javascript">

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
                $(".back-link")[0].click();
            } 
        }
        else
        {
            $(".back-link")[0].click();
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
            url:'{!! url('wiki/edit')."/". $unit_id ."/". $slug  !!}',
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
                    //setTimeout(function(){ history.back() },1000);
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
