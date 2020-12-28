<?php defined('SYSPATH') or die('No direct script access.');

class ORM extends Kohana_ORM
{

	// Standard column names. These can be overwritten in the model file, if different
	protected $_created_by_column = 'created_by';
	protected $_date_created_column = 'date_created';
	protected $_modified_by_column = 'modified_by';
	protected $_date_modified_column = 'date_modified';
	protected $_publish_column = 'publish';
    protected $_deleted_column = 'deleted';

    public function __construct($id = null)
    {
        try {
            if ($id && !is_array($id)) {
                return parent::__construct(['id' => $id, $this->_deleted_column => 0]);
            } else {
                return parent::__construct($id);
            }
        } catch (Exception $e) {
            return parent::__construct($id);
        }
    }

    public function get_publish_column()
    {
        return $this->_publish_column;
    }

    function where_undeleted()
    {
        return $this->where($this->_object_name.'.'.$this->_deleted_column, '=', 0);
    }

	function find_undeleted()
	{
		return $this->where($this->_object_name.'.'.$this->_deleted_column, '=', 0)->find();
	}

	function find_all_undeleted()
	{
		return $this->where($this->_object_name.'.'.$this->_deleted_column, '=', 0)->find_all();
	}

	function find_published()
	{
		return $this
			->where($this->_object_name.'.'.$this->_publish_column, '=', 1)
			->where($this->_object_name.'.'.$this->_deleted_column, '=', 0)
			->find();
	}

	function find_all_published()
	{
		return $this
			->where($this->_object_name.'.'.$this->_publish_column, '=', 1)
			->where($this->_object_name.'.'.$this->_deleted_column, '=', 0)
			->find_all();
	}

    // Deprecated alias. Use delete_and_save()
    function set_deleted()
    {
        self::delete_and_save();
    }

    function delete_and_save()
    {
        $this->set($this->_deleted_column, 1);
        return $this->save_with_moddate();
    }

    function toggle_publish_and_save($value)
    {
        $this->set($this->_publish_column, $value);
        return $this->save_with_moddate();
    }

	// Set the date modified and modified by columns when saving
	// If this is the first save, also set the date created and created by columns
	function save_with_moddate()
	{
		$user = Auth::instance()->get_user();

		$this->set($this->_modified_by_column, $user['id']);
		$this->set($this->_date_modified_column, date('Y-m-d H:i:s'));

		if ($this->{$this->_primary_key} == '')
		{
			$this->set($this->_created_by_column, $user['id']);
			$this->set($this->_date_created_column, date('Y-m-d H:i:s'));
		}

		return $this->save();
	}

    /*
     * If a column is of type ENUM, get its options
     */
    public function get_enum_options($column_name)
    {
        $type = DB::query(Database::SELECT, "SHOW COLUMNS FROM " . $this->_table_name." WHERE Field = :column")
            ->parameters([':column' => $column_name])
            ->execute()->get('Type');

        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);

        return $enum;
    }
    /*
     * If a column is of type SET, get its options
     */
    public function get_set_options($column_name)
    {
        $type = DB::query(Database::SELECT, "SHOW COLUMNS FROM " . $this->_table_name." WHERE Field = :column")
            ->parameters([':column' => $column_name])
            ->execute()->get('Type');

        preg_match("/^set\(\'(.*)\'\)$/", $type, $matches);
        $options = explode("','", $matches[1]);

        return $options;
    }

    /**
     * Helper function for DataTables
     * Include this method in a find query to apply sorting, filtering and pagination
     *
     * @param $args - Fields sent by the DataTables API
     * @param $column_definitions - Array of SQL definitions for columns that are to be searched/sorted
     * @return $this
     */
    public function apply_datatable_args($args, $column_definitions)
    {
        $sortColumns = $searchColumns = $column_definitions;

        // Don't search aggregate columns
        foreach ($searchColumns as $key => $column) {
            if (is_array($column) && is_object($column[0]) && strpos($column[0]->compile(), 'MAX(') !== false) {
                unset($searchColumns[$key]);
            }
        }

        // Global column search
        if (!empty($args['sSearch']) && count($searchColumns)) {
            $this->and_where_open();
            for ($i = 0; $i < count($searchColumns); $i++) {
                if (isset($args['bSearchable_'.$i]) && $args['bSearchable_'.$i] == 'true' && !empty($searchColumns[$i])) {
                    $this->or_where($searchColumns[$i],'like','%'.$args['sSearch'].'%');
                }
            }
            $this->where(DB::expr(1), '=', 1);
            $this->and_where_close();
        }

        // Individual column search
        for ($i = 0; $i < count($searchColumns); $i++) {
            if (isset($args['bSearchable_'.$i]) && $args['bSearchable_'.$i] == 'true' && $args['sSearch_'.$i] != '' && !empty($searchColumns[$i])) {
                $this->and_where($searchColumns[$i],'like','%'.$args['sSearch_'.$i].'%');
            }
        }

        // Order
        if (isset($args['iSortCol_0']) && is_numeric($args['iSortCol_0'])) {
            for ($i = 0; $i < $args['iSortingCols']; $i++) {
                if ($sortColumns[$args['iSortCol_' . $i]] != '' && !empty($sortColumns[$i])) {
                    $sortColumn = $sortColumns[$args['iSortCol_' . $i]];
                    if (is_array($sortColumn) && isset($sortColumn[0])) {
                        $sortColumn = $sortColumn[0];
                    }
                    $asc_desc = strtolower($args['sSortDir_' . $i]);
                    if (in_array($asc_desc, array('asc', 'desc'))) {
                        //echo '<pre>' . print_r($sortColumn, 1) . '</pre>';
                        $this->order_by($sortColumn, $asc_desc);
                    }
                }
            }
        } 

        // Limit. Only show the number of records for this paginated page
        if (empty($args['unlimited']) && isset($args['iDisplayLength']) && $args['iDisplayLength'] != -1) {
            if (isset($args['iDisplayStart'])) {
                $this->offset(intval($args['iDisplayStart']));
            }
            $this->limit(intval($args['iDisplayLength']));
        }
        return $this;
    }
}
