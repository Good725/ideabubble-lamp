var form_submited = false; //Check before send
var valid_cart = true; //Check before send

var checkout_data = checkout_data || {};

checkout_data.ini = function(){
    var products_list = $('#checkoutTable .product_line').get();

    //if(typeof checkout_data.products == 'undefined'){
    checkout_data.products = [];
    //}

    var length = products_list.length;
    //Foreach product add to the product variable
    for (var i = 0; i < length; i++) {
        var product = {};
        product.id = $(products_list[i]).data('product_id');
        product.item_name = $(products_list[i]).data('product_name');
        product.quantity = parseInt($(products_list[i]).find('.quantity').val());
        checkout_data.products.push(product);
    }
}

checkout_data.ini_cardpayment = function(){
    //Load default data
    checkout_data.ini();

    //payment details
    checkout_data.payment_ref       = document.getElementById("payment_ref").value;
    checkout_data.payment_total     = document.getElementById("payment_total").value;
    checkout_data.comments          = document.getElementById("comments").value;

    //Name and address
    checkout_data.ccName    = document.getElementById("ccName").value;
    checkout_data.phone     = document.getElementById("phone").value;
    checkout_data.email     = document.getElementById("email").value;

    //Credit Card Payment Details
    checkout_data.ccType  =  document.getElementById("ccType").options[document.getElementById("ccType").selectedIndex].value;
    checkout_data.ccNum   =  document.getElementById("ccNum").value.replace(/[\s\-]/g,'');
    checkout_data.ccv     =  document.getElementById("ccv").value;
    checkout_data.ccExpMM =  document.getElementById("ccExpMM").options[document.getElementById("ccExpMM").selectedIndex].value;
    checkout_data.ccExpYY =  document.getElementById("ccExpYY").options[document.getElementById("ccExpYY").selectedIndex].value;

    //sign up the newsletter
    checkout_data.signupnewsletter   =  document.getElementById("signupCheckbox").checked;

    //captcha
	if(document.getElementById("recaptcha_response_field")){
    	checkout_data.recaptcha_response_field   =  document.getElementById("recaptcha_response_field").value;
	    checkout_data.recaptcha_challenge_field   =  document.getElementById("recaptcha_challenge_field").value;
	} else {
		checkout_data.recaptcha_response_field = "";
		checkout_data.recaptcha_challenge_field = "";
	}

  //Thanks page
    if(document.getElementById("thanks_page")){
        checkout_data.thanks_page   =  document.getElementById("thanks_page").value;
    }
}

/**
 *
 * Paypal
 */
checkout_data.paypal_error = function (status){
    if(status != CHECKOUT.STATUS_S_OK){
        display_message('error', 'Error: An error has occurred with PayPal payment, please try again');
    }
};


function submitCheckout()
{
    //Protect the form. only one instance is possible
    if(form_submited == true || valid_cart == false){ return false; }
    form_submited = true;

	var $form = $('#payment_form');
	var $button_area = $('.submit_checkout_button');
    var submit_button = $button_area.html();
	$button_area.html('<img style="text-align: center" src="/assets/default/images/ajax-loader.gif" alt="AJAX loader">');

	var valid;
	if ($form.find('[data-validation-engine]').length)
	{
		valid = $form.validationEngine().validationEngine('validate');
	}
	else
	{
		valid = $form.validationEngine('validate');
	}

    if (valid)
	{
        //get data
        checkout_data.ini_cardpayment();
        checkout_data_s = JSON.stringify(checkout_data);
        //Submit payment
        $.ajax({
            url:'/frontend/payments/payment_processor_ib_pay',
            data:{ checkout:checkout_data_s},
            type: 'POST',
            dataType:'json'
        })
            .done(function(data){
                if(data.status == 'success'){
                    location.href = data.redirect;
                }
                else{
                   display_message('error', 'Error: ' +data.message);
               }
            })
            .fail(function(data){
                display_message('error', 'Error: Network error, please check your internet connection');
            });
    }
    else{
        setTimeout('removeBubbles()', 20000);
    }
    form_submited = false;
	$button_area.html(submit_button);
}


function removeBubbles() {
    $('.formError').each(function(i,e){document.body.removeChild(e);});
}


function display_message(type, message){
    switch( type ){
        case 'info':
            $('#checkout_messages').html('<div class="checkout_message"><a class="close">×</a>'+ message +'</div>');
            $(document).scrollTop(0);
            break;
        case 'success':
            $('#checkout_messages').html('<div class="checkout_message"><a class="close">×</a>'+ message +'</div>');
            $(document).scrollTop(0);
            break;
        case 'error':
            $('#checkout_messages').html('<div class="checkout_message_error"><a class="close">×</a></span>'+ message +'</div>');
            $(document).scrollTop(0);
            break;
        default:
    }

}

/** Close messages **/
$('#checkout_messages').on('click', '.close', function(){
    $(this).parent().remove();
});

$(document).on('click','#update_customer_and_pay',function(){
    var checkout_data = {};
    checkout_data.ccType = $("#cc_card_type").val();
    checkout_data.ccName = $("#cc_name_on_card").val();
    checkout_data.ccNum = $("#cc_card_number").val();
    checkout_data.ccv = $("#cc_ccv_number").val();
    checkout_data.ccExpMM = $("#cc_ExpMM").val();
    checkout_data.ccExpYY = $("#cc_ExpYY").val();
    checkout_data.realvault_card_id = $("#realvault_card_id").val();
	if(!checkout_data.realvault_card_id){
		checkout_data.realvault_card_id = false;
	}
	checkout_data.comments = $("#service_payment_comments").val();
    checkout_data.email = $("#email").val();
    checkout_data.phone = $("#payment_phone").val();
	checkout_data.invoices = [];
	var $invoices = $("[name*='pay_invoice']");
	for(var i = 0 ; i < $invoices.length ; ++i){
		if($invoices[i].checked){
			checkout_data.invoices.push($invoices[i].value);
		}
	}
	checkout_data.save_card = $("#save_card").prop('checked') ? true : false;
    checkout_data = JSON.stringify(checkout_data);
    $("#checkout_data").val(checkout_data);
    $("#contact_payment_form").submit();
});

    $("#submit_payment").on('click',function()
    {
        var required_fields = $('#cc_name_on_card');
        required_fields.attr('data-validation-engine', 'validate[required,custom[onlyLetterSp]]');
        var required_fields2 = $('#cc_card_number');
        required_fields2.attr('data-validation-engine', 'validate[required,funcCall[luhnTest]]');
        var required_fields3 = $('#cc_ccv_number, #cc_ExpMM, #cc_ExpYY');
        required_fields3.attr('data-validation-engine', 'validate[required,custom[onlyNumberSp]]');

        var valid = $(this).parent('form').validationEngine().validationEngine('validate');
        required_fields.attr('data-validation-engine', '');
        required_fields2.attr('data-validation-engine', '');
        required_fields3.attr('data-validation-engine', '');

        if (valid)
        {
            $('#tab-contact_details').find('.confirmation_message').html('Please confirm the details below are correct to continue.');
            $('#update_customer_and_pay').html('Confirm and Make Payment');
            $(".service-tabs ul li a[href='#tab-contact_details']").click();
        }
    });

$("#registration_form_email").on('change',function(){
    if($("#registration_form_email").val() != '')
    {
        $.post('/frontend/extra/check_email',{email: $("#registration_form_email").val()},function(data){
            console.log(data);
            if(data == '1')
            {
                $("#email_error").show();
                $("#registration_form_submit").hide();
            }
            else
            {
                $("#email_error").hide();
                $("#registration_form_submit").show();
            }
        });
    }
});




/** Change payment method **/
/**
 * Only add this function if there is more than one payment method, usually Paypal and Credit Card
 *
 * Method options:
 * method_1 Paypal
 * method_2 Credit Card
 */
function changeMethod(element){
    id = element.id;
    //High light the result, change the class from "gray" to "selected"
    $('.payment_method').addClass('gray').removeClass('selected');
    $('#' + id).addClass('selected').removeClass('gray');

    //Hide the payments views and display the right payment view/form/button
    $('.payment_method_view').hide();
    switch(id){
        case 'method_1':
            $('#checkoutForm').show();
            break;
        case 'method_2':
            $('#paypalButton').show();
            break;
    }
}

window.luhnTest = function(input){
    var value = input.val();
    // accept only digits, dashes or spaces
    if (/[^0-9-\s]+/.test(value)) {
        return "Invalid Credit/Debit Card Number";
    }

    // The Luhn Algorithm. It's so pretty.
    var nCheck = 0, nDigit = 0, bEven = false;
    value = value.replace(/\D/g, "");

    for (var n = value.length - 1; n >= 0; n--) {
        var cDigit = value.charAt(n),
            nDigit = parseInt(cDigit, 10);

        if (bEven) {
            if ((nDigit *= 2) > 9) nDigit -= 9;
        }

        nCheck += nDigit;
        bEven = !bEven;
    }

    return (nCheck % 10) == 0 ? undefined : "Invalid Credit/Debit Card Number";
};
