<?php
final class Model_Host extends Model
{
    const HOST_TABLE = 'plugin_contacts3_hosts';

    public static function save($host)
    {
        if ($host['contact_id'] && !@$host['id']) {
            $exists = DB::select('*')
                ->from(self::HOST_TABLE)
                ->where('contact_id', '=', $host['contact_id'])
                ->execute()
                ->current();
            if ($exists) {
                $host['id'] = $exists['id'];
            }
        }
        if (@$host['id']) {
            DB::update(self::HOST_TABLE)->set($host)->where('id', '=', $host['id'])->execute();
            $id = $host['id'];
        } else {
            $inserted = DB::insert(self::HOST_TABLE)->values($host)->execute();
            $id = $inserted[0];
        }
        return $id;
    }

    public static function get_by_contact_id($contact_id)
    {
        $host = DB::select('*')
            ->from(self::HOST_TABLE)
            ->where('contact_id', '=', $contact_id)
            ->execute()
            ->current();
        if ($host) {
            $host['facilities'] = explode(',', $host['facilities']);
            $host['student_profile'] = explode(',', $host['student_profile']);
        }
        return $host;
    }

    public static function get_datatable($filters)
    {
        $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
        $output    = array();
        // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
        // These must be ordered, as they appear in the resultant table and there must be one per column
        $columns   = array();
        $columns[] = 'contact.id';
        $columns[] = DB::expr("CONCAT_WS(' ', `contact`.`title`, `contact`.`first_name`, `contact`.`last_name`)");
        $columns[] = 'mobile.value';
        $columns[] = DB::expr("CONCAT_WS(', ', `address`.`address1`, `address`.`address2`)");
        $columns[] = 'hosts.facilities';
        $columns[] = 'hosts.student_profile';
        $columns[] = 'hosts.availability';
        $columns[] = array(DB::expr("if(hosts.pets = '', 'No', 'Yes')"));
        $columns[] = 'hosts.status';
        $columns[] = 'hosts.updated';

        $q   = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS contact.id'),
            array(DB::expr("CONCAT_WS(' ', `contact`.`title`, `contact`.`first_name`, `contact`.`last_name`)"), 'full_name'),
            'hosts.facilities',
            'hosts.student_profile',
            'hosts.availability',
            'hosts.status',
            array(DB::expr("if(hosts.pets = '', 'No', 'Yes')"), 'pets_bool'),
            'hosts.updated',
            'address.address1', 'address.address2',
            array('mobile.value', 'mobile')
        )
            ->from(array(self::HOST_TABLE, 'hosts'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE,      'contact'), 'inner')->on('hosts.contact_id', '=', 'contact.id')
            ->join(array(Model_Contacts3::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'LEFT')->on('contact.id', '=', 'has_role.contact_id')
            ->join(array(Model_Contacts3::ROLE_TABLE,          'role'   ), 'LEFT')->on('has_role.role_id', '=', 'role.id')
            ->join(array(Model_Contacts3::CONTACTS_TYPE_TABLE, 'type'   ), 'LEFT')->on('contact.type',      '=', 'type.contact_type_id')
            ->join(array(Model_Contacts3::FAMILY_TABLE,        'family' ), 'LEFT')->on('contact.family_id', '=', 'family.family_id')
            ->join(array(Model_Contacts3::ADDRESS_TABLE,       'address'), 'LEFT')->on('contact.residence', '=', 'address.address_id')
            ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobile'), 'LEFT')->on('mobile.group_id',   '=', 'contact.notifications_group_id')->on('mobile.deleted', '=', DB::expr('0'))->on('mobile.notification_id', '=', DB::expr('2'))
            ->group_by('contact.id')
            ->where('contact.delete', '=', 0)
            ->and_where('hosts.deleted', '=', 0);

        if (is_numeric(@$filters['check_permission_user_id'])) {
            $filter1 = DB::select('contact3_id')
                ->from(Model_Contacts3::TABLE_PERMISSION_LIMIT)
                ->where('user_id', '=', $filters['check_permission_user_id']);
            $q->and_where_open();
            $q->or_where('contact.id', 'in', $filter1);
            $q->and_where_close();
        }

        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $q->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '') {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
                }
            }
            $q->and_where_close();
        }
        // Individual column search
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '') {
                $filters['sSearch_'.$i] = preg_replace('/\s+/', '%', $filters['sSearch_'.$i]); //replace spaces with %
                $q->and_where($columns[$i],'like','%'.$filters['sSearch_'.$i].'%');
            }
        }

        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1) {
            $filters['iDisplayLength'] = 10;
        }

        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_'.$i]] != '') {
                    $q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
                }
            }
        }
        $q->order_by('contact.date_modified', 'desc');

        $results = $q->execute()->as_array();

        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($results); // displayed results
        $output['aaData'] = array();

        foreach ($results as $result) {
            $row   = array();
            $row[] = $result['id'];
            $row[] = '<a href="/admin/contacts3/add_edit_contact/'.$result['id'].'">'.$result['full_name'].'</a>';
            $row[] = $result['mobile'];
            $row[] = trim(trim($result['address1'].', '.$result['address2']), ',');
            $row[] = $result['facilities'];
            $row[] = $result['student_profile'];
            $row[] = $result['availability'];
            $row[] = $result['pets_bool'];
            $row[] = $result['status'];
            $row[] = date($date_format, strtotime($result['updated']));
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);

        return $output;
    }
}
?>