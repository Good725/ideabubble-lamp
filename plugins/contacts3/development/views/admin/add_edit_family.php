<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form class="form-horizontal educate-form" id="add_edit_family" action="/admin/contacts3/save_family" method="post">
    <div class="row gutters">
        <div class="col-sm-4">
            <h3 class="border-title">Family details</h3>

            <input type="hidden" id="family_id" name="family_id" value="<?= $family->get_id() ?>"/>
            <input type="hidden" id="family_action" name="action" value=""/>
            <input type="hidden" id="address" name="address" value=""/>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="family_name">Family name</label>
                </div>

                <div class="col-sm-12">
                    <?= Form::ib_input(null, 'family_name', $family->get_family_name(), ['id' => 'family_name', 'class' => 'validate[required] enforce_ucfirst']); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="family_primary_contact">Primary contact</label>
                </div>

                <div class="col-sm-12">
                    <?php
                    $options  = ['' => 'Please select'];
                    $selected = null;
                    $guardian_set = !empty($nonchildren);
                    $attributes = ['class' => 'primary-contact', 'id' => 'primary_contact_id'];

                    if (!empty($nonchildren)) {
                        foreach ($nonchildren as $nonchild) {
                            $options[$nonchild['id']] = $nonchild['first_name'] . ' ' . $nonchild['last_name'];

                            if ($nonchild['is_primary'] == 1) {
                                $selected = $nonchild['id'];
                            }
                        }
                    }

                    echo Form::ib_select(null, 'primary_contact_id', $options, $selected, $attributes);
                    ?>
                </div>
            </div>

            <div class="form-group hide">
                <div class="col-sm-12">
                    <label class="control-label" for="notes">Notes</label>
                </div>

                <div class="col-sm-12">
                    <?= Form::ib_textarea(null, 'notes', $family->get_notes(), ['id' => 'notes', 'rows' => 4]); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <h3 class="border-title">Address information</h3>

            <div class="residence_field">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label" for="family_country">Country</label>
                    </div>

                    <div class="col-sm-12">
                        <?php
                        $countries = $residence->get_all_countries();
                        $selected = ($residence->get_country() == '') ? 'IE' : $residence->get_country();
                        $options = html::optionsFromRows('code', 'name', $countries, $selected, ['value' => '', 'label' => 'Please select']);
                        echo Form::ib_select(null, 'country', $options, $selected, ['id' => 'family_country']);
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="address1">Address 1</label>
                </div>

                <div class="col-sm-12">
                    <?php
                    $attributes = ['id' => 'address1', 'class' => 'validate[required] enforce_ucfirst', 'disabled' => $guardian_set];
                    echo Form::ib_input(null, 'address1', $family->address->get_address1(), $attributes);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="address2">Address 2</label>
                </div>

                <div class="col-sm-12">
                    <?php
                    $attributes = ['id' => 'address2', 'class' => 'enforce_ucfirst', 'disabled' => $guardian_set];
                    echo Form::ib_input(null, 'address2', $family->address->get_address2(), $attributes);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="address3">Address 3</label>
                </div>
                <div class="col-sm-12">
                    <?php
                    $attributes = ['id' => 'address1', 'class' => 'enforce_ucfirst', 'disabled' => $guardian_set];
                    echo Form::ib_input(null, 'address1', $family->address->get_address3(), $attributes);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="town">Town</label>
                </div>
                <div class="col-sm-12">
                    <?php
                    $attributes = ['id' => 'town', 'class' => 'validate[required] enforce_ucfirst', 'disabled' => $guardian_set];
                    echo Form::ib_input(null, 'town', $family->address->get_town(), $attributes);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="family_county">County</label>
                </div>
                <div class="col-sm-12">
                    <?php
                    $counties   = $residence->get_all_counties();
                    $options    = html::optionsFromRows('id', 'name', $counties, $residence->get_county(), ['value' => '', 'label' => 'Please select']);
                    $attributes = ['id' => 'family_county', 'disabled' => $guardian_set];
                    echo Form::ib_select(null, 'county', $options, null, $attributes);
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label" for="postcode">Postcode</label>
                </div>

                <div class="col-sm-12">
                    <?php
                    $attributes = ['id' => 'postcode', 'placeholder' => 'V94 Y58Y', 'disabled' => $guardian_set];
                    echo Form::ib_input(null, 'postcode', $family->address->get_postcode(), $attributes);
                    ?>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <h3 class="border-title">Contact details</h3>

            <div class="form-group">
                <div class="add_contact_type col-sm-12">
                    <div class="contact_types_list">
                        <?php foreach($notifications as $notification): ?>
                            <?php include 'snippets/add_edit_contact_method.php'; ?>
                        <?php endforeach; ?>
                    </div>
                    <script type="text/javascript">
                        if ($('#primary_contact_id') != '') {
                            $(".add_contact_type :input").attr("disabled", true);
                        }
                    </script>
                    <?php // display:none, rather than type="hidden", so that .defaultValue can be used ?>
                    <label style="display:none">
                        <input class="form-control" type="text"<?= $guardian_set ? ' disabled' : '' ?> id="family_notifications_group_id" name="notifications_group_id" value="<?= $family->get_notifications_group_id() ?>" style="display:none;"/>
                    </label>

                    <?php if ($guardian_set): ?>
                        <div class="input-group">
                            <p class="edit-text">If you wish to edit contact details, please go to the <strong>primary</strong> contact to manage this update.</p>
                        </div>
                    <?php else: ?>
                        <div class="input-group">
                            <div class="selectbox">
                                <select class="form-control select_type" style="width:80px;">
                                    <optgroup label="Type">
                                        <?php foreach ($notification_types as $notification_type): ?>
                                            <option value="<?= $notification_type['id'] ?>" data-stub="<?= $notification_type['stub'] ?>"
                                                <?= $notification_type['stub'] == 'mobile' ? ' selected="selected"' : '' ?>>
                                                <?= $notification_type['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            <input class="form-control enter_value" type="text" id="contact_enter_value" style="width:155px;width:calc(100% - 80px);" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-link text-decoration-none submit_item" style="height:32px;">Add</button>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
        <? // Action buttons ?>

        <div class="well action-buttons form-action-group">
            <button type="button" class="btn btn-primary save_button" data-action="save" data-original-title="Save Family" data-content="Save the family and Assign the Primary Contact. And reload this form">Save</button>
            <button type="button" class="btn btn-primary save_button" data-action="save_and_exit" data-original-title="Save And Exit" data-content="Save the family and Assign the Primary Contact. And close this form">Save &amp; Exit</button>
            <?php if ($family->get_family_count() == 0 AND $family->get_id() != ''): ?>
                <button type="button" class="btn btn-danger delete_button" data-action="delete_family" data-original-title="Delete Family" data-content="Delete the family and remove the association to the members. Family members will be listed with No Family">Delete Family</button>
            <?php endif; ?>
            <?php if($family->get_id() == ''): ?>
                <input type="reset" class="btn btn-default" data-original-title="Reset" data-content="Undo all the change made."/>
            <?php endif; ?>
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
						<a href="/admin/contacts3/delete_family/<?= $family->get_id() ?>" class="btn btn-danger" data-action="delete_family" data-content="Proceed and delete the family, all the contacts from this family will not have any family associated anymore">Delete</a>
					</div>
				</div>
			</div>
        </div>
      
</form>
