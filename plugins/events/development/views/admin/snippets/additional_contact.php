<div class="edit-event-organizer-section edit-event-additional-organizer<?= ($key == -1) ? ' hidden' : '' ?>"<?= ($key == -1) ? ' id="add-another-organizer-template"' : '' ?>>
	<input type="hidden" class="organizer-contact-id" name="organizers[<?= $key ?>][contact_id]" <?= ($key > -1) ? 'value="'.$organizer['contact_id'].'"' : 'disabled="disabled"' ?> />
	<div class="panel panel-default">
		<div class="panel-heading">
			<button type="button" class="btn-link right organizer-toggle" data-toggle="collapse" data-target="#organizer-toggle-<?= ($key == '-1' ? 'template' : $key) ?>">show / hide</button>
			<h4 class="panel-title"><?= __('Add Another Organiser') ?></h4>
		</div>
		<div class="collapse<?= ($key == -1) ? ' in' : '' ?> panel-body" id="organizer-toggle-<?= ($key == -1) ? 'template' : $key ?>">
			<div class="form-vertical">
				<div class="form-group">
					<label class="col-sm-8">
						<?= __('Organiser Name') ?>
						<input
							type="text"
							class="form-control"
							name="organizers[<?= $key ?>][name]"
							<?= ($key > -1) ? 'value="'.$organizer['first_name'].'"' : 'disabled="disabled"' ?>
					</label>

					<label class="col-sm-4">
						<div class="checkbox">
							<label>
								<input type="checkbox" class="organiser_checkbox" name="organizers[<?= $key ?>][is_primary]" value="1" <?= $organizer['is_primary'] == true ? 'checked="checked"' : '' ?>> Make Primary
							</label>
						</div>
					</label>
				</div>


				<div class="form-group">
					<label class="col-sm-12 control-label" for="edit-organiser-profile-image-<?= $key ?>"><?= __('Organiser Profile Image') ?></label>
					<div class="col-sm-6">
						<?= View::factory('multiple_upload', array('name' => 'organiser_' . $key, 'single' => true, 'preset' => 'Organizer profiles', 'onsuccess' => 'organizerp_image_uploaded', 'presetmodal' => 'no', 'duplicate' => 0)) ?>
					</div>

					<div class="col-sm-12 saved-image">
						<input type="hidden" id="edit-event-organizer-contact-id-<?= $key ?>" name="organizers[<?= $key ?>][profile_media_id]" value="<?=@$event['organizers'][$key]['profile_media_id']?>" />
						<img class="col-sm-6 organizer_profile_image_saved <?=@$event['organizers'][$key]['profile_media_url']? '' : 'hidden'?>" src="<?=@$event['organizers'][$key]['profile_media_url']?: 'about:blank'?>" alt="" style="cursor:pointer;" title="<?=__('Click to edit')?>" />
						<button type="button" class="btn-link saved-image-remove <?=@$event['organizers'][$key]['profile_media_url']? '' : 'hidden'?>">
							<span class="icon-trash"></span>
						</button>
					</div>
				</div>

				<div class="form-group">
                    <label class="col-sm-8">
                        <span class="sr-only"><?= __('Email') ?></span>
                        <input
                            type="email"
                            class="form-control validate[required] organizer-email"
                            name="organizers[<?= $key ?>][email]"
                            <?= ($key > -1) ? 'value="'.$organizer['email'].'"' : 'disabled="disabled"' ?>
                            placeholder="<?= __('Email') ?> *"
                        />
                    </label>
				</div>

				<div class="form-group">
					<label class="col-sm-8">
						<span class="sr-only"><?= __('Telephone') ?></span>
						<input
							type="text"
							class="form-control"
							name="organizers[<?= $key ?>][telephone]"
							<?= ($key > -1) ? 'value="'.$organizer['phone'].'"' : 'disabled="disabled"' ?>
							placeholder="<?= __('Telephone') ?>"
							/>
					</label>
				</div>

				<div class="form-group">
					<label class="col-sm-8">
						<span class="sr-only"><?= __('Website Address') ?></span>
						<input
							type="text"
							class="form-control validate[custom[urlOptionalProtocol]]"
							name="organizers[<?= $key ?>][website]"
							<?= ($key > -1) ? 'value="'.$organizer['website'].'"' : 'disabled="disabled"' ?>
							placeholder="<?= __('Website Address') ?>"
							/>
					</label>
				</div>

				<div class="form-group">
					<label class="col-sm-8">
						<span class="sr-only"><?= __('Facebook Address') ?></span>
						<input
							type="text"
							class="form-control validate[custom[noturl]]"
							name="organizers[<?= $key ?>][facebook_url]"
							<?= ($key > -1) ? 'value="'.$organizer['facebook'].'"' : 'disabled="disabled"' ?>
							placeholder="<?= __('Facebook Address') ?>"
							/>
					</label>
				</div>

				<div class="form-group">
					<label class="col-sm-8">
						<span class="sr-only"><?= __('Twitter Address') ?></span>
						<input
							type="text"
							class="form-control validate[custom[noturl]]"
							name="organizers[<?= $key ?>][twitter_url]"
							<?= ($key > -1) ? 'value="'.$organizer['twitter'].'"' : 'disabled="disabled"' ?>
							placeholder="<?= __('Twitter Address') ?>"
							/>
					</label>
				</div>
				
				<div class="form-group">
					<label class="col-sm-8">
						<span class="sr-only"><?= __('Snapchat Address') ?></span>
						<input
							type="text"
							class="form-control"
							name="organizers[<?= $key ?>][snapchat_url]"
							<?= ($key > -1) ? 'value="'.$organizer['snapchat'].'"' : 'disabled="disabled"' ?>
							placeholder="<?= __('Snapchat Address') ?>"
							/>
					</label>
				</div>
				
				<div class="form-group">
					<label class="col-sm-8">
						<span class="sr-only"><?= __('Instagram Address') ?></span>
						<input
							type="text"
							class="form-control"
							name="organizers[<?= $key ?>][instagram_url]"
							<?= ($key > -1) ? 'value="'.$organizer['instagram'].'"' : 'disabled="disabled"' ?>
							placeholder="<?= __('Instagram Address') ?>"
							/>
					</label>
				</div>
			</div>
		</div>
	</div>
</div>
