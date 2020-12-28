<style type="text/css">
    .edit_cron_plugin_settings{border:1px solid #aaa;display:inline-block;margin:-20px 10px 10px 100px;padding:0 10px;}
    .edit_cron_plugin_settings .form-group{margin-left:-70px;}
    .edit_cron_plugin_settings legend{border:none;font-size:14px;margin:0;padding:0 10px;width:auto;}
</style>
<div class="col-sm-12 header">
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
</div>

<form class="col-sm-12 form-horizontal">
    <input type="hidden" id="edit_cron_id" name="id" />

    <div class="form-group">
        <label for="edit_cron_title" class="sr-only">Title</label>
        <input id="edit_cron_title" type="text" class="form-control ib_text_title_input required" placeholder="Enter title" />
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#config_tab" data-toggle="tab">Config</a></li>
        <li><a href="#activity_tab" data-toggle="tab">Activity</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="config_tab">

            <div class="form-group">
                <div class="col-sm-2 control-label"><label for="edit_cron_plugin">Plugin</label></div>
                <div class="col-sm-7">
                    <select class="form-control" id="edit_cron_plugin" name="plugin_id">
                        <option value="">Select Plugin</option>
                    </select>
                    <p style="margin:0;"><a id="view_plugin_settings" href="#">View plugin settings</a></p>
                </div>
            </div>

            <fieldset id="edit_cron_plugin_settings" class="edit_cron_plugin_settings" style="display:none;">
                <legend>Plugin Settings</legend>
                <div class="form-group">
                    <div class="col-sm-2 control-label"><label for="edit_cron_url">URL</label></div>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" id="edit_cron_url" name="url" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label"><label for="edit_cron_file_name">File Name</label></div>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" id="edit_cron_file_name" name="file_name" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label"><label for="edit_cron_file_type">File Type</label></div>
                    <div class="col-sm-7">
                        <select class="form-control" id="edit_cron_file_type" name="file_type">
                            <option value="">Select Type</option>
                            <option value="csv">CSV</option>
                            <option value="xml">XML</option>
                            <option value="html">HTML</option>
                        </select>
                    </div>
                </div>
            </fieldset><!-- #edit_cron_plugin_settings -->

            <div class="form-group">
                <div class="col-sm-2 control-label"><label for="edit_cron_frequency">Frequency</label></div>
                <div class="col-sm-7">
                    <select class="form-control" id="edit_cron_frequency" name="frequency_id">
                        <option value="">Select Frequency</option>
                    </select>
                    <label for="edit_cron_start_time" class="sr-only">Start Time</label>
                    <input type="text" id="edit_cron_start_time" name="start_time" placeholder="Enter Start Time" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2 control-label">Publish</div>
                <div class="col-sm-7">
					<?= html::toggle_button('publish', 'Publish', 'Unpublished', TRUE); ?>
                </div>
            </div>

        </div>

        <div class="tab-pane active" id="activity_tab">

        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-primary" data-redirect="save">Save</button>
        <button type="button" class="btn btn-primary" data-redirect="save_and_exit">Save &amp; Exit</button>
        <a class="btn" href="/admin/settings/crons">Cancel</a>
        <button type="reset"  class="btn">Reset</button>
        <?php if (isset($data['id']) AND $data['id'] != '') : ?>
            <a class="btn btn-danger" id="btn_delete" data-id="<?=$data['id']?>">Delete</a>
        <?php endif; ?>
    </div>

    <?php if (isset($data['id']) AND $data['id'] != '') : ?>
        <div id="delete_cron_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete_cron_modal_label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="delete_cron_modal_label">Delete Cron</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this Cron?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</form>

<script>
    $('#view_plugin_settings').on('click', function()
    {
        var plugin_settings = $('#edit_cron_plugin_settings');
        plugin_settings.is(':visible') ? plugin_settings.hide() : plugin_settings.show();
    });
</script>
