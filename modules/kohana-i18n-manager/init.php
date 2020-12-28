<?php defined('SYSPATH') or die('No direct script access.');

Route::set('i18n', 'i18n(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'i18n',
		'action' => 'index'
	));
