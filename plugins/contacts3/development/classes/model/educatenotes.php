<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_EducateNotes extends Model implements Interface_Contacts3
{

    /**
     ** ----- CONSTANT VALUES -----
     **/

    const NOTES_TABLE        = 'plugin_contacts3_notes';
    const NOTES_TABLES_TABLE = 'plugin_contacts3_notes_tables';

    /**
     ** ----- PRIVATE MEMBER DATA -----
     **/

    private $id            = null;
    private $link_id       = null;
    private $table_link_id = null;
    private $note          = '';
    private $publish       = 1;
    private $deleted       = 0;
    private $date_created  = '';
    private $date_modified = '';
    private $created_by    = null;
    private $modified_by   = null;


    /**
     ** ----- PUBLIC FUNCTIONS -----
     **/

    function __construct($id = null)
    {
        if(is_numeric($id))
        {
            $this->set_id($id);
            $this->get(true);
        }
    }

    public function load($data)
    {
        foreach($data AS $key=>$value)
        {
            if (property_exists($this,$key))
            {
                $this->{$key} = ($value == '') ? null : $value;
            }
        }
        return $this;
    }

    public function get($autoload = false)
    {
        $data = $this->_sql_get_note();
        if ($autoload)
        {
            $this->load($data);
        }
        return $this;
    }

    public function getByLink($linkId, $tableLinkId)
    {
        $data = DB::select('*')
            ->from(self::NOTES_TABLE)
            ->where('link_id', '=', $linkId)
            ->and_where('table_link_id', '=', $tableLinkId)
            ->and_where('deleted', '=', 0)
            ->order_by('date_modified', 'desc')
            ->execute()
            ->current();
        if ($data) {
            $this->load($data);
        }
        return $this;
    }

    public function save($dontStartTransaction = false)
    {
        $ok = $this->validate();
        if ($ok)
        {
            if (!$dontStartTransaction){
                Database::instance()->begin();
            }
            try
            {
                $this->set_date_modified();
                $this->set_modified_by();
                if (is_numeric($this->id))
                {
                    $this->_sql_update_note();
                }
                else
                {
                    $this->set_created_by();
                    $this->set_date_created();
                    $this->_sql_insert_note();
                }
                //Other saving functions
                if (!$dontStartTransaction) {
                    Database::instance()->commit();
                }
            }
            catch(Exception $e)
            {
                if (!$dontStartTransaction) {
                    Database::instance()->rollback();
                }
                $ok = false;
            }
        }
        return $ok;
    }

    public function get_instance()
    {
        return array(
            'id'                     => $this->id,
            'note'                   => $this->note,
            'link_id'                => $this->link_id,
            'table_link_id'          => $this->table_link_id,
            'publish'                => $this->publish,
            'deleted'                => $this->deleted,
            'date_created'           => $this->date_created,
            'date_modified'          => $this->date_modified,
            'created_by'             => $this->created_by,
            'modified_by'            => $this->modified_by
        );
    }

    public function delete()
    {
        $this->set_deleted(1);
        $this->set_publish(0);
        return $this->save();
    }

    public function validate()
    {
        $valid = true;

        return $valid;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_link_id()
    {
        return $this->link_id;
    }

    public function get_table_link_id()
    {
        return $this->table_link_id;
    }

    public function get_note()
    {
        return $this->note;
    }

    public function set_id($id)
    {
        $this->id = (is_numeric($id) AND $id > 0) ? (int) $id : null;
    }

	/**
	 * Change the value of a key for an instance of this object
	 *
	 * @param $column   string  the name of the object key to be changed
	 * @param $value    mixed   the value the key is being set to
	 * @return $this
	 */
	public function set_column($column, $value)
	{
		$this->{$column} = $value;
		return $this;
	}

    public function set_publish($value)
    {
        $this->publish = $value === 0 ? 0 :10;
    }

    public function set_deleted($value)
    {
        $this->deleted = $value === 1 ? 1 : 0;
    }

    public function set_date_created()
    {
        $this->date_created = date('Y-m-d H:i:s',time());
    }

    public function set_date_modified()
    {
        $this->date_modified = date('Y-m-d H:i:s',time());
    }

    public function set_created_by($created_by = null)
    {
        $logged_in_user   = Auth::instance()->get_user();
        $this->created_by = is_numeric($created_by) ? (int) $created_by : $logged_in_user['id'];
    }

    public function set_modified_by($modified_by = null)
    {
        $logged_in_user    = Auth::instance()->get_user();
        $this->modified_by = is_numeric($modified_by) ? (int) $modified_by : $logged_in_user['id'];
    }

    /**
     ** ----- PUBLIC STATIC FUNCTIONS -----
     **/
    public static function instance ($id = null)
    {
        return new self($id);
    }

    public static function get_all_notes($where_clauses)
    {
        // filter out deleted users before the join, so a note does not disappear if one of its editors is deleted
        $users = DB::select('id', 'email', 'name', 'surname')->from('engine_users')->where('deleted', '=', 0);
        $query = DB::select(
            'note.id', 'note.note', 'note.date_created', 'note.date_modified', array('table.id', 'table_id'), 'table.table',
            array('author.id', 'author_id'), array('author.email', 'author_email'), array('author.name', 'author_name'), array('author.surname', 'author_surname'),
            array('editor.id', 'editor_id'), array('editor.email', 'editor_email'), array('editor.name', 'editor_name'), array('editor.surname', 'editor_surname')
        )
            ->from(array(self::NOTES_TABLE,        'note'))
            ->join(array(self::NOTES_TABLES_TABLE, 'table' ), 'LEFT')->on('note.table_link_id', '=', 'table.id')
            ->join(array($users,                   'author'), 'LEFT')->on('note.created_by',    '=', 'author.id')
            ->join(array($users,                   'editor'), 'LEFT')->on('note.modified_by',   '=', 'editor.id')
            ->where('note.publish', '=', 1)
			->and_where('note.deleted', '=', 0)
			->and_where('table.deleted', '=', 0)
			->and_where('note.note', '<>', 'Booked after start date.')
            ->order_by('note.date_modified', 'desc');
        $query = self::where_clauses($query, $where_clauses);

        return $query->execute()->as_array();
    }

    public static function get_table_link_id_from_name($table)
    {
        return DB::select('id')->from(self::NOTES_TABLES_TABLE)
            ->where('table', '=', $table)->and_where('deleted', '=', 0)
            ->execute()->get('id', 0);
    }

    public static function get_table_name_from_id($id)
    {
        return DB::select('table')->from(self::NOTES_TABLES_TABLE)
            ->where('id', '=', $id)->and_where('deleted', '=', 0)
            ->execute()->get('table', 0);
    }

    /**
     ** ----- PRIVATE FUNCTIONS -----
     **/

    private static function where_clauses($query, $where_clauses)
    {
        foreach ($where_clauses as $clause)
        {
            if     ($clause == 'open')                        $query = $query->where_open ();
            elseif ($clause == 'close')                       $query = $query->where_close();
            elseif (isset($clause[3]) AND $clause[3] == 'or') $query = $query->or_where ($clause[0], $clause[1], $clause[2]);
            else                                              $query = $query->and_where($clause[0], $clause[1], $clause[2]);
        }
        return $query;
    }

    private function _sql_get_note()
    {
        $q = DB::select()->from(self::NOTES_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function _sql_insert_note()
    {
        $q = DB::insert(self::NOTES_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
        $this->set_id($q[0]);
    }

    private function _sql_update_note()
    {
        DB::update(self::NOTES_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
    }

}