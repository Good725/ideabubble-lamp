<?php $published = isset($published) ? $published : true; ?>
<label class="checkbox-icon publish-toggle-wrapper">
    <span class="hidden publish-toggle-sort"><?= $published ? 1 : 0 ?></span><?php // This is for table column sorting ?>

    <input
        type="checkbox"
        name="publish"
        value="1"
        class="publish-toggle"
        data-url="<?= isset($url) ? trim($url) : '' ?>"
        data-id="<?= isset($id) ? $id : '' ?>"
        <?= $published ? 'checked="checked"' : '' ?>
    />

    <span class="checkbox-icon-unchecked" title="<?= __('Click to publish') ?>">
        <span class="icon-ban-circle"></span>
    </span>

    <span class="checkbox-icon-checked" title="<?= __('Click to unpublish') ?>">
        <span class="icon-check"></span>
    </span>
</label>