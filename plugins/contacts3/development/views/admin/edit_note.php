<div id="edit_note_modal" class="modal fade edit_note_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3><span class="note_to"></span> Note #<?= $note->get_id()?></h3>
			</div>
			<div class="modal-body">
				<input type="hidden" class="note_table_name" value="<?= $table_name ?>" />
				<form class="form-horizontal">
					<input type="hidden" name="id"            value="<?= $note->get_id() ?>" />
					<input type="hidden" name="link_id"       value="<?= $note->get_link_id() ?>" />
					<input type="hidden" name="table_link_id" value="<?= $note->get_table_link_id() ?>" />
					<div class="form-group">
						<label class="col-sm-3 control-label" for="add_note_note">Note</label>
						<div class="col-sm-9">
							<textarea class="form-control" id="add_note_note" name="note"><?= $note->get_note() ?></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-primary note_save_btn">Save</a>
				<a href="#" class="btn btn-danger note_delete_btn">Delete</a>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
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
				<a href="#" id="delete_note_button" class="btn btn-danger" data-note-action="delete" data-id="<?= $note->get_id() ?>">Delete</a>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>