<?php

class Controller_Admin_Inventory extends Controller_Cms
{
    function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Inventory', 'link' => '/admin/inventory'),
        );
    }

    public function action_ajax_get_submenu()
    {
        $user = Auth::instance()->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $return['items'] = array();
        if (Auth::instance()->has_access('inventory_view')) {
            $return['items'][] = ['title' => __('Items'), 'link' => '/admin/inventory', 'icon_svg' => 'items'];
        }
        if (Auth::instance()->has_access('inventory_view_all_stock')) {
            $return['items'][] = ['title' => __('All stock'), 'link' => '/admin/inventory/stock', 'icon_svg' => 'all-stock'];
            $return['items'][] = ['title' => __('My stock'),  'link' => '/admin/inventory/stock?contact_id=' . $contact['id'], 'icon_svg' => 'my-stock'];
        } else if (Auth::instance()->has_access('inventory_view_my_stock')){
            $return['items'][] = ['title' => __('My stock'),  'link' => '/admin/inventory/stock?contact_id=' . $contact['id'], 'icon_svg' => 'my-stock'];
        }

        return $return;
    }

    public function action_ajax_get_inventory_items_autocomplete(){
        $params = $this->request->query();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $inventory_items = Model_Inventory::search($params);
        echo json_encode($inventory_items);
        
    }
    public function action_index()
    {
        if (!Auth::instance()->has_access('inventory_view')) {
            IbHelpers::set_message("You need 'Inventory view' permission enabled to see stock items.", 'warning popup_box');
            if(Auth::instance()->has_access('inventory_view_my_stock')) {
                $auth_contact = Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id']);
                $this->request->redirect("/admin/inventory/stock?contact_id={$auth_contact['id']}");
            } else {
                $this->request->redirect('/admin');
            }
        }
        $this->template->sidebar->breadcrumbs[] = ['link' => '#', 'name' => 'Items'];
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('inventory') . 'js/inventory.js"></script>';
        $this->template->sidebar->tools = '<a><button class="btn item-add" type="button">' . __('Add Item') . '</button></a>';

        $categories = Model_Category::get_all();
        //header('content-type: text/plain');print_r($categories);exit;
        $this->template->body = View::factory('admin/inventory')->set([
            'suppliers'  => Model_Contacts3::get_all_contacts([['subtype.subtype', '=', 'Supplier']]),
            'categories' => $categories
        ]);
    }

    public function action_item_save()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $data = $this->request->post();

        $id = Model_Inventory::item_save($data);
        echo json_encode(array('id' => $id));
    }

    public function action_items()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $result = Model_Inventory::items_datatable($post);
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function action_items_autocomplete()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $term = $this->request->query('term');
        $purchase_item_id = $this->request->query('purchase_item_id');
        $result = Model_Inventory::search(array('term' => $term, 'purchase_item_id' => $purchase_item_id));
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function action_item_view()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $id = $post['id'];
        $result = Model_Inventory::item_details($id);
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function action_stock()
    {
        $params = [
            'after'           => date('Y-m-d', strtotime('this week')),
            'before'          => date('Y-m-d', strtotime('next week')),
            'requested_by_id' => $this->request->query('contact_id')
        ];

        $auth_user = Auth::instance()->get_user();
        $auth_contact = Model_Contacts3::get_linked_contact_to_user($auth_user['id']);
        if(!Auth::instance()->has_access('inventory_view_all_stock')
            && $this->request->query('contact_id') !== $auth_contact['id']) {
            IbHelpers::set_message("You don't have permission to view other people's stock. You need " .
            "'Inventory view all stock' permission enabled.", 'warning popup_box');
            if(Auth::instance()->has_access('inventory_view_my_stock')) {
                $this->request->redirect("/admin/inventory/stock?contact_id={$auth_contact['id']}");
            } else {
                $this->request->redirect('/admin');
            }
        } else if((!Auth::instance()->has_access('inventory_view_my_stock') &&
                !Auth::instance()->has_access('inventory_view_all_stock'))
            && $auth_contact['id'] == $this->request->query('contact_id')) {
            IbHelpers::set_message("You don't have permission to view your stock. You need " .
                "'Inventory view my stock' permission enabled.", 'warning popup_box');
            $this->request->redirect("/admin");
        }  else if ($this->request->query('contact_id')) {
            $contact = new Model_Contacts3_Contact($this->request->query('contact_id'));
        }

        $reports = Model_Stock::get_reports($params);

        $this->template->sidebar->breadcrumbs[] = ['link' => '#', 'name' => isset($contact) ? 'My stock' : 'All stock'];

        $this->template->styles[URL::get_engine_assets_base() . 'css/validation.css'] = 'screen';
        $this->template->styles[URL::get_engine_assets_base() . 'css/bootstrap.daterangepicker.min.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css'] = 'screen';

        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('inventory') . 'js/stock.js"></script>';

        $categories = Model_Category::get_all();
        //header('content-type: text/plain');print_r($categories);exit;
        $this->template->body = View::factory('admin/stock')->set([
            'contact'    => isset($contact) ? $contact : null,
            'suppliers'  => Model_Contacts3::get_all_contacts([['subtype.subtype', '=', 'Supplier']]),
            'categories' => $categories,
            'locations'  => Model_Locations::autocomplete_locations(),
            'items'      => Model_Inventory::search(),
            'reports'    => $reports,
            'start_date' => $params['after'],
            'end_date'   => $params['before']
        ]);
    }

    public function action_stock_details()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $id = $this->request->post('id');
        $stock = Model_Stock::stock_details($id);
        echo json_encode($stock, JSON_PRETTY_PRINT);

    }

    public function action_stock_save()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $stock = $this->request->post();
        $id = Model_Stock::stock_save($stock);
        echo json_encode(array('id' => $id), JSON_PRETTY_PRINT);
    }

    public function action_stock_list()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        if (@$post['status']) {
            $post['status'] = explode(',', @$post['status']);
        }
        $result = Model_Stock::stocks_datatable($post);

        $result['reports'] = Model_Stock::get_reports($post);

        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function action_ajax_toggle_publish()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        try {
            $id      = $this->request->param('id');
            $publish = $this->request->query('publish');
            $item    = new Model_Inventory($id);

            if ($item->id) {
                $item->publish = $publish;
                $item->save_with_moddate();
                $success = true;
                $message = 'Item #'.$item->id.' &quot;'.htmlentities($item->title).'&quot; has been '.($publish ? 'published' : 'unpublished').'.';
            } else {
                $success = false;
                $message = 'Could not find inventory item #'.$id;
            }
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error changing publish state for inventory.\n".$e->getMessage()."\n".$e->getTraceAsString());
            $success = false;
            $message = 'Unexpected error toggling publish status. If this problem continues, please ask an administrator to check the error logs';
        }

        echo json_encode(['success' => $success, 'message' => $message]);
    }

    public function action_purchasing_autocomplete()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $term = $this->request->query('term');
        $items = Model_Stock::get_approved_purchasing_items($term);
        echo json_encode($items, JSON_PRETTY_PRINT);
    }

    public function action_checkin_save()
    {
        if (Auth::instance()->has_access('inventory_checkin')) {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $checkin = $this->request->post();
            $id = Model_Stock::checkin_save($checkin);
            echo json_encode(array('id' => $id), JSON_PRETTY_PRINT);
        }
    }

    public function action_checkin_details()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $id = $this->request->post('id');
        $stock = Model_Stock::checkin_details($id);
        echo json_encode($stock, JSON_PRETTY_PRINT);
    }

    public function action_checkout_save()
    {
        if(Auth::instance()->has_access('inventory_checkout')) {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $checkout = $this->request->post();
            $id = Model_Stock::checkout_save($checkout);
            echo json_encode(array('id' => $id), JSON_PRETTY_PRINT);
        }
    }

    public function action_checkout_details()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $id = $this->request->post('id');
        $stock = Model_Stock::checkout_details($id);
        echo json_encode($stock, JSON_PRETTY_PRINT);
    }
}