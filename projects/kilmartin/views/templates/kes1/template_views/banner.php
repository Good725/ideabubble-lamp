<?php $slides = isset($banner_items) ? $banner_items : (isset($page_data['banner_slides']) ? $page_data['banner_slides'] : array()); ?>

<?php if ( ! empty($slides) || ! empty($page_data['banner_image']) || ! empty($page_data['banner_map'])): ?>
    <?php
    $number_of_slides  = count($slides);
    $account_bookings  = Settings::instance()->get('account_managed_course_bookings');
    $image_path        = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'banners');
    $banner_search     = !empty($banner_search);
    $has_mobile_banner = isset($has_mobile_banner) ? $has_mobile_banner : true;

    // Check if at least one slide has a separate image for mobile
    $has_mobile_slides = (count(array_filter(array_column($slides, 'mobile_src'))) > 0);
    ?>

    <section class="banner-section
        <?= ( ! empty($slides)) ? 'banner-section--sequence' : 'banner-section--single'?>
        <?= $banner_search ? ' banner-section--has_search' : '' ?>
        <?= $has_mobile_slides ? ' banner-section--has_mobile_slides' : '' ?>
        <?= $has_mobile_banner ? '' : ' hidden--mobile' ?>"
    >
		<?php if ( ! empty($slides)): ?>
			<div class="swiper-container" id="home-banner-swiper"
                 data-autoplay="<?= ! empty($page_data['banner_sequence_data']['timeout']) ? $page_data['banner_sequence_data']['timeout'] : 5000 ?>"
                 data-direction="<?= ( ! empty($page_data['banner_sequence_data']['animation_type']) && $page_data['banner_sequence_data']['animation_type'] == 'vertical') ? 'vertical' : 'horizontal' ?>"
                 data-effect="<?= ( ! empty($page_data['banner_sequence_data']['animation_type']) && $page_data['banner_sequence_data']['animation_type'] == 'fade') ? 'fade' : 'slide' ?>"
                 data-slides="<?= $number_of_slides ?>"
                 data-speed="<?= ! empty($page_data['banner_sequence_data']['rotating_speed']) ? $page_data['banner_sequence_data']['rotating_speed'] : 300 ?>"
                >
				<div class="swiper-wrapper">
					<?php
                    foreach ($slides as $slide) {
                        include 'snippets/banner_slide.php';
                    }
                    ?>
				</div>

				<?php if ( ! empty($page_data['banner_sequence_data']['controls'])): ?>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
				<?php endif; ?>

				<?php if ( ! empty($page_data['banner_sequence_data']['pagination'])): ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
			</div>
        <?php elseif ( ! empty ($page_data['banner_map'])): ?>
            <div class="banner-map"><?= $page_data['banner_map'] ?></div>
		<?php elseif ( ! empty ($page_data['banner_image'])): ?>
			<div class="banner">
				<div class="banner-image" style="background-image: url('<?= $image_path.$page_data['banner_image'] ?>');"></div>
			</div>
		<?php endif; ?>

        <?php if ($banner_search): ?>
            <?= View::factory('finder_menu') ?>
        <?php endif; ?>
	</section>

    <?php // These are needed for clipping patterns into banners ?>
    <svg class="sr-only">
        <defs>
            <clipPath id="banner-clippath">
                <path xmlns="http://www.w3.org/2000/svg" d="M0,706.7c0,0,343.2,82.5,590,42.3s471.8-135.7,724.9-85.5  c253.1,50.1,606.4-166.1,606.4-166.1V0H0V706.7z"></path>
            </clipPath>
            <clipPath id="banner-clippath-mobile">
                <path xmlns="http://www.w3.org/2000/svg" d="M0,350c0,0,137.2,33,235.8,16.9s188.6-54.2,289.8-34.2c101.2,20,242.4-66.4,242.4-66.4  V0H0V0z"></path>
            </clipPath>
        </defs>
    </svg>
<?php endif; ?>