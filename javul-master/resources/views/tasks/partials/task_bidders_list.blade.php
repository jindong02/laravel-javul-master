<div class="list-group tab-pane" id="task_bidders">
    <div class="list-group-item">
        <table class="table table-stripped">
            <thead>
            <tr>
                <th>Bidder Name</th>
                <th>Amount</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @if(count($taskBidders) > 0)
                @foreach($taskBidders as $bidder)
                <tr>
                    <td>
                        <a href="{!! url('userprofiles/'.$userIDHashID->encode($bidder->user_id).'/'.
                                                strtolower($bidder->first_name.'_'.$bidder->last_name)) !!}">
                            {{$bidder->first_name.' '
                            .$bidder->last_name}}
                        </a>
                    </td>
                    <td>{{$bidder->amount}} <span class="badge">{{$bidder->charge_type}}</span></td>
                    <td>
                        @if($taskObj->status == "assigned" && $bidder->user_id == $taskObj->assign_to)
                            <a class="btn btn-xs btn-warning" style="color:#fff;">Assigned</a>
                        @elseif($taskObj->status=="completion_evaluation" && $bidder->user_id == $taskObj->assign_to)
                            <a class="btn btn-xs btn-success" style="color:#fff;">Completed</a>
                        @elseif($bidder->status == "offer_rejected")
                            <a class="btn btn-xs btn-danger" style="color:#fff;">Offer Rejected</a>
                        @elseif($taskObj->status=="in_progress" && $bidder->user_id == $taskObj->assign_to)
                            <a class="btn btn-xs btn-info" style="color:#fff;">In Progress</a>
                        @elseif((empty($taskObj->assign_to) && $isUnitAdminOfTask) || (!empty($taskObj->assign_to) && $isUnitAdminOfTask && $taskObj->status=="open_for_bidding"))
                            <a class="btn btn-xs btn-primary assign_now"
                               data-uid="{{$userIDHashID->encode($bidder->user_id)}}"
                               data-tid="{{$taskIDHashID->encode($bidder->task_id)}}"
                               style="color:#fff;">Assign now</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">No bidder found.</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>