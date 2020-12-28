<?=(isset($alert)) ? $alert : '';?>
<div id="list_discounts_alert_area">
</div>
<table class="table table-striped dataTable" id="discounts_list_table">
    <thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Title</th>
			<th scope="col">Summary</th>
			<th scope="col">Detail</th>
			<th scope="col">Valid From</th>
			<th scope="col">Valid To</th>
			<th scope="col">Edit</th>
			<th scope="col">Publish</th>
			<th scope="col">Delete</th>
		</tr>
    </thead>
    <tbody>
    <?php

    ?>
		<?php foreach ($discounts AS $key=>$discount): ?>
			<tr data-discount_id="<?=$discount['id'];?>">
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'><?=$discount['id']?></a></td>
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'><?=$discount['title']?></a></td>
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'><?=$discount['summary'];?></a></td>
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'><?

                    $detail = array();
                    if ($discount['code'] != '') {
                        $detail[] = 'Code: ' . $discount['code'];
                    }

                    if ($discount['amount_type'] == 'Percent') $detail[] = $discount['amount'] . '% Off';
                    if ($discount['amount_type'] == 'Fixed') $detail[] = '&euro;' . $discount['amount'] . ' Off';
                    if ($discount['amount_type'] == 'Quantity') $detail[] = $discount['amount'] . ' Items Free';

					$from_to = ($discount['from'] ? 'From: &euro;' . $discount['from'] . ' ' : '') . ( $discount['to'] > 0 ? 'To: &euro;' . $discount['to'] . ' ' : '');
                    if ($from_to != '') {
                        $detail[] = $from_to;
                    }

                    if ($discount['categories'] != '') {
                        $detail[] = 'courses in ' . Model_KES_Discount::get_course_cats_from_discount_x($discount['categories']);
                    }

                    if ($discount['min_students_in_family'] > 0) {
                        $detail[] = 'More than ' . $discount['min_students_in_family'] . ' students in family';
                    }

                    $detail[] = 'for ' . $discount['schedule_type'];

                    echo implode(', ', $detail);

				?></a></td>
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'><?=date('d-m-Y',strtotime($discount['valid_from']));?></a></td>
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'><?=date('d-m-Y',strtotime($discount['valid_to']));?></a></td>
				<td><a href='<?php echo URL::Site('admin/bookings/add_edit_discount/'.$discount['id']);?>'>Edit</a></td>
				<td>
					<label>
						<span class="hidden"><?= ($discount['publish'] == '') ? 1 : 0 ?></span>
						<input class="toggle_publish" type="checkbox"<?= ($discount['publish'] != 0) ? ' checked="checked"' : '' ?> />
						<i></i>
					</label>
				</td>
				<td class="remove_report"><i class="icon-remove"></i></td>
			</tr>
		<?php endforeach;?>
    </tbody>
</table>

<div id="delete_discount_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete_discount_heading" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<input type="hidden" id="discount_to_delete" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="delete_discount_heading">Modal header</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you wish to delete this discount?</p>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" aria-hidden="true" id="delete_discount">Delete</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true" id="cancel_delete">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>
	$('.toggle_publish').change(function()
	{
		var discount_id = $(this).closest('tr').data('discount_id');
		$.post('/admin/bookings/ajax_toggle_discount_publish', {
			id: discount_id,
			publish: this.checked ? 1 : 0
		}).done(function(result)
		{
			$('#list_discounts_alert_area').append(result);
		});
	});

	$('.remove_report').on('click', function()
	{
		document.getElementById('discount_to_delete').value = $(this).closest('tr').data('discount_id');
		$('#delete_discount_modal').modal();
	});

	$('#delete_discount').on('click', function()
	{
		var discount_id = document.getElementById('discount_to_delete').value;
		$.post('/admin/bookings/ajax_delete_discount/'+discount_id).done(function(result)
		{
			$('#discounts_list_table').find('tr[data-discount_id="'+discount_id+'"]').remove();
			$('#list_discounts_alert_area').append(result);
			$('#delete_discount_modal').modal('hide');
		});
	});
</script>