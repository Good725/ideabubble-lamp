<?php
$filters = Model_Courses::get_available_filters();
$filter_course_categories = $filters['categories'];
$filter_course_types      = ORM::factory('Course_Type')->find_all_published();
$filter_media_types       = ORM::factory('News_Item')->get_enum_options('media_type');
?>
<div class="hidden--tablet hidden--desktop clearfix news-filters-toggle-row">
    <button
        type="button"
        class="button--plain course-filters-toggle"
        data-hide_toggle=".filter-section[data-for=&quot;<?= $filter_for ?>&quot;]"
        data-hide_toggle-class="hidden--mobile"
        >
        <?= file_get_contents(ENGINEPATH.'plugins/courses/development/assets/images/filter_icon.svg') ?>
    </button>
</div>

<div class="row news-filters filter-section hidden--mobile" data-for="<?= $filter_for ?>">
    <div class="col-sm-3 news-filter-group" data-filter="course_category">
        <h3>
            <button
                type="button"
                class="news-filter-group-btn"
                data-hide_toggle="#news-filter-list-course_category"
                data-hide_toggle-class="hidden--tablet_up"
            >Choose category</button>
        </h3>

        <ul class="news-filter-group-list hidden--tablet_up" id="news-filter-list-course_category">
            <?php foreach ($filter_course_categories as $category): ?>
                <li>
                    <?= Form::ib_checkbox(
                        htmlspecialchars($category->category),
                        'course_category_ids[]',
                        $category->id,
                        false,
                        ['class' => 'update-results']
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="col-sm-3 news-filter-group" data-filter="course_type">
        <h3>
            <button
                type="button"
                class="news-filter-group-btn"
                data-hide_toggle="#news-filter-list-course_type"
                data-hide_toggle-class="hidden--tablet_up"
            >Choose type</button>
        </h3>

        <ul class="news-filter-group-list hidden--tablet_up" id="news-filter-list-course_type">
            <?php foreach ($filter_course_types as $type): ?>
                <li>
                    <?= Form::ib_checkbox(
                        htmlspecialchars($type->type),
                        'course_type_ids[]',
                        $type->id,
                        false,
                        ['class' => 'update-results']
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="col-sm-3 news-filter-group<?= isset($testimonials) ? ' hidden--mobile' : '' ?>" data-filter="media_type"
         <?php if (isset($testimonials)): ?>
             style="visibility: hidden" disabled="disabled"
         <?php endif?>
    >
        <h3>
            <button
                type="button"
                class="news-filter-group-btn"
                data-hide_toggle="#news-filter-list-media_type"
                data-hide_toggle-class="hidden--tablet_up"
            >Choose media</button>
        </h3>

        <ul class="news-filter-group-list hidden--tablet_up" id="news-filter-list-media_type">
            <?php foreach ($filter_media_types as $type): ?>
                <li>
                    <?= Form::ib_checkbox(
                        htmlspecialchars($type),
                        'media_types[]',
                        $type,
                        false,
                        ['class' => 'update-results']
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-sm-3 news-filter-group" data-filter="keyword">
        <h3 class="hidden--tablet_up">Search</h3>

        <button type="button" class="button--plain news-filter-reset hidden--tablet_up">
            reset criteria
        </button>

        <label class="sr-only" for="news-filter-keyword"><?= __('Keyword') ?></label>
        <div class="news-filter-keyword-input-wrapper input_group">
            <input class="form-input news-filter-keyword update-results" type="text" placeholder="<?= __('Keyword') ?>"
                   id="news-filter-keyword"
                   value="<?= !empty($_GET['keywords']) ? $_GET['keywords'] : '' ?>"/>
            <button type="button" class="button--plain input_group-icon update-results">
                <span class="icon_search flip-horizontally"></span>
            </button>
        </div>
    </div>
</div>

<script>
    // Dismiss dropdowns when clicked away from
    $(document).on('click', function (ev)
    {
        var $target    = $(ev.target);
        var is_menu    = ($target.hasClass('news-filter-group-list')  || $target.parents('.news-filter-group-list').length > 0);
        var is_toggle  = ($target.attr('data-hide_toggle') || $target.parents('[data-hide_toggle]').length > 0);
        var is_spinner = ($target.is('[style*="ajax-loader"]') || $target.find('[style*="ajax-loader"]').length > 0);

        // If the user has not clicked on the menu, the menu toggle or an AJAX spinner
        if (!is_menu && !is_toggle && !is_spinner) {
            $('.news-filter-group-list').addClass('hidden--tablet_up');
        }
    });

    // Reset fields when the reset button is clicked
    $('.news-filter-reset').click(function() {
        const $form = $(this).parents('.filter-section');

        $form.find(':input').each(function(i, element) {
            $(element).val('').prop('checked', false);
        });

        // Force refresh
        $form.find('[type="checkbox"]').first().change();
    });
</script>
