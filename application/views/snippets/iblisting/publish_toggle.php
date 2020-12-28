<label class="checkbox-icon">
    <input
        class="<?= $id_prefix ?>-table-publish"
        type="checkbox" <?= ($published ? 'checked' : '') ?>
        data-id="<?= $id ?>"
        />
    <span class="checkbox-icon-unchecked icon-ban-circle"></span>
    <span class="checkbox-icon-checked icon-check"></span>
</label>