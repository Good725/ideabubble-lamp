<?php
Controller_Page::addActionAlias('survey', 'Controller_Frontend_Surveys', 'action_render');
if(Model_Plugin::is_enabled_for_role('Administrator', 'surveys')) {
    Model_Automations::add_trigger(new Model_Survey_Surveynotcompletetrigger());
}