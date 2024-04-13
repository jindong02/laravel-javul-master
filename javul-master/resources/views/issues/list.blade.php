@extends('layout.default')
@section('page-meta')
<title>Issues - Javul.org</title>
@endsection
@section('page-css')
    <style>
        .related_para{margin:0 0 10px;}
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row form-group" style="margin-bottom: 15px;">
            @include('elements.user-menu',['page'=>'units'])
        </div>
        <div class="row form-group">
            <div class="col-md-4">
                @include('units.partials.unit_information_left_table',['unitObj'=>$unitObj,'availableFunds'=>$availableUnitFunds,'awardedFunds'=>$awardedUnitFunds])
                <div class="left" style="position: relative;margin-top: 30px;">
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
            <div class="col-md-8">
                <div class="issueListing">
                    @include('issues.partials.issue_listing')
                </div>

            </div>
        </div>
    </div>
    @include('elements.footer')
@stop

@section('page-scripts')
    <script src="{!! url('assets/plugins/jquery.ThreeDots.min.js') !!}" type="text/javascript"></script>
    <script>
        var msg_flag ='{{ $msg_flag }}';
        var msg_type ='{{ $msg_type }}';
        var msg_val ='{{ $msg_val }}';
        $(function(){
            $(".unit_description").css("min-height",($(".both-div").height())+10+'px');
            var the_obj = $('.text_wraps').ThreeDots({
                max_rows: 1
            });

            $(document).off("click",".sort_by").on("click",".sort_by",function(){
                var order_by=$(this).data('order_by');
                $.ajax({
                    type:'post',
                    url:'{!! url('issues/sort_issue') !!}',
                    data:{_token:'{{csrf_token()}}',unit_id:'{{$unitIDHashID->encode($unit_activity_id)}}',order_by:order_by},
                    dataType:'json',
                    success:function(resp){
                        if(resp.success){
                            $(".issueListing").html(resp.html);
                            if(order_by == "older") {
                                $(".issueListing").find("th a.sort_by").attr('data-order_by', 'new');
                                $(".issueListing").find("th a span").removeClass('fa-sort-desc').addClass('fa-sort-asc');
                                $(".issueListing").find("th a span").css('vertical-align','middle');
                            }
                            else {
                                $(".issueListing").find("th a.sort_by").attr('data-order_by', 'older');
                                $(".issueListing").find("th a span").removeClass('fa-sort-asc').addClass('fa-sort-desc');
                                $(".issueListing").find("th a span").css('vertical-align','top');
                            }
                            var the_obj = $('.text_wraps').ThreeDots({
                                max_rows: 1
                            });
                        }
                        else{
                            showToastMessage('SOMETHING_GOES_WRONG');
                        }
                    }
                })
                return false;
            })
        })
    </script>
    <script src="{!! url('assets/js/custom_tostr.js') !!}" type="text/javascript"></script>
@endsection
