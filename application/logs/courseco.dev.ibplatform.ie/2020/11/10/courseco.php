<?php defined('SYSPATH') or die('No direct script access.'); ?>

2020-11-10 17:39:35 --- INFO: ================================= [DALM][BEGIN] =================================
2020-11-10 17:39:35 --- ERROR: [DALM][PRIMARY CONNECTION][courseco.dev.ibplatform.ie][2020 * * * * 7:39:35]: Model_DALM_Exception: Unable to get processed models. ~ /var/www/wms/engine/application/classes/model/dalm.php [ 877 ]
2020-11-10 17:39:35 --- INFO: [DALM][PRIMARY CONNECTION][courseco.dev.ibplatform.ie][2020 * * * * 7:39:35]: Error (1053): Server shutdown in progress
2020-11-10 17:39:35 --- INFO: ================================= [DALM][ END ] =================================
2020-11-10 17:39:35 --- ERROR: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference Database_Exception [ 1053 ]: Server shutdown in progress [ SELECT `variable`, `value_dev` AS `value` FROM `engine_settings` WHERE `config_overwrite` = 1 ] ~ MODPATH/database/classes/kohana/database/mysqli.php [ 180 ]
2020-11-10 17:39:35 --- STRACE: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference Database_Exception [ 1053 ]: Server shutdown in progress [ SELECT `variable`, `value_dev` AS `value` FROM `engine_settings` WHERE `config_overwrite` = 1 ] ~ MODPATH/database/classes/kohana/database/mysqli.php [ 180 ]
--
#0 /var/www/wms/engine/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQLI->query(1, 'SELECT `variabl...', false, Array)
#1 /var/www/wms/engine/application/classes/settings.php(186): Kohana_Database_Query->execute()
#2 /var/www/wms/engine/application/classes/kohana.php(73): Settings::get_config_overwrite_settings()
#3 /var/www/wms/engine/application/bootstrap.php(206): Kohana::initialize_project()
#4 /var/www/wms/engine/www/index.php(109): require('/var/www/wms/en...')
#5 {main}
2020-11-10 17:39:37 --- ERROR: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference ErrorException [ 2 ]: mysqli::__construct(): (HY000/2002): No such file or directory ~ APPPATH/classes/model/dalm.php [ 198 ]
2020-11-10 17:39:37 --- STRACE: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference ErrorException [ 2 ]: mysqli::__construct(): (HY000/2002): No such file or directory ~ APPPATH/classes/model/dalm.php [ 198 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'mysqli::__const...', '/var/www/wms/en...', 198, Array)
#1 /var/www/wms/engine/application/classes/model/dalm.php(198): mysqli->__construct('localhost', 'ib_test', 'Jimmy19970725!')
#2 /var/www/wms/engine/application/classes/model/dalm.php(139): Model_DALM::_establish_primary_connection()
#3 /var/www/wms/engine/application/classes/kohana.php(68): Model_DALM::update_db()
#4 /var/www/wms/engine/application/bootstrap.php(206): Kohana::initialize_project()
#5 /var/www/wms/engine/www/index.php(109): require('/var/www/wms/en...')
#6 {main}
2020-11-10 17:39:38 --- ERROR: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference ErrorException [ 2 ]: mysqli::__construct(): (HY000/2002): No such file or directory ~ APPPATH/classes/model/dalm.php [ 198 ]
2020-11-10 17:39:38 --- STRACE: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference ErrorException [ 2 ]: mysqli::__construct(): (HY000/2002): No such file or directory ~ APPPATH/classes/model/dalm.php [ 198 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'mysqli::__const...', '/var/www/wms/en...', 198, Array)
#1 /var/www/wms/engine/application/classes/model/dalm.php(198): mysqli->__construct('localhost', 'ib_test', 'Jimmy19970725!')
#2 /var/www/wms/engine/application/classes/model/dalm.php(139): Model_DALM::_establish_primary_connection()
#3 /var/www/wms/engine/application/classes/kohana.php(68): Model_DALM::update_db()
#4 /var/www/wms/engine/application/bootstrap.php(206): Kohana::initialize_project()
#5 /var/www/wms/engine/www/index.php(109): require('/var/www/wms/en...')
#6 {main}
2020-11-10 17:40:01 --- INFO: ================================= [DALM][BEGIN] =================================
2020-11-10 17:40:01 --- ERROR: [DALM][PRIMARY CONNECTION][courseco.dev.ibplatform.ie][2020 * * * * 7:40:01]: Model_DALM_Exception: Unable to get processed models. ~ /var/www/wms/engine/application/classes/model/dalm.php [ 877 ]
2020-11-10 17:40:01 --- INFO: [DALM][PRIMARY CONNECTION][courseco.dev.ibplatform.ie][2020 * * * * 7:40:01]: Error (1053): Server shutdown in progress
2020-11-10 17:40:01 --- INFO: ================================= [DALM][ END ] =================================
2020-11-10 17:40:01 --- ERROR: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference Database_Exception [ 1053 ]: Server shutdown in progress [ SELECT `variable`, `value_dev` AS `value` FROM `engine_settings` WHERE `config_overwrite` = 1 ] ~ MODPATH/database/classes/kohana/database/mysqli.php [ 180 ]
2020-11-10 17:40:01 --- STRACE: http://courseco.dev.ibplatform.ie//admin/profile/save_user_column_preference Database_Exception [ 1053 ]: Server shutdown in progress [ SELECT `variable`, `value_dev` AS `value` FROM `engine_settings` WHERE `config_overwrite` = 1 ] ~ MODPATH/database/classes/kohana/database/mysqli.php [ 180 ]
--
#0 /var/www/wms/engine/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQLI->query(1, 'SELECT `variabl...', false, Array)
#1 /var/www/wms/engine/application/classes/settings.php(186): Kohana_Database_Query->execute()
#2 /var/www/wms/engine/application/classes/kohana.php(73): Settings::get_config_overwrite_settings()
#3 /var/www/wms/engine/application/bootstrap.php(206): Kohana::initialize_project()
#4 /var/www/wms/engine/www/index.php(109): require('/var/www/wms/en...')
#5 {main}