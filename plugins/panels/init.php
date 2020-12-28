<?php
if(
    Model_Plugin::is_enabled_for_role('Administrator', 'panels')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'linkchecker')
) {
    Model_Linkchecker::addTable('plugin_panels', 'id', 'link_url', '/admin/panels/add_edit_item/#ID');
}
