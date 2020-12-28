<?php
include 'template_views/header.php';
$course = ( ! empty($_GET['id']))
    ? Model_Courses::get_detailed_info(
        (int)$_GET['id'],
        true,
        true,
        (Settings::instance()->get('only_show_primary_trainer_course_dropdown') === '1'),
        true,
        ['book_on_website' => true]
    )
    : null;
$course_object = ORM::factory('Course')->where('id', '=', $course['id'] ?? '-1')->find_published();
?>

<?php
// Separate PHP block to avoid a conflict
$schedule_object  = ORM::factory('Course_Schedule')->where('id', '=', @$_GET['schedule_id'])->find_published();
$breadcrumb_type  = Settings::instance()->get('course_details_breadcrumbs');
$banner_text_type = Settings::instance()->get('course_details_banner_text');

$membership_discounts = $schedule_object->get_discounts(['member_only' => true, 'publish_on_web' => true]);
$courses_discounts_apply = Settings::instance()->get('courses_discounts_apply');
$additional_discounts = $schedule_object->get_discounts(['member_only' => false, 'publish_on_web' => true]);

if ($banner_text_type == 'accredited_provider') {
    $provider = $course_object->accreditation_providers()->find_undeleted();
    $banner_text = $provider->name ? htmlspecialchars(__('Accredited by $1', ['$1' => $provider->name])) : '&nbsp;';
}
elseif ($banner_text_type == 'summary') {
    $banner_text = $course_object->summary;
}
?>
<?php if ($course_object->id): ?>
<div style="<?= $course_object->get_color() ? ' --category-color:'.$course_object->get_color().';' : '' ?>">
    <div
        class="course-details-header fullwidth"
        <?php if ($course_object->banner): ?>
            style="background-image: url('<?= $course_object->get_banner_image_url() ?>'); --background-image: url('<?= $course_object->get_banner_image_url() ?>');"
        <?php endif; ?>
    >

        <div class="row">
            <div class="course-details-header-main">

                <?php if ($breadcrumb_type == 'subjects'): ?>
                    <h6 class="course-details-header-breadcrumbs">
                        Training
                        &gt; Course Topics
                        <?php if ($course_object->subject->id): ?>
                            &gt; <a href="/course-list?subject=<?= $course_object->subject->id?>"><?= htmlspecialchars($course_object->subject->name) ?></a>
                        <?php endif; ?>
                    </h6>
                <?php elseif ($breadcrumb_type == 'categories' && $course_object->category->category): ?>
                    <h6 class="course-details-header-breadcrumbs">
                        Programmes
                        &gt;
                        <?= htmlspecialchars($course_object->category->category) ?>
                    </h6>
                <?php endif; ?>

                <?php // This is a very arbitrary way of determining if a title is long enough to need extra space. ?>
                <h1 <?= (strlen(trim($course_object->title)) >= 40) ? ' class="long-title"' : '' ?>>
                    <?= htmlspecialchars($course_object->title) ?>
                </h1>

                <?php if (isset($banner_text)): ?>
                    <div class="course-details-header-summary<?= empty($banner_text) ? ' is-empty' : '' ?>">
                        <?= $banner_text ?>
                    </div>
                <?php endif; ?>

                <?php
                $current_step = 'details';
                include 'views/checkout_progress.php'
                ?>
            </div>
            <div id="course-details-menu-sticky-start"></div>

            <form
                method="post"
                action="#" data-checkout-type="<?= Settings::instance()->get('checkout_customization')?>"
                class="course-details-menu course-details-menu--collapsed"
                id="selectcform"
                data-action="<?= Model_Plugin::is_enabled_for_role('Administrator', 'Contacts3') ? '/booking-form' : '/checkout' ?>"
            >
            <div class="course-details-menu-inner">
                <input type="hidden" name="interested_in_course_id" value="<?= $course_object->id ?>"/>

                <?php include 'template_views/course_details_options.php'; ?>
                <?php $applied_members_discount = 0;
                    $max_membership_discount = 0;
                    $min_membership_discount = PHP_INT_MAX;
                    $applied_members_discount_summary = '';
                    if($membership_discounts !== false) {
                        if ($courses_discounts_apply == 'Minimum') {
                            foreach ($membership_discounts as $membership_discount) {
                                $membership_discount_amount = $membership_discount->get_amount();
                                if ($membership_discount_amount <= $min_membership_discount ) {
                                    $min_membership_discount = $membership_discount_amount;
                                    $applied_members_discount_summary = $membership_discount->get_summary();
                                }
                            }
                            $applied_members_discount = $min_membership_discount;
                        } elseif($courses_discounts_apply == 'Maximum') {
                            foreach ($membership_discounts as $membership_discount) {
                                $membership_discount_amount = $membership_discount->get_amount();
                                if ($membership_discount_amount >= $max_membership_discount) {
                                    $max_membership_discount = $membership_discount_amount;
                                    $applied_members_discount_summary = $membership_discount->get_summary();
                                }
                            }
                            $applied_members_discount = $max_membership_discount;
                        } elseif($courses_discounts_apply == 'All') {
                            foreach ($membership_discounts as $membership_discount) {
                                $applied_members_discount += $membership_discount->get_discount_for_fee($cheapest);
                                $applied_members_discount_summary .= ' ' . $membership_discount->get_summary();

                            }
                        } else {
                            $applied_members_discount = 0;
                        }
                    }
                    ?>
                <?php if ($membership_discounts !== false &&  $cheapest - $applied_members_discount != $cheapest): ?>
                    <div class="course-details-menu-header with_discount">
                        <?php
                        $auth_contact      = Auth::instance()->get_contact();
                        $is_auth_member    = $auth_contact->id &&  $auth_contact->has_tag('special_member');
                        $is_auth_nonmember = $auth_contact->id && !$auth_contact->has_tag('special_member');
                        ?>

                        <h2 class="course-details-price-header with_discount d-flex align-items-center">
                            <span class="m-auto text-center<?= $is_auth_member ? ' unavailable' : '' ?>">
                                <span class="cdh-price course-details-price--nonmember">&euro;<?= str_replace('.00', '', $cheapest) ?></span>
                                <span class="course-details-price-description">Non-member rate</span>
                            </span>

                            <span class="m-auto text-center<?= $is_auth_nonmember ? ' unavailable' : '' ?>">
                                <span id="member-discount-applied" class="hidden"><?=$applied_members_discount_summary?></span>
                                <span class="cdh-price course-details-price--member">&euro;<?= $cheapest - $applied_members_discount > 0 ? str_replace('.00', '', $cheapest - $applied_members_discount) : '0.00'?></span>
                                <span class="course-details-price-description"><?= Settings::instance()->get('company_title') ?> member rate</span>
                            </span>

                            <button type="button" class="course-details-collapse" id="course-details-collapse">
                                <span class="sr-only">Expand / collapse</span>
                            </button>
                        </h2>
                    </div>
                <?php else: ?>
                    <div class="course-details-menu-header">
                        <h2 class="course-details-price-header d-flex align-items-center">
                            <span class="course-details-price">&euro;<?= str_replace('.00', '', $cheapest) ?></span>
                            <span class="course-details-price-per">per person</span>

                            <span class="course-details-collapsed_title">Booking</span>

                            <button type="button" class="course-details-collapse" id="course-details-collapse">
                                <span class="sr-only">Expand / collapse</span>
                            </button>
                        </h2>
                    </div>
                <?php endif; ?>

                <?php
                    $max_additional_discount = 0;
                    $min_additional_discount = PHP_INT_MAX;
                    $additional_discount_summary = array();
                    if(!empty($additional_discounts)) {
                        if ($courses_discounts_apply == 'Minimum') {
                            foreach ($additional_discounts as $additional_discount) {
                                if ($additional_discount->preview_discount_on_fee($cheapest) != $cheapest
                                    && $additional_discount->get_summary()
                                    && !$additional_discount->get_code()) {
                                        $additional_discount_amount = $additional_discount->get_amount();
                                        if ($additional_discount_amount <= $min_additional_discount ) {
                                            $min_additional_discount = $additional_discount_amount;
                                            $additional_discount_summary = array($additional_discount->get_summary());
                                        }
                                    }
                                }
                        } elseif($courses_discounts_apply == 'Maximum') {
                            foreach ($additional_discounts as $additional_discount) {
                                if ($additional_discount->preview_discount_on_fee($cheapest) != $cheapest
                                    && $additional_discount->get_summary()
                                    && !$additional_discount->get_code()) {
                                    $additional_discount_amount = $additional_discount->get_amount();
                                    if ($additional_discount_amount >= $max_additional_discount) {
                                        $max_additional_discount = $additional_discount_amount;
                                        $additional_discount_summary = array($additional_discount->get_summary());
                                    }
                                }
                            }

                        } elseif($courses_discounts_apply == 'All') {
                            foreach ($additional_discounts as $additional_discount) {
                                if ($additional_discount->preview_discount_on_fee($cheapest) != $cheapest
                                    && $additional_discount->get_summary()
                                    && !$additional_discount->get_code()) {
                                        $additional_discount_summary[] = $additional_discount->get_summary();
                                }
                            }

                        }
                    }?>

                <?php foreach ($additional_discount_summary as $discount_summary): ?>
                            <div class="course-details-discount-description">
                                <?= htmlspecialchars($discount_summary) ?>
                            </div>
                    <?php endforeach; ?>

                <div class="course-details-menu-body">
                    <?php
                    if (Settings::instance()->get('course_layout_auto_details')) {
                        $label_text = 'Select location and dates';
                        $default_option_text = 'Choose location and date';
                        $book_text = 'Book Today';
                        $brochure_download_text = 'Download course brochure';
                    } else {
                        $label_text = 'Select your session';
                        $default_option_text = 'Select schedule';
                        $book_text = 'Book Now';
                        $brochure_download_text = 'Download Brochure <span class="fa fa-file-pdf-o"></span>';
                    }
                    ?>

                    <h6><?= htmlspecialchars($label_text) ?></h6>

                    <?php $group_bookings_only = $course_object->is_group_booking_only(); ?>

                    <div class="form-select-plain">
                        <?= Form::ib_select(null, 'interested_in_schedule_id', '<option value="">'.$label_text.'</option>'.$schedule_options, null, ['id' => 'schedule_selector', 'data-group_only' => (int)$group_bookings_only, 'class' => 'save-warning-exclude']) ?>
                    </div>

                    <?php // Hide this section, until the user selects a schedule. However, if there is only one schedule with group bookings, it's the only option, so no need to hide. ?>
                    <div class="d-flex align-items-center group_bookings_div<?= ($group_bookings_only) ? '' : ' hidden' ?>" style="justify-content: space-between;">
                        <h6>No. of attendees</h6>
                        <div class="ml-2 form-select-plain" style="width: 60px;">
                            <?php
                            // If there is only one schedule, use its max capacity
                            $options = [];
                            // current max number of attendees will be 5, NTH is to give this a setting.
                            for ($i = 1; $i <= 5; $i++) {
                                $options[$i] = $i;
                            }
                            echo Form::ib_select(null, 'num_delegates', $options, 1, ['class' => 'course-details-attendees save-warning-exclude', 'id' => 'num_delegates']);
                            ?>
                        </div>

                        <?php if (Auth::instance()->get_user()): ?>
                            <div class="text-center" style="flex: 1;">
                                <label class="checkbox-icon" title="<?= __('Add to wishlist') ?>">
                                    <input type="hidden" name="wishlist" value="0" />
                                    <input type="checkbox" class="course-details-wishlist-checkbox sr-only save-warning-exclude" id="course-details-wishlist-checkbox" name="wishlist" value="1" />
                                    <span class="checkbox-icon-unchecked"><?= file_get_contents(ENGINEPATH.'plugins/courses/development/assets/images/star.svg') ?></span>
                                    <span class="checkbox-icon-checked"><?= file_get_contents(ENGINEPATH.'plugins/courses/development/assets/images/star-filled.svg') ?></span>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty($course['schedules'])): ?>
                    <div class="course-details-actions">
                        <?php if (!empty($course['third_party_link'])): ?>
                                <a target="_blank"
                                   href="<?= (substr($course['third_party_link'], 0, 4) === "http") ? $course['third_party_link'] : "//" . $course['third_party_link'] ?>"
                                   class="button button--book w-100"
                                   id="apply-course"><?= __('Apply now') ?></a>

                        <?php elseif (Settings::instance()->get('courses_enable_bookings') == 1 &&
                            $course['book_button'] !== '0' && $course['payment_option_selected'] !== false): ?>
                            <button
                                type="submit"
                                class="button button--book w-100"
                                data-title="<?= urlencode($course['title']) ?>"
                                id="book-course"
                                data-id="0"
                                disabled="disabled"
                                ><?=$book_text?>
                            </button>
                        <?php endif; ?>

                        <?php if (Settings::instance()->get('course_enquiry_button') == 1): ?>
                            <button type="submit" formaction="/contact-us.html" formmethod="get" class="button bg-success w-100" id="course-details-enquire"><?= __('Enquire Now') ?></button>
                        <?php endif; ?>

                        <?php if ($account_bookings && $contact_id): ?>
                            <?php $in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $schedule_id))); ?>

                            <button class="button course-banner-button cl_bg wishlist_add<?= $in_wishlist == 0 ? '' : ' hidden' ?>" data-contact_id="<?= $contact_id ?>" data-schedule_id="<?=$schedule_id?>"><?= __('Add To Wishlist') ?></button>

                            <button class="button button--cl_remove course-banner-button cl_bg wishlist_remove<?= $in_wishlist == 0 ? ' hidden' : '' ?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule_id?>"><?= __('Remove From Wishlist') ?></button>
                        <?php endif; ?>
                        <button type="submit" formaction="/add-to-waitlist.html" id="add_to_waitlist_button" formmethod="get" class="button cl_bg w-100 hidden"><?= __('Add to Waitlist') ?></button>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($course_object->file_id || $course_object->use_brochure_template): ?>
                    <div class="course-details-menu-footer">
                        <button type="button" class="button--plain button--brochure" data-toggle="modal" data-target="#course-details-brochure-modal">
                            <?= $brochure_download_text ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            </form>
        </div>
    </div>

    <?php if (Settings::instance()->get('course_layout_auto_details')): ?>
        <div class="row">
            <div class="course-details-intro">
                <h3><?= htmlspecialchars($course_object->title) ?></h3>

                <?php $timeslots = $schedule_object->timeslots->find_all_undeleted() ?>
                <?php if (count($timeslots)): ?>
                    <div id="course-details-intro-duration-wrapper">
                        <hr />

                        <p>
                            <strong>Duration</strong>:
                            <span id="course-details-intro-duration"><?= count($timeslots) == 1 ? '1 day' : count($timeslots).' days' ?></span>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($course_object->level->level): ?>
                    <hr />

                    <p><strong>Level</strong>: <?= htmlspecialchars($course_object->level->level) ?></p>
                <?php endif; ?>

                <?php $accredited_by = $course_object->accreditation_providers()->find_undeleted(); ?>
                <?php if ($accredited_by->name): ?>
                    <hr />

                    <p><strong><?= __('Accredited by') ?></strong>: <?= htmlspecialchars($accredited_by->name) ?></p>
                <?php endif; ?>

                <?php $county = $schedule_object->location->get_county()->name; ?>

                <?php if (count($timeslots) || $county): ?>
                    <hr />

                    <?php
                    $items = [
                        [
                            'svg'     => 'location',
                            'text'    => $county,
                            'id'      => 'course-details-county',
                            'visible' => (bool) $county
                        ], [
                            'svg'     => 'clock',
                            'text'    => (count($timeslots) == 1) ? '1 day' : count($timeslots).' days',
                            'id'      => 'course-details-duration',
                            'visible' => count($timeslots) > 0
                        ]
                    ];

                    ?>

                    <ul class="course-details-intro-data<?= $county || count($items) ? '' : ' hidden' ?>">
                        <?php foreach ($items as $item): ?>
                            <li class="d-flex align-items-center<?= $item['visible'] ? '' : ' hidden' ?>">
                                <?= file_get_contents(ENGINEPATH.'plugins/courses/development/assets/images/'.$item['svg'].'.svg') ?>
                                <span id="<?= $item['id'] ?>"><?= htmlspecialchars($item['text']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div
                        class="course-details-intro-timeslots <?= count($timeslots) ? '' : ' hidden' ?>"
                        id="course-details-timeslots-dates"
                        data-date_format="D j M Y | H:i"
                    >
                        <ul>
                            <?php foreach ($timeslots as $timeslot): ?>
                                <li>
                                    <?= date('D j M Y | H:i - ', strtotime($timeslot->datetime_start)) ?>
                                    <?= date('H:i', strtotime($timeslot->datetime_end)) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($course_object->description): ?>
        <div class="row page-content course-page-content">
            <?= IbHelpers::expand_short_tags(IbHelpers::parse_block_editor($course_object->description)) ?>
        </div>
    <?php endif; ?>

    <?php ob_start(); ?>
        <form id="brochure_download" class="" action="/frontend/formprocessor/course_brochure_download" method="post">
            <script>
                $("#brochure_download").on("submit", function(){
                    if ($(this).validationEngine('validate')) {
                        $("#course-details-brochure-modal").modal("hide");
                    } else {
                        return false;
                    }

                });
            </script>
            <input type="hidden" name="course_id" value="<?=$course['id']?>" />
            <input type="hidden" name="schedule_id" value="" />

            <div class="form-row">
                <img src="<?= Model_Media::get_image_path(Settings::instance()->get('site_logo'), 'logos') ?>" width="200" alt="" class="d-block m-auto" />

                <h6 class="mb-2 text-primary" style="font-size: 1rem; line-height: 1.375;">To download a copy of our course brochure, please fill in your details:</h6>
            </div>

            <div class="form-row">
                <?= Form::ib_input('First name', 'first_name', null, ['class' => 'validate[required]', 'id' => 'course-details-brochure-first_name']) ?>
            </div>

            <div class="form-row">
                <?= Form::ib_input('Last name', 'last_name', null, ['class' => 'validate[required]', 'id' => 'course-details-brochure-last_name']) ?>
            </div>

            <div class="form-row">
                <?= Form::ib_input('Email', 'email', null, ['class' => 'validate[required,custom[email]]', 'id' => 'course-details-brochure-email']) ?>
            </div>

            <div class="form-row">
                <?= Form::ib_input('Phone number', 'telephone', null, ['id' => 'course-details-brochure-telephone']) ?>
            </div>

            <div class="form-row">
                <p class="mb-0" style="font-size: 11px;">
                    <?= __(
                        'By submitting this form, you agree that we may use your data to contact you with information related to this specific course. To learn more, see our {{privacy policy}}.',
                        [
                            '$1' => Settings::instance()->get('company_name'),
                            '{{' => !empty( Settings::instance()->get('company_site')) ? '<a href="' . Settings::instance()->get('company_site') . '/privacy-statement" target="_blank">' : '<a href="/privacy-policy">',
                            '}}' => '</a>'
                        ]
                    ) ?>
                </p>
            </div>

            <div>
                <button type="submit" class="button bg-success w-100">Submit</button>
            </div>
        </form>
    <?php $modal_body = ob_get_clean(); ?>


    <?php
    echo View::factory('front_end/snippets/modal')
        ->set('class', 'course-details-brochure-modal')
        ->set('id', 'course-details-brochure-modal')
        ->set('width', '395px')
        ->set('body', $modal_body)
    ;
    ?>
</div>
<?php else: ?>
    <div class="row page-content course-page-content">
        <h1><?= htmlspecialchars(__('Course not found')) ?></h1></div>
<?php endif; ?>

<div id="course-details-menu-sticky-end"></div>

<?php include 'views/footer.php'; ?>