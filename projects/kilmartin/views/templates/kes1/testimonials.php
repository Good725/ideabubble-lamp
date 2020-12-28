<?php include 'template_views/header.php'; ?>

    <?php
    $category     = trim(urldecode(Request::current()->param('item_category')));
    $testimonials = ORM::factory('Testimonial')
        ->apply_filters()
        ->order_by('date_modified', 'desc')
        ->find_all_published();
    ?>

    <?php
    if (Settings::instance()->get('enable_testimonial_filters')) {
        $filter_for = 'testimonials';
        include 'template_views/news_filters.php';
    }
    ?>

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

                <section class="testimonials-section<?= count($testimonials) ? '' : ' hidden' ?>" id="testimonials-section">
                    <?= View::factory('front_end/testimonial_results')->set(compact('testimonials')) ?>
                </section>

                <p<?= count($testimonials) ? ' class="hidden"' : '' ?> id="testimonials-section-empty">
                    <?= htmlspecialchars(__('No results found.')) ?>
                </p>
            </div>

            <?php if (trim($page_data['footer'])): ?>
                <div class="page-footer row content_area pb-0">
                    <div class="page-content"><?= IbHelpers::parse_page_content($page_data['footer']) ?></div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php if (Settings::instance()->get('enable_news_filters')): ?>
        <script>
            // Update results when a filter is changed
            $('.filter-section[data-for="testimonials"]').on('change', '.update-results', function() {
                update_testimonial_results();
            });

            $('#testimonials-section').on('click', '.pagination a', function(ev) {
                ev.preventDefault();
                const page = $(this).data('page');
                update_testimonial_results(page);
            });

            function update_testimonial_results(page)
            {
                page = page || 1;

                const $filters = $('.filter-section[data-for="testimonials"]');

                // Get data...
                let keyword = $filters.find('.news-filter-keyword').val();
                let course_category_ids = [];
                let course_type_ids = [];
                $filters.find('[name="course_category_ids[]"]:checked').each(function() { course_category_ids.push(this.value); });
                $filters.find('[name="course_type_ids[]"]:checked'    ).each(function() {     course_type_ids.push(this.value); });

                let data = {
                    course_category_ids: course_category_ids,
                    course_type_ids: course_type_ids,
                    term: keyword,
                    page: page
                };

                $.ajax({
                    url: '/frontend/testimonials/ajax_get_paginated_testimonials_html',
                    data: data
                }).done(function(result) {
                    $('#testimonials-section')
                        .toggleClass('hidden', result.count == 0)
                        .html(result.html);

                    $('#testimonials-section-empty').toggleClass('hidden', result.count != 0);

                    $('#testimonials-section:visible, #testimonials-section-empty:visible, .filter-section[data-for="testimonials"]')[0].scrollIntoView();
                });
            }
        </script>
    <?php endif; ?>

<?php include 'views/footer.php'; ?>
