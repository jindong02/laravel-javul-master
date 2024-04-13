@extends('layout.default')
@section('page-meta')
<title>Message Inbox - Javul.org</title>
@endsection
@section('page-css')
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
                    Message Inbox
                </h4></div>
                <div class="panel-body list-group">
                    <div class="col-md-2">
                        @include('message.menu',array())
                    </div>
                    <div class="col-md-10">
                        <ul class="message">
                            <?php foreach ($messages['message'] as $key => $value) { ?>
                                <li><a href="{!! url('message/view').'/'.$value['message_id'] !!}">
                                    <div class="heading">
                                        <?= $value['first_name'] ." ".$value['last_name'] ?>
                                        <span class="time">
                                            <?= $value['datetime'] ?>
                                        </span>
                                    </div>
                                    <div class="body">
                                        <?= $value['body'] ?>
                                    </div>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if(empty($messages['message'])){ ?>
                                <h4 class="text-center"><br><br>Your {!! $page !!} is Empty </h4>
                            <?php } ?>
                        </ul>
                        <div class="pagination">
                            <?= $messages['pagination'] ?>
                        </div>
                    </div>
                </div>
            </div>
             
        </div>
    </div>
</div>
@include('elements.footer')
@stop
@section('page-scripts')
<script type="text/javascript">
       
</script>
@endsection
