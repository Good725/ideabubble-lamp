<?php
$banner_search = true;
$course_categories = Settings::instance()->get('home_page_course_categories_feed') ? Model_Categories::get_all_published_categories(['include_empty' => false]) : [];
$testimonials = Model_Testimonials::get_all_items_front_end(null, 'Home feed', array('placeholder' => true));
$news_carousel_items = Model_News::get_all_items_front_end(null, 'Home feed', null, null, array('placeholder' => true));
include 'template_views/header.php';
?>
<?php if (trim($page_data['content'])): ?>
    <div class="row page-content"><?= $page_data['content'] ?></div>
<?php endif; ?>
<?php
// News and/or testimonials tickers
switch (Settings::instance()->get('home_page_feed_1')) {
    case 'news':
        $feed_type = 'news';
        $feed_items = Model_News::get_all_items_front_end();
        include 'template_views/snippets/news_slider.php';
        break;
    case 'testimonials':
        $feed_type = 'testimonials';
        $feed_items = Model_Testimonials::get_all_items_front_end();
        include 'template_views/snippets/news_slider.php';
        break;
}

if (Settings::instance()->get('home_page_feed_2') != Settings::instance()->get('home_page_feed_1')) {
    switch (Settings::instance()->get('home_page_feed_2')) {
        case 'news':
            $feed_type = 'news';
            $feed_items = Model_News::get_all_items_front_end();
            include 'template_views/snippets/news_slider.php';
            break;
        case 'testimonials':
            $feed_type = 'testimonials';
            $feed_items = Model_Testimonials::get_all_items_front_end();
            include 'template_views/snippets/news_slider.php';
            break;
    }
}
?>

<?php
$panel_model = new Model_Panels();
$home_panels = $panel_model->get_panels('home_content', (Settings::instance()->get('localisation_content_active') == '1'));
?>
<?php if (count($home_panels) > 0): ?>
    <?php $panel_path = $panel_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/'); ?>
    <section class="panels-section">
        <div class="row">
            <div class="row gutters panels-feed panels-feed--home panels-feed--home_content">
                <?php foreach ($home_panels as $panel): ?>
                    <div class="col-xs-12 col-md-4">
                        <?php
                        echo View::factory('front_end/snippets/panel_item')->set(array(
                            'title_position' => 'below',
                            'title' => htmlentities($panel['title']),
                            'image' => $panel_path . $panel['image'],
                            'link' => $panel['link_url'],
                            'button_text' => __('View More'),
                        ));
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($featured_events)): ?>
    <section class="row section--recommended">
        <h2 class="feed-heading"><?= __('Recommended Events') ?></h2>

        <div class="row gutters panels-feed panels-feed--home events_feed events_feed--recommended">
            <?php foreach ($featured_events as $event): ?>
                <div class="col-xs-12 col-md-4">
                    <?php
                    echo View::factory('front_end/snippets/panel_item')->set(array(
                        'title_position' => 'below',
                        'title' => htmlentities($event->name),
                        'image' => $event->get_image(),
                        'date' => $event->starts,
                        'text' => '<div>' . $event->venue->name . '</div><div>' . $event->venue->city . '</div>',
                        'link' => $event->get_url(),
                        'button_text' => __('Book Now'),
                    ));
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php $bars = Menuhelper::get_all_published_menus('Bars'); ?>

<?php if ($bars): ?>
    <?php $image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'menus'); ?>

    <section class="bars-section">
        <div class="row">
            <div class="row gutters panels-feed panels-feed--home panels-feed--bars">
                <?php foreach ($bars as $bar): ?>
                    <div class="col-xs-12 col-md-4">
                        <a href="<?= menuhelper::get_link($bar) ?>" class="bar" data-id="<?= $bar['id'] ?>">
                            <?php if (!empty($bar['filename'])): ?>
                                <div class="bar-icon">
                                    <?php if (pathinfo($bar['filename'] == 'svg')): ?>
                                        <?php
                                        try {
                                            // If the image is an SVG, render its contents, so it can easily be styled with CSS
                                            if (!empty($bar['image_id'])) {
                                                // Get the file contents based on the code path to the file
                                                echo file_get_contents(Model_Media::get_localpath_to_id($bar['image_id']));
                                            } else {
                                                // If that's not an option, use the URL (less favourable)
                                                echo file_get_contents($image_path . $bar['filename']);
                                            }
                                        } catch (Exception $e) {
                                            // If the SVG does not exist, show a broken image, rather than a code error
                                            echo '<img src="' . $image_path . $bar['filename'] . '" alt="" />';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <img src="<?= $image_path . $bar['filename'] ?>" alt=""/>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="bar-text"><?= htmlentities(__($bar['title'])) ?></div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (count($course_categories)): ?>
    <?php
    $image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'courses');
    $account_bookings = Settings::instance()->get('account_managed_course_bookings');
    $results_link = $account_bookings ? '/available-results.html' : '/course-list.html';
    ?>
    <section class="carousel-section">
        <div class="swiper-button-prev" id="courses-carousel-prev"></div>
        <div class="swiper-button-next" id="courses-carousel-next"></div>
        <div class="row">
            <div class="swiper-container" id="courses-carousel">
                <div class="swiper-wrapper">
                    <?php foreach ($course_categories as $category): ?>
                        <div class="swiper-slide">
                            <?php
                            echo View::factory('front_end/snippets/panel_item')->set(array(
                                'title_position' => 'above',
                                'title' => htmlentities($category['category']),
                                'image' => $image_path . (!empty($category['file_id']) ? $category['file_id'] : 'course-placeholder.png'),
                                'link' => $results_link . '?category=' . $category['id'],
                                'button_text' => __('View Courses'),
                            ));
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($all_upcoming_events)): ?>
    <h2 class="feed-heading"><?= __('Upcoming events') ?></h2>

    <?php
    $slides = array();
    foreach ($all_upcoming_events as $event) {
        $slides[] = View::factory('front_end/snippets/panel_item')->set(array(
            'title_position' => 'above',
            'title' => htmlentities($event->name),
            'image' => $event->get_image(),
            'date' => $event->starts,
            'text' => '<div>' . $event->venue->name . '</div><div>' . $event->venue->city . '</div>',
            'link' => $event->get_url(),
            'button_text' => __('Book Now'),
        ))->render();
    }
    ?>

    <section class="carousel-section">
        <div class="swiper-button-prev" id="courses-carousel-prev"></div>
        <div class="swiper-button-next" id="courses-carousel-next"></div>
        <div class="row">
            <div class="swiper-container" id="courses-carousel" data-slides="<?= htmlentities(json_encode($slides)) ?>">
                <div class="swiper-wrapper"></div>

                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($testimonials)): ?>
    <section class="carousel-section">
        <div class="swiper-button-prev" id="courses-carousel-prev"></div>
        <div class="swiper-button-next" id="courses-carousel-next"></div>
        <div class="row">
            <div class="swiper-container" id="courses-carousel">
                <div class="swiper-wrapper">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="swiper-slide">
                            <?php
                            echo View::factory('front_end/snippets/panel_item')->set(array(
                                'title_position' => 'below',
                                'title' => htmlentities($testimonial['title']),
                                'image' => $testimonial['image_url'],
                                'link' => '/testimonials/' . urlencode('Home feed') . '#testimonial-' . $testimonial['id'],
                                'button_text' => __('Read more'),
                            ));
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($news_carousel_items)): ?>
    <section class="carousel-section">
        <div class="swiper-button-prev" id="courses-carousel-prev"></div>
        <div class="swiper-button-next" id="courses-carousel-next"></div>
        <div class="row">
            <div class="swiper-container" id="courses-carousel">
                <div class="swiper-wrapper">
                    <?php foreach ($news_carousel_items as $news_item): ?>
                        <div class="swiper-slide">
                            <?php
                            echo View::factory('front_end/snippets/panel_item')->set(array(
                                'title_position' => 'above',
                                'title' => htmlentities($news_item['title']),
                                'image' => $news_item['image_url'],
                                'link' => '/news/' . urlencode('Home feed') . '/' . urlencode($news_item['news_url']),
                                'button_text' => __('View more'),
                            ));
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include 'views/footer.php'; ?>
