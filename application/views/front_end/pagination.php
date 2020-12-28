<?php
$prev_button_active = ($current_page > 1);
$next_button_active = ($current_page < $total_pages);
?>

<div class="pagination-wrapper">
    <input type="hidden" class="current_page" value="<?= $current_page ?>" />

    <?php if ($total_pages > 1): ?>
        <ul class="list-unstyled pagination" role="navigation" aria-label="Pagination">
            <li class="pagination-prev">
                <a
                    href="#"
                    <?= $prev_button_active ? '' : ' class="disabled"' ?>
                    data-page="<?= $current_page - 1 ?>"
                    >
                    <span class="sr-only"><?= __('Previous') ?></span>
                </a>
            </li>

            <?php $radius = 3; ?>
            <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                <?php if (($total_pages == 2 * $radius + 1) || ($page > $current_page - $radius && $page < $current_page + $radius)): ?>
                    <li>
                        <a
                            href="#"
                            aria-label="Page <?= $page ?>"
                            data-page="<?= $page ?>"
                            <?= ($page == $current_page) ? ' class="current"' : '' ?>
                            ><?= $page ?></a>
                    </li>
                <?php elseif (($total_pages != 2 * $radius + 1) && ($page == $current_page - $radius || $page == $current_page + $radius)): ?>
                    <li><a href="#" class="disabled">...</a></li>
                <?php endif; ?>
            <?php endfor; ?>

            <li class="pagination-next">
                <a
                    href="#"
                    <?= $next_button_active ? '' : ' class="disabled"' ?>
                    data-page="<?= $current_page + 1 ?>"
                    >
                    <span class="sr-only"><?= __('Next') ?></span>
                </a>
            </li>
        </ul>
    <?php endif; ?>
</div>
