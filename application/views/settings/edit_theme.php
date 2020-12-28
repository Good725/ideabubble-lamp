<form action="/admin/settings/save_theme/<?= $theme->id ?>" method="post" class="form-horizontal col-sm-12">
    <input type="hidden" name="id" value="<?= $theme->id ?>" />

    <?= (isset($alert)) ? $alert : '' ?>

    <div class="form-row gutters input_columns">
        <div class="col-sm-10">
            <?= Form::ib_input(null, 'title', $theme->title, array('required' => 'required', 'class' => 'validate[required]', 'placeholder' => 'Enter title')); ?>
        </div>

        <div class="col-sm-2">
            <label>
                <span class="sr-only"><?= __('Publish') ?></span>
                <input type="hidden" name="publish" value="0" />
                <input type="checkbox" name="publish" value="1"<?= ($theme->publish == 1 || $theme->publish === null) ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Stub</label>

        <div class="col-sm-5">
            <?php
            $attributes = array();
            if ($theme->id) $attributes['readonly'] = 'readonly';
            echo Form::ib_input(null, 'stub', $theme->stub, $attributes);
            ?>
        </div>
        <div class="col-sm-5">This name is used for reference in the code</div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Template</label>

        <div class="col-sm-5">
            <?php
            $options = array('' => 'Please Select');
            foreach ($templates as $template) {
                $options[$template->id] = $template->title;
            }
            echo Form::ib_select(null, 'template_id', $options, $theme->template_id);
            ?>
        </div>
    </div>

    <h3>Variables</h3>

    <?php foreach ($variables as $key => $variable): ?>
        <?php
        // If the theme has a value for this variable, use it. Otherwise use the variable's default value
        $value = (isset($theme_variables[$key])) ? $theme_variables[$key]->value : $variable->default;
        ?>

        <div class="form-group">
            <label class="col-sm-2 control-label"><?= $variable->name ?></label>

            <div class="col-sm-5">
                <?= Form::ib_input('$'.$variable->variable, 'variables['.$variable->id.']', $value, array('class' => 'form-input-colorpicker')) ?>
            </div>

            <div class="col-sm-5"><?= $variable->description ?></div>
        </div>
    <?php endforeach; ?>

    <h3>Email variables</h3>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="edit-template-email_header_color">Email header colour</label>
        <div class="col-sm-5">
            <?= Form::ib_input('$theme_color', 'email_header_color', $theme->email_header_color, ['id' => 'edit-template-email_header_color', 'class' => 'form-input-colorpicker']) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="edit-template-email_link_color">Email link colour</label>
        <div class="col-sm-5">
            <?= Form::ib_input('$link_color', 'email_link_color', $theme->email_link_color, ['id' => 'edit-template-email_link_color', 'class' => 'form-input-colorpicker']) ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <label for="edit-template-footer">Styles</label>
            <textarea class="form-control code_editor" id="edit-theme-styles" rows="23" name="styles" data-mode="text/css"
                ><?= htmlentities($theme->styles) ?></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>
