<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Donations extends Controller_Cms
{

    public function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',   'link' => '/admin'),
            array('name' => 'Requests', 'link' => '/admin/donations/requests')
        );
        $this->template->sidebar->menus = array
        (
            array(
                array('name' => 'Received'  , 'link' => '/admin/donations/requests_received'),
                array('name' => 'Rejected'  , 'link' => '/admin/donations/requests_rejected'),
                array('name' => 'Approved'  , 'link' => '/admin/donations/requests_approved'),
                array('name' => 'Invalid'  , 'link' => '/admin/donations/requests_invalid'),
                array('name' => 'Offline'  , 'link' => '/admin/donations/requests_offline'),
                array('name' => 'Ad-Hoc'  , 'link' => '/admin/donations/requests_adhoc')
            )
        );

        $this->template->sidebar->menus[0][] = array(
            'name' => 'Products',
            'link' => '/admin/donations/products'
        );
    }

    public static function sort_results($row1, $row2)
    {
        $d1 = strtotime($row1['created']);
        $d2 = strtotime($row2['created']);
        if ($d1 < $d2) {
            return 1;
        } else if ($d1 == $d2) {
            if (isset($row2['message_id']) && isset($row1['message_id'])) {
                return 0;
            } else if (isset($row2['message_id'])) {
                return -1;
            } else {
                return 1;
            }
        } else {
            return -1;
        }
    }

    public function action_index()
    {
        return $this->action_requests();
    }


    public function action_requests_received()
    {
        return $this->action_requests('received');
    }

    public function action_requests_rejected()
    {
        return $this->action_requests('rejected');
    }

    public function action_requests_approved()
    {
        return $this->action_requests('approved');
    }

    public function action_requests_invalid()
    {
        return $this->action_requests('invalid');
    }

    public function action_requests_offline()
    {
        return $this->action_requests('offline');
    }

    public function action_requests_adhoc()
    {
        return $this->action_requests('adhoc');
    }

    public function action_requests($id = '')
    {
        //$id = $this->request->param('id');

        $params = array();
        if ($this->request->post('mobile')) {
            $params['mobile'] = $this->request->post('mobile');
        }
        if ($id == 'received') {
            $params['status'] = 'Processing';
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Received'  , 'link' => '/admin/donations/requests_received');
        } else if ($id == 'rejected') {
            $params['status'] = 'Rejected';
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Rejected'  , 'link' => '/admin/donations/requests_rejected');
        } else if ($id == 'confirmed') {
            $params['status'] = 'Confirmed';
        } else if ($id == 'approved') {
            $params['status'] = 'Completed';
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Approved'  , 'link' => '/admin/donations/requests_approved');
        } else if ($id == 'invalid') {
            $params['status'] = 'Rejected';
            $params['product_id'] = null;
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Invalid'  , 'link' => '/admin/donations/requests_invalid');
        } else if ($id == 'offline') {
            $params['status'] = 'Offline';
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Offline'  , 'link' => '/admin/donations/requests_offline');
        } else if ($id == 'adhoc') {
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Ad-Hoc'  , 'link' => '/admin/donations/requests_adhoc');
        }
        $donations = Model_Donations::search($params);
        if ($this->request->post('output') == 'json') {
            //$messages = Model_Donations::get_messages($params['mobile']);
            //$result = array_merge($donations, $messages);
            //usort($result, 'Controller_Admin_Donations::sort_results');

            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json');
            echo json_encode($donations);
        } else {
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('donations') . 'js/list.js"></script>';
            $this->template->body = View::factory('/admin/list_donations')->set('donations', $donations);
            $this->template->body->adhoc = $id == 'adhoc';
        }
    }

    public function action_status_set()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');
        $post = $this->request->post();
        echo json_encode(
            array(
                'result' => Model_Donations::status_set(
                    $post['id'], @$post['status'], @$post['reply'], @$post['note'], @$post['paid'], @$post['mobile'], @$post['mute']
                )
            )
        );
    }

    public function action_products()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Products'  , 'link' => '/admin/donations/products');
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('donations') . 'js/products.js"></script>';
        $this->template->sidebar->tools = '<div class="btn-group">' .
            '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Select Action <span class="caret"></span></button>' .
            '<ul class="dropdown-menu">' .
            '<li><a class="product-edit" data-id="new">Add Product</a></li>' .
            '</ul></div>';
        $products = Model_Donations::products();
        $this->template->body = View::factory('/admin/list_products')->set('products', $products);
    }

    public function action_product()
    {
        $post = $this->request->post();
        $id = $post['id'];
        if (@$post['action']) {
            $product = $post;
            unset($product['action']);
            $product = Model_Donations::product_save($product);
        } else {
            $product = Model_Donations::product_details($id);
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');
        echo json_encode(array('product' => $product));
    }

    public function action_test_insms()
    {
        $this->template->body = View::factory('test_insms');
    }
}