<div class="mail-title">
    <h2><span id="messaging-compose-search-recipient_type">To</span>: <?= __('Find a contact') ?></h2>
    <button type="button" class="btn-link basic_close"><span class="fa icon-times" aria-hidden="true"></span></button>
</div>

<div class="padding10">
    <p><?= __('Select a condition below to create your search to find your contacts.') ?></p>

    <form id="mcontact-filter" onsubmit="return false;">
        <div class="form-row gutters">
            <div class="col-sm-6">
                <?php
                $options = array(
                    '' => __('Select a condition'),
                    'contact_type' => __('Contact type'),
                    'notification_preferences' => __('Notification preferences'),
                );
                if (Model_Plugin::is_loaded('courses')) {
                    $options['course'] = __('Course');

                    if (Settings::instance()->get('courses_schedule_interviews_enabled') == 1) {
                        $options['interview'] = __('Interview');
                    }
                }

                if (Model_Plugin::is_loaded('bookings')) {
                    $options['booking_status'] = __('Booking status');
                }

                if (Model_Plugin::is_loaded('contacts3')) {
                    $options['contact_label'] = __('Contact label');
                }

                $attributes = array('id' => 'messaging-compose-search-condition');
                echo Form::ib_select(null, 'condition', $options, null, $attributes);
                ?>
            </div>

            <div class="col-sm-6">
                <button type="button" class="btn form-btn btn-default hidden" id="messaging-compose-search-condition-add"><?= __('Add') ?></button>
            </div>
        </div>

        <?php
        $options = array();
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $types = Model_Contacts3::get_types();
            foreach($types as $type) {
                $options[$type['contact_type_id']] = $type['display_name'];
            }
        } else {
            $mlists = Model_Contacts::get_mailing_lists();
            foreach($mlists as $mlist) {
                $options[$mlist['id']] = $mlist['name'];
            }
        }
        $attributes = array('multiple' => 'multiple');
        $args['multiselect_options'] = [
            'includeSelectAllOption' => true,
            'selectAllText' => __('ALL')
        ];

        echo View::factory('snippets/panel_collapse')->set([
            'type'      => 'primary',
            'class'     => 'messaging-condition-panel hidden',
            'id'        => 'messaging-condition-panel--contact_type',
            'collapsed' => false,
            'removable' => true,
            'title'     => 'Contact type',
            'body'      => Form::ib_select('Select contact types', 'contact_type[]', $options, null, $attributes, $args)
        ]);
        ?>

        <?php
                    $options = [];
                    if (!empty(Model_Preferences::get_all_preferences_grouped()['notification']))
                    foreach (Model_Preferences::get_all_preferences_grouped()['notification'] as $notification) {
                        // Currently, leave it so you can only select marketing updates
            if ($notification['stub'] === 'marketing_updates') {
                $options[$notification['id']] = $notification['label'];
            }
        }
        $attributes = array('multiple' => 'multiple');
        $args['multiselect_options'] = [
            'includeSelectAllOption' => true,
            'selectAllText' => __('ALL')
        ];

        echo View::factory('snippets/panel_collapse')->set([
            'type'      => 'primary',
            'class'     => 'messaging-condition-panel hidden',
            'id'        => 'messaging-condition-panel--notification_preferences',
            'collapsed' => false,
            'removable' => true,
            'title'     => 'Notification preferences',
            'body'      => Form::ib_select('Select notification preferences', 'notification_preferences[]',
                $options, null, $attributes, $args)
        ]);
        ?>

        <?php if (Model_Plugin::is_loaded('courses')): ?>
            <?php ob_start(); ?>
                <div class="form-row">
                    <?php
                    $plocations = Model_Locations::get_parent_locations();
                    $options = array();
                    foreach ($plocations as $plocation) {
                        $options[$plocation['id']] = $plocation['name'];
                    }
                    $attributes = ['multiple' => 'multiple', 'id' => 'messaging-contact-course-location-finder'];
                    $args       = [
                        'multiselect_options' => [
                            'enableCaseInsensitiveFiltering' => true,
                            'enableFiltering' => true,
                            'filterPlaceholder' => __('Search locations'),
                            'nonSelectedText'   => __('Select locations'),
                            'selectAllText' => __('ALL')
                        ]
                    ];
                    echo Form::ib_select('Select locations', 'location_ids[]', $options, null, $attributes, $args);
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $categories = Model_Categories::get_all_categories();
                    $options = array();
                    foreach ($categories as $category) {
                        $options[$category['id']] = $category['category'];
                    }
                    $attributes = array('multiple' => 'multiple', 'id' => 'messaging-contact-course-category-finder');
                    $args       = [
                        'multiselect_options' => [
                            'enableCaseInsensitiveFiltering' => true,
                            'enableFiltering'                => true,
                            'filterPlaceholder'              => __('Search course categories'),
                            'includeSelectAllOption'         => true,
                            'nonSelectedText'                => __('Select course categories'),
                            'selectAllText'                  => __('ALL')
                        ]
                    ];
                    echo Form::ib_select('Select categories', 'category_ids[]', $options, null, $attributes, $args);
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $schedules = Model_Schedules::search(array('after' => date::now()));
                    $options = array();
                    foreach ($schedules as $schedule) {
                        $num_students = count(Model_Schedules::get_students($schedule['id']));
                        $options[$schedule['id']] = '#' . $schedule['id'] . ' - ' . $schedule['name'] . ' - ' . $schedule['trainer'] . ' - ' . date('D H:i', strtotime($schedule['start_date'])) . ' (' . $num_students . ')';
                    }
                    $attributes = array('multiple' => 'multiple', 'id' => 'messaging-contact-course-schedule-finder');
                    $args       = array(
                        'multiselect_options' => array(
                            'enableCaseInsensitiveFiltering' => true,
                            'enableFiltering'                => true,
                            'filterPlaceholder'              => __('Search schedules'),
                            'includeSelectAllOption'         => true,
                            'nonSelectedText'                => __('Select schedules'),
                            'selectAllText'                  => __('ALL')
                        )
                    );

                    echo Form::ib_select('Select schedules', 'schedule_ids[]', $options, null, $attributes, $args);
                    ?>
                </div>

                <div class="hidden">
                    <?php $days = array('1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat', '0' => 'Sun'); ?>
                    <?php foreach ($days as $key => $day): ?>
                        <label class="checkbox-icon">
                            <input class="booking-register-day" type="checkbox" name="days[]" value="<?= $key ?>" />
                            <span class="checkbox-icon-unchecked btn btn-default"><?= $day ?></span>
                            <span class="checkbox-icon-checked btn btn-primary"><?= $day ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php $body = ob_get_clean(); ?>

            <?php
            if (Model_Plugin::is_loaded('courses')) {
                echo View::factory('snippets/panel_collapse')->set([
                    'type'      => 'primary',
                    'class'     => 'messaging-condition-panel hidden',
                    'id'        => 'messaging-condition-panel--course',
                    'collapsed' => false,
                    'removable' => true,
                    'title'     => 'Course',
                    'body'      => $body
                ]);
            }
            ?>

            <?php ob_start(); ?>
                <div class="panel-body messaging-condition-panel-body collapse in" id="messaging-condition-panel-body--course">
                    <div class="form-row">
                        <?php
                        $options = array(
                            'Not Scheduled' => __('Not Scheduled'),
                            'Scheduled' => __('Scheduled'),
                            'No Follow Up' => __('No Follow Up'),
                            'No Show' => __('No Show'),
                            'Waiting List' => __('Waiting List'),
                            'Interviewed' => __('Interviewed'),
                            'Accepted' => __('Accepted'),
                            'Offered' => __('Offered'),
                            'No Offer' => __('No Offer'),
                            'Cancelled' => __('Cancelled'),
                            'On Hold' => __('On Hold')
                        );
                        echo Form::ib_select(null, 'interview_status', $options, null, null, null);
                        ?>
                    </div>
                </div>

                <div class="form-row">
                    <?php
                    $courses = Model_Courses::get_all();
                    $options = array(
                        '' => ''
                    );
                    foreach ($courses as $course) {
                        $options[$course['id']] = '#' . $course['id'] . ' - ' . $course['title'];
                    }
                    $attributes = array();
                    $args = array();
                    echo Form::ib_select(null, 'interview_course_id', $options, null, $attributes, $args);
                    ?>
                </div>

                <script>
                $("[name=interview_course_id]").on("change", function(){
                    $.get(
                        '/admin/courses/autocomplete_schedules?course_id=' + this.value + "&term=",
                        function (response) {
                            $("[name='interview_schedule_id[]']").html('');
                            for(var i in response) {
                                $("[name='interview_schedule_id[]']").append('<option value="' + response[i].value + '">' + response[i].label + '</option>');
                                $("[name='interview_schedule_id[]']").multiselect('rebuild');
                            }
                        }
                    )
                });
                </script>

                <div class="form-row">
                    <?php
                    $attributes = array('multiple' => 'multiple');
                    $args       = array(
                        'multiselect_options' => array(
                            'enableCaseInsensitiveFiltering' => true,
                            'enableFiltering'                => true,
                            'filterPlaceholder'              => 'Search schedules',
                            'nonSelectedText'                => __('Select schedules')
                        )
                    );
                    echo Form::ib_select(null, 'interview_schedule_id[]', array(), null, $attributes, $args);
                    ?>
                </div>
            <?php $body = ob_get_clean(); ?>

            <?php
            echo View::factory('snippets/panel_collapse')->set([
                'type'      => 'primary',
                'class'     => 'messaging-condition-panel hidden',
                'id'        => 'messaging-condition-panel--interview',
                'collapsed' => false,
                'removable' => true,
                'title'     => 'Interview',
                'body'      => $body
            ]);
            ?>
        <?php endif ?>

        <?php
        $status_options= ORM::factory('Booking_Status')
            ->find_all_undeleted()
            ->as_options(['id_column' => 'status_id', 'please_select' => false]);
        $attributes = ['multiple' => 'multiple'];
        $args = [
            'multiselect_options' => [
                'includeSelectAllOption' => true,
                'selectAllText' => __('ALL')
            ]
        ];

        echo View::factory('snippets/panel_collapse')->set([
            'type'      => 'primary',
            'class'     => 'messaging-condition-panel hidden',
            'id'        => 'messaging-condition-panel--booking_status',
            'collapsed' => false,
            'removable' => true,
            'title'     => 'Booking status',
            'body'      => Form::ib_select('Select status', 'booking_status_ids[]', $status_options, null, $attributes, $args)
        ]);
        ?>

        <?php
        $label_options = Model_Contacts3_Tag::get_all()->as_options(['name_column' => 'label', 'please_select' => false]);
        $attributes = ['multiple' => 'multiple'];
        echo View::factory('snippets/panel_collapse')->set([
            'type'      => 'primary',
            'class'     => 'messaging-condition-panel hidden',
            'id'        => 'messaging-condition-panel--contact_label',
            'collapsed' => false,
            'removable' => true,
            'title'     => 'Contact label',
            'body'      => Form::ib_select('Select status', 'contact_tag_ids[]', $label_options, null, $attributes, $args)
        ]);
        ?>

        <div class="text-center">
            <button
                type="button"
                class="btn btn-primary form-btn text-uppercase hidden"
                id="messaging-find_contacts-button"
            ><?= __('Find Contacts') ?></button>
        </div>
    </form>
    <div class="hidden" id="messaging-compose-search-contacts-list">
        <div class="row gutters">
            <div class="col-sm-9">
                <h3><?= __('Select your contacts') ?></h3>
            </div>
            <div class="col-sm-3 text-right">
                <button class="btn-link" id="messaging-compose-search-select_contact-all">select all</button>
            </div>
        </div>

        <?php $checkbox_attributes = array('class' => 'messaging-compose-search-select_contact'); ?>
        <table class="table table-striped dataTable" id="messaging-compose-search-contacts-table">
            <thead>
                <tr>
                    <th scope="col">Prefs</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="messaging-compose-search-contacts-table-notification-label">Email</th>
                    <th scope="col">Contact type</th>
                    <th scope="col">Select</th>
                </tr>
            </thead>

            <tfoot class="hidden">
                <tr id="messaging-compose-search-contacts-template">
                    <td class="text-center">
                        <span class="icon-phone invisible"></span>&nbsp;
                        <span class="icon-envelope invisible"></span>&nbsp;
                        <span class="icon-mobile invisible"></span>
                    </td>

                    <td>
                        <span class="messaging-search-contact-name"></span>
                    </td>

                    <td>
                        <span class="messaging-search-contact-notification"></span>
                    </td>

                    <td>
                        <span class="messaging-search-contact-primary"></span>
                    </td>

                    <td>
                        <?= Form::ib_checkbox(
                            null,
                            'contact_ids[]',
                            '',
                            false,
                            array(
                                'class' => 'messaging-compose-search-select_contact'
                            )
                        ) ?>
                        <span class="messaging-search-contact-na" title="<?= __('I don\'t want to receive emails') ?>" data-toggle="tooltip" data-placement="top" title="<?= htmlentities(__('I don&#39;t want to receive emails')) ?>">
                            <span class="icon-ban-circle"></span>
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="form-row text-center hidden" id="messaging-compose-search-contacts-counter_report">
            <strong class="singular_text hidden"><?= __('1 contact selected') ?></strong>
            <strong class="plural_text hidden"><?= __('X contacts selected', array('X' => '<span id="messaging-compose-search-contacts-counter"></span>')) ?></strong>

            <button type="button" class="btn-link" id="messaging-compose-search-select_contact-clear"><?= __('clear') ?></button>
        </div>

        <div class="text-center form-actions">
            <button type="button" class="btn btn-primary"><?= __('Add') ?></button>
            <?php if (Settings::instance()->get('messaging_popout_display_add_primary_contact') == 1) { ?>
            <button type="button" class="btn btn-primary add_primary"><?= __('Add with primary contacts') ?></button>
            <?php } ?>
            <button type="button" class="btn-cancel"><?= __('Cancel') ?></button>
        </div>
    </div>
</div>
