<?php defined('SYSPATH') or die('No Direct Script Access.');

if(Model_Plugin::is_enabled_for_role('Administrator', 'chat') && Auth::instance()->has_access('chat')) {
    $GLOBALS['ibcms_right_panels'][] = array(
        'css' => array(URL::get_engine_plugin_assets_base('chat') . 'css/chat.css'),
        'js' => array(URL::get_engine_plugin_assets_base('chat') . 'js/chat.js'),
        'view' => array(Kohana::find_file('views', 'admin/chat_panel'))
    );
    Model_Chat::update_online_list();
}
