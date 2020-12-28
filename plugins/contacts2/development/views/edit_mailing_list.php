<?= (isset($alert)) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form class="form-horizontal" action="/admin/contacts2/save_mailing_list/<?= $list['id'] ?>" method="post">
	<input type="hidden" id="edit-mlist-id" name="id" value="<?= $list['id'] ?>" />

	<div class="form-group">
		<label class="col-sm-2 control-label" for="edit-mlist-title"><?= __('Name') ?></label>
		<div class="col-sm-5">
			<input type="text" class="form-control validate[required]" id="edit-mlist-title" name="name" value="<?= $list['name'] ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="edit-mlist-summary"><?= __('Summary') ?></label>
		<div class="col-sm-9">
			<textarea class="form-control ckeditor" id="edit-mlist-summary" name="summary"><?= $list['summary'] ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-2 control-label"><?= __('People') ?></div>
		<div class="col-sm-9">
            <input type="text" class="form-control" id="edit-mlist-type_to_add" placeholder="<?= __('Type to add a contact') ?>" />
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-9">
            <table class="table table-striped<?= (empty($contacts)) ? ' hidden' : '' ?>" id="edit-mlist-contacts">
                <thead>
                    <tr>
                        <th scope="col"><?= __('Name') ?></th>
                        <th scope="col"><?= __('Email') ?></th>
                        <th scope="col"><?= __('Remove') ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ( ! empty($contacts)): ?>
                        <?php foreach ($contacts as $contact): ?>
                            <tr data-id="<?= $contact['id'] ?>">
                                <td class="edit-mlist-contact-name"><?= trim($contact['first_name'].' '.$contact['last_name']) ?></td>
                                <td class="edit-mlist-contact-email"><?= $contact['email'] ?></td>
                                <td class="edit-mlist-contact-actions">
                                    <input class="edit-mlist-contact-id" type="hidden" name="contact_ids[]" value="<?= $contact['id'] ?>" />

                                    <button type="button" class="btn-link edit-mlist-contact-remove" title="<?= __('Remove') ?>">
                                        <span class="sr-only"><?= __('Remove') ?></span>
                                        <span class="icon-remove"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>


                <tfoot hidden class="hidden" id="edit-mlist-contact-template">
                    <tr>
                        <td class="edit-mlist-contact-name"></td>
                        <td class="edit-mlist-contact-email"></td>
                        <td class="edit-mlist-contact-actions">
                            <input class="edit-mlist-contact-id" type="hidden" name="contact_ids[]" />

                            <button type="button" class="btn-link edit-mlist-contact-remove" title="<?= __('Remove') ?>">
                                <span class="sr-only"><?= __('Remove') ?></span>
                                <span class="icon-remove"></span>
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>

		</div>
	</div>

	<div class="form-action-group">
		<div class="btn-group">
			<button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only"><?= __('Toggle Dropdown') ?></span>
			</button>
			<ul class="dropdown-menu">
				<li><button type="submit" name="action" value="save_and_exit" class="btn-link"><?= __('Save &amp; Exit') ?></button></li>
			</ul>
		</div>

		<button class="btn btn-default" type="reset"><?= __('Reset') ?></button>
		<a href="/admin/contacts2/mailing_lists" class="btn btn-default" type="reset"><?= __('Cancel') ?></a>

		<?php if ( ! empty($list['id']) AND FALSE): ?>
			<button type="button" class="btn btn-danger"><?= __('Delete') ?></button>
		<?php endif; ?>
	</div>

    <div class="modal modal-primary fade" tabindex="-1" role="dialog" id="edit-mlist-otherwise_listed">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Contact in another mailing list') ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit-mlist--otherwise_listed-data" />

                    <p>The contact, <strong id="edit-mlist--otherwise_listed-contact_name"></strong>, is already in the mailing list, <strong id="edit-mlist--otherwise_listed-mailing_list"></strong></p>
                    <p>Adding them to this list will remove them from their current mailing list.</p>
                    <p>Are you sure you wish to continue?</p>
                </div>

                <div class="modal-footer form-actions">
                    <button type="button" class="btn btn-primary" id="edit-mlist-otherwise_listed-confirm">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</form>