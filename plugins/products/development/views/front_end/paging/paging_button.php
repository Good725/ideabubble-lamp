<?php if ($current_page > 0): ?>
    <button type="submit" name="page" value="<?= $current_page - 1; ?>" class="paging"><?= __('Prev') ?></button>
<?php endif; ?>

<?php for ($i = 0; $i < $number_of_pages; $i++): ?>
    <button type="submit" name="page" value="<?= $i ?>" class="paging<?= $i == $current_page ? ' active' : ''; ?>"><?= $i + 1 ?></button>
<?php endfor; ?>

<?php if ($current_page < $number_of_pages - 1): ?>
    <button type="submit" name="page" value="<?= $current_page + 1; ?>" class="paging"><?= __('Next') ?></button>
<?php endif; ?>