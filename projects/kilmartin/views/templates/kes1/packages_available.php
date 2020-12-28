<?php include 'template_views/header.php';?>

<?php
$is_student = false;
$contact = null;
if (class_exists('Controller_FrontEnd_Bookings')) {

$cb = new Controller_FrontEnd_Bookings(Request::$current, new Response());
$cart = Session::instance()->get('ibcart');
if (!$cart) {
    $cart = array(
        'booking' => array(),
        'booking_id' => null,
        'client_id' => null,
        'discounts' => array(),
        'courses' => array()
    );
}

if (!$cart['booking']) {
    $cart['booking'] = array();
}
if (!@$cart['courses']) {
    $cart['courses'] = array();
}
$cart = $cb->get_cart_data($cart['booking'], $cart['booking_id'], $cart['client_id'], $cart['discounts'], $cart['courses']);
$course_amend_fee_percent = Settings::instance()->get('course_amend_fee_percent');
$user            = Auth::instance()->get_user();
$contacts        = Model_Contacts3::get_contact_ids_by_user($user['id']);
if (@$user['id']) {
    $contact = current(Model_Contacts3::get_contact_ids_by_user($user['id']));
    $contact_id = @$contact['id'];
    $contact_details = new Model_Contacts3($contact_id);
    $is_student = $contact_details->has_role('student');
}
if (count($contacts)) {
    $contact = new Model_Contacts3($contacts[0]['id']);
    $family = new Model_Family($contact->get_family_id());
    $family_members = Model_Contacts3::get_family_members($contact->get_family_id());
    $students = array();
    foreach ($family_members as $family_member) {
        if (in_array('student', $family_member['has_roles']) || in_array('mature', $family_member['has_roles'])) {
            $students[] = $family_member;
        }
    }
} else {
    $contact = null;
    $family = null;
    $family_members = array();
    $students = array();
}

$discounts = array();
if (count($_GET) == 0) {
    $last_search_parameters = Session::instance()->get('last_search_params');
    if (!empty($last_search_parameters)) {
        $_GET = $last_search_parameters;
    }
}
$selected_student_id = @$_REQUEST['student_id'];
// set last search history into cookie
$cookie_name = "last_search_parameters";
$arr = Array();

if (isset($_COOKIE[ $cookie_name ])) {

    $cookie_data = json_decode($_COOKIE[ $cookie_name ]);

    if (isset($_GET[ 'location_id' ]) && isset($_GET[ 'course_id' ])) {
        $cookie = array();
        $cookie[ 'location' ] = $_GET[ 'location_id' ];
        $cookie[ 'course' ] = $_GET[ 'course_id' ];
        $cookie[ 'subject' ] = $_GET[ 'subject_id' ];
        $cookie[ 'year' ] = $_GET[ 'year_id' ];

        array_push($cookie_data, (object)$cookie);
    }

    $result = array_unique($cookie_data, SORT_REGULAR);
    if(sizeof($result)>5){
        array_shift($result);
    }
    setcookie($cookie_name, json_encode($result), time() + ( 86400 * 30 ), "/");
}
else {

    if (isset($_GET[ 'location_id' ]) && isset($_GET[ 'course_id' ])) {

        $cookie = array();
        $cookie[ 'location' ] = $_GET[ 'location_id' ];
        $cookie[ 'course' ] = $_GET[ 'course_id' ];
        $cookie[ 'course' ] = $_GET[ 'course_id' ];

        $cookies = array();
        $cookies[]=$cookie;
        setcookie($cookie_name, json_encode($cookies), time() + ( 86400 * 30 ), "/");
    }

}


	$bookings_folder_url = URL::get_project_plugin_assets_base('bookings');
	$shared_folder_url = URL::get_engine_assets_base();
	$view = '';
	$student_wrapper = '';
}

$filter_options = Model_Courses::get_available_filters();
?>

<?php
    $_GET = Kohana::sanitize($_GET);
    $location_id = false;
    $location_ids = false;
    $level = false;
    $subject_id = false;
    $category_id = false;
    $topic_id = false;
    $course_id = false;
    $year = false;
    $page = 1;
    $args['location_ids'] = $args['category_ids'] = array();
    $add_to_cart_schedule_id = @$_GET['add_to_cart_schedule_id'];
    $add_to_cart_timeslot_id = @$_GET['add_to_cart_timeslot_id'];
    $add_to_cart_contact_id = @$_GET['add_to_cart_contact_id'];

    if ( ! empty($_GET['location_id'])) { 
        if(false !== strpos($_GET['location_id'], ',')){//several locations
            $args['location_ids'] = $location_ids = explode(',', $_GET['location_id']);
        }
        else{
            $args['location_ids'][0] = $location_id = $_GET['location_id'];
        }
    }
    if ( ! empty($_GET['location_ids'])) { $args['location_ids'] = $_GET['location_ids'];  }
    if ( ! empty($_GET['subject_id'])) { $args['subject_ids'][0] = $subject_id = $_GET['subject_id']; }
    if ( ! empty($_GET['subject_ids'])) { $args['subject_ids'] = $_GET['subject_ids']; }

    if ( ! empty($_GET['category_id'])) { $args['category_ids'][0] = $category_id = $_GET['category_id']; }
    if ( ! empty($_GET['category_ids'])) { $args['category_ids'] = $_GET['category_ids']; }

    if ( ! empty($_GET['topic_id'])) { $args['topic_ids'][0] = $topic = $_GET['topic_id']; }
    ( ! empty($_GET['topic_ids'])) ? $args['topic_ids']= $_GET['topic_ids'] : $args['topic_ids'] = array();

    if ( ! empty($_GET['course_id']))   { $args['course_ids'][0]   = $course_id   = $_GET['course_id']; }
    if ( ! empty($_GET['course_ids']))   { $args['course_ids'] = $_GET['course_ids']; }

    $args['year_ids'] = array();
    if ( ! empty($_GET['year_id']))     { $args['year_ids'][0]         = $year     = $_GET['year_id'];     }
    if( ! empty($_GET['year_ids'])) $args['year_ids'] = $_GET['year_ids'];

     ( ! empty($_GET['keyword'])) ? $keyword = $_GET['keyword'] : $keyword=null;

    if ( ! empty($_GET['level']))    { $args['level']           = $level    = $_GET['level'];    }
    ( ! empty($_GET['level_ids'])) ? $args['level_ids']= $_GET['level_ids'] : $args['level_ids'] = array();

    if ( ! empty($_GET['type']))    { $args['type']           = $level    = $_GET['type'];    }
    ( ! empty($_GET['type_ids'])) ? $args['type_ids']= $_GET['type_ids'] : $args['type_ids'] = array();

?>

<?php if ( isset($alert) || ! empty($_POST['cart_emptied'])): ?>
    <div class="row">
        <?php
        echo (isset($alert)) ? $alert : '';

        if ( ! empty($_POST['cart_emptied'])) {
            IbHelpers::set_message(__('You have no items in your cart.'));
            echo IbHelpers::get_messages();
        }
        ?>
    </div>
<?php endif; ?>
<div id="msg_area"></div>

<form
    method="post"
    action="/checkout"
    id="cart-submit-form"
    data-contact_id="<?= $add_to_cart_contact_id ?>"
    data-timeslot_id="<?= $add_to_cart_timeslot_id ?>"
    data-schedule_id="<?= $add_to_cart_schedule_id ?>"
    <?= $is_student ? ' data-is_student="true"' : '' ?>
    >
	<div class="search-content">
        <div class="row">
        	<div class="left-section">
                <?php
                // Needs to be updated to use the checkout_progress.php view
                ?>
	        	<div class="checkout-progress">
	        		<ul>
	        			<li class="prev"><a href="home.html"><p><?= htmlentities(__('Home')) ?></p>
	        				<span></span></a>
	        			</li>
	        			<li class="curr"><a><p><?= htmlentities(__('Availability')) ?></p>
	        				<span></span></a>
	        			</li>
	        			<li><a><p><?= htmlentities(__('Checkout')) ?></p>
	        				<span></span></a>
	        			</li>
	        			<li><a><p><?= htmlentities(__('Thank you')) ?></p>
	        				<span></span></a>
	        			</li>
	        		</ul>
	        		<span class="pro-box-availability"></span>
                    <span class="unvisited-box-availability"></span>
	        	</div>
	        	
	        </div>
        </div>

        <div class="row" id="header_paging_controle">
        	<div class="left-section">
                <div class="availability-result_counters border-top-bottom fullwidth--mobile">
                    <?php
                    $filters = array(
                        array('name' => 'location', 'label' => 'Location', 'options' => $filter_options['locations'],  'option_name' => 'name'),
                        array('name' => 'trainer',  'label' => 'Teacher',  'options' => $filter_options['trainers'],   'option_name' => 'full_name'),
                        array('name' => 'subject',  'label' => 'Subject',  'options' => $filter_options['subjects'],   'option_name' => 'name'),
                        array('name' => 'category', 'label' => 'Category', 'options' => $filter_options['categories'], 'option_name' => 'category'),
                        array('name' => 'course',   'label' => 'Course',   'options' => $filter_options['courses'],    'option_name' => 'title'),
                        array('name' => 'year',     'label' => 'Year',     'options' => $filter_options['years'],      'option_name' => 'year'),
                        array('name' => 'level',    'label' => 'Level',    'options' => $filter_options['levels'],     'option_name' => 'level'),
                        array('name' => 'topic',    'label' => 'Topic',    'options' => $filter_options['topics'],     'option_name' => 'name'),
                        array('name' => 'cycle',    'label' => 'Cycle',    'options' => $filter_options['cycles'],     'option_name' => 'cycle'),
                        array('name' => 'is_fulltime', 'label' => 'Full Time', 'options' => array(array('id' => 'NO', 'is_fulltime' => 'NO'), array('id' => 'YES', 'is_fulltime' => 'YES')), 'option_name' => 'is_fulltime')
                    );
                    ?>

                    <?php $filter_count = 0; ?>

                    <?php ob_start(); ?>
                        <div class="available_results-filters-wrapper hidden--mobile fullwidth--mobile" id="available_results-filters-wrapper">
                            <div class="search-filters-blackout hidden--tablet hidden--desktop"></div>

                            <ul class="search-filters" id="available_results-filters">
                                <li class="search-filters-heading hidden--tablet hidden--desktop">
                                    <div class="row gutters">
                                        <div class="col-xs-6">
                                            <strong><?= __('Filter by:') ?></strong>
                                        </div>

                                        <div class="col-xs-6 text-right">
                                            <button type="button" class="button--plain search-filters-clear<?= ($filter_count > 0) ? ' visible' : '' ?>"><?= __('Clear all') ?></button>
                                        </div>
                                    </div>
                                </li>

                                <li class="search-filter-item search-filter-item--divider hidden--tablet hidden--desktop">
                                    <div class="dropdown search-filter-dropdown" data-autodismiss="false">
                                        <button class="btn-dropdown" type="button" data-toggle="dropdown" aria-expanded="true">
                                            <span class="search-filter-label"><?= __('Sort by') ?></span>
                                            <span class="arrow_caret-down search-filter-dropdown-icon"></span>

                                            <span class="search-filter-selected_items"></span>
                                        </button>

                                        <ul class="dropdown-menu features-dropdown-menu-id" role="menu">
                                            <?php $order_options = array('1' => __('Course title (A-Z)'), '2' => __('Package title (A-Z)'), '3' => __('Course type (A-Z)')); ?>

                                            <?php foreach ($order_options as $key => $option): ?>
                                                <li class="search-filter-dropdown-item">
                                                    <?= Form::ib_radio($option, 'availability-order_by', $key, false, array('class' => 'search-filter-radio')) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>

                                <?php foreach ($filters as $filter): ?>
                                    <?php if (count($filter['options'])): ?>
                                        <li class="search-filter-item"><?php include 'template_views/snippets/filter_dropdown.php'; ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <li class="hidden--mobile">
                                    <button type="button" class="search-filters-clear<?= $is_filtered ? ' visible' : '' ?>" id="search-filters-clear">
                                        Clear filters <span class="fa fa-times-circle-o" aria-hidden="true"></span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    <?php $filters_html = ob_get_clean(); ?>

                    <div class="row gutters hidden--tablet hidden--desktop">
                        <div class="col-xs-6"></div>

                        <div class="col-xs-6 text-right">
                            <button type="button" class="button--plain" id="search-filters-toggle" data-hide_toggle="#available_results-filters-wrapper" data-hide_toggle-class="hidden--mobile">
                                <?= __('Filters') ?>
                                <span class="search-filter-total"><?= $filter_count ? $filter_count : '' ?></span>
                                <span class="arrow_caret-down"></span>
                            </button>
                        </div>
                    </div>

                    <?= $filters_html ?>
                </div>

                <div>
                    <div class="border-top-bottom hidden--mobile">
                        <div class="pagination-and-search-results">
                            <div id="number_of_courses" class="pagination-new">
                                <select id="availability-order_by">
                                    <?php foreach ($order_options as $key => $option): ?>
                                        <option value="<?= $key ?>"><?= $option ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <ul>
                                    <li id="results_count">0 results</li>
                                </ul>

                            </div>
                            <div id="pagination-new" class="pagination-new">
                            </div>
                        </div>
                    </div>

                    <div id="search-offers" class="search-offers hidden--mobile" style="display: none" data-display-offers="<?=Settings::instance()->get('courses_search_display_offers')?>">
                        <h3>Offers</h3>
                        <ol></ol>
                    </div>

                    <div id="content_for_packages" class="search-package-available">
                        <!-- found packages from ajax call -->
                    </div>

                    <div id="content_for_courses" class="search-package-available hidden--mobile">
                        <!-- found courses from ajax call -->
                    </div>
                </div>

                <div id="content_for_courses--mobile-wrapper" class="hidden--tablet hidden--desktop">

                </div>
            </div>


	        <div class="right-section hidden--mobile" id="right-section">
	        	<div class="checkout-right-sect gray-box">
                    <?php include 'template_views/sidebar_cart.php'; ?>

                    <?php // Cart ?>
	        		<div class="purchase-packages">
		        		<div class="prepay-box">
                            <ul id="cart_total_container">
                                <?php // discounts go here  ?>

                                <li class="total">
                                    <p>
                                        <span class="left">TOTAL</span>
                                        <span class="right cart_total_amount">€0</span>
                                    </p>
                                </li>
		        			</ul>
		        		</div>
	        		</div>

                    <div class="hidden" id="booking-cart-notices"></div>

                    <div class="continue" <?=$contact == null && $user != null ? '' : 'onmouseover="checkIfCartEmpty()"'?>>
                        <input type="submit" value="Continue" class="button button--continue" id="continue-button" title="" <?=$contact == null && $user != null ? 'disabled="disabled"' : ''?>>
                    </div>

                    <a href="javascript:void(0);" class="item-summary-head" id="item-summary-head" data-toggle="collapse" data-target="#sidebar-search" aria-expanded="true">
                        <h4>Search
                            <span class="expand-icon fa fa-chevron-down"></span>
                        </h4>
                    </a>

                    <div class="sidebar-search collapse in" id="sidebar-search">
                        <label class="search-wrap">
                            <input id="search_keyword" type="text" placeholder="Enter keyword">
                            <span id="keyword_search_icon" class="fa fa-search" aria-hidden="true"></span>
                        </label>
                    </div><?php // #sidebar-search ?>
	        	</div>
	        </div>
        </div>

        <?php ob_start(); ?>
            <p>
                <?= __(
                    'This course is for $1. However $2 is $3. Are you sure you would like to book this item?',
                    array(
                        '$1' => '<span id="booking-year_mismatch-item_year"><i>Year</i></span>',
                        '$2' => '<span id="booking-year_mismatch-student_name"><i>Student</i></span>',
                        '$3' => '<span id="booking-year_mismatch-student_year"><i>Student Year</i></span>'
                    )
                ) ?>
            </p>
        <?php $modal_body = ob_get_clean(); ?>

        <?php ob_start(); ?>
            <button type="button" class="button button--book popup_close" id="booking-year_mismatch-continue"><?= __('Continue') ?></button>
            <button type="button" class="button button--cancel popup_close"><?= __('Cancel') ?></button>
        <?php $modal_footer = ob_get_clean(); ?>

        <?php
        echo View::factory('front_end/snippets/modal')
            ->set('id',         'booking-year_mismatch')
            ->set('width',      '680px')
            ->set('title',       __('Years don\'t match'))
            ->set('body_class', 'course-txt')
            ->set('body',       $modal_body)
            ->set('footer',     $modal_footer)
        ?>
    </div>



    <!-- hidden inputs with search criteria  -->
    <input type="hidden" name="location_id" id="home-search-location-id-on-available-results" />
    <input type="hidden" name="subject_id" id="home-search-subject-id-on-available-results" />
    <input type="hidden" name="category_id" id="home-search-category-id-on-available-results" />
    <input type="hidden" name="topic_id" id="home-search-topic-id-on-available-results" />
    <input type="hidden" name="course_id" id="home-search-course-id-on-available-results" />

    <div class="hidden" id="cart-alert-icon-template">
        <span class="alert-icon">
            <span class="alert-icon-amount"></span>
            <?= file_get_contents(APPPATH.'assets/shared/img/cart.svg') ?>
        </span>
    </div>
</form>

<?php include('template_views/booking_popup.php');?>

<script src="<?= URL::get_engine_assets_base() ?>/js/jquery-ui.js"></script>

<?php $just_registered = (isset($_GET['registered']) && $_GET['registered'] == 'success'); ?>

<?php if ( ! Auth::instance()->logged_in() ): ?>
    <div class="collapse<?= ($just_registered || isset($_GET['modal'])) ? ' in"' : '' ?>" id="login-overlay">
        <?php
        $guest_redirect = false; // Modal should just dismiss when you click "Continue as Guest"
        include 'template_views/login_overlay.php';
        ?>
    </div>
<?php endif; ?>

<div id="bookings-conflict-modal" class="sectionOverlay">
    <div class="overlayer"></div>

    <div class="screenTable">
        <div class="screenCell">
            <div class="sectioninner">
                <div class="popup-content page-content" id="bookings-conflict-message"></div>

                <div class="popup-footer">
                    <button type="button" class="button button--book popup_close" id="bookings-conflict-continue"><?= __('Add anyway') ?></button>
                    <button type="button" class="button button--cancel popup_close"><?= __('Go back') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
