$(function(){
    $(document).off("change","#category_firstbox").on('change',"#category_firstbox",function(event){
        var that = $(this);
        getNextBox(that,page);
        if(page=="unit") {
            var text = $(this).find(':selected').text();
            text= text.replace('>','');
            $(".selected_text_task").html('<b>' + text + '</b>');
            browse_category_box.find('.okay-btn').prop('disabled',false);
            //selected_categories_id.push($(this).val());

        }
        return false;
    });
    $(document).off("click","a.select_category").on('click',"a.select_category",function(event){
        $(this).parents('.new_box').find("a.hierarchy").removeClass('selected');
        var that = $(this).addClass('selected');
        if(page=="unit"){
            var text = $(this).html();
            text= text.replace('&nbsp; &gt;','');
            $(".selected_text_task").html('<b>' + text + '</b>');
            browse_category_box.find('.okay-btn').prop('disabled',false);
            selected_categories_id.pop();
            selected_categories_id.push($(this).attr('data-value'));
        }
        // $(this).parents('.hierarchy_parent').find(".buttons").find(".edit_category").removeClass("disabled");
        getNextBox(that,page);
        return false;
    });

    function getNextBox(that){

        if(that.attr('id') == "category_firstbox"){
            var id =that.val();
            var box_number = that.data('number');
            var type= that.find(':selected').attr('data-type');
        }
        else
        {
            var id =that.attr('data-value');
            var box_number = that.attr('data-number');
            var type= that.attr('data-type');
        }
        $.ajax({
            type:'get',
            url:siteURL+'/unit_categories/get_next_level_categories',
            data:{id:id,type:type,page:page},
            dataType:'json',
            success:function(resp){
                if(resp.success){

                    if(Object.keys(resp.data).length > 0)
                    {
                        $(".add_edit_categories").remove();
                        var next_level=$(".all_levels_category").find('.hierarchy_parent').length;
                        if(next_level > box_number ){
                            for(var i=box_number;i<=next_level;i++){
                                if($(".all_levels_category").find('.hierarchy_parent').length != box_number)
                                    $(".all_levels_category").find('.hierarchy_parent:last').remove();
                            }
                        }
                        next_level=$(".all_levels_category").find('.hierarchy_parent').length;

                        var html = '<div class="hierarchy_parent"><div class="hierarchy new_box" data-number="'+
                            (next_level+1)+'">';
                        $.each(resp.data,function(index,val){
                            html+='<a  href="" class="hierarchy select_category" data-number="'+(next_level+1)+'" data-value="'+index+'" ' +
                                'data-type="'+val.type+'" >'+val.name+'&nbsp; ></a>';
                        });
                        if(page == "site_admin") {
                            html += '</div>' +
                                '<div class="buttons">' +
                                '<div style="display:block;">' +
                                '<a class="btn black-btn btn-xs add_category" style="text-decoration: none; padding: 5px 10px;">' +
                                '<i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span>' +
                                '</a></div>' +
                                '<div style="display:block;margin-top:5px;"><a class="edit_category btn black-btn ' +
                                'btn-xs" style="padding-top: 6px;padding-bottom: 6px">Edit</a></div>' +
                                '</div></div>';
                        }

                        $(".all_levels_category").append(html);
                        return false;
                    }
                    else{
                        var current_clicked_category_temp = parseInt($(that).attr('data-number'));
                        if(current_clicked_category_temp == 1)
                            selected_categories_id.pop();

                        $(".add_edit_categories").remove();
                        var next_level=$(".all_levels_category").find('.hierarchy_parent').length;
                        if(next_level > box_number ){
                            for(var i=box_number;i<=next_level;i++){
                                if($(".all_levels_category").find('.hierarchy_parent').length != box_number)
                                    $(".all_levels_category").find('.hierarchy_parent:last').remove();
                            }
                        }
                        if($(".all_levels_category").find(".hierarchy_parent").length > 1)
                            var selected_text = $(".all_levels_category").find(".hierarchy_parent:last").find("a.selected").html();
                        else
                            var selected_text = $(".all_levels_category").find(".hierarchy_parent").eq(0).find('select').find(':selected').text();
                        selected_text = selected_text.replace(">","");
                        selected_text = selected_text.replace("&nbsp; &gt;","");
                        if(page == "site_admin") {
                            var html = '<div class="add_edit_categories"><div style="border:1px solid #828790;border-style: dashed;background-color:#EEEEEE;text-align:center;">' +
                                '<img src="' + siteURL + '/assets/images/completed.png" style="width:20px;vertical-align:top;"><span>Selected:<br/>' + selected_text.replace(">", "") + '</span>' +
                                '</div><div style="margin-top:5px;margin-left:10px;">' +
                                '<a  class="add_category btn black-btn btn-xs" data-pos="last" style="text-decoration: none; padding: 5px 10px;display: block;">' +
                                '<i class="fa fa-plus plus"></i> <span class="plus_text" style="left:-5px;">ADD</span></button>' +
                                '<a class="edit_category btn black-btn btn-xs" data-pos="last" style="text-decoration: none; ' +
                                'padding-top: 6px;padding-bottom: 6px;display: block;margin-top:5px;">Edit</a>' +
                                '<a class="delete_category btn black-btn btn-xs" style="text-decoration: none;' +
                                'padding-top: 7px;padding-bottom: 7px;display: block;margin-top:5px;">DELETE CATEGORY</a></div>' +
                                '</div>';
                        }

                        $(".all_levels_category").append(html);
                    }

                }

            }
        });
        return false;
    }
});