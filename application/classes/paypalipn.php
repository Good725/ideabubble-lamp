<?php
class PaypalIPN
{
    public static function is_valid($post = null)
    {
        $test = (Settings::instance()->get('paypal_test_mode') == 1);

        if ($post == null) {
            $post = $_POST;
        }

        $post['cmd'] = '_notify-validate';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        if ($test) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        curl_setopt($ch, CURLOPT_URL, 'https://www.' . ($test ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr');
        $response = curl_exec($ch);
        //$inf = curl_getinfo($ch);
        curl_close($ch);

        return $response == 'VERIFIED';
    }
}