@extends('layout.default')
@section('page-meta')
<title>Forum Thread: <?= $topic->title ?> - Javul.org</title>
@endsection
@section('page-css')
  <style type="text/css">
        .post-desc {
            margin-left: 24px;
        }
        .post-desc .up-down .glyphicon-arrow-up {
            margin: 0;
        }
        .post-desc .ideapoint  {
            cursor: pointer;
        }
        .post-desc .ideapoint.active {
            color: #fdb105;
        }
        .post-desc .up-down .count {
            margin: 0;
            text-align: center;
        }
        .post-desc .up-down {
            left: 18px;
        }
  </style>
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
                <div class="panel-heading">
                	<h4>Forum Thread: <?= $topic->title ?>
                     <?php if(isset($topic->objectLink)){ ?>
                        <a class="pull-right" href="<?= $topic->objectLink ?>"><?= $topic->objectLinkText ?></a>
                     <?php } ?>   
                    </h4>
                </div>
                <div class="panel-body list-group">
                 
                    <div class="list-group-item">
                        <div class="post-desc">
                            <div class="up-down">
                                <i data-value="1" data-id="<?= $topic->topic_id ?>" class="glyphicon <?= $topic->updownstatus == 1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-up"></i>
                                <i data-value="0" data-id="<?= $topic->topic_id ?>" class=" glyphicon <?= $topic->updownstatus == -1 ? 'active' : ''  ?> up-down-vote glyphicon-arrow-down"></i>
                            </div>
                            <b><a href="<?= $topic->link ?>" > <?= $topic->first_name ." " . $topic->last_name ?> </a></b>
                            <?= $topic->created_time ?> Point <span class="pointcount"><?= (int)$topic->votecount ?></span>
                            <i  data-id="<?= $topic->topic_id ?>" data-value="<?= (int)$topic->topicideapointstatus ?>" class="fa <?= (int)$topic->topicideapointstatus ? 'active' : '' ?> ideapoint fa-lightbulb-o"></i>
                             <span class="ideacount"> <?= (int)$topic->idepointcount ?> </span>
                            <br>
                            <?= $topic->desc ?>
                        </div>
                        <hr>
                        <div class="post-placeholder">
                        
                        </div>
                        <form role="form" method="post" class="post-form" id="form_topic_form"  enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            
                            <div class="form-group">
                                <textarea class="form-control summernote" name="post"></textarea>
                            </div>
                            <input type="hidden" name="reply_id" value="0">
                            <input type="hidden" name="topic_id" value="{!! $topic_id !!}">
 
                            <div class="col-sm-12 form-group">
                                <button type="submit" class="btn black-btn pull-right">Submit Reply</button>
                            </div>
                        </form>
                        <div class="clearfix"></div>
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
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    $(".post-desc").delegate(".ideapoint","click",function(){
        $this = $(this);
        var topic_id  = $this.attr("data-id");
        var val  = $this.attr("data-value");
        $.ajax({
            type:'post',
            url:'{!! url('forum/post_ideapoint') !!}',
            data:{
                _token : $("#form_topic_form").find("input[name=_token]").val(),
                val : val,
                topic_id : topic_id,
            },
            dataType:'json',
            beforeSend:function(){
                $this.toggleClass("active",'');
            },
            success:function(json){
              
                $this.attr("data-value", json['val']);
                var count = Number( $this.parents(".post-desc").find(".ideacount").text());
                $this.parents(".post-desc").find(".ideacount").text( count + (Number(json['val']) == 1 ? 1 : -1) ) ;
                
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
    });
    $(".post-desc").delegate(".up-down-vote","click",function(){
        $this = $(this);
        var topic_id  = $this.attr("data-id");
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
                    $this.parents(".post-desc").find(".pointcount").html(json['count']);
                }
                
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
    });
</script>
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

    function CKupdate(){
        for ( instance in CKEDITOR.instances ){
            CKEDITOR.instances[instance].setData('');
        }
    }

    var xhr;
    $(".panel-body").delegate(".post-form","submit",function(){
        $this = $(this);
    	if(xhr && xhr.readyState != 4){
            xhr.abort();
        }
        $this.find(".alert").remove();
        xhr = $.ajax({
            type:'post',
            url:'{!! url('forum/postSubmit') !!}',
            data:$(this).serialize(),
            dataType:'json',
            beforeSend:function(){
            	$this.find("button[type=submit]").button("loading");
            },
            error:function(){
                
            },
            complete:function(){
            	$this.find("button[type=submit]").button("reset");
            },
            success:function(json){
                if(json['errors']){
                	$.each(json['errors'],function(i,j){
                		$("[name='"+ i +"']").after("<div class='alert alert-danger'> "+ j +" </div>");
                	})
                } else {
                    if ($this.attr("id") != "form_topic_form") {
                        var html = renderHtml(json['post']['items'], $this.serializeArray(), $(".loader"), $this.parents("li:first"), false);
                        $(".loader"), $this.parents("li:first").append(html);
                    }
                    else {
                        var html = renderHtml(json['post']['items'], $this.serializeArray(), $(".loader"), $(".post-placeholder"), true);
                        $(".loader"), $(".post-placeholder").append(html);
                    }
                    if (json['success']) {
                        toastr['success'](json['success'], '');
                        if (Number(json['post']['items'][0]['reply_id'])) {
                            $this.remove();
                        }
                        CKupdate();
                    }
                    if (json['error']) {
                        toastr['error'](json['error'], '');
                    }
                }

                $this.find("button[type=submit]").button("reset");
            }
        });

        var reply_id = $(this).find('[name=reply_id]').val();
        $('.tool').find('[data-reply=' + reply_id + ']').show();

        return false;
    });
    function loadTopic(data,$input,$placeholder){
        data['_token'] = $("#form_topic_form").find("input[name=_token]").val();
        $.ajax({
            type:'post',
            url:'{!! url('forum/postLoad') !!}',
            data:data,
            dataType:'json',
            beforeSend:function(){
                $input.button("loading");
            },
            error:function(){
                
            },
            complete:function(){
                $input.button("reset");
            },
            success:function(json){
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
                
                var html = renderHtml(json['post'],data,$input,$placeholder);
                html += json['paginate'];
                $placeholder.append(html);
            }
        });
    }
    function renderHtml(json,data,$input,$placeholder,IsPrepend){
        var html = '';
        if(json){
            $.each(json,function(i,j){
                html += '<ul class="posts">';
                    html += '<li class="post-div" data-id="'+ j['post_id'] +'">';
                    html += '       <div class="up-down">';
                    html += '           <i data-value="1" class="glyphicon '+ (j['updown'] == 1 ? 'active' : '') +' up-down-vote glyphicon-arrow-up"></i>';
                    html += '           <i data-value="0" class="glyphicon '+ (j['updown'] == -1 ? 'active' : '') +' up-down-vote glyphicon-arrow-down"></i>';
                    html += '       </div>';
                    html += '    <div class="heading"><a href="'+ j['link'] +'">';
                    html +=          j['first_name'] + " "+  j['last_name'];
                    html += '        </a><span class="date">'+  j['created_time'] + '</span>';
                    html += '        <span class="point">'+  j['updownpoint'] + ' points</span>  ';
                    html += '        <span class="idea-point"><i data-value="'+ j['ideapoint'] +'" class="fa ideapoint '+ (j['ideapoint'] == 1 ? 'active' : '') +'  fa-lightbulb-o"></i><span class="count">'+ j['ideascore'] +'</span></span>';
                    html += '    </div>';
                    html += '    <div class="post-body">';
                    html +=          j['post'];
                    html += '       <div class="tool">';
                    html += '           <a href="javascript:void(0)" data-reply="'+ j['post_id'] +'" > Reply </a>';
                    html += '       </div>';
                    html += '    </div>';
                    if(j['reply']){
                        html += renderHtml(j['child']['items'],data,$input,$placeholder,IsPrepend);
                    }
                    html += '</li>';
                html += '</ul>';
            });
        }
        /*if(data['parent'] == '0'){
            $('[data-parent="0"]').parents("li").remove();
            if(json['left']){
                html += "<li class='loadmore' ><a href='javascript:void(0)' data-parent='0' data-page='"+ (Number(data['page']) + 1) +"' > Load more post </a> ("+ json['left'] +" post) </li>";
            }
        }
        else
        {
            $placeholder.find(".loadmore:first").remove();
            if(json['left']){
                html += "<li class='loadmore' ><a href='javascript:void(0)' data-parent='0' data-page='"+ (Number(data['page']) + 1) +"' > Load more post </a> ("+ json['left'] +" post) </li>";
            }
        }*/

        return html;
    }
    
    $(".post-placeholder").delegate(".cancel-reply","click",function(){
        $(this).parents(".tool").find('[data-reply]').show();
        $(this).parents("form").remove();
    });
    $(".post-placeholder").delegate(".up-down-vote","click",function(){
        $this = $(this);
        var post_id  = $this.parents("li:first").attr("data-id");
        var val  = $this.attr("data-value");
        $.ajax({
            type:'post',
            url:'{!! url('forum/postUpDown') !!}',
            data:{
                _token : $("#form_topic_form").find("input[name=_token]").val(),
                val : val,
                post_id : post_id,
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
                $this.parents("li.post-div").find(".heading .point").text( json['point'] + " Points" );
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
    });
    $(".post-placeholder").delegate(".ideapoint","click",function(){
        $this = $(this);
        var post_id  = $this.parents("li:first").attr("data-id");
        var val  = $this.attr("data-value");
        data['_token'] = $("#form_topic_form").find("input[name=_token]").val();
        $.ajax({
            type:'post',
            url:'{!! url('forum/ideapoint') !!}',
            data:{
                _token : $("#form_topic_form").find("input[name=_token]").val(),
                val : val,
                post_id : post_id,
            },
            dataType:'json',
            beforeSend:function(){
                $this.toggleClass("active",'');
            },
            success:function(json){
              
                $this.attr("data-value", json['val']);
                var count = Number( $this.parents(".idea-point").find(".count").text());
                $this.parents(".idea-point").find(".count").text( count + (Number(json['val']) == 1 ? 1 : -1) ) ;
                
                if(json['error']){
                    toastr['error'](json['error'], '');
                }
            }
        });
    });
    $(".post-placeholder").delegate("[data-reply]","click",function(){
        $this = $(this);
        var id  = $this.attr("data-reply");
        html =  '';
        html += '<form role="form" class="post-form" method="post" id="reply-'+ id +'" enctype="multipart/form-data">';
        html += '    {!! csrf_field() !!}';
        html += '    ';
        html += '    <div class="form-group">';
        html += '        <textarea class="form-control summernote" name="post"></textarea>';
        html += '    </div>';
        html += '    <input type="hidden" name="topic_id" value="{!! $topic_id !!}">';
        html += '    <input type="hidden" name="reply_id" value="'+ id +'">';
        html += '    <div class="pull-right form-group">';
        html += '        <button type="submit" class="btn black-btn">Submit Reply</button>';
        html += '        <button type="button" class="btn black-btn cancel-reply">Cancel</button>';
        html += '    </div>';
        html += '    <div class="clearfix"></div>';
        html += '</form>';
        $("#reply-" + id).remove();
        $this.before(html);

        $("#reply-" + id + " .summernote" ).ckeditor();

        $(this).hide();
    });
    $(".post-placeholder").delegate(".loadmore","click",function(){
        $this = $(this);
        var page = $this.find("a").attr("data-page");
        var parent = $this.find("a").attr("data-parent");
        var data = {};
        data['topic_id'] = '{!! $topic_id !!}';
        data['page'] = page;
        data['parent'] = parent;
        $placeholder = $this.parents("ul:first");
        if(parent == 0){
            $placeholder = $(".post-placeholder");
            $this.remove();
        }
        loadTopic(data,$this,$placeholder);
        
    })
    var data = {};
    data['topic_id'] = '{!! $topic_id !!}';
    data['page'] = '<?= isset($_GET["page"]) ? (int)$_GET["page"] : 0 ?>';
    data['parent'] = 0;
    loadTopic(data,$(".loader"),$(".post-placeholder"));
</script>
@endsection
