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
        product.quantity = parseInt($(products_list[i]).find('.quantity, .checkout-table-qty').val());
        checkout_data.products[$(products_list[i]).data('line_id')] = product;
    }

    checkout_data.size_guide_read = (document.getElementById("checkout-size_guide_read") && document.getElementById("checkout-size_guide_read").checked) ? 'Yes' : 'No';
    checkout_data.termsCheckbox   = (document.getElementById("termsCheckbox") && document.getElementById("termsCheckbox").checked) ? 'Yes' : 'No';
};

checkout_data.ini_cardpayment = function(){
    //Load default data
    checkout_data.ini();

    //Name and address
    checkout_data.ccName    = document.getElementById("ccName").value;
    checkout_data.address_1 = document.getElementById("address_1").value;
    checkout_data.address_2 = document.getElementById("address_2").value;
    checkout_data.address_3 = $('#address_3:not(:disabled)')[0] ? document.getElementById("address_3").value : '';
    checkout_data.address_4 = $('#address_4:not(:disabled)')[0] ? document.getElementById("address_4").value : '';
	checkout_data.postcode  = $('#postcode:not(:disabled)' )[0] ? document.getElementById("postcode" ).value : '';
	checkout_data.eircode   = $('#checkout_eircode:not(:disabled)')[0] ? document.getElementById("checkout_eircode").value : '';
    checkout_data.phone     = document.getElementById("phone").value;
    checkout_data.email     = document.getElementById("email").value;
    checkout_data.message_for_the_card = $("#message_for_the_card").val();
	checkout_data.purchase_order_reference = document.getElementById('checkout_purchase_order_reference') ? document.getElementById("checkout_purchase_order_reference").value : '';
    if ($('#template_name').length)
    {
        checkout_data.template_name = document.getElementById("template_name").value;
    }

    // Delivery method
    var delivery_method_input = document.getElementById('checkout_delivery_method');
    if (delivery_method_input) checkout_data.delivery_method = delivery_method_input.value;

    // Store ID
    var store_input = document.getElementById('checkout_store');
    if (store_input) checkout_data.store_id = store_input.value;

    // PO Number
    var po_number = document.getElementById('checkout_po_number');
    if (po_number) checkout_data.po_number = po_number.value;

    // Delivery time and date
    var delivery_time = document.getElementById('checkout_delivery_time');
    if (delivery_time) checkout_data.delivery_time = delivery_time.value;

    //Shipping Address
    if(document.getElementById("addressCheckbox").checked)
    {
        checkout_data.shipping_same_address = "true";
        checkout_data.shipping_name         = checkout_data.ccName;
        checkout_data.shipping_address_1    = checkout_data.address_1;
        checkout_data.shipping_address_2    = checkout_data.address_2;
        checkout_data.shipping_address_3    = checkout_data.address_3;
        checkout_data.shipping_address_4    = checkout_data.address_4;
        checkout_data.shipping_postcode     = checkout_data.postcode;
		checkout_data.shipping_phone        = checkout_data.phone;

    }
    else
    {
        checkout_data.shipping_same_address = "false";
		checkout_data.shipping_name         = (document.getElementById("shipping_name").value+' '+$('#shipping_surname').val()).trim();
        checkout_data.shipping_address_1    = document.getElementById("shipping_address_1").value;
        checkout_data.shipping_address_2    = document.getElementById("shipping_address_2").value;
        checkout_data.shipping_address_3    = $('#shipping_address_3:not(:disabled)')[0] ? document.getElementById("shipping_address_3").value : '';
        checkout_data.shipping_address_4    = $('#shipping_address_4:not(:disabled)')[0] ? document.getElementById("shipping_address_4").value : '';
        checkout_data.shipping_postcode     = $('#shipping_postcode:not(:disabled)' )[0] ? document.getElementById("shipping_postcode" ).value : '';
		checkout_data.shipping_phone        = $('#shipping_phone:not(:disabled)'    )[0] ? document.getElementById("shipping_phone"    ).value : '';
    }

    // Credit Card Payment Details
    if (document.getElementById("ccNum"))
    {
        checkout_data.ccType  =  document.getElementById("ccType").options[document.getElementById("ccType").selectedIndex].value;
        checkout_data.ccNum   =  document.getElementById("ccNum").value.replace(/[\s\-]/g,'');
        checkout_data.ccv     =  document.getElementById("ccv").value;
        checkout_data.ccExpMM =  document.getElementById("ccExpMM").options[document.getElementById("ccExpMM").selectedIndex].value;
        checkout_data.ccExpYY =  document.getElementById("ccExpYY").options[document.getElementById("ccExpYY").selectedIndex].value;
        checkout_data.MD      =  document.getElementById("MD") ? document.getElementById("MD").value : null;
        checkout_data.MDX     =  document.getElementById("MDX") ? document.getElementById("MDX").value : null;
        checkout_data.PaRes   =  document.getElementById("PaRes") ? document.getElementById("PaRes").value : null;
    }

    //Comments
    checkout_data.comments       =  document.querySelector("#comments, #checkout_comments") ? document.querySelector("#comments, #checkout_comments").value : null;

	checkout_data.is_gift        = document.getElementById('checkout-is_gift'       ) ? document.getElementById('checkout-is_gift'       ).checked : null;
	checkout_data.gift_card_text = document.getElementById('checkout-gift_card_text') ? document.getElementById('checkout-gift_card_text').value   : null;

    //sign up the newsletter
    checkout_data.signupnewsletter   =  document.getElementById("signupCheckbox").checked;
    //Thanks page
    if(document.getElementById("thanks_page")){
        checkout_data.thanks_page   =  document.getElementById("thanks_page").value;
    }

    checkout_data["g-recaptcha-response"] = $('#g-recaptcha-response').val();
}

/**
 *
 * Paypal
 */
checkout_data.paypal_error = function (status){
    if(status != CHECKOUT.STATUS_S_OK){
        display_message('error', 'Error: An error has occurred with PayPal payment, please try again');
    }
}

/**
 * update product list prices
 * @param data
 */
checkout_data.update_product_list = function(data){
    //For each element, update the values
    $('#checkoutTable .product_line').each(function(line){
            if (typeof data.lines[line] != 'undefined')
            {
                $(this).find('.quantity').val(data.lines[line].quantity);
                $(this).find('.price_total_line').html('&euro;' + parseFloat(0 + data.lines[line].price).toFixed(2));
            }
        }
    );
    //Minicart
    $('.mycart_summary_details .product_line').each(function(line){
            if (typeof data.lines[line] != 'undefined')
            {
                $(this).find('.quantity').html(data.lines[line].quantity);
                $(this).find('.price_total_line').html('&euro;' + parseFloat(0 + data.lines[line].price).toFixed(2));
            }

        }
    );

    // VAT
    if ( ! data['vat']) data['vat'] = 0;
    var vat = '€' + parseFloat(0 + data['vat']).toFixed(2);
    $('#checkoutTable').find('.vat').html(vat).val(vat);
    // Shipping
    if(!data['shipping_price']) data['shipping_price'] = '0';
    $('#checkoutTable .postage').html('&euro;' + parseFloat(0 + data['shipping_price']).toFixed(2));

	/*$('#div_cart_based_price_discount').html('');
	$('#div_cart_based_free_shipping').html('');
	$('#div_cart_based_qty_discount').html('');

    if(data['checkout_cart_based_discount_title'] != ''){
		$('#div_cart_based_price_discount').html(data['checkout_cart_based_discount_title']);
	}

	if(data['checkout_cart_based_shipping_free_title'] != ''){
		$('#div_cart_based_free_shipping').html(data['checkout_cart_based_shipping_free_title']);
	}

	if(data['checkout_cart_based_qty_discount_title'] != ''){
		$('#div_cart_based_qty_discount').html(data['checkout_cart_based_qty_discount_title']);
	}*/

    /* add cart based free shipping*/
    if(data['cart_based_free_shipping'] != 'yes' || data['cart_based_free_shipping'] == ''){
		$('#id_cart_based_free_shipping_title').css("visibility", "hidden");
	}else{
		$('#id_cart_based_free_shipping_title').css("visibility", "visible");
		$('#cart_based_free_shipping_title').html(data['cart_based_free_shipping_title']);
	}
    
    /* add cart based price discount*/
    if(data['cart_based_price_discount_title'] == '' || !data['cart_based_price_discount_title']){
		$('#id_cart_based_price_discount_title').css("visibility", "hidden");
	}else{
		$('#id_cart_based_price_discount_title').css("visibility", "visible");
		$('#cart_based_price_discount_title').html(data['cart_based_price_discount_title']);
		$('#cart_based_price_discount').html(data['cart_based_price_discount_percentage'] + '= &euro;' + parseFloat(0 + data['cart_based_price_discount']).toFixed(2));
	}

	/* add cart based qty discount*/
    if(data['qty_discount_title'] == '' || !data['qty_discount_title']){
		$('#id_cart_based_qty').css("visibility", "hidden");
	}else{
		$('#id_cart_based_qty').css("visibility", "visible");
		$('#cart_based_qty_title').html(data['qty_discount_title']);
		$('#cart_based_qty_discount').html(data['qty_discount_percentage'] + '= &euro;' + parseFloat(0 + data['cart_based_qty_discount']).toFixed(2));
	}

    if(!data['shipping_price']) data['shipping_price'] = '0';
    $('#cart_based_free_shipping').html('&euro;' + parseFloat(0 + data['shipping_price']).toFixed(2));
    
    // Total
    if(!data['final_price']) data['final_price'] = data['cart_price'];
    $('#checkoutTable .totalprice').html('&euro;' + parseFloat(0 + data['final_price']).toFixed(2));
    $('.checkout-settings .totalprice').val('€' + parseFloat(0 + data['final_price']).toFixed(2));
    // Gift option
    $('#checkoutTable .gift_price').html('&euro;' + parseFloat(0 + data['gift_price']).toFixed(2));
	var $tr_giftprice = $('.tr_giftprice');
    if (data['gift_option'])
	{
		$tr_giftprice.css('position','').css('visibility','');
		$('#tr_giftprice_buffer').css('height', '0');
	}
    else
	{
		$tr_giftprice.css('position','absolute').css('visibility','hidden');
		$('#tr_giftprice_buffer').css('height', $tr_giftprice.outerHeight());
	}
    // Subtotal
    if(!data['subtotal']) data['subtotal'] = data['subtotal'];
    $('#checkoutTable .subtotal').html('&euro;' + parseFloat(0 + data['subtotal']).toFixed(2));
    $('.checkout-settings .subtotal').val('€' + parseFloat(0 + data['subtotal']).toFixed(2));
    // Subtotal2
    if(!data['subtotal2']) data['subtotal2'] = data['subtotal2'];
    $('#checkoutTable .subtotal2').html('&euro;' + parseFloat(0 + data['subtotal2']).toFixed(2));
    $('.checkout-settings .subtotal2').val('€' + parseFloat(0 + data['subtotal2']).toFixed(2));
    // Discounts
    var discount_field = $('#checkoutTable .discounts');
    if(!data['discounts']) data['discounts'] = data['discounts'];
    discount_field.html('&minus;&nbsp;&euro;' + parseFloat(0 + data['discounts']).toFixed(2));
    (data['discounts'] == 0) ? discount_field.parents('tr').hide() : discount_field.parents('tr').show();
};

checkout_data.update_cart = function(data)
{
    $('.mycart_items_amount').html(data.number_of_items);
	$('#checkout_cart, .minicart-hidden-when-empty').attr('data-product_count', data.number_of_items);
    if ( ! data.final_price) data.final_price = data.cart_price;
    $('#mycart_total_price').html(parseFloat(data.final_price).toFixed(2));
    $('.cart_hidden').show();
};

checkout_data.display_empty_cart = function()
{
	$('.mycart_items_amount').html(0);
    $('#mycart_total_price').html(0);
	$('#checkout_cart, .minicart-hidden-when-empty').attr('data-product_count', 0);
    $('#checkoutTable .totalprice, #checkoutTable .subtotal, #checkoutTable .subtotal2').html(0);
    $('.cart_hidden').hide();
    $('.mycart_summary .mycart_summary_details').hide();
};

checkout_data.add_to_cart_callback = function(status, data){
    switch(status){
        case CHECKOUT.STATUS_S_OK:
            valid_cart = true;
            $('#checkout_messages').html('');
            checkout_data.update_product_list(data);
            checkout_data.update_cart(data);
            break;
        case CHECKOUT.STATUS_E_ERROR:
            display_message('error', 'Error adding the product, please try again.');
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

function show3DAuthFrame(acsurl, md, pareq, termurl)
{
	$("#d3secureiframe").remove();
	$("#d3secureform").remove();

	var fi = document.createElement("iframe");
	fi.id="d3secureiframe";
	fi.name="d3secureiframe";
	document.body.appendChild(fi);

	var form = document.createElement("form");
	form.target = "d3secureiframe";
	form.method = "post";
	form.action = acsurl;
	form.id = "d3secureform";
	form.style.display = "none";
	var input = null;
	
	input = document.createElement("input");
	input.name = "MD";
	input.value = md;
	form.appendChild(input);
	
	input = document.createElement("input");
	input.name = "PaReq";
	input.value = pareq;
	form.appendChild(input);
	
	input = document.createElement("input");
	input.name = "TermUrl";
	input.value = termurl;
	form.appendChild(input);
	
	document.body.appendChild(form);
	
	window.top.d3securedialog = $(fi).dialog({autoOpen: true,
											  height: 500,
											  width: 450,
											  modal: true,
											  closeOnEscape: false,
											  title: "3D Secure Authorization"});
	fi.style.width = "90%";
	fi.style.height = "450";
	fi.frameborder = 0;
	fi.marginWidth = 0;
	fi.marginHeight = 0;
	form.submit();
}

function hide3DSecureAuthFrame()
{
	if(window.top.d3securedialog){
		window.top.d3securedialog.dialog('close');
	}
	$("#MD").val("");
	$("#MDX").val("");
	$("#PaRes").val("");
}

var ignore_empty = false;

function submitCheckout(){
    if ($("#message_for_the_card").length > 0) {
        if ($("#message_for_the_card").val() == "") {
            if (!confirm("You have not entered 'Message for the card'. Do you wish to continue?")) {
                $("#message_for_the_card").focus();
                return false;
            }
        }
    }
    //Protect the form. only one instance is possible
    if(form_submited == true || valid_cart == false){ return false; }
    form_submited = true;

    var submit_button = $('.submit_checkout_button').html();
    $('.submit_checkout_button').html('<img style="text-align: center" src="/assets/default/images/ajax-loader.gif" alt="Loading">');


    subbmit_status = $("#creditCardForm").validationEngine('validate');
    if(subbmit_status){

		var $blackout = $('#checkout-blackout');

		if ($blackout.length == 0)
		{
			$blackout = $('<div class="checkout-blackout" id="checkout-blackout" style="">' +
							  '<div class="checkout-blackout-inner">Please wait, while we verify your information. This could take a moment.</div>' +
						  '</div>');
		}
		$blackout.show();
		$('body').append($blackout);

        //get data
        checkout_data.ini_cardpayment();
        checkout_data_s = JSON.stringify(checkout_data);

        if (window.disableScreenDiv) {
            // Stop the blackout from automatically dismissing when the AJAX request is done.
            // It should continue until the user has been redirected.
            window.disableScreenDiv.autoHide = false;
        }

        //Submit payment
        $.ajax({
            url:'/frontend/payments/payment_processor_ib_pay',
            data:{ checkout:checkout_data_s},
            type: 'POST',
            dataType:'json'
        })
            .done(function(data){
				if(data.Status == '3DAUTH'){
					show3DAuthFrame(data.ACSURL, data.MD, data.PAReq, data.TermUrl);
					$blackout.hide();
					return;
				} else if (data.status == 'success') {
                    location.href = data.redirect;
                } else {
                    display_message('error', 'Error: ' +data.message);
                }
				hide3DSecureAuthFrame();
			    $blackout.hide();
                dismiss_blackout();
            })
            .fail(function(data){
                display_message('error', 'Error: Network error, please check your internet connection.');
                $blackout.hide();
                dismiss_blackout();
            });
    }
    else{
        setTimeout('removeBubbles()', 5000);
    }
    form_submited = false;
    $('.submit_checkout_button').html(submit_button);

    function dismiss_blackout()
    {
        if (window.disableScreenDiv) {
            window.disableScreenDiv.autoHide = true;
            window.disableScreenDiv.style.visibility = 'hidden';
        }
    }
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
    });
}
function changeCountry(country){
	CHECKOUT.setCountry(country, function(status,data){
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
	});
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
$('#checkoutTable').on('click', '.delete_product', function(){
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
            // Remove from checkout
            $('#checkoutTable .product_line[data-line_id='+ line_id +']').remove();
            if(data != null){
                checkout_data.update_product_list(data);
                checkout_data.update_cart(data);
            }
            else{
                checkout_data.display_empty_cart();
                document.location = ".";
            }
        }
    });
}

var delete_msg =
'<div id="dialog-confirm" title="Remove Item">'+
    '<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Are you sure you want to remove this item from your shopping cart?</p>'+
'</div>';

// Only allow numbers and certain special keys to be pressed when changing the quantity field
$('#checkoutTable').on('keydown', '.checkout-table-qty', function (ev)
{
	// Allow delete, tab, escape and enter and backspace || Allow home, end, left, right
	if ($.inArray(ev.keyCode, [8, 9, 27, 13, 46]) !== -1 || (ev.keyCode >= 35 && ev.keyCode <= 39))
	{
		return;
	}
	// 48 to 57 = regular numbers, 96 to 105 = numpad numbers
	// If anything but a number is pressed or a number is pressed at the same time as Shift, stop it
	if ((ev.shiftKey || (ev.keyCode < 48 || ev.keyCode > 57)) && (ev.keyCode < 96 || ev.keyCode > 105))
	{
		ev.preventDefault();
	}
});

// When the quantity field is changed
$('#checkoutTable').on('change', '.checkout-table-qty', function()
{
	var $line_html = $(this).parents('.product_line');

	if (this.value <= 0)
	{
		this.value = 1;
		$line_html.find('.delete_product, .checkout-table-remove').click();
	}
	else
	{
		var line_id = $line_html.data('line_id');
		CHECKOUT.modifyCart(line_id, CHECKOUT.MODIFY_CART_SET, this.value, function(status, data)
		{
			switch (status)
			{
				case CHECKOUT.STATUS_S_OK:
					valid_cart = true;
					$('#checkout_messages').html('');
					checkout_data.update_product_list(data);
					checkout_data.update_cart(data);
					break;
				case CHECKOUT.STATUS_E_ERROR:
					display_message('error', 'Error updating the product amount. Please try again.');
					valid_cart = false;
					break;
				default:
					display_message('error', 'Error updating the product amount. Please try again.');
					valid_cart = false;
					break;
			}
		});
	}
});

/** Increase product amount **/
$('#checkoutTable').on('click', '.increase_product_amount', function(){
    var line_id = $(this).parent().parent().data('line_id');
    CHECKOUT.modifyCart(line_id, CHECKOUT.MODIFY_CART_ADD, 1, function(status,data){
        switch(status){
            case CHECKOUT.STATUS_S_OK:
				$.ajax({
					type: "POST",
					url: '/frontend/products/ajax_get_discount_html',
					success: function(html){
						if($(html).filter('.offer-con').html()){
							$('#DeliveryDeals').show();
							$('#checkout_discount_options').html(html);
						}else{
							$('#DeliveryDeals').hide();
							$('#checkout_discount_options').html('');
						}
						
					}
				});
				$.ajax({
					type: "POST",
					url: '/frontend/products/ajax_get_applied_discount_html',
					success: function(html){
						$('#checkout_discount_applied').html(html);
					}
				});
                valid_cart = true;
                $('#checkout_messages').html('');
                checkout_data.update_product_list(data);
                checkout_data.update_cart(data);
                break;
            case CHECKOUT.STATUS_E_ERROR:
                display_message('error', 'Error updating the product amount. Please try again.');
                valid_cart = false;
                break;
            default:
                display_message('error', 'Error updating the product amount. Please try again.');
                valid_cart = false;
                break;
        }
    });
});

/** Decrease product amount **/
$('#checkoutTable').on('click', '.decrease_product_amount', function(){
    var line_id = $(this).parent().parent().data('line_id');

    if(parseInt($('#checkoutTable .product_line[data-line_id="'+ line_id +'"] .quantity').val()) <= 1){
        $('#checkoutTable .product_line[data-line_id="'+ line_id +'"] .delete_product').trigger('click');
    }
    else{
        CHECKOUT.modifyCart(line_id, CHECKOUT.MODIFY_CART_REMOVE, 1, function(status,data){
            switch(status){
                case CHECKOUT.STATUS_S_OK:
                    valid_cart = true;
                    $.ajax({
						type: "POST",
						url: '/frontend/products/ajax_get_discount_html',
						success: function(html){
							if($(html).filter('.offer-con').html()){
							  $('#DeliveryDeals').show();
							  $('#checkout_discount_options').html(html);
						    }else{
								$('#DeliveryDeals').hide();
								$('#checkout_discount_options').html('');
							}	
						    
						}
					});
					$.ajax({
						type: "POST",
						url: '/frontend/products/ajax_get_applied_discount_html',
						success: function(html){
							
							$('#checkout_discount_applied').html(html);
						}
					});
                    $('#checkout_messages').html('');
                    checkout_data.update_product_list(data);
                    checkout_data.update_cart(data);
                    break;
                case CHECKOUT.STATUS_E_ERROR:
                    display_message('error', 'Error updating the product amount. Please try again.');
                    valid_cart = false;
                    break;
                default:
                    display_message('error', 'Error updating the product amount. Please try again.');
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
        case 'method_1': // Credit Card
            $('#CardDetails').show();
            $('#checkoutForm').show();
            $('#submit_checkout_button').show();
            break;
        case 'method_2': // PayPal
            $('#paypalButton').show();
            break;
        case 'method_3': // Stripe
            $('#stripeButton').show();
    }
}

/** Update price on edit product screen **/
function updateProductOptionPrice(){
    //Initialize price variables
    var current_price = parseFloat($('#final_price').data('product_price'));
    var options_price = 0;

    //for each option add the price to "options_price"
    $('.prod_option_item, .prod_option').each(function(){
        selected_option = $(this).find(':selected').data('option_price');
        if(typeof(selected_option) == "number"){
            options_price += parseFloat(selected_option);
        }
    });

    //Calc the total price
    var total = current_price + options_price;
    total = total.toFixed(2);
    $('#final_price').html('&euro;' + total);
}

//Some Stripe JS Crapola.
if(document.getElementById('stripe-button'))
{
    var stripe_button = document.getElementById('stripe-button');
    var handler       = StripeCheckout.configure(
        {
            key   : stripe_button.getAttribute('data-key'),
            token : function(token)
            {
                checkout_data.ini_cardpayment();
                var checkout_data_s  = JSON.stringify(checkout_data);
                var row              = $('[name="selected_subscription"]:checked').parents('tr');
                var promotional_code = document.getElementById('checkout_promotional_code');
                promotional_code     = (promotional_code == null) ? null : promotional_code.value;
                
                $.ajax({
                    url     : '/frontend/payments/payment_processor_ib_pay',
                    data    : {
                        'token'            : token,
                        'price'            : row.data('price'),
                        'checkout'         : checkout_data_s,
                        'promotional_code' : promotional_code,
                         payment_type      : 'stripe'
                    },
                    type     : 'post',
                    dataType : 'json',
                    async    : true
                })
                    .done(function(data)
                    {
                        if (data.status == 'success')
                        {
                            if (typeof(cms_ns) != "undefined") {
                                // Form has been successfully submitted. These are no longer considered unsaved changes
                                // This will stop the unsaved changes warning blocking the redirect.
                                cms_ns.modified = false;
                            }

                            location.href = data.redirect;
                        }
                        else
                        {
                            display_message('error', 'Error: ' +data.message);
                        }
                    })
                    .fail(function(data)
                    {
                        display_message('error', 'Error: Network error, please check your internet connection');
                    });
            },
            closed: function() {
                // Check if it closed because the user is making a payment, rather than them clicking the close icon.
                if (this.key) {
                    // Show the blackout
                    if (window.disableScreenDiv) {
                        window.disableScreenDiv.style.visible = 'visible';
                        window.disableScreenDiv.autoHide = false;
                    }

                }
            }
        });

    stripe_button.addEventListener('click', function(ev)
    {
        ev.preventDefault();

        var submit_status = $("#creditCardForm").validationEngine('validate');
        if (submit_status)
        {
            var row = $('[name="selected_subscription"]:checked').parents('tr');
            handler.open(
                {
                    description : row.data('product_name'),
                    amount      : row.data('final_price') * 100
                });

        }

    });
}

$('#checkout_delivery_method').on('change', function()
{
    var is_collecting = (this.value == 'pay_and_collect' || this.value == 'reserve_and_collect');
    document.getElementById('checkout_store_wrapper').style.display       = is_collecting ? 'block'   : 'none';
    document.getElementById('checkout_postal_zone_wrapper').style.display = is_collecting ? 'none'    : 'block';
    if (document.getElementById('delivery_time_wrapper'))
    {
        document.getElementById('delivery_time_wrapper').style.display    = is_collecting ? 'none'    : 'block';
    }

    CHECKOUT.setDeliveryMethod(this.value, function(status,data)
    {
        switch(status)
        {
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
    });

    if (this.value == 'reserve_and_collect')
    {
        $('#CardDetails, #paymentSelect').hide();
    }
    else
    {
        $('#CardDetails, #paymentSelect').show();
        $('#paymentSelect').find('.selected').click();
    }

    var $checkout_button = $('#submit_checkout_button');
    if ($checkout_button.find('.strong')[0])
    {
        $checkout_button = $checkout_button.find('.strong');
    }

    if (is_collecting)
    {
        $checkout_button.html('Reserve &raquo;');
        $('#shippingAddressDiv .CartDetails, #shipping_heading_wrapper').hide();
    }
    else
    {
        $checkout_button.html('Buy Now &raquo;');
        $('#shippingAddressDiv .CartDetails, #shipping_heading_wrapper').show();
    }
});

$('#checkout_store').on('change', function()
{
    CHECKOUT.setStoreID(this.value, function(status,data)
    {
        switch(status)
        {
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
    });
});

$('#checkout_delivery_time, #checkout_delivery_date').on('change', function()
{
    var time = $('#checkout_delivery_time').val()+' '+$('#checkout_delivery_date').val();
    CHECKOUT.setDeliveryTime(time, function(status, data)
    {
        switch (status)
        {
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
    });
});

$(document).ready(function()
{
    $('#checkout_delivery_method').trigger('change');
    $('#checkout_store').trigger('change');
});


$('#checkout-is_gift').on('change', function()
{
    var gift_option = false;
	if (this.checked)
	{
		$('#checkout-gift_card_text').prop('disabled', false);
		$('.checkout-gift_card_text-wrapper').removeClass('hidden');
        gift_option = true;
	}
	else
	{
		$('.checkout-gift_card_text-wrapper').addClass('hidden');
		$('#checkout-gift_card_text').prop('disabled', true);
        gift_option = false;
	}

    CHECKOUT.setGiftOption(gift_option, function(status, data)
    {
        switch (status)
        {
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
    });
}).trigger('change');