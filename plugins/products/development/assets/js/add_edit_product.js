var product_options_table = $("#product_stock_levels").dataTable();
var product_youtube_table = $("#youtube_table").dataTable();
$(document).ready(function() {
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });


    // CKEditor Configuration
    CKEDITOR.replace('brief_description', {

            // Toolbar settings
            toolbar :
                [
                    ['Source'],
                    ['Format', 'Font', 'FontSize'],
                    ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'],
                    ['NumberedList', 'BulletedList'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['PasteFromWord']
                ],

            // Editor width
            width   : '538px'

        }
    );
    CKEDITOR.replace('description', {

            // Toolbar settings
            toolbar :
                [
                    ['Source'],
                    ['Format', 'Font', 'FontSize'],
                    ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'],
                    ['NumberedList', 'BulletedList'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['PasteFromWord']
                ],

            // Editor width
            width   : '538px'

        }
    );
    CKEDITOR.replace('footer_editor', {

            // Toolbar settings
            toolbar :
                [
                    ['Source'],
                    ['Format', 'Font', 'FontSize'],
                    ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'],
                    ['NumberedList', 'BulletedList'],
                    ['Link', 'Unlink', 'Anchor'],
                    ['PasteFromWord']
                ],

            // Editor width
            width   : '538px'

        }
    );

    if ($('#builder').val() == 1)
    {
        $('#sign_builder_list').show();
    }
    else
    {
        $('#sign_builder_list').hide();
    }
    $(document).on('change','#builder',function()
    {
        var builder_option = $(this).val();
        if (builder_option == 1)
        {
            $('#sign_builder_list').show();
        }
        else
        {
            $('#sign_builder_list').hide();
        }
    });

    $('#add_category_btn').click(function() {
        var table = 'categories_table';
        var item  = $('#product_categories').val();

        if (item != '' && ! is_item_in_rows_db(table, item) && item != 0 && item != 'undefined') {
            add_category(item);
        }
    });

    $('#add_image').click(function() {
        var table = 'images_table';
        var item  = $('#' + table).parent('').children('select').val();

        if (item != '' && ! is_item_in_rows_db(table, item)) {
            add_image(item);
        }
    });

	$('#add_document_btn').click(function()
	{
		var table = 'documents_table';
		var item  = $('#edit_product_add_documents_dropdown').val();

		if (item != '' && ! is_item_in_rows_db(table, item))
		{
			add_document(item);
		}
	});

    $('#add_option').click(function() {
        var table = 'options_table';
        var options = $('#edit_product_add_option_dropdown_id')[0];
        if (options.selectedIndex < 0) {
            return;
        }
        var item_id  = options.options[options.selectedIndex].value;
        var item  = options.options[options.selectedIndex].text;

        if (item != '' && ! is_item_in_rows_db(table, item)) {
            var option = { group_id: item_id, group: item , required: 1, is_stock: 0};

            add_option(option);
        }
    });

    $('#add_related_to').click(function() {
        var table = 'related_to_table';
        var item  = $('#edit_product_add_related_dropdown_id').val();
        $('#edit_product_add_related_dropdown').val("");

        if (item != '' && ! is_item_in_rows_db(table, item)) {
            AJAX.make_get_request('/admin/products/ajax_get_product/?id=' + item, add_related_to, null);
        }
    });

    $(document).on('click', '.remove-row', function() {
        remove_row_from_db($(this).parent('').attr('id'));
        var group = $(this).parent('tr').data('group');
        remove_group_options(group);
        $(this).parent('').remove();
    });

    $(document).on('click', '.toggle-required-option', function() {
        var i = $(this).children('i');

        if(i.hasClass('icon-ok'))
        {
            $(this).parent('tr').data('required', '0');

            i.removeClass('icon-ok'    );
            i.addClass   ('icon-remove');
        } else {
            $(this).parent('tr').data('required', '1');

            i.removeClass('icon-remove');
            i.addClass   ('icon-ok'    );
        }
    });

    $(document).on('click', '.toggle-publish-stock-option', function() {
        var i = $(this).children('i');

        if(i.hasClass('icon-ok'))
        {
            $(this).children('.option_publish').val(0);

            i.removeClass('icon-ok'    );
            i.addClass   ('icon-remove');
        } else {
            $(this).children('.option_publish').val(1);

            i.removeClass('icon-remove');
            i.addClass   ('icon-ok'    );
        }
    });

    $(document).on('click', '.toggle-stock-option', function() {
        var i = $(this).children('i');
        var parent = $(this).parent('tr');

        if(i.hasClass('icon-ok'))
        {
            $(this).parent('tr').data('is_stock', '0');
            var parent_group = $(this).parent('tr').data('data-group');
            i.removeClass('icon-ok'    );
            i.addClass   ('icon-remove');
            $('.options_list [data-group="'+parent_group+'"]').remove();
            var group = $(this).parent('tr').data('group');
            remove_group_options(group);
        }
        else
        {
            $(this).parent('tr').data('is_stock', '1');
            i.removeClass('icon-remove');
            i.addClass   ('icon-ok'    );
            $.post('/admin/products/get_stock_option_rows',{product_id: $("#id").val(),option_id:$(parent).data('group_id')},function(result){
                destroy_products_table();
                $("#product_stock_levels tbody").append(result);
                rebuild_products_table();
                refresh_prices();
            });
        }
    });

    $("#form_add_edit_product").validate({
        submitHandler: function(form) {
            $('#product_category_ids').val(generate_json_for_categories_table());
			$('#images'              ).val(generate_json_for_images_table    ());
			$('#product_documents'   ).val(generate_json_for_documents_table ());
            $('#options'             ).val(generate_json_for_options_table   ());
            $('#related_to'          ).val(generate_json_for_related_table   ());
            $("#stock_options"       ).val(generate_json_for_stock_options   ());
            $("#youtube_options").val(generate_json_for_youtube());
            form.submit();
        }
    });

    /*
     * When the URL title field is changed, automatically format the title
     * and display an inline error if the title is used by another product.
     */
    $('#edit_product_url_title').on('change', function()
    {
        this.value = format_url_title(this.value);

        var $url_input = $(this).parent();
        $url_input.removeClass('invalid');
        $('#title_error').remove();
        $.ajax({
            'url'        : '/admin/products/ajax_validate_url_title',
            'type'       : 'POST',
            'data'       : {
                'url_title'  : this.value,
                'id'         : document.querySelector('#form_add_edit_product #id').value
            },
            'dataType'   : 'json'
        }).success(function(result)
            {
                if (result['error'])
                {
                    $url_input.addClass('invalid');
                    $url_input.after('<div id="title_error" class="alert alert-error">'+result['message']+'</div>');
                }
            });
    });

    /*
     * Convert a title to a URL-friendly title
     */
    function format_url_title(title)
    {
        title = title.replace(/[-|_]/g, ' '); // hyphens, spaces and underscores are all treated as spaces
        title = title.replace(/[^\w\s]/g, ''); // remove non-alphanumeric non-space characters
        title = title.replace(/ +/g, '-'); // replace spaces with dashes
        title = title.toLowerCase(); // convert to lowercase
        return title;
    }

    $('[type="submit"]').on('click', function(ev)
    {
        var layers = document.getElementById('sign_builder_layers_input').value;
        if (layers != '[]' && layers != '')
        {
            ev.preventDefault();
            document.getElementById('show_delete_icons').checked = 0; // don't want delete "x" icons appearing in the image
            document.getElementById('sign_builder_data_url').value = document.getElementById('builder_canvas').toDataURL('image/png');
            $(this).submit();
        }
    });

    // Categories
    AJAX.make_get_request('/admin/products/ajax_get_product_media_base_location/', function(r) {
        media_base_location = r;
        var category_ids = ($('#product_category_ids').val() == '') ? {} : jQuery.parseJSON($('#product_category_ids').val());
        $(category_ids).each(function(i,v)
        {
            add_category(v);
        });
    }, null);

    // Images
    AJAX.make_get_request('/admin/products/ajax_get_product_media_base_location/', function(r) {
        var images = ($('#images').val() == '') ? {} : jQuery.parseJSON($('#images').val());

        media_base_location = r;

        $(images).each(function(i, v) {
            add_image(v);
        });
    }, null);

	// Documents
	var documents = ($('#product_documents').val() == '') ? {} : jQuery.parseJSON($('#product_documents').val());
	$(documents).each(function(i, v) {
		add_document(v);
	});

    // Options
    var options    = ($('#options').val() == '') ? {} : jQuery.parseJSON($('#options').val());

    $(options).each(function(i, v) {
        add_option(v);
        if(v.is_stock == '1')
        {
            add_stock_option(v.group_id, v);
        }
    });

    // Related To
    var related_to = ($('#related_to').val() == '') ? {} : jQuery.parseJSON($('#related_to').val());

    $(related_to).each(function(i, v) {
        AJAX.make_get_request('/admin/products/ajax_get_product/?id=' + v, add_related_to, null);
    });

    // Image editor
    var editing_image = false;
	$('#add_existing_image_button').add_existing_image_button('products', false);
	$('#add_edit_existing_image_button').add_existing_image_button('products');


    /*
    $('#upload_files_modal').on('shown.bs.tab', function()
    {
        var preset_selector = $('#upload_files_modal').find('#preset_selector');
        preset_selector.val(preset_selector.find('option[data-title="Products"]').val());
    });*/


    $(document).on('click', '#cropped_image_done_btn', function()
    {
        if (multi_upload_images == '') // "Upload Images" button does its own thing
        {
            if ( ! editing_image)
            {
                add_image($('#image_editor_filename').val());
            }
            else
            {
                editing_image = false;
            }
        }

    });

	// Add an image without using the editor.
	// This is also applied when adding an SVG. Vectors don't use the editor regardless of which option is chosen.
    $(document).on('click', '.svg_thumb, .browse-images-noeditor .image_thumb', function()
    {
        var filepath = this.getElementsByTagName('img')[0].src.split('/');
        add_image(filepath[filepath.length - 1]);
        $(this).parents('.modal').modal('hide');
    });

	$(document).on(':ib-browse-image-selected', '.image_thumb', function()
	{
		// Open the editor, unless it has the "...noeditor" class
		if ($(this).parents('.browse-images-noeditor').length == 0)
		{
			// Get the path to the uploaded image, which has no preset applied
			var src = this.querySelector('img').src.replace('/_thumbs_cms/', '/');


			// Open the image editor, using the chosen image and the products preset
			existing_image_editor(
				src,
				'products',
				function(image)
				{
					// Set the uploaded image as a product image
					var filename = (typeof image == 'object' && image.file) ? image.file : image;
					filename = filename.substring(filename.lastIndexOf('/') + 1);
					add_image(filename);

					$('#edit_image_modal').modal('hide');
				}
			);
		}
	});

    $('#images_table').on('click', 'img', function()
    {
        existing_image_editor(this.src);
        editing_image = true;
    });

    $(document).on(':ib-fileuploaded', '.upload_item', function()
    {
		// Get the path to the uploaded image, which has no preset applied
		var src = this.querySelector('img').src.replace('/_thumbs_cms/', '/');

		// Open the image editor, using the chosen image and the products preset
		existing_image_editor(
			src,
			'products',
			function(image)
			{
				// Run the add_image function on the now-edited image
				var filename = (typeof image == 'object' && image.file) ? image.file : image;
				filename = filename.substring(filename.lastIndexOf('/') + 1);
				add_image(filename);

				// Dismiss the editor
				$('#edit_image_modal').modal('hide');
			}
		);

    });

    $(document).on("click",".edit_stock",function(){
        $("#myModal .product_name").val($(this).parent('td').siblings('.product_name').text());
        $("#myModal .option_group").val($(this).parent('td').siblings('.option_group').text());
        $("#myModal .option_label").val($(this).parent('td').siblings('.option_label').text());
        $("#myModal .option_quantity").val($(this).parent('td').siblings('.option_quantity').text());
        $("#myModal .option_price").val($(this).parent('td').siblings('.option_price').text());
        $("#myModal .option_location").val($(this).parent('td').siblings('.option_location').text());
        $("#myModal .option_option_id").val($(this).closest('tr').data('option_id'));
    });

    $(".save_option").click(function(){
        $.post('/admin/products/save_option_details',{product_id: $("#myModal .option_product_id").val(),option_id: $("#myModal .option_option_id").val(),quantity:$("#myModal .option_quantity").val(),location:$("#myModal .option_location").val(),price: $("#myModal .option_price").val()},function(result){
        if(result == 'true')
        {

        }
        });
    });

    $(document).on("change",".option_price",function(){
        refresh_prices();
    });

    $("#price").change(function(){
        refresh_prices();
    });

    fill_option_values();

    $(document).on('change','.option_quantity',function(){
        refresh_prices();
        if($(this).val() > 0 && $(this).parent('td').siblings('.toggle-publish-stock-option').children('i').hasClass('icon-remove'))
        {
            $(this).parent('td').siblings('.toggle-publish-stock-option').click();
        }
        else if(($(this).val() == 0 || $(this).val() == '') && $(this).parent('td').siblings('.toggle-publish-stock-option').children('i').hasClass('icon-ok'))
        {
            $(this).parent('td').siblings('.toggle-publish-stock-option').click();
        }
    });

    $(".save_button").click(function(){
        $("#redirect").val($(this).data('redirect'));
    });

    $("#quantity_enabled").click(function(){
        if($(this).is(":checked"))
        {
            $("#quantity").prop('readonly',false);
        }
        else
        {
            $("#quantity").prop('readonly',true);
        }
    });

    $("#add_youtube_video_button").on('click',function(){
        $.post('/admin/products/get_youtube_video_id',{url:$("#youtube_video_url").val()},function(data){
            product_youtube_table.fnAddData([data,'<i class="icon-remove"></i>']);
            $("#youtube_video_url").val('');
        });
        $("#close_button").click();
    });

    $("#youtube_table").on('click','.icon-remove',function(){
        var target_row = $(this).closest("tr").get(0); // this line did the trick
        var index = product_youtube_table.fnGetPosition(target_row);
        product_youtube_table.fnDeleteRow(index);
    });
});

var multi_upload_images = '';

function multi_upload_and_add(i)
{
    if (i != 0 && i != multi_upload_images.length)
    {
        add_image($('#image_editor_filename').val());
    }

    if (i < multi_upload_images.length)
    {
        var filepath = $(multi_upload_images[i]).find('img')[0].src.replace('/_thumbs_cms/', '/');
        var filename = filepath.split('/').pop().replace('%20', '');
        var ext      = filename.split('.').pop();

        if ($('#title').val() != '') {
            filename = $('#title').val() + '.' + ext;

            $.ajax({
                'url': '/admin/media/ajax_get_filename_suggestion',
                'type': 'POST',
                'data': {
                    'name': $('#title').val(),
                    'ext': ext,
                    'directory': 'products'
                },
                'dataType': 'json'
            }).success(function (result) {
                filename = result;
                $('#image_editor_filename').val(filename);
                $('#edit_image_source').html(filepath);
                prepare_editor();
                $('#edit_image_modal').modal();

                $('#cropped_image_done_btn').attr('onclick', 'multi_upload_and_add('+i+'+1)');
            });
        }

    }
    else
    {
        multi_upload_images = '';
    }
}

/* Adding tags */
$(document).ready(function()
{
    var searchbar         = $('#tag_selector_autocomplete');
    var searchbar_wrapper = $('#tag_selector_autocomplete_label');
    searchbar.on('keyup', function()
    {
        var wrapper = $(this).parent();
        wrapper.find('.search-autocomplete').remove();
        $.ajax({
            url      : '/admin/products/ajax_get_tags_ac/',
            type     : 'post',
            data     : { like: this.value },
            dataType : 'json',
            async    : false
        }).done(function(results)
            {
                if (results.length > 0)
                {
                    var list = '<ul class="search-autocomplete">\n';
                    for (var i = 0; i < results.length; i++)
                    {
                        var item = results[i];
                        list += '<li data-id="'+item.id+'"><a href="#" class="ac-item">'+item.title+'</a></li>\n';
                    }
                    list += '</ul>';
                }
                wrapper.append(list);
            });
    });

    // Remove the autocomplete when it is clicked away from
    searchbar_wrapper.on('click', function(ev) {
        ev.stopPropagation();
    });
    $(document).on('click', function () {
        $('#tag_selector_autocomplete_label').find('.search-autocomplete').remove();
    });

    // Move from searchbar to first ac result on down arrow
    searchbar.on('keydown', function(ev)
    {
        if (ev.keyCode == 40) // down arrow
        {
            ev.preventDefault();
            var item = $($(this).find('\+ ul li a')[0]).focus();
            console.log(item);
        }
    });

    // Events for particular key presses on autocomplete items
    $(document).on('keydown', '.ac-item:focus', function(ev)
    {
        switch (ev.keyCode)
        {
            case 13: // enter key
                break;

            case 40: // down arrow - move down through the autocomplete items
                ev.preventDefault();
                var next = $(this).parent().next();
                if (next.find('a.ac-item').length != 0)
                {
                    next.find('a.ac-item').focus();
                }
                break;

            case 38: // up arrow - move up through the autocomplete items
                ev.preventDefault();
                var prev = $(this).parent().prev();
                if (prev.find('a.ac-item').length != 0)
                {
                    prev.find('a.ac-item').focus();
                }
                else // focus the searchbar if pressed on the top item
                {
                    searchbar.focus();
                }
                break;

            default:
                // if another key is pressed, refocus the searchbar and type it in there
                searchbar.focus();
                break;
        }
    });

    searchbar_wrapper.on('click', '.ac-item', function(ev)
    {
        ev.preventDefault();
        document.getElementById('tags_list').innerHTML += '<span class="label label-primary">'+this.innerHTML+
            ' <a href="#" class="remove_tag">&times;</a>'+
            '<input type="hidden" name="tag_ids[]" value="'+this.parentNode.getAttribute('data-id')+'" />'+
            '</span>';
    });

    $('#tags_list').on('click', '.remove_tag', function(ev)
    {
        ev.preventDefault();
        $(this).parents('.label').remove();
    });
});



var media_base_location;
var rows_db = [];

/**
 *
 * @param table_name
 * @param id
 * @returns {string}
 * @private
 */
function _generate_row_id(table_name, id) {
    return table_name + '-' + id;
}

/**
 *
 * @returns {string}
 * @private
 */
function _generate_tr_id() {
    return 'row-id-' + (rows_db.length);
}

/**
 *
 * @param row_id
 * @returns {boolean}
 * @private
 */
function _is_row_in_db(row_id) {
    return (rows_db.indexOf(row_id) != -1);
}

/**
 *
 * @param table_name
 * @param item_id
 * @returns {boolean}
 */
function is_item_in_rows_db(table_name, item_id) {
    return _is_row_in_db(_generate_row_id(table_name, item_id));
}

/**
 *
 * @param table_name
 * @param item_id
 * @returns {string}
 */
function add_item_to_rows_db(table_name, item_id) {
    var row_id = _generate_row_id(table_name, item_id);
    var tr_id  = (_is_row_in_db(row_id)) ? null : _generate_tr_id();

    if (tr_id != null) {
        rows_db.push(row_id);
    }

    return tr_id;
}

/**
 *
 * @param tr_id
 */
function remove_row_from_db(tr_id) {
    rows_db.splice((tr_id.split('row-id-'))[1], 1);
}

/**
 *
 * @param id
 */
function add_category(id) {
    if (id.length)
    {
        var table    = 'categories_table';
        var category = $('#product_categories').find('[value="'+id+'"]');
        var row      = '';
        var image    = (category.data('image') != '') ? '<img src="'+media_base_location+category.data('image')+'" alt="'+category.data('name')+'" />' : '';

        row += '<tr id="' + add_item_to_rows_db(table, category.data('name')) + '"data-id="' + category.val() + '">';
        row += '<td>'+category.val()+'</td>';
        row += '<td>'+image+'</td>';
        row += '<td>' + category.data('name') + '</td>';
        row += '<td class="remove-row"><i class="icon-remove"></i></td>';
        row += '</tr>';

        $('#'+table).append(row);
    }
}

/**
 *
 * @param file_name
 */
function add_image(file_name) {
    if (file_name.length)
    {
        var table = 'images_table';
        var row   = '';

        row += '<tr id="' + add_item_to_rows_db(table, file_name) + '"data-id="' + file_name + '">';
        if (file_name.split('.').pop() == 'svg')
        {
            row += '<td class="svg_thumb"><img src="' + media_base_location.replace('/_thumbs_cms/', '/') + file_name + '" alt="' + file_name + '"></td>';
        }
        else
        {
            row += '<td><img src="' + media_base_location + file_name + '" alt="' + file_name + '"></td>';
        }
        row += '<td>' + file_name + '</td>';
        row += '<td class="remove-row"><i class="icon-remove"></i></td>';
        row += '</tr>';

        $('#' + table).append(row);
    }
}
function add_document(file_name)
{
	if (file_name.length)
	{
		var table = 'documents_table';
		var row   = '';

		row += '<tr id="' + add_item_to_rows_db(table, file_name) + '"data-id="' + file_name + '">';
		row += '<td>' + file_name + '</td>';
		row += '<td class="remove-row"><a href="#" class="icon-remove"></a></td>';
		row += '</tr>';

		$('#'+table).append(row);
	}
}

/**
 *
 * @param option
 */
function add_option(option) {
    if (option.group_id)
    {
		$.ajax('/admin/products/ajax_get_group_options/'+option.group_id).done(function(results)
		{
			var option_values = JSON.parse(results);
			var values_string = '';
			for (var i = 0; i < option_values.length; i++)
			{
				values_string += option_values[i].label+', ';
			}
			values_string = values_string.replace(/(^\s*,)|(,\s*$)/g, ''); // Remove trailing comma and spaces
			var table  = 'options_table';
			var row    = '';

			row += '<tr id="' + add_item_to_rows_db(table, option.group_id) + '"data-group_id="' + option.group_id + '" data-required="' + option.required + '" data-is_stock="' + option.is_stock + '">';
			row += '<td class="product_name">'+$("#title").val()+'</td>';
			row += '<td class="options_table_group">' + option.group + '</td>';
			row += '<td class="option_values">'+values_string+'</td>';
			row += '<td class="toggle-stock-option">' + ((option.is_stock == '1') ? '<i class="icon-ok">' : '<i class="icon-remove"></i>') + '</td>';
			row += '<td class="toggle-required-option">' + ((option.required == '1') ? '<i class="icon-ok">' : '<i class="icon-remove"></i>') + '</td>';
			row += '<td class="remove-row"><i class="icon-remove"></i></td>';
			row += '</tr>';

			$('#' + table).append(row);
		});
    }
}

/**
 *
 * @param data
 */
function add_related_to(data) {
    var product = (data == '') ? {} : jQuery.parseJSON(data);
    if (product.id) {
        var table = 'related_to_table';
        var row = '';

        row += '<tr id="' + add_item_to_rows_db(table, product.id) + '"data-id="' + product.id + '">';
        row += '<td>' + product.id + '</td>';
        row += '<td>' + product.product_code + '</td>';
        row += '<td>' + product.title + '</td>';
        row += '<td>' + product.category + '</td>';
        row += '<td>' + product.price + '</td>';
        row += '<td class="remove-row"><i class="icon-remove"></i></td>';
        row += '</tr>';

        $('#' + table).append(row);
    }
}

/**
 * Generate a JSON with the data contained in the categories table.
 * @return {string}
 */
function generate_json_for_categories_table() {
    var data = [];

    $('#categories_table').children('tbody').children('tr').each(function() {
        data.push($(this).data('id'));
    });

    return JSON.stringify(data);
}

/**
 * Generate a JSON with the data contained in the images table.
 * @return {string}
 */
function generate_json_for_images_table() {
    var data = [];

    $('#images_table').children('tbody').children('tr').each(function() {
        data.push($(this).data('id'));
    });

    return JSON.stringify(data);
}

/**
 * Generate a JSON with the data contained in the documents table.
 * @return {string}
 */
function generate_json_for_documents_table() {
	var data = [];

	$('#documents_table').children('tbody').children('tr').each(function() {
		data.push($(this).data('id'));
	});

	return JSON.stringify(data);
}
/**
 * Generate a JSON with the data contained in the options table.
 * @return {string}
 */
function generate_json_for_options_table() {
    var data = [];
    $('#options_table').find('tbody tr:not(.stock_row)').each(function() {
		var $this = $(this);
        data.push({
			group_id        : $this.data('group_id'),
			option_group_id : $this.data('group_id'),
			group           : $this.find('.options_table_group').html(),
			required        : $this.data('required'),
			is_stock        : $this.data('is_stock')
		});
    });
    console.log(data);
    return JSON.stringify(data);
}

/**
 * Generate a JSON with the data contained in the related to table.
 * @return {string}
 */
function generate_json_for_related_table() {
    var data = [];

    $('#related_to_table').children('tbody').children('tr').each(function() {
        data.push($(this).data('id'));
    });

    return JSON.stringify(data);
}

function remove_group_options(group_id)
{
    destroy_products_table();
    $("#product_stock_levels tbody tr[data-group_id='"+group_id+"']").each(function(){
        $(this).remove();
    });
    rebuild_products_table();
}

function refresh_prices()
{
    $("#product_stock_levels").find("tbody tr").each(function(){
        if($(this).children('.product_name').text() == '')
        {
            $(this).children('.product_name').text($("#title").val());
        }
        if($(this).children('.product_category').text() == '' && $("#category_id").val() != '')
        {
            $(this).children('.product_category').text($("#category_id").find("option:selected").text());
        }
        if ($("#category_id").val() == '' && $("#id").val() != '')
        {
            $(this).children('.product_category').text('')
        }

        $(this).children(".final_price").text($("#price").val());
        if($(this).find("input.option_price").val() != '' && $(this).children(".final_price").text() != "undefined")
        {
            $(this).children(".final_price").text((parseFloat($("#price").val()) + parseFloat($(this).find("input.option_price").val())).toFixed(2));
        }
    });

    stock_check();
}

function generate_json_for_stock_options()
{
    var data = [];
    var rows = product_options_table.fnGetNodes();

    $(rows).each(function(){
        var checked = $(this).find('input.option_publish').val();
        if(checked == "1")
        {
            checked = 1;
        }
        else
        {
            checked = 0;
        }
        data.push({option_id:$(this).data('option_id'),product_id:$("#id").val(),quantity:$(this).find('input.option_quantity').val(),price:$(this).find('input.option_price').val(),location:$(this).find('.option_location').val(),publish: checked});
    });
    return JSON.stringify(data);
}

function generate_json_for_youtube()
{
    var data = [];
    var rows = product_youtube_table.fnGetNodes();

    $(rows).each(function(){
        var value = $(':nth-child(1)',this).text();
        console.log(value);
        data.push({video_id:value});
    });
    return JSON.stringify(data);
}

function add_stock_option(group_id)
{
    $.post('/admin/products/get_stock_option_rows',{product_id: $("#id").val(),option_id:group_id},function(result){
        destroy_products_table();
        $("#product_stock_levels").find("tbody").append(result);
        rebuild_products_table();
        refresh_prices();
    });
}

function destroy_products_table()
{
    $("#product_stock_levels").dataTable().fnDestroy();
}

function rebuild_products_table()
{
    $("#product_stock_levels").dataTable();
}

function stock_check()
{
    var online_stock = 0;
    var offline_stock = 0;
    $("#product_stock_levels tbody tr td select.option_location").each(function(){
        if($(this).val() == '1')
        {
            online_stock+=parseInt($(this).closest('tr').find('.option_quantity').val());
        }
        else
        {
            offline_stock+=parseInt($(this).closest('tr').find('.option_quantity').val());
        }
    });

    $(".stock_count .online_stock").val(online_stock);
    $(".stock_count .offline_stock").val(offline_stock);
}

function fill_option_values()
{
    $("#options_table tbody tr").each(function(){
        var parent = $(this);
        $.post('/admin/products/get_option_labels',{group_id:$(this).data('group_id')},function(result){
            $(parent).find('.option_values').text(result);
        });
    });
}

$(document).ready(function()
{
	// Server-side datatable for product reviews
	var $table = $('#edit-product-reviews-table');
	$table.ready(function()
	{
	        var ajax_source = '/admin/products/ajax_get_reviews_datatable/?product_id='+$('#id').val();
            var settings = {
                "sPaginationType" : "bootstrap",
                };
			$table.ib_serverSideTable(ajax_source, settings);
	});

	// Open the link, when anywhere in the table row is clicked...
	// ... except for form elements or other links. (Clicking these have their own actions.)
	$table.on('click', 'tbody tr', function(ev)
	{
		// If the clicked element is a link or form element or is inside one, do nothing
		if ( ! $(ev.target).is('a, label, button, :input') && ! $(ev.target).parents('a, label, button, :input')[0])
		{
			// Find the edit link
			var link = $(this).find('.edit-link').attr('href');

			// If the user uses the middle mouse button or Ctrl/Cmd key, open the link in a new tab.
			// Otherwise open it in the same tab
			if (ev.ctrlKey || ev.metaKey || ev.which == 2)
			{
				window.open(link, '_blank');
			}
			else
			{
				window.location.href = link;
			}
		}
	});

	// Toggle the publish state
	$table.on('click', '.publish-btn', function()
	{
		var $this       = $(this);
		var id          = this.getAttribute('data-id');
		var old_publish = parseInt(this.getElementsByClassName('publish-value')[0].innerHTML);
		var new_publish = (old_publish + 1) % 2;
		$.ajax('/admin/products/ajax_toggle_review_publish/'+id+'?publish='+new_publish).done(function(result)
		{
			$this.find('.publish-value').html(new_publish);
			if (new_publish == 1)
			{
				$this.find('.publish-icon').removeClass('icon-ban-circle').addClass('icon-ok');
			}
			else
			{
				$this.find('.publish-icon').removeClass('icon-ok').addClass('icon-ban-circle');
			}

		});
	});

	// Open the delete modal and pass the relevant review's ID into it
	$table.on('click', '.list-delete-button', function()
	{
		var id = $(this).data('id');
		$($(this).data('target')).find('[name="id"]').val(id);
	});

    $("#edit_product_add_related_dropdown").autocomplete({
        select: function(e, ui) {
            $('#edit_product_add_related_dropdown').val(ui.item.label);
            $('#edit_product_add_related_dropdown_id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            $.get("/admin/products/autocomplete_products",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });
});