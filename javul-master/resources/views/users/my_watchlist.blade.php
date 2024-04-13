@extends('layout.default')
@section('page-meta')
<title>My Watchlist - Javul.org</title>
@endsection
@section('page-css')
    <style>
        hr, p{margin:0 0 10px !important;}
        .files_image:hover{text-decoration: none;}
        .file_documents{display: inline-block;padding: 10px;}
    </style>
@endsection
@section('content')
    <div class="container maincontent-div" style="display:none;">
        <div class="row form-group" style="margin-bottom:15px">
            @include('elements.user-menu',['page'=>'tasks'])
        </div>
        <div class="row form-group">
            <div class="col-sm-6">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>{!! trans('messages.units') !!}</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{!! trans('messages.unit_name') !!}</th>
                                <th>{!! trans('messages.unit_category') !!}</th>
                                <th>{!! trans('messages.description') !!}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($watchedUnits) > 0 )
                                @foreach($watchedUnits as $unit)
                                    <?php $category_ids = $unit->category_id;
                                    $category_names = $unit->category_name;
                                    $category_ids = explode(",",$category_ids);
                                    $category_names  = explode(",",$category_names );
                                    ?>
                                    <tr>
                                        <td><a href="{!! url('units/'.$unitIDHashID->encode($unit->id).'/'.$unit->slug) !!}">{{$unit->name}}</a></td>
                                        <td>
                                            @if(count($category_ids) > 0 )
                                                @foreach($category_ids as $index=>$category)
                                                    <a href="{!! url('units/category/'.$unitCategoryIDHashID->encode($category))
                                                    !!}">{{\App\UnitCategory::getName($category)}}</a>
                                                    @if(count($category_ids) > 1 && $index != count($category_ids) -1)
                                                        <span>&#44;</span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td><div class="text_wraps" data-toggle="tooltip" data-placement="top"  title="{!!trim
                                            ($unit->description)!!}"><span
                                                        class="ellipsis_text">{!!trim($unit->description)!!}</span></div></td>
                                        <td>
                                            <a href="#" class="remove-from-watchlist text-danger" data-id="{{$unitIDHashID->encode($unit->id)}}"
                                               data-type="unit">
                                                <span><i class="fa fa-trash" aria-hidden="true"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No record(s) found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>{!! trans('messages.objectives') !!}</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Objective Name</th>
                                <th>{!! trans('messages.description') !!}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($watchedObjectives) > 0)
                                @foreach($watchedObjectives as $objective)
                                    <tr>
                                        <td><a href="{!! url('objectives/'.$objectiveIDHashID->encode($objective->id).'/'.$objective->slug) !!}">{{$objective->name}}</a></td>

                                        <td><div class="text_wraps" data-toggle="tooltip" data-placement="top"  title="{!!trim
                                            ($objective->description)!!}">
                                                <span class="ellipsis_text">{!!trim($objective->description)!!}</span></div>
                                        </td>
                                        <td>
                                            <a href="#" class="remove-from-watchlist text-danger" data-id="{{$objectiveIDHashID->encode($objective->id)}}"
                                               data-type="objective">
                                                <span><i class="fa fa-trash" aria-hidden="true"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No record(s) found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-6">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>Tasks</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($watchedTasks) > 0 )
                                @foreach($watchedTasks as $task)
                                    <tr>
                                        <td><a href="{!! url('tasks/'.$taskIDHashID->encode($task->id).'/'.$task->slug) !!}">{{$task->name}}</a></td>
                                        <td><div class="text_wraps" data-toggle="tooltip" data-placement="top"  title="{!!trim
                                        ($task->description)!!}">
                                            <span class="ellipsis_text">{!!trim($task->description)!!}</span></div>
                                        </td>
                                        <td>
                                            <a href="#" class="remove-from-watchlist text-danger" data-id="{{$taskIDHashID->encode($task->id)}}"
                                               data-type="task">
                                                <span><i class="fa fa-trash" aria-hidden="true"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No record(s) found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>Issues</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Issue Name</th>
                                <th>Issue Category</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($watchedIssues) > 0 )
                                @foreach($watchedIssues as $issue)
                                    <?php $category_ids = $issue->category_id;
                                    $category_names = $issue->category_name;
                                    $category_ids = explode(",",$category_ids);
                                    $category_names  = explode(",",$category_names );
                                    ?>
                                    <tr>
                                        <td><a href="{!! url('issues/'.$issueIDHashID->encode($issue->id).'/'.$issue->slug.'view') !!}">{{ $issue->title}}</a></td>
                                        <td>
{{--                                            @if(count($category_ids) > 0 )--}}
{{--                                                @foreach($category_ids as $index=>$category)--}}
{{--                                                    <a href="{!! url('issue/category/'.$issueDocumentIDHashID->encode($category))--}}
{{--                                                    !!}">{{\App\IssueDocument::getName($category)}}</a>--}}
{{--                                                    @if(count($category_ids) > 1 && $index != count($category_ids) -1)--}}
{{--                                                        <span>&#44;</span>--}}
{{--                                                    @endif--}}
{{--                                                @endforeach--}}
{{--                                            @endif--}}
                                        </td>
                                        <td><div class="text_wraps" data-toggle="tooltip" data-placement="top"  title="{!!trim
                                            ($issue->description)!!}"><span
                                                        class="ellipsis_text">{!!trim( $issue->description)!!}</span></div></td>
                                        <td>
                                            <a href="#" class="remove-from-watchlist text-danger" data-id="{{$issueIDHashID->encode($issue->id)}}"
                                               data-type="issue">
                                                <span><i class="fa fa-trash" aria-hidden="true"></i></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No record(s) found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('elements.footer')
@endsection
@section('page-scripts')
    <script src="{!! url('assets/plugins/jquery.ThreeDots.min.js') !!}" type="text/javascript"></script>

    <script type="text/javascript">
        var msg_flag='';
        $(function(){
            $(".maincontent-div").show();
            var the_obj = $('.text_wraps').ThreeDots({
                max_rows: 1
            });

        })
    </script>
    <script src="{!! url('assets/js/users/my_watchlist.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
@endsection