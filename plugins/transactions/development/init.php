<?php defined('SYSPATH') or die('No Direct Script Access.');

if(
    Model_Plugin::is_enabled_for_role('Administrator', 'contacts2')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'transactions')
) {
    require_once __DIR__ . '/classes/contacttransactionsextention.php';
    Model_Contacts::registerExtention(new ContactTransactionsExtention());
}

if(
    Model_Plugin::is_enabled_for_role('Administrator', 'families')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'transactions')
) {
    require_once __DIR__ . '/classes/familytransactionsextention.php';
    Model_Families::registerExtention(new FamilyTransactionsExtention());
}
