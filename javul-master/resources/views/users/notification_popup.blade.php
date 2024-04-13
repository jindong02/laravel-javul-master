<div class="list-group " style="margin-bottom: 0px;">
    @if(!empty($notifications) && count($notifications) > 0)
        @foreach($notifications as $noti)
            <div class="list-group-item popup-content-item" data-id="{{$noti->id}}">
                {!! $noti->content !!}
            </div>
        @endforeach
    @else
        <div class="list-group-item popup-content-item">
            No notification found!!!
        </div>
    @endif
    <div class="list-group-item popup-content-item" style="text-align: center">
        <a href="#" onclick="$('[data-toggle=\'popover\']').popover('hide')" style="margin-right:15px;">Close</a>
        <span style="height:15px;border-right:1px solid #ddd;"></span>
        <a href="{!! url('my_alerts') !!}" style="margin-left:15px;">See all</a>
    </div>
</div>