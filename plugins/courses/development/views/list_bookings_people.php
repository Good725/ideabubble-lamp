<?= (isset($alert)) ? $alert : '' ?>
<div><?= $schedule['name'] ?> / <?= $schedule['start_date'] ?></div>
<span class="hide" id="schedule_data" data-id="<?=$schedule['id']?>"></span>
<? $x = 0; ?>
<table class="table table-striped" id="booking_people_table">
    <thead>
		<tr>
			<th scope="col">First name</th>
			<th scope="col">Surname</th>
			<th scope="col">Email</th>
			<th scope="col">Phone</th>
			<th scope="col">Comments</th>
			<th scope="col">School</th>
			<th scope="col">School Address</th>
			<th scope="col">Roll No</th>
			<th scope="col">School phone</th>
			<th scope="col">County</th>
			<th scope="col">Paid</th>
		</tr>
    </thead>
    <tbody>
    </tbody>

</table>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected category.
					<br>All items like subcategories, courses will be also deleted!</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>
		</div>
	</div>
</div>

