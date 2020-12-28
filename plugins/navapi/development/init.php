<?php defined('SYSPATH') or die('No Direct Script Access.');

if(Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
    Model_Remotesync::$tables[Model_NAVAPI::API_NAME . '-Booking'] = Model_KES_Bookings::BOOKING_TABLE;
    Model_Remotesync::$table_ids[Model_NAVAPI::API_NAME . '-Booking'] = 'booking_id';

    Model_Remotesync::$tables[Model_NAVAPI::API_NAME . '-Transaction'] = Model_Kes_Transaction::TRANSACTION_TABLE;
    Model_Remotesync::$table_ids[Model_NAVAPI::API_NAME . '-Transaction'] = 'id';

    Model_Remotesync::$tables[Model_NAVAPI::API_NAME . '-Payment'] = Model_Kes_Payment::PAYMENT_TABLE;
    Model_Remotesync::$table_ids[Model_NAVAPI::API_NAME . '-Payment'] = 'id';

    Model_Remotesync::$tables[Model_NAVAPI::API_NAME . '-Account'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids[Model_NAVAPI::API_NAME . '-Account'] = 'id';

    Model_Automations::add_action(new Model_Navapi_Bookingcreateaction());
    Model_Automations::add_action(new Model_Navapi_Transactioncreateaction());
    Model_Automations::add_action(new Model_Navapi_Paymentcreateaction());

    Model_Automations::add_trigger(new Model_Navapi_Datechangetrigger());
}

