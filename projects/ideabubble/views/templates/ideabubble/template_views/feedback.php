<?php
$number_of_slides = count($page_data['banner_slides']);
$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'banners');
?>

<?php if ( ! empty($page_data['banner_slides']) OR ( ! empty($page_data['html']))): ?>
	<section class="full-row feedback-column <?= ( ! empty($page_data['banner_slides'])) ? 'banner-section--sequence' : 'banner-section--single'?> home-banner">
		<div class="fix-container">
			<?php if ( ! empty($page_data['banner_slides'])): ?>
					<div class="swiper-container" id="courses-carousel"
						data-slides="<?= $number_of_slides ?>"
						data-speed="<?= ! empty($page_data['banner_sequence_data']['rotating_speed']) ? $page_data['banner_sequence_data']['rotating_speed'] : 300 ?>"
						data-autoplay="<?= ! empty($page_data['banner_sequence_data']['timeout']) ? $page_data['banner_sequence_data']['timeout'] : 5000 ?>"
						>
						<ul class="swiper-wrapper">
							<?php foreach ($page_data['banner_slides'] as $slide): ?>
									<li class="swiper-slide">
										<div class="panel-body">
											<p><?= $slide['html'];?> </p>
										</div>
										<div class="author-name">
											<?= $slide['title'];?>
										</div>
									</li>
							<?php endforeach;?>
						</ul>
					</div>

				<?php if ( ! empty($page_data['banner_sequence_data']['controls'])): ?>
						<div class="swiper-button-prev" id="courses-carousel-prev">4</div>
						<div class="swiper-button-next" id="courses-carousel-next">5</div>
				<?php endif;?>

				<?php if ( ! empty($page_data['banner_sequence_data']['pagination'])): ?>
						<div class="swiper-pagination"></div>
                <?php endif; ?>
					
        <?php endif; ?>
		</div>
	</section>
<?php endif;?>
