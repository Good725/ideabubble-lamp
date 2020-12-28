<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Usermanagement extends Controller_Head
{
    public static $run_after_edit = array();

    public function action_groups()
    {
        if (!Auth::instance()->has_access('role_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Management', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->menus = array(array(
            array('name' => 'Users', 'link' => '/admin/usermanagement/users'),
            array('name' => 'Groups', 'link' => '/admin/usermanagement/groups'),
            array('name' => 'Permissions', 'link' => '/admin/usermanagement/permissions'),
        ));
        $this->template->body = View::factory('admin/usermanagement/groups');
        $this->template->sidebar->tools = '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#group-edit-modal">Create Group</button>';

        $roles = new Model_Roles();
        $role_data = $roles->get_all_roles();
        $controllers = ORM::factory('Resources')->where('type_id', '=', 0)->find_all();
        $code_pieces = ORM::factory('Resources')->where('type_id','=',2)
            ->order_by('name', 'ASC')->find_all();

        $this->template->body->alert = null;
        $this->template->body->roles = $role_data;
        $this->template->body->controllers = $controllers;
        $this->template->body->code_pieces = $code_pieces;
    }

    public function action_group()
    {
        if (!Auth::instance()->has_access('role_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }

        $id = $this->request->param('id');
        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            if (!Auth::instance()->has_access('role_edit')) {
                ibhelpers::set_message('You do not have permission', 'error');
                $this->request->redirect('/admin');
            }

            $id = Model_Roles::save_post($post);
            $this->request->redirect('/admin/usermanagement/group/' . $id);
        }

        $roles = new Model_Roles();
        $role = $roles->get_role_data($id);
        $users = Model_Users::search(array('role_id' => $id));

        $controllers = ORM::factory('Resources')->where('type_id', '=', 0)->order_by('name', 'ASC')->find_all();
        $code_pieces = ORM::factory('Resources')->where('type_id','=',2)
            ->order_by('name', 'ASC')->find_all();

        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/users.js"></script>';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Groups', 'link' => '/admin/usermanagement/groups');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Group ' . $role['role'], 'link' => '/admin/usermanagement/group/' . $id);
        $this->template->sidebar->menus = array(array(

        ));
        if (Auth::instance()->has_access('role_edit')) {
            $this->template->sidebar->tools = '<button type="button" class="btn btn-default edit" data-toggle="modal" data-target="#group-edit-modal">Edit</button> &nbsp;' .
                '<button type="button" class="btn btn-default delete">Delete</button> <br />';
            //'<button type="button" class="btn btn-default add">Add</button></a>';
        }

        $this->template->body = View::factory('admin/usermanagement/group');
        $this->template->body->users = $users;
        $this->template->body->role = $role;
        $this->template->body->controllers = $controllers;
        $this->template->body->code_pieces = $code_pieces;
        $this->template->body->dashboards = Model_Dashboard::get_user_accessible(FALSE, $id);
    }

    public function action_delete_group()
    {
        if (!Auth::instance()->has_access('role_edit')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $id = $this->request->post('id');
        $roles = new Model_Roles();
        $message = $roles->delete_role($id);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(array('message' => $message));
    }

    public function action_permissions()
    {
        if (!Auth::instance()->has_access('permissions')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            $this->auto_render = false;
            //$this->response->headers('Content-type', 'application/json; charset=utf-8');
            $permissions = json_decode($post['permissions'], true);
            try {
                Database::instance()->begin();
                foreach ($permissions as $_resource_id => $roles) {
                    foreach ($roles as $_role_id => $yes) {
                        $resource_id = str_replace('_', '', $_resource_id);
                        $role_id = str_replace('_', '', $_role_id);
                        DB::delete('engine_role_permissions')
                            ->where('resource_id', '=', $resource_id)
                            ->and_where('role_id', '=', $role_id)
                            ->execute();
                        if ($yes == 1) {
                            DB::insert('engine_role_permissions')
                                ->values(array('resource_id' => $resource_id, 'role_id' => $role_id))
                                ->execute();
                        }
                    }
                }
                Database::instance()->commit();
            } catch (Exception $exc) {
                Database::instance()->rollback();
                throw $exc;
            }

        } else {
            $resources = Model_Resources::search();
            $roles = new Model_Roles();
            $roles = $roles->get_all_roles();
            //header('content-type: text/plain');print_R($resources);exit;
            $this->template->sidebar->breadcrumbs[] = array(
                'name' => 'Permissions',
                'link' => '/admin/usermanagement/Permissions'
            );
            $this->template->sidebar->menus = array(array(
                array('name' => 'Users', 'link' => '/admin/usermanagement/users'),
                array('name' => 'Groups', 'link' => '/admin/usermanagement/groups'),
                array('name' => 'Permissions', 'link' => '/admin/usermanagement/permissions')
            ));

            $this->template->body = View::factory('admin/usermanagement/permissions');
            $this->template->body->alert = IbHelpers::get_messages();
            $this->template->body->resources = $resources;
            $this->template->body->roles = $roles;
            $this->template->sidebar->tools = '<a href="/admin/usermanagement/export_permissions" class="btn btn-default" target="_blank">Export</a> <a href="/admin/usermanagement/import_permissions" class="btn btn-default"">Import</a>';
        }
    }

    public function action_users()
    {
        if (!Auth::instance()->has_access('user_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/users.js"></script>';
        $this->template->body = View::factory('admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Management', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->menus = array(array(
            array('name' => 'Users', 'link' => '/admin/usermanagement/users'),
            array('name' => 'Groups', 'link' => '/admin/usermanagement/groups'),
            array('name' => 'Permissions', 'link' => '/admin/usermanagement/permissions'),
        ));

        if (Auth::instance()->has_access('user_edit')) {
            $this->template->sidebar->tools = '<a href="/admin/usermanagement/invite_user" class="btn btn-default">Create User</a>' .
                ' &nbsp; <a href="/admin/usermanagement/export_users" class="btn btn-default">Export Users</a>';
        }
    }


    public function action_users_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');

        if (!Auth::instance()->has_access('user_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }

        $params = $this->request->query();
        $result = Model_Users::search_datatable($params);
        echo json_encode($result);
    }

    public function action_ajax_invite_user()
    {
        $this->auto_render = FALSE;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $response = ['popup_messages' => []];
        if (!Auth::instance()->has_access('user_edit')) {
            $response['popup_messages'][] = ['message' => 'You do not have access to the user_edit permission', 'success' => 'danger'];
            $this->request->redirect('/admin');
        } else {
            $post = $this->request->post();
            $result = Model_Users::invite($post);
            if (count($result['successful_emails']) > 0) {
                $response['popup_messages'][] =
                    ['message' => 'A user invitation email has been sent to ' . implode(', ' , $result['successful_emails']), 'success' => 'success'];
            } else {
                $response['popup_messages'][] =
                    ['message' => 'No user has been created', 'success' => 'danger'];
            }
        }
        echo json_encode($response);
    }

    public function action_invite_user()
    {
        if (!Auth::instance()->has_access('user_edit')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            $result = Model_Users::invite($post);
            if (count(@$result['ignored_emails'])) {
                IbHelpers::set_message('The following email addresses have been ignored: ' . implode(', ', $result['ignored_emails']), 'warning popup_box');
            }
            if (count($result['successful_emails'])) {
                IbHelpers::set_message('Users have been created for :' . implode(', ' , $result['successful_emails']), 'success popup_box');
            }
            $this->request->redirect('/admin/usermanagement/users');
        }
        if (@$post['action'] == 'resend') {
            Model_Users::invite($post);
            IbHelpers::set_message('Invitation has been sent', 'success popup_box');
            $this->request->redirect('/admin/usermanagement/user/' . $post['resend']);
        }
        $rolesm = new Model_Roles();
        $roles = $rolesm->get_all_roles();
        $this->template->body = View::factory('admin/usermanagement/invite_user');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Management', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Create User', 'link' => '/admin/usermanagement/invite_user');
        $this->template->body->roles = $roles;
    }

    public function action_user()
    {
        if (!Auth::instance()->has_access('user_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }

        $id = $this->request->param('id');
        $u = new Model_Users();

        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            $data = array();
            $data['password'] = '';
            $data['mpassword'] = '';
            $data['name'] = $post['name'];
            $data['surname'] = $post['surname'];
            $data['email'] = $post['email'];
            $data['role_id'] = $post['role_id'];
            $u->update_user_data($id, $data);
            $this->request->redirect('/admin/usermanagement/user/' . $id);
        }

        $user = $u->get_user($id, null);
        $r = new Model_Roles();
        $roles = $r->get_all_roles();
        $role = $r->get_role_data($user['role_id']);
        $user['role'] = $role;

        $this->template->body = View::factory('admin/usermanagement/user');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Management', 'link' => '/admin/usermanagement/groups');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User ' . $user['email'], 'link' => '/admin/usermanagement/user/' . $id);
        $this->template->sidebar->menus = array(
            array()
        );

        $this->template->body->user = $user;
        $this->template->body->roles = $roles;
    }

    public function action_user_delete()
    {
        if (!Auth::instance()->has_access('user_edit')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $id = $this->request->post('id');
        $users = new Model_Users();
        $response = $users->delete_user_data($id);
        $activity = new Model_Activity();
        $activity->set_item_id($id);
        $activity->set_item_type('user');
        $activity->set_action('delete');
        $activity->save();
        ibhelpers::set_message('User has been deactivated', 'info');
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function action_user_undelete()
    {
        if (!Auth::instance()->has_access('user_edit')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $id = $this->request->post('id');
        $users = new Model_Users();
        $response = $users->undelete_user_data($id);
        ibhelpers::set_message('User has been activated', 'info');
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($response);
    }

    public function action_export_users()
    {
        if (!Auth::instance()->has_access('user_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        header('Content-type: text/csv; charset=utf-8');
        header('Content-disposition: attachment; filename="users-' . date::now() . '.csv"');
        $tmp = tmpfile();

        $users = DB::select('*')
            ->from(Model_Users::MAIN_TABLE)
            ->order_by('id', 'asc')
            ->execute()
            ->as_array();
        foreach ($users as $i => $user) {
            if ($i == 0) {
                fputcsv($tmp, array_keys($user));
            }
            fputcsv($tmp, $user);
        }
        fseek($tmp, 0, 0);
        fpassthru($tmp);
        fclose($tmp);
        exit;
    }

    public function action_export_permissions()
    {
        if (!Auth::instance()->has_access('role_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }

        if ($this->request->post('export')) {
            $this->auto_render = false;
            header('Content-type: application/json; charset=utf-8');
            header('Content-disposition: attachment; filename="permissions-' . $_SERVER['HTTP_HOST'] . '-' . date('YmhHis') . '.json"');

            $group_ids = @$this->request->post('group_id');

            $groupsq = DB::select(
                'id',
                'role',
                'description',
                'access_type',
                'master_group',
                'default_dashboard_id',
                'allow_frontend_register',
                'allow_api_register',
                'allow_frontend_login',
                'allow_api_login'
            )
                ->from(Model_Roles::MAIN_TABLE)
                ->where('deleted', '=', 0);

            if ($group_ids) {
                $groupsq->and_where('id', 'in', $group_ids);
            }

            $groups = $groupsq->execute()->as_array();

            foreach ($groups as $i => $group) {
                unset($groups[$i]['id']);
                $groups[$i]['has_permission'] = array();
                $resources = DB::select(
                    array('resources.alias', 'resource')
                )
                    ->from(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'))
                    ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                    ->on('has_permissions.resource_id', '=', 'resources.id')
                    ->where('has_permissions.role_id', '=', $group['id'])
                    ->execute()
                    ->as_array();

                foreach ($resources as $resource) {
                    $groups[$i]['has_permission'][] = $resource['resource'];
                }
            }

            echo json_encode($groups, JSON_PRETTY_PRINT);
            exit;
        }

        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Management', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Export Permissions', 'link' => '/admin/usermanagement/export_permissions');
        $this->template->sidebar->menus = array(array(
            array('name' => 'Users', 'link' => '/admin/usermanagement/users'),
            array('name' => 'Groups', 'link' => '/admin/usermanagement/groups'),
            array('name' => 'Permissions', 'link' => '/admin/usermanagement/permissions'),
        ));

        $this->template->body      = View::factory('admin/usermanagement/export_permissions');
        $this->template->body->groups = DB::select(
            'id',
            'role',
            'description',
            'access_type',
            'master_group',
            'default_dashboard_id',
            'allow_frontend_register',
            'allow_api_register',
            'allow_frontend_login',
            'allow_api_login'
        )
            ->from(Model_Roles::MAIN_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();

        $this->template->styles    = array_merge($this->template->styles, array (
            URL::get_engine_assets_base().'css/list_settings.css' => 'screen',
            URL::get_engine_assets_base().'css/bootstrap-multiselect.css' => 'screen',
            URL::get_engine_assets_base().'css/spectrum.min.css' => 'screen'
        ));

        $this->template->sidebar->tools = '<a href="/admin/usermanagement/export_permissions" class="btn btn-default" target="_blank">Export</a> <a href="/admin/usermanagement/import_permissions" class="btn btn-default"">Import</a>';
    }

    public function action_import_permissions()
    {
        $access = Auth::instance()->has_access('settings_index');
        if( ! $access)
        {
            throw new Kohana_Exception('You do not have access to this resource.', array(), 501);
        }

        if (isset($_FILES['permissions']) && $_FILES['permissions']['error'] == UPLOAD_ERR_OK) {
            $tmp_file = tempnam(Kohana::$cache_dir, 'tmp_permissions');
            move_uploaded_file($_FILES['permissions']['tmp_name'], $tmp_file);
            $json = file_get_contents($tmp_file);
            $groups = json_decode($json, true);

            $db = Database::instance();
            $db->begin();
            try {
                foreach ($groups as $group) {
                    $egroup = DB::select('*')
                        ->from(Model_Roles::MAIN_TABLE)
                        ->where('role', '=', $group['role'])
                        ->execute()
                        ->current();
                    if ($egroup) {
                        $group_id = $egroup['id'];

                        DB::update(Model_Roles::MAIN_TABLE)
                            ->set(array(
                                'allow_api_register' => $group['allow_api_register'],
                                'allow_frontend_register' => $group['allow_frontend_register'],
                                'allow_api_login' => $group['allow_api_login'],
                                'allow_frontend_login' => $group['allow_frontend_login'],
                                'default_dashboard_id' => $group['default_dashboard_id'],
                                'master_group' => $group['master_group'],
                                'access_type' => $group['access_type'],
                                'description' => $group['description']
                            ))
                            ->where('id', '=', $group_id)
                            ->execute();

                        $resource_ids = DB::select(
                            DB::expr($group_id . " as role_id"),
                            array('resources.id', 'resource_id')
                        )
                        ->from(array(Model_Resources::TABLE_RESOURCES, 'resources'))
                        ->where('resources.alias', 'in', $group['has_permission']);


                        DB::delete(Model_Resources::TABLE_HAS_PERMISSION)
                            ->where('role_id', '=', $group_id)
                            ->execute();

                        DB::query(
                            null,
                            "insert into " .
                            Model_Resources::TABLE_HAS_PERMISSION .
                            " (role_id, resource_id) " .
                            " (" . $resource_ids . ")"
                        )->execute();
                    }
                }
                $db->commit();
            } catch (Exception $exc) {
                $db->rollback();
                throw $exc;
            }


            IbHelpers::set_message('Permissions have been imported', 'notice');
            $this->request->redirect('/admin/usermanagement/groups');
            exit;
        }

        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Management', 'link' => '/admin/usermanagement/users');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Import Permissions', 'link' => '/admin/usermanagement/import_permissions');
        $this->template->sidebar->menus = array(array(
            array('name' => 'Users', 'link' => '/admin/usermanagement/users'),
            array('name' => 'Groups', 'link' => '/admin/usermanagement/groups'),
            array('name' => 'Permissions', 'link' => '/admin/usermanagement/permissions'),
        ));
        $this->template->body      = View::factory('admin/usermanagement/import_permissions');
        $this->template->styles    = array_merge($this->template->styles, array (
            URL::get_engine_assets_base().'css/list_settings.css' => 'screen',
            URL::get_engine_assets_base().'css/bootstrap-multiselect.css' => 'screen',
            URL::get_engine_assets_base().'css/spectrum.min.css' => 'screen'
        ));

        $this->template->sidebar->tools = '<a href="/admin/usermanagement/export_permissions" class="btn btn-default" target="_blank">Export</a> <a href="/admin/usermanagement/import_permissions" class="btn btn-default"">Import</a>';
    }

    public function action_verify_user()
    {
        $id = $this->request->post('user_id');
        if (is_numeric($id)) {
            DB::update(Model_Users::MAIN_TABLE)
                ->set(
                    array('email_verified' => 1)
                )->where('id', '=', $id)
                ->execute();
            $this->request->redirect('/admin/usermanagement/user/' . $id);
        } else {
            $this->request->redirect('/admin/usermanagement/users');
        }
    }
}
