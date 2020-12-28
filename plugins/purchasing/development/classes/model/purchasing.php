<?php

class Model_Purchasing
{
    const TABLE_PURCHASES = 'plugin_purchasing_purchases';
    const TABLE_HAS_ITEMS = 'plugin_purchasing_purchases_has_items';

    public static function get_approvers()
    {
        $users = DB::select('users.id', DB::expr("CONCAT_WS(' ', users.name, users.surname, users.email) as name"))
            ->from(array(Model_Users::MAIN_TABLE, 'users'))
                ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permission'), 'inner')
                    ->on('users.role_id', '=', 'has_permission.role_id')
                ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                    ->on('has_permission.resource_id', '=', 'resources.id')
            ->where('resources.alias', '=', 'purchasing_approve')
            ->and_where('users.deleted', '=', 0)
            ->order_by('users.name')
            ->execute()
            ->as_array();
        return $users;
    }

    public static function get_money_spent_in_month($month_value) {
        $select = DB::select(DB::expr("ifnull(sum(total), 0) as 'sum'"))->from(SELF::TABLE_PURCHASES)
            ->where(DB::expr("month(purchased)"), "=", $month_value)->where("deleted", "=", "0")->execute()->current()['sum'];
        return $select;
    }
    public static function save($data, $user = null)
    {
        try {
            Database::instance()->begin();

            $products = $data['product'];
            unset($data['product']);
            if ($user == null) {
                $user = Auth::instance()->get_user();
            }
            if (!@$data['id']) {
                $data['created'] = date::now();
                $data['created_by'] = $user['id'];
            }
            
            $data['updated'] = date::now();
            $data['updated_by'] = $user['id'];
            $data['vat'] = $data['vat'] ?? $data['total_vat'] - $data['total'];
            if (is_numeric(@$data['id'])) {
                $id = $data['id'];
                DB::update(self::TABLE_PURCHASES)->set($data)->where('id', '=', $id)->execute();
            } else {
                unset($data['id']);
                $inserted = DB::insert(self::TABLE_PURCHASES)
                    ->values($data)
                    ->execute();
                $id = $inserted[0];
            }

            $item_ids = array();
            if (isset($products)) {
                // Make all products in PO deleted and manually undelete them so we know which items were deleted or not
                DB::update(self::TABLE_HAS_ITEMS)->set(array('deleted' => '1'))->where('purchase_id', '=', $id)->execute();
                foreach ($products as $product) {
                    if (is_numeric(@$product['id'])) {
                        $product['deleted'] = '0';
                        DB::update(self::TABLE_HAS_ITEMS)->set($product)->where('id', '=', $product['id'])->execute();
                        $item_ids[] = $product['id'];
                    } else {
                        if($product['inventory_item_id'] == "")
                            continue;
                        $product['purchase_id'] = $id;
                        unset($product['id']);
                        $pinserted = DB::insert(self::TABLE_HAS_ITEMS)->values($product)->execute();
                        $item_ids[] = $pinserted[0];
                    }
                }
            }
//            if (count($item_ids)) {
//                DB::update(self::TABLE_HAS_ITEMS)->set(array('deleted' => 1))->where('purchase_id', '=', $id)->and_where('id', 'not in', $item_ids)->execute();
//            } else {
//                DB::update(self::TABLE_HAS_ITEMS)->set(array('deleted' => 1))->where('purchase_id', '=', $id)->execute();
//            }
            if (isset($data['status']) && ($data['status'] == "Approved" || $data['status'] == "Declined")) {
                Model_Purchasing::send_requestee_email($data['id'], $data['status']);
            }
            Database::instance()->commit();
            return $id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function search($filters = array())
    {
        $sortColumns = array();
        $sortColumns[] = "purchase.created";
        $sortColumns[] = DB::expr("CONCAT_WS(' ', `department`.`first_name`, `department`.`last_name`)");
        $sortColumns[] = DB::expr("if(purchase.status = 'Approved', '', purchase.id)");
        $sortColumns[] = DB::expr("CONCAT_WS(' ', `creator`.`name`, `creator`.`surname`, `creator`.`email`)");
        $sortColumns[] = DB::expr("CONCAT_WS(' ', `supplier`.`first_name`, `supplier`.`last_name`)");
        $sortColumns[] = "purchase.total";
        $sortColumns[] = "purchase.status";
        $sortColumns[] = "purchase.updated";
        
        $searchColumns = array();
        $searchColumns[] = "purchase.created";
        $searchColumns[] = DB::expr("CONCAT_WS(' ', `department`.`first_name`, `department`.`last_name`)");
        $searchColumns[] = DB::expr("if(purchase.approved IS NULL, '', purchase.id)");
        $searchColumns[] = DB::expr("CONCAT_WS(' ', `creator`.`name`, `creator`.`surname`, `creator`.`email`)");
        $searchColumns[] = DB::expr("CONCAT_WS(' ', `supplier`.`first_name`, `supplier`.`last_name`)");
        $searchColumns[] = "purchase.total";
        $searchColumns[] = "purchase.status";
        $searchColumns[] = "purchase.updated";
        
        $undeleted_contacts = DB::select()->from(Model_Contacts3::CONTACTS_TABLE)->where('delete', '=', 0);
        
        
        $select = DB::select(
            DB::expr((@$filters['count'] ? 'SQL_CALC_FOUND_ROWS ' : '') . 'purchase.*'),
            [DB::expr("CONCAT_WS(' ', `creator`.`name`, `creator`.`surname`, `creator`.`email`)"), "creator"],
            [DB::expr("CONCAT_WS(' ', `department`.`first_name`, `department`.`last_name`)"),          "department"],
            [DB::expr("CONCAT_WS(' ', `supplier`.`first_name`, `supplier`.`last_name`)"),          "supplier"]
        )
            ->from([self::TABLE_PURCHASES, 'purchase'])
                ->join([Model_Users::MAIN_TABLE, 'creator' ], 'left')->on('purchase.created_by',  '=', 'creator.id')
                ->join([$undeleted_contacts,     'department'], 'left')->on('purchase.department_id', '=', 'department.id')
                ->join([$undeleted_contacts,     'supplier'], 'left')->on('purchase.supplier_id', '=', 'supplier.id')
            ->where('purchase.deleted', '=', 0);
            
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $select = $select->and_where_open();
            for ($i = 0; $i < count($searchColumns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $searchColumns[$i] != '') {
                    $select = $select->or_where($searchColumns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $select = $select->and_where_close();
        }
        if($filters['status'] != '') {
            $stasuses_selected = explode(",", $filters['status']);
            $select = $select->and_where_open();
            foreach($stasuses_selected as $stasus_selected) {
                $select = $select->or_where("purchase.status", '=', $stasus_selected);
            }
            $select = $select->and_where_close();
        }
        if (@$filters['before']) {
            $select->and_where_open();
            $select->or_where('purchase.created', '<=', $filters['before']);
            $select->and_where_close();
        }
    
        if (@$filters['after']) {
            $select->and_where_open();
            $select->or_where('purchase.created', '>=', $filters['after']);
            $select->and_where_close();
        }

        if (@$filters['department_id'] || Auth::instance()->has_access('purchasing_view_limited')) {
                $contact_user = Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id']);
                $contact_model = new Model_Contacts3($contact_user['id']);
                foreach ($contact_model->get_contact_relations_details() as $contact_relation) {
                    $filters['department_id'] = $contact_relation['parent_id'];
                }
            $select->and_where('purchase.department_id', '=', $filters['department_id']);
        }

        // Order
        if (@$filters['sort']) {
            $select->order_by($filters['sort'], 'asc');
        } else {
            $select->order_by('purchase.updated', 'desc');
        }
        if (isset($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($sortColumns[$filters['iSortCol_' . $i]] != '') {
                    $select = $select->order_by($sortColumns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }

        // Limit and offset. Only show the number of records for this paginated page.
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $select->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $select->offset(intval($filters['iDisplayStart']));
            }
        }

        $purchases = $select->order_by('purchase.updated', 'desc')->execute()->as_array();
        DB::query(null, "set @found_rows=found_rows()")->execute();

        return $purchases;
    }

    public static function datatable($filter = array())
    {
        $params = $filter;
        $params['count'] = 1;
        $data = self::search($params);
        $total = DB::select(DB::expr("@found_rows as total"))->execute()->get('total');
        $result = array(
            'iTotalDisplayRecords' => $total,
            'iTotalRecords' => count($data),
            'aaData' => array(),
            'sEcho' => $filter['sEcho']
        );

        foreach ($data as $purchase) {
            $date_required = ($purchase['date_required'] == "0000-00-00") ? "" : date('j F Y',
                strtotime($purchase['date_required']));
            $row = array();
            $row[] = '<span class="hidden">'.$purchase['created'].'</span>'.date('H:i, j F Y', strtotime($purchase['created']));
            $row[] = $purchase['department'];
            $row[] = ($purchase['status'] == 'Approved') ? $purchase['id'] : '';
            $row[] = $purchase['creator'];
            $row[] = $purchase['supplier'];
            $row[] = $purchase['total'];
            $row[] = $purchase['status'];
            $row[] = '<span class="hidden">' . $purchase['date_required'] . '</span>' . $date_required;
            $row[] = '<span class="hidden">'.$purchase['updated'].'</span>'.date('H:i, j F Y', strtotime($purchase['updated']));
            $action_dropdown = "<div class='action-btn'>
                    <a class='btn' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='icon-ellipsis-h' aria-hidden='true'></span></a>
                    <ul class='dropdown-menu'>
                        <li><button type='button' class='view'  data-id='{$purchase['id']}'>" . __('View') . "</button></li>";
            if ($purchase['status'] == 'Pending') {
                $action_dropdown .= '<li><button type="button" class="purchasing-purchases-change_status" data-id="' . $purchase['id'] . '" data-action="approve">' . __('Approve') . '</button></li>';
                $action_dropdown .= '<li><button type="button" class="purchasing-purchases-change_status" data-id="' . $purchase['id'] . '" data-action="decline">' . __('Decline') . '</button></li>';
            }
            if ($purchase['status'] == 'Approved') {
                $action_dropdown .= '<li><button type="button" class="purchasing-purchases-change_status" data-id="' . $purchase['id'] . '" data-action="purchase">' . __('Complete Purchase') . '</button></li>';
            }
            $action_dropdown .= "</ul>
                  </div>";
            $row[] = $action_dropdown;
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public static function details($id)
    {
        $purchase = DB::select('*')
            ->from(self::TABLE_PURCHASES)
            ->where('id', '=', $id)
            ->execute()
            ->current();
        if ($purchase) {
            $purchase['products'] = DB::select('has_items.*', ['items.title', 'product'])
                ->from([self::TABLE_HAS_ITEMS, 'has_items'])
                    ->join([Model_Inventory::TABLE_ITEMS, 'items'], 'left')->on('has_items.inventory_item_id', '=', 'items.id')
                ->where('has_items.purchase_id', '=', $id)
                ->and_where('has_items.deleted', '=', 0)
                ->execute()
                ->as_array();
        }
        return $purchase;
    }
    
    public static function send_requestee_email($id, $status) {
        $po_details = Model_Purchasing::details($id);
        $requestee_contact_details = Model_Contacts3::get_linked_contact_to_user($po_details['created_by']);
        $message = new Model_Messaging();
        $recipients = [['target_type' => 'CMS_CONTACT3', 'target' => $requestee_contact_details['id']]];
        // Only if PO has been approved does it get a PO number
        $po_number = ($status == "Approved") ? "Your purchase order number is: {$id}" : "";
        $parameters = [
            'name' => "{$requestee_contact_details['first_name']} {$requestee_contact_details['last_name']}",
            'po_status' => lcfirst($status),
            'po_number' => $po_number,
        ];
        $sent = $message->send_template('requested_po_updated', null, null, $recipients, $parameters);
    }
}