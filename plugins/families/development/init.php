<?php defined('SYSPATH') or die('No Direct Script Access.');

if(
    Model_Plugin::is_enabled_for_role('Administrator', 'contacts2')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'families')
) {
    require_once __DIR__ . '/classes/contactfamilyextention.php';
    Model_Contacts::registerExtention(new ContactFamilyExtention());
}
