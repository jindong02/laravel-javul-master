<?php
$upvote_class="";
$downvote_class="";
$voteClass="";
if(Auth::check()){
    $voteClass=" vote ";
    $flag = \App\Models\ImportanceLevel::checkImportanceLevel($issue_id,'issue_id');
    if($flag == "1")
        $upvote_class="success-upvote";
    elseif($flag == "-1")
        $downvote_class="success-downvote";
}
?>
<div style="float:left;">{{$importancePercentage}}%</div>
<div style="display: inline-block">
    <div class="{{$upvote_class}}" style="display: inline-block">
        <span class="fa fa-thumbs-up {{$voteClass}} upvote "
            @if(Auth::check()) data-id="{{ $issueIDHashID->encode($issue_id) }}"
            data-type="up" @endif
            title="upvote"></span>{{$upvotedCnt}}
    </div>
    <div class="{{$downvote_class}}" style="display: inline-block">
        <span class="fa fa-thumbs-down {{$voteClass}} downvote "
            @if(Auth::check()) data-id="{{ $issueIDHashID->encode($issue_id) }}"
            data-type="down" @endif
            title="downvote"></span>{{$downvotedCnt}}
    </div>
</div>
