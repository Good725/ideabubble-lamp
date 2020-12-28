<?php
$extra_plugin_id = DB::select('id')->from('engine_plugins')->where('name', '=', 'extra')->execute()->get('id');
if(!DB::select('id')->from('engine_cron_tasks')->where('plugin_id', '=', $extra_plugin_id)->execute()->get('id')){
	$messaging_extra_cron_data = array('plugin_id' => $extra_plugin_id, 'publish' => 1, 'delete' => 0);
	$messaging_extra_cron_data['frequency'] = '{"minute":["0"],"hour":["0"],"day_of_month":["*"],"month":["*"],"day_of_week":["*"]}';
	$crontask = Model_Cron::create()->set($messaging_extra_cron_data);
	$crontask->save();
}

Route::set('extra_plugin', 'plugins/extra/<filepath>.<ext>', [
    'filepath' => '[a-zA-Z0-9\-\_\/]+', // alphanumeric, hyphens, underscores and forward slashes
])
    ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'extra',
        'action'     => 'asset'
    ));

?>