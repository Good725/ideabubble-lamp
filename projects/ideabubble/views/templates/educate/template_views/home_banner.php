<?php
$number_of_slides = count($page_data['banner_slides']);
$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'banners');
?>

<?php
$banner_panel_html = '';
if ($page_data['layout'] == 'home')
{
    $panel_model = new Model_Panels();
    $home_panels = $panel_model->get_panels('home_right', (Settings::instance()->get('localisation_content_active') == '1'));
    if (count($home_panels) > 0)
    {
        $panel_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/');
        ob_start();
        ?>
        <div class="banner-panels">

            <div class="fix-container">
                <?php foreach ($home_panels as $panel): ?>
                    <div class="ib-service">
                        <?= $panel['text'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        $banner_panel_html = ob_get_contents();
        ob_end_clean();
    }
}
?>

<?php if ( ! empty($page_data['banner_slides']) OR ( ! empty($page_data['banner_image']))): ?>
	<section class="banner-section <?= ( ! empty($page_data['banner_slides'])) ? 'banner-section--sequence' : 'banner-section--single'?> home-banner">
        <?php if ( ! empty($page_data['banner_slides'])): ?>
            <div class="swiper-container home-banner-swiper" id="home-banner-swiper"
                 data-autoplay="<?= ! empty($page_data['banner_sequence_data']['timeout']) ? $page_data['banner_sequence_data']['timeout'] : 5000 ?>"
                 data-direction="<?= ( ! empty($page_data['banner_sequence_data']['animation_type']) && $page_data['banner_sequence_data']['animation_type'] == 'vertical') ? 'vertical' : 'horizontal' ?>"
                 data-effect="<?= ( ! empty($page_data['banner_sequence_data']['animation_type']) && $page_data['banner_sequence_data']['animation_type'] == 'fade') ? 'fade' : 'slide' ?>"
                 data-slides="<?= $number_of_slides ?>"
                 data-speed="<?= ! empty($page_data['banner_sequence_data']['rotating_speed']) ? $page_data['banner_sequence_data']['rotating_speed'] : 300 ?>"
                >
                <div class="swiper-wrapper">
                    <?php foreach ($page_data['banner_slides'] as $slide): ?>
                        <div class="swiper-slide">
                            <div class="banner">
                                <div class="banner-img banner-image" style="background-image:url('<?= $image_path.$slide['image'] ?>');"></div>

                                <?php if (trim($slide['html'])): ?>
                                    <div class="fix-container">
                                        <div class="caption banner-caption">
                                            <?= $slide['html'] ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($page_data['banner_slides']) > 1 AND ! empty($page_data['banner_sequence_data']['controls'])): ?>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                <?php endif; ?>

                <?php if (count($page_data['banner_slides']) > 1 AND  ! empty($page_data['banner_sequence_data']['pagination'])): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>

                <?= $banner_panel_html ?>
            </div>
        <?php elseif ( ! empty ($page_data['banner_image'])): ?>
            <div class="banner">
                <div class="banner-image" style="background-image:url('/shared_media/ibeducate/media/photos/banners/<?= $page_data['banner_image'] ?>');"></div>
            </div>

            <?= $banner_panel_html ?>
        <?php endif; ?>


	</section>
<?php endif;?>
