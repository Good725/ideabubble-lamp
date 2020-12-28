<form class="form-horizontal" id="form_add_edit_contact" name="form_add_edit_contact" action="/admin/contacts2/save/" method="post">
	<? $contact_details = (count($_POST) > 0) ? $_POST : (isset($contact_details) ? $contact_details : array()) ?>

    <div class="tab-body">
		<div class="col-sm-4">
			<?php
			foreach (Model_Contacts::getExtentions() as $extention) {
				foreach ($extention->getFieldsets($contact_details) as $xFieldset) {
					if ($xFieldset['position'] != 'first')continue;
			?>
			<?= View::factory($xFieldset['view'], array('contact' => $contact_details, 'data' => $extention->getData($contact_details))); ?>
			<?php
				}
			}
			?>

			<fieldset class="personal">
				<legend>Personal Information</legend>
				<?php if (count($relations) && !Model_Plugin::is_enabled_for_role('Administrator', 'families')): ?>
					<div class="form-group hidden">
						<label class="col-sm-12 control-label" for="relations">Related Contacts</label>
						<div class="col-sm-12">
							<div class="add">
								<select id="contact_relation_id">
									<option value=""></option>
									<?=HTML::optionsFromArray($relations, null)?>
								</select>
								<input type="text" id="contact_related_auto" />
								<button type="button" id="contact_relation_add">Add</button>
							</div>
							<ul id="contact_has_relations">
								<?php
								if (@$contact_details['relations']) {
									foreach ($contact_details['relations'] as $has_relation) {
								?>
								<li>
								<input type="hidden" name="has_relation[contact_2_id][]" value="<?=$has_relation['contact_2_id']?>">
								<input type="hidden" name="has_relation[relation_id][]" value="<?=$has_relation['relation_id']?>">
								<?=@$relations[$has_relation['relation_id']]?>: <a href="/admin/contacts2/edit/<?=$has_relation['contact_2_id']?>"><?=$has_relation['contact_2']?></a> &nbsp;
								<span onclick="$(this).parent().remove()">remove</span>
								</li>
								<?php
									}
								}

								if (@$contact_details['of_relations']) {
									foreach ($contact_details['of_relations'] as $of_relation) {
										?>
										<li>
										<?=@$relations[$of_relation['relation_id']]?> of <a href="/admin/contacts2/edit/<?=$of_relation['contact_1_id']?>"><?=$of_relation['contact_1']?></a> &nbsp;
										</li>
										<?php
									}
								}
								?>
							</ul>
						</div>
					</div>
				<?php endif; ?>

				<div class="form-group">
                    <div class="col-sm-12"><label class="control-label" for="edit-contact-title">Title</label></div>
                    <div class="col-sm-12">
                        <input type="text" class="form-input" name="title" id="edit-contact-title" value="<?=isset($contact_details['title']) ? $contact_details['title'] : ''?>" placeholder="Mr." />
                    </div>
				</div>

				<div class="form-group">
					<div class="col-sm-12"><label class="control-label" for="edit-contact-first_name">First name</div>

					<div class="col-sm-12">
                        <input type="text" class="form-input validate[required]" name="first_name" id="edit-contact-first_name" value="<?=isset($contact_details['first_name']) ? $contact_details['first_name'] : ''?>" />
                    </div>

                    <div class="col-sm-12" style="margin-top: .5em;">
                        <?= Form::ib_checkbox(__('Autocapitalise'), null, null, true, array('class' => 'enforce_ucfirst_toggle')); ?>
					</div>
				</div>

				<div class="form-group">
                    <div class="col-sm-12"><label class="control-label" for="edit-contact-last_name">Last name</div>

					<div class="col-sm-12">
                        <input type="text" class="form-input validate[required]" name="last_name" id="edit-contact-last_name" value="<?=isset($contact_details['last_name']) ? $contact_details['last_name'] : ''?>" />
                    </div>

                    <div class="col-sm-12" style="margin-top: .5em;">
                        <?= Form::ib_checkbox(__('Autocapitalise'), null, null, true, array('class' => 'enforce_ucfirst_toggle')); ?>
					</div>
				</div>

                <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'courses')): ?>
                    <div class="form-group">
                        <div class="col-sm-12"><label class="control-label" for="edit-contact-dob">Date of Birth</div>
                        <div class="col-sm-12">
                            <?php
                            $value = isset($contact_details['dob']) ? $contact_details['dob'] : '';
                            $attributes = array('class' => 'datepicker date', 'id' => 'edit-contact-dob');
                            echo Form::ib_input(null, 'dob', $value, $attributes);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

				<div class="form-group">
                    <div class="col-sm-12"><label class="control-label" for="mailing_list">Mailing List</div>

					<div class="col-sm-7">
                        <?php
                        $options = '';
                        foreach ($mailing_list as $item) {
                            $selected = (isset($contact_details['mailing_list']) AND ($contact_details['mailing_list'] == $item)) ? ' selected="selected"' : '';
                            $options .= '<option value="'.$item.'"'.$selected.'>'.$item.'</option>';
                        }
                        echo Form::ib_select(null, 'mailing_list', $options, null, array('id' => 'mailing_list'));
                        ?>
					</div>

					<div class="col-sm-5">
						<label class="sr-only" for="new_mailing_list">New Mailing List</label>
						<input type="text" class="form-input" id="new_mailing_list" name="new_mailing_list" />
					</div>
				</div>
			</fieldset>

			<?php
			foreach (Model_Contacts::getExtentions() as $extention) {
				foreach ($extention->getFieldsets($contact_details) as $xFieldset) {
					if ($xFieldset['position'] != 'personal')continue;
					?>
					<?= View::factory($xFieldset['view'], array('contact' => $contact_details, 'data' => $extention->getData($contact_details))); ?>
					<?php
				}
			}
			?>

            <fieldset>
                <legend>Publish</legend>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="publish">Publish</label>
                    <div class="col-sm-9">
                        <?php $published = (isset($contact_details['publish']) AND ($contact_details['publish'] == 1)) ?>
                        <div class="btn-group btn-group-slide" data-toggle="buttons" id="publish">
                            <label class="btn btn-default<?= $published ? ' active' : '' ?>">
                                <input type="radio"<?= $published ? ' checked="checked"' : '' ?> value="1" name="publish" class="publish yes">Yes
                            </label>
                            <label class="btn btn-default<?= (! $published) ? ' active' : '' ?>">
                                <input type="radio"<?= (! $published) ? ' checked="checked"' : '' ?> value="0" name="publish" class="publish no">No
                            </label>
                        </div>
                    </div>
                </div>

            </fieldset>
		</div>

		<div class="col-sm-4">
			<fieldset class="address">
				<legend>Address Information <?php if(Model_Plugin::is_enabled_for_role('Administrator', 'Family')){ ?><label><input type="checkbox" /> <small>Use family</small></label><?php } ?></legend>
				<div class="form-group">

                    <div class="col-sm-12"><label class="control-label" for="edit-contact-country">Country</label></div>

					<div class="col-sm-12">
                        <?php
                        $options = array('' => '');
                        foreach ($countries as $country) $options[$country['id']] = $country['name'];
                        $attributes = array('id' => 'edit-contact-country');
                        echo Form::ib_select(null, 'country_id', $options, $contact_details['country_id'], $attributes);
                        ?>
					</div>
				</div>

				<div class="form-group">
                    <div class="col-sm-12"><label class="control-label" for="address1">Address 1</label></div>

					<div class="col-sm-12">
						<input type="text" class="form-input" id="address1" name="address1" value="<?=isset($contact_details['address1']) ? $contact_details['address1'] : ''?>" />
					</div>
				</div>

				<div class="form-group">
                    <div class="col-sm-12"><label class="control-label" for="address2">Town / City</label></div>
					<div class="col-sm-12">
						<input type="text" class="form-input" id="address2" name="address2" value="<?=isset($contact_details['address2']) ? $contact_details['address2'] : ''?>" />
					</div>
				</div>

				<div class="form-group">

                    <div class="col-sm-12"><label class="control-label" for="address3">County</label></div>
                    <div class="col-sm-12">
						<input type="text" class="form-input" id="address3" name="address3" value="<?=isset($contact_details['address3']) ? $contact_details['address3'] : ''?>" />
					</div>
				</div>

				<div class="form-group">

                    <div class="col-sm-12"><label class="control-label" for="edit-contact-postcode">Postcode</label></div>
                    <div class="col-sm-12">
						<input type="text" class="form-input" id="edit-contact-postcode" name="postcode" value="<?=isset($contact_details['postcode']) ? $contact_details['postcode'] : ''?>" />
					</div>
				</div>

				<div class="form-group">
                    <div class="col-sm-12"><label class="control-label" for="edit-contact-coordinates">Coordinates</label></div>
					<div class="col-sm-12">
						<input type="text" class="form-input" id="edit-contact-coordinates" name="coordinates" value="<?= isset($contact_details['coordinates']) ? $contact_details['coordinates'] : '' ?>" />
					</div>
				</div>
			</fieldset>

			<fieldset class="hidden">
				<legend>Google Map</legend>
				<div class="form-group">
					<div class="col-sm-6"></div>
					<div class="col-sm-6">
						<button type="button" class="btn btn-default btn-lg"><?= __('Find Location') ?></button>
					</div>
				</div>
			</fieldset>
		</div>

        <div class="col-sm-4">
            <fieldset class="notes">
                <legend>Add a New Note</legend>
                <div class="form-group">
                    <label class="sr-only" for="notes">Notes</label>
                    <div class="col-sm-12">
                        <textarea class="form-input" id="notes" name="notes" rows="4"><?=isset($contact_details['notes']) ? $contact_details['notes'] : ''?></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="communications">
                <legend>
                    Contact Information
                    <label>
                        <input type="checkbox" />
                        <small>Use my profile information</small>
                    </label>
                </legend>

                <div class="form-group comm comm-template hidden">
                    <label class="col-sm-4 control-label" for="comm_index"></label>
                    <div class="col-sm-8">
                        <input type="hidden" class="comm_type_index" name="comm[index][id]" value="" />
                        <input type="hidden" class="comm_type_index" name="comm[index][type_id]" value="" />
                        <input type="text" class="form-input comm_index" name="comm[index][value]" value="">
                        <button type="button" class="btn-link comm-remove">
                            <span class="icon-remove"></span>
                        </button>
                    </div>
                </div>

                <div class="comm-list">
                    <?php if(isset($contact_details['communications']))foreach ($contact_details['communications'] as $comm_index => $comm) { ?>
                        <div class="form-group comm">
                            <label class="col-sm-4" for="comm_<?=$comm_index?>"><?=$comm['type']?></label>
                            <div class="col-sm-8">
                                <input type="hidden" class="comm_type_<?=$comm_index?>" name="comm[<?=$comm_index?>][id]" value="<?=$comm['id']?>" />
                                <input type="hidden" class="comm_type_<?=$comm_index?>" name="comm[<?=$comm_index?>][type_id]" value="<?=$comm['type_id']?>" />
                                <input type="text" class="form-input comm_<?=$comm_index?>" name="comm[<?=$comm_index?>][value]" value="<?=$comm['value']?>">
                                <button type="button" class="btn-link comm-remove">
                                    <span class="icon-remove"></span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="comm-add">
                    <div class="form-group">
                        <label class="col-sm-4" for="comm_new_type">
                            <?php
                            array_unshift($communication_types, array('id' => '', 'name' => 'Type', 'deleted' => 0));
                            $attributes = array('class' => 'comm_new_type');
                            echo Form::ib_select(null, null, html::optionsFromRows('id', 'name', $communication_types), null, $attributes);
                            ?>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-input comm_new_value" value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class=" col-sm-offset-10 col-sm-2">
                            <button type="button" class="btn btn-default add">Add</button>
                        </div>
                    </div>
                </div>
            </fieldset>

            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'families')): ?>
                <fieldset>
                    <legend>Medical information</legend>

                    <div class="form-group">
                        <?php foreach ($preference_types as $ptype): ?>
                            <?php
                            if ($ptype['name'] != 'Medical Information') continue;
                            $medical_information = '';
                            if (isset($contact_details['preferences'])) {
                                foreach ($contact_details['preferences'] as $preference) {
                                    if ($preference['type_id'] == $ptype['id'] AND $preference['value'] != '') {
                                        $medical_information = $preference['value'];
                                        break;
                                    }
                                }
                            }
                            ?>
                            <label class="col-sm-12">
                                <label class="sr-only" for="preference_<?=$ptype['id']?>"><?=$ptype['name']?></label>
                                <textarea class="form-input" rows="4" id="preference_<?=$ptype['id']?>" name="preference[Medical Information]"><?= $medical_information ?></textarea>
                            </label>
                        <?php endforeach; ?>
                    </div>


                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload_document_modal">
                                <span class="icon-plus"></span>
                                Attach Document
                            </button>
                        </div>
                    </div>
                </fieldset>

		    	<fieldset>
			    	<legend>Privacy</legend>

                    <div class="form-group">
                        <?php
                        foreach ($preference_types as $ptype) {
                            if ($ptype['name'] != 'Photo/Video Permission')continue;
                            $checked = false;
                            if (isset($contact_details['preferences'])) {
                                foreach ($contact_details['preferences'] as $preference) {
                                    if ($preference['type_id'] == $ptype['id'] && $preference['value'] == 1) {
                                        $checked = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            <label class="col-sm-12">
                                <input type="checkbox" id="preference_<?=$ptype['id']?>" name="preference[Photo/Video Permission]" value="1" <?=$checked ? 'checked="checked"' : ''?> />
                                <label for="preference_<?=$ptype['id']?>"><?=$ptype['name']?></label>
                            </label>
                        <?php
                        }
                        ?>
                    </div>
                </fieldset>

                <fieldset class="preferences">
                    <legend>Contact Preferences</legend>
                    <div class="form-group">
                        <?php
                        foreach ($preference_types as $ptype) {
                            if ($ptype['section'] != 'communication')continue;
                            $checked = false;
                            if (isset($contact_details['preferences'])) {
                                foreach ($contact_details['preferences'] as $preference) {
                                    if ($preference['type_id'] == $ptype['id'] && $preference['value'] == 1) {
                                        $checked = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            <label class="col-sm-12">
                                <input type="checkbox" id="preference_<?=$ptype['id']?>" name="preference[<?=$ptype['id']?>]" value="1" <?=$checked ? 'checked="checked"' : ''?> />
                                <label for="preference_<?=$ptype['id']?>"><?=$ptype['name']?></label>
                            </label>
                        <?php
                        }
                        ?>
                    </div>
                </fieldset>
            <?php endif; ?>

		</div>

	</div>

	<input type="hidden" id="id" name="id" value="<?=isset($contact_details['id']) ? $contact_details['id'] : ''?>">

	<div class="form-group">
		<div class="col-sm-12 form-actions form-action-group text-center">
			<button type="submit" class="btn btn-primary" data-action="save">Save</button>
			<button type="submit" class="btn btn-success" data-action="save_exit">Save &amp; Exit</button>
			<button type="reset" class="btn">Reset</button>
			<a href="/admin/contacts2" class="btn-link">Cancel</a>
		</div>
	</div>
</form>
