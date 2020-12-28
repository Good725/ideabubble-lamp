<?php
$finder_mode  = Settings::instance()->get('course_finder_mode');
$provider_ids = Model_Providers::get_providers_for_host();
$locations    = Model_Locations::get_locations_without_parent(null, $provider_ids);
$subjects     = Model_Subjects::get_all_subjects(array('publish' => true, 'must_have_categories' => true));

if (Model_Plugin::is_enabled_for_role('Administrator', 'events')) {
    $search_title = __('Find Your Event');
} else if (Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
    $search_title = __('Find Your Course');
} else {
    $search_title = __('Find');
}

if ($finder_mode == 'event_promoter') {
    $form_action = '/events';
} else {
    $form_action = $account_bookings ? '/available-results.html' : '/course-list.html';
}
if (isset($provider_ids[0])) {
    $provider = Model_Providers::get_provider($provider_ids[0]);
    if ($provider && $provider['list_url']) {
        $form_action = $provider['list_url'];
    }
}
$finder_fields = Model_Courses::finder_mode_settings($finder_mode);
?>
<?php // todo: remove now-duplicate CSS from forms.css, template CSS etc. ?>
<link rel="stylesheet" href="<?= URL::get_engine_assets_base() ?>css/finder.css" />

<div class="banner-search<?= !empty($is_external) ? ' banner-search--external' : '' ?>">
    <div class="row">
        <h2 class="banner-search-title">
            <span class="fa fa-search"></span>
            <?= htmlspecialchars($search_title) ?>
        </h2>

        <form action="<?= URL::base().trim($form_action, '/') ?>" class="validate-on-submit" id="banner-search-form" data-finder_mode="<?= $finder_mode ?>" method="get">
            <?php
            foreach ($finder_fields as $field) {
                include 'snippets/finder_field.php';
            }
            ?>

            <?php // Hidden input, with search criteria ?>
            <?php if ($finder_mode == 'event_promoter'): ?>
                <input type="hidden" name="event_id"  id="banner-search-event-id"  />
                <input type="hidden" name="county_id" id="banner-search-county-id" />
            <?php else: ?>
                <?php if ($account_bookings): ?>
                    <input type="hidden" name="location_id" id="banner-search-location-id" />
                    <input type="hidden" name="subject_id"  id="banner-search-subject-id"  />
                    <input type="hidden" name="year_id"     id="banner-search-year-id"     />
                    <input type="hidden" name="category_id" id="banner-search-category-id" />
                    <input type="hidden" name="topic_id"    id="banner-search-topic-id"    />
                    <input type="hidden" name="course_id"   id="banner-search-course-id"   />
                <?php else: ?>
                    <input type="hidden" name="location" id="banner-search-location-id" />
                    <input type="hidden" name="subject"  id="banner-search-subject-id"  />
                    <input type="hidden" name="year"     id="banner-search-year-id"     />
                    <input type="hidden" name="category" id="banner-search-category-id" />
                    <input type="hidden" name="topic"    id="banner-search-topic-id"    />
                    <input type="hidden" name="course"   id="banner-search-course-id"   />
                <?php endif; ?>
            <?php endif; ?>

            <?php
            // Get search history
            $str = '';
            if($account_bookings && isset($_COOKIE['last_search_parameters'])) {
                $cookie_data = json_decode($_COOKIE['last_search_parameters']);
                if(is_array($cookie_data) && !empty($cookie_data)){
                    $str .= '<span class="previous_search_text">'. htmlentities(__('Your searches:')).'</span>';
                    foreach ($cookie_data as $data){
                        foreach ($locations as $filter_location)
                        {
                            if ($data->location == $filter_location['id'])
                            {
                                $selected_location = $filter_location['name'];
                                if (isset($data->course)){
                                    $course_data = Model_Courses::get_course($data->course);
                                    $course = $course_data['title'];
                                }else {
                                    $course = '';
                                }
                                if (isset($data->subject)){
                                    $subject_data = Model_Subjects::get_subject($data->subject);
                                    $subject = $subject_data['name'];
                                }else {
                                    $subject = '';
                                }
                                if (isset($data->year)){
                                    $year_data = Model_Courses::get_year($data->year);
                                    $year = $year_data['year'];
                                    $data_year_id = $year_data['id'];
                                }else {
                                    $year = '';
                                    $data_year_id = "";
                                }

                                $data_subject_id=isset($data->subject) ? $data->subject : "";
                                $data_category_id=isset($data->category) ? $data->category : "";
                                $data_topic_id=isset($data->topic) ? $data->topic : "";

                                $str .= '<div class="last-search search_history"
                                                data-course_id="' . $data->course  . '"
                                                data-subject_id="' . $data_subject_id  . '"
                                                data-category_id="' . $data_category_id  . '"
                                                data-topic_id="' . $data_topic_id  . '"
                                                data-year-id="' . $data_year_id  . '"
                                                data-year-name="' . $year  . '"
                                                data-course_name="' . $course . '"
                                                data-subject_name="' . $subject . '"
                                                data-location_id="' . $filter_location["id"] . '"
                                                data-location_name="' . $filter_location['name'] . '" >'

                                    .'<a href="#" id="last-search" class="tags" >'.$filter_location['name'] .(trim($subject) ? ', '.trim($subject) : '').'</a><span class="fa fa-times-circle remove_search_history" aria-hidden="true"></span>
                                            </div>';
                                continue;
                            }

                        }
                    }
                }

            }
            ?>


            <div class="banner-search-column banner-search-column--continue">
                <button type="submit" id="search-button" class="button button--continue"><?= __('Continue') ?></button>
            </div>
            <div class="clear"></div>
            <?= $str; ?>
        </form>
    </div>

    <script src="<?= URL::get_engine_assets_base() ?>js/finder_menu.js"></script>
</div>

