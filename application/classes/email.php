<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Email module
 *
 * Ported from Kohana 2.2.3 Core to Kohana 3.0 module
 *
 * Updated to use Swiftmailer 4.0.4
 *
 * @package Core
 * @author Kohana Team
 * @copyright (c) 2007-2008 Kohana Team
 * @license http://kohanaphp.com/license.html
 */
class Email {

// SwiftMailer instance
    protected static $mail;

    /**
     * Creates a SwiftMailer instance.
     *
     * @param string DSN connection string
     * @return object Swift object
     */
    public static function connect($config = NULL)
    {
        if ( ! class_exists('Swift_Mailer', FALSE))
        {
        // Load SwiftMailer
           require Kohana::find_file('vendor', 'Swift/swift_required');
            //ini_set('include_path', 'swift/swift_required.php');
        }
        // Load default configuration
        ($config === NULL) and $config = Kohana::$config->load('email');

        switch ($config['driver'])
        {
            case 'smtp':
                 // Set port
                $port = empty($config['options']['port']) ? 25 : (int) $config['options']['port'];

                    // Create SMTP Transport
                $transport = Swift_SmtpTransport::newInstance($config['options']['hostname'], $port);

                if ( ! empty($config['options']['encryption']))
                {
                    // Set encryption
                    $transport->setEncryption($config['options']['encryption']);
                }

                // Do authentication, if part of the DSN
                empty($config['options']['username']) or $transport->setUsername($config['options']['username']);
                empty($config['options']['password']) or $transport->setPassword($config['options']['password']);

                // Set the timeout to 5 seconds
                $transport->setTimeout(empty($config['options']['timeout']) ? 5 : (int) $config['options']['timeout']);
                break;
            case 'sendmail':
                // Create a sendmail connection
                $transport = Swift_SendmailTransport::newInstance(empty($config['options']) ? "/usr/sbin/sendmail -bs" : $config['options']);

                break;
            default:
                // Use the native connection
                $transport = Swift_MailTransport::newInstance($config['options']);
                break;
        }

        // Create the SwiftMailer instance
        return Email::$mail = Swift_Mailer::newInstance($transport);
    }

    /**
     * Send an email message.
     *
     * @param string|array recipient email (and name), or an array of To, Cc, Bcc names
     * @param string|array sender email (and name)
     * @param string message subject
     * @param string message body
     * @param boolean send email as HTML
     * @return integer number of emails sent
     */
    public static function send($from, $to, $cc, $bcc, $subject, $message_body, $html = FALSE, $files)
    {

        // Connect to SwiftMailer
        (Email::$mail === NULL) and email::connect();

        // Determine the message type
        $html = ($html === TRUE) ? 'text/html' : 'text/plain';

        // Create the message
        $message = Swift_Message::newInstance($subject, $message_body, $html, 'utf-8');

        //turn strings into arrays if not array already :)

        if(!(is_array($to)))
        {
            $to = explode(",",trim($to));
        }

        if(!(is_array($cc)))
        {
            $cc = explode(",",trim($cc));
        }

        if(!(is_array($bcc)))
        {
            $bcc = explode(",",trim($bcc));
        }

        // setup the recipients associative array for use
        $recipients = array(
            'to' =>  $to,
            'cc' =>  $cc,
            'bcc' =>  $bcc
        );

        if (is_string($recipients))
        {
            // Single recipient
            $message->setTo($recipients);
        }
        elseif (is_array($recipients))
        {

            foreach ($recipients as $method => $set)
            {
                if ( ! in_array($method, array('to', 'cc', 'bcc'), true))
                {
                    // Use To: by default
                    $method = 'to';
                }

                // Create method name
                $method = 'add'.ucfirst($method);

                if (is_array($set))
                {
                    // Add a recipient with name
                    //loop through each set object with same method
                    foreach ($set as $set_item)
                    {
                        if (!empty($set_item)) //don't add if empty
                        {
                            $message->$method($set_item);
                        }
                    }
                }
                else
                {
                    // Add a recipient without name
                    $message->$method($set);
                }
            }
        }
        // setup sender
        if (is_string($from))
        {
            // From without a name
            $message->setFrom($from);
        }
        elseif (is_array($from))
        {
            // From with a name
            $message->setFrom($from[0], $from[1]);
        }

        //attach files before sending if any
        foreach ($files as $file)
        {
            if (is_file($file))
            {
                //attach this file to the email
                $message->attach(Swift_Attachment::fromPath($file));
            }
        }

        // for tracking add an activity record
        $activity = new Model_Activity();
        $activity
            ->set_item_type('user')
            ->set_action('email')
            ->set_item_id($subject)
            ->set_user_id(Request::$client_ip)
            ->save();

        // send the email over swift sender
        return Email::$mail->send($message);
    }

} // End email