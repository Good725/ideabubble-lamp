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
    $cart = $cb->get_cart_data($cart['booking'], $cart['booking_id'], $cart['client_id'], $cart['discounts'], $cart['courses']);
    $course_amend_fee_percent = Settings::instance()->get('course_amend_fee_percent');
    $user            = Auth::instance()->get_user();
    if (@$user['id']) {
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $contact = current($contacts);
        $contact_id = @$contact['id'];
        $contact_details = new Model_Contacts3($contact_id);
        $is_student = $contact_details->has_role('student');
    } else {
        if (!isset($contacts)){
          $contacts = array();
        }
        if (@$guardian) {
            $family = new Model_Family($guardian->get_family_id());
            $family_members = Model_Contacts3::get_family_members($guardian->get_family_id());
            $students = array();
            foreach ($family_members as $family_member) {
                if (in_array('student', $family_member['has_roles']) || in_array('mature', $family_member['has_roles'])) {
                    $students[] = $family_member;
                }
            }

        }
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
    if (count($_GET) == 0 && @$page_data['layout'] !== 'course_list2') {
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
?>

<?php $locations_list = Model_Locations::get_locations_without_parent(); ?>
<?php $subjects_list = Model_Subjects::get_all_subjects(array('publish' => true)); ?>
<?php $categories_list = Model_Categories::get_all_categories(); ?>
<?php $topics_list = Model_Topics::get_all_topics(); ?>
<?php $courses_list = Model_Courses::get_all_published(); ?>
<?php $years_list = Model_Years::get_all_years(); ?>
<?php $levels_list =  Model_Levels::get_all_levels(); ?>
<?php //$types_list =  Model_Types::get_all_types(); ?>

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

if ( ! empty($_GET['year_id']))     { $args['year_ids'][0]         = $year     = $_GET['year_id'];     }
( ! empty($_GET['year_ids'])) ? $args['year_ids']= $_GET['year_ids'] : $args['year_ids'] = array();

( ! empty($_GET['keyword'])) ? $keyword = $_GET['keyword'] : $keyword=null;

if ( ! empty($_GET['level']))    { $args['level']           = $level    = $_GET['level'];    }
( ! empty($_GET['level_ids'])) ? $args['level_ids']= $_GET['level_ids'] : $args['level_ids'] = array();

if ( ! empty($_GET['type']))    { $args['type']           = $level    = $_GET['type'];    }
( ! empty($_GET['type_ids'])) ? $args['type_ids']= $_GET['type_ids'] : $args['type_ids'] = array();

    $sub_total   = 0;
    $discount    = 0;
    $zone_fee    = 0;
    $booking_fee = 0;
    $has_amendable = false;
    $amend_fee = 0;
    $payment_method = 'cc';

    if (!empty($cart)) {
        foreach ($cart as $cart_item) {
            if ($cart_item['prepay'] || $cart_item['type'] == 'subtotal') {
                $discount += $cart_item['discount'];
            }
            if (!empty($course_amend_fee_percent) && isset($cart_item['details']['amendable']) && $cart_item['details']['amendable'] && $cart_item['details']['payment_type'] == 1) {
                $amend_fee += round(($course_amend_fee_percent / 100) * $cart_item['total'], 2);
            }
        }
    }
?>