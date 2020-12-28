<?php
include 'template_views/header.php';
$current_category = Request::$current->param('item_category') ? Request::$current->param('item_category') : 'News';
$item_identifier  = $page_data['current_item_identifier'];
$current_category = ORM::factory('News_Category')->where('category', '=', $current_category)->find_published();
$news             = $current_category->items->find_all_frontend();
$items_per_page   = (int) Settings::instance()->get('news_feed_item_count');
$items_per_page   = $items_per_page ? $items_per_page : 6;
?>

<div class="news-area" data-category="<?= $current_category->category ?>">
    <?php if ($item_identifier): ?>
        <?php // News item layout ?>

        <?php $item = ORM::factory('News_item')->where_identifier($item_identifier)->find_frontend() ?>
        <div class="row content_area mb-4">
            <div class="page-content">
                <h1 class="news-page-title">
                    <?= htmlentities($page_object->title ? $page_object->title : $page_object->name_tag) ?> &gt;
                    <a href="/<?= $page_data['name_tag'] ?>/<?= $item->category->category ?>" class="text-category text-decoration-none">
                        <?= htmlentities($item->category->category) ?>
                    </a>
                </h1>

                <?php if ($item->image): ?>
                    <div class="news-page-image">
                        <img class="w-100" src="<?= $item->get_image_url() ?>" alt="<?= $item->alt_text ?>" />
                    </div>
                <?php endif; ?>

                <div class="row gutters">
                    <div class="col-sm-8 news-column-content">
                        <h2 class="mt-0 news-page-title"><?= htmlspecialchars($item->title) ?></h2>

                        <div class="news-page-content">
                            <?= $item->parse_html() ?>
                        </div>
                    </div>

                    <div class="col-sm-4 news-column-feed news-sidebar-feed">
                        <h3 class="mt-0 mb-3 text-category"><?= htmlspecialchars(__('Latest')) ?></h3>

                        <ul class="list-unstyled">
                            <?php for ($i = 0; $i < $items_per_page && $i < count($news); $i++): ?>
                                <?php if ($news[$i]->id != $item->id): ?>
                                    <li class="my-2 pt-2 pb-3 news-sidebar-item">
                                        <a href="<?= $news[$i]->get_url() ?>" class="text-decoration-none">
                                            <h6 class="my-0"><?= htmlentities($news[$i]->title)?></h6>
                                        </a>

                                        <?php if ($news[$i]->author): ?>
                                            <span class="news-sidebar-author">
                                                By <?= htmlspecialchars($news[$i]->author) ?>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </ul>

                        <div class="news-page-subscribe mt-5">
                            <form action="/frontend/formprocessor" method="post" id="newsletter-form">
                                <h5 class="mb-3">Sign up to our Mailing List</h5>

                                <?php $form_identifier = 'newsletter_signup_'; ?>

                                <input type="hidden" name="subject" value="Newsletter Subscription Form" />
                                <input type="hidden" name="business_name" value="<?= Settings::instance()->get('company_name') ?>" />
                                <input type="hidden" name="redirect" value="subscription-thank-you.html" />
                                <input type="hidden" name="trigger" value="add_to_list" />
                                <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>" />

                                <div class="mb-3">
                                    <input type="text" class="form-input validate[required]" id="newsletter-form-name" name="<?= $form_identifier ?>form_name" placeholder="<?= __('Name') ?>" />
                                </div>

                                <div class="mb-3">
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

                                <?php if (Settings::instance()->get('newsletter_signup_terms')): ?>
                                    <div class="form-group">
                                        <label class="newsletter-signup-terms d-flex">
                                            <?= Form::ib_checkbox(null, 'terms', 1, false, ['class' => 'validate[required]', 'id' => 'newsletter-form-terms']) ?>
                                            <div class="newsletter-signup-terms-text"><?= Settings::instance()->get('newsletter_signup_terms') ?></div>
                                        </label>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <button type="submit" class="button bg-success w-100" id="submit-newsletter">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php // News feed layout ?>

        <?php $categories = ORM::factory('News_Category')->order_by('order')->order_by('category')->find_all_published(); ?>

        <?php if (trim($page_data['content'])): ?>
            <div class="fullwidth news-list-intro">
                <div class="row content_area">
                    <div class="page-content"><?= trim($page_data['content']) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="fullwidth news-category-tabs-section">
            <div class="container news-category-tabs clearfix">
                <?php foreach ($categories as $category): ?>
                    <a
                        href="/news/<?= $category->category ?>"
                        class="news-category-tab<?= $category->category == $current_category->category ? ' bg-category active' : ''?>"
                        data-category="<?= htmlspecialchars($category->category) ?>"
                        >
                        <h3 class="m-0"><?= htmlspecialchars($category->category) ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="fullwidth">
            <div class="container page-content">
                <div class="row gutters d-md-flex flex-wrap" id="news-feed"
                     data-category_id="<?= $current_category->id ?>"
                     data-limit="<?= $items_per_page ?>"
                     data-total="<?= count($news) ?>"
                >
                    <?php for ($i = 0; $i < count($news) && $i < $items_per_page; $i++): ?>
                        <div class="col-sm-6 col-md-4 d-md-flex mb-4 mb-md-5">
                            <?= View::factory('front_end/snippets/news_item_embed')->set('item', $news[$i])->set('button_class', 'bg-category'); ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <?php if ($items_per_page < count($news)): ?>
                    <div class="text-center my-5 px-4 d-flex d-sm-block" id="news-feed-see_more-wrapper">
                        <button type="button" class="button bg-category px-5 d-block d-sm-inline-block" style="flex-grow: 1;" id="news-feed-see_more">See more</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
            $('#news-feed-see_more').on('click', function() {
                var $feed    = $('#news-feed');
                var data = {
                    category_id: $feed.data('category_id'),
                    limit: $feed.data('limit'),
                    offset: $feed.find('.news-feed-item').length
                };

                $.ajax({url: '/frontend/news/ajax_get_news_html', data: data}).done(function(result) {
                    // Append new HTML
                    if (result.items_html) {
                        var html = '';
                        for (var i = 0; i < result.items_html.length; i++) {
                            html += '<div class="col-sm-6 col-md-4 d-md-flex mb-5">'+result.items_html[i]+'</div>';
                        }
                        $feed.append(html);
                    }

                    // Remove the button if everything has loaded
                    if ($feed.find('.news-feed-item').length >= $feed.data('total')) {
                        $('#news-feed-see_more-wrapper').addClass('hidden');
                    }
                });
            });
        </script>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>
