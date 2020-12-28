<script>

	$(document).ready(function(){

		$('input.main').click(function(){

			$checkboxes = $('div#controller_' + $(this).data('id')).find(':checkbox');

			console.log($checkboxes);

			if ($(this).is(':checked')) {
				$checkboxes.prop('checked', true);
			}
			else {
				$checkboxes.prop('checked', false);
			}


		});

		$('div#code_pices a.check').click(function(){

			$checkboxes = $('div#code_pices').find(':checkbox');

			$checkboxes.prop('checked', true);

		});

		$('div#code_pices a.uncheck').click(function(){

			$checkboxes = $('div#code_pices').find(':checkbox');

			$checkboxes.prop('checked', false);

		});

	});


</script>
<style>
	.permissions_form label{display:block;}
</style>

<?php $published = ($role->publish == 1 OR $role->publish == ''); ?>
<div class="col-sm-12">
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

	<form action="<?= URL::Site('admin/settings/edit_role/'.$role->id) ?>" method="post">

		<label for="edit_role_role" style="position:absolute;top:-9999px;left:-9999px;">Name</label>
		<input type="text" id="edit_role_role" name="role" class="form-control ib_text_title_input" value="<?= $role->role; ?>" style="margin-bottom:10px;">

		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-details" data-toggle="tab">Details</a></li>
				<li><a href="#tab-permissions" data-toggle="tab">Permissions</a></li>
                <li><a href="#tab-plugins" data-toggle="tab">Plugins</a></li>
			</ul>

			<div class="tab-content" style="overflow:initial;padding-bottom:10px;">
				<!-- Details tab -->
				<div class="tab-pane active" id="tab-details">
					<div class="form-horizontal">
						<!-- Publish -->
						<div class="form-group">
							<label class="col-sm-2 control-label">Publish</label>

							<div class="col-sm-9">
								<div data-toggle="buttons" class="btn-group">
									<label class="btn<?= $published ? ' active' : '' ?>">
										<input type="radio" name="publish" value="1"<?= $published ? ' checked="checked"' : '' ?>>Yes
									</label>
									<label class="btn<?= ( ! $published) ? ' active' : '' ?>">
										<input type="radio" name="publish" value="0"<?= ( ! $published) ? ' checked="checked"' : '' ?>>No
									</label>
								</div>
							</div>
						</div>

						<!-- Description -->
						<div class="form-group">
							<label for="edit_role_description" class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10">
								<textarea class="ckeditor" id="edit_role_description" name="description"><?= $role->description ?></textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="description" class="col-sm-2 control-label">Access Type</label>

							<div class="col-sm-5">
								<select class="form-control popinit" data-original-title="Type" rel="popover" data-content="This is the type of access you will have on the CMS." name="access_type" id="access_type">
                                    <option value="">-- Please select --</option>
									<option <?= $role->access_type == 'Both'      ? 'selected="selected" ' : ''; ?> value="Both">Both</option>
									<option <?= $role->access_type == 'Front end' ? 'selected="selected" ' : ''; ?> value="Front end">Front end</option>
									<option <?= $role->access_type == 'Back end'  ? 'selected="selected" ' : ''; ?> value="Back end">Back end</option>
								</select>
							</div>
						</div>

						<!-- Master Group -->
						<div class="form-group">
							<label class="col-sm-2 control-label">Master Group</label>

							<div class="col-sm-9">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default<?= ($role->master_group) ? ' active' : '' ?>">
                                        <input type="radio"<?= ($role->master_group) ? ' checked="checked"' : '' ?> value="1" name="master_group">Yes
                                    </label>
                                    <label class="btn btn-default<?= ( ! $role->master_group) ? ' active' : '' ?>">
                                        <input type="radio"<?= ( ! $role->master_group) ? ' checked="checked"' : '' ?> value="0" name="master_group">No
                                    </label>
                                </div>
							</div>
						</div>

						<?php if ($dashboards): ?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="edit_profile_default_dashboard">Default Dashboard</label>
								<div class="col-sm-5">
									<select class="form-control" id="edit_profile_default_dashboard" name="default_dashboard_id">
										<option value="-1">Use Main Dashboard</option>
										<option value="0">Use Role Dashboard</option>
										<?php foreach ($dashboards as $dashboard): ?>
											<option value="<?= $dashboard['id'] ?>"<?= $dashboard['id'] == $role->default_dashboard_id ? ' selected="selected"' : '' ?>><?= $dashboard['title'] ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						<?php endif; ?>

					</div>

					<?php if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){ ?>
					<!-- Activity Alerts -->
					<div class="form-group">
						<label class="col-sm-2 control-label">Activity Alerts</label>
			
						<table class="table" id="alert_table">
							<thead>
								<tr>
								<th></th>
								<?php foreach($activity_item_types as $activity_item_type){ ?>
								<th><a class="item_type_select_all" data-item-type-id="<?=$activity_item_type['id']?>"><?=$activity_item_type['name']?></a></th>
								<?php } ?>
								</tr>
							</thead>
							<tbody>
							<?php foreach($activity_actions as $activity_action){ ?>
								<tr>
									<th><a class="action_select_all" data-action-id="<?=$activity_action['id']?>"><?=$activity_action['name']?></a></th>
								<?php foreach($activity_item_types as $activity_item_type){ ?>
									<td><input class="item_type_<?=$activity_item_type['id']?> action_<?=$activity_action['id']?>" type="checkbox" name="activity_alert[<?=$activity_action['id']?>][<?=$activity_item_type['id']?>]" value="1" <?=isset($activity_alerts[$activity_action['id']][$activity_item_type['id']]) ? 'checked="checked"' : ''?> /></td>
								<?php } ?>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
					<?php } ?>
				</div>

				<!-- Permissions tab -->
				<div class="tab-pane" id="tab-permissions">
                    <div class="span6">
                        <h2>Controllers / Actions</h2>
                        <?php foreach($controllers as $controller): ?>
                            <b><?=$controller->name; ?></b>
                            <label>
                                <input type="checkbox" name="" value="" class="main" data-id="<?=$controller->id; ?>"/> check all
                            </label>

                            <div style="margin-left: 20px;" id="controller_<?=$controller->id; ?>">
                                <label>
                                    <input type="checkbox"<?= $role->has('resource', $controller->id) ? ' checked="checked"' : '' ?> name="resource[<?=$controller->id; ?>]" value="1" />
                                    <?= $controller->name ?> <span style="color: #999;">(<?= $controller->alias ?>)</span>
                                </label>

                                <?php foreach ($controller->get_actions_4_controller() as $action): ?>

                                    <label title="<?= html::entities($action->description) ?>">
                                        <input type="checkbox"<?= $role->has('resource', $action->id) ? ' checked="checked"' : '' ?> name="resource[<?=$action->id; ?>]" value="1" />
                                        <?=$action->name; ?> <span style="color: #999;">(<?=$action->alias; ?>)</span>
                                    </label>

                                <?php endforeach; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <div id="code_pices" class="span6">
                        <h2>Code pieces</h2>
                        <p><a class="check" href="#">check all</a> / <a class="uncheck" href="#">uncheck all</a></p>

                        <?php foreach($code_pieces as $code): ?>

                            <label>
                                <input type="checkbox" <?= $role->has('resource', $code->id) ? 'checked' : '' ?> name="resource[<?=$code->id; ?>]" value="1" />
                                <?=$code->name; ?> <span style="color: #999;">(<?=$code->alias; ?>)</span>
                            </label>

                        <?php endforeach; ?>
                    </div>
				</div>

                <div class="tab-pane active" id="tab-plugins">
                    <div class="form-horizontal">
                        <table class='table table-striped'>
                            <!-- Header -->
                            <thead>
                            <tr>
                                <th>Active Plugins</th>
                            </tr>
                            </thead>

                            <!-- Body -->
                            <tbody>
                            <?php
                            foreach ($plugins as $plugin) {
                                if (@$matrix[$plugin['id']][$role->id]) {
                                    ?>
                                    <tr><td><?=$plugin['name']?></td></tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

			</div>
		</div>

		<div class="form-actions">
			<button class="btn btn-primary" name="save" value="1" type="submit">Save</button>
			<button class="btn btn-success" name="save_and_exit" value="1" type="submit">Save &amp; Exit</button>
			<a class="btn btn-danger" data-toggle="modal" href="#delete_modal">Delete</a>
			<a class="btn btn-default" href="<?= URL::Site('admin/settings/manage_roles/'); ?>">Cancel</a>
		</div>

		<!--
		   ===================================
		   Modal Box for comment adding.
		   id: comment_modal
		   ===================================
		   -->

		<div class="modal fade" id="delete_modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<a class="close" data-dismiss="modal">&times;</a>

						<h3>Delete Role?</h3>
					</div>
					<div class="modal-body">
						<p>Warning: This cannot be undone.</p>
					</div>
					<div class="modal-footer">
						<button class="btn btn-danger" type="submit" id="delete_submit" name="delete" value="delete">Delete</button>
						<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</form>

</div>
<script>
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

	$(document).ready(function()
	{
		$('.master_checkbox').change(function()
		{
			$(this).parents('tbody').find('[type="checkbox"]').prop('checked', this.checked);
		});
	});
</script>
