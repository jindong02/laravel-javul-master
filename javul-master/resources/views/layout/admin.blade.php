<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"  @if(!empty(session('locale')) && session('locale') == "ar") dir="rtl" @endif>
<!--<![endif]-->
<!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>{{trans('messages.title')}}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <link rel="shortcut icon" href="{!! url('favicon.ico') !!}" type="image/icon">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="{{url('assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{url('assets/global/plugins/simple-line-icons/simple-line-icons.min.css')}}" rel="stylesheet" type="text/css" />
        @if(!empty(session('locale')) && session('locale') == "ar")
            <link href="{{url('assets/global/plugins/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet" type="text/css" />
            <link href="{{url('assets/global/plugins/bootstrap-switch/css/bootstrap-switch-rtl.min.css')}}" rel="stylesheet" type="text/css" />
        @else
            <link href="{{url('assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
            <link href="{{url('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css" />
        @endif
        <link href="{{url('assets/global/plugins/uniform/css/uniform.default.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{url('assets/global/plugins/bootstrap-toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />

        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="{{url('assets/global/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{url('assets/global/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        @if(!empty(session('locale')) && session('locale') == "ar")
            <link href="{{url('assets/global/css/components-rtl.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
            <link href="{{url('assets/global/css/plugins-rtl.min.css')}}" rel="stylesheet" type="text/css" />
            <link href="{{url('assets/global/css/layout-rtl.min.css')}}" rel="stylesheet" type="text/css" />
            <link href="{{url('assets/global/css/blue-rtl.min.css')}}" rel="stylesheet" type="text/css" id="style_color" />
            <link href="{{url('assets/global/css/custom-rtl.css')}}" rel="stylesheet" type="text/css" />
        @else
            <link href="{{url('assets/global/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
            <link href="{{url('assets/global/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />
            <link href="{{url('assets/global/css/layout.min.css')}}" rel="stylesheet" type="text/css" />
            <link href="{{url('assets/global/css/blue.min.css')}}" rel="stylesheet" type="text/css" id="style_color" />
            <link href="{{url('assets/global/css/custom.css')}}" rel="stylesheet" type="text/css" />
        @endif
        <!-- END THEME GLOBAL STYLES -->

        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
        @yield('page-css')
    </head>

    <!-- END HEAD -->


    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
        <div class="page-spinner-bar">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
        <div class="hide main_class_container">
            @include('elements.header')
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                @include('elements.sidebar')
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        @yield('content')
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
            <!-- END CONTENT -->
            </div>
            <!-- END CONTAINER -->
        </div>
        <!--[if lt IE 9]>
        <script src="{{url('plugins/respond.min.js')}}"></script>
        <script src="{{url('plugins/excanvas.min.js')}}"></script>
        <![endif]-->
        <script>
            var siteURL = "{{url()}}";
        </script>
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{url('assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/js.cookie.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/jquery.blockui.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/uniform/jquery.uniform.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{!! url('assets/global/plugins/bootbox/bootbox.min.js') !!}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/scripts/app.min.js')}}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="{{url('assets/js/layout.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/js/demo.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/js/quick-sidebar.min.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/global/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
        <script src="{!! url('assets/global/plugins/bootstrap-sessiontimeout/bootstrap-session-timeout.min.js') !!}" type="text/javascript"></script>
        <script src="{{url('assets/js/language/'.session("locale").'.js')}}" type="text/javascript"></script>
        <script>
            $(function(){
                $(".page-spinner-bar").addClass('hide');
                $('.main_class_container').removeClass('hide');

            })
            var SessionTimeout = function () {
                var handlesessionTimeout = function () {
                    $.sessionTimeout({
                        title: '{{trans("messages.session_time_out_notification")}}',
                        message: '{{trans("messages.your_session_about_to_expire")}}',
                        ajaxType:'POST',
                        keepAliveUrl:siteURL+'/account/check-auth',
                        redirUrl: siteURL+'/account/logout',
                        logoutUrl: siteURL+'/account/logout',
                        warnAfter: 900000, //warn after 15 mins
                        redirAfter: 1200000, //redirect after 20 secons,
                        countdownMessage: '{{trans("messages.redirecting_in")}} {timer} {{trans("messages.seconds")}}',
                        countdownBar: true
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handlesessionTimeout();
                    }
                };

            }();

            jQuery(document).ready(function() {
                SessionTimeout.init();
            });
        </script>
        @yield('page-scripts')
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>
</html>
