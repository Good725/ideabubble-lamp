<?php
if(
    Model_Plugin::is_enabled_for_role('Administrator', 'menus')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'linkchecker')
) {
    Model_Linkchecker::addTable('plugin_menus', 'id', 'link_url', '/admin/menus');
}
