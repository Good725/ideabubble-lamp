<?php
if (Model_Plugin::is_enabled_for_role('Administrator', 'donations')) {

    require_once __DIR__ . '/classes/DonationsInSMSPostProcessor.php';
    Model_Messaging::register_post_processor(new DonationsInSMSPostProcessor());
}

