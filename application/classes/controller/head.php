<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Head extends Controller_Cms
{
    protected $do_not_check_permissions_for_actions = array();
    function before()
    {
        parent::before();

        $action = $this->request->action();
        $controller = $this->request->controller();
        // Check the permissions and redirect if no permissions are held
        if (!Auth::instance()->has_access('settings') && !in_array($action, $this->do_not_check_permissions_for_actions) && !Session::instance()->get('auth_forced')) {
            if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                $this->response->headers('X-cms-error', 'no permission for ' . $controller . '->' . $action);
                $this->response->status(403);
                return;
            } else {
                IbHelpers::set_message('You do not have access to this feature.', 'error');
                $this->request->redirect('admin/');
            }
        }

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array();

        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home', 'link' => '/admin')
        );
        if (Request::$current->controller() == 'settings' && Request::$current->action() == 'index') {
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Settings', 'link' => '/admin/settings');
        } else {
            $this->template->sidebar->breadcrumbs[] = array('name' => 'System', 'link' => '/admin/settings/activities');
        }

        if (Auth::instance()->has_access('settings_usergroups', false)) {
            $menu_add = array(
                'Companies' => array(
                    array(
                        'name' => 'Manage Companies',
                        'link' => 'admin/settings/manage_usergroups'),
                    array(
                        'name' => 'Add Company',
                        'link' => 'admin/settings/add_usergroup')));

            $this->template->sidebar->menus = array_merge($this->template->sidebar->menus, $menu_add);
        }


        // If NewUserRequest is not setup
        if (Kohana::$config->load('config')->get('allow_user_registration_request')) {
            $this->template->sidebar->menus = array_merge($this->template->sidebar->menus,
                array('New User Requests' => array(
                    array(
                        'name' => 'Manage Requests',
                        'link' => 'admin/settings/list_userreq'
                    )
                )));
        }
    }
}
