<form action="/admin/settings/save_template/<?= $template->id ?>" method="post" class="form-horizontal col-sm-12">
    <input type="hidden" name="id" value="<?= $template->id ?>" />

    <?= (isset($alert)) ? $alert : '' ?>

    <div class="form-row guters input_columns">
        <div class="col-sm-10">
            <?= Form::ib_input('Enter title', 'title', $template->title, array('required' => 'required', 'class' => 'validate[required]', 'id' => 'edit-template-title')); ?>
        </div>

        <div class="col-sm-2">
            <label>
                <span class="sr-only"><?= __('Publish') ?></span>
                <input type="hidden" name="publish" value="0" />
                <input type="checkbox" name="publish" value="1"<?= ($template->publish == 1 || $template->publish === null) ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Stub</label>

        <div class="col-sm-5">
            <?php
            $attributes = array();
            if ($template->id) $attributes['readonly'] = 'readonly';
            echo Form::ib_input(null, 'stub', $template->stub, $attributes);
            ?>
        </div>
        <div class="col-sm-5 control-label text-left">This name is used for reference in the code</div>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#edit-template-tab--html_php">HTML / PHP</a></li>
        <li><a data-toggle="tab" href="#edit-template-tab--css">CSS</a></li>
    </ul>

    <div class="tab-content">
        <div id="edit-template-tab--html_php" class="tab-pane fade in active">
            <div class="form-group">
                <div class="col-sm-12">
                    <label for="edit-template-header">Header</label>
                    <textarea class="form-control code_editor" id="edit-template-header" data-mode="application/x-httpd-php" rows="15" name="header"
                        ><?= htmlentities($template->header) ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="edit-template-footer">Footer</label>
                    <textarea class="form-control code_editor" id="edit-template-footer" data-mode="application/x-httpd-php" rows="15" name="footer"
                        ><?= htmlentities($template->footer) ?></textarea>
                </div>
            </div>
        </div>

        <div id="edit-template-tab--css" class="tab-pane fade">
            <label for="edit-template-styles">CSS</label>
            <textarea class="form-control code_editor" id="edit-template-styles" data-mode="text/css" rows="25" name="styles"
                ><?= htmlentities($template->styles) ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="submit" name="redirect" value="/admin/settings/templates" class="btn btn-primary">Save &amp; Exit</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <button type="button" class="btn btn-danger">Delete</button>
    </div>
</form>