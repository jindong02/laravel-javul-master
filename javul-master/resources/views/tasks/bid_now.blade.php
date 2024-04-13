@extends('layout.default')
@section('page-meta')
<title>{!! $taskObj->name !!} - Javul.org</title>
@endsection
@section('page-css')
<link href="{!! url('assets/plugins/bootstrap-multiselect/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-summernote/summernote.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! url('assets/plugins/bootstrap-star-rating-master/css/star-rating.css') !!}" rel="stylesheet" type="text/css" />

<style>
    span.stars, span.stars span {
        display: block;
        background: url('{!! url("assets/images/stars.png") !!}') 0 -16px repeat-x;
        width: 80px;
        height: 16px;
    }

    span.stars span {
        background-position: 0 0;
    }
    .hide-native-select .btn-group, .hide-native-select .btn-group .multiselect, .hide-native-select .btn-group.multiselect-container
    {width:100% !important;}
    .bid_comment p{margin:0;}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom:15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    <div class="row form-group">
        <div class="col-sm-12 ">
            <div class="col-sm-6 grey-bg unit_grey_screen_height">
                <h1 class="unit-heading create_unit_heading">
                    <span class="glyphicon glyphicon-list-alt"></span>
                    {!! $taskObj->name !!}

                </h1>
                @if(!empty($daysRemainingTobid))
                    <span style="display: inline-block;font-weight: bold;">Time left for bid: {{$daysRemainingTobid}} days</span>
                @endif
                <br /><br />
            </div>
            @include('tasks.partials.task_information')
        </div>
    </div>
    <?php $active="task_details"; ?>
    @if($errors->has('amount') || $errors->has('comment'))
        <?php $active="bid_now"; ?>
    @endif

    <form role="form" method="post" id="form_sample_2"  novalidate="novalidate" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-4">
                @include('elements.site_activities',['ajax'=>false])
            </div>
            <div class="col-sm-8">
                <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                    <li @if($active =="task_details") class="active" @endif><a href="#task_details" data-toggle="tab">Task Details</a></li>
                    <li><a href="#task_actions" data-toggle="tab">Task Actions</a></li>
                    <li @if($active =="bid_now") class="active" @endif><a href="#bid_now" data-toggle="tab">
                        @if(!empty($taskBidder))
                            Bid Details
                        @else
                            Bid Now
                        @endif
                    </a></li>
                </ul>
                <div id="my-tab-content" class="tab-content">
                    <div class="list-group tab-pane @if($active == 'task_details') active @endif" id="task_details">
                        <div class="list-group-item">
                            <h4 class="text-orange">{!! strtoupper(trans('messages.task_status')) !!}</h4>
                            @if(empty($taskObj->assigned_to))
                            <div>Unassigned</div>
                            @elseif($taskObj->status == "completed")
                            <div>Completed</div>
                            <div>Completed On: date 23/05/2016</div>
                            @else
                            <div>assigned to user X</div>
                            @endif
                        </div>
                        <div class="list-group-item">
                            <h4 class="text-orange">{!! strtoupper(trans('messages.task_award')) !!}</h4>
                            <div>xx $</div>
                        </div>
                        <div class="list-group-item">
                            <h4 class="text-orange">{!! strtoupper(trans('messages.task_summary')) !!}</h4>
                            <div>{!! $taskObj->summary !!}</div>
                        </div>
                        <div class="list-group-item">
                            <h4 class="text-orange">{!! strtoupper(trans('messages.long_description')) !!}</h4>
                            <div>{!! $taskObj->description !!}</div>
                        </div>
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="text-orange">Task Documents</h4>
                                    @if(!empty($taskObj->task_documents))
                                    @foreach($taskObj->task_documents as $document)
                                    <?php $extension = pathinfo($document->file_path, PATHINFO_EXTENSION); ?>
                                    @if($extension == "pdf") <?php $extension="pdf"; ?>
                                    @elseif($extension == "doc" || $extension == "docx") <?php $extension="docx"; ?>
                                    @elseif($extension == "jpg" || $extension == "jpeg") <?php $extension="jpeg"; ?>
                                    @elseif($extension == "ppt" || $extension == "pptx") <?php $extension="pptx"; ?>
                                    @else <?php $extension="file"; ?> @endif
                                    <div class="file_documents">
                                        <a class="files_image" href="{!! url($document->file_path) !!}" target="_blank">
                                            <img src="{!! url('assets/images/file_types/'.$extension.'.png') !!}" style="height:50px;">
                                    <span style="display:block">
                                        @if(empty($document->file_name))
                                            &nbsp;
                                        @else
                                            {{$document->file_name}}
                                        @endif
                                    </span>
                                        </a>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="list-group tab-pane" id="task_actions">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="text-orange">TASK ACTIONS</h4>
                                    <div>{!! $taskObj->task_action !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group tab-pane @if($active == 'bid_now') active @endif" id="bid_now">
                        <div class="list-group-item">
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label" style="margin-bottom:0px">Task Completion Ratings: Quality of works :<span
                                                        class="stars" style="display:inline-block">{{$quality_of_work}}</span>
                                                        ({{$quality_of_work}}/5)
                                                        Timeliness :<span class="stars"
                                                                          style="display:inline-block">{{$timeliness}}</span>({{$timeliness}}/5)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-xs-6 col-sm-4  {{ $errors->has('amount') ? ' has-error' : '' }}">
                                    <div class="input-icon right">
                                        <label for="amount" class="control-label">Amount</label>
                                        <input name="amount" type="text" required id="amount" class="form-control"
                                               @if(!empty($taskBidder)) value="{{$taskBidder->amount}}" @else value="{{ old('amount')}}" @endif
                                        @if(!empty($taskBidder)) disabled @endif/>
                                        @if ($errors->has('amount'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('amount') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    <div class="input-icon right">
                                        <label for="amount" class="control-label">&nbsp;</label>
                                        <input class="toggle" @if(!empty($taskBidder) && $taskBidder->charge_type == "amount") checked
                                        disabled
                                        @endif
                                        data-on="Amount"
                                        data-off="Points"
                                        type="checkbox" name="charge_type">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-12 {{ $errors->has('comment') ? ' has-error' : '' }}">
                                    <div class="input-icon right">
                                        <label for="amount" class="control-label">Comment</label>
                                        @if(!empty($taskBidder))
                                            <span class="bid_comment">{!! $taskBidder->comment !!}</span>
                                        @else
                                            <textarea class="form-control summernote" id="comment" name="comment">{{old('comment')}}</textarea>
                                            @if ($errors->has('comment'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('comment') }}</strong>
                                                    </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(empty($taskBidder))
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn usermenu-btns orange-bg">Bid</button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script src="{!! url('assets/plugins/bootstrap-star-rating-master/js/star-rating.js') !!}"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    $.fn.stars = function() {
        return $(this).each(function() {
            // Get the value
            var val = parseFloat($(this).html());
            val = Math.round(val * 2) / 2;
            // Make sure that the value is in 0 - 5 range, multiply to get width
            var size = Math.max(0, (Math.min(5, val))) * 16;

            // Create stars holder
            var $span = $('<span />').width(size);
            // Replace the numerical value with stars
            $(this).html($span);
        });
    }
    $(function(){
        $('span.stars').stars();
    });
</script>
<script src="{!! url('assets/plugins/bootstrap-summernote/summernote.js') !!}" type="text/javascript"></script>
<script src="{!! url('assets/js/tasks/task_bid.js') !!}"></script>
@endsection