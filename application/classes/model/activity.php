<?php
class Model_Activity extends Model implements Interface_Ideabubble
{
	/*** CLASS CONSTANTS ***/
	const MAIN_TABLE       = 'engine_activities';
	const ACTION_TABLE     = 'engine_activities_actions';
	const ITEM_TYPES_TABLE = 'engine_activities_item_types';

	/*** PRIVATE MEMBER DATA ***/
	private $id                  = NULL;
	private $user_id             = NULL;
	private $action_id           = NULL;
	private $item_type_id        = NULL;
	private $item_subtype_id     = NULL;
	private $item_id             = NULL;
	private $level2_item_type_id = NULL;
	private $level2_item_id      = NULL;
	private $level3_item_type_id = NULL;
	private $level3_item_id      = NULL;
	private $scope_id            = NULL;
	private $file_id             = NULL;
	private $status_id           = NULL;
	private $timestamp           = NULL;
	private $deleted             = 0;

	/*** PUBLIC FUNCTIONS ***/
	function __construct($id = null)
	{
		if (is_numeric($id))
		{
			$this->set_id($id);
			$this->get(true);
		}
		else
		{
			$user            = Auth::instance()->get_user();
			$this->user_id   = $user['id'];
			$this->timestamp = date('Y-m-d H:i:s');
		}
	}

	public function set($data)
	{
		foreach($data as $key=>$item)
		{
			if (property_exists($this,$key))
			{
				$this->{$key} = $item;
			}
		}

		return $this;
	}

	public function get($autoload = FALSE)
	{
		$data = $this->get_details();
		if ($autoload)
		{
			$this->set($data);
		}
		return $data;
	}

	public function get_instance()
	{
		return array(
			'id'                  => $this->id,
			'user_id'             => $this->user_id,
			'action_id'           => $this->action_id,
			'item_type_id'        => $this->item_type_id,
			'item_subtype_id'     => $this->item_subtype_id,
			'item_id'             => $this->item_id,
			'level2_item_type_id' => $this->level2_item_type_id,
			'level2_item_id'      => $this->level2_item_id,
			'level3_item_type_id' => $this->level3_item_type_id,
			'level3_item_id'      => $this->level3_item_id,
			'scope_id'            => $this->scope_id,
			'file_id'             => $this->file_id,
			'status_id'           => $this->status_id,
			'timestamp'           => $this->timestamp,
			'deleted'             => $this->deleted
		);
	}

	public function set_id($value)
	{
		$this->id = is_numeric($value) ? intval($value) : $this->id;
		return $this;
	}

	public function set_user_id($value)
	{
		$this->user_id = $value;
		return $this;
	}

	// Set the action ID, by specifying the ID of the action
	public function set_action_id($value)
	{
		$this->action_id = $value;
		return $this;
	}

	// Set the action ID, by specifying the name of the action
	public function set_action($value)
	{
		$action = $this->sql_get_action($value);
		$this->action_id = isset($action['id']) ? $action['id'] : $this->action_id;
		return $this;
	}

	// Set the item type ID, by specifying the ID of the item type
	public function set_item_type_id($value, $level = 1)
	{
		switch ($level)
		{
			case 1: $this->item_type_id        = is_numeric($value) ? intval($value) : $this->item_type_id;        break;
			case 2: $this->level2_item_type_id = is_numeric($value) ? intval($value) : $this->level2_item_type_id; break;
			case 3: $this->level3_item_type_id = is_numeric($value) ? intval($value) : $this->level3_item_type_id; break;
		}

		return $this;
	}

	// Set the item type ID, by specifying the name of the item type
	public function set_item_type($value, $level = 1)
	{
		$item_type = $this->sql_get_item_type($value);
		switch ($level)
		{
			case 1: $this->item_type_id        = isset($item_type['id']) ? $item_type['id'] : $this->item_type_id;        break;
			case 2: $this->level2_item_type_id = isset($item_type['id']) ? $item_type['id'] : $this->level2_item_type_id; break;
			case 3: $this->level3_item_type_id = isset($item_type['id']) ? $item_type['id'] : $this->level3_item_type_id; break;
		}
		return $this;
	}

	public function set_item_subtype_id($value)
	{
		$this->item_subtype_id = is_numeric($value) ? intval($value) : $this->item_subtype_id;
		return $this;
	}

	public function set_item_subtype($value)
	{
		$item_subtype = $this->sql_get_item_type($value);
		$this->item_subtype_id = isset($item_subtype['id']) ? $item_subtype['id'] : $this->item_subtype_id;
		return $this;
	}

	public function set_item_id($value, $level = 1)
	{
		switch ($level)
		{
			case 1: $this->item_id        = is_numeric($value) ? intval($value) : $this->item_id;        break;
			case 2: $this->level2_item_id = is_numeric($value) ? intval($value) : $this->level2_item_id; break;
			case 3: $this->level3_item_id = is_numeric($value) ? intval($value) : $this->level3_item_id; break;
		}
		return $this;
	}

	public function set_scope_id($value)
	{
		$this->scope_id = is_numeric($value) ? intval($value) : $this->scope_id;
		return $this;
	}

	public function set_file_id($value)
	{
		$this->file_id = is_numeric($value) ? intval($value) : $this->scope_id;
		return $this;
	}

	public function set_status_id($value)
	{
		$this->status_id = is_numeric($value) ? intval($value) : $this->status_id;
		return $this;
	}

	public function set_deleted($value)
	{
		$this->deleted = ($value == 1 OR $value == 0) ? $value : $this->deleted();
		return $this;
	}

	public function get_id()
	{
		return $this->id;
	}

	public function validate()
	{
		return TRUE;
	}

	public function add()
	{
		$user = Auth::instance()->get_user();
		$this->user_id = $user['id'];
		$this->timestamp = date('Y-m-d H:i:s');
		return $this->sql_insert();
	}

	public function save()
	{
	    // If Activity Tracking is not turned on in Settings and if the Tracking Item or Activity Action is disable by the admin do not save
		if (Settings::instance()->get('track_activities') && !in_array($this->item_type_id, (array) Settings::instance()->get('disable_activity_tracking_items'))
            && !in_array($this->action_id, (array)Settings::instance()->get('disable_activity_tracking_actions')))
		{
			$result = (is_numeric($this->id)) ?  $this->sql_update() : $this->sql_insert();
			if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
				Model_Messaging::send_activity_alerts($this->get_instance());
			}
			return $result;
		}
		else
		{
			return TRUE;
		}
	}

	public function delete()
	{
		$this->set_deleted(1);
		return $this->save();
	}

	/*** Private functions ***/
	private function get_details()
	{
		return $this->sql_get_details();
	}

	private function sql_insert()
	{

		$q = DB::insert(self::MAIN_TABLE, array_keys($this->get_instance()))->values($this->get_instance())->execute();
		$this->set_id($q[0]);
		return $q[0];
	}
	private function sql_update()
	{
		return DB::update(self::MAIN_TABLE)->set($this->get_instance())->where('id', '=', $this->id)->execute();
	}

	private function sql_get_details()
	{
		return DB::select_array(array_keys($this->get_instance()))
			->from(self::MAIN_TABLE)
			->where('id', '=', $this->id)
			->where('deleted', '=', 0)
			->execute()
			->current();
	}

	private function sql_get_action($stub)
	{
		return DB::select('id','stub','name')
			->from(self::ACTION_TABLE)
			->where('stub', '=', $stub)
			->execute()
			->current();
	}

	private function sql_get_item_type($stub)
	{
		return DB::select('id','stub','name', 'table_name', 'parent_id')
			->from(self::ITEM_TYPES_TABLE)
			->where('stub', '=', $stub)
			->execute()
			->current();
	}

	/*** Public static functions ***/
	public static function create($id = NULL)
	{
		return new self($id);
	}

	// Get all activities.
	// Set $execute = FALSE, to prevent the query from executing, so more clauses can be added to the query
	public static function get_all($execute = TRUE)
	{
		$q = DB::select(
			'activity.id',
			'activity.user_id', 'user.email', array('user.name', 'firstname'), 'user.surname',
			'activity.action_id', array('action.stub', 'action'), array('action.name', 'action_name'),
			'activity.item_type_id', array('item_type.stub','item_type'), array('item_type.name', 'item_type_name'),
			'activity.item_id',
			'activity.level2_item_type_id', array('level2_item_type.stub','level2_item_type'), array('level2_item_type.name', 'level2_item_type_name'),
			'activity.level2_item_id',
			'activity.level3_item_type_id', array('level3_item_type.stub','level3_item_type'), array('level3_item_type.name', 'level3_item_type_name'),
			'activity.level3_item_id',
			'activity.item_subtype_id', array('item_subtype.stub','item_subtype'), array('item_subtype.name', 'item_subtype_name'),
			'activity.scope_id',
			'activity.file_id', array('file.name', 'file_name'),
			'activity.status_id',
			'activity.timestamp'
		)
			->from(array(self::MAIN_TABLE,       'activity'        ))
			->join(array('engine_users',         'user'            ), 'left')->on('activity.user_id',             '=', 'user.id')
			->join(array(self::ACTION_TABLE,     'action'          ), 'left')->on('activity.action_id',           '=', 'action.id')
			->join(array(self::ITEM_TYPES_TABLE, 'item_type'       ), 'left')->on('activity.item_type_id',        '=', 'item_type.id')
			->join(array(self::ITEM_TYPES_TABLE, 'level2_item_type'), 'left')->on('activity.level2_item_type_id', '=', 'level2_item_type.id')
			->join(array(self::ITEM_TYPES_TABLE, 'level3_item_type'), 'left')->on('activity.level3_item_type_id', '=', 'level3_item_type.id')
			->join(array(self::ITEM_TYPES_TABLE, 'item_subtype'    ), 'left')->on('activity.item_subtype_id',     '=', 'item_subtype.id')
			->join(array('plugin_files_file',    'file'            ), 'left')->on('activity.file_id',             '=', 'file.id')
			->where('activity.deleted', '=', 0)
			->order_by('activity.timestamp', 'desc');

		if ($execute)
		{
			$q = $q->execute()->as_array();
            $q = self::get_message_sender($q);
		}
		return $q;
	}

    private static  function get_message_sender($q)
    {
        foreach($q as $key=>$activity)
        {
            $sender = '' ;
            if ($activity['item_type_name'] == 'Message')
            {
                $r = DB::select('target')->from('plugin_messaging_message_targets')->where('message_id','=',$activity['item_id'])->execute()->as_array();
                foreach($r as $senders)
                {
                    if( ! preg_match("/^[0-9]+$/",$senders['target'])){
                        $sender = $senders['target'];
                    }
                }
                if ($sender == '')
                {
                    $m = DB::select('message')->from('plugin_messaging_messages')->where('id','=',$activity['item_id'])->execute()->as_array();
                    foreach($m as $message)
                    {
                        $pos_at = strpos($message['message'],'@');
                        if ($pos_at !== false)
                        {
                            $name_pos = strpos($message['message'],'Name');
                            $name_part = substr($message['message'],$name_pos);
                            $name = substr($name_part,0,strpos($name_part,'<br'));
                            $sub = substr($message['message'],0,$pos_at);
                            $before_space = explode(' ',$sub);
                            $after = substr($message['message'],$pos_at);
                            $break_pos = strpos($after,'<br');
                            $sender = $name . '<br>Email: '.$before_space[sizeof($before_space)-1]. (($break_pos>-1)? substr($message['message'],$pos_at,$break_pos) : substr($message['message'],$pos_at) );
                        }
                    }
                }
            }
            $q[$key]['sender'] = $sender;
        }
        return $q;
    }

	private static function get_all_where()
	{
		return self::get_all(FALSE);
	}

	// Get all activities for the logged-in user
	public static function get_all_for_user()
	{
		$user = Auth::instance()->get_user();
		$q = self::get_all_where()->where('user.id', '=', $user['id'])->execute()->as_array();
        $q = self::get_message_sender($q);
        return $q;
	}

	public static function get_all_activity_item_types()
    {
        return DB::select('id', 'name')->from(self::ITEM_TYPES_TABLE)->execute()->as_array();
    }

    public static function get_all_activity_actions()
    {
	    return DB::select('id', 'name')->from(self::ACTION_TABLE)->execute()->as_array();
    }
    
    public static function get_all_activity_item_types_for_settings($current)
    {
        $current = (is_array($current)) ? $current : array();
	    $activity_items = self::get_all_activity_item_types();
        $options = '';
        foreach ($activity_items as $activity_item)
        {
            $selected = false;
            if (in_array($activity_item['id'], $current)) {
                $selected = true;
            }
            $options .= '<option value="' . $activity_item['id'] . '" . ' . ($selected ? 'selected="selected"' : '') . '>' . $activity_item['name'] . '</option>';
        }
        return $options;
    }

    public static function get_all_activity_actions_for_settings($current)
    {
        $current = (is_array($current)) ? $current : array();
        $activity_actions = self::get_all_activity_actions();
        $options = '';
        foreach ($activity_actions as $activity_action) {
            $selected = false;
            if (in_array($activity_action['id'], $current)) {
                $selected = true;
            }
            $options .= '<option value="' . $activity_action['id'] . '" . ' . ($selected ? 'selected="selected"' : '') . '>' . $activity_action['name'] . '</option>';
        }
        return $options;
    }
	// Get activity from today.
	// Show all activity if the relevant permission is set. Otherwise show the logged-in user's activity.
	public static function get_latest_activity()
	{
		$q    = self::get_all(FALSE);
		if ( ! Auth::instance()->has_access('settings_activities'))
		{
			$user = Auth::instance()->get_user();
			$q->where('user.id', '=', $user['id']);
		}
		$q = $q->where('activity.timestamp', '>', DB::expr('NOW() - INTERVAL 1 DAY'))->limit(20)->execute()->as_array();
        $q = self::get_message_sender($q);
        return $q;
	}

	// Get all activities for a specified scope
	public static function get_all_by_scope_id($scope_id)
	{
		if (is_null($scope_id))
		{
			return array();
		}
		else
		{
			return self::get_all_where()->where('activity.scope_id', '=', $scope_id)->execute()->as_array();
		}
	}

	// $status_table = the name of the table that stores status names
	// $status_column = the column in $status_table that stores the status names
	public static function get_all_with_status($status_table, $status_column = 'status', $scope_id = NULL)
	{
		$q = self::get_all_where()
			->select(array('status.'.$status_column,'status'))
			->join(array($status_table,'status'),'left')
			->on('activity.status_id', '=', 'status.id');

		if ( ! is_null($scope_id))
		{
			$q->where('activity.scope_id', '=', $scope_id);
		}

		return $q->execute()->as_array();
	}

	public static function get_all_for_item($item_type, $item_id, $status_table = NULL, $status_column = 'status')
	{
		$q = self::get_all_where()
			->where_open()
				->where_open()
					->where('item_type.stub','=',$item_type)
					->and_where('activity.item_id', '=', $item_id)
				->where_close()
				->or_where_open()
					->where('level2_item_type.stub','=',$item_type)
					->and_where('activity.level2_item_id', '=', $item_id)
				->or_where_close()
				->or_where_open()
					->where('level3_item_type.stub','=',$item_type)
					->and_where('activity.level3_item_id', '=', $item_id)
				->or_where_close()
			->where_close();

		if ( ! is_null($status_table))
		{
			$q
				->select(array('status.'.$status_column,'status'))
				->join(array($status_table,'status'),'left')
				->on('activity.status_id', '=', 'status.id');
		}

		return $q->execute()->as_array();
	}

	public static function get_family_activities($family_id)
    {
	    $family = new Model_Family($family_id);
	    $family_members = $family->get_members($family_id);
	    $family_activities = array();
	    foreach($family_members as $family_member)
	    {
	        $family_member_contact_id = $family_member->get_id();
	        $family_activities[$family_member_contact_id] = self::get_contact_activities($family_member_contact_id);
        }
	    return $family_activities;
    }

    // If the contact ID matches the scope ID, it's (most definitely) a match
    public static function get_contact_activities($contact_id)
    {
        $contact = new Model_Contacts3_Contact($contact_id);
        return self::get_all_where()
            ->and_where_open()
                ->where('user_id',  '=', $contact->linked_user_id) // done by the contact
                ->or_where('scope_id', '=', $contact_id) // done to the contact
            ->and_where_close()
            ->execute()
            ->as_array();
    }

	public static function get_for_datatable($filters, $show_scope = TRUE, $show_status = FALSE, $show_notes = TRUE,$show_message_detail = TRUE)
	{
		$columns   = array();
		$columns[] = 'activity.id';
		$columns[] = 'activity.timestamp';
		$columns[] = DB::expr('CONCAT_WS(\' \',`user`.`name`, `user`.`surname`)');
		$columns[] = 'action.name';
		$columns[] = DB::expr('CONCAT_WS(\' \',`item_type`.`name`, `item_subtype`.`name`)');
		if ($show_status)
			$columns[] = 'status.status';
		$columns[] = 'activity.item_id';
		if ($show_scope)
			$columns[] = 'activity.scope_id';
        if ($show_message_detail)
        {
            $columns[] = '';
        }
		if ($show_notes)
			$columns[] = '';

		$q = DB::select()
			->from(array(self::MAIN_TABLE,       'activity'    ))
			->join(array('engine_users',         'user'        ),'left')->on('activity.user_id',         '=', 'user.id')
			->join(array(self::ACTION_TABLE,     'action'      ),'left')->on('activity.action_id',       '=', 'action.id')
			->join(array(self::ITEM_TYPES_TABLE, 'item_type'   ),'left')->on('activity.item_type_id',    '=', 'item_type.id')
			->join(array(self::ITEM_TYPES_TABLE, 'item_subtype'),'left')->on('activity.item_subtype_id', '=', 'item_subtype.id')
			->where('activity.deleted', '=', 0);

		// Global search
		if (isset($filters['sSearch']) and $filters['sSearch'] != '')
		{
			$q->and_where_open();
			for ($i = 0; $i < count($columns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '')
				{
					$q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_where_close();
		}
		// Individual column search
		for ($i = 0; $i < count($columns); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
				$q->and_where($columns[$i],'like','%'.$filters['sSearch_'.$i].'%');
			}
		}
		$q_all       = clone $q;
		$q_displayed = clone $q;

		if (!isset($filters['iDisplayLength']) || $filters['iDisplayLength'] == -1 || $filters['iDisplayLength'] > 100) {
			$filters['iDisplayLength'] = 10;
		}
		// Limit
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q_displayed->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q_displayed->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($columns[$filters['iSortCol_'.$i]] != '')
				{
					$q_displayed->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$q_displayed->order_by('activity.timestamp','desc');

		// Output
		$results = $q_displayed->select(
			'activity.id',
			'activity.user_id', 'user.email', array('user.name', 'firstname'), 'user.surname',
			'activity.action_id', array('action.stub', 'action'), array('action.name', 'action_name'),
			'activity.item_type_id', array('item_type.stub','item_type'), array('item_type.name', 'item_type_name'),
			'activity.item_subtype_id', array('item_subtype.stub','item_subtype'), array('item_subtype.name', 'item_subtype_name'),
			'activity.item_id',
			'activity.scope_id',
			'activity.status_id',
			'activity.timestamp'
		)->execute()->as_array();
        if ($show_message_detail)
        {
            $results = self::get_message_sender($results);
        }
		$output['iTotalDisplayRecords'] = $q_all->select(array(DB::expr('COUNT(*)'),'count'))->execute()->get('count',0);
		$output['iTotalRecords'] = $q->select(array(DB::expr('COUNT(*)'),'count'))->execute()->get('count',0);

		$output['aaData'] = array();
		foreach ($results as $result)
		{
			$row = array();
			$row[] = $result['id'];
			$row[] = '<span class="hidden">'.$result['timestamp'].'</span>'.date('d/m/Y H:i:s', strtotime($result['timestamp']));
			$row[] = $result['firstname'].' '.$result['surname'];
			$row[] = $result['action_name'];
			$row[] = $result['item_type_name'].' '.$result['item_subtype_name'];
			if ($show_status)
			{
				$row[] = $result['status'];
			}
			$row[] = $result['item_id'];
			if ($show_scope)
			{
				$row[] = $result['scope_id'];
			}
            if ($show_message_detail)
            {
                $row[] = $result['sender'];
            }
			if ($show_notes)
			{
				$row[] = '<div rel="popover" data-placement="left" data-original-title="Notes" class="popinit" data-trigger="focus">
				<a href="#" class="activity-notes-icon" data-id="'.$result['id'].'" data-item_type="'.$result['item_type'].'" data-item_id="'.$result['item_id'].'"></a>
				</div>';
			}
			$output['aaData'][] = $row;
		}
		$output['sEcho'] = intval($filters['sEcho']);

		return json_encode($output);
	}

    public static function get_family_member_booking_activities($booking_id)
    {
        return self::get_all_where()->where('activity.item_id', '=', $booking_id)
            ->where('item_type.name', '=', 'booking')
            ->execute()->as_array();
    }

	public static function get_action_list()
	{
		return DB::select('*')
					->from(self::ACTION_TABLE)
					->order_by('name', 'asc')
					->execute()
					->as_array();
	}
	
	public static function get_item_type_list()
	{
		return DB::select('*')
					->from(self::ITEM_TYPES_TABLE)
					->order_by('name', 'asc')
					->execute()
					->as_array();
	}
}
