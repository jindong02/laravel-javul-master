@if(count($objectives) > 0 )
    @foreach($objectives as $objective)
        @if($from_page == "unit_view")
            <tr>
                <td>
                    <a href="{!! url('objectives/'.$objectiveIDHashID->encode($objective->id).'/'.$objective->slug) !!}" title="edit">
                        {{$objective->name}}
                    </a>
                </td>
                <td  class="text-center">{{\App\Task::getTaskCount('available',$objective->id)}}</td>
                <td  class="text-center">{{\App\Task::getTaskCount('in-progress',$objective->id)}}</td>
                <td  class="text-center">{{\App\Task::getTaskCount('completed',$objective->id)}}</td>
            </tr>
        @else
            <tr>
                <td><a href="{!! url('objectives/'.$objectiveIDHashID->encode($objective->id).'/'.$objective->slug)!!}">{{$objective->name}}</a></td>
                <td><a href="{!! url('units/'.$unitIDHashID->encode($objective->unit_id).'/'.\App\Unit::getSlug($objective->unit_id) )!!}">{{\App\Unit::getUnitName($objective->unit_id)}}</a></td>
                <td><a href="{!! url('userprofiles/'.$userIDHashID->encode($objective->user_id).'/'.strtolower
                                (\App\User::getUserName($objective->user_id)))!!}">
                        {{\App\User::getUserName($objective->user_id)}}
                    </a></td>
                <td>{{$objective->status}}</td>
            </tr>
        @endif
    @endforeach
    <tr style="background-color: #fff !important;text-align: right">
        <td colspan="4">
        <a href="{!! url('objectives/add')!!}"class="btn black-btn" id="add_unit_btn" type="button">
            <i class="fa fa-plus plus"></i> <span class="plus_text">{!! trans('messages.add_objective')!!}</span>
        </a>

        @if($objectives->lastPage() > 1 && $objectives->lastPage() != $objectives->currentPage())
            <a href="#" data-url="{{$objectives->url($objectives->currentPage()+1) }}" data-from_page="{{$from_page}}"
               @if(!empty($unit_id)) data-unit_id="{{$unitIDHashID->encode($unit_id)}}" @endif
               class="btn more-black-btn more-objectives" type="button">
                MORE OBJECTIVES <span class="more_dots">...</span>
            </a>
        @endif
        </td>
    </tr>
@endif