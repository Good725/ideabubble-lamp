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
        checkout_data.products[$(products_list[i]).data('line_id')] = product;
    }
}

checkout_data.ini_cardpayment = function(){
    //Load default data
    checkout_data.ini();
    checkout_data.custom();

    //Name and address
    checkout_data.ccName    = document.getElementById("ccName").value;
    checkout_data.address_1 = document.getElementById("address_1").value;
    checkout_data.address_2 = document.getElementById("address_2").value;
    checkout_data.address_3 = document.getElementById("address_3").value;
    checkout_data.address_4 = document.getElementById("address_4").value;
    checkout_data.phone     = document.getElementById("phone").value;
    checkout_data.email     = document.getElementById("email").value;

    //Shipping Address
    if(document.getElementById("addressCheckbox").checked){
        checkout_data.shipping_same_address = "true";
        checkout_data.shipping_name         = checkout_data.ccName;
        checkout_data.shipping_address_1    = checkout_data.address_1;
        checkout_data.shipping_address_2    = checkout_data.address_2;
        checkout_data.shipping_address_3    = checkout_data.address_3;
        checkout_data.shipping_address_4    = checkout_data.address_4;
    }
    else{
        checkout_data.shipping_same_address = "false";
        checkout_data.shipping_name         = document.getElementById("shipping_name").value;
        checkout_data.shipping_address_1    = document.getElementById("shipping_address_1").value;
        checkout_data.shipping_address_2    = document.getElementById("shipping_address_2").value;
        checkout_data.shipping_address_3    = document.getElementById("shipping_address_3").value;
        checkout_data.shipping_address_4    = document.getElementById("shipping_address_4").value;
    }

    //Credit Card Payment Details
    checkout_data.ccType  =  document.getElementById("ccType").options[document.getElementById("ccType").selectedIndex].value;
    checkout_data.ccNum   =  document.getElementById("ccNum").value;
    checkout_data.ccv     =  document.getElementById("ccv").value;
    checkout_data.ccExpMM =  document.getElementById("ccExpMM").options[document.getElementById("ccExpMM").selectedIndex].value;
    checkout_data.ccExpYY =  document.getElementById("ccExpYY").options[document.getElementById("ccExpYY").selectedIndex].value;
	checkout_data.MD      =  document.getElementById("MD") ? document.getElementById("MD").value : null;
	checkout_data.MDX     =  document.getElementById("MDX") ? document.getElementById("MDX").value : null;
	checkout_data.PaRes   =  document.getElementById("PaRes") ? document.getElementById("PaRes").value : null;

    //Comments
    checkout_data.comments   =  document.getElementById("comments").value;

    //sign up the newsletter
    checkout_data.signupnewsletter   =  document.getElementById("signupCheckbox").checked;
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
        display_message('error', 'Error: An error has occurred with paypal payment, please try again');
    }
}

/**
 * update product list prices
 * @param data
 */
checkout_data.update_product_list = function(data){

    //For each element, update the values
    //Checkout
    $('.checkoutTable .product_line').each(function(line){
            $(this).find('.quantity').val(data.lines[line].quantity);
            $(this).find('.price_total_line').html('&euro;' + data.lines[line].price);
        }
    )
    //Minicart
    $('.mycart_summary_details .product_line').each(function(line){
            $(this).find('.quantity').html(data.lines[line].quantity);
            $(this).find('.price_total_line').html('&euro;' + data.lines[line].price);
        }
    )

    //Shipping
    if(!data['shipping_price']) data['shipping_price'] = '0';
    $('.checkoutTable .postage').html('&euro;' + data['shipping_price']);
    //Total
    if(!data['final_price']) data['final_price'] = data['cart_price'];
    $('.checkoutTable .totalprice').html('&euro;' + data['final_price']);
}

checkout_data.update_cart = function(data){
    $('.mycart_items_amount').html(data.number_of_items);
    if(!data.final_price) data.final_price = data.cart_price;
    $('#mycart_total_price').html(data.final_price);
    $('.cart_hidden').show();
}

checkout_data.display_empty_cart = function(){
    $('.mycart_items_amount').html(0);
    $('#mycart_total_price').html(0);
    $('.cart_hidden').hide();
    $('#checkout_cart .mycart_summary .mycart_summary_details').hide();
}

checkout_data.update_cart_pl_discount = function(data){
    if(!data.final_price) data.final_price = data.cart_price;
    $('.totalprice').html('€' + data.final_price);

    if(typeof(data.payback_loyalty_discount) != "undefined" && data.payback_loyalty_discount > 0){
        $('#pl_discount_line').html('<td class="product_line_first_td">'+ 'Payback Loyalty discount' +'</td><td></td><td></td><td class="pb_discount_price">€ -'+ data.payback_loyalty_discount +'</td><td></td>');
    }
    else{
        $('#pl_discount_line').html('<td></td><td></td><td></td><td></td><td></td>');
    }
}

checkout_data.add_to_cart_callback = function(status, data){
    switch(status){
        case CHECKOUT.STATUS_S_OK:
            valid_cart = true;
            $('#checkout_messages').html('');
            checkout_data.update_product_list(data);
            checkout_data.update_cart(data);
            break;
        case CHECKOUT.STATUS_E_ERROR:
            display_message('error', 'Error adding the product, please try again');
            valid_cart = false;
            break;
        default:
            display_message('error', 'Error adding the product, please try again');
            valid_cart = false;
            break;
    }
}
checkout_data.reload_minicart = function(status){
    if(status == CHECKOUT.STATUS_S_OK){
        //Request the complete view
        $.post('/frontend/frontend/get_template/mini_cart', function(data, status){
            if(status == 'success'){
                $('#mini_cart_wrapper').html(data);
            }
        });
    }
}


function submitCheckout(){
    if(checkout_data.getTotalPrice() < 10){
        return false;
    }

    //Protect the form. only one instance is possible
    if(form_submited == true || valid_cart == false){ return false; }
    form_submited = true;

    var submit_button = $('.submit_checkout_button').html();
    $('.submit_checkout_button').html('<img style="text-align: center" src="'+ shared_assets +'/img/ajax-loader.gif" alt="ajax-loader">');


    subbmit_status = $("#creditCardForm").validationEngine('validate');

    if(subbmit_status){
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
        setTimeout('removeBubbles()', 5000);
    }
    form_submited = false;
    $('.submit_checkout_button').html(submit_button);
}


function removeBubbles() {
    $('.formError').each(function(i,e){document.body.removeChild(e);});
}

//CHECKOUT.modifyCart(0, CHECKOUT.MODIFY_CART_ADD, 1,function(d){console.log(d)});
function changeZone(zone_id){
    CHECKOUT.setPostalZone(zone_id, function(status,data){
        switch(status){
            case CHECKOUT.STATUS_S_OK:
                valid_cart = true;
                $('#checkout_messages').html('');
                checkout_data.update_product_list(data);
                checkout_data.update_cart(data);
                break;
            case CHECKOUT.STATUS_E_ERROR:
                display_message('error', 'Sorry, we have experienced an issue checking out this product. Please contact us today for help purchasing online');
                valid_cart = false;
                break;
            default:
                display_message('error', 'Sorry, we have experienced an issue checking out this product. Please contact us today for help purchasing online');
                valid_cart = false;
                break;
        }
    })
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

/** Delete Line **/
$('.checkoutTable').on('click', '.delete_product', function(){
    var line_id = $(this).parent().parent().data('line_id');
    //Display message
    $(delete_msg).dialog({
        resizable: false,
        height:140,
        modal: true,
        buttons: {
            Yes: function() {
                $( this ).dialog( "close" );
                delete_cart_line(line_id);
            },
            No: function() {
                $( this ).dialog( "close" );
            }
        }
    });
});

function delete_cart_line(line_id){
    CHECKOUT.deleteFromCart(line_id, function(status,data){
        if(status != CHECKOUT.STATUS_S_OK ){
            display_message('error', 'Error: The product can\'t be deleted, please check your network connection and try again');
            $(document).scrollTop(0);
        }
        else{
            //Remove form checkout
            $('.checkoutTable .product_line[data-line_id='+ line_id +']').remove();
            //Remove from mini cart
            $('.mycart_summary_details .product_line[data-line_id='+ line_id +']').remove();
            if(data != null){
                checkout_data.update_product_list(data);
                checkout_data.update_cart(data);
                checkout_data.apply_garretts_rules();
            }
            else{
                checkout_data.display_empty_cart();
            }
        }
    });
    checkout_data.apply_garretts_rules();
}

var delete_msg =
'<div id="dialog-confirm" title="Remove Item">'+
    '<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Are you sure you want to remove this item from your shopping cart?</p>'+
'</div>';


/** Increase product amount **/
$('.checkoutTable').on('click', '.increase_product_amount', function(){
    var line_id = $(this).parent().parent().data('line_id');
    CHECKOUT.modifyCart(line_id, CHECKOUT.MODIFY_CART_ADD, 1, function(status,data){
        switch(status){
            case CHECKOUT.STATUS_S_OK:
                valid_cart = true;
                $('#checkout_messages').html('');
                checkout_data.update_product_list(data);
                checkout_data.update_cart(data);
                checkout_data.apply_garretts_rules();
                break;
            case CHECKOUT.STATUS_E_ERROR:
                display_message('error', 'Error on update product amount, please try again');
                valid_cart = false;
                break;
            default:
                display_message('error', 'Error on update product amount, please try again');
                valid_cart = false;
                break;
        }
    });
});

/** Decrease product amount **/
$('.checkoutTable').on('click', '.decrease_product_amount', function(){
    var line_id = $(this).parent().parent().data('line_id');

    if(parseInt($('.checkoutTable .product_line[data-line_id="'+ line_id +'"] .quantity').val()) <= 1){
        $('.checkoutTable .product_line[data-line_id="'+ line_id +'"] .delete_product').trigger('click');
    }
    else{
        CHECKOUT.modifyCart(line_id, CHECKOUT.MODIFY_CART_REMOVE, 1, function(status,data){
            switch(status){
                case CHECKOUT.STATUS_S_OK:
                    valid_cart = true;
                    $('#checkout_messages').html('');
                    checkout_data.update_product_list(data);
                    checkout_data.update_cart(data);
                    checkout_data.apply_garretts_rules();

                    //Custom rule, if the price is
                    if((parseFloat(data.final_price) < MINIMUM_SHIPPING_AMOUNT && (!($('.select_location').css('display') == 'none' ) ) ) ){
                        $('#pick_from_store').click();
                    }
                    break;

                case CHECKOUT.STATUS_E_ERROR:
                    display_message('error', 'Error on update product amount, please try again');
                    valid_cart = false;
                    break;
                default:
                    display_message('error', 'Error on update product amount, please try again');
                    valid_cart = false;
                    break;
            }
        });
    }
});

/** Validate coupon code **/
function validate_coupon(){
    coupon_code = document.getElementById("coupon_code").value;
    CHECKOUT.setCouponCode(coupon_code, function(status, data){
        switch(status){
            case CHECKOUT.STATUS_S_OK:
                $('#checkout_messages').html('');
                display_message('success', 'Coupon code accepted');
                checkout_data.update_product_list(data);
                checkout_data.update_cart(data);
                checkout_data.apply_garretts_rules();
                break;
            case CHECKOUT.STATUS_E_WRONG_COUPON_CODE:
                display_message('error', 'Error The coupon code is invalid');
                break;
            case CHECKOUT.STATUS_E_ERROR:
                display_message('error', 'Error validating coupon, please try again');
                break;
            default:
                display_message('error', 'Error validating coupon, please try again');
                break;
        }
    });
}

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

/** Update price on edit product scree **/
function updateProductOptionPrice(obj,product_id)
{
    //Initialize price variables
    var current_price = parseFloat($('#final_price').data('product_price'));
    var options_price = 0;
    var out_of_stock = false;
    var disable_add_to_cart = false;

    //for each option add the price to "options_price"
    $('.prod_option_item').each(function(index)
    {
        var internal_stock  = false;
        selected_option     = $(this).find(':selected').data('option_price');
        var option_id       = $(this).children('select').val();
        $.post('/frontend/products/check_stock_levels',
            {
                product_id:product_id,
                option_id:option_id
            },
            function(result)
            {
            result = $.parseJSON(result);

            if(result.is_stock_item == true && result.quantity == "0")
            {
                out_of_stock = true;
                hide_buy_button(true);
            }
            if(result.is_stock_item == true && result.quantity > 0)
            {
                var selected_quantity = parseInt($("#qty").val());
                console.log(selected_quantity <= result.quantity);
                console.log("selected quantity:"+selected_quantity);
                console.log("quantity:"+result.quantity);
                if(selected_quantity > result.quantity)
                {
                    out_of_stock = true;
                    hide_buy_button(true);
                    $(obj).parents('div').next('.notice').html('Sorry, we only have <b>'+result.quantity+'</b> of this option in stock.');
                    //$("#qty").change();
                    $(obj).parents('div').next('.notice').addClass('notice_bad');
                    $(obj).parents('div').next('.notice').removeClass('notice_good');
                    $(obj).parents('div').next('.notice').show();
                }
                if(selected_quantity <= result.quantity)
                {
                    $(obj).parents('div').next('.notice').html('<b>'+selected_quantity+'</b> in stock, '+(result.quantity - selected_quantity)+' more available.');
                    $(obj).parents('div').next('.notice').addClass('notice_good');
                    $(obj).parents('div').next('.notice').removeClass('notice_bad');
                    $(obj).parents('div').next('.notice').show();
                }
            }
            else if(result.is_stock_item == true && result.quantity == 0)
            {
                hide_buy_button(true);
                //$(obj).parents('div').next('.notice').stop(true,true).fadeOut();
                $(obj).parents('div').next('.notice').html('Sorry, this product is out of stock.');
                //$("#qty").change();
                $(obj).parents('div').next('.notice').addClass('notice_bad');
                $(obj).parents('div').next('.notice').removeClass('notice_good');
                $(obj).parents('div').next('.notice').show();
            }
        });

        if(typeof(selected_option) == "number")
        {
            options_price += parseFloat(selected_option);
        }
        if(disable_add_to_cart)
        {

        }
    });

    //Calc the total price
    var total = current_price + options_price;
    total = total.toFixed(2);
    $('#final_price').html('&euro;' + total);
    if(out_of_stock == false)
    {
        hide_buy_button(false);
    }
}

function hide_buy_button(is)
{
    if(is)
    {
        $("#add_to_cart_button").hide();
        $("#purchase_button").hide();
        //$(".out_of_stock").show();
    }
    else
    {
        $("#add_to_cart_button").show();
        $("#purchase_button").show();
        //$(".out_of_stock").hide();
    }
}

/**************************************
 **                                  **
 **         Rewards Club             **
 **                                  **
 **************************************/

checkout_data.convert_points_to_cash = function(){

    $.post('/frontend/paybackloyalty/convert_points_to_cash', function(data, status){
        if(status != 'success'){
            display_message('error', 'An error has occurred please try again later');
        }
        else{
            if(data.err_msg != "OK"){
                display_message('error', data.err_msg);
            }
            else{
                display_message('info', data.cashed_points + ' points have been turned into: €' + data.cashed_total + ' For your Online Purchase.');

                CHECKOUT.getCartSummary(function(status, cart){
                    if(status != CHECKOUT.STATUS_S_OK){
                        display_message('error', 'An error has happened, please try again or contact with us if the error still happening.');
                    }
                    else{
                        checkout_data.update_cart_pl_discount(cart);
                        checkout_data.apply_garretts_rules();

                    }
                });
                //data.cashed_total;
            }
        }
    }, 'json');
}

checkout_data.revert_reward_club_points = function(){
    $.post('/frontend/paybackloyalty/revert_converted_points', function(data, status){
        if(status != 'success'){
            display_message('error', 'An error has occurred please try again later');
        }
        else{
            if(data.err_msg != "OK"){
                display_message('error', data.err_msg);
            }
            else{
                display_message('info', '€' + data.returned_total + ' have been returned back to: '+ data.returned_points +' Rewards Club - Points.');

                CHECKOUT.getCartSummary(function(status, cart){
                    if(status != CHECKOUT.STATUS_S_OK){
                        display_message('error', 'An error has happened, please try again or contact with us if the error still happening.');
                    }
                    else{
                        checkout_data.update_cart_pl_discount(cart);
                        checkout_data.apply_garretts_rules();
                    }
                });
            }
        }
    }, 'json');
}

checkout_data.update_account_card_in_use = function(element){
    var data = { card_num_to_use: element.value };

    if(element.value == '0'){
        display_message('error', 'Please select a card');
    }
    else{
        $('#checkout_messages').html('');

        //if there is a discount, revert
        if($('.pb_discount_price').size() > 0){
            checkout_data.revert_reward_club_points();
        }

        $.post('/frontend/paybackloyalty/update_account_card_in_use', data, function(card, status){
            //Remove the reverted points message
            $('#checkout_messages').html('');

            //Update Rewards Club points displayed
            number = parseFloat(card.card_loyalty_points * 100);
            number = parseInt(number.toFixed(1));
            $('.current_cart_points').html(number);
            $('.current_points_in_cash').html(card.card_loyalty_points);

            CHECKOUT.getCartSummary(function(status, cart){
                if(status != CHECKOUT.STATUS_S_OK){
                    display_message('error', 'An error has happened, please try again or contact with us if the error still happening.');
                }
                else{
                    checkout_data.update_cart_pl_discount(cart);
                    checkout_data.apply_garretts_rules();
                    checkout_data.reload_minicart(CHECKOUT.STATUS_S_OK);
                }
            });

        }, 'json');
    }
}

/**************************************
 **                                  **
 ** CUSTOM FUNCTION FOR THIS PROJECT **
 **                                  **
 **************************************/

checkout_data.custom = function(){

    //Custom garret data
    checkout_data.preferred_deliverydate = document.getElementById("deliverydate").value;
    checkout_data.pick_from_store = document.getElementById("pick_from_store").checked;
    checkout_data.store = $("#postalZone option:selected").html();
}


$(document).ready(function(){
   $('.delivery_type').on('click', function(){
      if(this.value == 'store'){
        $('.select_store option:first').attr('selected', 'selected')
        $('.select_store').show();
        $('.select_location').hide();
        $('.delivery_date').hide();
      }
       else{
          $('.select_location option:first').attr('selected', 'selected')
          $('.select_store').hide();
          $('.select_location').show();
          $('.delivery_date').show();
      }
   });

    $(".tr_destination").before('<tr><td style="font-size:13px;color:#8b6d49;padding:5px 9px;"><i>Note: We offer Free Delivery on all Orders Over €100.</i></td></tr>');
});


$(document).on('change',"#qty",function(){
    $(".prod_option").each(function(){
        $(this).change();
    });
});

var MINIMUM_PAYMENT_AMOUNT  = 10;
var MINIMUM_SHIPPING_AMOUNT = 50;
var FREE_SHIPPING_AMOUNT    = 100;

checkout_data.apply_garretts_rules = function(){


    //Get total price
    totalprice = checkout_data.getTotalPrice();

    if( isNaN(totalprice) || totalprice < MINIMUM_PAYMENT_AMOUNT ){
        //you can't pay
        checkout_data._hidePaymentButton();
    }
    else if( totalprice >= MINIMUM_PAYMENT_AMOUNT && totalprice < MINIMUM_SHIPPING_AMOUNT ){
        //you can pay (pick up at shop)
        checkout_data._pickAtShop();
    }
    else if( totalprice >= MINIMUM_SHIPPING_AMOUNT && totalprice < FREE_SHIPPING_AMOUNT ){
        //delivery at home
        checkout_data._deliveryAtHome();
    }
    else if( totalprice >= FREE_SHIPPING_AMOUNT ){
        //Free shipping
        checkout_data._freeShipping();
    }
}

checkout_data.getTotalPrice = function(){
    var postage = $('.postage').text();
    postage     = postage.replace('€','');
    postage     = (postage == '') ? 0 : postage;
    postage     = parseFloat(postage);

    var totalprice = $('.totalprice').text();
    totalprice     = totalprice.replace('€','');
    totalprice     = (totalprice == '') ? 0 : totalprice;
    totalprice     = parseFloat(totalprice);
    totalprice     = totalprice - postage;
    if(totalprice < 50)
    {
        $('#warningmessage_postage').html("<span style='color:#fa991e'><br/>To avail of our delivery service, there is a minimum <br/>purchase charge of €50.</span>");
    }
    return totalprice;
}

checkout_data._hidePaymentButton = function(){
    $("#postalZone").attr("disabled", "disabled");
    $("#postalZoneDelivery").attr("disabled", "disabled");
    $("#deliverydate").attr("disabled", "disabled");
}

checkout_data._pickAtShop = function(){
    $("#postalZone").removeAttr("disabled");
    $("#postalZoneDelivery").attr("disabled", "disabled");
    $("#deliverydate").attr("disabled", "disabled");
    //Force one for prevent error
    //changeZone(($('.select_store option:last').val()));
}

checkout_data._deliveryAtHome = function(){
    $("#postalZone").removeAttr("disabled");
    $("#postalZoneDelivery").removeAttr('disabled');
    $("#deliverydate").removeAttr('disabled');
}

checkout_data._freeShipping = function(){
    $("#postalZone").removeAttr("disabled");
    $("#postalZoneDelivery").removeAttr("disabled");
    $("#deliverydate").removeAttr("disabled");
}



$(document).ready(function(){
    checkout_data.apply_garretts_rules();
});