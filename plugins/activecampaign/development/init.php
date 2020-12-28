<?php defined('SYSPATH') or die('No Direct Script Access.');

if (Model_Plugin::is_enabled_for_role('Administrator', 'activecampaign')) {
    Model_Remotesync::$tables['ActiveCampaign-Contact'] = Model_Contacts3::CONTACTS_TABLE;
    Model_Remotesync::$table_ids['ActiveCampaign-Contact'] = 'id';

    Model_Automations::add_action(new Model_Activecampaign_SaveContactAction());
    Model_Automations::add_action(new Model_Activecampaign_DeleteContactAction());
}
