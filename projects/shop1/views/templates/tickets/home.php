<?php include 'template_views/header.php' ?>

<?php
$number_of_slides = count($banner_items);
$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'banners');
?>

<section class="banner banner--home<?= ( ! empty($page_data['banner_slides'])) ? ' banner-section--sequence' : ' banner-section--single'?>" id="home_banner">
    <div class="banner-inner">
        <?php if ( ! empty($banner_items)): ?>
            <div class="swiper-container" id="home-banner-swiper"
                 data-animation="<?= ($banner_sequence['animation_type'] == 'fade') ? 'fade' : 'slide' ?>"
                 data-slides="<?= $number_of_slides ?>"
                 data-speed="<?= ( ! empty($banner_sequence['rotating_speed'])) ? $banner_sequence['rotating_speed'] : 300 ?>"
                 data-autoplay="<?= ( ! empty($banner_sequence['timeout'])) ? $banner_sequence['timeout']: 5000 ?>"
                 <?= ( ! empty($page_data['banner_sequence_data']['pagination'])) ? 'data-has_pagination' : '' ?>
                >
                <div class="swiper-wrapper">
                    <?php foreach ($banner_items as $banner_index => $banner_event): ?>
                        <div class="swiper-slide<?= ($banner_index == 0) ? ' swiper-slide-active' : '' ?>">
                            <div class="banner">
                                <?php if ( ! empty($banner_event['url'])): ?>

                                    <a href="<?=$banner_event['url']?>" target="<?=$banner_event['target']?>"><?php endif; ?>
                                            <div class="banner-image" style="background-image:url('<?= $banner_event['image'] ?>');">
                                                <img class="show-for-sr" src="<?= $banner_event['image'] ?>" />
                                            </div>


                                        <div class="orbit-caption">
                                            <div class="row">
                                                <?php if(!empty($banner_event['title'])): ?>
                                                    <div class="slider_event_info">
                                                        <div class="orbit-caption-text">
                                                            <div class="event_info">
                                                                <span class="cl_title"><?=$banner_event['title']?></span>
                                                                <?php
                                                                if ( ! empty($banner_event['start_date']))
                                                                {
                                                                    echo '<span class="cl_date">'.date('l jS F', strtotime($banner_event['start_date']));
                                                                    if ( ! empty($banner_event['end_date']))
                                                                    {
                                                                        echo ' &ndash; '.date('l jS F', strtotime($banner_event['end_date']));
                                                                    }
                                                                    echo '</span>';
                                                                }
                                                                elseif ( ! empty($banner_event['html']))
                                                                {
                                                                    echo '<span class="cl_date">'.$banner_event['html'].'</span>';
                                                                }
                                                                ?>
                                                                <span class="cl_label"><?=$banner_event['label']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                         <?php if ( ! empty($banner_event['url'])): ?>
                                    </a>
                                         <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ( ! empty($page_data['banner_sequence_data']['controls'])): ?>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                <?php endif; ?>

                <?php if ( ! empty($page_data['banner_sequence_data']['pagination'])): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
        <?php elseif ( ! empty ($page_data['banner_image'])): ?>
            <div class="banner">
                <?php $image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'banners'); ?>
                <div class="banner-image" style="background-image:url('<?= $image_path.$page_data['banner_image'] ?>');"></div>
            </div>
        <?php endif; ?>

        <form class="banner-search" action="/events" method="get">
            <div class="row row-full collapse">
                <label class="columns small-12 medium-6 large-8 banner-search-event">
                    <span class="show-for-sr"><?= __('Find your event') ?></span>
                    <input type="text" name="term" class="form_field" placeholder="<?= __('Find your event') ?>" />
                </label>
                <label class="columns small-12 medium-4 large-2 banner-search-date">
                    <span class="show-for-sr"><?= __('Date') ?></span>
                            <span class="input-with-icon input-with-icon--right">
                                <input type="text" name="date" class="form_field" placeholder="<?= __('Date') ?>" id="home_banner-search-input" />
                                <span class="input-icon flaticon-calendar"></span>
                            </span>
                </label>
                <div class="columns small-12 medium-2 large-2 float-left banner-search-button">
                    <button type="submit" class="button secondary"><?= __('Search') ?></button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php if (count($featured_events) > 0): ?>
	<section class="row section--recommended">
		<h3 class="text-center">uTicket Recommends</h3>
		<div class="events_feed events_feed--recommended">
			<?php foreach ($featured_events as $event) include 'template_views/event_feed_item.php'; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ($upcoming_events_count > 0): ?>
	<section class="row section--upcoming">
		<h3 class="text-center"><?= __('Upcoming Events') ?></h3>
		<div class="events_feed events_feed--upcoming">
            <?php foreach ($upcoming_events as $event) include 'template_views/event_feed_item_upcoming.php'; ?>
		</div>
	</section>
    <?php if ($upcoming_events_count > 16): ?>
        <div class="text-center"><a href="#" id="more_events" class="button large" data-offset="16"><?= __('See More Events')?></a></div>
    <?php endif; ?>
<?php endif; ?>
<?php include 'template_views/footer.php' ?>
