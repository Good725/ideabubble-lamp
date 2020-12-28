<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Notifications extends Controller_Cms
{
    public function before()
    {
        parent::before();

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array( 'name' => 'Notifications', 'link' => '/admin/notifications')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',          'link' => '/admin'),
            array('name' => 'Notifications', 'link' => '/admin/notifications')
        );
        $this->template->sidebar->tools = '<a href="/admin/notifications/new"><button type="button" class="btn">Create Form</button></a>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('notifications') . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('notifications') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('notifications') . 'js/notifications.js"></script>';

        $this->template->styles[URL::get_engine_plugin_assets_base('notifications').'css/validation.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('notifications').'css/notifications.css'] = 'screen';


    }

    /**
     *
     */
    public function action_index()
    {
        //get icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('notifications');

        // Select the view
        $this->template->body = View::factory('list_events',$results);

        // Get the data
        $this->template->body->events   = Model_Notifications::get_event_all  ();
        $this->template->body->contacts = Model_Contacts     ::get_contact_all();
    }

	/**
	 * Delete
	 */
	public function action_delete()
	{
		$id    = $this->request->param('id');
		$event = new Model_Notifications($id);
		$event->set_deleted(1);
		if ($event->save())
		{
			IBhelpers::set_message('Event #'.$id.': '.$event->get_name().' deleted.', 'success popup_box');
		}
		else
		{
			IBhelpers::set_message('Failed to delete event.', 'danger popup_box');
		}
		$this->request->redirect('/admin/notifications/');
	}

	/**
     * Save.
     */
    public function action_save()
    {
        $ok = FALSE;

        try
        {
            // Load the event or create a new one
            $event = new Model_Notifications($_POST['id']);

            // Set the info
            $event->set_from   ($_POST['from'   ]);
            $event->set_subject($_POST['subject']);
            $event->set_header ($_POST['header' ]);
            $event->set_footer ($_POST['footer' ]);

            // Set the recipients
            $event->set_to (isset($_POST['to' ]) ? $_POST['to' ] : array());
            $event->set_cc (isset($_POST['cc' ]) ? $_POST['cc' ] : array());
            $event->set_bcc(isset($_POST['bcc']) ? $_POST['bcc'] : array());

            // Save
            $ok = $event->save();
        }
        catch (Exception $e)
        {
            // Bad request
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/notifications/');
        }

        if ($ok)
        {
            // Operation completed
            IbHelpers::set_message('Event successfully updated.', 'success popup_box');

            $this->request->redirect('/admin/notifications/');
        }
        else
        {
            // Operation not completed
            IbHelpers::set_message('Unable to complete the requested operation.', 'info popup_box');

            $this->request->redirect('/admin/notifications');
        }
    }

    public function action_test()
    {
        $id = Model_Notifications::get_event_id('default');

        if ($id)
        {
            $event = new Model_Notifications($id);
            $ok    = $event->send('BODY');
        }
    }

    public function action_ajaxValidateFieldName()
    {
        $this->auto_render = FALSE;

        $name  = $this->request->query('fieldId');
        $value = $this->request->query('fieldValue');

        $return[0] = $name;
        $return[1] = Model_Notifications::get_event_id($value) === FALSE;

        $this->response->body(json_encode($return));
    }

    public function action_new()
    {
        $results['plugin'] = Model_Plugin::get_plugin_by_name('notifications');
        $this->template->body = View::factory('add_event',$results);
    }

    public function action_save_notification()
    {
        $post = $this->request->post();
        Model_Notifications::add_notification_entry($post);
        $this->request->redirect('/admin/notifications/');
    }
}
