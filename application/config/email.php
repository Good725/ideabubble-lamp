<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
    /**
     * SwiftMailer driver, used with the email module.
     *
     * Valid drivers are: native, sendmail, smtp
     */
   'driver' => 'smtp',

    /**
     * To use secure connections with SMTP, set "port" to 465 instead of 25.
     * To enable TLS, set "encryption" to "tls".
     *
     * Note for SMTP, 'auth' key no longer exists as it did in 2.3.x helper
     * Simply specifying a username and password is enough for all normal auth methods
     * as they are autodeteccted in Swiftmailer 4
     *
     * PopB4Smtp is not supported in this module as I had no way to test it but
     * SwiftMailer 4 does have a PopBeforeSMTP plugin so it shouldn't be hard to implement
     *
     * Encryption can be one of 'ssl' or 'tls' (both require non-default PHP extensions
     *
     * Driver options:
     * @param null native: no options
     * @param string sendmail: executable path, with -bs or equivalent attached
     * @param array smtp: hostname, (username), (password), (port), (encryption)
     */
    'options' => array(
    /**
     * The following options are available for SwiftMailer:
     * (taken from  http://symfony.com/doc/current/reference/configuration/swiftmailer.html) Thank you.
     *
     *  'transport' : The exact transport method to use to deliver emails. Valid values are:
            smtp
            gmail
            mail
            sendmail
            null (same as setting disable_delivery to true)
     *  'auth_mode' : The authentication mode to use when using smtp as the transport. Valid values are plain, login, cram-md5, or null.
     *  'port' : The port when using smtp as the transport. This defaults to 465 if encryption is ssl and 25 otherwise.

                Note: Looks like starting in version 4.1.3 swiftmailer added starttls support. In version 4.1.2 and eariler, using port 465
     *          and specifying 'tls' as the encryption method worked fine. However 4.1.3 looks like it does not support using a tls wrapper
     *          and only allows starttls. In other words "tls" stopped meaning "tls wrapper" and started meaning "starttls". Thus, changing
     *          the port to 587 instead of 465 (as the SES documentation says should be used for starttls connections) solved the problem for me.
     */
    'transport' => 'smtp',
    'username' => Settings::instance()->get('smtp_user'), // take from settings
    'password' => Settings::instance()->get('smtp_password'), // take from settings
    'hostname' => Settings::instance()->get('smtp_server'), // take from settings
    'port' => Settings::instance()->get('smtp_port'), // take from settings
    'encryption' => Settings::instance()->get('smtp_encryption'),  // take from settings
    'auth_mode' => Settings::instance()->get('smtp_auth_mode')  // take from settings
    )
);