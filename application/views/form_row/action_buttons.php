<div class="form-action-group">
    <button type="submit" class="btn btn-primary" name="redirect" value="save"><?= htmlspecialchars(__('Save')) ?></button>
    <button type="submit" class="btn btn-primary" name="redirect" value="save_and_exit"><?= htmlspecialchars(__('Save & Exit')) ?></button>

    <?php if ($form->has_delete_button()) : ?>
        <button
            type="button"
            class="btn btn-danger"
            id="<?= $form->id ?>-delete-btn"
            data-toggle="modal"
            data-target="#<?= $form->id?>-delete-modal"
        ><?= htmlspecialchars(__('Delete')) ?></button>
    <?php endif; ?>

    <button type="reset" class="btn btn-default"><?= htmlspecialchars(__('Reset')) ?></button>

    <?php if (!empty($form->cancel_url)): ?>
        <a href="<?= $form->cancel_url ?>" class="btn btn-cancel"><?= htmlspecialchars(__('Cancel')) ?></a>
    <?php endif; ?>
</div>

