var layers;
var laminate_selected = false;
var adhesive_selected = false;
var custom_timestamp = 0;
var sign_thumbnail = '';
var button = null;
var checkout_data = checkout_data || {};

$(document).ready(function () {
    $('#available_images').load('/frontend/products/available_images/' + document.getElementById('builder_category_list').value, function () {
        var default_image = this.getAttribute('data-default').replace(/ /g, '%20');
        $(this).find('[src$="/' + default_image + '"]').parents('figure').addClass('selected');
        prepare_drag_and_drop();
        sign_builder();
    });

    $(document).on('click', '#preview_sign', function () {
        var delete_icons_box = document.getElementById('show_delete_icons');
        var show_delete_value = delete_icons_box.checked;
        delete_icons_box.checked = false;
        $(delete_icons_box).trigger('change');
        document.getElementById('complete_sign_preview_image').src = document.getElementById('builder_canvas').toDataURL('image/png');
        delete_icons_box.checked = show_delete_value;
        $('#preview_modal').show_modal();
        $("#preview_sign_dialog").show();
        $("#purchase_complete_dialog").hide();
    });

    $("#builder_lamination_type").on('change', function () {
        var laminate_prices = {A0: 9.00, A1: 5.00, A2: 3.00, A3: 2.00, A4: 1.50, A5: 1.00};
        var size = $('#builder_size').find(':selected').text().split(' (')[0];
        if (this.value != "" && !laminate_selected) {
            laminate_selected = true;
            $("#static_price").text(parseFloat(($("#static_price").text()) + laminate_prices[size]).toFixed(2));
            $('#unit_price').text( parseFloat($("#static_price").text()) );
            $(".full_price").text( parseFloat($("#static_price").text()) );
        }
        else if (this.value == "") {
            laminate_selected = false;
            $("#static_price").text(parseFloat(($("#static_price").text()) - laminate_prices[size]).toFixed(2));
            $('#unit_price').text( parseFloat($("#static_price").text()) );
            $(".full_price").text( parseFloat($("#static_price").text()) );
        }
    });

    $("#builder_adhesive").find("input").on('click', function () {
        var adhesive_prices = {A0: 9.00, A1: 5.00, A2: 3.00, A3: 2.00, A4: 1.50, A5: 1.00};
        var size = $('#builder_size').find(':selected').text().split(' (')[0];
        if ($(this).val() == "1" && !adhesive_selected) {
            adhesive_selected = true;
            $("#static_price").text(parseFloat(($("#static_price").text()) + adhesive_prices[size]).toFixed(2));
            $('#unit_price').text( parseFloat($("#static_price").text()) );
            $(".full_price").text( parseFloat($("#static_price").text()) );
        }
        else if ($(this).val() == "0" && adhesive_selected) {
            adhesive_selected = false;
            $("#static_price").text(parseFloat(($("#static_price").text()) - adhesive_prices[size]).toFixed(2));
            $('#unit_price').text( parseFloat($("#static_price").text()) );
            $(".full_price").text( parseFloat($("#static_price").text()) );
        }
    });
});

function disable_screen() {
    document.getElementById('modal').style.display = 'block';
}

function enable_screen() {
    document.getElementById('modal').style.display = 'none';
}

function get_layers() {
    return layers;
}

function parse_layers() {
    var data = [];
    $.each(layers, function () {
        data.push({background_color: this.background_color, border_color: this.border_color, height: this.height, line_height: this.line_height, padding: this.padding, rounded: this.rounded, truescale: this.truescale, src: this.src, nulltext: this.nulltext, text: this.text, text_align: this.text_align, text_styles: this.text_styles, Objecttext_width: this.Objecttext_width, type: this.type, width: this.width, x: this.x, y: this.y});
    });

    return JSON.stringify(data);
}

// Clone "layers" and remove layers image object, so that they can be stringified
function parse_layers2() {
    var data = [];
    var i = 0;
    $.each(layers, function () {
        data[i] = jQuery.extend({}, this);
        data[i].image = null;
        i++;
    });
    return JSON.stringify(data);
}

function sign_builder() {
    var canvas = document.getElementById('builder_canvas');
    var canvas_wrapper = document.getElementById('canvas_guidelines');
    var context = canvas.getContext('2d');
    var margin = Number($('#safe_line').css('left').replace('px', ''));
    var drag_index, dragging, mouse_x, mouse_y, drag_hold_x, drag_hold_y;
    var selected_layer;
    var cursor_interval;
    var saved_layers = (document.getElementById('sign_builder_layers_input') != null && document.getElementById('sign_builder_layers_input').value != '') ? JSON.parse(document.getElementById('sign_builder_layers_input').value) : [];
    var cms_editor = (document.getElementById('sign_builder_tab')) ? true : false;
	var user_agent = window.navigator.userAgent;
	// variable to say if the user is using Internet Explorer
    var is_ie = (
		user_agent.indexOf('MSIE ') > 0 || // older versions of IE
		!!user_agent.match(/Trident.*rv\:11\./) || // IE11
		user_agent.indexOf('Edge/') != -1 // IE Edge
	);

    initialize();

    function initialize() {
        layers = [];
        update_dimensions();
        if (saved_layers.length == 0) {
            var image = $('#available_images').find('.selected img');
            if (image.length > 0) {
                image[0].onload = function () {
                    initial_layers();
                    draw_layers(true);
                    update_dimensions();
                };
            }
        }
        else {
            // convert from type Object to type Layer
            for (var i = 0; i < saved_layers.length; i++) {
                var layer = new Layer(this.type, this.width, this.height, this.x, this.y);
                for (var key in saved_layers[i]) {
                    layer[key] = saved_layers[i][key];
                }
                saved_layers[i] = layer;
            }

            layers = saved_layers;
            draw_layer_list();
            draw_layers(true);
        }


        canvas_wrapper.addEventListener('mousedown', mousedown_listener, false);
        // canvas_wrapper.addEventListener('dblclick',   mousedown_listener, false);
        canvas_wrapper.addEventListener('touchstart', mousedown_listener, false);
    }

    // Create the layers for the product's image and default text
    function initial_layers() {
        // Image
        var image = $('#available_images').find('.selected img');

        if (image.length > 0) {
            var src = image.attr('src').replace('/_thumbs/', '/');
            var scale = 0.75;
            var image_data_width = image[0].getAttribute('data-width');
            var image_data_height = image[0].getAttribute('data-height');
            if (!image_data_width || !image_data_height) {
                var temp = new Image();
                temp.src = image[0].src;
                image_data_width = temp.width;
                image_data_height = temp.height;
            }

            var image_width = canvas.width - 2 * margin;
            var image_height = image_data_height / image_data_width * image_width;
            var image_x = margin + (canvas.width - image_width * scale) / 2;
            var image_y = margin + 0;
            var image_layer = new Layer('image', image_width, image_height, image_x, image_y);
            image_layer.set_src(src);
            image_layer.set_scale(scale);
            layers.push(image_layer);

            // Text
            var text_y = margin + image_height * scale + 10;
            var text_layer = new Layer('text', 0, 0, 0, text_y);
            text_layer.set_text('Enter text');
            var text_x = margin + (canvas.width - text_layer.width) / 2;
            text_layer.set_x(text_x);
            layers.push(text_layer);
            draw_layer_list();
        }
    }

    // Draw all the layers on to the canvas.
    function draw_layers(new_image) {
        new_image = (typeof new_image == 'undefined') ? false : new_image;
        var i;

        if (new_image) {
            // ensure all the images have loaded before drawing
            // Always run this if the browser is Internet Explorer, so that the image cross-origin gets set
            var loaders = [];
            for (i = 0; i < layers.length; i++) {
                if (layers[i].type == 'image') {
                    loaders.push(create_image(i, layers[i].src));
                }
            }
            $.when.apply(null, loaders).done(function () {
                update_layers();
            });
        }
        else {
            update_layers();
        }

        //alert("Draw Image Complete");
    }

    // Redraw all the layers, with updated settings
    function update_layers() {
        canvas.width = canvas.width + 0;
        var background_color = document.getElementById('builder_background_color').value;
        var show_delete_icons = document.getElementById('show_delete_icons').checked;
        draw_rectangle(0, 0, canvas.width, canvas.height, background_color, 0, 0, 0);

        for (var i = 0; i < layers.length; i++) {
            if (layers[i].type == 'image') {
                draw_image_layer(layers[i]);
            }
            else if (layers[i].type == 'text') {
                draw_text_layer(layers[i]);
            }
            if (show_delete_icons) {
                draw_delete_icon(layers[i]);
            }
            if (i == selected_layer) {
                draw_guideline(layers[selected_layer]);
            }
        }

        var layers_input = document.getElementById('sign_builder_layers_input')
        if (layers_input) {
            layers_input.value = parse_layers2();
        }

    }

    function draw_layer_list() {
        var list_html = '';
        var text_list_html = '';
        var image_list_html = '';
        var text_i = 1, image_i = 1;

        for (var i = 0; i < layers.length; i++) {
            var active = (i == selected_layer) ? ' class="active"' : '';
            if (layers[i].type == 'image') {
                list_html += '<li ' + active + 'data-id="' + i + '" style="background-image:url(\'' + layers[i].src + '\');">' +
                    '<strong>Image</strong>' +
                    '<span class="delete_layer" title="delete"></span>' +
                    '</li>';
                image_list_html += '<li data-id="' + i + '"' + active + '>' + (text_i) + '<span class="delete_layer" title="delete"></span></li>';
                text_i++;
            }
            if (layers[i].type == 'text') {
                list_html += '<li ' + active + 'data-id="' + i + '">' +
                    '<strong>Text</strong>' +
                    '<span class="layer_text" title="' + layers[i].text + '">' + layers[i].text + '</span><span class="delete_layer" title="delete"></span>' +
                    '</li>';
                text_list_html += '<li data-id="' + i + '"' + active + '>' + (image_i) + '<span class="delete_layer" title="delete"></span></li>';
                image_i++;
            }
        }
        text_list_html += '<li class="new_layer" data-type="text" title="Add new text layer">+ add text box</li>';
        image_list_html += '<li class="new_layer" data-type="image" title="Add new image">+ add image</li>';
        $('#layer_list').find('ol').html(list_html);
        $('#text_layer_tabs').find('ul').html(text_list_html);
        $('#image_layer_tabs').find('ul').html(image_list_html);
    }


    function create_image(i, src)
	{
        var deferred = $.Deferred();
        var image_object = new Image();

		// If the layer is not a data URL, set the cross origin, for Internet Explorer support
		if ( ! is_data_url(src))
		{
			image_object.crossOrigin = 'Anonymous';
		}
        image_object.onload = function ()
		{
            deferred.resolve();
        };
        image_object.src = src;
        layers[i].image = image_object;
        return deferred.promise();
    }

    function draw_image_layer(layer)
    {
        var width  = layer.width  * layer.scale;
        var height = layer.height * layer.scale;
        if (typeof layer.image != 'undefined')
        {
			// If the layer is not a data URL, set the cross origin, for Internet Explorer support
			if ( ! is_data_url(layer.image.src))
			{
				layer.image.crossOrigin = 'Anonymous';
			}

			// Internet Explorer does not like SVGs being added to the canvas. It will not allow you to use .toDataURL()
			if (layer.image.src.split('.').pop().toLowerCase() == 'svg' && is_ie && ! layer.png_image)
			{
				// Convert the SVG to a PNG
				var $object = $('<object class="accessible-hide" data="'+layer.image.src+'"</object>');
				$('body').append($object);
				$object[0].onload = function()
			    {
					setTimeout(function()
					{
						var svg = $object[0].contentDocument.querySelector('svg');
						var png = $(svg).svg_to_data_url("image/png",
						{
							callback: function(data)
							{
								var png_image = new Image();
								png_image.onload = function()
								{
									layer.png_image = png_image;
									context.drawImage(layer.png_image, layer.x, layer.y, width, height);
								};
								png_image.src = data;
							}
						});
						$object.remove();
					},1000);
			    };

			}
			else
			{
				var image = (layer.png_image) ? layer.png_image : layer.image;
				context.drawImage(image, layer.x, layer.y, width, height);
			}
        }
    }

    function draw_text_layer(layer) {
        // Draw a rectangle
        var fill = (layer.background_color == 'transparent') ? false : layer.background_color;
        var stroke = layer.border_color;
        var radius = (layer.rounded == 0) ? 0 : layer.padding;
        var border_width = (layer.border_width) ? layer.border_width : 0;
        draw_rectangle(layer.x, layer.y, layer.width, layer.height, fill, stroke, radius, border_width);

        // Add text
        var text_array = (layer.text).split('\n');
        context.font = layer.text_styles['font-size'] + ' ' + layer.text_styles['font-family'];
        context.textAlign = layer.text_align;
        context.fillStyle = layer.text_styles['color'];

        var x = layer.x;

        switch (layer.text_align) {
            case 'center':
                x = x + layer.width / 2;
                break;
            case 'right' :
                x = x + layer.width - layer.padding;
                break;
            default      :
                x = x + layer.padding;
        }

        for (var i = 0; i < text_array.length; i++) {
            context.fillText(text_array[i], x, layer.y + (i + 1) * layer.line_height);
        }
    }

    function draw_rectangle(x, y, width, height, fill, stroke, radius, border_width, dashed) {
        stroke = (typeof stroke == 'undefined') ? false : stroke;
        radius = (typeof radius == 'undefined') ? 0 : radius;
        border_width = (typeof border_width == 'undefined') ? 0 : border_width;
        dashed = (typeof dashed == 'undefined') ? false : dashed;

        (dashed) ? context.setLineDash([6]) : context.setLineDash([]);

        context.beginPath();
        context.moveTo(x + radius, y);
        context.lineTo(x + width - radius, y);
        context.quadraticCurveTo(x + width, y, x + width, y + radius);
        context.lineTo(x + width, y + height - radius);
        context.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        context.lineTo(x + radius, y + height);
        context.quadraticCurveTo(x, y + height, x, y + height - radius);
        context.lineTo(x, y + radius);
        context.quadraticCurveTo(x, y, x + radius, y);
        context.closePath();

        if (stroke && border_width != 0) {
            context.lineWidth = border_width;
            context.strokeStyle = stroke;
            context.stroke();
        }
        if (fill) {
            context.fillStyle = fill;
            context.fill();
        }
    }

    function draw_delete_icon(layer) {
        var scale = (layer.type == 'image') ? layer.scale : 1;
        var x = layer.x + layer.width * scale - 15;
        var y = layer.y + 5;

        context.font = '16px Arial';
        context.fillStyle = '#FF0000';
        context.textAlign = 'center';

        draw_rectangle(x, y, 10, 10, false, '#FF0000', 0, 1);

        context.fillText(String.fromCharCode(215), x + 5, y + 10); // 215 = multiplication symbol
    }

    function draw_guideline(layer) {
        if (document.getElementById('show_delete_icons').checked) {
            var gap = 8;
            var color = $('#preview_sign').css('background-color');
            draw_rectangle(
                layer.x - gap, // x
                layer.y - gap, // y
                layer.width * layer.scale + 2 * gap, // width
                layer.height * layer.scale + 2 * gap, // height
                'transparent', // fill
                color,         // stroke
                0,             // border radius
                2,             // border width
                true           // dashed
            );
        }
    }

	// Check if a string is a data URL
	function is_data_url(string)
	{
		var data_url_regex = /^\s*data:([a-z]+\/[a-z]+(;[a-z\-]+\=[a-z\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i;
		return (data_url_regex.test(string));
	}

    /* Event listener functions */

    // mousedown and touchstart listener function
    function mousedown_listener(ev) {
        // The coordinates are within ev.changedTouches, if it is a touch event (tablets and phones)
        // and within ev, if it is a mouse event
        var event = (typeof ev.changedTouches == 'undefined') ? ev : ev.changedTouches[0];
        var dblclick = (ev.type == 'dblclick');
        if (dblclick) {
            dragging = false;
            canvas.removeEventListener('mousedown', mousedown_listener, false);
            canvas.removeEventListener('touchstart', mousedown_listener, false);
        }


        // Colour selection mode
        if (document.getElementsByClassName('dropper_mode').length != 0) {
            var pixel_color = canvas.getContext('2d').getImageData(event.offsetX, event.offsetY, 1, 1).data;
            $('.dropper_mode').removeClass('dropper_mode');
            $('.gray_out').remove();
            var color_string = 'rgba(' + pixel_color[0] + ',' + pixel_color[1] + ',' + pixel_color[2] + ',' + pixel_color[3] + ')';

            var custom_palette = $('.custom_palette');
            if (custom_palette.find('td:not([style])').length == 0) {
                custom_palette.append('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
            }
            custom_palette.find('td:not([style])').first().css('background-color', color_string);
        }

        // Layer dragging mode
        else {
            var highest_index = -1;
            var boundary = canvas.getBoundingClientRect();

            mouse_x = (event.clientX - boundary.left) * canvas.width / boundary.width;
            mouse_y = (event.clientY - boundary.top) * canvas.width / boundary.width;

            // Loop through layers to check for hits.
            // If there are multiple layers under the click/touch, get the top one
            for (var i = 0; i < layers.length; i++) {
                if (hit_test(layers[i], mouse_x, mouse_y)) {
                    if (!validate_size()) {
                        $('#complete_step_1_modal').show_modal();
                    }
                    else if (!validate_material() || !validate_background()) {
                        $('#complete_step_2_modal').show_modal();
                    }
                    else {
                        if (hit_delete_test(layers[i], mouse_x, mouse_y)) {
                            show_delete_modal(layers[i]);
                        }
                        else {
                            open_editor(i, dblclick);
                        }

                        dragging = true;
                        drag_hold_x = mouse_x - layers[i].x;
                        drag_hold_y = mouse_y - layers[i].y;
                        highest_index = i;
                        drag_index = i;
                    }

                }
            }

            if (dragging) {
                window.addEventListener('mousemove', mousemove_listener, false);
                window.addEventListener('touchmove', mousemove_listener, false);
            }
            canvas.removeEventListener('mousedown', mousedown_listener, false);
            canvas.removeEventListener('touchstart', mousedown_listener, false);
            window.addEventListener('mouseup', mouseup_listener, false);
            window.addEventListener('touchend', mouseup_listener, false);

        }

        if (ev.preventDefault) {
            ev.preventDefault();
        }
        else if (ev.returnValue) {
            ev.returnValue = false;
        }

        return false;
    }

    function mouseup_listener() {
        canvas.addEventListener('mousedown', mousedown_listener, false);
        canvas.addEventListener('touchstart', mousedown_listener, false);
        window.removeEventListener('mouseup', mouseup_listener, false);
        window.removeEventListener('mouseend', mouseup_listener, false);

        if (dragging) {
            // focus the text editor after the user has finished dragging the text layer
            // focusing immediately on click/touch, gets annoying when trying to drag on phone/tablet
            if (layers[selected_layer].type == 'text') {
                document.getElementById('builder_text').blur();
                document.getElementById('builder_text').focus();
                $('#builder_text').val('').val(layers[selected_layer].text);
            }
            dragging = false;
            window.removeEventListener('mousemove', mousemove_listener, false);
            window.removeEventListener('touchmove', mousemove_listener, false);
        }
    }

    function mousemove_listener(ev) {
        var touch = (typeof ev.changedTouches == 'undefined') ? ev : ev.changedTouches[0];

        var pos_x, pos_y;
        var layer_width = layers[drag_index].width * layers[drag_index].scale;
        var layer_height = layers[drag_index].height * layers[drag_index].scale;
        var max_x = canvas.width - layer_width - margin;
        var max_y = canvas.height - layer_height - margin;
        var boundary = canvas.getBoundingClientRect();

        mouse_x = (touch.clientX - boundary.left) * canvas.width / boundary.width;
        mouse_y = (touch.clientY - boundary.top) * canvas.width / boundary.width;

        pos_x = mouse_x - drag_hold_x;
        pos_x = (pos_x < margin) ? margin : ((pos_x > max_x) ? max_x : pos_x);
        pos_y = mouse_y - drag_hold_y;
        pos_y = (pos_y < margin) ? margin : ((pos_y > max_y) ? max_y : pos_y);

        layers[drag_index].set_x(pos_x);
        layers[drag_index].set_y(pos_y);

        draw_layers();
    }

    // See if the user touched / clicked a layer on the canvas
    function hit_test(layer, click_x, click_y) {
        var x_hit = (click_x >= layer.x && click_x <= layer.x + layer.width * layer.scale);
        var y_hit = (click_y >= layer.y && click_y <= layer.y + layer.height * layer.scale);
        return (x_hit && y_hit);
    }

    // See if the user touched / clicked a layer's delete icon
    function hit_delete_test(layer, click_x, click_y) {
        if (document.getElementById('show_delete_icons').checked) {
            var x_hit = (click_x >= layer.x + layer.width * layer.scale - 15 && click_x <= layer.x + layer.width * layer.scale);
            var y_hit = (click_y >= layer.y && click_y <= layer.y + 15);
            return (x_hit && y_hit);
        }
        return false;
    }

    // Open the image or text editor, with a specified layer's values
    function open_editor(index, double_click) {
        // Set the active tab
        var layer_tabs = $('.sb-tabs');
        layer_tabs.find('.active').removeClass('active');
        layer_tabs.find('li[data-id=' + index + ']').addClass('active');

        if (typeof double_click == 'undefined') double_click = false;

        selected_layer = index;
        draw_layers();
        var layer = layers[index];

        if (layer.type == 'image') {
            $('.toggleable-block').hide();
            $('#image_editor').show();
            $('#builder_scale').val(layer.scale * 100).trigger('input');
            $('#builder_image_preview').css('background-image', 'url(\'' + layer.src + '\')');
            $('#builder_scale_wrapper').toggle(!( !layer.src));

        }
        else if (layer.type == 'text') {
            $('.toggleable-block').hide();
            $('#text_editor').show();
            $('#builder_font').val(layer.text_styles['font-family']);
            $('#builder_font_color').val(layer.text_styles['color']).trigger('change');
            $('#builder_text_border_color').val(layer.border_color).trigger('change');
            $('#builder_text_background_color').val(layer.background_color).trigger('change');
            $('#builder_text').val(layer.text);
            $('#builder_text_align, #builder_rounded').find('.selected').removeClass('selected');
            $('[name="text_align"][value="' + layer.text_align + '"]').parent().addClass('selected');
            $('[name="rounded"][value="' + ((layer.rounded) ? 1 : 0) + '"]').parent().addClass('selected');
            $('#builder_font_size').val(layer.text_styles['font-size'].replace('pt', '')).trigger('input');
            $('#builder_text_width').val(layer.width / canvas.width * 100).trigger('input');
            $('#builder_text_width_auto').prop('checked', this.fixed_width);
            $('#builder_text_height').val(layer.height / canvas.width * 100).trigger('input');
            $('#builder_text_height_auto').prop('checked', this.fixed_height);
        }
        var layer_list = $('#layer_list');
        layer_list.find('li.active').removeClass('active');
        layer_list.find('li[data-id="' + index + '"]').addClass('active');
    }

    $('#layer_list').on('click toucheend', 'li', function () {
        open_editor(this.getAttribute('data-id'));
        var textbox = $('#builder_text');
        var value = textbox.val();
        textbox.focus().val('').val(value); // reentering the text to move the text cursor to the end
    });

    /*
     * ****************************************
     * Choose new or existing
     * ****************************************
     */
    $('#select_existing_sign_button').on('click', function (ev) {
        var category_id = document.getElementById('existing_sign_category_list').value;
        $('#browse_existing_signs_body').load('/frontend/products/ajax_list_sign_builders/' + category_id, function () {
            $('#browse_existing_signs_modal').show_modal();
        });
    });

    $('#existing_sign_category_list').on('change', function () {
        var category_id = document.getElementById('existing_sign_category_list').value;
        $('#browse_existing_signs_body').load('/frontend/products/ajax_list_sign_builders/' + category_id);
    });

    $('#start_blank_button').on('click', function () {
        $('.controls_cover').fadeOut();
    });

    /*
     * ****************************************
     * Clicking an accordion button
     * ****************************************
     */

    $('button[data-step]').on('click', function () {
        var step = this.getAttribute('data-step');
        var blocks = $('.toggleable-block');

        if (step > 1 && !validate_size()) {
            $('#complete_step_1_modal').show_modal();
            blocks.hide();
            document.getElementById('size_editor').style.display = 'block';
        }
        else if (step > 2 && ( !validate_material() || !validate_background())) {
            $('#complete_step_2_modal').show_modal();
            blocks.hide();
            document.getElementById('material_editor').style.display = 'block';
        }
        else {
            var pane_name = this.getAttribute('data-pane');
            var pane = $('#' + pane_name);

            if (pane.is(':visible')) {
                pane.hide();
            }
            else {
                blocks.hide();

                // If "Add images" or "Add text" is clicked
                // * If the selected layer is of the same type, load its details
                // * Else, load the details of the last layer of its type
                // * Else, add a new layer of the type
                if (pane_name == 'image_editor' || pane_name == 'text_editor') {
                    var type = pane_name.substring(0, pane_name.indexOf('_'));
                    if (typeof selected_layer == 'undefined' || selected_layer.type != type) {
                        var found = false;
                        for (var i = layers.length - 1; i >= 0 && !found; i--) {
                            if (layers[i].type == type) {
                                selected_layer = i;
                                found = true;
                            }
                        }
                        if (!found) {
                            var layer = new Layer(type, 0, 0);
                            if (type == 'text')  layer.set_text('Enter text');
                            if (type == 'image') layer.set_scale(.5);
                            layers.push(layer);
                            selected_layer = layers.length - 1;
                            draw_layer_list();
                            draw_layers();
                        }
                    }
                    open_editor(selected_layer);
                    if (type == 'text') $('#builder_text').blur().focus().val('').val(layers[selected_layer].text);
                }

                pane.show();
            }
        }

    });

    /*
     * ****************************************
     * Adding a new layer
     * ****************************************
     */

    $('.sb-tabs').on('click', '.new_layer', function () {
        var layer;
        var editors = $('#text_editor, #image_editor');
        editors.find(':input').each(function () {
            this.value = this.getAttribute('value');
        });
        editors.find('.color_value').val('');
        var type = this.getAttribute('data-type');
        if (this.getAttribute('data-type') == 'text') {
            layer = new Layer('text', 0, 0);
            layer.set_text('Enter text');
        }
        else {
            layer = new Layer('image', 0, 0);
            layer.set_scale(.5);
        }
        layers.push(layer);
        selected_layer = layers.length - 1;
        open_editor(selected_layer);
        if (layer.type == 'text') $('#builder_text').focus().val('').val(layer.text);
        draw_layer_list();
        draw_layers();

    });

    /*
     * ****************************************
     * Editing the selected layer
     * ****************************************
     */

    // Open editor
    $('.sb-tabs').on('click', 'li:not(.new_layer):not(.active)', function () {
        open_editor(this.getAttribute('data-id'));
    });

    /*
     * Editing selected image layer
     */
    // browse image
    $('#available_images').on('click', 'img', function ()
	{
        var new_image = (layers[selected_layer].src == null); // To distinguish between adding and replacing an image

        layers[selected_layer].set_src(this.getAttribute('src').replace('/_thumbs/', '/'));
        layers[selected_layer].set_scale(0.75);
        layers[selected_layer].set_width(canvas.width - 2 * margin);

        // thumbnail images have their original dimensions stored in data- attributes
        var image_width  = this.getAttribute('data-width');
        var image_height = this.getAttribute('data-height');

        // if the data- attributes don't exist, use the embedded images dimensions
        if (image_width == null || image_height == null)
        {
            var temp;
            if (is_ie)
            {
                // Internet Explorer requires the image to be in the DOM to get its width and height
                temp = document.getElementById('dummy_image');
                temp.onload = function()
                {
                    add_existing_image(temp.width, temp.height, new_image);
					setTimeout(function()
				   {
					   // it should work the first time, but do again after half a second to be safe
					   add_existing_image(temp.width, temp.height, new_image);
				   }, 500);
                    temp.src = '';
                };

				temp.src = this.src;

            }
            else
            {
                temp = new Image();
                temp.onload = function()
                {
                    add_existing_image(temp.width, temp.height, new_image);
					temp.src = '';
                };
				temp.src = this.src;
            }
        }
        else
        {
            add_existing_image(image_width, image_height, new_image);
        }
    });

    function add_existing_image(image_width, image_height, new_image)
    {
        layers[selected_layer].set_height(image_height / image_width * layers[selected_layer].width);

        // If it's a new image, put it in the centre.
        // Otherwise leave it in the same position as the image it is replacing
        if (new_image)
		{
            layers[selected_layer].set_x((canvas.width - layers[selected_layer].width * layers[selected_layer].scale) / 2);
            layers[selected_layer].set_y((canvas.height - layers[selected_layer].height * layers[selected_layer].scale) / 2);
        }
		else
		{
			layers[selected_layer].png_image = false; // wipe this if the image is being replaced
		}

        draw_layers(true);
        draw_layer_list();
        open_editor(selected_layer);
        $('#browse_images_modal').hide_modal();
    }

	// button upload image
	$('#builder_upload').on('change', function()
	{
		var file = this.files[0];
		if (validate_upload(file))
		{
			var fr = new FileReader();
			fr.onload = function()
			{
				var imageObj = new Image();
				imageObj.onload = function()
				{
					layers[selected_layer].set_src(imageObj.src);
					layers[selected_layer].set_width(imageObj.width);
					layers[selected_layer].set_height(imageObj.height);

					$('#upload_image_modal').hide_modal();
					draw_layers(true);
					draw_layer_list();
					open_editor(selected_layer);
				};
				imageObj.src = fr.result;
			};
			fr.readAsDataURL(file);
		}
	});

    // drag and drop upload image
    $('#builder_drop_upload_file').on('change', function()
	{
        var imageObj = new Image();
        imageObj.src = this.value;
        imageObj.onload = function ()
		{
            layers[selected_layer].set_src(imageObj.src);
            layers[selected_layer].set_width(imageObj.width);
            layers[selected_layer].set_height(imageObj.height);

            $('#upload_image_modal').hide_modal();
            draw_layers(true);
            draw_layer_list();
            open_editor(selected_layer);
        };
    });

    // scale image
    $('#builder_scale').on('change', function () {
        layers[selected_layer].set_scale(this.value / 100);
        update_layers();
    });

    /*
     * Editing selected text layer
     */
    $('#builder_text').on('keyup', function () {
        layers[selected_layer].set_text(this.value);
        update_layers();
        draw_layer_list();
    });
    $('#builder_font').on('change', function () {
        layers[selected_layer].set_text_style('font-family', this.value);

        // If the font is from Google fonts, add the <link /> to Google fonts, if it doesn't already exist
        if (this[this.selectedIndex].getAttribute('data-google_font')) {
            var font = (this[this.selectedIndex].value).replace(/^'|'$/g, '');
            var href = 'http://fonts.googleapis.com/css?family=' + (font.replace(/ /g, '+'));

            if (!document.querySelector('link[href="' + href + '"]')) {
                var link = document.createElement('link');
                link.setAttribute('rel', 'stylesheet');
                link.setAttribute('type', 'text/css');
                link.setAttribute('href', href);
                document.head.appendChild(link);

                $(link).load(function () {
                    update_layers();
                    setTimeout(function () {
                        update_layers();
                    }, 3000);
                });
            }
            else {
                update_layers();
                setTimeout(function () {
                    update_layers();
                }, 3000);
            }
        }
        else {
            update_layers();
            setTimeout(function () {
                update_layers();
            }, 3000);
        }


    });
    $('#builder_font_size').on('change', function () {
        layers[selected_layer].set_text_style('font-size', this.value + 'pt');
        update_layers();
    });
    $('#builder_font_color').on('change', function () {
        layers[selected_layer].set_text_style('color', this.value);
        $(this).find('\+ .picker_label').css('border-bottom-color', this.value);
        update_layers();
    });
    $('#builder_text_border_color').on('change', function () {
        layers[selected_layer].set_border_color(this.value);
        $(this).find('\+ .picker_label').css('background-color', this.value);
        update_layers();
    });
    $('#builder_border_width').on('change', function () {
        layers[selected_layer].set_border_width(this.value);
        update_layers();
    });
    $('#builder_text_background_color').on('change', function () {
        layers[selected_layer].set_background_color(this.value);
        $(this).find('\+ .picker_label').css('background-color', this.value);
        update_layers();
    });
    $('#builder_text_align').on('click', function () {
        layers[selected_layer].set_text_align($(this).find(':checked').val());
        update_layers();
    });
    $('#builder_rounded').on('click', function () {
        var value = ($(this).find(':checked').val() == 1);
        layers[selected_layer].set_rounded(value);
        update_layers();
    });
    $('#builder_text_width_auto').on('change', function () {
        if ($(this).prop('checked')) {
            layers[selected_layer].set_fixed_width(false);
            update_layers();
        }
    });
    $('#builder_text_width').on('change', function () {
        $('#builder_text_width_auto').prop('checked', false);
        layers[selected_layer].set_fixed_width(true);
        layers[selected_layer].set_width(canvas.width * this.value / 100);
        update_layers();
    });
    $('#builder_text_height_auto').on('change', function () {
        if ($(this).prop('checked')) {
            layers[selected_layer].set_fixed_height(false);
            update_layers();
        }
    });
    $('#builder_text_height').on('change', function () {
        $('#builder_text_height_auto').prop('checked', false);
        layers[selected_layer].set_fixed_height(true);
        layers[selected_layer].set_height(canvas.width * this.value / 100);
        update_layers();
    });

    // Display cursor at the end of text, while the text is being edited
    document.getElementById('builder_text').onfocus = function () {
        clearInterval(cursor_interval);
        var layer = layers[selected_layer];
        var text_box = this;
        var i = 0;
        cursor_interval = setInterval(function () {
            if (text_box === document.activeElement) // only continue the loop, if the textarea is still selected
            {
                if (i == 1) {
                    var cursor_position = get_caret_position(text_box);
                    // Draw the cursor to be displayed for half a second.
                    var x = layer.x;
                    switch (layer.text_align) {
                        //case 'center': x = x + layer.width / 2;             break;
                        case 'right' :
                            x = x + layer.width - layer.padding;
                            break;
                        default      :
                            x = x + layer.padding;
                    }

                    var substring = layer.text.substr(('\n' + layer.text).lastIndexOf('\n'), cursor_position.col);
                    context.font = layer.text_styles['font-size'] + ' ' + layer.text_styles['font-family'];
                    context.textAlign = layer.text_align;
                    context.fillStyle = layer.text_styles['color'];
                    x = x + context.measureText(substring).width;
                    context.restore();

                    context.fillText('|', x, layer.y + (cursor_position.row + 1) * layer.line_height);

                }
                else {
                    // Remove the cursor for the other half of the second.
                    draw_layers();
                }
                i = (i + 1) % 2;
            }
            else {
                clearInterval(cursor_interval);
                draw_layers();
            }

        }, 500);
    };

    // Get text cursor position.
    // Based on http://flightschool.acylt.com/devnotes/caret-position-woes/
    function get_caret_position(field) {
        var results = {pos: 0, row: 0, col: 0};
        // IE Support
        if (document.selection) {
            field.focus();                                            // Set focus on the element
            var selection = document.selection.createRange();         // To get cursor position, get empty selection range
            selection.moveStart('character', -field.value.length);   // Move selection start to 0 position
            results.pos = selection.text.length;                      // The caret position is selection length
        }
        else if (field.selectionStart || field.selectionStart == '0') // Firefox support
        {
            results.pos = field.selectionStart;
        }

        var text = field.value.substr(0, results.pos);
        results.row = text.split('\n').length - 1;
        results.col = text.substr(text.lastIndexOf('\n') + 1).length;

        return results;
    }

    /*
     * ****************************************
     * Deleting layers
     * ****************************************
     */

    // Removing all layers (clearing the canvas)
    $('#clear_canvas').on('click', function () {
        $('#clear_canvas_modal').show_modal();
    });

    $('#confirm_clear_canvas_button, #create_another_button').on('click', function () {
        layers = [];
        var background_picker = document.getElementById('builder_background_color');
        background_picker.value = background_picker.getAttribute('data-default');
        $(background_picker).trigger('change');
        document.getElementById('sign_builder_form').reset();
        $('#builder_material').trigger('change'); // force the displayed price to update
        $('.toggleable-block').hide();
        update_layers();
        document.getElementById('existing_or_scratch_modal').style.display = 'block';
    });

    /* Deleting an individual layer */
    // display prompt
    $(document).on('click', '.delete_layer', function (ev) {
        var layer_id = $(this).parents('li').data('id');
        ev.preventDefault();
        show_delete_modal(layers[layer_id]);
    });

    function show_delete_modal(layer) {
        var preview_area = document.getElementById('delete_layer_preview');
        if (layer.type == 'image') {
            preview_area.innerHTML = '';
            $(preview_area).css('background-image', 'url(\'' + layer.src + '\')');
        }
        else {
            preview_area.innerHTML = layer.text;
            $(preview_area).css('background-image', '');
        }

        $('#delete_layer_modal').show_modal();
    }

    // delete the layer and change the selected layer to the last one added
    $('#delete_layer_button').on('click', function () {
        $('.toggleable-block').hide();
        layers.splice(selected_layer, 1);
        update_layers();
        draw_layer_list();
        selected_layer = layers.length - 1;
    });

    /*
     * ****************************************
     * Canvas altering functions
     * ****************************************
     */

    // Update the canvas dimensions
    function update_dimensions() {
        var canvas = $('#builder_canvas');
        var units = $('#builder_units').find(':selected');
        var coeff = units.data('coeff');
        var gap = units.data('gap'); // increment
        var width = document.getElementById('builder_width').value;
        var height = document.getElementById('builder_height').value;
        var ratio = width / height;
        var h_items = width / gap;
        var v_items = height / gap;
        var h_ruler = $('.ruler-horizontal').html('');
        var v_ruler = $('.ruler-vertical').html('');
        var item_size = canvas.width() / h_items;

        // Update canvas dimensions
        canvas.attr('height', canvas.attr('width') / ratio);
        v_ruler.css('height', canvas.attr('width') / ratio);

        // Keep record the new ratio
        document.getElementById('builder_ratio').value = width / height;

        // Redraw rulers, with necessary amount of markers, with correct sizes
        for (var i = 1; i < h_items + 2; i++) {
            h_ruler.append('<li style="width:' + item_size + 'px;">' + (i * gap) + '</li>');
        }
        for (i = 1; i < v_items + 2; i++) {
            v_ruler.append('<li style="height:' + item_size + 'px;padding-top:' + item_size + 'px;">' + (i * gap) + '</li>');
        }

        // Scale guidelines. Guideline gap = 5 mm
        var line_gap = 5 * canvas.width() / (width * coeff);
        $('.bleed_line').css('border-width', line_gap + 'px').css('width', canvas.width() + 'px').css('height', canvas.height() + 'px');
        $('.safe_line')
            .css('width', (canvas.width() - line_gap * 4 + 1) + 'px').css('left', (line_gap * 2 + 1) + 'px')
            .css('height', (canvas.height() - line_gap * 4    ) + 'px').css('top', (line_gap * 2 + 1) + 'px');

        // Keep record of the new guideline gap
        margin = Number($('#safe_line').css('left').replace('px', ''));


        update_layers();
    }

    // When a size preset (A4, A3 etc.), is chosen, set its dimensions as the width and height
    // Only allow manual width and height entries for "custom"
    $('#builder_size, #builder_orientation input').on('change', function () {
        var size = $('#builder_size').find(':selected');
        var width_input = $('#builder_width');
        var height_input = $('#builder_height');
        var orientation = document.getElementById('builder_orientation').querySelector(':checked').value;

        if (size.val() != 'custom' && size.val() != '') {
            if (document.getElementById('builder_dimensions').getAttribute('data-always_show') == 0) {
                $('#builder_dimensions, #builder_dimensions_labels').fadeOut();
            }
            var coeff = $('#builder_units').find(':selected').data('coeff');
            var width = (orientation != 'landscape') ? size.data('width') : size.data('height');
            var height = (orientation != 'landscape') ? size.data('height') : size.data('width');

            width_input.val(width / coeff).prop('readonly', true);
            height_input.val(height / coeff).prop('readonly', true);
            $('#lock_ratio').prop('disabled', true);
            update_dimensions();
        }
        else {
            $('#builder_dimensions, #builder_dimensions_labels').fadeIn();
            var width_element = document.getElementById('builder_width');
            $(width_element).prop('readonly', false);
            width_element.focus();
            $('#builder_height').prop('readonly', false);
            $('#lock_ratio').prop('disabled', false);

            if ((parseInt(width_input.val()) > parseInt(height_input.val()) && orientation == 'portrait') || (parseInt(width_input.val()) < parseInt(height_input.val()) && orientation == 'landscape')) {
                var temp = width_input.val();
                width_input.val(height_input.val());
                height_input.val(temp);
                update_dimensions();
            }
        }
    });

    $('.dimension_input').on('change', function () {
        var width = document.getElementById('builder_width').value;
        var height = document.getElementById('builder_height').value;
        var orientation = document.getElementById('builder_orientation').querySelector(':checked').value;
        // if the width is less than the height, ensure the orientation is portrait and vice versa
        if (width < height && orientation == 'landscape') $('#builder_orientation_portrait').parents('label').click();
        if (width > height && orientation == 'portrait') $('#builder_orientation_landscape').parents('label').click();
        update_dimensions();
    });

    function validate_area() {
        var valid = true;
        var min_area = document.getElementById('sb_min_area').value;
        var max_area = document.getElementById('sb_max_area').value;
        if (min_area != '' && max_area != '') {
            var unit_input = document.getElementById('builder_units')[document.getElementById('builder_units').selectedIndex];
            var coefficient = unit_input.getAttribute('data-coeff');
            var unit = unit_input.value;
            var width = document.getElementById('builder_width').value * coefficient;
            var height = document.getElementById('builder_height').value * coefficient;
            var area = Math.round(width * height * 1000) / 1000; // remove tiny rounding errors
            var message = '';

            if (area < min_area) message = 'Sign is too small. The minimum area is ' + Math.round(min_area / coefficient / coefficient * 100) / 100 + ' ' + unit + '&sup2;';
            if (area > max_area) message = 'Sign is too big. The maximum area is ' + Math.round(max_area / coefficient / coefficient * 100) / 100 + ' ' + unit + '&sup2;';

            if (message != '') {
                valid = false;
                display_error('<p>' + message + '</p>');
            }
        }

        return valid;
    }

    $('#builder_units').on('change', function () {
        var units = this.value;
        var width_box = document.getElementById('builder_width');
        var height_box = document.getElementById('builder_height');
        var prev_units_box = document.getElementById('builder_previous_units');
        var prev_units = prev_units_box.value;
        var old_coeff = $(this).find('[value=' + prev_units + ']').data('coeff');
        var new_coeff = $(this).find(':selected').data('coeff');

        prev_units_box.value = units;
        // "* old_coeff" converts old unit to mm
        // "/ new_coeff" converts mm to new unit
        width_box.value = width_box.value * old_coeff / new_coeff;
        height_box.value = height_box.value * old_coeff / new_coeff;
        update_dimensions();

        document.getElementById('preview_units').innerHTML = this.options[this.selectedIndex].getAttribute('data-name');
    });

    // Canvas background colour
    $('#builder_background_color').on('change', function () {
        $(this).find('\+ .picker_label').css('background-color', this.value);
        update_layers();
    });

    // Show / hide delete icons
    $('#show_delete_icons').on('change', function () {
        draw_layers();
    });

    /** Colour picker **/
    var custom_color_link = $('#custom_color_link');

    // Add colour codes as title text
    $('.color_palette').find('td[style]:not(.transparent_option)').each(function () {
        var color = $(this).css('background-color');
        this.setAttribute('title', color);
    });

    // Dismiss colour palette when clicked away from
    $(window).on('click', function (ev) {
        ev.stopPropagation();
        if (document.getElementsByClassName('dropper_mode').length == 0) {
            $('#color_palette').hide();
        }
    });
    $('.color_picker, #color_palette').on('click', function (ev) {
        ev.stopPropagation();
    });

    // Show / hide the colour list, when the colour picker is clicked and place it after the relevant picker
    $('.color_picker .picker_label').on('click', function () {
        $("#color_palette").show().insertAfter(this);
    });

    // Set colour, when a colour from the palette is clicked
    $('#color_palette').on('click touchup', 'tbody td[style]:not([colspan])', function () {
        var color = ($(this).hasClass('transparent_option')) ? 'transparent' : $(this).css('background-color');
        var picker = $(this).parents('.color_picker');
        picker.find('.color_value').val(color).trigger('change');
        $('#color_palette').hide();
    });

    // make spectrum appear when "custom" is clicked
    custom_color_link.on('click touchup', function (ev) {
        ev.preventDefault();
        $(this).find('input').spectrum();
        $(this).find('.sp-replacer').click();
        $('.sp-container').appendTo(document.getElementById('color_palette'));

        // Custom colour picker dismissed when the colour is clicked
        $('.sp-dragger').on('mouseup touchend', function () {
            $('.sp-choose').click();
        });
    });

    // put value from spectrum into empty custom colour cell
    custom_color_link.find('input').on('change', function () {
        var custom_palette = $('.custom_palette');
        // Create a new row, if necessary
        if (custom_palette.find('td:not([style])').length == 0) {
            custom_palette.append('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
        }

        var color = custom_color_link.find('.sp-preview-inner').css('background-color');
        custom_palette.find('td:not([style])').first().css('background-color', color);
    });

    // Dropper tool
    $('#dropper_tool').on('mousedown touchdown', function (ev) {
        ev.preventDefault();
        var cover = $('<div class="gray_out"></div>');
        $('#builder_canvas_wrapper, #builder_canvas').addClass('dropper_mode');
        $('#builder_canvas').before(cover);
    });
    $(document).on('gray_out', 'mouseup touchup', function () {
        $(this).remove();
        $('.dropper_mode').removeClass('dropper_mode');
    });

    $("#sign_builder_form").find("select[class*='validate[required]']").on('change', function () {
        $.post('/frontend/products/matrix_price', {option1: $("#builder_size").val(), option2: $("#builder_material").val(), product_id: $("#id").val()}, function (data) {
            $("#final_price").val(data);
            if (data == "") {
                data = 0.00;
            }
            var current_price = parseFloat($("#final_price").data('product_price'));
            data = parseFloat(data);
            $("#static_price").text((current_price + data).toFixed(2));
            $('#unit_price').text( parseFloat($("#static_price").text()) );
            $(".full_price").text( parseFloat($("#static_price").text()) );
        });
    });

    $('#builder_material').on('change', function () {
        var tooltip_icon = $(this).find(' .tooltip_icon');
        var selected = $(this).find(':selected');
        if ($(this).val() != '' && selected.data('description') != '') {
            var description = selected.data('description');
            tooltip_icon.attr('title', description);
            tooltip_icon.show();
        }
        else {
            tooltip_icon.attr('title', '');
            tooltip_icon.hide();
        }
    });
    $('#material_description_link').on('click', function (ev) {
        ev.preventDefault();
        $('#material_description_modal').show_modal();
    });

    // Show lamination type dropdown if "Yes" is selected for "Laminate".
    // Hide if "No" is selected
    $('#builder_laminate').find('input').on('change', function () {
        document.getElementById('builder_lamination_type_wrapper').style.display = (document.querySelector('#builder_laminate input:checked').value == 1) ? 'block' : 'none';
    });

    $('#purchase_button, #add_to_cart_button').on('click', function (ev) {
        button = $(this);
        ev.preventDefault();
        if (validate_form()) {
            var form = $("#sign_builder_form");
            form.validationEngine();

            var delete_icons_box = document.getElementById('show_delete_icons');
            var show_delete_value = delete_icons_box.checked;
            $(delete_icons_box).prop('checked', false).trigger('change');

            setTimeout(function () {
                sign_thumbnail = document.getElementById('builder_canvas').toDataURL('image/png');
                document.getElementById('checkout_complete_sign_preview_image').src = sign_thumbnail;
                $(delete_icons_box).prop('checked', show_delete_value);
                $('#checkout_preview_modal').show_modal();
                prepare_for_print(false);

            }, 800);
        }
    });

    $('#preview_pdf').on('click', function () {
        prepare_for_print(true);
    });

    // Get the data URL for a 300 dpi image
    function prepare_for_print(show_pdf)
    {
        if (validate_form())
        {
            // Switch the selected canvas to a bigger off-screen canvas
            // The layers are to be drawn blown up on this canvas
            canvas = document.getElementById('print_canvas');
            context = canvas.getContext('2d');

            var start = new Date().getTime();

            // keep record of the layers and canvas original (screen) sizes
            var original_layers = [];
            for (var i = 0; i < layers.length; i++) {
                original_layers.push(jQuery.extend({}, layers[i]));
                original_layers[i].text_styles = jQuery.extend({}, layers[i].text_styles);
            }
            canvas.width = document.getElementById('builder_canvas').width;
            canvas.height = document.getElementById('builder_canvas').height;

            // Convert the dimensions to inches and multiply by 300 to get a 300dpi canvas
            var to_mm_coeff = $('#builder_units').find(':selected').data('coeff');
            var page_width  = document.getElementById('builder_width' ).value * to_mm_coeff / 25.4 * 300;
            var page_height = document.getElementById('builder_height').value * to_mm_coeff / 25.4 * 300;

            var ratio = page_width / canvas.width;
            var font_size;
            var size = layers.length;
            for (i = 0; i < size; i++)
            {
                layers[i].set_width(layers[i].width * ratio);
                layers[i].set_height(layers[i].height * ratio);
                layers[i].set_x(layers[i].x * ratio);
                layers[i].set_y(layers[i].y * ratio);
                layers[i].set_border_width(layers[i].border_width * ratio);
                if (layers[i].type == 'text')
                {
                    font_size = Number(layers[i].text_styles['font-size'].replace('pt', ''));
                    layers[i].set_text_style('font-size', font_size * ratio + 'pt');
                    layers[i].set_text_dimensions();
                }
            }

            // Draw the resized layers on the resized canvas and get the data URL of the image
            /*
            canvas.width = page_width;
            canvas.height = page_height;
            var show_delete_icons = document.getElementById('show_delete_icons').checked;
            document.getElementById('show_delete_icons').checked = false;
            draw_layers();


            // Draw border, with corner crosshairs 5 mm from the edges
            var bleedline_margin = document.getElementById('sign_builder_bleedline_margin').value;
            if (bleedline_margin > 0) {
                var gap = bleedline_margin * canvas.width / $('#builder_width').val() * to_mm_coeff;

                // top
                context.moveTo(gap / 2, gap - 1);
                context.lineTo(canvas.width - gap / 2, gap - 1);
                // right
                context.moveTo(canvas.width - gap + 1, gap / 2);
                context.lineTo(canvas.width - gap + 1, canvas.height - gap / 2);
                // bottom
                context.moveTo(gap / 2, canvas.height - gap + 1);
                context.lineTo(canvas.width - gap / 2, canvas.height - gap + 1);
                // left
                context.moveTo(gap - 1, gap / 2);
                context.lineTo(gap - 1, canvas.height - gap / 2);
                context.stroke();
            }

            // If there is a transparent background, change it to white for printing
            var background_color_input = document.getElementById('builder_background_color');
            if (background_color_input.value == 'transparent' || background_color_input.value == '') {
                context.globalCompositeOperation = "destination-over"; // set to draw behind current content
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, canvas.width, canvas.height);   // draw white rectangle
            }

            var src         = canvas.toDataURL('image/jpeg');
            */
            var src         = document.getElementById('builder_canvas').toDataURL('image/png');

            var pdf_size    = $("#builder_size").find(":selected");
            var size_name   = pdf_size.text().split(' (')[0];
            var pdf_width   = pdf_size.data('width');
            var pdf_height  = pdf_size.data('height');
            var width       = document.getElementById('builder_width').value;
            var height      = document.getElementById('builder_height').value;
            var orientation = document.getElementById('builder_orientation').querySelector(':checked').value;

            if (pdf_width == 0)
            {
                var coeff  = $('#builder_units').find(':selected').attr('data-coeff');
                pdf_width  = ((height > width) ? width  : height) * coeff;
                pdf_height = ((height > width) ? height : width ) * coeff;
            }

            $.post('/frontend/products/add_canvas', {
                id:               $("#id").val(),
                canvas_size:      size_name,
                width:            pdf_width,
                height:           pdf_height,
                layers:           src,
                layers_obj:       JSON.stringify(layers),
                background_color: document.getElementById('builder_background_color').value,
                orientation:      orientation
            },function (data)
            {
                data = $.parseJSON(data);
                if (data.error)
                {
                    $('#checkout_preview_modal').hide_modal();
                    display_error(data.error);
                    document.getElementById('modal').style.display = 'none';
                }
                else
                {
                    custom_timestamp = data.timestamp;
                    layers = original_layers;
                    // document.getElementById('show_delete_icons').checked = show_delete_icons;
                    // Revert to using the main canvas
                    canvas = document.getElementById('builder_canvas');
                    context = canvas.getContext('2d');
                    draw_layers();
                    var end = new Date().getTime();
                    var time = end - start;
                    console.log('Execution time: ' + time);
                    document.getElementById('modal').style.display = 'none';
                    if (show_pdf)
                    {
                        window.location.href = document.getElementById('get_pdf').getAttribute('href');
                        document.getElementById('modal').style.display = 'none';
                    }
                    if (document.activeElement.getAttribute('id') == "add_to_cart_button")
                    {
                        var delete_icons_box = document.getElementById('show_delete_icons');
                        var show_delete_value = delete_icons_box.checked;
                        delete_icons_box.checked = false;
                        $(delete_icons_box).trigger('change');
                        document.getElementById('checkout_complete_sign_preview_image').src = document.getElementById('builder_canvas').toDataURL('image/png');
                        delete_icons_box.checked = show_delete_value;
                        $('#checkout_preview_modal').show_modal();
                        $("#purchase_complete_dialog").show();
                        $("#preview_sign_dialog").hide();
                    }

                    if ($(button).attr('id') == 'purchase_button')
                    {
                        validateQty_and_checkout(document.getElementById('id').value, $('#builder_quantity').val(), getOptions());
                    }
                    else
                    {
                        validateQty(document.getElementById('id').value, $('#builder_quantity').val(), getOptions());
                    }
                }
            }).fail(function ()
                {
                    display_error('<p>Failed to generate PDF.</p>');
                    document.getElementById('modal').style.display = 'none';
                });
        }
        else
        {
            document.getElementById('modal').style.display = 'none';
        }
    }

    /*
     * ****************************************
     * Validation
     * ****************************************
     */
    function validate_form() {
        if (!validate_size()) {
            // var step  = document.querySelector('[data-pane="size_editor"]').getAttribute('data-step');
            $('#complete_step_1_modal').show_modal();
            return false;
        }
        else if (!validate_material() || !validate_background()) {
            // var step  = document.querySelector('[data-pane="material_editor"]').getAttribute('data-step');
            $('#complete_step_2_modal').show_modal();
            return false;
        }
        else if (!validate_finish()) {
            // var step  = document.querySelector('[data-pane="finish_editor"]').getAttribute('data-step');
            $('#complete_finish_step_modal').show_modal();
            return false
        }
        else {
            return (validate_area());
        }
    }

    function validate_size() {
        if (cms_editor)
            return true;
        else
            return (document.getElementById('builder_size').value != '' && document.getElementById('builder_width').value != '' && document.getElementById('builder_height').value != '');
    }

    function validate_material() {
        if (cms_editor)
            return true;
        else
            return (document.getElementById('builder_material').value != '');
    }

    function validate_background() {
        if (cms_editor)
            return true;
        else
            return (document.getElementById('builder_background_color').value != '');
    }

    function validate_finish() {
        if (cms_editor || !document.getElementById('builder_laminate')) {
            return true;
        }
        else {
            var laminate = document.getElementById('builder_laminate').querySelector(':checked').value;
            var lamination_type = document.getElementById('builder_lamination_type').value;

            return (laminate == 0 || (laminate = 1 && lamination_type != ''));
        }
    }

    function display_error(message) {
        document.getElementById('error_message_area').innerHTML = message;
        $('#error_message_modal').show_modal();
    }

    function additional_options() {
        var options = [];
        if (document.getElementById('finish_editor')) {
            if ($("#builder_laminate label.selected input").val() == 1) {
                options.push({laminate: $("#builder_lamination_type").val()});
            }
            else {
                options.push({laminate: 'None'});
            }

            if ($("#builder_adhesive label.selected input").val() == 1) {
                options.push({adhesive: 'Yes'});
            }
            else {
                options.push({adhesive: 'None'});
            }
        }

        return options;
    }

    $(document).on('click', '#add_to_cart_warning', function () {
        if (validate_form()) {
            $("#confirm_continue_add_to_cart").show_modal();
        }
    });

    $(document).on('click', '#purchase_button_warning', function () {
        if (validate_form()) {
            $("#confirm_continue_buy_now").show_modal();
        }
    });

    $.fn.show_modal = function () {
        $(this).show();
        $('body').css('overflow', 'hidden');
    };
    $.fn.hide_modal = function () {
        $(this).fadeOut();
        $('body').css('overflow', '');
    };

    window.onresize = function () {
        update_dimensions();
    }

}

/*
 =====================================================================
 Old code below this line needs removal / integration into the above
 =====================================================================
 */


$('#sign_builder_wrapper').find('input[type="text"], input[type="number"]').keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
    }
});

$('#builder_category_list').on('change', function () {
    fetch_images();
});

// drag and drop upload
function prepare_drag_and_drop() {
    var drop_area = document.getElementById('builder_drop_upload'),
        dnd_supported = 'draggable' in document.createElement('span');

    if (dnd_supported) {
        drop_area.ondragover = function () {
            $(this).addClass('hover');
            return false;
        };
        drop_area.ondragend = function () {
            $(this).removeClass('hover');
            return false;
        };
        drop_area.ondrop = function (ev) {
            ev.preventDefault();
            $(this).removeClass('hover');
            var files = ev.dataTransfer.files;
            var file = files[files.length - 1];

            if (validate_upload(file)) {
                var reader = new FileReader();
                reader.onload = function (ev2) {
                    $('#builder_drop_upload_file').val(ev2.target.result).change();
                };
                reader.readAsDataURL(file);
            }

        }
    }
}

function validate_upload(file) {
    var error_zone = document.getElementById('upload_error_message');
    error_zone.innerHTML = '';
    var accepted_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
    var accepted = (accepted_types.indexOf(file.type) >= 0);
    if (!accepted) {
        error_zone.innerHTML = 'Unsupported file type.';
    }
    return accepted;
}

$('#builder_image_search').on('keyup', function () {
    fetch_images();
});

$('#browse_images_btn').on('click', function () {
    $('#browse_images_modal').show_modal();
});
$('#upload_image_btn').on('click', function () {
    $('#upload_image_modal').show_modal();
});
$('.sb-modal-dismiss').on('click', function (ev) {
    ev.preventDefault();
    $(this).parents('.sb-modal-overlay').hide_modal();
});

$('.radio-toggle label').click(function () {
    $(this).parent().find('.selected').removeClass('selected');
    $(this).addClass('selected');
});

// Add numbers before, after and above sliders to show min, max and current values
$('input.slider').each(function () {
    var $this = $(this);
    var unit = (typeof $this.data('unit') == 'undefined') ? '%' : $this.data('unit');
    $this.wrap('<div class="slider_wrapper"></div>');
    $this.before('<div class="slider_indicator">' + this.value + unit + '</div><span class="slider_min"></span>');
    $this.after('<span class="slider_max"></span>');

    var wrapper = $this.parents('.slider_wrapper');
    var indicator = wrapper.find('.slider_indicator');
    indicator.html(this.value + unit);
    wrapper.find('.slider_min').html(this.getAttribute('min'));
    wrapper.find('.slider_max').html(this.getAttribute('max'));
    this.oninput = function () {
        indicator.html(this.value + unit);
    }
});

// Replace file upload buttons, with standard buttons, which are easier to customise
$('input[type="file"].upload_button').each(function () {
    var $this = $(this);
    $this.wrap('<div class="upload_button_wrapper"></div>');
    $this.before('<button type="button" class="sb-button-plain pseudo_upload_button">Upload File</div>');
    $this.hide();
});
$(document).on('click', '.pseudo_upload_button', function () {
    $(this).parent().find('.upload_button').click();
});

function fetch_images() {
    var cat_id = document.getElementById('builder_category_list').value;
    var term = document.getElementById('builder_image_search').value;
    $('#available_images').load('/frontend/products/available_images/' + cat_id + '?term=' + term);
}

// tooltips
// http://osvaldas.info/elegant-css-and-jquery-tooltip-responsive-mobile-friendly
$(function () {
    var e = $(".tooltip_icon"), t = false, n = false, r = false;
    e.bind("mouseenter", function () {
        t = $(this);
        var e = t.attr("title");
        n = $('<div class="tooltip"></div>');
        t.removeAttr("title");
        n.css("opacity", 0).html(e).appendTo("body");
        var r = function () {
            if ($(window).width() < n.outerWidth() * 1.5) n.css("max-width", $(window).width() / 2);
            else n.css("max-width", 340);
            var e = t.offset().left + t.outerWidth() / 2 - n.outerWidth() / 2, r = t.offset().top - n.outerHeight() - 20;
            if (e < 0) {
                e = t.offset().left + t.outerWidth() / 2 - 20;
                n.addClass("left")
            }
            else n.removeClass("left");
            if (e + n.outerWidth() > $(window).width()) {
                e = t.offset().left - n.outerWidth() + t.outerWidth() / 2 + 20;
                n.addClass("right")
            }
            else n.removeClass("right");
            if (r < 0) {
                r = t.offset().top + t.outerHeight();
                n.addClass("top")
            }
            else n.removeClass("top");
            n.css(
                {
                    left: e,
                    top: r
                }).animate(
                {
                    top: "+=10",
                    opacity: 1
                }, 50)
        };
        r();
        $(window).resize(r);
        var i = function () {
            n.animate(
                {
                    top: "-=10",
                    opacity: 0
                }, 50, function () {
                    $(this).remove()
                });
            t.attr("title", e)
        };
        t.bind("mouseleave", i);
        n.bind("click", i)
    })
});


/*
 ***************************
 * Converting SVGs to PNG
 * slightly modified version of https://github.com/sampumon/SVG.toDataURL
 ***************************
 */

$.prototype.svg_to_data_url = function(type, options) {
	var _svg = this[0];

	function debug(s) {
		console.log("SVG.toDataURL:", s);
	}

	function exportSVG() {
		var svg_xml = XMLSerialize(_svg);
		var svg_dataurl = base64dataURLencode(svg_xml);
		debug(type + " length: " + svg_dataurl.length);

		// NOTE double data carrier
		if (options.callback) options.callback(svg_dataurl);
		return svg_dataurl;
	}

	function XMLSerialize(svg) {

		// quick-n-serialize an SVG dom, needed for IE9 where there's no XMLSerializer nor SVG.xml
		// s: SVG dom, which is the <svg> elemennt
		function XMLSerializerForIE(s) {
			var out = "";

			out += "<" + s.nodeName;
			for (var n = 0; n < s.attributes.length; n++) {
				out += " " + s.attributes[n].name + "=" + "'" + s.attributes[n].value + "'";
			}

			if (s.hasChildNodes()) {
				out += ">\n";

				for (var n = 0; n < s.childNodes.length; n++) {
					out += XMLSerializerForIE(s.childNodes[n]);
				}

				out += "</" + s.nodeName + ">" + "\n";

			} else out += " />\n";

			return out;
		}


		if (window.XMLSerializer) {
			debug("using standard XMLSerializer.serializeToString")
			return (new XMLSerializer()).serializeToString(svg);
		} else {
			debug("using custom XMLSerializerForIE")
			return XMLSerializerForIE(svg);
		}

	}

	function base64dataURLencode(s) {
		var b64 = "data:image/svg+xml;base64,";

		// https://developer.mozilla.org/en/DOM/window.btoa
		if (window.btoa) {
			debug("using window.btoa for base64 encoding");
			b64 += btoa(s);
		} else {
			debug("using custom base64 encoder");
			b64 += Base64.encode(s);
		}

		return b64;
	}

	function exportImage(type) {
		var canvas = document.createElement("canvas");
		var ctx = canvas.getContext('2d');

		// TODO: if (options.keepOutsideViewport), do some translation magic?

		var svg_img = new Image();
		var svg_xml = XMLSerialize(_svg);
		svg_img.src = base64dataURLencode(svg_xml);

		svg_img.onload = function() {
			debug("exported image size: " + [svg_img.width, svg_img.height])
			canvas.width = svg_img.width;
			canvas.height = svg_img.height;
			ctx.drawImage(svg_img, 0, 0);

			// SECURITY_ERR WILL HAPPEN NOW
			var png_dataurl = canvas.toDataURL(type);
			debug(type + " length: " + png_dataurl.length);

			if (options.callback) options.callback( png_dataurl );
			else debug("WARNING: no callback set, so nothing happens.");
		}

		svg_img.onerror = function() {
			console.log(
				"Can't export! Maybe your browser doesn't support " +
					"SVG in img element or SVG input for Canvas drawImage?\n" +
					"http://en.wikipedia.org/wiki/SVG#Native_support"
			);
		}

		// NOTE: will not return anything
	}

	function exportImageCanvg(type) {
		var canvas = document.createElement("canvas");
		var ctx = canvas.getContext('2d');
		var svg_xml = XMLSerialize(_svg);

		// NOTE: canvg gets the SVG element dimensions incorrectly if not specified as attributes
		//debug("detected svg dimensions " + [_svg.clientWidth, _svg.clientHeight])
		//debug("canvas dimensions " + [canvas.width, canvas.height])

		var keepBB = options.keepOutsideViewport;
		if (keepBB) var bb = _svg.getBBox();

		// NOTE: this canvg call is synchronous and blocks
		canvg(canvas, svg_xml, {
			ignoreMouse: true, ignoreAnimation: true,
			offsetX: keepBB ? -bb.x : undefined,
			offsetY: keepBB ? -bb.y : undefined,
			scaleWidth: keepBB ? bb.width+bb.x : undefined,
			scaleHeight: keepBB ? bb.height+bb.y : undefined,
			renderCallback: function() {
				debug("exported image dimensions " + [canvas.width, canvas.height]);
				var png_dataurl = canvas.toDataURL(type);
				debug(type + " length: " + png_dataurl.length);

				if (options.callback) options.callback( png_dataurl );
			}
		});

		// NOTE: return in addition to callback
		return canvas.toDataURL(type);
	}

	// BEGIN MAIN

	if (!type) type = "image/svg+xml";
	if (!options) options = {};

	if (options.keepNonSafe) debug("NOTE: keepNonSafe is NOT supported and will be ignored!");
	if (options.keepOutsideViewport) debug("NOTE: keepOutsideViewport is only supported with canvg exporter.");

	switch (type) {
		case "image/svg+xml":
			return exportSVG();
			break;

		case "image/png":
		case "image/jpeg":

			if (!options.renderer) {
				if (window.canvg) options.renderer = "canvg";
				else options.renderer="native";
			}

			switch (options.renderer) {
				case "canvg":
					debug("using canvg renderer for png export");
					return exportImageCanvg(type);
					break;

				case "native":
					debug("using native renderer for png export. THIS MIGHT FAIL.");
					return exportImage(type);
					break;

				default:
					debug("unknown png renderer given, doing noting (" + options.renderer + ")");
			}

			break;

		default:
			debug("Sorry! Exporting as '" + type + "' is not supported!")
	}
}
