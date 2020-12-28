$(document).ready(function () {
    var canvas    = document.getElementById('tshirt_builder_canvas');
    var matrix_id = document.getElementById('tshirt_matrix').value;
    var overlay   = new Image();
    overlay.src   = document.getElementById('tshirt_overprint').value;

    // Redraw the tshirt when an option is changed
    $('.prod_option').on('change', function () {
		var option_1     = document.getElementById('option_0').value;
		var option_2     = document.getElementById('option_1').value;
		var thumb_canvas = document.getElementById('product_thumbnail_canvas');

        canvas.redraw(option_1, option_2);
		thumb_canvas.redraw(option_1, option_2, thumb_canvas.width / canvas.width);

		// update price
		updateProductOptionPrice();

		// If the selected option has a message to display
		var message = $(this).find(':selected').data('message');
		var $message_box = $('.product-option-message[data-option_number="'+$(this).data('option_number')+'"]');
		if (message && message.trim())
		{
			$message_box.html(message).fadeIn();
			setTimeout(function()
			{
				$message_box.fadeOut()
			}, 10000);
		}
		else
		{
			$message_box.hide();
		}
    });


    overlay.onload = function () {
        $('.prod_option').trigger('change');
    };

    // Use first option by default (temporary)
    var options1 = document.getElementById('option_0');
    if (options1.options.length > 1) {
        options1.selectedIndex = 1;
        $(options1).change();
    }

    // Change the color dropdown when a colour icon is clicked
    $('[name="swatch_color"]').on('change', function () {
        // document.getElementById('product_option_color').options[this.value].select;
        $('.product_option_color').val(this.value).trigger('change');
        $('.color_swatches .active').removeClass('active');
        $('.color_swatches input:checked').parents('label').addClass('active');
    });

    // preview colour change on hover
    var swatch_label = $('.color_swatches label');
    swatch_label.mouseenter(function () {
        canvas.redraw(document.getElementById('option_0').value, $(this).find('input').val());
    });
    swatch_label.mouseleave(function (ev)
	{
		var destination = (typeof ev.toElement != 'undefined') ? ev.toElement : ev.relatedTarget;

		// If the mouse is moved from a colour switcher to something that isn't a colour switcher,
		// revert to the currently selected colour
		if ( ! $(destination).is('.color_swatches label, .color_swatches label *'))
		{
			canvas.redraw(document.getElementById('option_0').value, document.getElementById('option_1').value);
		}
    });

    // When the first option is changed, only show the applicable choices in the second option list
    $('#option_0').on('change', function () {
        var selected = $('#option_1').val();
        $.ajax({
            'url': '/frontend/products/get_option_2_list',
            'type': 'POST',
            'data': {
                'option1': document.getElementById('option_0').value,
                'matrix_id': matrix_id
            },
            'dataType': 'json'
        }).success(function (results)
	        {
                if (typeof results == 'object') {
                    $('.color_swatches li').hide();
                    var html = '<option value="">Please select</option>';
					var is_selected;
                    for (var i = 0; i < results.length; i++)
					{
						is_selected = ((results[i]['option2'] == selected) || (selected == 0 && i == 0));

						html += '<option' +
                            ' value="' + results[i]['option2'] + '"' + (is_selected ? ' selected="selected"' : '') +
                            '>' + results[i]['label'] + '</option>';
                        $('.color_swatches li[data-id="' + results[i]['option2'] + '"]').show();
                    }

                    var options2 = document.getElementById('option_1');
                    options2.innerHTML = html;
                    if (options2.options.length > 1) {
						if ( ! options2.selectedIndex) {
							options2.selectedIndex = 1;
						}

                        $(options2).change();
                    }
                }
            });
    });
    $('#option_0').trigger('change');


});

var drawing_shirt_ajax;
var last_drawing_shirt_ajax_url = null;

HTMLCanvasElement.prototype.redraw = function(option_1, option_2, scale)
{
	scale = (typeof scale != 'undefined') ? scale : 1;
	var canvas = this;

	var matrix_id        = document.getElementById('tshirt_matrix').value;
	var url              = '/frontend/products/get_matrix_option_details?option1='+option_1+'&option2='+option_2+'&matrix_id='+matrix_id;
	var repeated_request = (url == last_drawing_shirt_ajax_url);

	last_drawing_shirt_ajax_url = url;

	// If an option is empty, do not continue.
	// If the request is the same as last time, do not continue
	if (option_1 && option_2 && matrix_id && ! repeated_request)
	{
		// If the AJAX function is called multiple times in quick succession, cancel unfinished requests and only finish the last one
		if(drawing_shirt_ajax) {
			drawing_shirt_ajax.abort();
		}

		setTimeout(function()
		{
			drawing_shirt_ajax = $.ajax({
				'url'     : url,
				'type'    : 'GET',
				'dataType': 'json'
			}).success(function (results) {
				if (typeof results.image != 'undefined')
				{
					canvas.draw_image(results.image, scale);
				}
				var $final_price  = $('#final_price');
				var base_price    = $final_price.data('product_price') ? parseFloat($final_price.data('product_price')) : 0;
				var matrix_price  = results.price ? parseFloat(results.price) : 0;
				var options_price = 0;
				$('#product_options').find('option:selected').each(function()
				{
					if ($(this).data('option_price'))
					{
						options_price += parseFloat($(this).attr('data-option_price'));
					}
				});
				$final_price.html('&euro;'+((base_price + matrix_price + options_price).toFixed(2)));

				var $facebook_btn = $('#tshirt-facebook-share');
				var link = $facebook_btn.attr('href');

				// Update the button to point to the new image
			   /*  Need to save data URL as a publicly-visible file before this will work
				var pattern = new RegExp("([?&])picture=.*?(&|$)", "i");
				$facebook_btn.attr('href', link.replace(pattern, '$1' + "picture=" + canvas.toDataURL() + '$2'));
				*/
			});
		}, 1);
	}
};

$('#tshirt-facebook-share').on('click', function(ev)
{
	ev.preventDefault();
	window.open(this.getAttribute('href'), '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes')
});

// Draw an image onto a canvas
HTMLCanvasElement.prototype.draw_image = function(src, scale)
{
	scale          = (typeof scale != 'undefined') ? scale : 1;
	var canvas     = this;
	var context    = canvas.getContext('2d');
	var overlay    = new Image();
	overlay.src    = document.getElementById('tshirt_overprint').value;

	var image_object    = new Image();
	image_object.onload = function () {
		context.drawImage(image_object, 0, 0, image_object.width * scale, image_object.height * scale);
		context.drawImage(overlay, Math.round((image_object.width - 130) / 2) * scale, 100 * scale, 130 * scale, 130 * scale);
	};
	image_object.src    = src;
};