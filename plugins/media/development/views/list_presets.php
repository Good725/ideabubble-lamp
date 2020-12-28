<?= @$alert; ?>
<table class='table table-striped dataTable'>
	<thead>
		<tr>
			<th>Title</th>
			<th>Directory</th>
			<th>Width x Height (Preset)</th>
			<th>Action (Preset)</th>
			<th>Thumb</th>
			<th>Width x Height (Thumb)</th>
			<th>Action (Thumb)</th>
			<th>Last Modified</th>
			<th>Modified By</th>
			<th>Publish</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($presets_items as $id => $preset_item): ?>
		<tr>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?=$preset_item['title']?>
				</a>
			</td>
			<td><a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>"><?php echo $preset_item['directory']; ?></a></td>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?php echo $preset_item['width_large'].' x' .$preset_item['height_large']; ?>
				</a>
			</td>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?php echo $preset_item['action_large']; ?>
				</a>
			</td>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?php echo (($preset_item['thumb'])? 'Yes' : 'No'); ?>
				</a>
			</td>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?php echo $preset_item['width_thumb'].' x' .$preset_item['height_thumb']; ?>
				</a>
			</td>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?php echo $preset_item['action_thumb']; ?>
				</a>
			</td>
			<td><a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>"><?php echo $preset_item['date_modified']; ?></a></td>
			<td>
				<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger_<?=$preset_item['id']?>">
					<?php echo $preset_item['modified_by_role'].' '.$preset_item['modified_by_name']; ?>
				</a>
			</td>
			<td id="publish_<?=$preset_item['id']?>" class="publish" data-item_publish="<?php echo $preset_item['publish'];?>">
				<?php echo (($preset_item['publish'] == '1')? '<i class="icon-ok"></i>' : '<i class="icon-ban-circle"></i>')?>
			</td>
			<td id="delete_<?=$preset_item['id']?>" class="delete_preset"><i class="icon-remove-circle"></i></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>


<!-- Presets Editor window-->
<div class="modal fade" id="media_presets_editor">
	<div class="modal-dialog">
		<div class="modal-content form-horizontal">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3><span id="preset_editor_action">Add</span> Media Preset <span id="preset_editor_title"></span></h3>
				<div class="form-group" id="preset_editor_alert_area">
				</div>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="col-sm-3 control-label">Title</div>
					<div class="col-sm-8">
						<input class="form-control" id="item_title" type="text" name="item_title" value=""/>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 control-label">Directory</div>
					<div class="col-sm-8">
						<select class="form-control" id="item_directory" name="item_category_id">
							<option value="0">-- Select Preset Directory --</option>
							<?php echo Model_Presets::get_preset_directories_as('options');?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 control-label">Preset Width (large)</div>
					<div class="col-sm-4">
						<div class="input-group">
							<input class="form-control" id="item_width_large" type="text" name="item_width_large" value=""/>
							<div class="input-group-addon">px</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 control-label">Preset Height (large)</div>
					<div class="col-sm-4">
						<div class="input-group">
							<input class="form-control" id="item_height_large" type="text" name="item_height_large" value=""/>
							<div class="input-group-addon">px</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 control-label">Preset Action (large)</div>
					<div class="col-sm-8">
						<select class="form-control" id="item_action_large" name="item_action_large">
							<option value="0">-- Select Preset Action --</option>
							<?php echo Model_Presets::get_preset_actions_as('options');?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 control-label">Create Thumbnail</div>
					<div class="col-sm-8">
						<input id="item_thumb"
							   type="checkbox"
							   name="item_thumb"
							   value="1"
							   onclick="toggleOptDetails('item_thumb');"
							/>
					</div>
					<div id="item_thumb_details" style="clear:both;display:none;">
						<div class="form-group">
							<div class="col-sm-3 control-label">Thumb Width (small)</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input class="form-control" id="item_width_thumb" type="text" name="item_width_thumb" value="" />
									<div class="input-group-addon">px</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-3 control-label">Thumb Height (small)</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input class="form-control" id="item_height_thumb" type="text" name="item_height_thumb" value="" />
									<div class="input-group-addon">px</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-3 control-label">Thumb Action</div>
							<div class="col-sm-8">
								<select class="form-control" id="item_action_thumb" name="item_action_thumb">
									<option value="0">-- Select Thumb Action --</option>
									<?php echo Model_Presets::get_preset_actions_as('options');?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3 control-label">Publish</div>
					<div class="col-sm-3">
						<select class="form-control" id="item_publish" name="item_publish">
							<option value="1" selected="selected">Yes</option>
                            <option value="0">No</option>
						</select>
					</div>
				</div>
				<input type="hidden" id="item_id" name="item_id" value="" />
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-primary" id="btn_save_preset">Save</a>
				<span id="delete_preset_holder" style="display:none;">
					<a href="#" class="btn btn-danger" id="btn_delete_preset_yes">Delete</a>
				</span>
			</div>
		</div>
	</div>
</div>


<!-- Confirm window-->
<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Are you sure you wish to delete this Preset?</h3>
			</div>

			<div class="modal-body">
				<p><strong>Please Note</strong> that this <strong>CANNOT</strong> be <strong>UNDONE</strong>.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes" data-item_id="">Delete</a>
			</div>

		</div>
	</div>
</div>