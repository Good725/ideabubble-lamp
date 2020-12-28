<form action="/admin/settings/save_layout/<?= $layout->id ?>" method="post" class="form-horizontal col-sm-12">
	<input type="hidden" name="id" value="<?= $layout->id ?>" />

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

    <div class="form-row gutters input_columns">
        <label class="sr-only" for="edit-layout-title">Title</label>

        <div class="col-sm-10">
            <input type="text" class="form-input" required="required" id="edit-layout-title" name="layout" value="<?= $layout->layout ?>" placeholder="Enter title" />
        </div>

        <div class="col-sm-2">
            <label>
                <span class="sr-only"><?= __('Publish') ?></span>
                <input type="hidden" name="publish" value="0" />
                <input type="checkbox" name="publish" value="1"<?= ($layout->publish == 1 || $layout->publish === null) ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Template</label>

        <div class="col-sm-5 col-md-4">
            <?php
            $selected = $layout->id ? $layout->template_id : $default_template->id;
            $options  = array('' => 'Please select');
            foreach ($templates as $template) {
                $options[$template->id] = $template->title;
            }
            echo Form::ib_select(null, 'template_id', $options, $selected);
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="panel-title left">View template header</h3>

            <div class="right">
                <button
                    type="button"
                    class="btn-link collapsed"
                    data-toggle="collapse"
                    data-target="#edit-layout-panel-body-header"
                    title="<?= __('Expand/collapse') ?>"
                    >
                    <span class="sr-only"><?= __('Expand/collapse') ?></span>
                    <span class="icon-angle-double-up"></span>
                </button>
            </div>
        </div>

        <div class="panel-body collapse" id="edit-layout-panel-body-header">
            <label class="sr-only" for="edit-layout-header">Header</label>

            <textarea class="form-control code_editor" id="edit-layout-header" name="header" rows="10" data-mode="application/x-httpd-php" disabled="disabled"
                ><?= htmlentities($layout->template->header) ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <label for="edit-layout-source">Layout section</label>

            <label class="right">
                Overwrite layout
                <input type="checkbox" name="use_db_source" value="1"<?= ($layout->use_db_source == 1) ? ' checked="checked"' : '' ?> id="edit-layout-source-toggle" />
            </label>

            <textarea class="form-control code_editor" id="edit-layout-source" name="source" rows="30" data-mode="application/x-httpd-php"><?= htmlentities($layout->source) ?></textarea>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="panel-title left">View template footer</h3>

            <div class="right">
                <button
                    type="button"
                    class="btn-link collapsed"
                    data-toggle="collapse"
                    data-target="#edit-layout-panel-body-footer"
                    title="<?= __('Expand/collapse') ?>"
                    >
                    <span class="sr-only"><?= __('Expand/collapse') ?></span>
                    <span class="icon-angle-double-up"></span>
                </button>
            </div>
        </div>

        <div class="panel-body collapse" id="edit-layout-panel-body-footer">
            <label class="sr-only" for="edit-layout-footer">Footer</label>

            <textarea class="form-control code_editor" id="edit-layout-footer" name="footer" rows="10" data-mode="application/x-httpd-php" disabled="disabled"
                ><?= htmlentities($layout->template->footer) ?></textarea>
        </div>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <?php if ($layout->id): ?>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#layout-delete-modal">Delete</button>
        <?php endif; ?>
    </div>
</form>

<?php if ($layout->id): ?>
    <div class="modal" tabindex="-1" role="dialog" id="layout-delete-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete this layout?</p>
                </div>

                <div class="modal-footer">
                    <a href="/admin/settings/delete_layout/<?= $layout->id ?>" class="btn btn-danger">Delete</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    $('#edit-layout-source-toggle').on('change', function() {
        var $source   = $('#edit-layout-source');
        var cm_editor = $source.find('\+.CodeMirror')[0].CodeMirror;
        var disabled  = ! $(this).is(':checked');

        $source.prop('disabled', disabled);
        cm_editor.setOption('readOnly', disabled);
        cm_editor.refresh();
    });
</script>