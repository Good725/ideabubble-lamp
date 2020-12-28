<?php
$item_identifier  = $page_data['current_item_identifier'];

// Banner applies to the listing screen, not the individual news items
if (!empty($item_identifier)) {
    unset($page_data['banner_slides']);
}

include 'template_views/header.php';
$filters = Model_Courses::get_available_filters();
$filter_course_categories = $filters['categories'];
$filter_course_types      = $filters['types'];
$filter_media_types       = ORM::factory('News_Item')->get_enum_options('media_type');

$news = [];
$current_category = ORM::factory('News_Category')->where('category', '=', $current_category)->find_published();
$items_per_page   = (int) Settings::instance()->get('news_feed_item_count');
$items_per_page   = $items_per_page ? $items_per_page : 6;
?>

<div class="news-area" data-category="<?= $current_category->category ?>">
    <?php if ($item_identifier): ?>
        <?php // News item layout ?>

        <?php $item = ORM::factory('News_item')->where_identifier($item_identifier)->find_frontend() ?>

        <div class="row content_area">
            <div class="page-content">
                <h1 class="news-page-category-title">
                    <?= htmlentities($page_object->title ? $page_object->title : $page_object->name_tag) ?>
                </h1>

                <?php if ($item->image): ?>
                    <div class="news-page-image">
                        <img class="w-100" src="<?= $item->get_image_url() ?>" alt="<?= $item->alt_text ?>" />
                    </div>
                <?php endif; ?>

                <div class="row gutters">
                    <div class="col-sm-8 news-column-content">
                        <header>
                            <h1 class="news-page-title">
                                <?= htmlspecialchars($item->title) ?>
                            </h1>

                            <?php if ($item->event_date): ?>
                                <span class="news-page-date"><?= htmlspecialchars(date('d M Y', strtotime($item->event_date))) ?></span>
                            <?php endif; ?>
                        </header>

                        <div class="news-page-content">
                            <?= $item->parse_html() ?>
                        </div>
                    </div>

                    <div class="col-sm-4 news-column-feed news-sidebar-feed">
                        <h3><?= htmlspecialchars($item->category->category) ?></h3>

                        <ul class="list-unstyled">
                            <?php
                            $news = ORM::factory('News_Item')
                                ->where('category_id', '=', $item->category_id)
                                ->order_by('event_date', 'desc')
                                ->find_all_frontend();
                            ?>

                            <?php for ($i = 0; $i < $items_per_page && $i < count($news); $i++): ?>
                                <?php if ($news[$i]->id != $item->id): ?>
                                    <li class="news-sidebar-item">
                                        <a href="<?= $news[$i]->get_url() ?>" class="text-decoration-none">
                                            <h4><?= htmlentities($news[$i]->title)?></h4>
                                        </a>

                                        <?php if ($news[$i]->event_date): ?>
                                            <span class="news-sidebar-date">
                                                <?= htmlspecialchars(date('d M Y', strtotime($news[$i]->event_date))) ?>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </ul>

                        <div class="news-page-subscribe">
                            <form action="/frontend/formprocessor" method="post" id="newsletter-form">
                                <h4>Sign up to our Mailing List</h4>

                                <?php $form_identifier = 'newsletter_signup_'; ?>

                                <input type="hidden" name="subject" value="Newsletter Subscription Form" />
                                <input type="hidden" name="business_name" value="<?= Settings::instance()->get('company_name') ?>" />
                                <input type="hidden" name="redirect" value="subscription-thank-you.html" />
                                <input type="hidden" name="trigger" value="add_to_list" />
                                <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>" />

                                <div class="form-group">
                                    <input type="text" class="form-input validate[required]" id="newsletter-form-name" name="<?= $form_identifier ?>form_name" placeholder="<?= __('Name') ?>" />
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-input validate[required,custom[email]]" id="newsletter-form-email" name="<?= $form_identifier ?>form_email_address" placeholder="<?= __('E-mail') ?>" />
                                </div>

                                <?php if (Settings::instance()->get('captcha_enabled') && Settings::instance()->get('newsletter_subscription_captcha')): ?>
                                    <script src='https://www.google.com/recaptcha/api.js'></script>

                                    <div class="captcha-section hidden" id="newsletter-form-captcha-section">
                                        <div class="form-group">
                                            <input type="text" class="sr-only" id="newsletter-form-captcha-hidden" tabindex="-1" />

                                            <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>" data-size="compact" style="display: table;margin: auto;"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (Settings::instance()->get('newsletter_signup_terms') != 0): ?>
                                    <div class="form-group">
                                        <label class="newsletter-signup-terms d-flex">
                                            <?= Form::ib_checkbox(null, 'terms', 1, false, ['class' => 'validate[required]', 'id' => 'newsletter-form-terms']) ?>
                                            <div class="newsletter-signup-terms-text"><?= Settings::instance()->get('newsletter_signup_terms') ?></div>
                                        </label>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <button type="submit" class="button bg-primary w-100" id="submit-newsletter">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if ($item->seo_footer): ?>
                    <div class="row page-footer">
                        <div class="page-content"><?= IbHelpers::parse_page_content($item->seo_footer) ?></div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php else: ?>
        <?php // News feed layout ?>

        <?php if (trim($page_data['content'])): ?>
            <div class="fullwidth news-list-intro">
                <div class="row content_area">
                    <div class="page-content"><?= IbHelpers::parse_page_content($page_data['content']) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        if (Settings::instance()->get('enable_news_filters')) {
            $filter_for = 'news';
            include 'template_views/news_filters.php';
        }
        ?>

        <div class="fullwidth news-container">
            <div class="container page-content">
                <?php foreach ($filter_media_types as $media_type): ?>
                    <?php $news_items = ORM::factory('News_Item')->where('media_type', '=', $media_type)->find_all_frontend(); ?>

                    <?php if (count($news_items) > 0): ?>
                        <div class="fullwidth news-list-by-media_type" data-media="<?= $media_type ?>">
                            <div class="container">
                                <h2><?= $media_type!= 'Blog' ? htmlspecialchars($media_type) . 's' :  htmlspecialchars($media_type)?></h2>

                                <div class="news-list-inner">
                                    <?= View::factory('/front_end/news_results')->set(compact('media_type', 'news_items')) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <p class="hidden" id="news-list-no_results">No results found.</p>
            </div>
        </div>

        <?php if (trim($page_data['footer'])): ?>
            <div class="fullwidth news-list-footer">
                <div class="row content_area">
                    <div class="page-content"><?= IbHelpers::parse_page_content($page_data['footer']) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (Settings::instance()->get('enable_news_filters')): ?>
            <script>
                // Update results when a filter is changed
                $('.filter-section[data-for="news"]').on('change', '.update-results', function() {
                    update_news_results();
                });

                $(document).on('click', '.news-list-by-media_type .pagination a', function(ev) {
                    ev.preventDefault();
                    const section = $(this).parents('.news-list-by-media_type').data('media');
                    const page = $(this).data('page');
                    update_news_results(section, page);
                });

                function update_news_results(section, page)
                {
                    section = section || null;
                    page = page || 1;

                    const $filters = $('.filter-section[data-for="news"]');

                    // Get data...
                    let keyword = $filters.find('.news-filter-keyword').val();
                    let course_category_ids = [];
                    let course_type_ids = [];
                    let media_types = [];
                    $filters.find('[name="course_category_ids[]"]:checked').each(function() { course_category_ids.push(this.value); });
                    $filters.find('[name="course_type_ids[]"]:checked'    ).each(function() {     course_type_ids.push(this.value); });
                    $filters.find('[name="media_types[]"]:checked'        ).each(function() {         media_types.push(this.value); });

                    const $sections = section ? $('.news-list-by-media_type[data-media="'+section+'"]') : $('.news-list-by-media_type');

                    // Update the news items in each group
                    $sections.each(function(index) {
                        let $section = $(this);
                        let data = {
                            course_category_ids: course_category_ids,
                            course_type_ids: course_type_ids,
                            media_types: media_types,
                            media_type: $(this).data('media'),
                            term: keyword,
                            page: page
                        };

                        $.ajax({
                            url: '/frontend/news/ajax_get_paginated_news_html',
                            data: data
                        }).done(function(result) {
                            $section
                                .toggleClass('hidden', result.count == 0)
                                .find('.news-list-inner').html(result.html);

                                // After the last section has been toggled, check if any are visible
                                if (index + 1 == $sections.length) {
                                    const has_results = ($('.news-list-by-media_type:visible').length > 0);

                                    $('#news-list-no_results').toggleClass('hidden', has_results);
                                }
                        });

                    });

                }
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>
