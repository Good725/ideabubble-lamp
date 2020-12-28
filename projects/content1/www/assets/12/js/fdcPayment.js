/**
 * Created by Provident CRM.
 * User: itabarino
 * Date: 29/01/15
 * Time: 09:42
 */

// Javascript - FDC Payment
var fdcPayment = new function ()
{
    /**
     * onLoad configurations
     */
    function config()
    {
        // Load Invoice
        loadInvoice();

        // Submit Create Payment
        $("#submit").click(function ()
        {
			if ($('#sugarcrm_payment_form').validationEngine('validate'))
			{
				savePayment();
			}
        });
    }

    /**
     * Load Invoice
     */
    function loadInvoice()
    {
        // Get Data
        var data = {
            request_type: "getInvoice",
            invoice_id: $('#invoice_id').val(),
            iid: $('#iid').val()
        };

        // Call Rest API
        CallAPI(data, function (data)
        {
            if (data['error']) {
                var divRet =  $('#return');
                divRet.addClass('alert-danger');
                divRet.html('<p>'+data['message']+'</p>');

                $('#form, #card').remove();
                return false;
            }

            if (data['paid'].toUpperCase() == 'YES') {
                var divRet =  $('#return');
                divRet.addClass('alert-success');
                divRet.html('<p>Invoice number "<span class="highlight">'+data['quote_num']+'</span>" already <span class="highlight">Paid</span>.</p>');

                $('#form, #card').remove();
                return false;
            }

            $('#invoice_id').val(data['invoice_id']);
            $('#account_id').val(data['account_id']);
            $('#invoice_name').html('Invoice: '+data['name'] + '<br/>Amount: <span class="checkout-price" id="amount">&euro;'
                + Number(data['total']).toFixed(2) + '</span>');
            $('#total').val(data['total']);
            $('#form, #card').show();

        });
    }

    /**
     * Create Payment
     */
    function savePayment()
    {
        // Get Data
        var data = 'request_type=createPayment';
        data += '&card_type=' + $('#card_type').val();
        data += '&name=' + $('#name').val();
        data += '&number=' + $('#number').val();
        data += '&cvv=' + $('#cvv').val();
        data += '&expiry=' + $('#expiry').val();
        data += '&amount=' + $('#amount').text().replace(/[^\d.]/g, '');
        data += '&account_id=' + $('#account_id').val();
        data += '&invoice_id=' + $('#invoice_id').val();

        console.log("savePayment");
        // Call Rest API
        CallAPI(data, function (data)
        {
            var divRet = $('#return');

            $('#form').hide();
            $('#card').hide();

            if (data['payment_status'] == 'PAID')
            {
                divRet.addClass('alert-success');
                divRet.html('<p>The Payment was <span class="highlight">Accepted</span></p>');

                if (data['receipt_number'] != null && data['receipt_number'] != '')
                {
                    divRet.html(divRet.html() + '<p>Your Receipt Number is <span class="highlight">'
                        + data['receipt_number'] + '</span></p>');
                }
            }
            else if (data['payment_status'] == 'DECLINED')
            {
                divRet.addClass('alert-danger');
                divRet.html('<p>The Payment was <span class="highlight">Declined</span></p>');
            }
        });
    }

    /**
     * Get data from rest call results
     */
    function CallAPI(data, _callBackFunction)
    {
        // Ajax Call
        $.ajax({
            type: "POST",
            url: "/frontend/sugarcrm/parserestcall",
            data: data,
            dataType: "json"
        }).done(function(data) {
                // Call Back Function
                _callBackFunction(data);
            });
    }

    /**
     * Public functions
     */
    this.Init = function ()
    {
        config();
    }
}();

// onLoad
$(function ()
{
    fdcPayment.Init();
});
