$(document).ready(function(){

    /**
     * Newsletter Forms Validation
     */
    jQuery("#subcription_frm").validationEngine();
    $('#submit_form').click(function(event){
        event.preventDefault();
        $(this).parent().attr('action', 'subcription-thanks');
        if($('#subcription_frm').validationEngine('validate')){
            $(this).parent().submit();
        }
    })
    /**
     * Contact Forms Validation
     */

    jQuery("#contact_form").validationEngine();
    $('#contact_form').submit(function(event){
        var error_msg = "";
        if($.trim($("#contact_form").parent().find("[name='name']").val()) == ""){
            error_msg = error_msg + "Please fill the name\n";
        }
        if($.trim($("#contact_form").parent().find("[name='phone']").val()) == "" && $.trim($("#contact_form").parent().find("[name='email']").val()) == ""){
            error_msg = error_msg + "Please fill the Email or Phone\n";
        }
        if($.trim($("#contact_form").parent().find("[name='message']").val()) == ""){
            error_msg = error_msg + "Please fill the message\n";
        }
        if(error_msg != ""){
            alert(error_msg);
            return false;
        }
        else{
            var body = 'Name: '  + $.trim($("#contact_form").parent().find("[name='name']").val())    + '<br>' +
                       'Phone: ' + $.trim($("#contact_form").parent().find("[name='phone']").val())   + '<br>' +
                       'Email: ' + $.trim($("#contact_form").parent().find("[name='email']").val())   + '<br>' +
                       'Message: ' + '<br>' + $.trim($("#contact_form").parent().find("[name='message']").val());

            $("#contact_form").find("[name='email-body']").val(body);
            $("#contact_form").attr('action', '/frontend/notifications/test');
            $(this).trigger('submit');
        }
    })


    /**
     * Payment Forms Validation
     */

    jQuery("#payment_form").validationEngine();
    $('#submit-payment').click(function(event){
        event.preventDefault();
        $(this).parent().attr('action', 'payment-thanks');
        if($('#payment_form').validationEngine('validate')){
            $(this).parent().submit();
        }
    })

    /**
     * End Forms Validation
     */


});