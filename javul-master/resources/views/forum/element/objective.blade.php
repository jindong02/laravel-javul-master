<div class="panel panel-grey panel-default">
    <div class="panel-heading">
        <h4>Comments
        <?php if(isset($addComments)){ ?>
            <a class="btn black-btn pull-right" href="<?= $addComments ?>">Add Comment</a>
        <?php } ?>
        </h4>
    </div>
    <div class="panel-body list-group objectiveComment">
        <div class="list-group-item">
            <div class="row">
                <ul class="posts"></ul>
                <div class="pagingnation-forum pull-right">Showing last <span class="item-count"> 0 </span> comments. 

                    <a href="<?= isset($addComments) ?  $addComments : '' ?>" class="<?= !isset($addComments) ?  'hide' : '' ?>"> View Forum Thread </a>
                    &nbsp;&nbsp;
                </div>
                <div class="clearfix"></div>
                @if(\Auth::check())
                <hr>
                <div class="form">
                    <form role="form" method="post" id="form_topic_form"  enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="col-sm-12 form-group">
                            <h4 class="control-label">Comment</h4>
                            <textarea class="form-control summernote" name="desc"></textarea>
                        </div>
                        <input type="hidden" name="unit_id" value="<?=  $unit_id ?>">
                        <input type="hidden" name="section_id" value="<?=  $section_id ?>">
                        <input type="hidden" name="object_id" value="<?=  $object_id ?>">
                        <div class="col-sm-12 form-group">
                            <button class="btn black-btn pull-right">Submit Comment</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
