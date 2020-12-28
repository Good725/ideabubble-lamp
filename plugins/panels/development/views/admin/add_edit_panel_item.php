<form class="col-sm-12 form-horizontal" name="form_panel_add_edit" id="form_panel_add_edit" action="/admin/panels/process_editor/" method="post">
    <input type="hidden" id="plugin_name" name="plugin" value="panels" />
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
    <div class="form-group">
		<div class="col-sm-7" id="page_edit_name">
			<label class="sr-only" for="panel_title">Title</label>
			<input id="panel_title" type="text" name="panel_title" value="<?php echo @$panel_data['title'];?>" class="form-control ib_text_title_input name_input" />
		</div>
    </div>

    <div class="col-sm-12 tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Details</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
				<div>

					<? /*
					<button id="multi_upload_button" type="button" class="btn" style="float:left;">Upload Images</button>
					<button id="add_existing_image_button" type="button" class="btn">Add Existing Image</button>
					*/ ?>

					<div class="form-group">
						<label class="col-sm-2 control-label"for="panel_type_id">Type</label>
						<div class="col-sm-4">
							<select class="form-control" id="panel_type_id" name="type_id" onchange="togglePanelType(this.value);">
								<?= @$panel_type_options ?>
							</select>
						</div>
					</div>

					<div id="panelStaticDiv" style="display: <?= (@$panel_data['type'] == 'static') ? 'block' : 'none'; ?>;">

						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10 panel-drag-and-drop">
								<?= View::factory('multiple_upload') ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"for="panel_image">Image</label>
							<div class="col-sm-4">
								<select class="form-control" id="panel_image" name="panel_image">
									<option value="0">-- Select Image --</option>
									<option disabled="disabled">Actions:</option>
									<option value="browse">Browse Media</option> -->
									<!-- <option value="upload">Upload</option> -->
									<option disabled="disabled">Existing Images:</option>
									<?php
									echo Model_Media::factory('Media')->get_all_items_based_on(
										'location',
										'panels',
										'as_options',
										'=',
										@$panel_data['image']);
									?>
								</select>
							</div>
							<div class="col-sm-4" id="imagePreview">
								<?php if(isset($panel_data['image']) AND !empty($panel_data['image'])){?>
									<img src="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$panel_data['image'], 'panels/_thumbs_cms');?>" alt="<?php echo $panel_data['image'];?>"/>
								<?php }?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="panel_link">Link</label>

							<div class="col-sm-4">
								<select class="form-control" id="panel_link" name="panel_link" onchange="linkChange(this.value)">
									<option value="">-- Select Link URL --</option>
									<option value="0"<?php echo (isset($panel_data['link_id']) AND $panel_data['link_id'] == 0)? ' selected="selected"':'';?>>External URL</option>
									<?php echo Model_Pages::get_pages_as_options(@$panel_data['link_id']);?>
								</select>
							</div>

							<div class="col-sm-4">
								<input class="form-control" type="text" id="panel_link_url" name="panel_link_url"
									   value="<?php echo @$panel_data['link_url'];?>" size="25"
									<?php echo (!isset($panel_data['link_url']) AND empty($panel_data['link_url']))?' style="display:none"':""?> />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"for="panel_text">Text</label>
							<div class="col-sm-10">
								<textarea class="form-control" rows="" cols="" name="panel_text" id="panel_text" ><?= @$panel_data['text']; ?></textarea>
							</div>
						</div>

					</div><!--panelStaticDiv-->

					<div id="panelViewDiv" style="display: <?= (@$panel_data['type'] == 'view') ? 'block' : 'none'; ?>;">
						<div class="form-group">
							<label class="col-sm-2 control-label"for="panel_view">Path to View</label>
							<div class="col-sm-4">
								<input class="form-control" id="panel_view" type="text" name="view" value="<?= @$panel_data['view']; ?>" />
							</div>
						</div>
					</div><!-- panelPredefinedDiv -->

					<div id="panelPredefinedDiv" style="display: <?= (@$panel_data['type'] == 'predefined') ? 'block' : 'none'; ?>;">
						<div class="form-group">
							<label class="col-sm-2 control-label"for="panel_predefined_id">Predefined Panels</label>
							<div class="col-sm-4">
								<select class="form-control" id="panel_predefined_id" name="predefined_id">
									<?= @$predefined_options ?>
								</select>
							</div>
						</div>
					</div><!-- panelPredefinedDiv -->

					<div id="panelCustomDiv" style="display: <?= (@$panel_data['type'] == 'custom') ? 'block' : 'none'; ?>;">
						<input type="hidden" id="panel_sequence_id" value="<?=@$panel_data['sequence_id']?>" />
						<?=(isset($panel_data['panel_data']['panel_sequence_editor_view']))? $panel_data['panel_data']['panel_sequence_editor_view'] : ''?>
					</div>

					<div id="plugin_independent_fields">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="panel_date_publish">Start date</label>
							<div class="col-sm-4">
								<input name="panel_date_publish" type="text" id="panel_date_publish" class="form-control datetimepicker" value="<?php echo @$panel_data['date_publish'];?>" size="20" readonly="readonly" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="panel_date_remove">End date</label>
							<div class="col-sm-4">
								<input name="panel_date_remove" type="text" id="panel_date_remove" class="form-control datetimepicker" value="<?php echo @$panel_data['date_remove'];?>" size="20" readonly="readonly" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="panel_order_no">Order</label>
							<div class="col-sm-4">
								<input class="form-control" name="panel_order_no" type="text" id="panel_order_no" value="<?php echo @$panel_data['order_no'];?>" size="3"/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="panel_position">Location</label>
							<div class="col-sm-4">
								<select class="form-control" id="panel_position" name="panel_position" class="">
									<option value="0">-- Select Position --</option>
									<?php echo Model_Panels::get_template_positions_as('options', @$panel_data['position'])?>
								</select>
								<? /* NOT REQUIRED AT THE MOMENT @TODO: to be further developed if required at a later stage ?>
							&nbsp;<input type="input" name="panel_new_position" value="" placeholder="Or Add New Position" size="15" />
							<? */?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"for="panel_publish">Publish</label>
							<div class="col-sm-4">
								<select class="form-control" id="panel_publish" name="panel_publish">
									<option value="1"<?php echo (@$panel_data['publish'] == '1') ? ' selected="selected"' : '';?>>Yes</option>
									<option value="0"<?php echo (@$panel_data['publish'] == '0') ? ' selected="selected"' : '';?>>No</option>
								</select>
							</div>
						</div>

					</div>
				</div>

            </div>


			<?php if(isset($panel_data['id']) AND $panel_data['id'] > 0): ?>
				<div class="tab-pane" id="tab3">
					<div class="form-group">
						<iframe id="panel_live_preview" src="/admin/panels/panel_preview/<?=$panel_data['id']?>" style="width:100%; height:500px;"></iframe></div>
					</div>
				</div>
			<?php endif; ?>
        </div>

	<input type="hidden" value="<?php echo @$panel_id?>" name="panel_id" id="panel_id"/>
	<input type="hidden" value="<?php echo (isset($panel_data['id']) AND $panel_data['id'] > 0)? 'edit' : 'add';?>" name="editor_action" id="editor_action"/>
	<input type="hidden" value="/admin/panels/add_edit_item" name="editor_redirect" id="editor_redirect"/>

    <div id="ActionMenu" class="floatingMenu">
        <a class="btn btn-primary" id="btn_save">Save</a>
        <a class="btn btn-primary" id="btn_save_exit">Save &amp; Exit</a>
        <a class="btn btn-primary" href="">Reset</a>
	<?php if(isset($panel_data['id']) AND $panel_data['id'] > 0){ ?>
		<a class="btn btn-danger" id="btn_delete">Delete</a>
	<?php }?>
    </div>
    <div class="floating-nav-marker"></div>

    <!-- Confirm window-->
    <div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Are you sure you wish to delete this Panel?</h3>
				</div>
				<div class="modal-body">
					<p></p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
    </div>
</form>

<!-- Validation failed -->
<div class="modal fade" id="validation_failed">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p>
					Please be sure to give a title for the panel and select the panel position before saving the panel.
				</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" id="btn_review">Review</a>
			</div>
		</div>
	</div>
</div>
<div id="image_editor_holder"></div>
<div id="browse_images_holder"></div>


<script type="text/javascript">
    // @TODO Just load the relevant form, rather than selectively hide the unnecessary ones
    function togglePanelType(value) {
        var name = $('#panel_type_id').find('[value='+value+']').attr('data-name');

        switch(name) {
            case 'none':
                $('#panelStaticDiv').hide();
                $('#panelCustomDiv').hide();
                $('#panelViewDiv').hide();
                $('#panelPredefinedDiv').hide();
                $('#plugin_independent_fields').hide();
                break;
            case 'static':
                $('#panelStaticDiv').show();
                $('#panelCustomDiv').hide();
                $('#panelViewDiv').hide();
                $('#panelPredefinedDiv').hide();
                $('#plugin_independent_fields').show();
                break;
            case 'custom':
                $('#panelStaticDiv').hide();
                load_custom_panel_editor_view();
                $('#panelCustomDiv').show();
                $('#panelViewDiv').hide();
                $('#panelPredefinedDiv').hide();
                $('#plugin_independent_fields').show();
                break;
            case 'view':
                $('#panelStaticDiv').hide();
                $('#panelCustomDiv').hide();
                $('#panelViewDiv').show();
                $('#panelPredefinedDiv').hide();
                $('#plugin_independent_fields').show();
                break;
            case 'predefined':
                $('#panelStaticDiv').hide();
                $('#panelCustomDiv').hide();
                $('#panelViewDiv').hide();
                $('#panelPredefinedDiv').show();
                $('#plugin_independent_fields').show();
                break;
        }
    }

    function load_custom_panel_editor_view() {
          $.post(
            '/admin/customscroller/ajax_get_custom_sequence_editor_view/',
            {
                plugin_item_id : $('#panel_id').val(),
                plugin_name	   : 'panels',
                sequence_id    : <?=isset($panel_data['sequence_id']) ? $panel_data['sequence_id'] : '\'\''?>
            },
            function(result) {
                if(result.err_msg == '') {
                    $('#panelCustomDiv').html(result.cs_editor_view);
                } else {
                    $('#panelCustomDiv').html(result.err_msg);
                }

                if ('<?=isset($panel_data['sequence_id']) ? $panel_data['sequence_id'] : ''?>' != '')
                {
                    update_sequence_information('current');
                }
            },
            'json'
        );
    }
	$('#panel_type_id').trigger('change');
</script>
