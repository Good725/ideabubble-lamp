<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Notifications extends Model
{
    // Tables
    const TABLE_EVENT = 'plugin_notifications_event';
    const TABLE_TO    = 'plugin_notifications_to';
    const TABLE_CC    = 'plugin_notifications_cc';
    const TABLE_BCC   = 'plugin_notifications_bcc';

    // Fields
    private $id;
    private $name;
    private $description;
    private $from;
    private $subject;
    private $to;
    private $cc;
    private $bcc;
    private $header;
    private $footer;
	private $deleted;

    //
    // PUBLIC FUNCTIONS
    //

    /**
     * @param int $id Event identifier.
     */
    public function __construct($id = NULL)
    {
        if (isset($id))
        {
            $this->load($id);
        }
        else
        {
            $this->id          = NULL;
            $this->name        = NULL;
            $this->description = NULL;
            $this->from        = NULL;
            $this->subject     = NULL;
            $this->to          = NULL;
            $this->cc          = NULL;
            $this->bcc         = NULL;
            $this->header      = NULL;
            $this->footer      = NULL;
			$this->deleted     = 0;
        }
    }

    //
    // SETTER METHODS
    //

    /**
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = trim($name);
    }

    /**
     * @param string $description
     */
    public function set_description($description)
    {
        $this->from = trim($description);
    }

    /**
     * @param string $from
     */
    public function set_from($from)
    {
        $this->from = trim($from);
    }

    /**
     * @param string $subject
     */
    public function set_subject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param int[] $to
     */
    public function set_to($to)
    {
        $this->to = array_unique($to);
    }

    /**
     * @param int[] $cc
     */
    public function set_cc($cc)
    {
        $this->cc = array_unique($cc);
    }

    /**
     * @param int[] $bcc
     */
    public function set_bcc($bcc)
    {
        $this->bcc = array_unique($bcc);
    }

    /**
     * @param string $header
     */
    public function set_header($header)
    {
        $this->header = $header;
    }

	/**
	 * @param string $footer
	 */
	public function set_footer($footer)
	{
		$this->footer = $footer;
	}

	/**
	 * @param string $footer
	 */
	public function set_deleted($deleted)
	{
		$this->deleted = $deleted;
	}

    //
    // GET METHODS
    //
    public function get_to()
    {
        return $this->to;
    }

	public function get_name()
	{
		return $this->name;
	}

	public function get_header()
	{
		return $this->header;
	}

	public function get_footer()
	{
		return $this->footer;
	}

	public function get_deleted()
	{
		return $this->deleted;
	}

    public function get_from()
    {
        return $this->from;
    }

    /**
     * @return bool TRUE if the details are saved. Otherwise, FALSE.
     */
    public function save()
    {
        $ok = FALSE;
        $db = Database::instance();

        if ( ! is_null($db) AND $db->begin() )
        {
            try
            {
                $insert_array = $this->build_insert_array();

                if ($this->id == NULL)
                {
                    // Add a new event
                    $ok = $this->add_event($insert_array);
                }
                else
                {
                    // Update an existing event
                    $ok = $this->update_event($insert_array);
                }

                // If no errors, commit the transaction. Otherwise, throw an exception.
                if (!$ok)
                    throw new Exception();
                else
                {
                    $ok = $db->commit();
                }
            }
            catch (Exception $e)
            {
                // Rollback the transaction
                $db->rollback();
            }
        }

        return $ok;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function send($body, $files = null, $custom_recipient = null)
    {
        // Get the recipients
        $to  = implode(',', Model_Contacts::get_contacts_email($this->to ));
        $cc  = implode(',', Model_Contacts::get_contacts_email($this->cc ));
        $bcc = implode(',', Model_Contacts::get_contacts_email($this->bcc));

        if($custom_recipient){
            $to = $custom_recipient;
        }

        // Compose the message
        $message = '<html><body>'.$this->header.$body.$this->footer.'</body></html>';

		if ($to.$cc.$bcc == '')
		{
			$sent = FALSE;
			IbHelpers::set_message('Internal error. No recipients have been configured for this email.', 'error popup_box');
		}
		else
		{
			$sent = IbHelpers::send_email($this->from, $to, $cc, $bcc, $this->subject, $message,$files);
			if ( ! $sent)
			{
				IbHelpers::set_message('Internal error sending email.', 'error popup_box');
			}
		}

		return $sent;
    }

    /**
     * @param $to
     * @param $body
     * @return bool
     */
    public function send_to($to, $body,$subject = NULL)
    {
        // Compose the message
        $subject = (isset($subject) AND $subject != '') ? "Order Confirmation ".$subject : $this->subject;
        $message = '<html><body>'.$body.'</body></html>';

        return IbHelpers::send_email($this->from, $to, NULL, NULL, $subject, $message);
    }

    public function send_to_custom($to, $body,$subject = NULL)
    {
        // Compose the message
        $subject = (isset($subject) AND $subject != '') ? $subject : $this->subject;
        $message = '<html><body>'.$body.'</body></html>';

        return IbHelpers::send_email($this->from, $to, NULL, NULL, $subject, $message);
    }

    public function update_tags(&$notification,$data)
    {
        $this->update_tag_keys($data);
        if(isset($notification->header))
        {
            $notification->header = str_replace(array_keys($data),$data,$notification->header);
        }
    }

    private function update_tag_keys(&$data)
    {
        $keys = array();
        foreach($data AS $key=>$info)
        {
            unset($data[$key]);
            $keys['{'.$key.'}'] = $info;
        }
        $data = $keys;
        unset($keys);
    }

    //
    // STATIC/SERVICE FUNCTIONS (DO NOT ABUSE OF THEM)
    //

    /**
     * @return array|bool
     */
    public static function get_event_all()
    {
        $events = array();

        $r = DB::select('id', 'name', 'description', 'from', 'subject', 'header', 'footer')
                 ->from(Model_Notifications::TABLE_EVENT)
				 ->where('deleted', '=' ,0)
                 ->order_by('id')
                 ->execute()
                 ->as_array();

        for ($i = 0; $i < count($r); $i++)
        {
            // Set event information
            $event = array
            (
                // Event information
                'id'          => $r[$i]['id'         ],
                'name'        => $r[$i]['name'       ],
                'description' => $r[$i]['description'],
                'from'        => $r[$i]['from'       ],
                'subject'     => $r[$i]['subject'    ],
                'header'      => $r[$i]['header'     ],
                'footer'      => $r[$i]['footer'     ],

                // To, Cc, Bcc
                'to'          => Model_Notifications::get_event_recipients(Model_Notifications::TABLE_TO , $r[$i]['id']),
                'cc'          => Model_Notifications::get_event_recipients(Model_Notifications::TABLE_CC , $r[$i]['id']),
                'bcc'         => Model_Notifications::get_event_recipients(Model_Notifications::TABLE_BCC, $r[$i]['id']),
            );

            // Add the event to the array
            array_push($events, $event);
        }

        return (count($events) > 0) ? $events : FALSE;
    }

    /**
     * @param string $table
     * @param int $id
     * @return array
     */
    public static function get_event_recipients($table, $id)
    {
        $recipients = array();

        // Get recipients
        $r = DB::select('id_contact')
                 ->from($table)
                 ->where('id_event', '=', $id)
                 ->execute()
                 ->as_array();

        // Load recipients
        for ($i = 0; $i < count($r); $i++)
        {
            array_push($recipients, $r[$i]['id_contact']);
        }

        return $recipients;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function get_event_id($name)
    {
        $r = DB::select('id')
                 ->from(Model_Notifications::TABLE_EVENT)
                 ->where('name', '=', $name)
                 ->execute()
                 ->as_array();

        return (count($r) == 1) ? $r[0]['id'] : FALSE;
    }

    //
    // PRIVATE FUNCTIONS
    //

    /**
     * @param int $id Event identifier.
     * @return bool TRUE is the function success. Otherwise, FALSE.
     */
    private function load($id)
    {
        // Load the event information
        $ok = $this->load_event_information($id);

        if ($ok)
        {
            $this->load_recipients($id);
        }

        return $ok;
    }

    /**
     * @param int $id
     * @return bool
     */
    private function load_event_information($id)
    {
        // Event information
        $r = DB::select('id', 'name', 'description', 'from', 'subject', 'header', 'footer')
                 ->from(Model_Notifications::TABLE_EVENT)
                 ->where('id', '=', $id)
				 ->where('deleted', '=', 0)
                 ->execute()
                 ->as_array();

        $ok = (count($r) == 1);

        // If information available, set class members
        if ($ok)
        {
            $this->id          = $r[0]['id'         ];
            $this->name        = $r[0]['name'       ];
            $this->description = $r[0]['description'];
            $this->from        = $r[0]['from'       ];
            $this->subject     = $r[0]['subject'    ];
            $this->header      = $r[0]['header'     ];
			$this->footer      = $r[0]['footer'     ];
			$this->deleted     = 0;
        }

        return $ok;
    }

    /**
     * @param int $id
     */
    private function load_recipients($id)
    {
        // Get 'to' recipients
        $this->to = array();

        $r = DB::select('id_contact')
                 ->from(Model_Notifications::TABLE_TO)
                 ->where('id_event', '=', $id)
                 ->execute()
                 ->as_array();

        // Load them
        for ($i = 0; $i < count($r); $i++)
        {
            array_push($this->to, $r[$i]['id_contact']);
        }

        // Get 'cc' recipients
        $this->cc = array();

        $r = DB::select('id_contact')
                 ->from(Model_Notifications::TABLE_CC)
                 ->where('id_event', '=', $id)
                 ->execute()
                 ->as_array();

        // Load them
        for ($i = 0; $i < count($r); $i++)
        {
            array_push($this->cc, $r[$i]['id_contact']);
        }

        // Get 'bcc' recipients
        $this->bcc = array();

        $r = DB::select('id_contact')
                 ->from(Model_Notifications::TABLE_BCC)
                 ->where('id_event', '=', $id)
                 ->execute()
                 ->as_array();

        // Load them
        for ($i = 0; $i < count($r); $i++)
        {
            array_push($this->bcc, $r[$i]['id_contact']);
        }
    }

    /**
     * @return bool
     */
    private function add_event($insert_array)
    {
        $ok = FALSE;

        // Add the new event information
        $id = $this->sql_add_event($insert_array);

        if ($id !== FALSE)
        {
            // Update the recipients
            $ok = $this->update_recipients($this->id);
        }

        return $ok;
    }

    /**
     * @return bool
     */
    private function update_event($insert_array)
    {
        $ok = TRUE;

        // Update event information
        $ok = ($ok AND $this->sql_update_event($this->id, $insert_array));

        // Update the recipients
        $ok = ($ok AND $this->update_recipients($this->id));

        return $ok;
    }

    /**
     * @param $id
     * @return bool
     */
    private function update_recipients($id)
    {
        $ok = TRUE;

        // Delete all the recipients for this event
        $this->sql_delete_recipients($id);

        // Updates table TABLE_TO
        if ( $ok AND (count($this->to) > 0) )
        {
            // Creates the arrays in the proper format: [(id_event, id_contact),(id_event, id_contact)...]
            $values = array_map(function($id_event, $id_contact) { return array($id_event, $id_contact); }, array_fill(0, count($this->to ), $id), $this->to );

            $ok = $this->sql_insert_recipients(Model_Notifications::TABLE_TO , $values);
        }

        // Updates table TABLE_CC
        if ( $ok AND (count($this->cc) > 0) )
        {
            // Creates the arrays in the proper format: [(id_event, id_contact),(id_event, id_contact)...]
            $values = array_map(function($id_event, $id_contact) { return array($id_event, $id_contact); }, array_fill(0, count($this->cc ), $id), $this->cc );

            $ok = $this->sql_insert_recipients(Model_Notifications::TABLE_CC , $values);
        }

        // Updates table TABLE_BCC
        if ( $ok AND (count($this->bcc) > 0) )
        {
            // Creates the arrays in the proper format: [(id_event, id_contact),(id_event, id_contact)...]
            $values = array_map(function($id_event, $id_contact) { return array($id_event, $id_contact); }, array_fill(0, count($this->bcc), $id), $this->bcc);

            $ok = $this->sql_insert_recipients(Model_Notifications::TABLE_BCC, $values);
        }

        return $ok;
    }

    /**
     * @return array
     */
    private function build_insert_array()
    {
        // Create the array with the values
        $data = array
        (
            'name'        => $this->name,
            'description' => $this->description,
            'from'        => $this->from,
            'subject'     => $this->subject,
            'header'      => $this->header,
            'footer'      => $this->footer,
			'deleted'     => $this->deleted
        );

        return $data;
    }

    //
    // SQL FUNCTIONS
    //

    /**
     * @param array $info
     * @return bool
     */
    private function sql_add_event($info)
    {
        $r = DB::insert(Model_Notifications::TABLE_EVENT, array_keys($info))
                 ->values(array_values($info))
                 ->execute();

        return ($r[1] == 1) ? $r[0] : FALSE;
    }

    /**
     * @param $id
     * @param $info
     * @return bool
     */
    private function sql_update_event($id, $info)
    {
        DB::update(Model_Notifications::TABLE_EVENT)
            ->set($info)
            ->where('id', '=', $id)
            ->execute();

        // Changed or not, assume update was successful (otherwise it will raise an exception)
        return TRUE;
    }

    /**
     * @param $id
     */
    private function sql_delete_recipients($id)
    {
        $recipients_tables = array(Model_Notifications::TABLE_TO, Model_Notifications::TABLE_CC, Model_Notifications::TABLE_BCC);

        foreach ($recipients_tables as $table)
        {
            DB::delete($table)
                ->where('id_event', '=', $id)
                ->execute();
        }
    }

    /**
     * @param string $table
     * @param array $values
     * @return bool
     */
    private function sql_insert_recipients($table, $values)
    {
        // Define the query
        $q = DB::insert($table, array('id_event', 'id_contact'));

        foreach ($values as $array)
        {
            $q->values($array);
        }

        // Execute the query
        $r = $q->execute();

        return (count($values) == $r[1]);
    }

    public static function add_notification_entry($post)
    {
        try {
            DB::insert(self::TABLE_EVENT,array('name','description','from','subject'))->values(array($post['name'],$post['description'],$post['from'],$post['subject']))->execute();
        }
        catch(Exception $e)
        {
            Log::instance()->add(Log::ERROR,"Failed to add notification.");
            throw new Exception("Unable to add notification. ".$e->getMessage());
        }
    }

    /**
     * @param string $message - pass by reference
     * @return void
     */
    public static function apply_tags(&$message)
    {
        /*** Ideally tags will be stored in the DB. For now, it will be array. ***/
        $tags = array(
            'TIME_NOW' => date('F j,  Y,  g:i a'),
            'SITE'      => $_SERVER['HTTP_HOST']
        );

        preg_match_all('/{(.*?)}/i', $message, $matches);

        if(is_array($matches) AND isset($matches[1]) AND !empty($matches[1]))
        {
            foreach($matches[1] AS $key=>$match)
            {
                if(array_key_exists($match,$tags))
                {
                    $message = str_replace($matches[0][$key],$tags[$match],$message);
                }
            }
        }
    }
}
