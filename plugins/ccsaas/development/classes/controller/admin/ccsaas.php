<?php

class Controller_Admin_Ccsaas extends Controller_Cms
{
    public function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = ['Courses' => self::get_menu_links()];

        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home',    'link' => '/admin'],
        ];
    }

    public function get_menu_links()
    {
        $auth = Auth::instance();
        $menu = [];
        if ($auth->has_access('ccsaas')) {
            if ($auth->has_access('ccsaas_view')) {
                $menu[] = [
                    'name' => 'Websites',
                    'link' => '/admin/ccsaas',
                    'icon' => 'websites'
                ];
            }

            if ($auth->has_access('ccsaas_edit')) {
                $menu[] = [
                    'name' => 'Servers',
                    'link' => '/admin/ccsaas/bservers',
                    'icon' => 'servers'
                ];
            }

            if ($auth->has_access('ccsaas_view_limited')) {
                $menu[] = [
                    'name' => 'My Websites',
                    'link' => '/admin/ccsaas',
                    'icon' => 'websites'
                ];
            }
        }

        return $menu;
    }

    public function action_ajax_get_submenu()
    {
        $menu = self::get_menu_links();
        $return = ['items' => []];

        foreach ($menu as $item) {
            $return['items'][] = [
                'title'    => $item['name'],
                'link'     => $item['link'],
                'icon_svg' => $item['icon']
            ];
        }

        return $return;
    }

    public function action_index()
    {
        if (!Auth::instance()->has_access('ccsaas_view')) {
            IbHelpers::set_message("Access denied!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => 'CC SAAS', 'link' => '/admin/ccsaas'];

        $websites = new Model_Ccsaas_Websites();
        $websites = $websites->find_all_undeleted();
        $branches = new Model_Ccsaas_Branchservers();
        $branches = $branches->find_all();
        $this->template->body = View::factory('admin/list_websites');
        $this->template->body->websites = $websites;
        $this->template->body->branches = $branches;
    }

    public function action_edit()
    {
        if (!Auth::instance()->has_access('ccsaas_edit')) {
            IbHelpers::set_message("Access denied!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        if (Settings::instance()->get('ccsaas_mode') == Model_Ccsaas::BRANCH) {
            IbHelpers::set_message("Edit is not allowed on branch servers", 'warning popup_box');
            $this->request->redirect('/admin/ccsaas');
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => 'CC SAAS', 'link' => '/admin/ccsaas'];
        $this->template->scripts[] = URL::get_engine_plugin_asset('ccsaas', 'js/ccsaas_edit.js', ['script_tags' => true, 'cachebust' => true]);

        $id = $this->request->query('id');
        $website = new Model_Ccsaas_Websites($id);
        if ($website->contact_id) {
            $contact3 = new Model_Contacts3($website->contact_id);
        } else {
            $contact3 = new Model_Contacts3();
        }
        //$data = $website->object();
        $servers = Model_Ccsaas_Branchservers::get_all_options();
        $project_folders = Model_Ccsaas::get_project_folders();
        $this->template->body = View::factory('admin/edit_website');
        $this->template->body->data = $website;
        $this->template->body->contact = $contact3;
        $this->template->body->servers = $servers;
        $this->template->body->project_folders = $project_folders;
    }

    public function action_view()
    {
        if (!Auth::instance()->has_access('ccsaas_view')) {
            IbHelpers::set_message("Access denied!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => 'CC SAAS', 'link' => '/admin/ccsaas'];
        $this->template->scripts[] = URL::get_engine_plugin_asset('ccsaas', 'js/ccsaas_edit.js', ['script_tags' => true, 'cachebust' => true]);

        $id = $this->request->query('id');
        $website = new Model_Ccsaas_Websites($id);
        //$data = $website->object();
        $servers = Model_Ccsaas_Branchservers::get_all_options();
        $project_folders = Model_Ccsaas::get_project_folders();
        $this->template->body = View::factory('admin/view_host');
        $this->template->body->data = $website;
        $this->template->body->servers = $servers;
        $this->template->body->project_folders = $project_folders;
    }


    public function action_bservers()
    {
        if (!Auth::instance()->has_access('ccsaas_view')) {
            IbHelpers::set_message("Access denied!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => 'CC SAAS', 'link' => '/admin/ccsaas'];

        $bservers = new Model_Ccsaas_Branchservers();
        $bservers->where('deleted', '=', 0);
        $bservers = $bservers->find_all();
        $this->template->body = View::factory('admin/list_bservers');
        $this->template->body->bservers = $bservers;
    }

    public function action_edit_bserver()
    {
        if (!Auth::instance()->has_access('ccsaas_edit')) {
            IbHelpers::set_message("Access denied!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        if (Settings::instance()->get('ccsaas_mode') == Model_Ccsaas::BRANCH) {
            IbHelpers::set_message("Edit is not allowed on branch servers", 'warning popup_box');
            $this->request->redirect('/admin/ccsaas');
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => 'CC SAAS', 'link' => '/admin/ccsaas'];
        $this->template->scripts[] = URL::get_engine_plugin_asset('ccsaas', 'js/ccsaas_bserver_edit.js', ['script_tags' => true, 'cachebust' => true]);

        $id = $this->request->query('id');
        $bserver = new Model_Ccsaas_Branchservers($id);
        $this->template->body = View::factory('admin/edit_bserver');
        $this->template->body->data = $bserver;
    }

    public function action_test_vhost()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');

        echo apachectl::configtest();
    }

    public function action_test_graceful()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');

        $ret = apachectl::graceful();
        echo $ret;
    }

    public function action_test_vhost_create()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');

        $template_filename = Settings::instance()->get('ccsaas_vhost_conf_template');
        $template = file_get_contents($template_filename);
        preg_match_all('/\$[a-z0-9\_]+\$/i', $template, $matches);
        $params = array_unique($matches[0]);

        print_r($params);exit;
        $params = array(
            'HOSTNAME' => 'mepro.dev.ibplatform.ie',
            'DBHOST' => '192.168.2.3',
            'DBUSERNAME' => 'root',
            'DBPASSWORD' => '',
            'PROJECT_FOLDER' => 'content1',
        );

        apachectl::vhost_create($params);
    }

    public function action_test_host_create()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');

        $user = Auth::instance()->get_user();
        $website = new Model_Ccsaas_Websites();
        $data['hostname'] = 'mepro.dev.ibplatform.ie';
        $data['starts'] = date::today();
        $data['expires'] = date('Y-m-d', strtotime('+1 year'));
        $data['is_trial'] = 0;
        $data['project_folder'] = 'content1';
        $data['date_created'] = date::now();
        $data['date_modified'] = date::now();
        $data['created_by'] = $user['id'];
        $data['modified_by'] = $user['id'];
        $data['published'] = 1;
        $data['deleted'] = 0;
        $data['branch_server_id'] = 1;
        $website->values($data)->create();
        //echo $website->id;

        $settings = Settings::instance();
        $mode = $settings->get('ccsaas_mode');
        if ($mode == Model_Ccsaas::CENTRAL) {
            // send create host request to branch server
            if ($data['branch_server_id']) {
                //$bs = new Model_Ccsaas_Branchservers(1);
                $bs = new Model_Ccsaas_Branchservers();
                $f = $bs->find(array('host' => 'http://courseco.co'));
                print_r($bs->object());
                //print_r($f);
            }
        } else {
            $vhost_params = array(
                'PROJECT_FOLDER' => $data['project_folder'],
                'HOSTNAME' => $data['hostname'],
                'DBHOST' => '192.168.2.3',
                'DBUSERNAME' => 'root',
                'DBPASSWORD' => '',
            );

            Model_Ccsaas::create_vhost($vhost_params);
            // create host on self
        }
    }
}