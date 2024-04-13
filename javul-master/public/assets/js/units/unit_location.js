$(function(){
    //get state after selecting country
    $("#country").on('change',function(){
        var value = $(this).val();
        var token = $('[name="_token"]').val();
        if($.trim(value) == "" && value != 247){
            $("#state").html('<option value="">Select</option>').select2({allowClear:true,placeholder:"Select State"});
            $("#city").html('<option value="">Select</option>').select2({allowClear:true,placeholder:"Select City"});
            $("#state").prop('disabled',false);
            $("#city").prop('disabled',false);
        }
        else if($.trim(value) == 247){
            $("#state").prop('disabled',true);
            $("#city").prop('disabled',true);
            return false;
        }
        else
        {

            $(".states_loader.location_loader").show();
            $("#state").prop('disabled',true);
            $("#city").prop('disabled',true);
            $.ajax({
                type:'POST',
                url:siteURL+'/units/get_state',
                dataType:'json',
                async:true,
                data:{country_id:value,_token:token },
                success:function(resp){
                    $(".states_loader.location_loader").hide();
                    $("#state").prop('disabled',false);
                    $("#city").prop('disabled',false);
                    if(resp.success){
                        var html='<option value="">Select</option>';
                        $.each(resp.states,function(index,val){
                            html+='<option value="'+index+'">'+val+'</option>'
                        });
                        $("#state").html(html).select2({allowClear:true,placeholder:"Select State"});
                    }
                }
            })
        }
    });


    //get state after selecting country
    $("#state").on('change',function(){
        var value = $(this).val();
        var token = $('[name="_token"]').val();
        if($.trim(value) == ""){
            $("#city").html('<option value="">Select</option>').select2({allowClear:true,placeholder:"Select City"});
            $("#city").prop('disabled',false);
        }
        else
        {
            $(".cities_loader.location_loader").show();
            $("#city").prop('disabled',true);
            $.ajax({
                type:'POST',
                url:siteURL+'/units/get_city',
                dataType:'json',
                async:true,
                data:{state_id:value,_token:token },
                success:function(resp){
                    $(".cities_loader.location_loader").hide();
                    $("#city").prop('disabled',false);
                    if(resp.success){
                        if(Object.keys(resp.cities).length > 0){
                            var html='<option value="">Select</option>';
                            $.each(resp.cities,function(index,val){
                                html+='<option value="'+index+'">'+val+'</option>'
                            });
                            $("#city").html(html).select2({allowClear:true,placeholder:"Select City"});
                            $("#empty_city_state_name").val('');
                        }else{
                            var html='<option value="">Select</option>';

                                html+='<option value="'+value+'">'+resp.state_name+'</option>';

                            $("#city").html(html).select2({allowClear:false});
                            $("#city").select2("val",value);
                            $("#empty_city_state_name").val(JSON.stringify([{"id":value,"name":resp.state_name}]));
                        }
                    }
                }
            })
        }
    });

    $("#country").select2({
        theme: "bootstrap",
        placeholder:"Select Country",
        templateResult:format,
        escapeMarkup: function(m) {
            return m;
        }
    });

    $("#state").select2({
        theme: "bootstrap",
        placeholder:"Select State"
    });

    $("#city").select2({
        theme: "bootstrap",
        placeholder:"Select City"
    });
});
function format(country) {
    if (country.id == "dash_line1" || country.id == "dash_line"){
        // return ' <span><img src="'+horiz_line+'" style="width:100%"></span> ';
        return '<hr style="margin:0px;">';
    }
    else
        return country.text;
}