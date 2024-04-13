@if(count($tasks) > 0 )
    @foreach($tasks as $task)
        @if($from_page == "unit_view")
            <tr>
                <td width="60%">
                    <a href="{!! url('tasks/'.$taskIDHashID->encode($task->id).'/'.$task->slug) !!}"
                       title="edit">
                        {{$task->name}}
                    </a>
                </td>
                <td width="20%" class="text-center">
                    @if($task->status == "editable")
                        <span class="colorLightGreen">{{\App\SiteConfigs::task_status($task->status)}}</span>
                    @else
                        <span class="colorLightGreen">{{\App\SiteConfigs::task_status($task->status)}}</span>
                    @endif
                </td>
                <td class="text-center">{{\App\Task::getTaskCount('in-progress',$task->id)}}</td>
                <td class="text-center">{{\App\Task::getTaskCount('completed',$task->id)}}</td>
            </tr>
        @else
            <?php $unitSlug = \App\Unit::getSlug($task->unit_id);
            $objectiveSlug = \App\Objective::getSlug($task->objective_id);?>
            <tr>
                <td><a href="{!! url('tasks/'.$taskIDHashID->encode($task->id).'/'.$task->slug)!!}">{{$task->name}}</a></td>
                <td>
                    <a href="{!! url('objectives/'.$objectiveIDHashID->encode($task->objective_id).'/'.$objectiveSlug)!!}">
                        {{\App\Objective::getObjectiveName($task->objective_id)}}
                    </a>
                </td>
                <td>
                    <a href="{!! url('units/'.$unitIDHashID->encode($task->unit_id).'/'.$unitSlug) !!}">
                        {{\App\Unit::getUnitName($task->unit_id)}}
                    </a>
                </td>
                <td>
                    {!! \App\JobSkill::getSkillNameLink($task->skills) !!}
                </td>
                <td>
                    @if(empty($task->assign_to))
                        -
                    @else
                        <a href="{!! url('userprofiles/'.$userIDHashID->encode($task->assign_to).'/'.strtolower(\App\User::getUserName($task->assign_to))) !!}">
                            {{\App\User::getUserName($task->assign_to)}}
                        </a>
                    @endif
                </td>
                <td>{{\App\SiteConfigs::task_status($task->status)}}</td>
                <td width="11%">
                    @if(\App\Task::isUnitAdminOfTask($task->id))
                        <a title="Change Task Status" href="{!! url('tasks/'.$taskIDHashID->encode($task->id)).'/edit/change_status' !!}" class="btn btn-xs btn-primary">
                            Change Status
                        </a>
                    @endif
                    @if($task->status == "approval" || $task->status == "open_for_bidding")
                    <!-- User cannot bid on their task -->
                        @if(isset($authUserObj->id) && $authUserObj->id != $task->user_id)
                            @if(\App\TaskBidder::checkBid($task->id))
                                <a title="bid now" href="{!! url('tasks/bid_now/'.$taskIDHashID->encode($task->id)).'#bid_now' !!}" class="btn btn-xs btn-primary">
                                    <!--<span><img src="{!! url('assets/images/bid_small.png') !!}"/></span>-->
                                    Bid now
                                </a>
                            @else
                                <a title="applied bid" class="btn btn-xs btn-warning">
                                    Applied Bid
                                </a>
                            @endif
                        @elseif(!\App\Task::isUnitAdminOfTask($task->id))
                            -
                        @endif
                    @elseif(!\App\Task::isUnitAdminOfTask($task->id))
                        -
                    @endif
                </td>
            </tr>
        @endif
    @endforeach
    <tr style="background-color: #fff !important;text-align: right">
        <td colspan="7">
            <!-- <a href="{!! url('tasks/add')!!}"class="btn black-btn" id="add_unit_btn"
               type="button">
                <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_task')!!}</span>
            </a> -->
            @if($from_page != "task_search_view")
                @if($tasks->lastPage() > 1 && $tasks->lastPage() != $tasks->currentPage())
                    <a href="#" data-url="{{$tasks->url($tasks->currentPage()+1) }}" data-from_page="{{$from_page}}"
                       @if(!empty($objective_id)) data-objective_id="{{$objectiveIDHashID->encode($objective_id)}}" @endif
                       @if(!empty($unit_id)) data-unit_id="{{$unitIDHashID->encode($unit_id)}}" @endif
                       class="btn more-black-btn more-tasks" type="button">
                        MORE TASKS <span class="more_dots">...</span>
                    </a>
                @endif
            @endif
        </td>
    </tr>
@else
    <tr>
        <td colspan="7">No record found.</td>
    </tr>
@endif