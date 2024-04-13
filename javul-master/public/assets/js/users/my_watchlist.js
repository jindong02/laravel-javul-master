$(function(){
   $(".remove-from-watchlist").on('click',function(){
       var id = $(this).attr('data-id');
       var type = $(this).attr('data-type');
       var that = $(this);
       if($.trim(id) != "" && $.trim(type) != ""){
           $.ajax({
               type:'get',
               url:siteURL+'/remove_from_watchlist',
               data:{id:id,type:type},
               dataType:'json',
               success:function(resp){
                   type= type.charAt(0).toUpperCase() + type.slice(1);
                   toastr[getToastMessage('REMOVED_FROM_WATCH_LIST')['type']](type+getToastMessage('REMOVED_FROM_WATCH_LIST')['text'], '');
                   if(that.parents('tbody').find('tr').length == 1)
                       that.parents('tr').html('<td colspan=3>No record(s) found.');
                   else
                    that.parents('tr').remove();
               }
           })
       }else{
            showToastMessage('SOMETHING_GOES_WRONG');
       }
       return false;
   });
});