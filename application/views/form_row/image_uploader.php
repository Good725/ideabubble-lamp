<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div class="<?= $form->input_size_small ?>">
        <?= View::factory('multiple_upload', $args); ?>
        <input<?= html::attributes($attributes) ?> />
    </div>

    <div class="col-sm-3<?= empty($value) ? ' hidden' : '' ?>" id="<?= $attributes['id'] ?>-preview-wrapper">
        <button type="button" class="btn-link right" id="<?= $attributes['id'] ?>-remove" title="<?= __('Remove') ?>">
            <span class="sr-only"><?= __('Remove') ?></span>
            <span class="icon-times"></span>
        </button>

        <div id="<?= $attributes['id'] ?>-preview">
            <?php if (!empty($value)): ?>
                <img
                    src="<?= Model_Media::get_image_path($value, $args['preset']); ?>"
                    alt="<?= $value ?>"
                    class="w-100" />
            <?php else: ?>
                <img src="" alt="" class="w-100 hidden" />
            <?php endif; ?>
        </div>
    </div>
</div>