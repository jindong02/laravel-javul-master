    @extends('layout.default')
@section('page-meta')
<title>Tasks - Javul.org</title>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'tasks'])
    </div>
    <div class="row form-group">
        <div class="col-md-8 col-md-push-4">
            <div class="panel panel-default panel-grey">
                <div class="panel-heading">
                    <h4>Tasks</h4>
                </div>
                <div class="panel-body table-inner table-responsive loading_content_hide">
                    <div class="loading_dots task_loading" style="position: absolute;top:20%;left:43%;z-index: 9999;display: none;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <table class="table table-striped tasks-table" style="overflow:hidden !important; ">
                        <thead>
                        <tr>
                            <td colspan=7 class="tasks_search_td">
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <select name="task_skill_search" class="form-control" id="task_skill_search"></select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select name="task_status_search" class="form-control" id="task_status_search">
                                            <option value="">Search by status</option>
                                            <?php $task_status = App\Models\SiteConfigs::task_status(); asort($task_status); ?>
                                            @foreach($task_status as $index=>$status)
                                                <option value="{{$index}}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div style="display:inline-block;position:relative;top:-2px">
                                    <a class="btn black-btn form-control search_tasks" data-token="{{csrf_token()}}">Search</a>

                                    <a class="btn black-btn form-control reset_unit_search" href="{!! url('tasks') !!}"
                                       style="display:none">Reset</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Task Name</th>
                            <th>Objective Name</th>
                            <th>Unit Name</th>
                            <th>Skills</th>
                            <th>Assigned to</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($tasks) > 0 )
                            @foreach($tasks as $task)
                                @include('tasks.partials.task_listing',['task'=>$task])
                            @endforeach
                        @else
                        <tr>
                            <td colspan="7">No record(s) found.</td>
                        </tr>
                        @endif
                        <tr style="background-color: #fff;text-align: right;">
                            <td colspan="7" >
                                <!-- <a href="{!! url('tasks/add')!!}"class="btn black-btn" id="add_task_btn" type="button">
                                    <i class="fa fa-plus plus"></i> <span class="plus_text">Add Task</span>
                                </a> -->

                                @if($tasks->lastPage() > 1 && $tasks->lastPage() != $tasks->currentPage())
                                    <a href="#" data-url="{{$tasks->url($tasks->currentPage()+1) }}" class="btn more-black-btn more-tasks"
                                       id="add_unit_btn"
                                       type="button">
                                        MORE TASKS <span class="more_dots">...</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-md-4 col-md-pull-8">
            <div class="left">
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
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script src="{!! url('assets/plugins/jquery.ThreeDots.min.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    var msg_flag ='{{ $msg_flag }}';
    var msg_type ='{{ $msg_type }}';
    var msg_val ='{{ $msg_val }}';
    $(function(){
        var the_obj = $('.text_wraps').ThreeDots({
            max_rows: 1
        });
    })
</script>
<script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/tasks/delete_task.js') !!}"></script>
@endsection
