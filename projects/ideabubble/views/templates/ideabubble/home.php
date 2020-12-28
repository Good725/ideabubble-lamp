<?php  include('template_views/header.php');?>

<div class="body-content">
    <?php require_once('template_views/home_banner.php');?>

    <?php if (strpos($page_data['content'], 'fix-container') > -1): ?>
        <?= $page_data['content'] ?>
    <?php else: ?>
        <div class="fix-container"><?= $page_data['content'] ?></div>
    <?php endif; ?>

    <?= $page_data['footer'] ?>
</div>

<?php  require_once('template_views/footer.php');?>
