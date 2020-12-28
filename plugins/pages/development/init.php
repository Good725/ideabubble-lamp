<?php
if (Model_Plugin::is_enabled_for_role('Administrator', 'pages') && Model_Plugin::is_enabled_for_role('Administrator', 'linkchecker')) {
    Model_Linkchecker::addTable('plugin_pages_pages', 'id', 'content', '/admin/pages/edit_pag/#ID');
}

if (Settings::instance()->get('site_searchbar')) {
    Controller_Page::addActionAlias('search_results', 'Controller_Frontend_Pages', 'action_search');
}
