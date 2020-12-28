<?php
$locked = DB::query(Database::SELECT, "SELECT GET_LOCK('messaging_cron_install',60) AS locked")
    ->execute()
    ->get('locked');
if ($locked == 1) {
    $messaging_plugin_id = DB::select('id')->from('engine_plugins')->where('name', '=', 'messaging')->execute()->get('id');
    if (!DB::select('id')
        ->from('engine_cron_tasks')
        ->where('plugin_id', '=', $messaging_plugin_id)
        ->execute()
        ->get('id')
    ) {
        $messaging_plugin_cron_data = array(
                'title' => 'Messaging',
                'plugin_id' => $messaging_plugin_id,
                'publish' => 0,
                'delete' => 0
        );
        $messaging_plugin_cron_data['frequency'] = '{"minute":["*"],"hour":["*"],"day_of_month":["*"],"month":["*"],"day_of_week":["*"]}';
        $crontask = Model_Cron::create()->set($messaging_plugin_cron_data);
        $crontask->save();
    }
}
DB::query(Database::SELECT, "SELECT RELEASE_LOCK('messaging_cron_install') AS unlocked")->execute()->get('unlocked');


if(
    Model_Plugin::is_enabled_for_role('Administrator', 'contacts2')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'messaging')
) {
    $auth = Auth::instance();
    $allowed =
        $auth->has_access('messaging_access_own_mail')
        ||
        $auth->has_access('messaging_access_system_mail')
        ||
        $auth->has_access('messaging_global_see_all')
        ||
        $auth->has_access('messaging_view_system_email')
        ||
        $auth->has_access('messaging_view_system_sms')
        ||
        $auth->has_access('messaging_access_others_mail');
    if ($allowed) {
        require_once __DIR__ . '/classes/contactmessagingextention.php';
        Model_Contacts::registerExtention(new ContactMessagingExtention());
    }
}

if (Model_Plugin::is_enabled_for_role('Administrator', 'messaging')) {
    Model_Automations::add_trigger(new Model_Messaging_Systemmessagetrigger());
}
?>