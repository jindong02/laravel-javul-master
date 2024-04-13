<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('pageTitle') Javul.org</title>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="{!! url('favicon.ico') !!}" type="image/icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <link href="//fonts.googleapis.com/css?family=Roboto:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/bootstrap-toastr/toastr.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/select2/css/select2.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/select2/select2-bootstrap.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" type="text/css" />

    <link href="{!! url('assets/css/style.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! url('assets/css/component.css') !!}" rel="stylesheet" type="text/css" />

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link href="{!! url('assets/plugins/tooltipster-master/dist/css/tooltip.custom.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <link rel="apple-touch-icon" sizes="57x57" href="{!! url('assets/images/apple-icon-57x57.png') !!}">
    <link rel="apple-touch-icon" sizes="60x60" href="{!! url('assets/images/apple-icon-60x60.png') !!}">
    <link rel="apple-touch-icon" sizes="72x72" href="{!! url('assets/images/apple-icon-72x72.png') !!}">
    <link rel="apple-touch-icon" sizes="76x76" href="{!! url('assets/images/apple-icon-76x76.png') !!}">
    <link rel="apple-touch-icon" sizes="114x114" href="{!! url('assets/images/apple-icon-114x114.png') !!}">
    <link rel="apple-touch-icon" sizes="120x120" href="{!! url('assets/images/apple-icon-120x120.png') !!}">
    <link rel="apple-touch-icon" sizes="144x144" href="{!! url('assets/images/apple-icon-144x144.png') !!}">
    <link rel="apple-touch-icon" sizes="152x152" href="{!! url('assets/images/apple-icon-152x152.png') !!}">
    <link rel="apple-touch-icon" sizes="180x180" href="{!! url('assets/images/apple-icon-180x180.png') !!}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{!! url('assets/images/android-icon-192x192.png') !!}">
    <link rel="icon" type="image/png" sizes="32x32" href="{!! url('assets/images/favicon-32x32.png') !!}">
    <link rel="icon" type="image/png" sizes="96x96" href="{!! url('assets/images/favicon-96x96.png') !!}">
    <link rel="icon" type="image/png" sizes="16x16" href="{!! url('assets/images/favicon-16x16.png') !!}">
    @yield('page-css')
    <!--<script type="text/javascript" src="//js.stripe.com/v2/"></script>
    <script type="text/javascript">Stripe.setPublishableKey('{{env("STRIPE_SECRET")}}');</script>-->

{{--    {!! \Analytics::render() !!}--}}
</head>
    <body>
        @include('elements.header')
        <div id="loadingDiv" style="display: none;"><img id="loading" src="{!! url('assets/images/loader.gif') !!}" alt="" /></div>
        <!-- BEGIN LOGIN -->
        <div class="content">
             @yield('content')
        </div>
        <script>
            var siteURL = '{!! url('') !!}';
            var login = '{{ \Auth::check() }}';
            // comes from app/providers/ViewComposerServiceProvider.php Line no:- 53 to 57
{{--            var user_messages = JSON.parse('{!! $user_messages !!}');--}}

        </script>
        <!--[if lt IE 9]>
        <script src="{!! url('assets/plugins/respond.min.js') !!}"></script>
        <script src="{!! url('assets/plugins/excanvas.min.js') !!}"></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{!! url('assets/plugins/jquery.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/bootstrap-datetimepicker/moment.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/bootstrap-datetimepicker/transition.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/bootstrap-datetimepicker/collapse.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/bootstrap/js/bootstrap.min.js') !!}" type="text/javascript"></script>
        <script type="text/javascript" src="{!! url('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')!!}"></script>
        <!--Tooltipster-->
        <script type="text/javascript" src="{!! url('assets/plugins/tooltipster-master/dist/js/tooltip.custom.js')!!}   "></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{!! url('assets/plugins/jquery-validation/js/jquery.validate.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/jquery-validation/js/additional-methods.min.js') !!}" type="text/javascript"></script>
        <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
        <script src="{!! url('assets/plugins/select2/js/select2.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/bootstrap-toastr/toastr.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/bootbox/bootbox.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/ckeditor/ckeditor.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/plugins/ckeditor/adapters/jquery.js') !!}" type="text/javascript"></script>


        <script src="{!! url('assets/js/app.min.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/js/function.js') !!}" type="text/javascript"></script>
        <script src="{!! url('assets/js/user_messages.js') !!}" type="text/javascript"></script>
        <script>
            var logged_in ='{{Auth::check()?true:false}}';
            //showing toast message for global search query
            var toast_msg_val = '@if(Session::has("msg_val")) {!! trim(Session::get("msg_val")) !!} @endif';
            if($.trim(toast_msg_val) !== ''){
                showToastMessage(toast_msg_val);
            }

            $(function(){
                if(logged_in) {
                    $('[data-toggle="popover"]').popover({
                        placement: 'bottom',
                        html: true,
                        container: 'body',
                        title: 'Notification',
                        content: function () {
                            return $.ajax({
                                url: '{!! url('account/get_notifications') !!}',
                                dataType: 'html',
                                async: false
                            }).responseText;
                        }
                    }).data('bs.popover')
                            .tip()
                            .addClass('notification-popover');

                    $('[data-toggle="popover"]').on('shown.bs.popover', function () {
                        // do something…
                        $(".popover-content").find('.list-group').find(".list-group-item").each(function () {
                            if ($(this).is(':visible') == true) {
                                var id = $(this).data('id');
                                if ($.trim(id) != "") {
                                    $.ajax({
                                        url: '{!! url('account/update_notifications') !!}',
                                        data: {id: id, _token: '{{csrf_token()}}'},
                                        type: 'post',
                                        async: false,
                                        success: function (resp) {
                                            $(".notification_popover").find('i').removeClass('colorOrange ');
                                        }
                                    })
                                }
                            }
                        });
                        $(".div-table-second-cell").css('z-index', '100');
                        $(".list-item-main").css('z-index', '100');
                    });
                    $('[data-toggle="popover"]').on('hidden.bs.popover', function () {
                        // do something…
                        $(".div-table-second-cell").css('z-index', '99999');
                        $(".list-item-main").css('z-index', '99999');
                    });
                }else{
                    if($(".is_logged_in").length > 0){
                        window.location.reload(true);
                    }
                }

                $('span.tooltipster').tooltipster({ //find more options on the tooltipster page
                    position: 'right'
                });
                $('.left-button').each(function(index, item) {
                    $(item).find('.btn').css(
                        'width', 100 / $(item).find('.btn').length + '%'
                    )
                });



                window.onresize = function(event) {
                    $(".time_text").each(function(){
                        if($(this).height() > 19){
                            $(this).prev('.time_digit').css({'top':'0px','position':'relative'});
                        }
                        else
                            $(this).prev('.time_digit').css('top','0px');
                    });
                }

            })
        </script>

        <script type="text/javascript">
            <?php  if(\Auth::check()){ ?>
                setTimeout(function() {
                        check_user_login()
                    },30000
                );
                function check_user_login(){
                    $.ajax({
                        type:'get',
                        url:siteURL  + '/account/check_user_login',
                        dataType:'json',
                        success:function(json){
                            if(json.success){
                                setTimeout(function(){
                                    check_user_login()
                                },30000);
                            }
                        }
                    })
                }
                function unreadmsg() {
                    $.ajax({
                        type:'post',
                        url:siteURL  + '/inbox/new_msg',
                        data:{_token:'{{csrf_token()}}'},
                        dataType:'json',
                        complete: function(xhr, textStatus) {
                          setTimeout(function() {
                            unreadmsg();
                          }, 8000);
                        },
                        success:function(json){
                            if(parseInt( json['count'] ) > 0){
                                $("#unread-message").html(json['count']).fadeIn();
                                $('#alerts-notification').addClass('colorOrange');
                            }
                            else
                            {
                                $("#unread-message").append(0).fadeOut();
                            }
                        }
                    })
                }

                unreadmsg();
            <?php } ?>


            $(".container").delegate(".start-unit-chat","click",function(){
            $this = $(this);
            var unit_id = $this.attr("data-unit_id");

            $.ajax({
                type:'post',
                url:siteURL  + '/chat/create_room',
                data:{_token:'{{csrf_token()}}',unit_id:unit_id},
                dataType:'json',
                complete: function(xhr, textStatus) {
                   if(xhr.status == 401){
                        location = siteURL + "/login";
                   }
                },
                success:function(resp,text,xhr){

                    if(resp.location){
                       location = resp.location;
                    }
                    else{
                        showToastMessage('SOMETHING_GOES_WRONG');
                    }
                }
            })
        });
        </script>
        <!-- END PAGE LEVEL PLUGINS -->
        @yield('page-scripts')
    </body>
</html>
