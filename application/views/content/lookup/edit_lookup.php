<div class="col-sm-12">
    <?=(isset($alert)) ? $alert : ''?>
</div>

<form class="col-sm-12 form-horizontal" id="form_edit_lookup" name="form_edit_lookup" action="/admin/lookup/edit_lookup/<?=$lookup['id']?>" method="post">
    <input type="hidden" name="id" value="<?=$lookup['id']?>" />
    <div class="form-group">
        <label class="col-sm-3 control-label" for="category">Field</label>
        <div class="col-sm-4">
            <select class="form-control" id="category" name="field_id" required>
                <option value="">Select Field Type</option>
                <?=html::optionsFromRows('id', 'name', $field_names, $lookup['field_id']);?>
            </select>
        </div>
    </div>
    <div class="col-sm-12 text-danger text-center error-area" id="lookup-label-error-area"></div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="label">Label</label>
        <div class="col-sm-4">
            <input class="form-control" id="lookup-label"  type="text" name="label" value="<?=html::chars($lookup['label'])?>" />
        </div>
    </div>
    <div class="col-sm-12 text-danger text-center error-area" id="lookup-value-error-area"></div>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="value">Value</label>
        <div class="col-sm-4">
            <input class="form-control" id="lookup-value" type="number" name="value" value="<?=html::chars($lookup['value'])?>" />
        </div>
    </div>
    <label class="col-sm-3 control-label" for="default">Default</label>
    <div>
        <?php $checked = $lookup['is_default'] == 1 ?'checked': ''?>
        <input type="checkbox" name="default" <?= $checked?> data-toggle="toggle" data-onstyle="success" data-on="On" data-off="Off">
    </div>
    <div class="well text-center">
        <button type="submit" id= "edit-lookup" name="action" value="save" class="btn btn-primary continue-button"><?= __('Save') ?></button>
        <a href="/admin/lookup" class="btn btn-default"><?= __('Cancel') ?></a>
    </div>
    </div>
</form>