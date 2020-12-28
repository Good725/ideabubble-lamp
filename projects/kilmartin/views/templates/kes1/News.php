<?php include 'template_views/header.php'; ?>

    <div class="content-columns">
        <div class="row content-columns">
            <?php include 'template_views/sidebar.php'; ?>

            <div class="content_area">
                <div class="page-content"><?= trim($page_data['content']) ?></div>
                <div class="page-content page-content--news" id="news-feed-listing">
                    <?= Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category'], false, $items_per_page); ?>
                </div>

                <?php if ($number_of_pages > 1): ?>
                    <div class="pagination-wrapper mt-3" id="news-feed-pagination">
                        <ul class="pagination" role="navigation">
                            <li class="pagination-prev">
                                <a href="#" data-page="0" class="disabled">
                                    <span class="sr-only"><?= __('Previous') ?></span>
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $number_of_pages; $i++): ?>
                                <li class="pagination-item">
                                    <a href="#" data-page="<?= $i ?>"<?= ($i == 1) ? ' class="disabled current"' : '' ?>><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="pagination-next">
                                <a href="#" data-page="2">
                                    <span class="sr-only"><?= __('Next') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php include 'views/footer.php'; ?>
