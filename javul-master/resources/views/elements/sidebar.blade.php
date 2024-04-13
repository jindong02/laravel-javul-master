<?php $userAuth = \App\User::getAll();?>
<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
<!-- BEGIN SIDEBAR -->
<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
    <!-- BEGIN SIDEBAR MENU -->
    <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
    <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
    <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
    <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <li class="sidebar-toggler-wrapper hide">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler"> </div>
                <!-- END SIDEBAR TOGGLER BUTTON -->
            </li>
            <li class="nav-item start @if(empty($active_menu)) active open @endif">
                <a href="{!! url() !!}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">{{trans('messages.dashboard')}}</span>
                    @if(empty($active_menu))
                        <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>
            @if($userAuth->role != "tenants")
                @if($userAuth->role == "admin")
                <li class="nav-item  @if($active_menu =='landlord') active open @endif ">
                    <a href="{{url('landlord')}}" class="nav-link nav-toggle">
                        <i class="icon-puzzle"></i>
                        <span class="title">{{trans('messages.landlord_manager')}}</span>
                        @if($active_menu =='landlord')
                            <span class="selected"></span>
                        @endif
                        <span class="arrow"></span>
                    </a>
                </li>
                @endif
            <li class="nav-item @if($active_menu =='properties') active @endif">
                <a href="{!! url('properties') !!}" class="nav-link nav-toggle">
                    <i class="icon-bulb"></i>
                    <span class="title">{{trans('messages.properties')}}</span>
                    @if($active_menu =='properties')
                        <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>
            <li class="nav-item  @if($active_menu =='tenants') active @endif">
                <a href="{!! url('tenants') !!}" class="nav-link nav-toggle">
                    <i class="icon-wallet"></i>
                    <span class="title">{{trans('messages.tenants')}}</span>
                    @if($active_menu =='tenants')
                        <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>

            <li class="nav-item  @if($active_menu =='announcements') active @endif">
                <a href="{!! url('announcements') !!}" class="nav-link nav-toggle">
                    <i class="icon-pointer"></i>
                    <span class="title">{{trans('messages.announcements')}}</span>
                    @if($active_menu =='announcements')
                    <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>
            @endif

            @if($userAuth->role != "tenants")
            <li class="nav-item  @if($active_menu =='reports') active @endif">
                <a href="{!! url('reports') !!}" class="nav-link nav-toggle">
                    <i class="icon-bar-chart"></i>
                    <span class="title">{{trans('messages.reports')}}</span>
                    @if($active_menu =='reports')
                    <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>
            @endif

            @if($userAuth->role == "admin")
            <li class="nav-item  @if($active_menu =='api') active @endif">
                <a href="{{url('api')}}" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">{{trans('messages.api')}}</span>
                    @if($active_menu =='api')
                    <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            <li class="nav-item  @if($active_menu =='account') active @endif">
                <a href="{!! url('account') !!}" class="nav-link nav-toggle">
                    <i class="icon-user"></i>
                    <span class="title">{{trans('messages.account')}}</span>
                    @if($active_menu =='account')
                    <span class="selected"></span>
                    @endif
                    <span class="arrow"></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{!! url('account/logout') !!}" class="nav-link nav-toggle">
                    <i class="icon-key"></i>
                    <span class="title">{{trans('messages.logout')}}</span>
                    <span class="arrow"></span>
                </a>
            </li>
        </ul>
    <!-- END SIDEBAR MENU -->
    <!-- END SIDEBAR MENU -->
    </div>
<!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->