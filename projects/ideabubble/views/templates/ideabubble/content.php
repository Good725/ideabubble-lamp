<?php include('template_views/header.php') ?>

<div class="body-content">
    <?php require_once('template_views/home_banner.php') ?>

    <?php
    // Replace pay-online form tag. This should be replaced with a general form short tag.
    if (strpos($page_data['content'], '&lt;[payonlineform]&gt;') > -1)
    {
        ob_start();
        include('template_views/pay_online_form.php');
        $form = ob_get_clean();
        $page_data['content'] = str_replace('&lt;[payonlineform]&gt;', $form, $page_data['content']);
    }
    ?>

    <?php if (strpos($page_data['content'], 'fix-container') > -1): ?>
        <?= $page_data['content'] ?>
    <?php else: ?>
        <div class="fix-container"><?= $page_data['content'] ?></div>
    <?php endif; ?>

    <?= $page_data['footer'] ?>

</div>

<?php include('template_views/footer.php') ?>
