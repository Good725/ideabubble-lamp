$(document).ready(function() {
    build_table();
    var $table = $('#products_table');
    // Search by individual columns
    $table.find('.search_init').on('change', function ()
    {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
    });
    // Needed to get validation bubbles to work with bootstrap multiselect
    $('#products_table').find('input[data-content]').popover({placement:'top',trigger:'hover'});
});

var product_objects = [];

function set_options(){
    if(typeof $('#products_table_options').val() != 'undefined'){
        var products_table_options = JSON.parse($('#products_table_options').val()),
            sort_by = products_table_options.sort_by,
            order = products_table_options.sort_order;

        if(products_table_options.sort_by == -1){
            sort_by = 1;
            order = 'asc';
        }

        $('#products_table').dataTable().fnDestroy();
        var oTable = $('#products_table').dataTable( {
            "bDestroy": true,
            "iDisplayLength": products_table_options.rows_per_page,
            "aaSorting": [[sort_by, order]],
            "sPaginationType" : 'bootstrap'
            }
        );
        oTable.fnPageChange(products_table_options.page - 1);
        $('#products_table_length select').find(":selected").val(products_table_options.rows_per_page).trigger('change');
    }
}

/**
 * Build the products table.
 */
function build_table()
{
	var ajax_source = '/admin/products/ajax_get_all_products/';
	var settings = {
		"sPaginationType" : 'bootstrap'
	};

	$('#products_table').ib_serverSideTable(ajax_source, settings).on('click', '.publish', function(){
		var publishitem = $(this);
		var product_id = $(this).data("product-id");
		$.ajax({
				type: "POST",
				url: '/admin/products/ajax_toggle_product_publish_option',
				data: {data:JSON.stringify({id:publishitem.data('product-id')})},
				complete: function(response){
					if(publishitem.find('.icon-ok').length){
						publishitem.find('.icon-ok').removeClass("icon-ok").addClass("icon-remove");
					} else {
						publishitem.find('.icon-remove').removeClass("icon-remove").addClass("icon-ok");
					}
				}
			});
		return false;
	}).on('click', '.delete', function(){
		var deleteitem = $(this);
		$('#warning_message').html('<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected product.</p>');
		$('#confirm_delete').modal();
		$("#btn_delete").on("click",function() {
			$('#confirm_delete').modal('hide');
			$.ajax({
				type: "POST",
				url: '/admin/products/ajax_delete_product',
				data: {data:JSON.stringify({id:deleteitem.data('product-id')})},
				complete: function(response){
					$(deleteitem).parents("tr").remove();
				}
			});
			$("#btn_delete").off("click");
			return false;
		});
		return false;
	}).on('change', '.toggle_featured', function()
    {
        var product_id = this.getAttribute('data-product_id');
        $.post('/admin/products/ajax_toggle_product_featured/'+product_id);
    })
	// Open the link, when anywhere in the table row is clicked...
	// ... except for form elements or other links. (Clicking these have their own actions.)
	.on('click', 'tbody tr', function(ev)
	{
		// If the clicked element is a link or form element or inside one, do nothing
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
	})
	.on('click', '.manage-categories-button', function()
	{
		var product_id = this.getAttribute('data-product_id');

		$.ajax('/admin/products/ajax_get_product_categories/'+product_id).done(function(response)
		{
			var data           = JSON.parse(response);
			var category_ids   = data['category_ids'];
			var $included_list = $('#manage-product-categories-included');
			var $excluded_list = $('#manage-product-categories-excluded');

			document.getElementById('manage-product-categories-product-name').innerHTML = data['product_name'];
			document.getElementById('manage-product-categories-product-id').value = product_id;


			// Move all categories to the "excluded" list
			$included_list.children('li').appendTo($excluded_list);

			// Move the categories for the selected product to the "included" list
			for (var i = 0; i < category_ids.length; i++)
			{
				$excluded_list.find('[data-id="'+category_ids[i]+'"]').appendTo($included_list)
			}

			$('#manage-product-categories-modal').modal();
		});

	});

	$("#btn_delete").off("click");
}

$( "#manage-product-categories-excluded, #manage-product-categories-included" ).sortable({
	connectWith: ".manage-product-categories-list"
}).disableSelection();

$('#manage-product-categories-save').on('click', function()
{
	var product_id   = document.getElementById('manage-product-categories-product-id').value;
	var category_ids = [];
	$('#manage-product-categories-included').children('li').each(function()
	{
		category_ids.push(this.getAttribute('data-id'));
	});

	$.ajax({
		url    : '/admin/products/ajax_save_product_categories',
		data   : { id: product_id, category_ids: category_ids },
		method : 'post'
	}).done(function()
	{
		$('#manage-product-categories-modal').modal('hide');
		$('#products_table_length').find('select').trigger('change'); // force a refresh
	});
});

