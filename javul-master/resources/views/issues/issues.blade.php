@extends('layout.default')
@section('page-meta')
<title>Issues - Javul.org</title>
@endsection
@section('content')
    <div class="container">
        <div class="row form-group" style="margin-bottom:15px;">
            @include('elements.user-menu',['page'=>'units'])
        </div>
        <div class="row form-group">
            <div class="col-md-8 col-md-push-4">
                <div class="panel panel-grey panel-default">
                    <div class="panel-heading">
                        <h4>ISSUES</h4>
                    </div>
                    <div class="panel-body table-inner table-responsive loading_content_hide">
                        <div class="loading_dots unit_loading" style="position: absolute;top:20%;left:43%;z-index: 9999;display:none;">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <table class="table table-striped unit-table">
                            <thead>
                            <tr>
                                <th>Creation Date</th>
                                <th>Issue Name</th>
                                <th>Unit Name</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($issues) > 0 )


                                @foreach($issues as $issue)

                                    <tr>
                                        <td>{!! $issue->age !!}</td>
                                        <td>
                                            <a href="{!! url('issues/'.$issueIDHashID->encode($issue->id).'/view') !!}">
                                                {{$issue->title}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{!! url('units/'.$unitIDHashID->encode($issue->unit_id).'/'.\App\Models\Unit::getSlug($issue->unit_id)) !!}">
                                                {{\App\Models\Unit::getUnitName($issue->unit_id)}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$issue->status}}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No record(s) found.</td>
                                </tr>
                            @endif
                            <tr style="background-color: #fff;text-align: right;">
                                <td colspan="4">
                                    <a href="{!! url('issues/add')!!}"class="btn black-btn" id="add_unit_btn"
                                       type="button">
                                        <i class="fa fa-plus plus"></i> <span class="plus_text">ADD ISSUE</span>
                                    </a>
                                    @if($issues->lastPage() > 1 && $issues->lastPage() != $issues->currentPage())
                                        <a href="#" data-url="{{$issues->url($issues->currentPage()+1) }}" class="btn more-black-btn
                                        more-issues" id="add_unit_btn"
                                           type="button">
                                            MORE ISSUES <span class="more_dots">...</span>
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

            /*$('[data-toggle="tooltip"]').tooltip({
             container: 'body'
             });*/
        })
    </script>
    <script src="{!! url('assets/js/units/delete_unit.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
@endsection
