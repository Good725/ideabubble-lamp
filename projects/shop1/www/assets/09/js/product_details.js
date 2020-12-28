function validateQty(id, quantity, options) {
    var form;
    if(document.getElementById('ProductDetailsForm') == null)
    {
        form = "#sign_builder_form"
    }
    else
    {
        form = "#ProductDetailsForm";
    }
    var valid = ($(form).validationEngine('validate'));
    if (valid) {
        var optArray = new Array();
        var optionsOk = true;
		var toBeValidated = false;
		$('select.prod_option').each(function(){
			optArray.push(this.value);
			if($(this).hasClass('validate[dropdown]'))
            {
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
                checkout_data.reload_minicart(status);
                $('div.successful').fadeIn("fast");
                $("div.successful").html("<br/><h3>Your product has been added to the cart. <a href='/checkout.html'>Checkout</a></h3>");
                setTimeout(function(){

                        $("div.successful").fadeOut("slow", function () {
                            $("div.successful").html('');
                        });

                    }, 10000);

            });
        }
		else{
			alert('Please ensure that all mandatory options have been selected.');
		}
    }

}//end of function

function validateQty_and_checkout(id, quantity, options) {
    var form;
    if(document.getElementById('ProductDetailsForm') == null)
    {
        form = "#sign_builder_form"
    }
    else
    {
        form = "#ProductDetailsForm";
    }

    var valid = ($(form).validationEngine('validate'));
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
function imageTrick(no){
    var smallSrc = $('#prodImage_'+no).attr('src');
    var largeSrc = $('#prodImage_0').attr('src');
    $('#prodImage_0').attr('src', smallSrc.replace('products/_thumbs', 'products'));
    $('#zoomImage').attr('href', smallSrc.replace('products/_thumbs', 'products'));
    $('#prodImage_'+no).attr('src', largeSrc.replace('products', 'products/_thumbs'));
}
function getOptions(){
	var options = {};
    if (document.getElementById('sign_builder_wrapper'))
    {
        $('#sign_builder_wrapper').find('select').each(function(){
            if($(this).hasClass("validate[required]"))
            {
                options[$(this).data('select_id')] = parseInt($(':selected',this).val());
            }
        });
    }
    else
    {
        $('.prod_option').each(function(){
            if($(this).hasClass("validate[required]") || $(this).hasClass('validate[dropdown,min[1]]'))
            {
                options[$(this).prop('name')] = parseInt($(this).val());
            }
        });
    }

    if(document.getElementById('finish_editor'))
    {
        if($("#builder_laminate label.selected input").val() == 1)
        {
            options['laminate'] = $("#builder_lamination_type").val();
        }
        else
        {
            options['laminate'] = 'None';
        }

        if($("#builder_adhesive label.selected input").val() == 1)
        {
            options['adhesive'] = 'Yes';
        }
        else
        {
            options['adhesive'] = 'No';
        }
    }

	return options;
}

function compile_canvas()
{
    //var canvas_size = $("#builder_size option:selected").text();
    //var layers = parse_layers();
    //$.post('/frontend/products/add_canvas',{id:$("#id").val(),canvas_size:canvas_size,layers:layers},function(data){});
}