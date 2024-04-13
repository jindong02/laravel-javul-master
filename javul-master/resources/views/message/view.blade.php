@extends('layout.default')
@section('page-meta')
<title>View Message - Javul.org</title>
@endsection
@section('page-css')
<style>
    .bodyMSG{
        min-height: 200px;
        white-space: pre;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row form-group" style="margin-bottom: 15px;">
        @include('elements.user-menu',['page'=>'units'])
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="panel panel-grey panel-default">
                <div class="panel-heading">
                    <?= $message['subject'] ?>
                </div>
                <div class="panel-body list-group">
                    <div class="col-md-2">
                        @include('message.menu',array())
                    </div>
                    <div class="col-md-8">
                        <div class="bodyMSG col-md-12">
                        <div class="name">
                            <?= $message['to'] == $myId ? 'From' : 'To' ?> : 
                            <a href="<?= $message['link'] ?>" > <?= $message['first_name'] ." ".$message['last_name'] ?> </a> <?= $message['datetime'] ?> </div>
                        <br><?= $message['body'] ?></div>
                    </div> 
                    <div class="clearfix"></div>
                    <a href="{!! url('message/send')."/".$message['from'] !!}" class="btn black-btn pull-right">Reply</a>
                </div>
            </div>
             
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')

@endsection
