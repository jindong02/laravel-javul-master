<div class="col-sm-4 left-menu">
    <div class="btn-pref btn-group btn-group-justified btn-group-lg light_grey_bg" role="group" aria-label="...">
        <div class="btn-group left-button" role="group">
            <a href="{!! url('units') !!}" class="btn user-menu-left" style="border-bottom-left-radius: 0px;border-top-left-radius: 0px;
            border-right:1px solid #D3D3D3;">
                <i class="fa fa-stack-overflow"></i>
                <span class="hidden-x s">Units</span>
            </a>
        </div>
        <div class="btn-group left-button" role="group">
            <a href="{!! url('objectives') !!}" class="btn user-menu-left" style="border-right:1px solid #D3D3D3;padding-right:3px;
            padding-left:3px;">
                <i class="fa fa-bullseye"></i>
                <span class="hidden-x s" style="padding-left: 0px;">Objectives</span>
            </a>
        </div>
        <div class="btn-group left-button" role="group">
            <a href="{!! url('tasks') !!}" class="btn user-menu-left" style="border-right:1px solid #D3D3D3;">
                <i class="fa fa-pencil-square-o"></i>
                <span class="hidden-x s">Tasks</span>
            </a>
        </div>
        <div class="btn-group left-button" role="group">
            <a href="{!! url('issues') !!}" class="btn user-menu-left" style="border-bottom-right-radius: 0px;border-top-right-radius: 0px;">
                <i class="fa fa-bug"></i>
                <span class="hidden-x s">Issues</span>
            </a>
        </div>
    </div>
</div>
<div class="col-sm-8 right-menu">
    <div class="light_grey_bg">
        <a href="{!! url('units/add') !!}" class="btn user-menu-right widthMenu" style="width:20%;text-align:left">
            <i class="fa fa-plus" style="margin-right: 0px;"></i>
            <span class="hidden-x s">UNIT</span>
        </a>
        <a href="{!! url('activities') !!}" class="btn user-menu-right widthMenu" style="width:39%">
            <i class="fa fa-globe"></i>
            <span class="hidden-x s">GLOBAL ACTIVITY LOG</span>
        </a>
        <a href="{!! url('units') !!}" class="btn user-menu-right widthMenu pull-right" style="width:40%">
            <i class="fa fa-star"></i>
            <span class="hidden-x s">MOST ACTIVE: CONTRIBUTORS | UNITS</span>
        </a>

        <div class="btn-group pull-right hide" role="group">
            <a href="{!! url('my_watchlist') !!}" class="btn user-menu-right user-menu-right-icons">
                <i class="fa fa-eye font16pt pinkClr"></i>
            </a>
            <a href="{!! url('objectives') !!}" class="btn user-menu-right user-menu-right-icons">
                <i class="fa fa-credit-card font16pt pinkClr"></i>
            </a>
            <a href="{!! url('account') !!}" class="btn user-menu-right user-menu-right-icons">
                <i class="fa fa-user font16pt pinkClr"></i>
            </a>
        </div>
    </div>
</div>