$(function(){

    /*********************** if user submit form and return back then display tostr ************************/
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    if($.trim(msg_flag) != "")
    {
        toastr[msg_type](msg_val, ''); // Wire up an event handler to a button in the toast, if it exists
    }
    /********************************************** END *****************************************************/
})