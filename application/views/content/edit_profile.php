<?= (isset($alert)) ? $alert : ''; ?>
 <?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>
<?php $logged_in_user = Auth::instance()->get_user(); ?>
<style type="text/css">
    <?php // TODO: Move to stylesheet ?>
    .edit_profile_section header p{font-size:12px;}
    .edit_profile_wrapper figure{text-align:center;}

	.edit_profile_wrapper section {
		clear: both;
		padding-bottom: 18px;
	}

	.edit_profile_wrapper h2 {
		margin-bottom: 19px;
	}

	.edit_profile_wrapper .control-label {text-align:left;font-weight:bold;padding-left: 5px;}

	.edit_profile_wrapper .form-group {
		margin-bottom: 12px;
	}

	.edit_profile_wrapper .form-control {
		border-radius: 2px;
		padding-top: 8px;
		padding-bottom: 8px;
		height: 37px;
	}

	.edit_profile_wrapper .form-control[rows] {
		height: auto;
	}

	.profile-cms-avatar-controls .btn {
		margin-top: 1.154em;
		padding-top: .7275em;
		padding-bottom: .7275em;
		width: 100%;
	}
</style>
<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save" method="post">
        <input class="form-control" id="edit_profile_redirect" type="hidden" name="redirect" value="save" />

		<!-- Personal details and avatar -->
		<section>
			<h2 style="margin-bottom: 5px;"><?= __('Profile Picture') ?></h2>
			<div>
				<?php
				$gravatar_enabled = Settings::instance()->get('gravatar_enabled');
				$use_gravatar = ($gravatar_enabled AND ($data['use_gravatar'] == 1 OR trim($data['avatar']) == ''));
				?>

				<?php if ($gravatar_enabled): ?>
					<div class="form-group">
						<div class="col-sm-12">
							<label>
								<input type="radio" id="edit-profile-use_gravatar" name="use_gravatar" value="1"<?= $use_gravatar ? ' checked' : '' ?> /> Use Gravatar
							</label>
							<br />
							<label>
								<input type="radio" id="edit-profile-use_local" name="use_gravatar" value="0"<?= ( ! $use_gravatar) ? ' checked' : '' ?> /> Upload your own avatar
							</label>
						</div>
					</div>
				<?php else: ?>
					<input type="hidden" name="use_gravatar" value="<?= ($data['use_gravatar'] == 1 OR trim($data['avatar']) == '') ? 1 : 0 ?>" />
				<?php endif; ?>

				<div class="form-group">
					<div class="profile-cms-avatar-controls <?= $use_gravatar ? ' hidden' : '' ?>" id="profile-cms-avatar-controls">
						<div class="col-sm-2">
							<button type="button" data-accept=".jpg,.jpeg,.png" class="btn-link" id="multi_upload_button" style="border:none;padding:0;" data-onsuccess="set_profile_avatar" data-preset="Avatars">
								<img src="<?= trim($data['avatar']) ? URL::get_avatar() : URL::get_gravatar('dummy@ideabubble.ie', 150) ?>" id="edit-profile-cms-avatar" style="height: 153px; width: 153px;border-radius:2px;" />
								<span class="btn btn-primary btn-actions"><span class="icon-plus"></span> <?= __('Upload Photo') ?></span>
							</button>
							<input type="hidden" name="avatar" value="<?= $data['avatar'] ?>" id="edit-profile-avatar-filename" />
						</div>
					</div>
					<?php if ($gravatar_enabled): ?>
						<div<?= ( ! $use_gravatar) ? ' class="hidden"' : '' ?> id="profile-gravatar-controls" style="display: flex; align-items: center;">
							<div class="col-sm-2">
								<img src="<?= URL::get_gravatar($data['email'], 100); ?>" alt="Avatar" style="max-width: 100%;" />
							</div>
							<div class="col-sm-4">
								<a href="http://en.gravatar.com/emails/" target="_blank"><?= __('Change avatar with Gravatar') ?></a>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section>
			<h2><?= __('Personal Details') ?></h2>
			<div>
				<div class="form-group">
					<label class="sr-only" for="edit_profile_name"><?= __('First Name') ?></label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit_profile_name" name="name" placeholder="<?= __('First Name') ?>" value="<?= $data['name'] ?>"/>
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_surname">Last Name</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit_profile_surname" name="surname" placeholder="<?= __('Last Name') ?>" value="<?= $data['surname'] ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_email">Email</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit_profile_email" name="email" placeholder="Email" value="<?= $data['email'] ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_phone">Phone Number</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit_profile_phone" name="phone" placeholder="Phone Number" value="<?= $data['phone'] ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_address">Address</label>
					<div class="col-sm-12">
						<textarea class="form-control" id="edit_profile_address" name="address" rows="3" placeholder="Address"><?= $data['address'] ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_eircode">Eircode</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit_profile_eircode" name="eircode" placeholder="Eircode" value="<?= isset($data['eircode']) ? $data['eircode'] : '' ?>" />
					</div>
				</div>
			</div>
		</section>

		<!-- Permissions -->
		<!-- <section>
			<h2>Permissions</h2>
			<div>
				<div class="form-group">
					<label class="sr-only" for="edit_profile_role">Role</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit_profile_role" value="<?= $role->role ?>" disabled="disabled" />
					</div>
				</div>
			</div>
		</section> -->

		<!-- Timezone -->
		<!-- <section>
			<h2>Timezone</h2>
			<div>
				<div class="form-group">
					<label class="sr-only" for="edit_profile_timezone">Timezone</label>
					<div class="col-sm-12">
						<select class="form-control" id="edit_profile_timezone" name="timezone">
							<option value="">Please select</option>
							<?php foreach ($timezones as $key => $timezone): ?>
								<?php $selected = ($key == @$data['timezone']) ? ' selected="selected"' : '' ?>
								<option value="<?= $key ?>"<?= $selected ?>><?= $timezone ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</section> -->


		<!-- Password -->
		<section>
			<h2>Password</h2>
			<div>
				<div class="form-group">
					<label class="sr-only" for="edit_profile_password">Password</label>
					<div class="col-sm-12">
						<input placeholder="********" type="password" class="form-control" id="edit_profile_password" name="password" placeholder="Change Password" autocomplete="off" />
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_mpassword">Retype password</label>
					<div class="col-sm-12">
						<input  placeholder="********" type="password" class="form-control" id="edit_profile_mpassword" name="mpassword" placeholder="Retype Password" autocomplete="off" />
					</div>
				</div>
			</div>
		</section>

        <!-- User Preferences -->
		<section>
			<h2>Preferences</h2>

			<div>
				<?php if ($dashboards): ?>
					<div class="form-group">
						<label class="sr-only" for="edit_profile_default_dashboard">Default Dashboard</label>
						<div class="col-sm-12">
							<select class="form-control" id="edit_profile_default_dashboard" name="default_dashboard_id">
								<option value="-1" <?=$data['default_dashboard_id'] == -1 ? ' selected="selected"' : '' ?>>Use Main Dashboard</option>
								<option value="0" <?=$data['default_dashboard_id'] == 0 ? ' selected="selected"' : '' ?>>Use Role Dashboard</option>
								<?php foreach ($dashboards as $dashboard): ?>
									<option value="<?= $dashboard['id'] ?>"<?= $dashboard['id'] == $data['default_dashboard_id'] ? ' selected="selected"' : '' ?>><?= $dashboard['title'] ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label class="sr-only" for="edit_profile_default_home_page">Default Home Page</label>
					<div class="col-sm-12">
						<input type="input" class="form-control" id="edit_profile_default_home_page" name="default_home_page" placeholder="Default Home Page" value="<?= $data['default_home_page'] ?>"/>
					</div>
				</div>

				<div class="form-group">
					<label class="sr-only" for="user_column_profile">Display Options</label>
					<div class="col-sm-12">
						<?php $options = Model_Settings::column_toggle();?>
						<select class="form-control" id="user_column_profile" name="user_column_profile" >
							<option value=""<?= is_null($data['user_column_profile']) ? ' selected="selected"' : ''?>>Change Display Options</option>
							<?php foreach($options as $key=>$option):?>
								<option value="<?=$key?>" <?=$data['user_column_profile'] == $key ? ' selected="selected"' : ''?> ><?= $option ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="form-group hidden">
					<label class="sr-only" for="auto_logout_minutes">Inactive Logout <span style="font-size:10px">(minutes)</span></label>
					<div class="col-sm-6">
						<input type="input" class="form-control col-sm-1" id="auto_logout_minutes" name="auto_logout_minutes" value="<?= $data['auto_logout_minutes'] ?>"/>
					</div>
				</div>

				<?php if ($printing) { ?>
					<div class="form-group">
						<label class="sr-only" for="default_eprinter"><?=__('Default Printer')?></label>
						<div class="col-sm-6">
							<select class="form-control" name="default_eprinter">
								<option value=""><?=__('Select Default Printer')?></option>
								<?php foreach ($printers as $printer) { ?>
								<option value="<?=$printer['email']?>" <?=$data['default_eprinter'] == $printer['email'] ? 'selected="selected"' : ''?>><?=$printer['location'] . ' ' . $printer['tray'] . ' (' . $printer['email'] . ')'?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
			</div>
		</section>

        <?php
        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
        ?>
        <section>
            <h2>Email</h2>

            <div class="form-group">
                <label class="sr-only" for="default_messaging_signature">Default Message Signature</label>

                <div class="col-sm-12">
                    <textarea class="form-control" id="default_messaging_signature"
                              name="default_messaging_signature" rows="3"
                              placeholder="Default Message Signature"><?= html::entities(@$data['default_messaging_signature']) ?></textarea>
                </div>
            </div>

            <?php if (Settings::instance()->get('imap_per_user') == 1) { ?>
            <div>
                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-username">Username</label>

                    <div class="col-sm-9">
                        <input class="form-control" id="imap-username" type="text" name="imap[username]" value="<?=@$imap_settings['username']?>"
                               placeholder="Imap/Pop Username"/>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-password">Password</label>

                    <div class="col-sm-9">
                        <input class="form-control" id="imap-password" type="password" name="imap[password]" value="<?=@$imap_settings['password']?>"
                               placeholder="Imap/Pop Password"/>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-host">Host</label>

                    <div class="col-sm-9">
                        <input class="form-control" id="imap-host" type="text" name="imap[host]" value="<?=@$imap_settings['host']?>"
                               placeholder="Imap/Pop Host"/>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-port">Port</label>

                    <div class="col-sm-9">
                        <input class="form-control" id="imap-port" type="text" name="imap[port]" value="<?=@$imap_settings['port']?>"
                               placeholder="Imap/Pop Port"/>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-security">Imap/Pop Security</label>

                    <div class="col-sm-9">
                        <select class="form-control" id="imap-security" name="imap[security]">
                            <?= html::optionsFromArray(array('' => 'Imap/Pop Security None', 'SSL' => 'SSL', 'TLS' => 'TLS'), @$imap_settings['security']) ?>
                        </select>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-username">Imap/Pop Protocol</label>

                    <div class="col-sm-9">
                        <select class="form-control" id="imap-use_pop3" name="imap[use_pop3]">
                            <?= html::optionsFromArray(array('0' => 'Imap', '1' => 'Pop3'), @$imap_settings['use_pop3']) ?>
                        </select>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="sr-only" for="imap-auto_sync_minutes">Imap/Pop Sync Period(Mins)</label>

                    <div class="col-sm-9">
                        <input class="form-control" id="imap-auto_sync_minutes" type="text" name="imap[auto_sync_minutes]"
                               value="<?=@$imap_settings['auto_sync_minutes'] ? @$imap_settings['auto_sync_minutes'] : 10?>" placeholder="Sync Period(Mins)"/>
                    </div>
                </div>
                <?php } ?>
            </div>
        </section>
        <?
        }
        ?>

        <?php if (Settings::instance()->get('facebook_pixel_enabled_per_user')): ?>
            <section>
                <h2><?= __('Facebook Pixel') ?></h2>

                <div class="form-row gutters">
                    <div class="col-sm-3"><?= __('Facebook Pixel tracking') ?></div>

                    <div class="col-sm-4">
                        <div class="btn-group" data-toggle="buttons" id="edit-profile-facebook_pixel_enabled">
                            <label class="btn btn-default<?= $data['facebook_pixel_enabled'] ? ' active' : '' ?>">
                                <input type="radio"<?= $data['facebook_pixel_enabled'] ? ' checked' : '' ?> value="1" name="facebook_pixel_enabled">On
                            </label>

                            <label class="btn btn-default<?= ( ! $data['facebook_pixel_enabled']) ? ' active' : '' ?>">
                                <input type="radio" <?= ( ! $data['facebook_pixel_enabled']) ? ' checked' : '' ?> value="0" name="facebook_pixel_enabled">Off
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row gutters">
                    <label class="col-sm-3" for="edit-profile-facebook_pixel_code"><?= __('Facebook Pixel ID') ?></label>

                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="edit-profile-facebook_pixel_code" name="facebook_pixel_code" value="<?= $data['facebook_pixel_code'] ?>" />
                    </div>
                </div>
            </section>
        <?php endif; ?>

		<?php
        foreach ($extraSections as $extraSection) {
            $esData = $extraSection->getData($data['id']);
            $esView = View::factory($extraSection->getView(), $esData);
            echo $esView;
        }
        ?>
        <div class="form-action-group" id="ActionMenu">
            <button type="button" class="btn btn-primary profile_save_btn" data-redirect="save">Save</button>
            <button type="button" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit">Save &amp; Exit</button>
            <a href="/admin" class="btn btn-default">Cancel</a>
            <input type="reset" class="btn btn-cancel" />
        </div>
    </form>

	<div class="modal fade" id="form-submit-error-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Invalid details</h4>
				</div>
				<div class="modal-body" id="form-submit-error-message">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

</div>
<script type="text/javascript">
	$(".item_type_select_all").on("click", function(){
		var id = $(this).data("item-type-id");
		$("#alert_table").find(".item_type_" + id).prop('checked', ! this.checked);
		this.checked = ! this.checked;
	});

	$(".action_select_all").on("click", function(){
		var id = $(this).data("action-id");
		$("#alert_table").find(".action_" + id).prop('checked', ! this.checked);
		this.checked = ! this.checked;
	});
    $('.profile_save_btn').click(function()
    {
        $('#edit_profile_redirect').val(this.getAttribute('data-redirect'));
        $(this).parents('form').submit();
    });
	$('#edit-profile-form').on('submit', function(ev)
	{
		if (document.getElementById('edit_profile_name').value == '' || document.getElementById('edit_profile_surname').value == '')
		{
			ev.preventDefault();
			$('#form-submit-error-message').html('<p>A user must have both a first name and surname.</p>');
			$('#form-submit-error-modal').modal();
		}
	});

	$(document).ready(function()
	{
		$('[name="use_gravatar"]').on('change', function()
		{
			if (this.value == 1)
			{
				$('#profile-cms-avatar-controls').addClass('hidden');
				$('#profile-gravatar-controls').removeClass('hidden');
			}
			else
			{
				$('#profile-gravatar-controls').addClass('hidden');
				$('#profile-cms-avatar-controls').removeClass('hidden');
			}
		});
	});

	$('#change-avatar-btn').on('click', function(ev)
	{
		ev.preventDefault();
		if ($(this).hasClass('new'))
		{
			$('#change-avatar-upload-modal').modal();
		}
		else
		{
			existing_image_editor(this.src);
		}
	});

	$(document).on('show.bs.modal', '#select_preset_modal', function(ev)
	{
		ev.preventDefault();
		$('#preset_selector_prompt').find('[data-directory="avatars"]').prop('selected');
		$(this).find('#preset_selector_done_btn').click();
		// $(this).trigger('hide.bs.modal');
	});

	$(document).on(':ib-fileuploaded', '.upload_item', function()
	{
		// Get the path to the uploaded image, which has no preset applied
		var src = this.querySelector('img').src.replace('/_thumbs_cms/', '/');

		// Open the image editor, using the chosen image and the products preset
		existing_image_editor(
			src,
			'avatars',
			function(image)
			{
				if (image.file) {
					image = image.file;
				}
				$('#edit-profile-cms-avatar').attr('src', '/'+image+'?ts='+Date.now());
				$('#edit-profile-avatar-filename').val(image.split('/').pop());
			}
		);
		$('#upload_files_modal').modal('hide');
	});

	$(document).on('click', '#image-edit-save', function()
	{
		var $image = $('#edit-profile-cms-avatar');
		$image[0].src = $image[0].src+'?ts='+Date.now();
		$('#edit_image_modal').modal('hide');
	});

	function set_profile_avatar(file, preview)
	{
		$("#edit-profile-avatar-filename").val(file);
		$("#edit-profile-cms-avatar").attr("src", preview);
        $("[name=use_gravatar]").val("0");
        $("[name=use_gravatar]").prop("checked", false);
	}
</script>
