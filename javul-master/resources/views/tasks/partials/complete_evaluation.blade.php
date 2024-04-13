@if(!empty($taskEditors) && count($taskEditors) > 0)
<div class="row reward-panel" style="display: none;">
    <div class="col-sm-12">
        <div class="panel panel-default panel-dark-grey">
            <div class="panel-heading">
                <h4>Reward Assignment</h4>
                <span class="text-right">( 10% of task reward among all task editor and task creator)</span>
            </div>

            <div class="panel-body reward-assignment-body">
                @if(!$rewardAssigned)
                <div class="row form-group {{  $errors->has('split_error') ? ' has-error' : ''  }}
                        {{  $errors->has('amount_percentage['.$taskObj->user_id.']')? ' has-error' : ''  }}">
                    <div class="col-sm-4 col-xs-8">
                        {{\App\User::getUserName($taskObj->user_id)}} (<b>task creator</b>)
                    </div>
                    <div class="col-sm-2 col-xs-4">
                        <input type="text" name="amount_percentage[{{$taskObj->user_id}}]"
                               value="{{ old('amount_percentage['.$taskObj->user_id.']') }}"
                               class="form-control onlyDigits amount_percentage"
                               style="display:inline-block;float:left;width:50px"/>
                        <span style="line-height:35px;padding-left:2px">%</span>
                    </div>
                </div>
                @endif
                @foreach($taskEditors as $editor)
                    @if($editor->user_id != $taskObj->user_id)
                        <div class="row form-group {{  $errors->has('split_error') ? ' has-error' : ''  }}
                        {{  $errors->has('amount_percentage['.$editor->user_id.']')? ' has-error' : ''  }}">
                            <div class="col-sm-4 col-xs-8">
                                {{\App\User::getUserName($editor->user_id)}}
                                @if($rewardAssigned && $editor->user_id == $taskObj->user_id)
                                    (<b>task creator</b>)
                                @else
                                (<b>task editor</b>)
                                @endif
                            </div>
                            <div class="col-sm-2 col-xs-4">
                                <input type="text" name="amount_percentage[{{$editor->user_id}}]"
                                       @if($rewardAssigned) value="{{ $editor->reward_percentage}}" @else
                                       value="{{ old('amount_percentage['.$editor->user_id.']')}}" @endif
                                class="form-control onlyDigits amount_percentage"
                                style="display:inline-block;float:left;width:50px"/>
                                <span style="line-height:35px;padding-left:2px">%</span>
                            </div>
                        </div>
                    @endif
                @endforeach
                @if($errors->has('split_error'))
                    <span class="has-error error-not-100">
                        <span class="control-label">{{$errors->first('split_error')}}</span>
                    </span>
                @elseif($errors->has('amount_percentage['.$taskObj->user_id.']'))
                    <span class="has-error error-not-100">
                        <span class="control-label">Please enter percentage</span>
                    </span>
                @elseif(count($errors) > 0 && !$error->has('comment'))
                    <span class="has-error error-not-100">
                        <span class="control-label">Please enter percentage</span>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-12 form-group" >
        <button id="ok_complete" type="button"  class="btn btn-success" data-tid="{{$taskIDHashID->encode($taskObj->id)}}">
            <span class="glyphicon glyphicon-check"></span> Ok
        </button>
        <button type="button"  class="btn btn-danger cancel_btn">
            <span class="glyphicon glyphicon-new-window"></span> Cancel
        </button>
    </div>
</div>

@endif
<div class="row comment_block" style="display: none;">
    <div class="col-sm-12 form-group">
        <label class="control-label">Comments</label>
        <textarea class="form-control summernote" name="comment" id="comment"></textarea>
    </div>
    <div class="col-sm-12 form-group">
        <button id="ok_reassign" type="button"  class="btn btn-success" data-tid="{{$taskIDHashID->encode($taskObj->id)}}">
            <span class="glyphicon glyphicon-check"></span> Ok
        </button>
        <button type="button"  class="btn btn-danger cancel_btn">
            <span class="glyphicon glyphicon-new-window"></span> Cancel
        </button>
    </div>
</div>
@if($taskObj->status == "completion_evaluation")
<div class="row form-group">
    <div class="col-sm-12 complete_assign_btn">
        @if(!empty($taskEditors) && count($taskEditors) > 0)
            <button id="mark_as_complete" type="button"  class="btn btn-success" >
                <span class="glyphicon glyphicon-check"></span> Mark as Complete
            </button>
        @else
            <button id="ok_complete" type="button"  class="btn btn-success" data-tid="{{$taskIDHashID->encode($taskObj->id)}}">
                <span class="glyphicon glyphicon-check"></span> Mark as Complete
            </button>
        @endif
        <button id="reassign_task_btn" type="button"  class="btn orange-bg">
            <span class="glyphicon glyphicon-new-window"></span> Re Assign
        </button>
    </div>
</div>
@endif