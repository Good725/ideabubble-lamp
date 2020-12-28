<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Families extends ORM
{
    const TABLE = 'plugin_family_families';

    protected $_table_name = self::TABLE;

    protected $_has_many = array(
        'contact' => array('model' => 'contacts', 'through' => Model_Families_Members::TABLE),
    );

    protected static $extentions = array();
    public static function registerExtention(FamiliesExtention $extention)
    {
        self::$extentions[] = $extention;
    }

    public static function getExtentions()
    {
        return self::$extentions;
    }


    public static function get_family_of($contact_id, $family_id = null)
    {
        $familyq = DB::select('families.*')
            ->from(array(self::TABLE, 'families'));

        if ($contact_id) {
            $familyq->join(array(Model_Families_Members::TABLE, 'members'), 'inner')
                ->on('families.id', '=', 'members.family_id');
            $familyq->where('members.contact_id', '=', $contact_id);
        }
        if ($family_id) {
            $familyq->where('families.id', '=', $family_id);
        }
        $family = $familyq
            ->and_where('families.deleted', '=', 0)
            ->execute()
            ->current();

        if ($family) {
            $family['members'] = Model_Families_Members::get_all($family['id']);
        }
        return $family;
    }

    public static function autocomplete($term = null)
    {
        $familiesq = DB::select('families.id', array('families.family', 'value'))
            ->from(array(self::TABLE, 'families'))
            ->and_where('families.deleted', '=', 0);
        if ($term) {
            $familiesq->and_where('families.family', 'like', '%' . $term . '%');
        }
        $familiesq->order_by('families.family', 'asc');
        $families = $familiesq->execute()->as_array();
        return $families;
    }

    public static function set_family($family_id, $family, $published, $deleted, $primary_contact_id = null)
    {
        $user = Auth::instance()->get_user();
        $params = array();
        $params['family'] = $family;
        if ($primary_contact_id !== null) {
            $params['primary_contact_id'] = $primary_contact_id;
        }
        if (is_numeric($family_id)) {
            $params['updated'] = date('Y-m-d H:i:s');
            $params['updated_by'] = $user['id'];
        } else {
            $params['created'] = $params['updated'] = date('Y-m-d H:i:s');
            $params['created_by'] = $params['updated_by'] = $user['id'];
        }
        $params['published'] = $published;
        $params['deleted'] = $deleted;
        if (is_numeric($family_id)) {
            DB::update(self::TABLE)->set($params)->where('id', '=', $family_id)->execute();
        } else {
            $result = DB::insert(self::TABLE)->values($params)->execute();
            $family_id = $result[0];
        }

        return $family_id;
    }

    public static function get_datatable($filters)
    {
        $output    = array();
        // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
        // These must be ordered, as they appear in the resultant table and there must be one per column
        $columns   = array();
        $columns[] = 'families.id';
        $columns[] = 'families.family';
        $columns[] = 'families.updated';


        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS families.id'),
            'families.family',
            'families.published',
            'families.updated'
        )
            ->from(array(self::TABLE, 'families'));
        if (isset($filters['check_permission_user_id']) && is_numeric($filters['check_permission_user_id'])) {
            $filter1 = DB::select('members.family_id')
                ->from(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'permissions'))
                    ->join(array(Model_Families_Members::TABLE, 'members'), 'inner')
                        ->on('permissions.contact_id', '=', 'members.contact_id')
                ->where('permissions.user_id', '=', $filters['check_permission_user_id']);
            $filter2 = DB::select('members.family_id')
                ->from(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'permissions'))
                    ->join(array(Model_Families_Members::TABLE, 'members'), 'inner')
                        ->on('permissions.contact_id', '=', 'members.contact_id')
                    ->join(array(Model_Contacts::TABLE_HAS_RELATIONS, 'related1'), 'inner')
                        ->on('permissions.contact_id', '=', 'related1.contact_1_id')
                ->where('permissions.user_id', '=', $filters['check_permission_user_id']);
            $filter3 = DB::select('members.family_id')
                ->from(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'permissions'))
                    ->join(array(Model_Families_Members::TABLE, 'members'), 'inner')
                        ->on('permissions.contact_id', '=', 'members.contact_id')
                    ->join(array(Model_Contacts::TABLE_HAS_RELATIONS, 'related1'), 'inner')
                        ->on('permissions.contact_id', '=', 'related1.contact_2_id')
                ->where('permissions.user_id', '=', $filters['check_permission_user_id']);
            $select->and_where_open();
            $select->or_where('families.id', 'in', $filter1);
            $select->or_where('families.id', 'in', $filter2);
            $select->or_where('families.id', 'in', $filter3);
            $select->and_where_close();
        }

        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $select->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $select->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $select->and_where_close();
        }
        // Individual column search
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $filters['sSearch_' . $i] != '') {
                $select->and_where($columns[$i], 'like', '%'.$filters['sSearch_' . $i] . '%');
            }
        }

        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) || $filters['iDisplayLength'] == -1 || $filters['iDisplayLength'] > 100) {
            $filters['iDisplayLength'] = 10;
        }

        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $select->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $select->offset(intval($filters['iDisplayStart']));
            }
        }

        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_' . $i]] != '') {
                    $select->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }
        $select->order_by('families.updated', 'desc');

        $results = $select->execute()->as_array();

        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords']        = count($results); // displayed results
        $output['aaData']               = array();

        foreach ($results as $result) {
            $row   = array();
            $row[] = $result['id'];
            $row[] = $result['family'];
            $row[] = $result['updated'];
            $row[] = '<i class="icon-view"></i>';
            $row[] = $result['published'] == 1 ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>';
            $row[] = '<i class="icon-remove-circle"></i>';
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);

        return $output;
    }
}
