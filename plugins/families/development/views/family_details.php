<form class="form-horizontal family-form" action="/admin/families/save" method="post">
    <fieldset>
		<input type="hidden" name="family_id" value="<?=@$family['id']?>"/>

		<div class="col-sm-12" style="margin-bottom: 2rem;">
			<h2>Family Details</h2>
		</div>

		<div class="family-form-columns clearfix">

			<div class="column col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="family_name">Family Name</label>
					<div class="col-sm-8">
						<input type="text" id="family_name" class="form-control validate[required] enforce_ucfirst" name="family_name" value="<?=@$family['family']?>"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="primary_contact_id">Primary Contact</label>
					<div class="col-sm-8">
						<select class="form-control" id="primary_contact_id" name="primary_contact_id">
							<option value="">-- Please Select --</option>
							<?php $primary_contact = NULL; ?>
							<?php if (isset($family['members'])) { ?>
								<?=html::optionsFromRows('id', 'fullname', $family['members'], $family['primary_contact_id'])?>
								<?php
								foreach ($family['members'] as $member)
								{
									if ($member['id'] == $family['primary_contact_id']) $primary_contact = $member;
								}
								?>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>

			<div class="column residence_field col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="family_country">Country</label>
					<div class="col-sm-8">
						<select class="form-control" id="family_country" name="country" disabled="disabled">
							<option>-- Please Select --</option>
							<?php if ( ! empty($countries)): ?>
								<?php foreach ($countries as $country): ?>
									<?php $selected = (isset($primary_contact) AND $primary_contact['address4'] == $country['id']) ? ' selected="selected"' : ''; ?>
									<option value="<?= $country['id'] ?>"<?= $selected ?>><?= $country['name'] ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="address1">Address 1</label>
					<div class="col-sm-8">
						<input type="text" id="address1" class="form-control validate[required] enforce_ucfirst" name="address1"
							   value="<?= isset($primary_contact['address1']) ? $primary_contact['address1'] : '' ?>" disabled="disabled"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="address2">Town / City</label>
					<div class="col-sm-8">
						<input type="text" id="address2" class="form-control enforce_ucfirst" name="address2"
							   value="<?= isset($primary_contact['address2']) ? $primary_contact['address2'] : '' ?>" disabled="disabled"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="address3">County</label>
					<div class="col-sm-8">
						<input type="text" id="address3" class="form-control enforce_ucfirst" name="address3"
							   value="<?= isset($primary_contact['address3']) ? $primary_contact['address3'] : '' ?>" disabled="disabled"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="postcode">Postcode</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" id="postcode" name="postcode" value="" placeholder="" disabled="disabled"/>
					</div>
				</div>

				<p>If you wish to EDIT address details,<br /> please go to the <a class="contact-link" href="/admin/contacts2/edit/<?=$family['primary_contact_id']?>" data-id="<?=$family['primary_contact_id']?>">PRIMARY CONTACT</a> to manage this update</p>
			</div>
		</div>

		<div class="form-action-group text-center" style="margin-top: 3em;">
			<button type="button" class="btn btn-primary save_button" data-action="save" data-original-title="Save Family" data-content="Save the family and Assign the Primary Contact. And reload this form">Save</button>
			<button type="button" class="btn btn-success save_button" data-action="save_and_exit" data-original-title="Save And Exit" data-content="Save the family and Assign the Primary Contact. And close this form">Save &amp; Exit</button>
			<?php if (FALSE){ ?>
				<input type="reset" class="btn" data-original-title="Reset" data-content="Undo all the change made."/>
			<?php } ?>
			<button type="button" class="btn btn-cancel cancel_button" data-original-title="Cancel" data-content="Exit the form without saving changes">Cancel</button>
		</div>

        <? // Modal boxes ?>
        <div id="family_confirm_delete" class="modal fade confirm_delete_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Confirm Deletion</h3>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you wish to delete this family?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn" data-dismiss="modal" data-content="Return to the form to Edit the family">Cancel</a>
                        <a href="/admin/contacts3/delete_family/<?= 0 ?>" class="btn btn-danger" data-action="delete_family" data-content="Proceed and delete the family, all the contacts from this family will not have any family associated anymore">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</form>