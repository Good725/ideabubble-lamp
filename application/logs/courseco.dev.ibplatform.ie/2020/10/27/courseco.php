<?php defined('SYSPATH') or die('No direct script access.'); ?>

2020-10-27 15:33:55 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging/check_notifications?time=1603812832 Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 15:33:55 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging/check_notifications?time=1603812832 Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 15:46:32 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging/notification_templates Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 15:46:32 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging/notification_templates Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 15:47:04 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging/check_notifications?time=1603813622 Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 15:47:04 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging/check_notifications?time=1603813622 Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 15:48:28 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 15:48:28 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 15:49:42 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging/check_notifications?time=1603813779 Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 15:49:42 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging/check_notifications?time=1603813779 Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 15:58:56 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 15:58:56 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 16:03:10 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 16:03:10 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 16:03:16 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 16:03:16 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}
2020-10-27 16:05:35 --- ERROR: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
2020-10-27 16:05:35 --- STRACE: http://courseco.dev.ibplatform.ie//admin/messaging Error [ 0 ]: Call to undefined function curl_init() ~ /var/www/wms/engine/plugins/messaging/development/classes/model/drivers/email/sparkpost.php [ 16 ]
--
#0 /var/www/wms/engine/plugins/messaging/development/classes/model/messaging.php(147): Model_Messaging_Driver_Email_Sparkpost->__construct()
#1 /var/www/wms/engine/plugins/messaging/development/classes/controller/admin/messaging.php(40): Model_Messaging->get_drivers()
#2 [internal function]: Controller_Admin_Messaging->before()
#3 /var/www/wms/engine/system/3-2/classes/kohana/request/client/internal.php(103): ReflectionMethod->invoke(Object(Controller_Admin_Messaging))
#4 /var/www/wms/engine/system/3-2/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /var/www/wms/engine/system/3-2/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/wms/engine/www/index.php(116): Kohana_Request->execute()
#7 {main}