<?php

class Model_Inventory extends ORM
{
    const TABLE_ITEMS = 'plugin_inventory_items';

    protected $_table_name           = self::TABLE_ITEMS;
    protected $_date_created_column  = 'created';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated';

    public static function item_save($data, $user = null)
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
                $existing_item = DB::select('*')
                    ->from(self::TABLE_ITEMS)
                    ->where('id', '=', $id)
                    ->execute()
                    ->current();
                if ($existing_item) {
                    DB::update(Model_Product::MAIN_TABLE)
                        ->set(
                            array(
                                'title' => $data['title'],
                                'category_id' => $data['category_id'],
                                'date_modified' => $data['updated'],
                                'modified_by' => $data['updated_by']
                            )
                        )
                        ->where('id', '=', $existing_item['product_id'])
                        ->execute();
                    DB::update(self::TABLE_ITEMS)->set($data)->where('id', '=', $id)->execute();
                }
            } else {
                unset($data['id']);
                $product_inserted = DB::insert(Model_Product::MAIN_TABLE)
                    ->values(
                        array(
                            'title' => $data['title'],
                            'category_id' => $data['category_id'],
                            'date_entered' => $data['created'],
                            'created_by' => $data['created_by'],
                            'date_modified' => $data['updated'],
                            'modified_by' => $data['updated_by']
                        )
                    )->execute();
                $data['product_id'] = $product_inserted[0];
                $inserted = DB::insert(self::TABLE_ITEMS)
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

    public static function search($params = array())
    {
        $sortColumns = isset($params['columns']) ? $params['columns'] : [];

        $select = DB::select(
            DB::expr((@$params['count'] ? 'SQL_CALC_FOUND_ROWS ' : '') . 'inventories.*'),
            ['inventories.title', 'label'],
            [DB::expr("CONCAT_WS(' ', `creator`.`name`, `creator`.`surname`, `creator`.`email`)"), "creator"],
            'categories.category'
        )
            ->from([self::TABLE_ITEMS, 'inventories'])
                ->join([Model_Users::MAIN_TABLE, 'creator' ], 'left')->on('inventories.created_by',  '=', 'creator.id')
                ->join([Model_Category::MAIN_TABLE, 'categories'], 'left')->on('inventories.category_id', '=', 'categories.id')
            ->where('inventories.deleted', '=', 0);

        if (@$params['purchase_item_id']) {
            $select->join([Model_Purchasing::TABLE_HAS_ITEMS, 'poitems'], 'inner')
                ->on('inventories.id', '=', 'poitems.inventory_item_id')
                ->and_where('poitems.id', '=', $params['purchase_item_id']);
        }

        if (@$params['term']) {
            $select->and_where('inventories.title', 'like', '%' . $params['term'] . '%');
            $select->order_by('inventories.title', 'asc');
        } else {
            $select->order_by('inventories.updated', 'desc');
        }

        if (!empty($params['sSearch']) && !empty($sortColumns)) {
            $select->and_where_open();
            foreach ($sortColumns as $sortColumn) {
                if ($sortColumn) {
                    $select->or_where($sortColumn, 'like', '%'.$params['sSearch'].'%');
                }
            }
            $select->and_where_close();
        }

        // Order
        if (isset($params['iSortCol_0'])) {
            for ($i = 0; $i < $params['iSortingCols']; $i++) {
                if ($sortColumns[$params['iSortCol_' . $i]] != '') {
                    $select = $select->order_by($sortColumns[$params['iSortCol_' . $i]], $params['sSortDir_' . $i]);
                }
            }
        }

        // Limit and offset. Only show the number of records for this paginated page.
        if (isset($params['iDisplayLength']) && $params['iDisplayLength'] != -1) {
            $select->limit(intval($params['iDisplayLength']));
            if (isset($params['iDisplayStart'])) {
                $select->offset(intval($params['iDisplayStart']));
            }
        }

        $purchases = $select->execute()->as_array();
        DB::query(null, "set @found_rows=found_rows()")->execute();

        return $purchases;
    }

    public static function items_datatable($filter = array())
    {
        $params = $filter;
        $params['count'] = 1;
        $params['columns'] = [
            'inventories.id',
            'inventories.title',
            'categories.category',
            'inventories.amount_type',
            'inventories.use',
            'inventories.vat_rate',
            'inventories.created',
            'inventories.updated',
            'inventories.publish',
            null
        ];
        $data = self::search($params);

        $total = DB::select(DB::expr("@found_rows as total"))->execute()->get('total');
        $result = array(
            'iTotalDisplayRecords' => $total,
            'iTotalRecords' => count($data),
            'aaData' => array(),
            'sEcho' => $filter['sEcho']
        );

        $amount_types = [
        "Volume" => "Lt",
            "Weight" => "Kg",
            "Unit" => "qty"
        ];

        foreach ($data as $item) {
            $row = array();
            $row[] = $item['id'];
            $row[] = $item['title'];
            $row[] = $item['category'];
            $row[] = $amount_types[$item['amount_type']];
            $row[] = $item['use'];
            $row[] = $item['vat_rate'] ? $item['vat_rate'] : '';
            $row[] = IBHelpers::relative_time_with_tooltip($item['created']);
            $row[] = IBHelpers::relative_time_with_tooltip($item['updated']);
            if ($item['publish']) {
                $publish_icon = '<span class="sr-only">Published</span><span class="icon-check"></span>';
            } else {
                $publish_icon = '<span class="sr-only">Unpublished</span><span class="icon-ban-circle"></span>';
            }
            $row[] = '<button type="button" class="btn-link publish_toggle" data-id="'.$item['id'].'" data-publish="'.$item['publish'].'">'.$publish_icon.'</button>';

            $action_dropdown = "<div class='action-btn'>" .
                    "<a class='btn' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><span class='icon-ellipsis-h' aria-hidden='true'></span></a>" .
                    "<ul class='dropdown-menu'>";

            $action_dropdown .= "<li><button type='button' class='view'  data-id='{$item['id']}'>" . __('View') . "</button></li>";
            $action_dropdown .= "</ul>" .
                  "</div>";
            $row[] = $action_dropdown;
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public static function item_details($id)
    {
        $item = DB::select('*')
            ->from(self::TABLE_ITEMS)
            ->where('id', '=', $id)
            ->execute()
            ->current();
        return $item;
    }
}