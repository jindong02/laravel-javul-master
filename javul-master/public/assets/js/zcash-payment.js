$(document).ready(function(){
    $(document).on('click','.cb-proceed', function(){
        var url = $(this).attr('href');
        let confirmMessage = bootbox.confirm({ 
            size: "small",
            message: "Are you sure you want to transfer?", 
            callback: function(action){
                if(action){
                    $("#loadingDiv").show();
                    $.ajax({
                        type:'get',
                        url:url,
                        success:function(response){
                            $("#loadingDiv").hide();
                            //console.log("Current Response -> ",response);
                            if(response.success){
                                //console.log("Response Success -> ",response);
                                toastr['success'](response.message, '');
                                window.location.reload(true);
                            }else{
                                //console.log("Error Response -> ",response);
                                if(response.error){
                                    //Get 2FAToken for zcash transaction
                                    if(response.need_to_unlock){
                                        let box = bootbox.prompt({ 
                                            size: "small",
                                            title: "Please enter varification code.", 
                                            callback: function(result){
                                                //console.log('Varification Code Data  :- ',result);
                                                if(result && result != ''){
                                                    $("#loadingDiv").show().css({'z-index':'10051'});
                                                    var two_faToken = result.trim();
                                                    $.ajax({
                                                        type:'get',
                                                        data:{two_faToken : two_faToken},
                                                        url:url,
                                                        success:function(response){
                                                            $("#loadingDiv").hide().css({'z-index':''});
                                                            //console.log("Response from 2FA Token request -> ",response);
                                                            if(response.success){
                                                                toastr['success'](response.message, '') ;
                                                                $('.bootbox-input.bootbox-input-text').val('');
                                                                box.modal('hide')
                                                                window.location.reload(true);
                                                            }else{
                                                                toastr['error'](response.message, '') ;
                                                                $('.bootbox-input.bootbox-input-text').val('');
                                                            }
                                                        }
                                                    });
                                                    return false;
                                                }else{
                                                    //return false;
                                                    $('.bootbox-input.bootbox-input-text').val('');
                                                    box.modal('hide')
                                                }
                                            }
                                        });
                                        $(".div-table-second-cell").css('z-index','100');
                                        $(".list-item-main").css('z-index','100');

                                        box.on("hidden.bs.modal", function (e) {
                                            $(".list-item-main").css('z-index','99999');
                                            $(".div-table-second-cell").css('z-index','99999');
                                        });

                                        box.modal('show');
                                        return false;
                                    }
                                    if($.trim(response.message) != '')
                                        toastr['error'](response.message, '') ;
                                }
                            }
                        }
                    });
                }else{
                    console.log("Cancel Clicked");
                }
            }
          });

        //change z-index of site activity bar content
        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');

        confirmMessage.on("hidden.bs.modal", function (e) {
            if(!$("#loadingDiv").is(":visible")){
                $(".list-item-main").css('z-index','99999');
                $(".div-table-second-cell").css('z-index','99999');
            }
        });
        return false;
    });

    //Calcel button click
    $(document).on('click','.cb-cancel', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        let confirmMessage = bootbox.confirm({ 
            size: "small",
            message: "Are you sure you want to reject request?", 
            callback: function(action){
                if(action){
                    window.location.href = url;
                }
            }
          });

        //change z-index of site activity bar content
        $(".div-table-second-cell").css('z-index','100');
        $(".list-item-main").css('z-index','100');

        confirmMessage.on("hidden.bs.modal", function (e) {
            if(!$("#loadingDiv").is(":visible")){
                $(".list-item-main").css('z-index','99999');
                $(".div-table-second-cell").css('z-index','99999');
            }
        });
    })

});