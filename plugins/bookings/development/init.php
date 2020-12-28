<?php defined('SYSPATH') or die('No Direct Script Access.');

if(Model_Plugin::is_enabled_for_role('Administrator', 'bookings') && Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
    Controller_Page::addActionAlias('checkout',         'Controller_Frontend_Bookings', 'action_checkout');
    if((bool)Settings::instance()->get('host_application')) {
        Controller_Page::addActionAlias('host-application', 'Controller_Frontend_Bookings', 'action_host_application');
    }

    if (Auth::instance()->has_access('messaging') && Auth::instance()->has_access('courses')) {
        $GLOBALS['ibcms_right_panels'][] = array(
            'css' => array(URL::get_engine_plugin_assets_base('bookings') . 'admin/css/home_textalert.css'),
            'js' => array(URL::get_engine_plugin_assets_base('bookings') . 'admin/js/home_textalert.js'),
            'view' => array(Kohana::find_file('views', 'admin/dashboard_textalert'))
        );
    }

    if (Auth::instance()->has_access('applications')) {
        MenuArea::factory()->register_link('applications', 'Applications', 'applications', null, null, 'study-mode');
    }


    Model_Remotesync::$tables['booking_transactions'] = Model_Kes_Transaction::TRANSACTION_TABLE;
    Model_Remotesync::$table_ids['booking_transactions'] = 'id';

    Model_Automations::add_trigger(new Model_Bookings_Bookingdigesttrigger());
    Model_Automations::add_trigger(new Model_Bookings_Salesquotedigesttrigger());
    Model_Automations::add_trigger(new Model_Bookings_Adminbookingcreatetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Frontendbookingcreatetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Frontendquotecreatetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Adminquotecreatetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Waitlistformsubmittrigger());
    Model_Automations::add_trigger(new Model_Bookings_Tuapplicationsubmittedtrigger());
    Model_Automations::add_trigger(new Model_Bookings_Trainerupcomingdigesttrigger());
    Model_Automations::add_trigger(new Model_Bookings_Waitlistdigesttrigger());
    Model_Automations::add_trigger(new Model_Bookings_Cancelledbookingsdigesttrigger());
    Model_Automations::add_trigger(new Model_Bookings_Rollcalldigesttrigger());

    Model_Automations::add_trigger(new Model_Bookings_Paymentsavetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Paymentdeletetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Transactionsavetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Transactiondeletetrigger());
    Model_Automations::add_trigger(new Model_Bookings_Checkouttrigger());
    Model_Automations::add_trigger(new Model_Bookings_Wishlistaddtrigger());
    Model_Automations::add_trigger(new Model_Bookings_Wishlistremovetrigger());

    Model_Automations::add_action(new Model_Bookings_Tuapplicationcreateaction());
}

