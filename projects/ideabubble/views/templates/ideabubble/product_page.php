<?php include('template_views/header.php') ?>

<div class="body-content">
    <?php require_once('template_views/home_banner.php') ?>


    <?php if (strpos($page_data['content'], 'fix-container') > -1): ?>
        <?= $page_data['content'] ?>
    <?php else: ?>
        <div class="fix-container"><?= $page_data['content'] ?></div>
    <?php endif; ?>

    <?php
    $panel_model = new Model_Panels();
    $pages_model = new Model_Pages();
    $panels      = $panel_model->get_panels('content_content', (Settings::instance()->get('localisation_content_active') == '1'));
    ?>

    <?php if (count($panels)): ?>
        <div class="fix-container">
            <div class="content-panels">
                <?php foreach ($panels as $panel): ?>
                    <?php
                    if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
                    {
                        $page = $pages_model->get_page_data( $panel['link_id'] );
                        $panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
                    }
                    ?>

                    <div class="content-panel">
                        <?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>

                            <?php if (trim($panel['image'])): ?>
                                <img src="<?= $panel_path.$panel['image'] ?>" alt="" />
                            <?php endif; ?>
                            <?= IbHelpers::expand_short_tags($panel['text']) ?>

                        <?php if (trim($panel['link_url'])): ?></a><?php endif; ?>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?= Model_Testimonials::get_plugin_items_front_end_feed('Testimonials'); ?>

    <?= $page_data['footer'] ?>

</div>

<?php include('template_views/footer.php') ?>
