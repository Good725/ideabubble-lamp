<?php defined('SYSPATH') or die('No Direct Script Access.');

if (Model_Plugin::is_enabled_for_role('Administrator', 'remoteaccounting')) {
    Model_Remotesync::$tables['Xero-Contact'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids['Xero-Contact'] = 'id';

    Model_Remotesync::$tables['Xero-Transaction'] = Model_Kes_Transaction::TRANSACTION_TABLE;
    Model_Remotesync::$table_ids['Xero-Transaction'] = 'id';

    Model_Remotesync::$tables['Xero-Payment'] = Model_Kes_Payment::PAYMENT_TABLE;
    Model_Remotesync::$table_ids['Xero-Payment'] = 'id';

    Model_Remotesync::$tables['Bigredcloud-Contact'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids['Bigredcloud-Contact'] = 'id';

    Model_Remotesync::$tables['Bigredcloud-Transaction'] = Model_Kes_Transaction::TRANSACTION_TABLE;
    Model_Remotesync::$table_ids['Bigredcloud-Transaction'] = 'id';

    Model_Remotesync::$tables['Bigredcloud-Payment'] = Model_Kes_Payment::PAYMENT_TABLE;
    Model_Remotesync::$table_ids['Bigredcloud-Payment'] = 'id';

    Model_Remotesync::$tables['AccountsIQ-Contact'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids['AccountsIQ-Contact'] = 'id';

    Model_Remotesync::$tables['AccountsIQ-Transaction'] = Model_Kes_Transaction::TRANSACTION_TABLE;
    Model_Remotesync::$table_ids['AccountsIQ-Transaction'] = 'id';

    Model_Remotesync::$tables['AccountsIQ-Payment'] = Model_Kes_Payment::PAYMENT_TABLE;
    Model_Remotesync::$table_ids['AccountsIQ-Payment'] = 'id';

    Model_Automations::add_action(new Model_Remoteaccounting_ContactSaveAction());
    Model_Automations::add_action(new Model_Remoteaccounting_ContactDeleteAction());

    Model_Automations::add_action(new Model_Remoteaccounting_TransactionSaveAction());
    Model_Automations::add_action(new Model_Remoteaccounting_TransactionDeleteAction());

    Model_Automations::add_action(new Model_Remoteaccounting_PaymentSaveAction());
    Model_Automations::add_action(new Model_Remoteaccounting_PaymentDeleteAction());
}
