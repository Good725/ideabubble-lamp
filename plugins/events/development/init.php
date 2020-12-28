<?php
if (Model_Plugin::is_enabled_for_role('Basic', 'events') || Model_Plugin::is_enabled_for_role('External User', 'events') || Model_Plugin::is_enabled_for_role('Administrator', 'events')) {
    if (class_exists('Controller_Page') && Model_Plugin::is_enabled_for_role('External User', 'events')) {
        Controller_Page::addActionAlias('events.html', 'Controller_Frontend_Events', 'action_index');
        Controller_Page::addActionAlias('event', 'Controller_Frontend_Events', 'action_event');
        Controller_Page::addActionAlias('venue', 'Controller_Frontend_Events', 'action_venue');
        Controller_Page::addActionAlias('organiser', 'Controller_Frontend_Events', 'action_organiser');
        Controller_Page::addActionAlias('checkout.html', 'Controller_Frontend_Events', 'action_checkout');
        Controller_Page::addActionAlias('ticket', 'Controller_Frontend_Events', 'action_ticket');
        Controller_Page::addActionAlias('mytickets.html', 'Controller_Frontend_Events', 'action_mytickets');
        Controller_Page::addActionAlias('registration.html', 'Controller_Frontend_Events', 'action_registration');
        Controller_Page::addActionAlias('check_organizer_url', 'Controller_Frontend_Events', 'action_check_organizer_url');
        Controller_Page::addActionAlias('login.html', 'Controller_Frontend_Events', 'action_login');
        Controller_Page::addActionAlias('logout.html', 'Controller_Frontend_Events', 'action_logout');
        Controller_Page::addActionAlias('myevents.html', 'Controller_Frontend_Events', 'action_myevents');
        Controller_Page::addActionAlias('event_edit.html', 'Controller_Frontend_Events', 'action_event_edit');
        Controller_Page::addActionAlias('qrcode', 'Controller_Frontend_Events', 'action_qrcode');
    }

    if (class_exists('Controller_Admin_Profile')) {
        Controller_Admin_Profile::$extraSections[] = new Model_Eventextraprofilesection();
    }
}

