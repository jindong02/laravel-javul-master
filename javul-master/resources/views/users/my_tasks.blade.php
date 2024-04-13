@extends('layout.default')
@section('page-meta')
<title>My Tasks - Javul.org</title>
@endsection
@section('content')

<div class="container">
<div class="row form-group" style="margin-bottom: 15px;">
    @include('elements.user-menu',array('page'=>'home'))
</div>
<div class="row form-group" >
    <div class="col-md-4">
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
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default panel-grey">
                    <div class="panel-heading">
                        <h4>My Bids</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Bid Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($myBids) > 0 && !empty($myBids))
                                @foreach($myBids as $bid)
                                    <tr>
                                        <td>
                                            <a href="{!! url('tasks/'.$taskIDHashID->encode($bid->task_id).'/'.$bid->slug)!!}">
                                                {{$bid->name}}
                                            </a>
                                        </td>
                                        <td><a class="show_bid_details" data-id="{{$bid->id}}">Show Details</a></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">
                                        No record found.
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default panel-grey">
                    <div class="panel-heading">
                        <h4>My Assigned Task</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($myAssignedTask) > 0)
                                @foreach($myAssignedTask as $assigned_task)
                                    <tr>
                                        <td>
                                            <a href="{!! url('tasks/'.$taskIDHashID->encode($assigned_task->id).'/'.$assigned_task->slug)!!}">
                                                {{$assigned_task->name}}
                                            </a>
                                        </td>
                                        <td>{{\App\SiteConfigs::task_status($assigned_task->status)}}</td>
                                        <td>
                                            <a href="{!! url('tasks/complete_task/'.$taskIDHashID->encode($assigned_task->id)) !!}"
                                               class="btn btn-xs btn-success" >
                                                Complete Task
                                            </a>
                                            <a href="{!! url('tasks/cancel_task/'.$taskIDHashID->encode($assigned_task->id)) !!}"
                                               class="btn btn-xs btn-danger" >
                                                Cancel Task
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3">
                                        No record found.
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
<!-- only super admin can evaluate the completed task -->
@if($authUserObj->role == "superadmin")
    <div class="row">
        <div class="col-sm-6 form-group">
            <div class="panel panel-default panel-grey">
                <div class="panel-heading">
                    <h4>Task Evaluation</h4>
                </div>
                <div class="panel-body table-inner table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Completed By</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($myEvaluationTask) > 0)
                        @foreach($myEvaluationTask as $completed_task)
                        <tr>
                            <td>
                                <a href="{!! url('tasks/'.$taskIDHashID->encode($completed_task->task_id).'/'.$completed_task->slug)!!}">
                                    {{$completed_task->name}}
                                </a>
                            </td>
                            <td>
                                <a href="{!! url('userprofiles/'.$userIDHashID->encode($completed_task->user_id).'/'.strtolower
                                ($completed_task->first_name.'_'.$completed_task->last_name)) !!}">
                                    {{$completed_task->first_name.' '.$completed_task->last_name}}
                                </a>
                            </td>
                            <td>{{\App\SiteConfigs::task_status($completed_task->status)}}</td>
                            <td>
                                <a href="{!! url('tasks/complete_task/'.$taskIDHashID->encode($completed_task->task_id)) !!}"
                                   class="btn btn-xs btn-success mark-complete" >
                                    Mark as Complete
                                </a>
                                <a href="{!! url('tasks/complete_task/'.$taskIDHashID->encode($completed_task->task_id)) !!}"
                                   class="btn btn-xs btn-danger re-assigned" >
                                    Re Assign
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4">
                                No record found.
                            </td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 form-group">
            <div class="panel panel-default panel-grey">
                <div class="panel-heading">
                    <h4>Task Cancellation</h4>
                </div>
                <div class="panel-body table-inner table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Cancelled By</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($myCancelledTask) > 0)
                        @foreach($myCancelledTask as $cancel_task)
                        <tr>
                            <td>
                                <a href="{!! url('tasks/'.$taskIDHashID->encode($cancel_task->task_id).'/'.$cancel_task->slug)!!}">
                                    {{$cancel_task->name}}
                                </a>
                            </td>
                            <td>
                                <a href="{!! url('userprofiles/'.$userIDHashID->encode($cancel_task->user_id).'/'.strtolower
                                ($cancel_task->first_name.'_'.$cancel_task->last_name)) !!}">
                                    {{$cancel_task->first_name.' '.$cancel_task->last_name}}
                                </a>
                            </td>
                            <td>{{\App\SiteConfigs::task_status($cancel_task->status)}}</td>
                            <td>
                                <a href="{!! url('tasks/cancel_task/'.$taskIDHashID->encode($cancel_task->task_id)) !!}"
                                   class="btn btn-xs btn-success mark-complete" >
                                   Cancel Task
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4">
                                No record found.
                            </td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Showing Zcash Withdrawal Request List -->
    <div class="row">
        <div class="col-sm-6 form-group">
            <div class="panel panel-default panel-grey">
                <div class="panel-heading">
                    <h4>Withdrawal Request</h4>
                </div>
                <div class="panel-body table-inner table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($zcashTransferList) > 0)
                        @foreach($zcashTransferList as $transfer)
                        <tr>
                            <td>
                                <a href="{!! url('userprofiles/'.$userIDHashID->encode($transfer->user_id).'/'.strtolower
                                ($transfer->first_name.'_'.$transfer->last_name)) !!}">
                                    {{$transfer->first_name.' '.$transfer->last_name}}
                                </a>
                            </td>
                            <td>{{$transfer->amount}}</td>
                            <td>{{$transfer->status}}</td>
                            <td>
                                <a href="{!! url('zcash/proceed/'.$btcTransactionIDHashID->encode($transfer->id)) !!}"
                                   class="btn btn-xs btn-success cb-proceed" >
                                   Proceed
                                </a>
                                <a href="{!! url('zcash/cancel/'.$btcTransactionIDHashID->encode($transfer->id)) !!}"
                                   class="btn btn-xs btn-danger cb-cancel" >
                                   Cancel
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4">
                                No record found.
                            </td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
@endsection
@section('page-scripts')
<script src="{!! url('assets/js/zcash-payment.js') !!}" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $('.show_bid_details').on('click',function(){
            var id = $(this).attr('data-id');
            if($.trim(id) != ""){
                $.ajax({
                    type:'get',
                    url:siteURL+'/tasks/get_biding_details',
                    data:{id:id},
                    dataType:'json',
                    success:function(resp){
                        if(resp.success){
                            $(".div-table-second-cell").css('z-index','100');
                            $(".list-item-main").css('z-index','100');
                            var box = bootbox.dialog({
                                message: resp.html
                            });

                            box.on("hidden.bs.modal", function (e) {
                                $(".list-item-main").css('z-index','99999');
                                $(".div-table-second-cell").css('z-index','99999');
                            });

                            box.modal('show');
                        }
                    }
                });
            }
            return false;
        });
    })
</script>
@endsection