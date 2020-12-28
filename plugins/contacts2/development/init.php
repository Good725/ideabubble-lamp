<?php defined('SYSPATH') or die('No Direct Script Access.');

Model_Users::$on_delete_data_handlers[] = 'Model_Contacts::delete_user_data';
