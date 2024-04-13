@extends('layout.default')
@section('page-meta')
<title>New Message - Javul.org</title>
@endsection
@section('page-css')
<style>
    .related_para{margin:0 0 10px;}
    .custom-menu {
        display: none;
        z-index: 1000;
        position: absolute;
        overflow: hidden;
        border: 1px solid #CCC;
        white-space: nowrap;
        font-family: sans-serif;
        background: #FFF;
        color: #333;
        border-radius: 5px;
        padding: 0;
    }
    /* Each of the items in the list */
    .custom-menu li {
        padding: 8px 12px;
        cursor: pointer;
        list-style-type: none;
        transition: all .3s ease;
    }
    .custom-menu li:hover {
        background-color: #DEF;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="panel panel-grey panel-default">
                <div class="panel-heading">
                    New Message
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-2">
                        @include('message.menu',array())
                    </div>
                    <div class="col-md-10">
                        <form role="form" method="post" id="form_topic_form"  enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            
                            <br>
                            <?php if($user_id > 0){ ?>
                                <input type="hidden" name="user_id" value="{!! $user_id !!}">
                            <?php }else{ ?>
                                <div class="col-sm-12 form-group">
                                    <label>To</label>
                                    <select  id="user_id_fromSel2" name="user_id" class="form-control" >
                                        <?php foreach ($user as $key => $value) { ?>
                                            <option value="<?= $value->id ?>">
                                                <?= $value->first_name ?> <?= $value->last_name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <div class="col-sm-12 form-group">
                                    <label>Subject</label>
                                    <input  name="subject" class="form-control" >
                                </div>
                            <div class="col-sm-12 form-group">
                                <label class="control-label">Message</label>
                                <textarea class="form-control summernote" rows="5" name="message"></textarea>
                            </div>
                            <div class="col-sm-12 form-group">
                                <button class="btn black-btn pull-right">Send Message</button>
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
</script>
<script type="text/javascript">
    var xhr;
    $("#form_topic_form").submit(function(){
        if(xhr && xhr.readyState != 4){
            xhr.abort();
        }
        $("#form_topic_form").find(".alert").remove();
        xhr = $.ajax({
            type:'post',
            url:'{!! url('message/send') !!}/{!! $user_id !!}',
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
                    $("#form_topic_form textarea").val('');
                    $("#form_topic_form input").val('');
                   // setTimeout(function(){ location = json['location'] },1000);
                }
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
        return false;
    })
</script>

<script>
    $('#user_id_fromSel2').select2({
        minimumInputLength: 2
    });
</script>
@endsection
