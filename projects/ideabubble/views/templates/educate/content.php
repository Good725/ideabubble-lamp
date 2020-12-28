<?php require_once('template_views/header.php'); ?>
<!-- body starts here -->
<div class="body-content">
    <section class="full-row">
        <?php if (strpos($page_data['content'], 'fix-container') > -1): ?>
            <?= $page_data['content'] ?>
        <?php else: ?>
            <div class="fix-container<?= (strpos($page_data['content'], 'product-item') > -1) ? 'fix-container--products' : '' ?> relative"><?= $page_data['content'] ?></div>
        <?php endif; ?>

    </section>
</div>
<!-- footer starts here -->
<?php require_once('template_views/footer.php'); ?>
