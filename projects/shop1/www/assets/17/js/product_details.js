function validateQty(id, quantity, options) {
    var valid = ($("#ProductDetailsForm").validationEngine('validate'));
    if (valid) {
        var optArray = [];
        var optionsOk = true;
		var toBeValidated = false;
		$('select.prod_option').each(function(){
			optArray.push(this.value);
			if($(this).hasClass('validate[dropdown]')){
				toBeValidated = true;
			}
			if(this.value == 0 && toBeValidated){
				optionsOk = false;
			}
			if (this.value == '' && toBeValidated){
				optionsOk = false;
			}
		});
        if (optionsOk) {
			CHECKOUT.addToCart(parseInt(id),parseInt(quantity),options,function(status,data){
                checkout_data.add_to_cart_callback(status,data);
                // checkout_data.reload_minicart(status);

				var $message;

				if (document.getElementById('add-to-cart-success-message'))
				{
					$message = $('#add-to-cart-success-message');
				}
				else
				{
					$message = $('div.successful');
					$message.html("<h3>Your product has been added to the cart. <a href='/checkout.html'>Checkout</a></h3>");
				}
				$message.fadeIn('fast');
				setTimeout(function()
				{
					$message.fadeOut('slow');
                }, 10000);

            });
        }
		else{
			alert('Please ensure that all mandatory options have been selected.');
		}
    }
}//end of function

function validateQty_and_checkout(id, quantity, options) {
    var valid = ($("#ProductDetailsForm").validationEngine('validate'));
    if (valid) {
        var optArray = new Array();
        var optionsOk = true;
        var toBeValidated = false;
        $('select.prod_option').each(function(){
            optArray.push(this.value);
            if($(this).hasClass('validate[dropdown]')){
                toBeValidated = true;
            }
            if(this.value == 0 && toBeValidated){
                optionsOk = false;
            }
            if (this.value == '' && toBeValidated){
                optionsOk = false;
            }
        });
        if (optionsOk) {
            CHECKOUT.addToCart(parseInt(id),parseInt(quantity),options,function(status,data){
                //checkout_data.add_to_cart_callback(status,data);
                window.location.href = "/checkout.html";
            });
        }
        else{
            alert('Please ensure that all mandatory options have been selected.');
        }
    }
}//end of function

$(function() {
    var options = {
        zoomWidth: 259,
        zoomHeight: 256,
        title: false,
        lens: false,
        xOffset: 10,
        yOffset: 0,
        position: "right"};
    //$(".jqzoom").jqzoom(options);
});
function imageTrick(no){
    var smallSrc = $('#prodImage_'+no).attr('src');
    var largeSrc = $('#prodImage_0').attr('src');
    $('#prodImage_0').attr('src', smallSrc.replace('products/_thumbs', 'products'));
    $('#zoomImage').attr('href', smallSrc.replace('products/_thumbs', 'products'));
    $('#prodImage_'+no).attr('src', largeSrc.replace('products', 'products/_thumbs'));
}
function getOptions(){
	var options = {};
	$('select.prod_option').each(function(){
		options[$(this).prop('name')] = parseInt($(this).val());
	});
	return options;
}

$('.product-details-tabs').find('li a').on('click', function(ev)
{
	ev.preventDefault();
	var $group = $(this).parents('.product-details-tabs-wrapper');
	$group.find('.product-details-tabs .active').removeClass('active');
	$group.find('.product-details-tabpanel.active').removeClass('active');
	$(this).parents('li').addClass('active');
	$(this.getAttribute('href')).addClass('active');
});


/* When the first option is changed, update the second to only show the applicable options */
$(document).ready(function()
{
	var matrix = document.getElementById('product-matrix-id');
	if (matrix && matrix.value != '' && matrix.value != 0)
	{
		$('#option_0').on('change', function ()
		{
			var selected = $('#option_0').val();
			$.ajax({
				'url': '/frontend/products/get_option_2_list',
				'type': 'POST',
				'data': {
					'option1': selected,
					'matrix_id': matrix.value
				},
				'dataType': 'json',
				'async': false
			}).done(function (results)
				{
					if (typeof results == 'object')
					{
						var available_option_ids = [];

						for (var i = 0; i < results.length; i++)
						{
							available_option_ids.push(results[i]['option2']);
						}

						$('#option_1').find('option').each(function()
						{
							if (available_option_ids.indexOf(this.value) == -1 && this.value != '')
							{
								// Disable second options when not applicable. If an unapplicable one is currently selected, unselect it
								$(this).prop('selected', false).prop('disabled', true);
								$('#option_1').find('[value="'+this.value+'"]').prop('selected', false);
							}
							else
							{
								// If the option is applicable, ensure it is not disabled
								$(this).prop('disabled', false);
							}
						});
					}
				});
		});

		$('#option_0, #option_1').on('change', function ()
		{
			$.ajax({
				'url': '/frontend/products/get_matrix_option_details',
				'type': 'POST',
				'data': {
					'option1': $('#option_0').val(),
					'option2': $('#option_1').val(),
					'matrix_id': matrix.value
				},
				'dataType': 'json',
				'async': false
			})
				.done(function(results)
				{
					var $price_field        = $('#final_price');
					var $nonvat_price_field = $('#product-nonvat-price');
					var additional_price    = (results.price) ? parseFloat(results.price) : 0;
					var original_price      = $price_field.data('product_price');
					var vat_rate            = parseFloat($('#product-vat-rate').val());
					var adjusted_price      = original_price + additional_price * (1 + vat_rate);

					$price_field.html('&euro;'+adjusted_price.toFixed(2));
					$nonvat_price_field.html('&euro;'+((adjusted_price / (1+vat_rate)).toFixed(2)));
				});
		});
	}
});