<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class="table table-striped dataTable table-condensed " id="product_reviews_table">
	<thead>
		<tr>
			<th scope="col"><?= __('ID') ?></th>
			<th scope="col"><?= __('Title') ?></th>
			<th scope="col"><?= __('Product') ?></th>
			<th scope="col"><?= __('Rating') ?></th>
			<th scope="col"><?= __('Author') ?></th>
			<th scope="col"><?= __('Email') ?></th>
			<th scope="col"><?= __('Created') ?></th>
			<th scope="col"><?= __('Modified') ?></th>
			<th scope="col"><?= __('Created by') ?></th>
			<th scope="col"><?= __('Actions') ?></th>
			<th scope="col"><?= __('Publish') ?></th>
		</tr>
	</thead>
</table>


<div class="modal fade" tabindex="-1" role="dialog" id="delete-product-review-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/products/delete_review" method="post">
				<input type="hidden" name="id" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete review') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this review?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-product-review-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function()
	{
		// Server-side datatable
		var $table = $('#product_reviews_table');
		$table.ready(function()
		{
		        var ajax_source = '/admin/products/ajax_get_reviews_datatable/';
		        var settings = {
                "sPaginationType" : "bootstrap"
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
	});
</script>
