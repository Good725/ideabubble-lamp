<?php include 'template_views/header.php'; ?>

<?php
$feed = ($page_data['current_item_identifier'] == '');
$side_panels = $panel_model->get_panels('content_right', ($settings_instance->get('localisation_content_active') == '1'));
?>

<div class="row">
    <div class="column-content columns <?= count($side_panels) ? 'medium-7 large-8' : 'medium-12' ?>">
        <article>
            <section class="entry-content"><?= $page_data['content'] ?></section>
        </article>

        <section id="news-section">
            <div class="news-section-feed">
                <?= Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category'], false, 3, 0) ?>
            </div>
        </section>
    </div>

    <?php include 'template_views/content_sidebar.php'; ?>
</div>

<?php include 'template_views/footer.php'; ?>
