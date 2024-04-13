$(function(){
    $("#unit_category_search").select2({
        placeholder:'Search by UnitCategory',
        width: '100%',
        multiple: false,
        allowClear:true,
        minimumInputLength: 1,
        ajax: {
            type: 'get',
            url: siteURL+"/units/search_by_category",
            delay: 250,
            dataType: 'json',
            processResults: function(data) {
                return { results: data.items };
            },
            cache: true
        }
    });

    $("#location_search").select2({
        placeholder:'Search by Location',
        width: '100%',
        multiple: false,
        minimumInputLength: 1,
        ajax: {
            type: 'get',
            url: siteURL+"/units/search_by_location",
            delay: 250,
            dataType: 'json',
            processResults: function(data) {
                return { results: data.items };
            },
            cache: true
        }
    });

    $(".delete-unit").on('click',function(){
        var id = $(this).attr('data-id');
        var that = $(this);
        if($.trim(id) != ""){
            //disable click event until result not come

            var unit_name = $(this).parents('tr').find('td:first-child').find('a').html();
            bootbox.dialog({
                title: "Are you sure?",
                message: "<p>You want delete <b>Unit: </b>"+unit_name+'?<p> <b>Note:</b> Objectives and tasks of unit "'+unit_name+'" also deleted.',
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
                            var total_rows = $(this).parents('tbody').find('tr').length;
                            that.addClass('prevent-click');
                            $.ajax({
                                type:'get',
                                url:siteURL+'/units/delete_unit',
                                data:{id:id},
                                dataType:'json',
                                success:function(resp){
                                    if(resp.success){
                                        if(total_rows == 1)
                                            that.parents('tr').html('<td colspan="4">No record(s) found.</td>');
                                        else
                                            that.parents('tr').remove();
                                        showToastMessage('UNIT_DELETED');
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
    });
    $(document).off('click','.search_unit').on('click','.search_unit',function(){
        var category_type_search = $("#unit_category").val();
        var country_search = $("#country").val();
        var state_search = $("#state").val();
        var city_search = $("#city").val();

        if($.trim(category_type_search) == "" && $.trim(country_search) == "" && $.trim(state_search) == "" && $.trim(city_search) == ""){
            $("#unit_category").parent('td').addClass('has-error');
            $("#country").parents('td').addClass('has-error');
            $("#state").parents('td').addClass('has-error');
            $("#city").parents('td').addClass('has-error');
        }else{
            $("#unit_category").parent('td').removeClass('has-error');
            $("#location_search").parent('td').removeClass('has-error');
            $("#country").parents('td').removeClass('has-error');
            $("#state").parents('td').removeClass('has-error');
            $("#city").parents('td').removeClass('has-error');
            $(".reset_unit_search").show();
            var token = $(this).attr('data-token');

            $(".unit_loading").show();
            $(this).parents(".loading_content_hide").css('opacity','0.5');

            $.ajax({
                type:'post',
                url:siteURL+'/units/search_units',
                data:{category:category_type_search,country:country_search,state:state_search,city:city_search,_token:token},
                dataType:'json',
                success:function(resp){
                    $(".unit-table tbody tr:last-child").remove();
                    $(".unit-table tbody").html(resp.html);
                    $(".unit_loading").hide();
                    $(".loading_content_hide").css('opacity','1');
                    var the_obj = $('.text_wraps').ThreeDots({
                        max_rows: 1
                    });
                }
            })
        }

        return false;
    });
});