<?php
$has_breadcrumbs = true;
include 'template_views/header.php';
?>

    <div class="row hidden--mobile">
        <ol class="breadcrumbs">
            <li><a href="/"><?= __('Home') ?></a></li>
            <li class="current"><?= ucwords(str_replace('.html', '', preg_replace('/-/', ' ', $page_data['name_tag']))) ?></li>
        </ol>
    </div>

    <div class="content-columns">
        <div class="row content-columns">
            <?php
            // The sidebar is on the opposite side than the "content", which is what the setting is for.
            $sidebar_location = (Settings::instance()->get('content_location') == 'left') ? 'right' : 'left';

            $panel_model    = new Model_Panels();
            $sidebar_panels = $panel_model->get_panels('content_'.$sidebar_location, (Settings::instance()->get('localisation_content_active') == '1'));
            ?>
            <?php if (count($sidebar_panels) > 0): ?>
                <?php $panel_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/'); ?>

                <aside class="sidebar sidebar--<?= $sidebar_location ?>">
                    <?php
                    foreach ($sidebar_panels as $panel) {
                        $panel_model->render($panel['title']);
                    } ?>
                </aside>
            <?php endif; ?>


            <div class="content_area">
                <div class="page-content"><?= trim($page_data['content']) ?></div>
            </div>
        </div>
    </div>

<?php include 'views/footer.php'; ?>