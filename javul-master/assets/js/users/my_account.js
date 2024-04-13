$(document).ready(function() {
    $('#tabs').tab();
    $(document).off('click',".withdraw-submit").on('click','.withdraw-submit',function(e){
        $(".remove-alert").remove();
        $that = $(this);
        var $form = $("#withdraw-amount");
        //validate email address for pay
        if($('#paypal_email').length > 0 && $("#payment_method").val() == "PAYPAL"){
            var Emailflag = validateEmail();
            if(!Emailflag){
                e.preventDefault();
                return false;
            }
        }else if($("#payment_method").val() == "Zcash"){
            //show error message when zcash address field is empty
            var zcash_address = $("#zcash_address").val();
            if($.trim(zcash_address) == ""){
                if($.trim(zcash_address) == ""){
                    $("#zcash_address").closest('.col-sm-4').addClass('has-error');
                    var icon = $("#zcash_address").parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", 'Please enter Zcash address').tooltip({'container': 'body'});

                    e.preventDefault();
                    return false;
                }
            }
        }
        $(this).prop('disabled', true);
        var modal_title = "Transfer amount to Paypal account";
        var text = "Transfer all your balance of $"+$(".donation_received").html()+" to your Paypal account?";
        if($("#payment_method").val() == "Zcash"){
            modal_title = "Request to transfer amount to Zcash account";
            text = "Transfer all your balance of "+$(".donation_received").html()+" to your Zcash account?";
        }

        bootbox.dialog({
            message: text,
            title: modal_title,
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-success",
                    callback: function() {
                        if($("#payment_method").val() == "Zcash"){
                            $(".withdraw-submit").html('<span class="saving">Sending request<span>.</span><span>.</span><span>.</span></span>');
                        }else{
                            $(".withdraw-submit").html('<span class="saving">Transferring amount<span>.</span><span>.</span><span>.</span></span>');
                        }
                        $.ajax({
                            type:'post',
                            data:$form.serialize(),
                            url:$('#withdraw-amount').attr('action'),
                            success:function(resp){
                                if(!resp.success){
                                    var html = '';
                                    $.each(resp.errors,function(index,val){
                                        html+="<span>"+val+"</span>";
                                    })
                                    var errorHTML = '<div class="remove-alert alert alert-danger">'+
                                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <a href="'+siteURL+'/assets/images/error-icon.png">'+html
                                    '</div>';
                                    $form.prepend(errorHTML);
                                    $that.prop('disabled', false);
                                    if($("#payment_method").val() == "Zcash"){
                                        $(".withdraw-submit").html('<span class="withdraw-text">Send transfer request</span>');
                                    }else{
                                        $(".withdraw-submit").html('<span class="withdraw-text">Transfer my full balance to my Paypal account</span>');
                                    }
                                }
                                else
                                {
                                    var message = getToastMessage('AMOUNT_TRANSFERED_SUCCESSFULLY')['text'];
                                    var buttonText = "Transfer my full balance to my Paypal account";
                                    if($("#payment_method").val() == "Zcash"){
                                        message = getToastMessage(' REQUEST_SEND_SUCCESSFULLY')['text'];
                                        buttonText = "Send transfer request";
                                    }
                                    $form.find("input,select").val('');
                                    var errorHTML = '<div class="remove-alert alert alert-success">'+
                                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
                                        '<img src="'+siteURL+'/assets/images/success-icon.png"> <strong>Success!!!</strong>'+message+
                                        '</div>';
                                    $form.prepend(errorHTML);
                                    $that.prop('disabled', false);
                                    $(".amount-field").hide();
                                    $(".donation_received").html(resp.availableBalance);
                                    $(".withdraw-submit").html('<span class="withdraw-text">'+buttonText+'</span>');
                                }
                            }
                        });
                    }
                },
                danger: {
                    label: "Cancel",
                    className: "btn-danger",
                    callback:function(){
                        $that.prop('disabled', false);
                    }
                }
            },
            onEscape: function() {
                $that.prop('disabled', false);
            }
        });
        return false;

    });

    $(document).off('click',".withdraw-amount-btn").on('click','.withdraw-amount-btn',function(){
        $(".remove-alert").remove();
        var $form = $("#withdraw-amount");
        var Emailflag = validateEmail();
        var amountFlag = validateAmount();
        if(!Emailflag)
            return false;
        if(!amountFlag)
            return false;

        $(this).prop('disabled', true);
        $(".withdraw-amount-btn").html('<span class="saving">Submitting<span>.</span><span>.</span><span>.</span></span>');
        $.ajax({
            type:'post',
            data:$form.serialize(),
            url:siteURL+'/account/withdraw',
            success:function(resp){
                if(!resp.success){
                    var html = '';
                    $.each(resp.errors,function(index,val){
                        html+="<span>"+val+"</span>";
                    })
                    var errorHTML = '<div class="remove-alert alert alert-danger">'+
                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <a href="'+siteURL+'/assets/images/error-icon.png">'+html
                    '</div>';
                    $form.prepend(errorHTML);
                    $that.prop('disabled', false);
                    $(".withdraw-amount-btn").html('<span class="withdraw-text">Withdraw</span>');
                }
                else
                {
                    $form.find("input,select").val('');
                    var errorHTML = '<div class="remove-alert alert alert-success">'+
                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
                        '<img src="'+siteURL+'/assets/images/success-icon.png"> <strong>Success!!!</strong> '+getToastMessage('AMOUNT_TRANSFERED_SUCCESSFULLY')['text']+
                        '</div>';
                    $form.prepend(errorHTML);
                    $that.prop('disabled', false);
                    $(".amount-field").hide();
                    $(".donation_received").html(resp.availableBalance);
                    $(".withdraw-amount-btn").addClass('withdraw-submit').removeClass('withdraw-amount-btnt').html('<span class="withdraw-text">Verify Email</span>');
                }
            }
        });
        return false;
    });

    $('[data-numeric]').payment('restrictNumeric');
    $('.cc-number').payment('formatCardNumber');
    $('.cc-cvc').payment('formatCardCVC');
    $.fn.toggleInputError = function(erred) {
        this.parents('.form-row').toggleClass('has-error', erred);
        return this;
    };

    $('.update-creditcard').on('click',function() {
        $(".remove-alert").remove();
        var $form = $('#new-credit-card-form');
        var cardType = $.payment.cardType($('.cc-number').val());
        $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
        //$('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));
        $('[name="exp_month"]').toggleInputError(!$.payment.validateCardExpiry($("[name='exp_month']").val(),
            $("[name='exp_year']").val()));
        $('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));
        $("#cc-card-type").toggleInputError(!$.payment.validateCardType($('#cc-card-type').val()));
        $('.cc-brand').text(cardType);
        if($('.has-error').length == 0){
            $(this).prop('disabled', true);
            $that = $(this);
            $that.html('<span class="saving">Updating<span>.</span><span>.</span><span>.</span></span>');
            $.ajax({
                type:'post',
                data:$form.serialize(),
                url:siteURL+'/account/update-creditcard',
                success:function(resp){
                    if(!resp.success){
                        var html = '';
                        $.each(resp.errors,function(index,val){
                            html+="<span>"+val+"</span>";
                        })
                        var errorHTML = '<div class="remove-alert alert alert-danger">'+
                            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <a href="'+siteURL+'/assets/images/error-icon.png">'+html
                        '</div>';
                        $form.prepend(errorHTML);
                        $that.prop('disabled', false);
                        $that.html('Update Details');
                    }
                    else{
                        var errorHTML = '<div class="remove-alert alert alert-success">'+
                            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> ' +
                            '<img src="'+siteURL+'/assets/images/success-icon.png"> <strong>Success!!!</strong> '+getToastMessage('CREDIT_CARD_DETAILS_UPDATED')['text']+
                        '</div>';
                        $form.prepend(errorHTML);
                        $form.find('input,select').val('');
                        $(".card_image").html('');
                        $that.prop('disabled', false);
                        $that.html('Update Details');
                    }
                }
            });
        }
    });

    $("#cc-number").on('keyup',function(){
        var cardType = $.payment.cardType($(this).val());

        if($.trim(cardType) != "" && cardType != 'null')
            $(".card_image").html('<img src="'+url+'/'+cardType+'.png" height="30px;">');
        else
            $(".card_image").html('');

    })

    $("[name='amount_from_available_bal']").on('keyup keydown',function(e){
        var val = $(this).val();
        if(val < avlblamt)
            $(".availableLabel").html(avlblamt-val);
        else{
            $(".availableLabel").html(0);
            $(this).val(avlblamt);
        }
    })

    $("[name='credit_available_bal']").on('click',function(){
        var val = $(this).val();
        $(".donationDiv").hide();
        $("."+val).show();
    });

    $("#pay_now").on('click',function(){
        var amount = $("[name='amount_from_available_bal']").val();
        if($.trim(amount) == "" || (amount != avlblamt && amount > avlblamt )){
            $("[name='amount_from_available_bal']").parent('div').addClass('has-error');
            return false;
        }
    });

    //change card number on selected card
    /*$("[name='credit_cards']").on('change',function(){
        var val =$(this).val();
        if(val == "")
            $("[name='card_number']").val('');
        else{
            $loading.show();
            $.ajax({
                type:'get',
                data:{last4:val},
                url:siteURL+'/funds/get-card-name',
                success:function(resp){
                    if($.trim(resp) != ""){
                        $(".reused_card_image").html('<img src="'+siteURL+'/assets/images/'+resp+'" style="height:40px;"/>');
                    }
                    else
                        $(".reused_card_image").html('');
                    $loading.hide();
                }
            });
            $("[name='card_number']").val('XXXX XXXX XXXX '+val);
        }
        return false;
    });*/

    $("#state").prop('disabled',true);
    $("#city").prop('disabled',true);

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
            // $("#city").prop('disabled',true);
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
                    $("#city").prop('disabled',true);
                    if(resp.success){
                        var html='<option value="">Select</option>';
                        $.each(resp.states,function(index,val){
                            html+='<option value="'+index+'">'+val+'</option>'
                        });
                        $("#state").html(html).select2({allowClear:true,placeholder:"Select State"});
                        $("#city").html(html).select2({allowClear:true,placeholder:"Select City"});
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
                        var html='<option value="">Select</option>';
                        $.each(resp.cities,function(index,val){
                            html+='<option value="'+index+'">'+val+'</option>'
                        });
                        $("#city").html(html).select2({allowClear:true,placeholder:"Select City"});
                    }
                }
            })
        }
    });

    function enableCityState() {
        $("#city").prop('disabled',false);
        $("#state").prop('disabled',false);
    }

    $("#update_profile").on('click',function(){
        enableCityState();
        var profilePic = $(".kv-file-content").find('img').attr("src");
        $form = $("#personal-info");
        $form.find('.help-block').html('');
        $.ajax({
            type:'POST',
            url:siteURL+'/account/update_personal_info',
            dataType:'json',
            async:true,
            data:$form.serialize() + '&profilePic=' + profilePic,
            success:function(resp){
                if(resp.success){
                    $(".message").html('<div class="alert alert-success" style="margin-bottom:15px;">'+
                        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+
                    '<img src="'+siteURL+'/assets/images/success-icon.png"> <strong>Success!</strong>'+getToastMessage('PROFILE_UPDATED_SUCCESSFULLY')['text']+
                    '</div>');

                }
                else{
                    $.each(resp.errors,function(index,value){
                        $form.find("#"+index).parent('.col-sm-4').find('.help-block').html(value);
                    });
                }
            }
        })
    });

    function formatSkills (repo) {
        if (repo.loading) return repo.text;

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.name + "</div></div></div></div>";
        return markup;
    }

    function formatSkillsSelection (repo) {
        return repo.text;
    }

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

    var skillSelect2 = $("#task_skills").select2({
        // allowClear: true,
        showSearchBox: false,
        theme: "bootstrap job-skill-options",
        width: '100%',
        displayValue:'skill_name',
        // ajax: {
        //     url: siteURL+"/job_skills/get_skills",
        //     dataType: 'json',
        //     delay: 250,
        //     data: function (params) {
        //         return {
        //             term: params.term, // search term
        //             page: params.page
        //         };
        //     },
        //     processResults: function (data, params) {

        //         // parse the results into the format expected by Select2
        //         // since we are using custom formatting functions we do not need to
        //         // alter the remote JSON data, except to indicate that infinite
        //         // scrolling can be used
        //         params.page = params.page || 1;

        //         return {
        //             results: data.items,
        //             pagination: {
        //                 //more: (params.page * 1) < data.total_counts
        //                 more:false
        //             }
        //         };
        //     },
        //     cache: true
        // },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        // minimumInputLength: 1,
        // templateResult: formatSkills, // omitted for brevity, see the source of this page
        // templateSelection: formatSkillsSelection // omitted for brevity, see the source of this page
    });

    skillSelect2.on("select2:unselect",function(e){
        var id = e.params.data.id;
        var index = selected_skill_id.indexOf(id);
        if (index > -1) {
            selected_skill_id.splice(index, 1);
        }
        return false;
    });
    //disabled search input from job skill #101
    skillSelect2.on('select2:opening select2:closing', function( event ) {
        var $searchfield = $( '#'+event.target.id ).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    });

    var areaOfInterestSelect2 =  $("#area_of_interest").select2({
        allowClear: true,
        width: '100%',
        displayValue:'skill_name',
        ajax: {
            url: siteURL+"/area_of_interest/get_area_of_interest",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {

                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        //more: (params.page * 1) < data.total_counts
                        more:false
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatSkills, // omitted for brevity, see the source of this page
        templateSelection: formatSkillsSelection // omitted for brevity, see the source of this page
    });
    areaOfInterestSelect2.on("select2:unselect",function(e){
        var id = e.params.data.id;
        var index = selected_area_of_interest_id.indexOf(id);
        if (index > -1) {
            selected_area_of_interest_id.splice(index, 1);
        }
        return false;
    });
});

function validateEmail(){
    var email=$("#paypal_email").val();
    if($.trim(email) =="")
    {
        $("#paypal_email").closest('.col-sm-4').addClass('has-error');
        var icon = $("#paypal_email").parent('.input-icon').children('i');
        icon.removeClass('fa-check').addClass("fa-warning");
        icon.attr("data-original-title", 'Please enter paypal email').tooltip({'container': 'body'});
        return false;
    }
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var flag = re.test(email);
    if(!flag){
        $("#paypal_email").closest('.col-sm-4').addClass('has-error');
        var icon = $("#paypal_email").parent('.input-icon').children('i');
        icon.removeClass('fa-check').addClass("fa-warning");
        icon.attr("data-original-title", 'Email address is invalid').tooltip({'container': 'body'});
        return false;
    }
    var icon = $("#paypal_email").parent('.input-icon').children('i');
    $("#paypal_email").closest('.col-sm-4').removeClass('has-error').addClass('has-success'); // set success class to the control group
    icon.removeClass("fa-warning").addClass("fa-check");
    return true;
}

function validateAmount(){
    var amount=$("#cc-amount").val();
    if($.trim(amount) =="")
    {
        $("#cc-amount").closest('.col-sm-4').addClass('has-error');
        var icon = $("#cc-amount").parent('.input-icon').children('i');
        icon.removeClass('fa-check').addClass("fa-warning");
        icon.attr("data-original-title", 'Please enter amount').tooltip({'container': 'body'});
        return false;
    }

    if(isNaN(amount)){
        $("#cc-amount").closest('.col-sm-4').addClass('has-error');
        var icon = $("#cc-amount").parent('.input-icon').children('i');
        icon.removeClass('fa-check').addClass("fa-warning");
        icon.attr("data-original-title", 'Amount must be numeric only').tooltip({'container': 'body'});
        return false;
    }
    var icon = $("#cc-amount").parent('.input-icon').children('i');
    $("#cc-amount").closest('.col-sm-4').removeClass('has-error').addClass('has-success'); // set success class to the control group
    icon.removeClass("fa-warning").addClass("fa-check");
    return true;
}
function format(country) {
    if (country.id == "dash_line1" || country.id == "dash_line"){
        // return ' <span><img src="'+horiz_line+'" style="width:100%"></span> ';
        return '<hr style="margin:0px;">';
    }
    else
        return country.text;
}

//cancel button at zcash withdrawal list
$(document).on('click',".zcash-cancel",function(e){
    e.preventDefault();
    var url = $(this).attr('href');
    var transaction_id = $(this).attr('data-id');
    let confirmMessage = bootbox.confirm({ 
        size: "small",
        message: "Are you sure you want to cancel this request?", 
        callback: function(action){
            if(action){
                $.ajax({
                    type:'get',
                    data:{'cancel_request':true,'transaction_id':transaction_id},
                    success:function(response){
                        if(response.success){
                            toastr['success'](response.message, '');
                            setTimeout(() => {
                                window.location.reload(true);    
                            }, 300);                            
                        }
                    }
                });
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

