<?php defined('SYSPATH') or die('No Direct Script Access.');

if(Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
    Model_Remotesync::$tables[Model_CDSAPI::API_NAME . '-Account'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids[Model_CDSAPI::API_NAME . '-Account'] = 'id';
}

