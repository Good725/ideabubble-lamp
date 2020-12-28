<div id="edit_note_modal" class="modal fade edit_note_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3><span class="note_to"></span> Note #</h3>
			</div>
			<div class="modal-body">
				<input type="hidden" class="note_table_name" value="" />
				<form class="form-horizontal">
					<input type="hidden" name="note_id" value="" />
					<input type="hidden" name="reference_id" value="" />
					<input type="hidden" name="type" value="" />
					<div class="form-group">
						<label class="col-sm-3 control-label" for="add_note_note">Note</label>
						<div class="col-sm-9">
							<textarea class="form-control" id="add_note_note" name="note"></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a class="btn btn-primary save">Save</a>
				<a class="btn cancel" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>

<div id="confirm_delete_note_modal" class="modal fade confirm_delete_note_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this note?</p>
			</div>
			<div class="modal-footer">
				<a href="#" id="delete_note_button" class="btn btn-danger" data-note-action="delete" data-id="">Delete</a>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>