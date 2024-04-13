$(function(){
    $(".delete-objective").on('click',function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            //disable click event until result not come
            var total_rows = $(this).parents('tbody').find('tr').length;
            $(this).addClass('prevent-click');
            var objective_name = $(this).parents('tr').find('td:first-child').find('a').html();
            bootbox.dialog({
                title: "Are you sure?",
                message: "<p>You want delete <b>Objective: </b>"+objective_name+'?<p> <b>Note:</b> Tasks of objectives "'+objective_name+'" also deleted.',
                buttons: {
                    danger: {
                        label: "Cancel",
                        className: "btn-danger",
                        callback: function() {
                            that.removeClass('prevent-click');
                            bootbox.hideAll();
                        }
                    },
                    success: {
                        label: "Delete",
                        className: "btn-success",
                        callback: function() {
                            $.ajax({
                                type:'get',
                                url:siteURL+'/objectives/delete_objective',
                                data:{id:id},
                                dataType:'json',
                                success:function(resp){
                                    if(resp.success){
                                        if(total_rows == 1)
                                            that.parents('tr').html('<td colspan="5">No record(s) found.</td>');
                                        else
                                            that.parents('tr').remove();
                                        showToastMessage('OBJECTIVE_DELETED');
                                    }
                                    else{
                                        showToastMessage('SOMETHING_GOES_WRONG');
                                        //enable click event if result is false.
                                        that.removeClass('prevent-click');
                                    }
                                }
                            })
                        }
                    }
                }
            });
        }
        return false;
    })
})