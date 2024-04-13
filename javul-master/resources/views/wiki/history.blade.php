@extends('layout.default')
@section('page-meta')
<title>Wiki: {!! $wiki['name'] !!}  - Javul.org</title>
@endsection
@section('page-css')
    <link href="{!! url('assets/css/wiki.css') !!}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<link rel="stylesheet" type="text/css" href="{!! url('assets/css/forum.css') !!}">
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            @include('units.partials.unit_information_left_table')
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
            <div class="panel panel-grey panel-default">
                <div class="panel-heading">
                	<h4> 
                        <b>Wiki :</b> {!! $wiki['name'] !!} 
                        <div class="button pull-right">
                            <a href="{!! url('wiki') !!}/{!! $unit_id !!}/{!! $wiki['slug'] !!}">Back</a>
                        </div>
                    </h4>
                </div>
                <div class="panel-body list-group">
                    <div class="">
                        <table class="table">
                            <tbody>
                            <?php if(empty($wikiHistory['item'])){ ?>
                                <tr>
                                    <td colspan="100%"><h4 class="text-center">No any Histrory Found</h4></td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($wikiHistory['item'] as $key => $value) { ?>
                                 <tr>
                                    <td width="50px" ><?= $value['datetime'] ?></td>
                                    <td width="50px" ><?= $value['user_name'] ?></td>
                                    <td width="100px" ><?= $value['change_byte'] ?> bytes</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="100%"><?= $wikiHistory['links']; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts') 
@endsection
