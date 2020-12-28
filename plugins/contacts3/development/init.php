<?php defined('SYSPATH') or die('No Direct Script Access.');

if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
    Model_Users::$on_delete_data_handlers[] = 'Model_Contacts3::delete_user_data';
    Controller_Admin_Login::$run_after_external_register[] = 'Model_Contacts3::create_contact_for_external_register';
    Controller_Admin_Login::$run_before_external_register[] = 'Model_Contacts3::check_existing_contact_before_external_register';
    Auth::$run_after_login[] = 'Controller_Frontend_Contacts3::check_profile_completion';
    Controller_Admin_Users::$run_after_edit[] = 'Model_Contacts3::sync_user_to_contact';
    Controller_Page::addActionAlias('sign_in', 'Controller_Admin_Login', 'action_index');
    Controller_Page::addActionAlias('sign_out', 'Controller_Admin_Login', 'action_logout');
    Controller_Page::addActionAlias('sign_up', 'Controller_Admin_Login', 'action_register');
    Controller_Page::addActionAlias('dashboard', 'Controller_Admin_Contacts3', 'action_dashboard');
    Controller_Page::addActionAlias('profile', 'Controller_Admin_Contacts3', 'action_profile');
    Controller_Page::addActionAlias('timetables', 'Controller_Admin_Contacts3', 'action_timetables');
    Controller_Page::addActionAlias('timetables2', 'Controller_Admin_Contacts3', 'action_timetables2');
    Controller_Page::addActionAlias('accounts', 'Controller_Admin_Contacts3', 'action_accounts');
    Controller_Page::addActionAlias('wishlist', 'Controller_Admin_Contacts3', 'action_wishlist');
    Controller_Page::addActionAlias('bookings', 'Controller_Admin_Contacts3', 'action_bookings');

    if (Auth::instance()->has_access('messaging')) {
        $GLOBALS['ibcms_right_panels'][] = array(
            'css' => array(URL::get_engine_plugin_assets_base('contacts3') . 'css/dashboard_add_note.css'),
            'js' => array(URL::get_engine_plugin_assets_base('contacts3') . 'js/dashboard_add_note.js'),
            'view' => array(Kohana::find_file('views', 'admin/dashboard_add_note'))
        );
    }


    Model_Remotesync::$tables['contacts3'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids['contacts3'] = 'id';

    Model_Automations::add_trigger(new Model_Contacts3_Contactsavetrigger());
    Model_Automations::add_trigger(new Model_Contacts3_Contactdeletetrigger());
    Model_Automations::add_trigger(new Model_Contacts3_Contactregistertrigger());
}
