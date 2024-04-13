@extends('layout.default')
@section('page-meta')
<title>Chat - Javul.org</title>
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
<link rel="stylesheet" type="text/css" href="{!! url('assets/css/chat.css') !!}">
<ul class='custom-menu'>
  
</ul>
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
        
            <div class="chat-room">
                
                <div class="right">
                    <div class="top"><span> <i class="fa fa-comments-o"></i> Chat </span></div>
                    <div class="chat active-chat message-load" data-chat="person1">
                                                
                    </div>
                    
                    <div class="write  ">
                        <textarea id="emoji" class="hide"   ></textarea>
                        <div id="container_emoji" class="hide"  ></div>
                        <input id="chat-message" type="text"  />
                        <a href="javascript:;"  class="write-link smiley"><i class="fa fa-smile-o" aria-hidden="true"></i></a>
                        <a id="send-message" href="javascript:;" class="write-link send disabled"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>
                        <div class="emoji">
                            <?php foreach ($smily as $key => $value) { ?>
                                <?= $value ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="left">
                    <div class="top">
                        <input type="text" name="search" placeholder="User Search.." />
                        <i class="search-icon fa fa-search"></i>
                    </div>
                    <div class="filter-message" id="filterMessage"></div>
                    <ul class="people">
                                              
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script type="text/javascript"></script>
<script type="text/javascript">
        $('#chat-message').on('keyup', function() {
            if($(this).val() != '') {
                $('#send-message').removeClass('disabled');
            } else {
                $('#send-message').addClass('disabled');
            }
        });

        $(".chat-room").delegate('[contextmenu]',"contextmenu", function (event) {
            event.preventDefault();
            showMenu(event,$(this));
        });
        $(".chat-room").delegate('[contextmenu]',"click", function (event) {
            event.preventDefault();
            showMenu(event,$(this));
        });
        function showMenu(event,$this){
            var html = '\
            <li><a href="'+ $this.attr("data-profile") +'" > Profile </a></li>\
            <li><a href="{!! url("message/send") !!}/'+ $this.attr("data-id") +'" > Private Message </a></li>';

            if($this.attr("data-id") == {{ Auth::user()->id }}) {
                html = '<li><a href="'+ $this.attr("data-profile") +'" > My Profile </a></li>';
            }
            $(".custom-menu").finish().toggle(100).css({
                top: event.pageY + "px",
                left: event.pageX + "px"
            }).html(html);
        }
        $(document).bind("mousedown", function (e) {
            if (!$(e.target).parents(".custom-menu").length > 0) {
                $(".custom-menu").hide(100);
            }
        });
        $(".custom-menu").delegate("li","click",function(){
            $this = $(this);
            switch($this.attr("data-action")) {
                case "chat":
                    var userId = $this.attr("data-id");
                    alert(userId);
                break;
            }
            $(".custom-menu").hide(100);
        });
        $(".right .send").click(function(event){
            chat.sendmsg();
        });
        $(".right .emoji").click(function(event){
            event.stopPropagation();
        });
        $(".write .smiley").click(function(event){
            event.stopPropagation();
            $(".write input").focus();
            $(".right .emoji").fadeIn();
        });
        $(document).click(function(){
            $(".right .emoji").fadeOut();
        });
        $(".write input").focus();
        var chat = {
            init : function(roomId,user_id) {
                this.room = roomId;
                this.user_id = user_id;
                this.lastId = 0;
                this.input = ".write input";
            },
            loaduser : function(){
                 $this = this;
                $.ajax({
                    type:'post',
                    url:'{!! url('chat/loaduser') !!}',
                    data:{_token:'{{csrf_token()}}',roomId:this.room},
                    dataType:'json',
                    beforeSend:function(){
                    },
                    complete:function(){
                    },
                    success:function(json){
                        $this.loaduserHtml(json['members']);
                    }
                })
            },
            loaduserHtml:function(json){
                var html = '';
                $.each(json,function(i,j){
                    if(j['name'].toUpperCase().indexOf($(".chat-room input[name=search]").val().toUpperCase()) != -1){
                        html += '<li class="person" data-id="'+ j['user_id'] +'" data-profile="'+ j['link'] +'" contextmenu data-chat="person1">';
                        html += '    <div class="img"  >'+ j['name'].charAt(0) +'</div>';
                        html += '    <span class="name">'+ j['name'] +'</span>';
                        html += '</li>';
                    }
                });
                $(".left .people").html(html);
            },
            sendmsg: function(){
                $this = this;
                var message = $.trim($(this.input).val());
                if(message != ''){
                    $.ajax({
                        type:'post',
                        url:'{!! url('chat/sendmsg') !!}',
                        data:{_token:'{{csrf_token()}}',roomId:this.room,message:message},
                        dataType:'json',
                        beforeSend:function(){
                            $($this.input).prop("readonly",true);
                        },
                        complete:function(){
                            $($this.input).prop("readonly",false);
                        },
                        success:function(json){
                            if(json['success']){
                                $($this.input).val('');
                                $this.loadmsg(false);
                            }
                            else
                            {
                                showToastMessage('SOMETHING_GOES_WRONG');
                            }
                        }
                    })
                }
            },
            loadmsg: function(reCall){
                $this = this;
                if(this.xhr && this.xhr.readyState != 4){
                    this.xhr.abort();
                }
                this.xhr = $.ajax({
                    type:'post',
                    url:'{!! url('chat/loadmsg') !!}',
                    data:{_token:'{{csrf_token()}}',roomId:this.room,lastId:this.lastId,loaduser:true},
                    dataType:'json',
                    beforeSend:function(){
                    },
                    error:function(){
                        setTimeout(function(){ $this.loadmsg(); }, 10000);
                    },
                    complete:function(){
                    },
                    success:function(json){
                        var html = '';
                        if(json['messages']){
                            $.each(json['messages'],function(i,j){
                                if( Number($this.lastId) <= Number(j['id']) ){
                                    $this.lastId = Number(j['id']);
                                }
                                var classs = "you";
                                if($this.user_id == j['user']) classs = "me";
                                html += '<div class="bubble '+ classs +'" data-id="'+ j['id'] +'">';
                                html += "<b contextmenu  data-id='" + j['user'] +"' data-profile='"+ j['link'] +"' >" + j['name'] + "</b><br> " + j['body'];
                                html += '<span class="time">'+ j['time'] +'</span>';
                                html += '</div>';
                            });
                        }
                        $this.loaduserHtml(json['members']);
                        if(reCall){
                            setTimeout(function(){ $this.loadmsg(true); }, 5000);
                        }
                        if(html != ''){
                            $(".message-load").append(html);
                            if(html != '') $('.message-load').animate({scrollTop: $('.message-load').prop("scrollHeight")}, 500);
                        }
                    }
                })
                
            },
            getid: function(){
                console.log(this.room);
            }
        }
        chat.init("<?= $roomId ?>",<?= $user_id ?>);
        chat.loaduser();
        chat.loadmsg(true);
        $.fn.EnableInsertAtCaret = function() {
            $(this).on("focus", function() {        
                $(".insertatcaretactive").removeClass("insertatcaretactive");
                 $(this).addClass("insertatcaretactive");
            });
        };
        function insert_smiley(text) {
            $(".write input").EnableInsertAtCaret();
            InsertAtCaret(text);
            $(".write input").focus();
        }
        function InsertAtCaret(myValue) {
 
            return $(".insertatcaretactive").each(function(i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                } else if (this.selectionStart || this.selectionStart == '0') {
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }

                $('#send-message').removeClass('disabled');
            })
        }
        $(".write input").keypress(function (e) {
            var key = e.which;
            if(key == 13)
            {
                chat.sendmsg();
                return false;  
            }
        });  
        $(".chat-room .left input[name=search]").keyup(function(){
            var txt = $(".chat-room input[name=search]").val();

            if(txt.length > 0) {
                $('#filterMessage').text('Filtered by name: ' + txt);
            } else {
                $('#filterMessage').text('');
            }

            $("ul.people").find("li").each(function(){
                if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
                    $(this).show();
                }
                else
                {
                    $(this).hide();
                }
            });
        }) 
    //})
</script>
@endsection
