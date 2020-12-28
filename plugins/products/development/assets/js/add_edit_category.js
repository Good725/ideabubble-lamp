$(document).ready(function() {
    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    // CKEditor Configuration
    CKEDITOR.replace('information');

    $('#image')
        .change(function() {
            var item = $('#image').val();

            if (item == '') {
                $('#image_preview').attr('alt', '').attr('src', '');
                $('#image_preview_container').hide();
            } else {
                $('#image_preview').attr('alt', item).attr('src', media_base_location + item);
                $('#image_preview_container').show();
            }
        });



	$('#edit-category-add-product-btn').click(function()
	{
		var $product   = $('#edit-category-select-product').find(':selected');
		var product_id = $product.val();

		$('#edit-category-products-table').find('tbody')[0].innerHTML += '' +
			'<tr data-id="'+product_id+'">' +
				'<td><input type="hidden" name="product_ids[]" value="'+product_id+'" />'+product_id+'</td>' +
				'<td>'+$product.html()+'</td>' +
				'<td><button type="button" class="close">&times;</button></td>' +
			'</tr>';
	});
	$('#edit-category-products-table').on('click', '.close', function()
	{
		$(this).parents('tr').remove();
	});


    $("#form_add_edit_category").validate();

    AJAX.make_get_request('/admin/products/ajax_get_category_media_base_location/', function(r) { media_base_location = r; $('#image').trigger('change'); }, null);
});

var media_base_location;
