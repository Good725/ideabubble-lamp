/*
ts:2016-12-12 11:51:00
*/

UPDATE `plugin_reports_reports` SET `php_modifier`='$scheduleIds = $this->get_parameter(\'Schedule\');\nif (array_search(\'\', $scheduleIds) !== false){\n    unset($scheduleIds[array_search(\'\', $scheduleIds)]);\n}\n\n$sql = $this->_sql;\nif ($scheduleIds)$sql = str_replace(\'__PHP1__\', \'AND s.id IN (\' . implode(\',\', $scheduleIds) . \')\', $sql);\nelse $sql = str_replace(\'__PHP1__\', \'\', $sql);\n$this->_sql = $sql;\n\n' WHERE (`name`='Payment Due');

