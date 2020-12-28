$(document).ready(function() {
    if (typeof jqzoom != 'undefined')
    {
    	var options = {
            zoomWidth: 259,
            zoomHeight: 256,
            title: false,
            lens: false,
            xOffset: 10,
            yOffset: 0,
            position: "right"
        };
    	$(".jqzoom").jqzoom(options);
    }
});

function validateQty(id, quantity, options)
{
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
			if (this.value == ''){
				optionsOk = false;
			}
		});
        if (optionsOk) {
			CHECKOUT.addToCart(parseInt(id),parseInt(quantity),options,function(status,data){
                checkout_data.add_to_cart_callback(status,data);
                checkout_data.reload_minicart(status);

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
}

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
            if (this.value == ''){
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
}

function imageTrick(no){
    var smallSrc = $('#prodImage_'+no).attr('src');
    var largeSrc = $('#prodImage_0').attr('src');
    $('#prodImage_0').attr('src', smallSrc.replace('products/_thumbs', 'products'));
    $('#zoomImage').attr('href', smallSrc.replace('products/_thumbs', 'products'));
    $('#prodImage_'+no).attr('src', largeSrc.replace('products', 'products/_thumbs'));
}
function getOptions(){
	var options = {};
	$('.prod_option').each(function(){
		options[$(this).prop('name')] = parseInt($(this).val());
        if(isNaN(options[$(this).prop('name')]))
        {
            options[$(this).prop('name')] = ($(this).val());
        }
	});
	return options;
}