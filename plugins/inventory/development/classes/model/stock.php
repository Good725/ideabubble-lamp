<?php

class Model_Stock
{
    const TABLE_STOCKS = 'plugin_inventory_stocks';
    const TABLE_CHECKOUTS = 'plugin_inventory_stocks_has_checkouts';
    const TABLE_CHECKINS = 'plugin_inventory_stocks_has_checkouts_has_checkins';

    public static function stock_save($data, $user = null)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = Auth::instance()->get_user();
            }
            if (!@$data['id']) {
                $data['created'] = date::now();
                $data['created_by'] = $user['id'];
            }

            $data['updated'] = date::now();
            $data['updated_by'] = $user['id'];

            if (is_numeric(@$data['id'])) {
                $id = $data['id'];
                $existing_stock = DB::select('*')
                    ->from(self::TABLE_STOCKS)
                    ->where('id', '=', $id)
                    ->execute()
                    ->current();
                if ($existing_stock) {
                    DB::update(self::TABLE_STOCKS)->set($data)->where('id', '=', $id)->execute();
                }
            } else {
                unset($data['id']);
                $inserted = DB::insert(self::TABLE_STOCKS)
                    ->values($data)
                    ->execute();
                $id = $inserted[0];
                DB::insert(self::TABLE_CHECKOUTS)->values(array('stock_id' => $id))->execute();
            }

            Database::instance()->commit();
            return $id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function search($params = array())
    {
        DB::query(null, "drop temporary table if exists checkout_amounts")->execute();
        DB::query(null, "create temporary table checkout_amounts (id int primary key, stock_id int, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into checkout_amounts (id, stock_id, amount) (select o.id, o.stock_id, o.amount - IFNULL(sum(i.amount), 0) from plugin_inventory_stocks_has_checkouts o left join plugin_inventory_stocks_has_checkouts_has_checkins i on o.id = i.checkout_id where i.deleted = 0 and o.deleted = 0 and o.amount is not null group by o.id)")->execute();
        DB::query(null, "drop temporary table if exists stock_amounts")->execute();
        DB::query(null, "create temporary table stock_amounts (id int primary key, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into stock_amounts (id, amount) (select s.id, s.amount - ifnull(sum(o.amount), 0) from plugin_inventory_stocks s left join checkout_amounts o on s.id = o.stock_id where s.deleted = 0 group by s.id)")->execute();

        $select = DB::select(
            DB::expr((@$params['count'] ? 'SQL_CALC_FOUND_ROWS ' : '') . 'stocks.*'),
            ["stock_amounts.amount", "stock_available"],
            [DB::expr("CONCAT_WS(' ', `creator`.`name`, `creator`.`surname`, `creator`.`email`)"), "creator"],
            [DB::expr("CONCAT_WS(' ', `creator_in`.`name`, `creator_in`.`surname`, `creator_in`.`email`)"), "creator_in"],
            [DB::expr("CONCAT_WS(' ', `creator_out`.`name`, `creator_out`.`surname`, `creator_out`.`email`)"), "creator_out"],
            ['po.id', 'po_id'],
            ['locations.name', 'location'],
            ['items.title', 'item'],
            DB::expr("CONCAT_WS(' ', requestee_ins.first_name, requestee_ins.last_name) as requestee_in"),
            DB::expr("CONCAT_WS(' ', requestee_outs.first_name, requestee_outs.last_name) as requestee_out"),
            ['checkouts.amount', 'amount_out'],
            ['checkout_amounts.amount', 'amount_out_available'],
            ['checkins.amount', 'amount_in'],
            ['checkins.id', 'checkin_id'],
            ['checkouts.id', 'checkout_id'],
            ['checkins.created', 'checkin_date'],
            ['checkouts.created', 'checkout_date'],
            DB::expr("IFNULL(checkins.updated, IFNULL(checkouts.updated, stocks.updated)) as updated"),
            DB::expr("IFNULL(checkins.created, IFNULL(checkouts.created, stocks.created)) as created")
        )
            ->from([self::TABLE_STOCKS, 'stocks'])
                ->join('stock_amounts', 'left')->on('stocks.id', '=', 'stock_amounts.id')
                ->join([Model_Users::MAIN_TABLE, 'creator' ], 'left')->on('stocks.created_by',  '=', 'creator.id')
                ->join([Model_Purchasing::TABLE_HAS_ITEMS, 'poitems'], 'left')->on('stocks.purchasing_item_id', '=', 'poitems.id')
                ->join([Model_Purchasing::TABLE_PURCHASES, 'po'], 'left')->on('poitems.purchase_id', '=', 'po.id')
                ->join([Model_Locations::TABLE_LOCATIONS, 'locations'], 'left')->on('stocks.location_id', '=', 'locations.id')
                ->join([Model_Inventory::TABLE_ITEMS, 'items'], 'left')->on('stocks.item_id', '=', 'items.id')
                ->join([self::TABLE_CHECKOUTS, 'checkouts'], 'left')->on('stocks.id', '=', 'checkouts.stock_id')
                ->join('checkout_amounts', 'left')->on('checkouts.id', '=', 'checkout_amounts.id')->on('checkouts.stock_id', '=', 'checkout_amounts.stock_id')
                ->join([self::TABLE_CHECKINS, 'checkins'], 'left')->on('checkouts.id', '=', 'checkins.checkout_id')
                ->join([Model_Contacts3::CONTACTS_TABLE, 'requestee_ins'], 'left')->on('checkins.requestee_id', '=', 'requestee_ins.id')
                ->join([Model_Contacts3::CONTACTS_TABLE, 'requestee_outs'], 'left')->on('checkouts.requestee_id', '=', 'requestee_outs.id')
                ->join([Model_Users::MAIN_TABLE, 'creator_in' ], 'left')->on('checkins.created_by',  '=', 'creator_in.id')
                ->join([Model_Users::MAIN_TABLE, 'creator_out' ], 'left')->on('checkouts.created_by',  '=', 'creator_out.id')
            ->where('stocks.deleted', '=', 0)
            ->order_by('stocks.updated', 'desc');

        if (@$params['status']) {
            $select->and_where_open();
            if (in_array('Lost', $params['status'])) {
                $select->or_where('checkins.lost', '=', 1);
            }
            if (in_array('Checked In', $params['status'])) {
                $select->or_where('checkins.created', 'is not', null);
            }
            if (in_array('Checked Out', $params['status'])) {
                $select->or_where_open();
                $select->and_where('checkins.created', 'is', null);
                $select->and_where('checkouts.created', 'is not', null);
                $select->or_where_close();
            }
            if (in_array('In Stock', $params['status'])) {
                $select->or_where('checkouts.created', 'is', null);
            }
            $select->and_where_close();
        }

        if (@$params['before']) {
            $select->and_where_open();
            $select->or_where('stocks.created', '<=', $params['before']);
            $select->or_where('checkins.created', '<=', $params['before']);
            $select->or_where('checkouts.created', '<=', $params['before']);
            $select->and_where_close();
        }

        if (@$params['after']) {
            $select->and_where_open();
            $select->or_where('stocks.created', '>=', $params['after']);
            $select->or_where('checkins.created', '>=', $params['after']);
            $select->or_where('checkouts.created', '>=', $params['after']);
            $select->and_where_close();
        }

        if (@$params['keyword']) {
            $select->and_where_open();
            $select->or_where('items.title', 'like', '%' . $params['keyword'] . '%');
            $select->or_where('requestee_ins.first_name', 'like', '%' . $params['keyword'] . '%');
            $select->or_where('requestee_ins.last_name', 'like', '%' . $params['keyword'] . '%');
            $select->or_where('requestee_outs.first_name', 'like', '%' . $params['keyword'] . '%');
            $select->or_where('requestee_outs.last_name', 'like', '%' . $params['keyword'] . '%');
            $select->or_where('creator.name', 'like', '%' . $params['keyword'] . '%');
            $select->or_where('creator.surname', 'like', '%' . $params['keyword'] . '%');
            $select->and_where_close();
        }

        if (!empty($params['requested_by_id'])) {
            $select->and_where_open();
            $select->or_where('requestee_ins.id',  '=', $params['requested_by_id']);
            $select->or_where('requestee_outs.id', '=', $params['requested_by_id']);
            $select->and_where_close();
        }

        if (!empty($params['item_ids'])) {
            $select->and_where('items.id', 'in', $params['item_ids']);
        }

        if (!empty($params['location_ids'])) {
            $select->and_where('locations.id', 'in', $params['location_ids']);
        }

        if (@$params['offset']) {
            $select->offset($params['offset']);
        }

        if (@$params['limit']) {
            $select->limit($params['limit']);
        }

        $stocks = $select->execute()->as_array();
        DB::query(null, "set @found_rows=found_rows()")->execute();

        return $stocks;
    }

    public static function stocks_datatable($filter = array())
    {
        $params = array();
        $params['count'] = 1;
        if (@$filter['status']) {
            $params['status'] = $filter['status'];
        }
        if (@$filter['before']) {
            $params['before'] = $filter['before'];
        }
        if (@$filter['after']) {
            $params['after'] = $filter['after'];
        }
        if (@$filter['iDisplayStart']) {
            $params['offset'] = $filter['iDisplayStart'];
        }
        if (@$filter['iDisplayLength']) {
            $params['limit'] = $filter['iDisplayLength'];
        }
        if (@$filter['sSearch']) {
            $params['keyword'] = $filter['sSearch'];
        }

        if (!empty($filter['requested_by_id'])) {
            $params['requested_by_id'] = $filter['requested_by_id'];
        }

        if (!empty($filter['item_ids'])) {
            $params['item_ids'] = $filter['item_ids'];
        }

        if (!empty($filter['location_ids'])) {
            $params['location_ids'] = $filter['location_ids'];
        }

        $data = self::search($params);

        $total = DB::select(DB::expr("@found_rows as total"))->execute()->get('total');
        $result = array(
            'iTotalDisplayRecords' => $total,
            'iTotalRecords' => count($data),
            'aaData' => array(),
            'sEcho' => $filter['sEcho']
        );

        $amount_types = ['Unit' => '', 'Volume' => 'lt', 'Weight' => 'kg'];
        foreach ($data as $item) {
            $row = array();
            $row[] = '<span class="hidden">' . $item['created'].'</span>' . date('H:i, j F Y', strtotime($item['created']));
            $row[] = $item['creator_in'] ? $item['creator_in'] : ($item['creator_out'] ? $item['creator_out'] : $item['creator']);
            $row[] = $item['requestee_out'] ? $item['requestee_out'] : $item['requestee_in'];
            $row[] = $item['item'];
            $row[] = $item['location'];
            $amount = ($item['amount_in'] ? $item['amount_in'] : ($item['amount_out'] ? $item['amount_out'] : $item['amount']));
            $row[] = ($item['amount_type'] == 'Unit' ? (int)$amount : $amount) . ' ' . $amount_types[$item['amount_type']];
            $available = ($item['amount_in'] ? '' : ($item['amount_out_available'] ? $item['amount_out_available'] : $item['stock_available']));
            $row[] = $available == '' ? '' : (($item['amount_type'] == 'Unit' ? (int)$available : $available) . ' ' . $amount_types[$item['amount_type']]);
            $row[] = $item['amount_in'] ? __('Checked In')  : ($item['amount_out'] ? __('Checked Out') : __('In Stock'));
            $row[] = '<span class="hidden">' . $item['updated'].'</span>' . date('H:i, j F Y', strtotime($item['updated']));
            $action_dropdown = "<div class='action-btn'>" .
                "<a class='btn' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='icon-ellipsis-h' aria-hidden='true'></span></a>" .
                "<ul class='dropdown-menu'>";

            // No "view" button when showing stock for a specific contact
            if (empty($filter['requested_by_id'])) {
                if ($item['amount_in']) {
                    $action_dropdown .= "<li><button type='button' class='view-checkin'  data-checkin_id='{$item['checkin_id']}'>" . __('View') . "</button></li>";
                } else if ($item['amount_out']) {
                    $action_dropdown .= "<li><button type='button' class='view-checkout'  data-checkout_id='{$item['checkout_id']}'>" . __('View') . "</button></li>";
                } else {
                    $action_dropdown .= "<li><button type='button' class='view'  data-id='{$item['id']}'>" . __('View') . "</button></li>";
                }
            }

            if (!$item['amount_in']) {
                if ($item['amount_out'] && Auth::instance()->has_access('inventory_checkin')) {
                    $action_dropdown .= "<li><button type='button' class='checkin'  data-checkout_id='{$item['checkout_id']}'>" . __('Check In') . "</button></li>";
                } else if(Auth::instance()->has_access('inventory_checkout')) {
                    $action_dropdown .= "<li><button type='button' class='checkout'  data-id='{$item['id']}'>" . __('Check Out') . "</button></li>";
                }
            }
            $action_dropdown .= "</ul>" .
                "</div>";
            $row[] = $action_dropdown;

            $result['aaData'][] = $row;
        }
        return $result;
    }

    public static function get_reports($params)
    {
        $in_stock = Model_Stock::search(['status' => ['In Stock']]    + $params);
        $in_stock_counter = 0;
        foreach ($in_stock as $in_stock_item) {
            if(is_numeric($in_stock_item['stock_available'])) {
              $in_stock_counter += intval($in_stock_item['stock_available']);
            }
        }
        Model_Stock::search(['status' => ['Checked Out'], 'count' => 1]    + $params);
        $checked_out = DB::select(DB::expr("@found_rows as total"))->execute()->get('total');
         DB::select(DB::expr("@found_rows as total"))->execute()->get('total');
        $params['status'] = 'Purchased';
        $all = count(Model_Purchasing::search($params));
        return [
            ['text' => 'In stock',    'amount' => $in_stock_counter],
            ['text' => 'Checked out', 'amount' => $checked_out],
            ['text' => 'Available',   'amount' => $in_stock_counter],
            ['text' => 'Purchased',     'amount' => $all],
        ];
    }

    public static function stock_details($id)
    {
        DB::query(null, "drop temporary table if exists checkout_amounts")->execute();
        DB::query(null, "create temporary table checkout_amounts (id int primary key, stock_id int, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into checkout_amounts (id, stock_id, amount) (select o.id, o.stock_id, o.amount - IFNULL(sum(i.amount), 0) from plugin_inventory_stocks_has_checkouts o left join plugin_inventory_stocks_has_checkouts_has_checkins i on o.id = i.checkout_id where i.deleted = 0 and o.deleted = 0 and o.amount is not null group by o.id)")->execute();
        DB::query(null, "drop temporary table if exists stock_amounts")->execute();
        DB::query(null, "create temporary table stock_amounts (id int primary key, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into stock_amounts (id, amount) (select s.id, s.amount - ifnull(sum(o.amount), 0) from plugin_inventory_stocks s left join checkout_amounts o on s.id = o.stock_id where s.deleted = 0 group by s.id)")->execute();

        $stock = DB::select(
            'stocks.*',
            ["stock_amounts.amount", "available"],
            DB::expr("CONCAT_WS(' ', suppliers.first_name, suppliers.last_name) as supplier"),
            ['locations.name', 'location'],
            ['items.title', 'item'],
            ['poitems.title', 'purchasing_item']
        )
            ->from(array(self::TABLE_STOCKS, 'stocks'))
                ->join('stock_amounts', 'left')->on('stocks.id', '=', 'stock_amounts.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'suppliers'), 'left')->on('stocks.supplier_id', '=', 'suppliers.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('stocks.location_id', '=', 'locations.id')
                ->join(array(Model_Inventory::TABLE_ITEMS, 'items'), 'left')->on('stocks.item_id', '=', 'items.id')
                ->join(array(Model_Purchasing::TABLE_HAS_ITEMS, 'has_poitems'), 'left')->on('stocks.purchasing_item_id', '=', 'has_poitems.id')
                ->join(array(Model_Inventory::TABLE_ITEMS, 'poitems'), 'left')->on('has_poitems.inventory_item_id', '=', 'poitems.id')
            ->where('stocks.id', '=', $id)
            ->execute()
            ->current();
        return $stock;
    }

    public static function get_approved_purchasing_items($term = '')
    {
        $label_column = DB::expr("CONCAT(`has_items`.`purchase_id`, ' - ', `suppliers`.`first_name`, ' ', `suppliers`.`last_name`, ' - ', `items`.`title`)");

        $itemsq = DB::select(
            'has_items.*',
            'purchases.supplier_id',
            [DB::expr("CONCAT_WS(' ', suppliers.first_name, suppliers.last_name)"), 'supplier'],
            [$label_column, 'label']
        )
            ->from(array(Model_Purchasing::TABLE_PURCHASES, 'purchases'))
                ->join(array(Model_Purchasing::TABLE_HAS_ITEMS, 'has_items'), 'inner')
                    ->on('purchases.id', '=', 'has_items.purchase_id')
                ->join(array(Model_Inventory::TABLE_ITEMS, 'items'), 'left')
                    ->on('items.id', '=', 'has_items.inventory_item_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'suppliers'), 'left')
                    ->on('purchases.supplier_id', '=', 'suppliers.id')
            ->where('purchases.status', 'in', ['Approved', 'Purchased']);

        if ($term != '') {
            $itemsq->and_where($label_column, 'LIKE', '%' . $term . '%');
        }
        $itemsq->order_by('items.title');
        $items = $itemsq->execute()->as_array();
        return $items;
    }

    public static function checkin_save($data, $user = null)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = Auth::instance()->get_user();
            }
            if (!@$data['id']) {
                $data['created'] = date::now();
                $data['created_by'] = $user['id'];
            }

            $data['updated'] = date::now();
            $data['updated_by'] = $user['id'];

            if (is_numeric(@$data['id'])) {
                $id = $data['id'];
                $existing_checkin = DB::select('*')
                    ->from(self::TABLE_CHECKINS)
                    ->where('id', '=', $id)
                    ->execute()
                    ->current();
                if ($existing_checkin) {
                    DB::update(self::TABLE_CHECKINS)->set($data)->where('id', '=', $id)->execute();
                }
            } else {
                unset($data['id']);
                $inserted = DB::insert(self::TABLE_CHECKINS)
                    ->values($data)
                    ->execute();
                $id = $inserted[0];
            }

            Database::instance()->commit();
            return $id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function checkin_details($id)
    {
        DB::query(null, "drop temporary table if exists checkout_amounts")->execute();
        DB::query(null, "create temporary table checkout_amounts (id int primary key, stock_id int, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into checkout_amounts (id, stock_id, amount) (select o.id, o.stock_id, o.amount - IFNULL(sum(i.amount), 0) from plugin_inventory_stocks_has_checkouts o left join plugin_inventory_stocks_has_checkouts_has_checkins i on o.id = i.checkout_id where i.deleted = 0 and o.deleted = 0 and o.amount is not null group by o.id)")->execute();
        DB::query(null, "drop temporary table if exists stock_amounts")->execute();
        DB::query(null, "create temporary table stock_amounts (id int primary key, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into stock_amounts (id, amount) (select s.id, s.amount - ifnull(sum(o.amount), 0) from plugin_inventory_stocks s left join checkout_amounts o on s.id = o.stock_id where s.deleted = 0 group by s.id)")->execute();

        $stock = DB::select(
            'checkins.*',
            ["checkouts.amount", "checkout_amount"],
            ["checkout_amounts.amount", "checkout_available"],
            DB::expr("CONCAT_WS(' ', suppliers.first_name, suppliers.last_name) as supplier"),
            ['locations.name', 'location'],
            ['items.title', 'item'],
            ['poitems.title', 'purchasing_item'],
            DB::expr("CONCAT_WS(' ', requestee_ins.first_name, requestee_ins.last_name) as requestee")
        )
            ->from(array(self::TABLE_CHECKINS, 'checkins'))
            ->join(array(self::TABLE_CHECKOUTS, 'checkouts'), 'left')->on('checkins.checkout_id', '=', 'checkouts.id')
            ->join('checkout_amounts', 'left')->on('checkouts.id', '=', 'checkout_amounts.id')
            ->join(array(self::TABLE_STOCKS, 'stocks'), 'left')->on('checkouts.stock_id', '=', 'stocks.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'suppliers'), 'left')->on('stocks.supplier_id', '=', 'suppliers.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('stocks.location_id', '=', 'locations.id')
            ->join(array(Model_Inventory::TABLE_ITEMS, 'items'), 'left')->on('stocks.item_id', '=', 'items.id')
            ->join(array(Model_Purchasing::TABLE_HAS_ITEMS, 'has_poitems'), 'left')->on('stocks.purchasing_item_id', '=', 'has_poitems.id')
            ->join(array(Model_Inventory::TABLE_ITEMS, 'poitems'), 'left')->on('has_poitems.inventory_item_id', '=', 'poitems.id')
            ->join([Model_Contacts3::CONTACTS_TABLE, 'requestee_ins'], 'left')->on('checkins.requestee_id', '=', 'requestee_ins.id')
            ->where('checkins.id', '=', $id)
            ->execute()
            ->current();
        return $stock;
    }

    public static function checkout_save($data, $user = null)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = Auth::instance()->get_user();
            }
            if (!@$data['id']) {
                $data['created'] = date::now();
                $data['created_by'] = $user['id'];
            }

            $data['updated'] = date::now();
            $data['updated_by'] = $user['id'];

            if (is_numeric(@$data['id'])) {
                $id = $data['id'];
                $existing_checkout = DB::select('*')
                    ->from(self::TABLE_CHECKOUTS)
                    ->where('id', '=', $id)
                    ->execute()
                    ->current();
                if ($existing_checkout) {
                    DB::update(self::TABLE_CHECKOUTS)->set($data)->where('id', '=', $id)->execute();
                }
            } else {
                unset($data['id']);
                $inserted = DB::insert(self::TABLE_CHECKOUTS)
                    ->values($data)
                    ->execute();
                $id = $inserted[0];
                DB::insert(self::TABLE_CHECKINS)->values(array('checkout_id' => $id))->execute();
            }

            Database::instance()->commit();
            return $id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function checkout_details($id)
    {
        DB::query(null, "drop temporary table if exists checkout_amounts")->execute();
        DB::query(null, "create temporary table checkout_amounts (id int primary key, stock_id int, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into checkout_amounts (id, stock_id, amount) (select o.id, o.stock_id, o.amount - IFNULL(sum(i.amount), 0) from plugin_inventory_stocks_has_checkouts o left join plugin_inventory_stocks_has_checkouts_has_checkins i on o.id = i.checkout_id where i.deleted = 0 and o.deleted = 0 and o.amount is not null group by o.id)")->execute();
        DB::query(null, "drop temporary table if exists stock_amounts")->execute();
        DB::query(null, "create temporary table stock_amounts (id int primary key, amount decimal(10, 2))")->execute();
        DB::query(null, "insert into stock_amounts (id, amount) (select s.id, s.amount - ifnull(sum(o.amount), 0) from plugin_inventory_stocks s left join checkout_amounts o on s.id = o.stock_id where s.deleted = 0 group by s.id)")->execute();

        $stock = DB::select(
            'checkouts.*',
            ["checkout_amounts.amount", "available"],
            ["stocks.amount", "stock_amount"],
            ["stock_amounts.amount", "stock_available"],
            DB::expr("CONCAT_WS(' ', suppliers.first_name, suppliers.last_name) as supplier"),
            ['locations.name', 'location'],
            ['items.title', 'item'],
            ['poitems.title', 'purchasing_item'],
            DB::expr("CONCAT_WS(' ', requestee_outs.first_name, requestee_outs.last_name) as requestee")
        )
            ->from(array(self::TABLE_CHECKOUTS, 'checkouts'))
            ->join('checkout_amounts', 'left')->on('checkouts.id', '=', 'checkout_amounts.id')
            ->join('stock_amounts', 'left')->on('checkouts.stock_id', '=', 'stock_amounts.id')
            ->join(array(self::TABLE_STOCKS, 'stocks'), 'left')->on('checkouts.stock_id', '=', 'stocks.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'suppliers'), 'left')->on('stocks.supplier_id', '=', 'suppliers.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('stocks.location_id', '=', 'locations.id')
            ->join(array(Model_Inventory::TABLE_ITEMS, 'items'), 'left')->on('stocks.item_id', '=', 'items.id')
            ->join(array(Model_Purchasing::TABLE_HAS_ITEMS, 'has_poitems'), 'left')->on('stocks.purchasing_item_id', '=', 'has_poitems.id')
            ->join(array(Model_Inventory::TABLE_ITEMS, 'poitems'), 'left')->on('has_poitems.inventory_item_id', '=', 'poitems.id')
            ->join([Model_Contacts3::CONTACTS_TABLE, 'requestee_outs'], 'left')->on('checkouts.requestee_id', '=', 'requestee_outs.id')
            ->where('checkouts.id', '=', $id)
            ->execute()
            ->current();
        return $stock;
    }
}