<?php include('template_views/header.php') ?>

<div class="body-content">
    <?php require_once('template_views/home_banner.php') ?>

    <?php if (strpos($page_data['content'], 'fix-container') > -1): ?>
        <?= $page_data['content'] ?>
    <?php else: ?>
        <div class="fix-container"><?= $page_data['content'] ?></div>
    <?php endif; ?>

    <div class="fix-container"><?= Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) ?></div>

    <?= $page_data['footer'] ?>
</div>

<?php include('template_views/footer.php') ?>
