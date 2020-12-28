<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Frontend_Messaging extends Controller
{
    protected $autoLogCron = false;

    protected $mm = null;
    
    function before()
    {
        parent::before();
        $this->mm = new Model_Messaging();
        $this->mm->get_drivers();
    }
    
    public function action_status_callback()
    {
        //file_put_contents("sc" . time() . ".txt", var_export(array(&$_GET, &$_POST), 1) );
    
        try {
            $this->mm->handle_frontend_status_callback();
        } catch (Exception $e) {
            $logMessage = "Error receiving messaging callback\n".$e->getMessage()."\n".$e->getTraceAsString()
                . "\n GET: " . var_export($_GET, true) . "\n POST:" . var_export($_POST, true);
            Log::instance()->add(Log::ERROR, $logMessage)->write();
            header("content-type: text/plain");
            echo "NOK";
        }
        exit();
    }
    
    public function action_receive_callback()
    {
        file_put_contents("rc" . time() . ".txt", var_export(array(&$_GET, &$_POST), 1));
        try {
            $this->mm->handle_frontend_receive_callback();
        } catch (Exception $e) {
            $logMessage = "Error receiving messaging callback\n".$e->getMessage()."\n".$e->getTraceAsString()
                . "\n GET: " . var_export($_GET, true) . "\n POST:" . var_export($_POST, true);
            Log::instance()->add(Log::ERROR, $logMessage)->write();
            header("content-type: text/plain");
            echo "NOK";
        
        }
        exit();
    }
    
    public function action_cron()
    {
        $this->auto_render = false;
        header('Content-Type: text/plain; charset=utf-8');
        $lock_filename = Kohana::$cache_dir . '/' . preg_replace('/[^a-z0-9_]/', '-', strtolower($_SERVER['HTTP_HOST'])) . "-plugin.messaging.lock";
        $lock = fopen($lock_filename, "c+");
        if ($lock) {
            if (flock($lock, LOCK_EX|LOCK_NB)) {
                set_time_limit(0);
                $started = time();
                ob_start();
                $scheduledReports = 0;
                $scheduledTemplates = 0;
                $processedMessages = 0;
                $receivedMessages = 0;
                try {
                    $scheduledReports = $this->mm->schedule_report_notifications();
                    $scheduledTemplates = $this->mm->schedule_template_notifications();
                    $processedMessages = count($this->mm->process_scheduled_messages());
                    $receivedMessages = $this->mm->receive_cron();
                    $finished = time();
                } catch (Exception $exc) {
                    header('content-type: text/plain; charset=utf-8');
                    print_r($exc);
                    $finished = null;
                }
                $output = ob_get_clean();


                if ($scheduledReports + $scheduledTemplates + $processedMessages + $receivedMessages) {
                    $output .= $scheduledReports . " reports processed\n" .
                        $scheduledTemplates . " templates processed\n" .
                        $processedMessages . " messages have been processed\n" .
                        $receivedMessages . " messages have been received\n";
                }
                Model_Cron::insert_log('messaging', array(
                    'started' => date('Y-m-d H:i:s', $started),
                    'finished' => $finished ? date('Y-m-d H:i:s', $finished) : null,
                    'output' => $output
                ));
                echo $output;
                unlink($lock_filename);
                flock($lock, LOCK_UN);
                fclose($lock);
            } else {
                fclose($lock);
            }
        } else {
        }
        echo "Messaging Cron Completed";
    }

    public function action_check_notifications()
    {
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            //$this->request->redirect('/admin');
        }
        header('Content-Type: application/json; charset=utf-8');

        $user = Auth::instance()->get_user();
        $return['html'] = '';

        if ($user)
        {
            $date_to    = null;
            $recipients = null;

            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $contact3 = Model_Contacts3::get_contact_ids_by_user($user['id']);
                if ( ! empty($contacts3[0])) {
                    $date_to = time();
                    $recipients = array(array('target_type' => 'CMS_CONTACT3', 'target' => $contact3[0]['id']));
                }
            }
            $notifications = $this->mm->get_messages_for_notification_tray(true, null, 0, $date_to, $recipients);
            $return['amount'] = count($notifications);

            if (count($notifications) > 0)
            {
                $return['html'] = (string) View::factory('messaging_popout_menu_front')->set('notifications', $notifications);
            }
        }

        echo json_encode($return);
        exit();
    }

    // Update the time the user last opened the notifications tray.
    // Used for determining if there are notifications since the user last opened it.
    public function action_update_notifications_checked_time()
    {
        $user = Auth::instance()->get_user_orm();
        $user->set('notifications_last_checked', date('Y-m-d H:i:s'));
        $user->update();
    }

    public function action_ajax_view_message()
    {
        $id      = $this->request->param('id');
        $model   = new Model_Messaging;
        $message = $model->get_message_details($id);
        $return  = (string) View::factory('messaging_ajax_view')->set('message', $message)->set('nolink', true);
        echo $return;
        exit();
    }
}

?>