<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Cardbuilder extends Model
{
    /* Private member data */
    private $id                   = NULL;
    private $user_id              = NULL;
    private $employee_name        = '';
	private $post_nominal_letters = '';
    private $title                = '';
    private $department           = '';
    private $telephone            = '';
    private $fax                  = '';
    private $mobile               = '';
    private $email                = '';
    private $office_id            = NULL;
    private $approved             = 0;
    private $created_by           = NULL;
    private $modified_by          = NULL;
    private $date_created         = NULL;
    private $date_modified        = NULL;
    private $publish              = 1;
    private $deleted              = 0;
    private $printed              = 0;

    public function __construct($id = NULL)
    {
        if ( ! is_null($id) AND is_numeric($id))
        {
            $this->set_id($id);
            $this->get(true);
        }
    }

    /**
     * Dynamically set member data.
     * @param $data
     */
    public function set($data)
    {
        foreach($data AS $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * Return data from database for this card.
     * @param  $autoload
     * @return array
     */
    public function get($autoload = FALSE)
    {
        $data = $this->get_card_details();
        if ($autoload)
        {
            $this->set($data);
        }
        return $data;
    }

    public function get_instance()
    {
        return array(
            'id'                   => $this->id,
            'user_id'              => $this->user_id,
            'employee_name'        => $this->employee_name,
			'post_nominal_letters' => $this->post_nominal_letters,
			'title'                => $this->title,
            'department'           => $this->department,
            'telephone'            => $this->telephone,
            'fax'                  => $this->fax,
            'mobile'               => $this->mobile,
            'email'                => $this->email,
            'office_id'            => $this->office_id,
            'approved'             => $this->approved,
            'created_by'           => $this->created_by,
            'modified_by'          => $this->modified_by,
            'date_created'         => $this->date_created,
            'date_modified'        => $this->date_modified,
            'publish'              => $this->publish,
            'deleted'              => $this->deleted,
            'printed'              => $this->printed
        );
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function get_unapproved_cards($user_id = NULL)
    {
        return $this->sql_get_cards(TRUE, TRUE, $user_id);
    }

	public function set_column($column, $value)
	{
		$this->{$column} = $value;
		return $this;
	}

    /**
     * @param array $value
     * @return $this
     */
    public function set_id($value = NULL)
    {
        $this->id = is_numeric($value) ? intval($value) : $this->id;
        return $this;
    }
    public function set_user_id($value = NULL)
    {
        $this->user_id = is_numeric($value) ? intval($value) : $this->user_id;
        return $this;
    }
    public function set_employee_name($value = NULL)
    {
        $this->employee_name = $value;
        return $this;
    }
	public function set_post_nominal_letters($value = NULL)
	{
		$this->post_nominal_letters = $value;
		return $this;
	}
	public function set_title($value = NULL)
	{
		$this->title = $value;
		return $this;
	}
    public function set_department($value = NULL)
    {
        $this->department = $value;
        return $this;
    }
    public function set_telephone($value = NULL)
    {
        $this->telephone = $value;
        return $this;
    }
    public function set_fax($value = NULL)
    {
        $this->fax = $value;
        return $this;
    }
    public function set_mobile($value = NULL)
    {
        $this->mobile = $value;
        return $this;
    }
    public function set_email($value = NULL)
    {
        $this->email = $value;
        return $this;
    }
    public function set_office_id($value = NULL)
    {
        $this->office_id = is_numeric($value) ? intval($value) : $this->office_id;
        return $this;
    }
    public function set_approved($value = NULL)
    {
        $this->approved = is_numeric($value) ? intval($value) : $this->approved;
        return $this;
    }
    public function set_modified_by($value = NULL)
    {
        $this->modified_by = $value;
        return $this;
    }
    public function set_publish($value = NULL)
    {
        $this->publish = is_numeric($value) ? intval($value) : $this->publish;
    }
    public function set_deleted($value = NULL)
    {
        $this->deleted = is_numeric($value) ? intval($value) : $this->deleted;
    }

    public function update_date_modified()
    {
        $this->date_modified = date('Y-m-d H:i:s');
        return $this;
    }

    public function validate()
    {
        return TRUE;
    }

    public function add()
    {
        // Set the logged-in user as the first and most recent editor
        // Set now as the first and most recent edit dates
        $logged_in_user      = Auth::instance()->get_user();
        $this->created_by    = $logged_in_user['id'];
        $this->modified_by   = $logged_in_user['id'];
        $this->date_created  = date('Y-m-d H:i:s');
        $this->date_modified = date('Y-m-d H:i:s');
        return $this->sql_insert_card();
    }

    public function save()
    {
        // Set the logged-in user as the most recent editor
        // Set now as the most recent edit date
        $logged_in_user      = Auth::instance()->get_user();
        $this->modified_by   = $logged_in_user['id'];
        $this->date_modified = date('Y-m-d H:i:s');
        return $this->sql_update_card();
    }

    public function delete()
    {
        $this->set_deleted(1);
        $this->set_publish(0);
        return $this->save();
    }

    /*
     * Private functions
     */
    private function get_card_details()
    {
        return $this->sql_get_card_details();
    }

    private function sql_insert_card()
    {
        $q = DB::insert('plugin_cardbuilder_cards',array_keys($this->get_instance()))
            ->values($this->get_instance())
            ->execute();
        $this->set_id($q[0]);
        return $q[0];
    }

    private function sql_get_card_details()
    {
        return DB::select_array(array_keys($this->get_instance()))
            ->from('plugin_cardbuilder_cards')
            ->where('id', '=', $this->id)
            ->execute()
            ->current();
    }
    private function sql_get_cards($unapproved_only = FALSE, $published_only = FALSE, $user_id = NULL)
    {
        $q = DB::select_array(array_keys($this->get_instance()))->from('plugin_cardbuilder_cards')->where('deleted', '=', 0);

        if ($unapproved_only)
        {
            $q = $q->where('approved', '=', 0);
        }
        if ($published_only)
        {
            $q = $q->where('publish', '=', 1);
        }
        if ( ! is_null($user_id))
        {
            $q = $q->where('user_id', '=', $user_id);
        }

        return $q->execute();
    }

    private function sql_update_card()
    {
        return DB::update('plugin_cardbuilder_cards')->set($this->get_instance())->where('id','=',$this->id)->execute();
    }

    public static function get_all_cards()
    {
        return DB::select()->from('plugin_cardbuilder_cards')->where('publish','=',1)->and_where('deleted','=',0)->execute()->as_array();
    }

	public static function get_users_cards($id)
	{
		return DB::select()->from('plugin_cardbuilder_cards')->where('publish','=',1)->and_where('deleted','=',0)->and_where('created_by', '=', $id)->execute()->as_array();
	}

    public static function get_list_of_cards($list,$limit = null)
    {
        $q = DB::select()->from('plugin_cardbuilder_cards')->where('publish','=',1)->and_where('deleted','=',0)->and_where('id','IN',$list);

        if(is_numeric($limit))
        {
            $q->limit($limit);
        }

        return $q->execute()->as_array();
    }

	public static function set_cards_approved($cards,$status)
	{
		$approved = $status ? 1 : 0;
		DB::update('plugin_cardbuilder_cards')->set(array('approved' => $approved))->where('id','IN',$cards)->execute();
	}

	public static function set_cards_printed($cards,$status)
	{
		$printed = $status ? 1 : 0;
		DB::update('plugin_cardbuilder_cards')->set(array('printed' => $printed))->where('id','IN',$cards)->execute();
	}

	public static function set_cards_order($cards, $order_id)
	{
		DB::update('plugin_cardbuilder_cards')->set(array('order_id' => $order_id))->set(array('approved' => 1))->where('id','IN',$cards)->execute();
	}

    public static function save_images($images, $cards)
    {
        $result = array();
		$i = 0;
        foreach($images as $key=>$image)
        {
            list($type, $data) = explode(';', $image);
            list(, $data)      = explode(',', $image);
            $data = str_replace(' ','+',$data);
            $data = base64_decode(chunk_split($data));
            $filename = time().'-'.$cards[$i].'.png';
            file_put_contents('/var/tmp/'.$filename, $data);
            $result[] = $filename;
			$i++;
        }

        return $result;
    }
}