<?php
$logged_in_user = Auth::instance()->get_user();
?>

<div class="textnote-area" id="right-panel-text-note">

	<form class="form-horizontal textnote-area-expanded hidden" id="form_text_note" name="form_text_note" action="/admin/contacts3/dashboard_note_save" method="post">

		<div class="textnote-header">
			<span class="textnote-header-icon">
				<span class="icon-bullhorn"></span>
			</span>

			<span class="textnote-user">
				<span class="username"><?= $logged_in_user['name'].' '.$logged_in_user['surname'] ?></span>

				<img class="textnote-user-avatar" src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="profile" width="50" height="50" title="<?= __('Profile: ').$logged_in_user['name'] ?>" />
			</span>

			<button type="button" class="textnote-toggle">
				<span class="icon-times"></span>
			</button>
		</div>

		<div class="textnote-body">

			<h3><?= __('Send a quick note' ) ?></h3>

			<div class="form-group">
				<div class="col-sm-12">
					<label class="sr-only" for="textnote-contact"><?= __('Contact') ?></label>
					<input type="text" name="contact_selector" placeholder="Type contact name" />
					<input type="hidden" name="contact_id" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-12">
					<label class="sr-only" for="textnote-note"><?= __('Note') ?></label>
					<textarea class="form-control" id="textnote-note" name="note" rows="3" placeholder="<?= __('Text of the note') ?>"></textarea>
				</div>
			</div>
		</div>

		<div class="textnote-footer">
			<div class="form-action-group">
				<button class="btn btn-primary" type="button" name="save"><?= __('Save') ?></button>
			</div>
		</div>
	</form>

	<div class="textnote-area-collapsed" id="textnote-area-collapsed">
		<button type="button" class="textnote-toggle">
			<span class="custom-edit-icon" data-icon="k"></span>
		</button>
	</div>
</div>
