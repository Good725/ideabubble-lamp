<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 16/04/15
 * Time: 09:15
 * Added for Codeception
 */

class Cookie extends Kohana_Cookie {

    public static function set($name, $value, $expiration = NULL) {
        if (Request::initial()->secure()) {
            parent::$secure = true;
        }
        if (Kohana::$environment == Kohana::TESTING) {
            Request::initial()->cookie($name, $value);
        }
        //parent::$httponly = true;
        return parent::set($name, $value, $expiration);
    }
}
