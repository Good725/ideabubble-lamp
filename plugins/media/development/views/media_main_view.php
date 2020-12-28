<? /*<script src="<?php echo URL::get_engine_plugin_assets_base('media'); ?>js/jquery-ui-1.8.18.custom.min.js"></script> */ ?>
<link href="<?php echo URL::get_engine_plugin_assets_base('media'); ?>css/smoothness/jquery-ui-1.8.18.custom.css"
      rel="stylesheet" type="text/css"/>
<link href="<?php echo URL::get_engine_plugin_assets_base('media'); ?>css/media_list.css" rel="stylesheet"
      type="text/css"/>

<?= isset($alert) ? $alert : ''; ?>

<?php
if ( ! empty($selectionDialog) OR Auth::instance()->has_access('media'))
{
	include 'multiple_upload.php';
}
?>

<?= $media_all_list ?>

<?php if (@$selectionDialog) { ?>
	<a href="/admin/media/multiple_upload?dialog=yes" id="upload_link"><button type="button" class="btn">Upload Image</button></a>
	<button class="btn btn-default close-dialog">Close</button>
<?php } ?>

<!-- Media Uploader window-->
<div class="modal fade" id="media_uploader">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3><span id="media_uploader_action">Upload</span> Media <span id="media_uploader_type">Item</span> <span
						id="media_uploader_title"></span></h3>

				<div class="row" id="media_uploader_alert_area">
				</div>
			</div>
			<div class="modal-body">
				<form name="media_uploader_form" id="media_uploader_form" action="/admin/media/upload_media_item" method="post"
					  enctype="multipart/form-data">
					<div class="row">
						<div class="span2" id="file_to_upload_label">Select File to Upload</div>
						<div class="span8">
							<input type="file" name="file_to_upload"/>
						</div>
					</div>

					<div class="row" id="presets_area">
						<div class="span2">Select Preset to Upload</div>
						<div class="span8">
							<select name="preset_id" id="preset_id" onchange="set_preset_details('preset_id');">
								<option value="0"
										data-title="" data-directory=""
										data-height_large="" data-width_large="" data-action_large=""
										data-thumb="" data-height_thumb="" data-width_thumb="" data-action_thumb="">-- Select
									Preset --
								</option>
								<?php echo Model_Presets::get_presets_items_as('options');?>
							</select>
						</div>
						<input type="hidden" name="preset_title" id="preset_title" value=""/>
						<input type="hidden" name="preset_directory" id="preset_directory" value=""/>
						<input type="hidden" name="preset_height_large" id="preset_height_large" value=""/>
						<input type="hidden" name="preset_width_large" id="preset_width_large" value=""/>
						<input type="hidden" name="preset_action_large" id="preset_action_large" value=""/>
						<input type="hidden" name="preset_thumb" id="preset_thumb" value=""/>
						<input type="hidden" name="preset_height_thumb" id="preset_height_thumb" value=""/>
						<input type="hidden" name="preset_width_thumb" id="preset_width_thumb" value=""/>
						<input type="hidden" name="preset_action_thumb" id="preset_action_thumb" value=""/>
					</div>
					<input type="hidden" name="media_tab_preview" id="media_tab_preview" value="photos"/>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<!--		<a href="#" class="btn btn-primary" id="btn_save_preset">Upload <span id="media_upload_item_title"></span></a>-->
				<button class="btn btn-primary" id="btn_upload_media_item" onclick="$('form#media_uploader_form').submit();">
					Upload <span id="media_upload_item_title"></span></button>
			</div>
		</div>
	</div>
</div>
<!-- //Media Uploader window-->


<!-- Confirm Delete window-->
<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Are you sure you wish to delete this Media Item?</h3>
			</div>
			<div class="modal-body">
				<p>
					<strong>Please Note</strong> that this <strong>CAN NOT</strong> be <strong>UNDONE</strong>,
					and the Media Item will be <strong>completely</strong> removed from the Media Filesystem and Database.
				</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>
		</div>
	</div>
</div>
<!-- //Confirm Delete window-->