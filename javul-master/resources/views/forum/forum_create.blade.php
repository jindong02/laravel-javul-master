@extends('layout.default')
@section('page-meta')
<title>Create New Thread - Javul.org</title>
@endsection
@section('page-css')
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
                	Create New Thread
                </h4></div>
                <div class="panel-body list-group">
                    <div class="list-group-item">
                        <form role="form" method="post" id="form_topic_form"  enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="col-sm-12 form-group">
	                            <label class="control-label">Title</label>
	                            <div class="input-icon right">
	                                <i class="fa"></i>
	                                <input type="text" name="title" value="" class="form-control" placeholder="title"/>
	                            </div>
	                        </div>
	                        <div class="col-sm-12 form-group">
                                <label class="control-label">Content</label>
                                <textarea class="form-control summernote" name="desc"></textarea>
                            </div>
                            <input type="hidden" name="unit_id" value="{!! $unit_id !!}">
                            <input type="hidden" name="section_id" value="{!! $section_id !!}">
                            <div class="col-sm-12 form-group">
                            	<button class="btn black-btn pull-right">Submit New Thread</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
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

    var xhr;
    $("#form_topic_form").submit(function(){
    	if(xhr && xhr.readyState != 4){
            xhr.abort();
        }
        $("#form_topic_form").find(".alert").remove();
        xhr = $.ajax({
            type:'post',
            url:'{!! url('forum/submit') !!}',
            data:$(this).serialize(),
            dataType:'json',
            beforeSend:function(){
            	$("#form_topic_form button").button("loading");
            },
            error:function(){
                
            },
            complete:function(){
            	$("#form_topic_form button").button("reset");
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
