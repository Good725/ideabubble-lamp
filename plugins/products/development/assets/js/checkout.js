//
// CHECKOUT
//

/*
    - All the functions require a callback function to be defined. This function will receive two
      arguments: a status code and a response object.
    - If the operation succeeded, the status code will be STATUS_S_OK and a response object will be returned.
    - If not, an error code will be issued.
*/

var CHECKOUT = CHECKOUT || {};

// STATUS CODES
CHECKOUT.STATUS_S_OK                =  0;
CHECKOUT.STATUS_E_ERROR             = -1;
CHECKOUT.STATUS_E_MISSING_OPTIONS   = -2;
CHECKOUT.STATUS_E_WRONG_COUPON_CODE = -3;

// OPERATION CODES
CHECKOUT.MODIFY_CART_ADD            =  1;
CHECKOUT.MODIFY_CART_REMOVE         =  2;
CHECKOUT.MODIFY_CART_SET            =  3;

/**
 *
 * @param {string} action
 * @param {object} data
 * @param {function} f
 * @private
 */
CHECKOUT._makePOSTRequest = function(action, data, f) {
    $.post(action, { data : JSON.stringify(data) })
        .always(function(r) {
			if (r == '') r = 'null';
            var status, data, response = jQuery.parseJSON(r);

            try {
                status   = response.status;
                data     = response.data;
            } catch (e) {
                status   = CHECKOUT.STATUS_E_ERROR;
                data     = null;
            }

            f(status, data);
        });
};

/**
 *
 * @param {string} action
 * @param {object} data
 * @param {function} f
 * @private
 */
CHECKOUT._makeGETRequest = function(action, data, f) {
    $.get(action, data)
        .always(function(r) {
			if (r == '') r = 'null';
            var status, data, response = jQuery.parseJSON(r);

            try {
                status   = response.status;
                data     = response.data;
            } catch (e) {
                status   = CHECKOUT.STATUS_E_ERROR;
                data     = null;
            }

            f(status, data);
        });
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR, STATUS_E_MISSING_OPTIONS.
 * @param {int} product_id
 * @param {int} quantity
 * @param {object} options
 * @param {function} f
 */
CHECKOUT.addToCart = function(product_id, quantity, options, f)
{
    custom_timestamp = (typeof custom_timestamp == 'undefined') ? null : custom_timestamp;
    var sign_thumbnail   = (typeof sign_thumbnail   == 'undefined') ? null : sign_thumbnail;
	if (document.getElementById('product_thumbnail_canvas'))
	{
		sign_thumbnail = document.getElementById('product_thumbnail_canvas').toDataURL();
	}
    var data = { product_id : parseInt(product_id), quantity : parseInt(quantity), options : options, timestamp: custom_timestamp, sign_thumbnail: sign_thumbnail};

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_add_to_cart', data, f);
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR.
 * @param {int} line_id
 * @param {function} f
 */
CHECKOUT.deleteFromCart = function(line_id, f) {
    var data = { line_id : parseInt(line_id) };

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_delete_from_cart', data, f);
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR.
 * @param {function} f
 */

CHECKOUT.getCartSummary = function(f) {
    CHECKOUT._makePOSTRequest('/frontend/products/checkout_get_cart_summary', null, f);
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR.
 * @param {int} line_id
 * @param {int} operation
 * @param {int} quantity
 * @param {function} f
 */
CHECKOUT.modifyCart = function(line_id, operation, quantity, f) {
    var data = { line_id : parseInt(line_id) , operation : parseInt(operation) , quantity: parseInt(quantity) };
	CHECKOUT._makePOSTRequest('/frontend/products/checkout_modify_cart', data, f);
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR.
 * @param {int} zone_id
 * @param {function} f
 */
CHECKOUT.setPostalZone = function(zone_id, f) {
    var data = { zone_id : parseInt(zone_id) };

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_postal_zone', data, f);
};

CHECKOUT.setCountry = function(country, f) {
	CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_country', { country : country }, f);
};

CHECKOUT.setDeliveryMethod = function(delivery_method, f)
{
    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_delivery_method', { delivery_method : delivery_method }, f);
};

CHECKOUT.setStoreID = function(store_id, f)
{
    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_store_id', { store_id : store_id }, f);
};

CHECKOUT.setPONumber = function(po_number, f)
{
    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_po_number', { po_number : po_number }, f);
};

CHECKOUT.setDeliveryTime = function(delivery_time, f)
{
    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_delivery_time', { delivery_time : delivery_time }, f);
};

CHECKOUT.setGiftOption = function (gift_option, f) {
    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_gift_option', { gift_option : gift_option }, f);
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR.
 * @param {int} location_id
 * @param {function} f
 */
CHECKOUT.setLocation = function(location_id, f) {
    var data = { location_id : parseInt(location_id) };

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_location', data, f);
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_WRONG_COUPON_CODE, STATUS_E_ERROR.
 * @param {string} coupon_code
 * @param {function} f
 */
CHECKOUT.setCouponCode = function(coupon_code, f) {
    var data = { coupon_code : coupon_code };

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_set_coupon_code', data, f);
};

/**
 * Possible status codes: STATUS_E_ERROR.
 * @param {string} return_url
 * @param {string} cancel_return_url
 * @param {function} f_on_error
 */
CHECKOUT.checkoutWithPayPal = function(return_url, cancel_return_url, f_on_error)
{
    if (document.getElementById('creditCardForm'))
    {
        var valid = $('#creditCardForm').validationEngine('validate');
        if ( ! valid)
        {
            setTimeout('removeBubbles()', 5000);
            return;
        }
    }

    var data = { return_url : return_url , cancel_return_url : cancel_return_url, form_data: $('#creditCardForm').serialize()};

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_get_paypal_form', data, function(status, data)
    {
        if (status == CHECKOUT.STATUS_S_OK)
        {
            var whereDisplay;
            if (typeof payPalRedirect == "undefined")
            {
                whereDisplay = 'body';
            }
            else if (payPalRedirect == 0)
            {
                var newWindow = window.open('', 'PayPal', "scrollbars=1,height=500,width=980");
                var newWindowBody = newWindow.document.body;

                newWindowBody.style.background = 'url("' + urlBase + 'assets/default/images/loading.gif") no-repeat center 200px';
                whereDisplay = newWindowBody;
            }
            else
            {
                whereDisplay = 'body';
            }

            var form = $('<form id="paypal_form" method="post" action="https://www.'+(data.test_mode ? 'sandbox.' : '')+'paypal.com/cgi-bin/webscr">').appendTo(whereDisplay);

            for (var property in data)
            {
                $(form)
                    .append($('<input>')
                        .prop('type' , 'hidden')
                        .prop('name' , property)
                        .prop('value', data[property]));
            }

            $(form).submit();
        }
        else
        {
            f_on_error(status, data);
        }
    });
};

/**
 * Possible status codes: STATUS_S_OK, STATUS_E_ERROR.
 * @param {int} product_id
 * @param {object} options
 * @param {function} f
 */
CHECKOUT.getLineId = function(product_id, options, f) {
    var data = { product_id : product_id , options : options};

    CHECKOUT._makePOSTRequest('/frontend/products/checkout_get_line_id', data, f);
};

/**
 *
 */
function shippingAddress() {
    if ($('#addressCheckbox').is(':checked')) {
        $('#shippingAddressDiv').hide();

        // Set the hidden field: Shipping address fields not compulsory
        $('#shipping_name').attr('class', '');

        $('#shipping_address_1').removeAttr('class');
        $('#shipping_address_2').removeAttr('class');
        $('#shipping_address_3').removeAttr('class');
        $('#shipping_address_4').removeAttr('class');
        $('#shipping_phone').removeAttr('class');
    } else {
        $('#shippingAddressDiv').show();

        // Setup the fields of the #shippingAddress COMPULSORY
        $('#shipping_name').attr('class', 'validate[required] text-input');

        $('#shipping_address_1').attr('class', 'validate[required] text-input');
        $('#shipping_address_2').attr('class', 'validate[required] text-input');
        $('#shipping_address_3').attr('class', 'validate[required] text-input');
        $('#shipping_address_4').attr('class', 'validate[required] text-input');
        $('#shipping_phone').attr('class', 'validate[required] text-input');
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
