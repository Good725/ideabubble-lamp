<?php defined('SYSPATH') or die('No direct access allowed.');

Kohana_Cookie::$domain = '.' . str_replace( 'www.', '', $_SERVER['HTTP_HOST'] );
Kohana_Cookie::$httponly = true;
return array(
    'native' => array(
        'name' => str_replace(array('www.', '.'), array('', '_'), $_SERVER['HTTP_HOST']) . '_session_name',
        'lifetime' => (60*60*16), // 16 Hours
    ),
);