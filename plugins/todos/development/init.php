<?php
spl_autoload_register(function($className) {
    $namespace = 'Ideabubble\Todos\\';
    if (substr($className, 0, strlen($namespace)) == $namespace) {
        $localName = substr($className, strlen($namespace));
        $filename = dirname(__DIR__) . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, $localName) . '.php';
        include $filename;
    }
});


$logged_in_user = Auth::instance()->get_user();

if(
		Model_Plugin::is_enabled_for_role('Administrator', 'contacts2')
		&&
		Model_Plugin::is_enabled_for_role('Administrator', 'todos')
) {
	require_once __DIR__ . '/classes/contacttodosextention.php';
	Model_Contacts::registerExtention(new ContactTodosExtention());
}

if(Model_Plugin::is_enabled_for_role('Administrator', 'todos')) {
    Model_Automations::add_trigger(new Model_Todos_TaskAssignedTrigger());
    Model_Automations::add_trigger(new Model_Todos_AssignmentAssignedTrigger());
    Model_Automations::add_trigger(new Model_Todos_AssesmentAssignedTrigger());
    Model_Automations::add_trigger(new Model_Todos_TaskDueTrigger());
    Model_Automations::add_trigger(new Model_Todos_AssignmentDueTrigger());
    Model_Automations::add_trigger(new Model_Todos_AssesmentDueTrigger());

    Model_Automations::add_trigger(new Model_Todos_TaskSaveTrigger());
    Model_Automations::add_trigger(new Model_Todos_AssignmentSaveTrigger());
    Model_Automations::add_trigger(new Model_Todos_ExamSaveTrigger());

    Model_Automations::add_action(new Model_Todos_AssigneeAlertAction());
}
