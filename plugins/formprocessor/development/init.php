<?php defined('SYSPATH') or die('No Direct Script Access.');

if (Model_Plugin::is_enabled_for_role('Administrator', 'formprocessor')) {
    Model_Automations::add_trigger(new Model_Formprocessor_Brochuredownloadtrigger());
    Model_Automations::add_trigger(new Model_Formprocessor_Newslettersubscribetrigger());
    Model_Automations::add_trigger(new Model_Formprocessor_Contactustrigger());
}
