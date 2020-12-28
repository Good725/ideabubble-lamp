<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Contacts3 extends Controller_Cms
{
    private $family = null;
    private $contact = null;
    private $linked_to_contact = false;

    function before()
    {
        parent::before();
        if (isset($_REQUEST['dialog'])) {
            $this->template = View::factory('cms_templates/empty/template');
            $this->template->scripts = array();
            $this->template->title = '';
            $this->template->sidebar = View::factory('sidebar');
            $this->template->sidebar->menus = array();
            $this->template->sidebar->breadcrumbs = array();
            $this->template->header = new stdClass();
            $this->template->sidebar->tools = '';
            $this->template->styles = array();
        }

        $google_map_key = Settings::instance()->get('google_map_key');
        if ($google_map_key) {
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.Settings::instance()->get('google_map_key').'&libraries=places&sensor=false"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/maps.js"></script>';
        }

        if (!$this->request->query('old_ui')) {
            $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/bootstrap.daterangepicker.min.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/timetable_view.js' . '"></script>';
        }

        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/js.cookie.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/moment.min.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/jquery.validationEngine2.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/jquery.eventCalendar.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('bookings', 'admin/js/bookings.js', ['cachebust' => true]).'"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('contacts3', 'js/contacts.js', ['cachebust' => true]) .'"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('contacts3', 'js/families.js', ['cachebust' => true]) .'"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/accounts.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('contacts3', 'js/list_contacts.js', ['cachebust' => true]).'"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/documents.js"></script>';
        $this->template->sidebar->breadcrumbs[]   = array('name' => 'Families', 'link' => 'admin/contacts3/families');
        $this->template->styles[URL::get_engine_plugin_assets_base('bookings') .'admin/css/fullcalendar.min.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('bookings') .'admin/css/eventCalendar.css']    = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('bookings') .'admin/css/bookings.css']         = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('contacts3').'css/contacts.css']               = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('contacts3').'css/validation.css']             = 'screen';
    
        $action = $this->request->action();
        
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',     'link' => 'admin'),
            array('name' => 'Contacts', 'link' => 'admin/contacts3')
        );
    
        $types = Model_Contacts3::get_types();
        
        $is_valid_type = false;
        $all_contacts_toolbar = '';
        foreach ($types as $type) {
            if(str_replace(' ', '_', strtolower($type['display_name'])) == strtolower($action)) {
                $is_valid_type = true;
                $this->template->sidebar->tools = "<a href='/admin/contacts3/add_edit_contact/{$type['name']}'><button class='btn' type='button'>New {$type['label']}</button></a>";
                break;
            }
            $all_contacts_toolbar .= "<li><a class='add_contact_btn' href='/admin/contacts3/add_edit_contact/{$type['name']}'>{$type['label']}</a></li>";
        }
        
        // If you are in a valid contact type filter, only show the dropdown toolbar for that one
        if(empty($this->template->sidebar->tools)) {
            $this->template->sidebar->tools =
                '<div class="btn-group">' .
                '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">New Contact <span class="caret"></span></button>' .
                '<ul class="dropdown-menu">' . $all_contacts_toolbar . "</ul></div>";
        }
    
        $this->template->sidebar->menus = array(
            'Contacts' => array(
                array(
                    'icon' => 'department',
                    'name' => 'Department',
                    'link' => '/admin/contacts3/departments'
                )
            )
        );
        
        $this->template->sidebar->tools .=
            '<div class="btn-group" style="margin-left: 5px;">' .
            '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="sr-only">Actions</span>
            <span class="icon-ellipsis-h"></span></button>' .
            '<ul class="dropdown-menu"><li><a href="/admin/contacts3/import_csv" class="btn">CSV Import</a></li></ul></div>';
        
        $class = new ReflectionClass("Controller_Admin_Contacts3");
        if (!$class->hasMethod('action_' . $action) && $is_valid_type) {
            $this->request->query('type', $action);
            $this->request->action("generic_contact_type");
        }
        
        $user       = Auth::instance()->get_user();
        $this->user = Model_Users::get_user($user['id']);
        $contacts   = Model_Contacts3::get_contact_ids_by_user($user['id']);
        if (isset($contacts[0])) {
            $this->contact = new Model_Contacts3($contacts[0]['id']);
            if ($this->contact->get_id() != '') {
                $this->linked_to_contact = true;
            }
        }
    }
    
    public function action_ajax_get_submenu()
    {
        $types = Model_Contacts3::get_types();
        $custom_filters = array("department", "organisation", "host");
        if (Settings::instance()->get('contacts_create_family') == 1) {
            array_splice($custom_filters, 0, 0,  array("family"));
        }
        $return['items'] = [
            array(
                'title' => 'Contacts',
                'link' => '/admin/contacts3',
                'icon_svg' => 'contacts'
            ),
            array(
                'title' => 'Contact types',
                'link' => '/admin/contacts3/types',
                'icon_svg' => 'contacts'
            ),
            array(
                'title' => 'Organisations',
                'link' => '/admin/contacts3/organisations',
                'icon_svg' => 'business'
            )];
        if (Settings::instance()->get('contacts_create_family') == 1) {
            $return['items'][] =  array (
                'title' => 'Hosts',
                'link' => '/admin/contacts3/hosts',
                'icon_svg' => 'provider');
        }

            if (Settings::instance()->get('contacts_create_family') == 1) {
            array_splice ($return['items'], 2, 0, array(array(
                'title' => 'Family groups',
                'link' => '/admin/contacts3/family_groups',
                'icon_svg' => 'family'
            )));
        }
        foreach ($types as $type) {
            if(!in_array($type['name'], $custom_filters) && count(Model_Contacts3::get_contact_type_columns($type['contact_type_id'])) > 0) {
                $url_name = str_replace(' ', '_', strtolower($type['display_name']));
                $return['items'][] = array (
                    'title' => "{$type['display_name']}",
                    'link' => "/admin/contacts3/{$url_name}",
                    'icon_svg' => 'contacts'
                );
            }
        }
        
        return $return;
    }
    
    public function action_index()
    {
        Model_Family::fix_invalid_primary_contacts();
        if (Auth::instance()->has_access('contacts3_limited_view') || Auth::instance()->has_access('contacts3_limited_family_access')) {
            $this->request->redirect('/');
        }

        $alert = IbHelpers::get_messages();
        $uses_student_ids = Model_Contacts3::uses_student_ids();

        $this->template->body = View::factory('admin/list_contacts', compact('alert', 'uses_student_ids'));
    }
    public function action_types() {
        $this->template->sidebar->breadcrumbs[] = array(
            'name' => "Contact types",
            'link' => "/admin/contacts3/types"
        );
        $this->template->sidebar->tools = '<button type="button" class="btn btn-primary contact-type-toggle" id="contact-type-add">Add contact type</button>';
        $this->template->body = View::factory('admin/list_contact_types', array('alert' => IbHelpers::get_messages()));
    }
    
    public function action_types_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        
        $filters = $this->request->query();
        echo json_encode(Model_Contacts3::get_contact_types_datatable($filters), JSON_PRETTY_PRINT);
    }

    public function action_autocomplete_types()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $data = $this->request->query();
        $types = DB::select(array('contact_type_id', 'id'), array('display_name', 'label'))
            ->from(Model_Contacts3::CONTACTS_TYPE_TABLE)
            ->where('display_name', 'like', '%' . $data['term'] . '%')
            ->and_where('deletable', '=', 0)
            ->order_by('display_name', 'asc')
            ->execute()
            ->as_array();
        $this->response->body(json_encode($types));
    }
    
    public function action_ajax_save_contact_type()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        try {
            Database::instance()->begin();
            $post = $this->request->post();
            $contact_type['id'] = $post['id'] ?? null;
            if(isset($post['name'])) {
                $contact_type['name'] = str_replace(' ', '_', strtolower($post['name']));
                $contact_type['display_name'] = $post['name'];
                $contact_type['label'] = $post['name'];
                $contact_type['deletable'] = 1;
            }
            $contact_type['publish'] = $post['publish'] ?? 1;
            if(!empty($post['name']) && Model_Contacts3::find_type($post['name']) !== null) {
                $return['success'] = false;
                $return['message'] = 'false';
                $return['messages'] = [
                    [
                        'success' => false,
                        'message' => 'A contact type already exists that is called: ' . $post['name']
                    ]
                ];
                echo json_encode($return, JSON_PRETTY_PRINT);
                exit;
            }
            Model_Contacts3::save_contact_type($contact_type);
            $return['success'] = true;
            $return['message'] = 'success';
            if(!empty($contact_type['id'])) {
                $return['messages'][] = ['success' => true, 'message' => 'Contact type updated successfully'];
            } else {
                $return['messages'][] = ['success' => true, 'message' => 'Contact type created successfully'];
            }
            
            Database::instance()->commit();
        } catch (Exception $e) {
            Database::instance()->rollback();
            Log::instance()->add(Log::ERROR,
                "Error saving contact type.\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            $return['success'] = false;
            $return['message'] = 'false';
            $return['messages'] = [
                [
                    'success' => false,
                    'message' => 'Error saving contact type. If this problem continues, please ask an administrator to check the error logs.'
                ]
            ];
        }
        echo json_encode($return, JSON_PRETTY_PRINT);
    }
    
    public function action_ajax_delete_contact_type()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        try {
            Database::instance()->begin();
            $contact_type_id = $this->request->post('contact_type_id');
            $family_contact_type = Model_Contacts3::find_type('Family');
            $where_clauses[] = array("type.contact_type_id", '=', $contact_type_id);
            $contacts = Model_Contacts3::get_all_contacts($where_clauses);
            foreach($contacts as $contact) {
                $contact_model = new Model_Contacts3($contact['id']);
                $contact_model->set_type($family_contact_type['contact_type_id']);
                $contact_model->save();
            }
            $contact_amount = count($contacts);
            Model_Contacts3::delete_contact_type($contact_type_id);
            $return['success'] = true;
            $return['message'] = 'true';
            $return['messages'] = [
                [
                    'success' => true,
                    'message' => "Successfully delete contact type. $contact_amount contacts were moved to the Family contact type."
                ]
            ];
            Database::instance()->commit();
        } catch
        (Exception $e) {
            Database::instance()->rollback();
            Log::instance()->add(Log::ERROR,
                "Error saving contact type.\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            $return['success'] = false;
            $return['message'] = 'false';
            $return['messages'] = [
                [
                    'success' => false,
                    'message' => 'Error deleting contact type. If this problem continues, please ask an administrator to check the error logs.'
                ]
            ];
        }
        echo json_encode($return, JSON_PRETTY_PRINT);
    }
    
    public function action_ajax_get_contact_type()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $contact_type_id = $this->request->param('id');
        $contact_type = Model_Contacts3::get_contact_type($contact_type_id);
        if(!isset($contact_type)) {
            $contact_type['id'] = '';
            $contact_type['display_name'] = '';
        }
        echo json_encode($contact_type, JSON_PRETTY_PRINT);
    }
    
    
    public function action_generic_contact_type() {
        $type_name = $this->request->query('type');
        $action = $this->request->action();
        $type_name = str_replace('_', ' ', $type_name);
        $type = Model_Contacts3::find_type($type_name);
        if (strtolower($action) !== "index") {
            $this->template->sidebar->breadcrumbs[] = array(
                'name' => $type['display_name'],
                'link' => "admin/contacts3/{$type['name']}"
            );
        }
        if(!isset($type)) {
            IbHelpers::set_message("Select a valid contact type.",
                'warning popup_box');
            $this->request->redirect('/admin/contacts3/');
        }
        $table_columns = Model_Contacts3::get_contact_type_columns($type['contact_type_id']);
        $this->template->body = View::factory('admin/list_contact_type_generic', array('alert' => IbHelpers::get_messages()))
            ->bind('table_columns', $table_columns);
    }

    public function action_generic_contact_type_datatable() {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->post();
        echo json_encode(Model_Contacts3::get_generic_contact_type_datatable($filters), JSON_PRETTY_PRINT);
    }
    
    public function action_settings()
    {
        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            Model_Contacts3::save_settings($post);
        }
        $settings = Model_Contacts3::load_settings();
        $this->template->body = View::factory('admin/settings');
        $this->template->body->preferences = Model_Preferences::get_all_preferences();
        $this->template->body->notification_types = Model_Contacts3::get_notification_types();
        $this->template->body->course_types = Model_Categories::get_categories_without_parent();
        $this->template->body->subjects = Model_Subjects::get_all_subjects();
        $this->template->body->family = Model_Family::instance($this->family);
        $this->template->body->schools = Model_Providers::get_all_schools();
        $this->template->body->years = Model_Years::get_years_where(array(array('publish', '=', 1)));
        $this->template->body->roles = Model_Contacts3::get_all_roles();
        $this->template->body->settings = $settings;
    }

    public function action_add_edit_contact()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $id = $this->request->param('id');
        $isDialog = $this->request->query('dialog') == 'yes';
        $contact = new Model_Contacts3();
        if((is_string($id) AND !is_numeric($id)) OR empty($id))
        {
            $type = Model_Contacts3::get_contact_type_by_name($id);
            $contact->set_type($type['contact_type_id']);
            $teacher = FALSE;
            // Need to make contact type label to sentance case
            $sentance_case_label = "Add contact " . strtolower($type['label']);
        }
        elseif(is_numeric($id))
        {
            $contact->set_id($id);
            $contact->get(true);
            $teacher = $contact->get_is_teacher();
            $type = Model_Contacts3::get_contact_type_by_name($contact->get_type());
            $sentance_case_label = "Edit contact " . strtolower($type['label']);
        }
        $this->template->sidebar->breadcrumbs[] = array(
            'name' => "{$sentance_case_label}",
            'link' => "admin/contacts3/add_edit_contact/{$id}"
        );
        $display_invite_button = false;
        $invitation = Model_Contacts3::invite_check_contact_id($contact->get_id());
        if ($contact->get_id() == null) {
            $display_invite_button = true;
        }
        if ($contact->get_linked_user_id() == 0 && $invitation == null) {
            $display_invite_button = true;
        }
        if ($contact->get_linked_user_id()) {
            $linked_user = Model_Users::get_user($contact->get_linked_user_id());
            if ($linked_user['email_verified'] == 0 && $invitation == null) {
                $display_invite_button = true;
            }
        }
		$messages = null;
		if(class_exists('Model_Messaging')){
            $emails = DB::select('has.value')
                ->from(array('plugin_contacts3_contact_has_notifications','has'))
                ->join(array('plugin_contacts3_notifications','n'))
                ->on('has.notification_id', '=', 'n.id')
                ->where('n.stub','=','email')
                ->where('has.contact_id','=',$contact->get_id())
                ->execute()
                ->as_array();
            if ($emails) {
                $messaging = new Model_Messaging();
                $messages = $messaging->search_messages(array('target' => $contact->get_id(), 'target_type' => 'CMS_CONTACT3'));
            }
            else {
                $messages = '' ;
            }
		}

        $this->template->styles[URL::get_engine_plugin_assets_base('contacts3').'css/contacts.css']   = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('contacts3').'css/validation.css'] = 'screen';
        $academic_years = Model_AcademicYear::get_academic_years_options(TRUE);
        $this->family = (! is_null($this->family)) ? $this->family:$contact->get_family_id();
        $this->template->body                       = View::factory('admin/add_edit_contact',array('academic_years'=>$academic_years));
        $this->template->body->all_roles            = Model_Roles::get_all_roles();
        $this->template->body->contact_types        = Model_Contacts3::get_types();
        $this->template->body->contact_subtypes = Model_Contacts3::get_subtypes(true);
        $this->template->body->contact            = $contact;
        $this->template->body->organisation_sizes = Model_Organisation::get_organisation_sizes();
        $this->template->body->organisation_industries   = Model_Organisation::get_organisation_industries();
        $this->template->body->organisation       = ($contact->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id'])
            ? Model_Organisation::get_org_by_contact_id($contact->get_id()) : null;
        $this->template->body->job_functions      = Model_Contacts3::get_job_functions();
        $this->template->body->notifications      = $contact->get_contact_notifications();
        $this->template->body->residence          = new Model_Residence($contact->get_residence());
        $this->template->body->billing_residence  = $contact->get_billing_address();
        $this->template->body->preferences        = Model_Preferences::get_all_preferences();
        $this->template->body->notification_types = Model_Contacts3::get_notification_types();
        $this->template->body->course_types       = Model_Categories::get_categories_without_parent();
        $this->template->body->subjects           = Model_Subjects::get_all_subjects();
        $this->template->body->family             = Model_Family::instance($this->family);
        $this->template->body->schools            = Model_Providers::get_all_schools();
        $this->template->body->years              = Model_Years::get_years_where(array(array('publish', '=', 1)));
		$this->template->body->messages           = $messages;
        $this->template->body->subject_preferences_ids = $contact->get_subject_preferences_ids();
        $this->template->body->levels             = Model_Levels::get_all_levels();
        $this->template->body->subject_preferences= $contact->get_subject_preferences();
        $this->template->body->display_invite_button = $display_invite_button;
        $this->template->body->invitation = $invitation;
        $this->family = null;
        $courses_subjects = array();
        if($teacher)
        {
            $categories                           = $contact['course_type_preferences'];
            $subjects                             = $contact['subject_preferences'];

            $courses_subjects   = Model_Courses::get_contact_available_courses($categories,$subjects);
        }
        if ( ! $courses_subjects)
        {
            $courses_subjects   = Model_Courses::get_all_published();
        }
        $this->template->body->courses_subjects = $courses_subjects;
        $this->template->body->isDialog           = $isDialog;
        $this->template->body->timeoff_config = array();
    }

    public function action_save_contact()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $post = $this->request->post();
        $result = $this->save_contact($post);
        if ($result['id']) {
            $redirect = ($post['action'] == 'save' || $post['action'] == 'save_and_add') ? '?contact=' . $result['id'] : '';
            $redirect .= ($post['action'] == 'save_and_add') ? '&add_new=yes' : '';
            $this->request->redirect('/admin/contacts3/' . $redirect);
        } else {
            $this->request->redirect('/admin/contacts3/');
        }
    }

    public function action_hosts()
    {
        $this->template->sidebar->breadcrumbs[] = array(
            'name' => 'Hosts',
            'link' => 'admin/contacts3/hosts'
        );
        $this->template->body = View::factory('admin/list_hosts', array('alert' => IbHelpers::get_messages()));
    }

    public function action_hosts_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $filters = $this->request->query();
        echo json_encode(Model_Host::get_datatable($filters), JSON_PRETTY_PRINT);
    }
    
    public function action_delete_contact()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $id      = $this->request->param('id');
        $contact = new Model_Contacts3($id);
        if ($contact->delete())
        {
            IbHelpers::set_message('Contact #'.$contact->get_id().': '.$contact->get_first_name().' '.$contact->get_last_name().' deleted', 'success popup_box');
        }
        else
        {
            IbHelpers::set_message('Failed to delete contact', 'error popup_box');
        }

        $this->request->redirect('/admin/contacts3/');
    }

    public function action_family_groups()
    {
        $this->template->sidebar->breadcrumbs[] = array(
            'name' => 'Family groups',
            'link' => 'admin/contacts3/family_groups'
        );
        Model_Family::fix_invalid_primary_contacts();
        $this->template->body                   = View::factory('admin/list_families');
    }

    public function action_departments()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Departments', 'link' => 'admin/contacts3/departments');
        Model_Family::fix_invalid_primary_contacts();
        $this->template->body                   = View::factory('admin/list_departments');
    }

    public function action_organisations()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Organisation', 'link' => 'admin/contacts3/organisations');
        Model_Family::fix_invalid_primary_contacts();
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/list_organisations.js"></script>';
        $this->template->body                   = View::factory('admin/list_organisations');
    }

    public function action_add_edit_family()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $family_id                                = $this->request->param('id');
        $family                                   = new Model_Family($family_id);
        $nonchildren                              = ($family_id == '') ? '' : $family->get_nonchildren();
        $this->template->styles[URL::get_engine_plugin_assets_base('contacts3').'css/contacts.css']   = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('contacts3').'css/validation.css'] = 'screen';
        $this->template->body                     = View::factory('/admin/add_edit_family');
        $this->template->body->family             = $family;
        $this->template->body->nonchildren            = $nonchildren;
        $this->template->body->residence          = new Model_Residence($family->get_residence());
        $this->template->body->notifications      = $family->get_contact_notifications();
        $this->template->body->notification_types = Model_Contacts3::get_notification_types();
    }

    public function action_save_family()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $post     = $this->request->post();
        $family   = new Model_Family();
        $family->load($post);
        $family->address->load($post);
        $saved    = $family->save();
        IbHelpers::set_message(($saved) ? 'Family saved' : 'Failed to save family', ($saved) ? 'success popup_box' : 'error popup_box');
        $redirect = ($post['action'] == 'save') ? 'add_edit_family/'.$family->get_id() : 'families';
        $this->request->redirect('/admin/contacts3/'.$redirect);
    }

    /**
     * Use to delete a family and remove the family association from the contacts
     */
    public function action_delete_family()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $id      = $this->request->param('id');
        $family = new Model_Family($id);
        if ($family->delete())
        {
            // Remove the family id when deleting a family
            $res = $family->remove_family_id($id) ;
            if ($res)
            {
                IbHelpers::set_message('Family (and association) #'.$family->get_id().': '.$family->get_family_name().' deleted', 'success popup_box');
            }
            else{
                IbHelpers::set_message('Family #'.$family->get_id().': '.$family->get_family_name().' deleted but association not removed', 'success popup_box');
            }
        }
        else
        {
            IbHelpers::set_message('Failed to delete family', 'error popup_box');
        }

        $this->request->redirect('/admin/contacts3/families');
    }

	// Return datatable results
	public function action_ajax_get_datatable()
	{
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $filters = $this->request->query();
        if (Auth::instance()->has_access('contacts3_limited_view') || Auth::instance()->has_access('contacts3_limited_family_access')) {
            $filters['check_permission_user_id'] = $this->user['id'];
	}
		$this->response->body(Model_Contacts3::get_for_datatable($filters));
	}
    
    // Return datatable results
    public function action_ajax_get_organisation_datatable()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        
        $filters = $this->request->query();
        if (Auth::instance()->has_access('contacts3_limited_view')) {
            $filters['check_permission_user_id'] = $this->user['id'];
        }
        $this->response->body(Model_Contacts3::get_for_organisation_datatable($filters));
    }

    public function action_ajax_get_external_organisations() {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->post();
        $api_turned_on = Settings::instance()->get('organisation_integration_api');
        if (!$api_turned_on) {
            $this->response->body(json_encode(array()));
            return true;
        }
        $cds_api = new Model_CDSAPI();

        if (isset($filters['external_api_organisation_id']) && !empty($filters['external_api_organisation_id'])) {
            if (is_numeric($filters['external_api_organisation_id'])) {
                $organisations = $cds_api->get_account($filters['external_api_organisation_id']);
            } else {
                $organisations = $cds_api->get_account_by_remote_id($filters['external_api_organisation_id']);
            }
            $organisations = array($organisations);
            //check if organization is present in CDS (IBEC Member = true)
        } elseif (!empty($filters['domain_name'])) {
            if (isset($filters['organisation_id']) && !empty($filters['organisation_id'])) {
                $organisation = new Model_Contacts3($filters['organisation_id']);
                if ($organisation->get_is_public_domain()) {
                    $organisations = $cds_api->search_accounts('name', $organisation->get_first_name(), true, false);
                } else {
                    $organisations = $cds_api->search_accounts('sp_homepage', 'www.' . $filters['domain_name'], true);
                }
            } else {
                $organisations = $cds_api->search_accounts('sp_homepage', 'www.' . $filters['domain_name'], true);
            }
        } elseif (!empty($filters['external_api_organisation_name'])) {
            $organisations = $cds_api->search_accounts('name', $filters['external_api_organisation_name']);
        } else {
            $organisations = array();
        }
        $organisations_datatables = array();
        if (!empty($organisations)) {
            if ($api_turned_on) {
                foreach ($organisations as $organisation) {
                    $org_data = array();
                    $org_data['id'] = $organisation['accountid'];
                    $org_data['synced'] = !empty($organisation['synced_value']);
                    $org_data['name'] = $organisation['name'];
                    $address = '';
                    $address .= $organisation['address1_line1'];
                    $org_data['address1'] = @$organisation['address1_line1'];
                    if (!empty($organisation['address1_line2'])) {
                        $org_data['address2'] = @$organisation['address1_line2'];
                        $address .= ',' . $organisation['address1_line2'];
                    }
                    if (!empty($organisation['address1_line3'])) {
                        $org_data['address3'] = @$organisation['address1_line3'];
                        $address .= ',' . $organisation['address1_line3'];
                    }
                    if (!empty($organisation['address1_city']))  {
                        $org_data['city'] = @$organisation['address1_city'];
                        $address .= ',' . $organisation['address1_city'];
                    }
                    if (!empty($organisation['address1_county']))  {
                        $address .= ',' . $organisation['address1_county'];
                    }
                    if (!empty($organisation['address1_country']))  {
                        $address .= ',' . $organisation['address1_country'];
                    }
                    $org_data['county'] = '';
                    if (!empty($organisation['sp_countycode'])) {
                        $county = Model_Cities::get_counties($organisation['sp_countycode'], 'code');
                        if(!empty($county)) {
                            $org_data['county'] = $county[0]['id'];
                        }

                    }
                    $org_data['country'] = '';
                    if (!empty($organisation['sp_countrycode'])) {
                        $countries = Model_Country::get_countries(3);
                        if(!empty($countries)) {
                            $org_data['country'] = array_key_exists($organisation['sp_countrycode'], $countries) ? $countries[$organisation['sp_countrycode']]['id'] : '';
                        }
                    }
                    $org_data['is_member'] = @$organisation['sp_membershipstatus'];
                    $org_data['postcode'] = @$organisation['address1_postalcode'];
                    $org_data['address'] = $address;
                    $organisations_datatables[] = $org_data;
                }
            }

        }
        $this->response->body(json_encode($organisations_datatables));
    }

    public function action_ajax_get_external_organisation(){
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->post();
        $api_turned_on = Settings::instance()->get('organisation_integration_api');
        if (!$api_turned_on) {
            $this->response->body(json_encode(array()));
            return true;
        }
    }

    public function action_ajax_get_linked_organisation() {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->post();
        $api_turned_on = Settings::instance()->get('organisation_integration_api');

        if (!$api_turned_on) {
            $this->response->body(json_encode(array()));
            return true;
        }
        $rs = new Model_Remotesync();
        if (isset($filters['contact_id'])) {
            $synced_account = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $filters['contact_id']);
        } else {
            $synced_account = array();
        }
        $this->response->body(json_encode($synced_account));
    }
    
    public function action_ajax_get_organisation_members_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->query();
        $auth_user = Auth::instance()->get_user() ?? '0';
        $auth_contact = new Model_Contacts3(Model_Contacts3::get_linked_contact_to_user($auth_user['id'])['id']);
        $contact_relations = $auth_contact->get_contact_relations();
        $can_view_org_members = false;
        // You can only view org members if you are linked to the org and you are an org rep, a permission would be better than this hardcode
        if(Model_Contacts3::get_contact_type($auth_contact->get_type())['name'] == 'org_rep') {
            foreach ($contact_relations as $contact_relation) {
                if (Model_Contacts3::get_contact_type(Model_Contacts3::instance($contact_relation['parent_id'])->get_type())['name'] == 'organisation') {
                    $can_view_org_members = true;
                    $filters['org_contact_id'] = $contact_relation['parent_id'];
                    break;
                }
            }
        }
        if ($can_view_org_members) {
            $this->response->body(Model_Contacts3::get_for_organisation_members_datatable($filters));
        } else {
            return false;
        }
    }
    // Return datatable results
    public function action_ajax_get_department_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        
        $filters = $this->request->query();
        $this->response->body(Model_Contacts3::get_for_department_datatable($filters));
    }
    
	// Return datatable results
	public function action_ajax_get_family_datatable()
	{
		$this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->query();
        if (Auth::instance()->has_access('contacts3_limited_view') || Auth::instance()->has_access('contacts3_limited_family_access')) {
            $filters['check_permission_user_id'] = $this->user['id'];
	}
        $this->response->body(Model_Family::get_for_datatable($filters));
	}

    /**
     * Used to display family details under the list of contacts, when a contact is clicked
     */
    public function action_ajax_display_family_details()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render    = FALSE;
        $family_id            = $this->request->param('id');
        if (is_null($family_id))
        {
            $contact_id = $this->request->post('contact_id');
            $contact    = new Model_Contacts3($contact_id);
            $family_id  = $contact->get_family_id();
        }
        else
        {
            $contact_id = NULL;
            $contact    = new Model_Contacts3();
        }
        $family     = new Model_Family($family_id);
        $nonchildren  = $family->get_nonchildren();
        $contact_type = Model_Contacts3::get_contact_type($contact->get_type());
        // Departments/organisations do not have families, they have members
        if ($contact->get_type() !== null && ($contact_type['name'] == 'department' || $contact_type['name'] == 'organisation')) {
            $members = Model_Contacts3::get_contact_relations_child_details($contact_id);
        } else {
            $members = ((is_null($family_id))
                ? Model_Contacts3::get_all_contacts(array(array('contact.id',        '=', $contact_id)))
                : Model_Contacts3::get_all_contacts(array(array('contact.family_id', '=', $family_id)))
            );
        }
        $view   = View::factory('/admin/list_contacts_details')->set(array(
            'contact'            => $contact,
            'host' => Model_Host::get_by_contact_id($contact_id),
            'family'             => $family,
            'nonchildren'            => $nonchildren,
            'notifications'      => $family->get_contact_notifications(),
            'family_members'     => $members,
            'residence'          => new Model_Residence($family->get_residence()),
            'notification_types' => Model_Contacts3::get_notification_types()
        ));
        $this->response->body($view);
    }

    public function action_ajax_set_family_primary_contact()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render    = FALSE;
        $contact_id           = $this->request->post('contact_id');
        $family_id            = $this->request->post('family_id');
        $contact    = new Model_Contacts3($contact_id);
        $answer = $contact->set_family_primary_contact($family_id,$contact_id);
        if ($answer)
        {
            $family = new Model_Family($answer);
            $residence = new Model_Residence($family->get_residence());
            $notifications = $family->get_contact_notifications();
            $results = array('status'=>'success','message'=>'','residence'=>$residence,'notifications'=>$notifications);
        }
        else
        {
            $results = array('status'=>'error','message'=>'An error happened trying to reset the primary contact');
        }
        exit(json_encode($results));
    }

    /**
     * Used to display contact details under the list of contacts, when a family member is clicked
     */
    public function action_ajax_display_contact_details()
    {
        $this->auto_render    = FALSE;
        $contact_id           = $this->request->post('contact_id');
        $family_id            = $this->request->post('family_id');

        $auth = Auth::instance();
        if (!$auth->has_access('contacts3_view')) {
            $auth_ok = false;
            //check if there is limited access(self view or family member view)
            if ($contact_id == $auth->get_contact3()->get_id()) {
                if (Auth::instance()->has_access('contacts3_limited_view')) {
                    $auth_ok = true;
                }
            } else {
                if (Auth::instance()->has_access('contacts3_limited_family_access')) {
                    $fmembers = Model_Contacts3::get_contact_ids_by_user($auth->get_user()['id']);
                    foreach ($fmembers as $fmember) {
                        if ($fmember['id'] == $contact_id) {
                            $auth_ok = true;
                            break;
                        }
                    }
                }
            }
            if (!$auth_ok) {
                $error_id = Model_Errorlog::save(null, 'SECURITY');
                IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                $this->request->redirect('/admin');
            }
        }

        if ($contact_id) {
            Model_KES_Bookings::all_set_inprogress_completed($contact_id);
        }
        $contact              = new Model_Contacts3($contact_id);

        $accountsiq_id = null;
        if (Model_Plugin::is_enabled_for_role('Administrator', 'remoteaccounting'))
        if (Settings::instance()->get('remoteaccounting_api') == Model_Accountsiq::API_NAME) {
            $rs = new Model_Remotesync();
            $accountsiq_synced = $rs->get_object_synced('AccountsIQ-Contact', $contact_id, 'cms');
            if ($accountsiq_synced) {
                $accountsiq_id = $accountsiq_synced['remote_id'];
            }
        }
        $display_invite_button = false;
        $invitation = Model_Contacts3::invite_check_contact_id($contact->get_id());
        if ($contact->get_id() == null) {
            $display_invite_button = true;
        }
        if ($contact->get_linked_user_id() == 0) {
            $display_invite_button = true;
        }
        if ($contact->get_linked_user_id()) {
            $linked_user = Model_Users::get_user($contact->get_linked_user_id());
            if ($linked_user['email_verified'] == 0 && $invitation == null) {
                $display_invite_button = true;
            }
        }
        $family_id            = $contact->get_family_id() ?? $family_id ?? null;
        $family               = new Model_Family($family_id);
        $teacher = $contact->get_is_teacher();
        $courses = array();
        if($teacher)
        {
            $categories = $subjects       = array();
            foreach ($contact->get_course_type_preferences() as $preference) $categories[] = $preference['course_type_id'];
            foreach ($contact->get_subject_preferences()     as $preference) $subjects[]     = $preference['subject_id'];
            if (empty($categories) OR empty($subjects))
            {
                $courses        = Model_Courses::get_all_published();
            }
            else
            {
                $courses = Model_Courses::get_contact_available_courses($categories, $subjects);
            }
        }
        if (empty($courses))
        {
            $courses        = Model_Courses::get_all_published();
        }
        $timeoff_config = DB::select('*')
            ->from('plugin_timeoff_config')
            ->where('item_id', '=', $contact->get_id())
            ->execute()
            ->as_array();
        foreach ($timeoff_config as $tc) {
            $timeoff_config[$tc['name']] = $tc;
        }

        $booked_locations = Model_KES_Bookings::get_booking_locations($contact_id);
        $contact_phone_number = Model_Contacts3::get_contact_phone_number($contact_id);

        if ($contact_id) {
            $family_balance = ORM::factory('Kes_Transaction')->get_contact_balance_label(null, $contact->get_family_id());
            $contact_balance = ORM::factory('Kes_Transaction')->get_contact_balance_label($contact_id, null);
            $tx_balances = array(
                'contact_balance'   => $contact_balance,
                'family_balance'    => $family_balance
            );
        } else {
            $tx_balances = array(
                'contact_balance'   => 0,
                'family_balance'    => 0
            );
        }
        $rolesm = new Model_Roles();
        $view                 = View::factory('/admin/add_edit_contact')->set(array(
            'contact_types'      => Model_Contacts3::get_types(),
            'contact_subtypes' => Model_Contacts3::get_subtypes(true),
            'all_roles'          => $rolesm->get_all_roles(),
            'contact'            => $contact,
            'organisation_sizes' => Model_Organisation::get_organisation_sizes(),
            'organisation_industries' => Model_Organisation::get_organisation_industries(),
            'organisation'       => ($contact->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id']) ? Model_Organisation::get_org_by_contact_id($contact->get_id()) : null,
            'job_functions'      => Model_Contacts3::get_job_functions(),
            'host' => Model_Host::get_by_contact_id($contact_id),
            'login_details'      => Model_Contacts3::get_user_by_contact_id($contact->get_id()),
            'notifications'      => $contact->get_contact_notifications(),
            'family'             => $family,
            'residence'          => new Model_Residence($contact->get_residence() ?? $family->get_residence()),
            'billing_residence' =>  $contact->get_billing_address(),
            'preferences'        => Model_Preferences::get_all_preferences(),
            'notification_types' => Model_Contacts3::get_notification_types(),
            'accountsiq_id'      => $accountsiq_id,
            'course_types'       => Model_Categories::get_categories_without_parent(),
            'subjects'           => Model_Subjects::get_all_subjects(),
            'courses_subjects'   => $courses,
            'academic_years'     => Model_AcademicYear::get_academic_years_options(TRUE),
            'schools'            => Model_Providers::get_all_schools(),
            'years'              => Model_Years::get_years_where(array(array('publish', '=', 1))),
            'subject_preferences_ids'  => $contact->get_subject_preferences_ids(),
            'levels'             => Model_Levels::get_all_levels(),
            'subject_preferences'  => $contact->get_subject_preferences(),
            'timeoff_config'    => $timeoff_config,
            'display_invite_button' => $display_invite_button,
            'booked_locations' => $booked_locations,
            'contact_phone_number' => $contact_phone_number,
            'tx_balances' => $tx_balances,
            'invitation' => $invitation
        ));
        $this->response->body($view);
    }

    protected function save_contact($post)
    {
        // If subtype doesn't exist, give it a a family one
        $post['subtype_id'] = $post['subtype_id'] ?? 1;
        if ( ! array_key_exists('staff_member',$post))
        {
            $post['staff_member'] = 0 ;
        }
        if ($post['new_family'] == 1 AND $post['family_id'] == '')
        {
            $family            = new Model_Family();
            $family->load(array('family_name' => @$post['last_name'] ?: $post['first_name']));
            $family->address->load($post);
            $family_saved      = $family->save();
            IbHelpers::set_message(($family_saved) ? 'Family saved' : 'Failed to save family', ($family_saved) ? 'success popup_box' : 'error popup_box');
            $result['family']  = $family->get_instance();
            $post['family_id'] = $family->get_id();
            $post['address_id'] = $family->get_residence();
        }
        else
        {
            $family            = new Model_Family($post['family_id']);
            $result['family']  = $family->get_instance();
            if ($post['contact_family'] != $family->get_family_name()) {
                $family->set_family_name($post['contact_family']);
                $family->save();
            }
        }

        if (isset($post['school_id']) AND ! is_numeric($post['school_id']))
        {
            $post['school_id'] = '';
        }
        
        if (!isset($post['last_name'])) {
            //$post['last_name'] = ' ';
        }
        $contact_relations = array();
        if (@$post['linked_organisation_id']) {
            $contact_relations[] = array(
                'parent_id' => $post['linked_organisation_id'],
                'position' => 'organisation'
            );
        }
        if (@$post['linked_department_id']) {
            $contact_relations[] = array(
                'parent_id' => $post['linked_department_id'],
                'role' => $post['linked_department_role'],
                'position' => 'department'
            );
        }

        $type = new Model_Contacts3_Type($post['type'] ?? '');

        // If an org rep is created with no role, assume "org_rep" is the role.
        if (empty($post['role_id']) && $type->name == 'org_rep') {
            $post['role_id'] = [ORM::factory('Contacts3_Role')->where('stub', '=', 'org_rep')->find_undeleted()->id];
        }

        if(isset($post['role_id'])){
            $post['roles'] = $post['role_id'];
            unset($post['role_id']);
        }
        $notifications = [];
        $contact_details = $post['contactdetail_value'];
        foreach ($post['contactdetail_value'] as $key => $contactdetail) {
            if ((!isset($contactdetail['mobile'])
                && !isset($contactdetail['landline'])
                && !isset($contactdetail['value']))
                    || (empty($contactdetail['value'])
                        && empty($contactdetail['mobile'])
                        &&  empty($contactdetail['landline'])) ) {
                //if no mobile, phone or other value - skip
                continue;
            }
            // Don't add the notification if is is an empty value
            //Process phones to save correctly
            if (is_array($contactdetail)) {

                if (isset($contactdetail['country_dial_code_landline'])) {
                    $notifications[] = [
                        'id' => strpos($key, 'new') === false ? $key : null,
                        'notification_id' => 3,
                        'country_dial_code' => $contactdetail['country_dial_code_landline'],
                        'dial_code' => $contactdetail['dial_code_landline'],
                        'value' => $contactdetail['landline']
                    ];
                } elseif(isset($contactdetail['country_dial_code_mobile'])) {
                    $notifications[] = [
                        'id' => strpos($key, 'new') === false ? $key : null,
                        'notification_id' => 2,
                        'country_dial_code' => $contactdetail['country_dial_code_mobile'],
                        'dial_code' => $contactdetail['dial_code_mobile'],
                        'value' => $contactdetail['mobile']
                    ];
                } else {
                    $notifications[] = [
                        'id' => strpos($key, 'new') === false ? $key : null,
                        'notification_id' => $contactdetail['notification_id'],
                        'value' => $contactdetail['value']
                    ];
                }
            } else {
                if(!empty($contactdetail)) {
                    $notifications[] = [
                        'id' => strpos($key, 'new') === false ? $key : null,
                        'notification_id' => $key,
                        'value' => $contactdetail
                    ];
                }
            }

        }
        unset($post['contactdetail_type_id']);
        unset($post['contactdetail_value']);
        $user = Auth::instance()->get_user();
        if (!is_numeric($post['id'])) {
            $post['created_by'] = $user['id'];
        }
        $post['modified_by'] = $user['id'];
        $contact = new Model_Contacts3($post['id']);
        $contact->trigger_save = true;
        $contact->set_notifications_group_id(null);
        $contact->set_contact_relations($contact_relations);

        if(!empty(Settings::instance()->get('contact_default_preferences')) && isset($post['preferences'])) {
            $post['preferences'] = array_unique(array_merge($post['preferences'], Settings::instance()->get('contact_default_preferences')));
        } elseif(!isset($post['preferences'])) {
            $post['preferences'] = array();
        }
        $contact->load($post);
        $rs = new Model_Remotesync();
        if ($contact->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id']) {
            $cds = new Model_CDSAPI();
            $api_turned_on = Settings::instance()->get('organisation_integration_api');
            $cds_admin_allowed = Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi');
            if ($api_turned_on && !empty($post['external_api_account'])) {
                $current_remote_sync = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['external_api_account'], 'remote');
                if ($current_remote_sync && is_numeric($post['id']) && $post['id'] != $current_remote_sync['cms_id']) {
                    IbHelpers::set_message('Incorrect external API id',  'error popup_box');
                    return false;
                }
            } elseif($api_turned_on && empty($post['external_api_account'])) {
                $current_remote_sync = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['id'], 'cms');
                if (!empty($current_remote_sync)) {
                    $rs->delete_object_synced(Model_CDSAPI::API_NAME . '-Account',$post['id'], 'cms' );
                }
            }
            $is_member = false;
            if (Settings::instance()->get('organisation_api_control_membership') && $api_turned_on && $cds_admin_allowed) {
                if (isset($post['external_api_account']) && !empty($post['external_api_account'])) {
                    $cds_account = is_numeric($post['external_api_account']) ? $cds->get_account($post['external_api_account']) : $cds->get_account_by_remote_id($post['external_api_account']);
                    $is_member = (bool) @$cds_account['sp_membershipstatus'];
                    $contact->set_is_special_member($is_member);
                } else {
                    $contact->set_is_special_member(false);
                }
            } else {
                if (isset($post['special_member'])) {
                    $contact->set_is_special_member(@$post['special_member']);
                    $is_member = (bool)@$post['special_member'];
                }
            }
        }

        $contact->set_tags($post['contact_tags'] ?? []);
        $contact->load(array('notifications' => $notifications));
        $contact->address->load($post);
        $contact->billing_address->load($post['billing_address']);
        $contact_saved = $contact->save();
        //save after contact is saved, update all contacts in organisation
        if (Settings::instance()->get('organisation_api_control_membership')) {
            if ($contact->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id']) {
                $contact->update_membership_for_organisation($is_member);
            } else {
                $organisation = $contact->get_linked_organisation();
                if (!empty($organisation)) {
                    if (Settings::instance()->get('organisation_api_control_membership')
                        && Settings::instance()->get('organisation_integration_api')
                        && Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                        $membership_status = false;
                        $cds = new Model_CDSAPI();
                        $cds_account = $cds->get_account($organisation->get_id());

                        if (!empty($cds_account)) {
                            $membership_status = @$cds_account['sp_membershipstatus'];
                        }
                        $organisation->update_membership_for_organisation($membership_status);
                    }

                }
            }
        }
        if ($contact->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id']) {
            if ($api_turned_on && $cds_admin_allowed) {
                if (!empty($post['external_api_account'])) {
                    if(!is_numeric($post['external_api_account'])) {
                        $current_cc_contact_sync = $rs->save_object_synced(
                            Model_CDSAPI::API_NAME . '-Account',
                            $post['external_api_account'],
                            $contact->get_id());
                    }
                } else {
                    //Model_Contacts3::create_external_account($contact);
                    if (!is_numeric($post['id']) && Settings::instance()->get('organisation_create_external_account')) {
                      Model_Contacts3::create_external_account($contact);
                    } else {
                      $rs->delete_object_synced(Model_CDSAPI::API_NAME . '-Account', $contact->get_id());
                    }
                }
            }
        }
        if (@$post['send_invite'] == 1) {
            foreach ($post['contactdetail_id'] as $key => $contactdetail_id) {
                if ($post['contactdetail_type_id'][$key] == 1 && $post['contactdetail_value'][$key] != '') {
                    // If the contact is sent an invite, update their contact prefernences but do not overwrite their other preferences if they have some
                    $invited_contact_id = $contact->get_id();
                    Model_Contacts3::invite_member($post['contactdetail_value'][$key], $invited_contact_id,
                        $contact->get_first_name() . ' ' . $contact->get_last_name(), $invited_contact_id, true);
                    break;
                }
            }
        }
        $host_type = Model_Contacts3::find_type('Host Family');
        if ($contact_saved && $post['type'] == $host_type['contact_type_id']) {
            $host = array(
                'contact_id' => $contact->get_id(),
                'pets' => @$post['host']['pets'],
                'facilities_description' => @$post['host']['facilities_description'],
                'student_profile' =>  @$post['host']['student_profile'] ? implode(',', @$post['host']['student_profile']) : '',
                'availability' => $post['host']['availability'],
                'facilities' => $post['host']['facilities'] ? implode(',', $post['host']['facilities']) : '',
                'rules' => $post['host']['rules'],
                'other' => $post['host']['other'],
                'status' => $post['host']['status'],
                'updated' => date::now(),
            );
            Model_Host::save($host);
        }
        if ($contact->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id']) {
            $contact_id = (empty($post['id'])) ? $contact->get_id() : $post['id'];
            $org_contact = Model_Organisation::get_org_by_contact_id($contact_id);
            $org_contact->set_organisation_size_id($post['organisation_size_id']);
            $org_contact->set_organisation_industry_id($post['organisation_industry_id']);
            $org_contact->set_primary_biller_id((empty($post['primary_biller_id'])) ? null : $post['primary_biller_id']);
            $org_contact->save();
        }
        if ($contact_saved && Model_Plugin::is_enabled_for_role('Administrator', 'timeoff')) {
            $contact_type = '';
            $contact_types = Model_Contacts3::get_types();
            foreach ($contact_types as $type) {
                if ($contact->get_type() == $type['contact_type_id']) {
                    $contact_type = $type['label'];
                    break;
                }
            }
            DB::delete('plugin_timeoff_config')
                ->where('item_id', '=', $contact->get_id())
                ->execute();
            $level = 'contact';
            if ($contact_type == 'Department') {
                $level = 'department';
            }
            if ($contact_type == 'Business') {
                $level = 'organisation';
            }
            if (@$post['timeoff_annual_leave']) {
                DB::insert('plugin_timeoff_config')
                    ->values(
                        array(
                            'name' => 'timeoff.annual_leave',
                            'item_id' => $contact->get_id(),
                            'level' => $level,
                            'value' => $post['timeoff_annual_leave']
                        )
                    )->execute();
            }
            if (@$post['timeoff_log_hours_per_week']) {
                DB::insert('plugin_timeoff_config')
                    ->values(
                        array(
                            'name' => 'timeoff.log_hours_per_week',
                            'item_id' => $contact->get_id(),
                            'level' => $level,
                            'value' => $post['timeoff_log_hours_per_week']
                        )
                    )->execute();
            }
            if (@$post['timeoff_times']) {
                $days = array(0 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                foreach ($days as $day => $day_name) {
                    if (!isset($post['timeoff_times'][$day]['active'])) {
                        $post['timeoff_times'][$day]['active'] = 0;
                    }

                    DB::insert('plugin_timeoff_config')
                        ->values(
                            array(
                                'name' => 'timeoff.time_preferences_' . $day_name,
                                'item_id' => $contact->get_id(),
                                'level' => $level,
                                'value' => $post['timeoff_times'][$day]['hours'],
                                'start_time' => $post['timeoff_times'][$day]['start'] ?: null,
                                'end_time' => $post['timeoff_times'][$day]['end'] ?: null,
                                'is_active' => $post['timeoff_times'][$day]['active']
                            )
                        )->execute();
                }
            }
        }

        if ($contact_saved) {
            if (!empty($contact_details)) {
                $is_organisation = $contact->get_type() == Model_Contacts3::find_type('Organisation');
                $contactUser = Model_Contacts3::get_user_by_contact_id($contact->get_id());
                $users = new Model_Users();
                $data = array();
                foreach ($contact_details as $cdindex => $contactdetail_value) {
                    if ($contactdetail_value['notification_id'] == 1 && !empty($contactdetail_value['value'])) {
                        $data['email'] = $contactdetail_value['value'];
                    }
                    if ($contactdetail_value['notification_id'] == 2) {
                        $data['dial_code_mobile'] = $contactdetail_value['dial_code_mobile'];
                        $data['country_dial_code_mobile'] = $contactdetail_value['country_dial_code_mobile'];
                        $data['mobile'] = $contactdetail_value['mobile'];
                    }
                }
                if (!empty($post['password']) && !empty($post['mpassword'])) {
                    $data['password'] = $post['password'];
                    $data['mpassword'] = $post['mpassword'];
                }

                $data['name'] = $post['first_name'];
                $data['surname'] = @$post['last_name'];

                try {
                    $roles = new Model_Roles();
                    if (!$contactUser && !$is_organisation) { // set role for new user only
                        if (in_array('teacher', $contact->get_roles_stubs())) {
                            $data['role_id'] = $roles->get_id_for_role('Teacher');
                        } elseif (in_array('mature', $contact->get_roles_stubs())) {
                            $data['role_id'] = $roles->get_id_for_role('Mature Student');
                        } elseif (in_array('supervisor', $contact->get_roles_stubs())) {
                            $data['role_id'] = $roles->get_id_for_role('Manager');
                        } elseif (in_array('admin', $contact->get_roles_stubs())) {
                            $data['role_id'] = $roles->get_id_for_role('Manager');
                        } else {
                            $data['role_id'] = in_array('guardian',
                                $contact->get_roles_stubs()) ? $roles->get_id_for_role('Parent/Guardian') : $roles->get_id_for_role('Student');
                        }
                    }

                    Database::instance()->begin();
                    if (!$contactUser && !$is_organisation && @$post['send_invite'] == 1) {
                        $data['send_verification_email'] = false;

                        if (empty($post['password']) && empty($post['mpassword'])) {
                            $data['password'] = $data['mpassword'] = $users->random_password();
                        }

                        $result = $users->register_user($data);

                        if ($result['success']) {
                            Database::instance()->commit();
                            $contact->set_linked_user_id($result['id']);
                            $contact->set_permissions(array($result['id']));
                            $contact->save();

                            DB::update('engine_users')->set(array('default_home_page' => '/admin'))->where('id',
                                '=', $result['id'])->execute();
                        } else {
                            if (!empty($result['error'])) {
                                Database::instance()->rollback();
                            }
                        }
                    } else {
                        $updated = $users->update_user_data($contactUser['id'], $data);

                        if ($updated) {
                            Database::instance()->commit();
                        } else {
                            Database::instance()->rollback();
                        }
                    }
                } catch (Exception $exc) {
                    Database::instance()->rollback();
                    throw $exc;
                }
            }

            $this->family = $contact->get_family_id();
            if ($post['is_primary'] == 1 || $post['new_family'] == 1) {
                $contact_id = $contact->get_id();
                $family = new Model_Family($contact->get_family_id());
                $family->load(array('residence' => $contact->get_residence()));
                $family->set_primary_contact_id($contact_id);
                $family->set_residence($contact->get_residence());
                $family->set_notifications_group_id($contact->get_notifications_group_id());
                $family->save();
                $contact->set_family_primary_contact($post['family_id'], $contact_id);
                $contact->save();
            }
        }

        IbHelpers::set_message(($contact_saved) ? 'Contact saved.' : 'Failed to save contact', ($contact_saved) ? 'success popup_box' : 'error popup_box');

        if ($contact_saved AND ! empty($post['notes']))
        {
            $this->save_note(array('note' => $post['notes'], 'table' => 'contacts', 'link_id' => $contact->get_id()));
        }

        $result['id']     = ($contact_saved) ? $contact->get_id() : FALSE;
        $result['name']   = ($contact_saved) ? $contact->get_first_name() . ' ' . $contact->get_last_name() : false;
        $result['alerts'] = IbHelpers::get_messages();

        return $result;
    }

    function action_ajax_save_contact()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $post    = $this->request->post();
        $result = $this->save_contact($post);
        $this->response->body(json_encode($result));
    }

    function action_ajax_save_family()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
		$post              = $this->request->post();
        $id                = is_numeric($post['family_id']) ? $post['family_id'] : NULL ;
        $family            = new Model_Family($id);
        $family->load($post);
        if ($post['primary_contact_id'] != '')
        {
            $contact = new Model_Contacts3($post['primary_contact_id']);
            $family->set_primary_contact_id($post['primary_contact_id']);
            $notification = $contact->get_notifications_group_id();
            $residence = $contact->get_residence();
            $family->set_notifications_group_id($notification);
            $family->set_residence($residence);
            $stat = $contact->set_family_primary_contact($post['family_id'],$post['primary_contact_id']);
        }
        $result['success'] = $family->save();

        if ($result['success'])
        {
            IbHelpers::set_message('Family saved', 'success popup_box');
            $result['id'] = $family->get_id();
            $residence = new Model_Residence($family->get_residence());
            $result['address'] = $residence->get_instance();
            $result['notifications'] = $family->get_contact_notifications();
            $result['notification_types'] = Model_Contacts3::get_notification_types();
        }
        $result['alerts'] = IbHelpers::get_messages();
        $this->response->body(json_encode($result));
    }

    /**
     * @purpose Generating a list of contacts, using a posted phone number
     * @return string - json object of contacts
     */
    function action_ajax_get_contacts_by_phone_number()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $number            = $this->request->post('number');
        $contacts          = Model_Contacts3::get_all_contacts_by_phone_number($number);

        $this->response->body(json_encode($contacts));
    }

    function action_ajax_get_residence()
    {
        $this->auto_render = FALSE;
        $family_id         = $this->request->post('family_id');
        $family            = new Model_Family($family_id);
        $residence         = new Model_Residence($family->get_residence());

        $this->response->body(json_encode($residence->get()));
    }

    public function action_ajax_search()
    {
        $filters = [];
        if (!empty($this->request->query('tag_id'))) {
            $filters[] = ['tag.id', '=', $this->request->query('tag_id')];
        }

        $contacts  = Model_Contacts3::get_all_contacts($filters);
        $results = [];
        foreach ($contacts as $key => $contact) {
            $results[] = ['id' => $contact['id'], 'full_name' => $contact['full_name']];
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');
        $this->response->body(json_encode(['contacts' => $results]));
    }

    /**
     * @purpose Generating a list of contacts for autocompletes
     * @return
     */
    public function action_ajax_get_all_contacts_ui()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $filter            = '%'.$this->request->query('term').'%';
        $not_family_id     = $this->request->query('not_family_id');
		$no_family         = $this->request->query('no_family');
		$where_clauses[]   = 'open';
        $where_clauses[]   = array('c_notif_m.value',       'LIKE', $filter, 'or');
        $where_clauses[]   = array('c_notif_e.value',       'LIKE', $filter, 'or');
        $where_clauses[]   = array('family.family_name', 'LIKE', $filter, 'or');
        $where_clauses[]   = array(DB::expr("CONCAT(`contact`.`first_name` , ' ', `contact`.`last_name`)"), 'LIKE', $filter, 'or');
        $where_clauses[]   = 'close';
		if ($no_family)
		{
			$where_clauses[]    = array('family.family_id', '=', NULL);
		}
        elseif ( ! is_null($not_family_id))
        {
            $where_clauses[]   = 'open';
            $where_clauses[]   = array('family.family_id', '!=', $not_family_id);
            $where_clauses[]   = array('family.family_id',  '=', NULL, 'or');
            $where_clauses[]   = 'close';
        }
        $results           = Model_Contacts3::get_all_contacts($where_clauses);
        $results = array_slice($results, 0, 10);

        if (sizeof($results) <= 0 )
        {
            $results = array();
        }

        for($i = 0; $i < sizeof($results); $i++)
        {
            $results[$i]['value'] = $results[$i]['mobile'].' - '.$results[$i]['first_name'].' '.$results[$i]['last_name'].' - ';
            $results[$i]['value'] .= $this->request->query('show_email') ? @$results[$i]['email'] : $results[$i]['family'];
            $results[$i]['value'] = trim(trim(trim($results[$i]['value']), '-'));
        }

        $this->response->headers('Content-type', 'application/json');
        $this->response->body(json_encode($results));
    }

    /**
     * @purpose Generating a list of families for the family select autocomplete
     * @return string - json
     */
    public function action_ajax_get_all_families_ui()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $filter            = '%'.$this->request->query('term').'%';
        $where_clauses[]   = array('family.family_name', 'LIKE', $filter);
        $results           = Model_Family::get_all_families($where_clauses, 'family.family_name', 'asc', 10);
        $results           = array_slice($results, 0, 10);

        for ($i = 0; $i < sizeof($results); $i++)
        {
            $results[$i]['id']    = $results[$i]['family_id'];
            $results[$i]['value'] = $results[$i]['family_name'].' - '.$results[$i]['address1'].', Co. '.$results[$i]['county'];
//            $results[$i]['value'] = trim(trim($results[$i]['value'], ', '), ' - ');
        }

        $this->response->headers('Content-type', 'application/json');
        $this->response->body(json_encode($results));
    }

    /**
     * @purpose Generating a list of family members for the primary-contact field
     * @return string - json
     */
    public function action_ajax_get_all_family_members_ui()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $family_id         = $this->request->param('id');
        $filter            = '%'.$this->request->query('term').'%';
        $where_clauses[]   = array('contact.family_id',  '=', $family_id);
        $where_clauses[]   = 'open';
        $where_clauses[]   = array('role.name',    'LIKE', $filter, 'or');
        $where_clauses[]   = array('mobile.value', 'LIKE', $filter, 'or');
        $where_clauses[]   = array(DB::expr("CONCAT(`contact`.`first_name` , ' ', `contact`.`last_name`)"), 'LIKE', $filter, 'or');
        $where_clauses[]   = 'close';
        $results           = Model_Contacts3::get_all_contacts($where_clauses);
        for ($i = 0; $i < sizeof($results); $i++)
        {
            $results[$i]['value']  = (isset($results[$i]['first_name']) ? $results[$i]['first_name'] : '').' ';
            $results[$i]['value'] .= (isset($results[$i]['last_name'])  ? $results[$i]['last_name']  : '').' - ';
            $results[$i]['value'] .= (isset($results[$i]['mobile'])     ? $results[$i]['mobile']     : '').' - ';
            $results[$i]['value'] .= (isset($results[$i]['role'])       ? $results[$i]['role']       : '');
            $results[$i]['value']  = trim(trim(trim(str_replace('-  -', '-', $results[$i]['value'])), '-'));
        }

        $this->response->headers('Content-type', 'application/json');
        $this->response->body(json_encode($results));
    }

    public function action_find_contact()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $result = array();
        $this->auto_render = false;
        $data = $this->request->query();
        if(isset($data['role'])) {
            $where_clauses[] = array("role.name", '=', $data['role']);
        };
        if (isset($data['contact_type'])) {
            $where_clauses[] = array("type.label", '=', $data['contact_type']);
        };
        if (isset($data['contact_type_id'])) {
            $where_clauses[] = array("type.contact_type_id", '=', $data['contact_type_id']);
        };
        if (isset($data['linked_organisation_id'])) {
            $where_clauses[] = array("business_relations.parent_id", '=', $data['linked_organisation_id']);
        };
        
        $where_clauses[] = 'open';
        $where_clauses[]   = array(DB::expr("CONCAT_WS(' ', `contact`.`first_name`, `contact`.`last_name`)"), 'LIKE', "%{$data['term']}%");
        $where_clauses[]   = 'close';
        $limit = 10;
        $q = Model_Contacts3::get_all_contacts($where_clauses, $limit);
        foreach ($q as $key=>$row)
        {
            $result[] = array('id'=>$row['id'],'value'=> $row['id'] . ' - ' . $row['title'].' '.$row['first_name'].' '.$row['last_name']);
        }
        $this->response->headers('Content-type', 'application/json');
        $this->response->body(json_encode($result));
    }

    public function action_ajax_load_notifications()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $post = $this->request->post();

        if (isset($post['family_id']))
        {
            $family             = new Model_Family($post['family_id']);
            $notifications      = $family->get_contact_notifications();
            $return['group_id'] = $family->get_notifications_group_id();
            $return['html']     = '';
            foreach ($notifications as $notification)
            {
                $return['html'] .= (string) View::factory('admin/snippets/add_edit_contact_method')->set('notification', $notification);
            }
            $this->response->body(json_encode($return));
        }
    }

    /**
     * @purpose Get all family members, with the account preference ticked
     */
    public function action_ajax_get_family_account_supervisors()
    {
        $this->auto_render = FALSE;
        $family_id         = $this->request->param('id');
        $account_contacts  = Model_Family::get_family_account_supervisors($family_id);
        $this->response->body(json_encode($account_contacts));
    }

    public function action_ajax_get_primary_contacts()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $family_id         = $this->request->param('id');
        $primary_contacts  = Model_Contacts3::get_all_contacts(array(
            array('family.family_id', '=', $family_id)),
            array('contact.is_primary', '=', 1)
        );
        $this->response->body(json_encode($primary_contacts));
    }

    public function action_ajax_get_notes()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_notes')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
		$note_type         = $this->request->param('id');
		$item_id           = $this->request->query('id');
        $where_clauses[]   = array('note.link_id', '=', $item_id);
        $where_clauses[]   = array('table.table',  '=', 'plugin_contacts3_'.$note_type);
        $notes             = Model_EducateNotes::get_all_notes($where_clauses);

		// If this is for a contact, also show notes for their booking items
		if ($note_type == 'contacts')
		{
			$booking_item_notes = Model_KES_Bookings::get_contact_booking_notes($item_id);
			$notes = array_merge($notes, $booking_item_notes);
		}

        $view              = View::factory('admin/list_notes')->set('notes', $notes);
        $this->response->body($view);
    }
	
	public function action_ajax_get_messages()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_messages')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
		$contact_id = $this->request->query('contact_id');
        $auth = Auth::instance();
        $contact3 = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id']);
        if (!$auth->has_access('messaging_access_others_mail')) {
            $contact = $auth->get_contact3();
            if ($contact->get_id() != $contact_id) {
                $error_id = Model_Errorlog::save(null, 'SECURITY');
                IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                $this->request->redirect('/admin');
            } else {
                if (!$auth->has_access('messaging_access_own_mail')) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }

        $emails = DB::select('has.value')
            ->from(array('plugin_contacts3_contacts', 'contacts'))
                ->join(array('plugin_contacts3_contact_has_notifications','has'), 'inner')
                    ->on('contacts.notifications_group_id', '=', 'has.group_id')
                ->join(array('plugin_contacts3_notifications','n'))
                    ->on('has.notification_id', '=', 'n.id')
            ->where('n.stub','=','email')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array();
        $user = Model_Contacts3::get_user_by_contact_id($contact_id);
        $messaging = new Model_Messaging();
        if ($user !== null) {
            $messages = array_merge(
                $messaging->search_messages([
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $user['id'],
                    'assoc' => 'id']),
                $messaging->search_messages([
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $contact_id,
                    'assoc' => 'id']));
        } elseif ($emails) {
            $messaging = new Model_Messaging();
            $messages = $messaging->search_messages(array('target_type' => 'CMS_CONTACT3', 'target' => $contact_id));
        } else {
            $messages = '';
        }
        $view = View::factory('admin/list_contact3_messages')->set('messages', $messages);
        $this->response->body($view);
    }

    public function action_ajax_get_todos()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_tasks')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $family_id = isset($_GET['family_id']) ? $_GET['family_id'] : 0;
        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : 0;
        $todos             = Model_Todos::get_all_educate_todos($family_id,$contact_id);
        $view              = View::factory('admin/list_todos')->set('todos', $todos);
        $this->response->body($view);
    }

    public function action_ajax_get_subtype_contacts() {
        $term = $this->request->query('term');
        $contact_subtype = $this->request->query('subcontact_type');
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');
        $data = Model_Contacts3::get_contacts_by_subtype($contact_subtype, $term);
        echo json_encode($data);
    }
    /**
     *  Public functions For
     *  BOOKINGS & ACCOUNTS
     */

    /**
     * Get the Bookings
     */
    public function action_ajax_get_bookings()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        if (@$_GET['contact_id']) {
            Model_KES_Bookings::all_set_inprogress_completed(@$_GET['contact_id']);
        }

        $family_id  = isset($_GET['family_id'])  ? $_GET['family_id']  : 0;
        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : 0;
        $bookings   = Model_KES_Bookings::get_contact_family_bookings($family_id, $contact_id, null, false);
        if(Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
            $nav_api = new Model_NAVAPI();
            foreach ($bookings as &$booking) {
                /*$nav_booking = $nav_api->get_booking($booking['booking_id']);
                if (!empty($nav_booking)) {
                    $booking['nav_outstanding'] = @$nav_booking['remainingAmount'];
                } else {
                    $booking['nav_outstanding'] = 0.00;
                }*/

                $booking['nav_outstanding'] = '-';

                $booking_obj = new Model_KES_Bookings($booking['booking_id']);
                $booking['payment_type'] = $booking_obj->get_payment_method();
                if ($booking['payment_type'] == 'cc') {
                    $booking['payment_type'] = 'credit card';
                }
                $booking['nav_invoice'] = $this->_get_booking_invoices($booking['booking_id'], $contact_id);
            }
        }
        $view       = View::factory('admin/list_bookings')->set('bookings', $bookings);

        if ($contact_id) {
            $linked_bookings = Model_KES_Bookings::get_contact_family_bookings(null, null, null, true,  null, $contact_id);

            if (count($linked_bookings)) {
                $view  = '<h2>Contact bookings</h2>'.$view;
                $view .= '<h2>Linked bookings</h2>'.View::factory('admin/list_bookings')->set('bookings', $linked_bookings);
            }
        }

        $this->response->body($view);
    }

    public function action_ajax_get_applications()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_applications')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $contact_id   = $this->request->query('contact_id');
        $view         = View::factory('admin/list_course_applications')->set([
            'applications'          => Model_KES_Bookings::get_contact_applications($contact_id),
            'has_instrument_column' => (Settings::instance()->get('checkout_customization') == 'lsm')
        ]);
        $this->response->body($view);
    }
    /**
     * Load the timetable
     * @throws \View_Exception
     */
    public function action_ajax_get_booking_timetable()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_timetable')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $query             = $this->request->post();
        $this->view_booking_timetable($query);
    }

    public function view_booking_timetable($query)
    {
        $start_date = date('Y-m-d H:i:s', strtotime('this week', time()));
        $end_date   = date('Y-m-d H:i:s',strtotime("+4 Week", strtotime("-1 Day",strtotime($start_date))));
        $before            =  (isset($query['before']) AND $query['before'] != '') ? date('Y-m-d H:i:s', strtotime($query['before'])) : $end_date;
        $after             =  (isset($query['after']) AND $query['after'] != '') ? date('Y-m-d H:i:s', strtotime($query['after']))  : $start_date;
        $booking_items     = array();
        $print_filter = $query['print_filter'];

        if (isset($query['contact_id']))
        {
            $booking_items = Model_KES_Bookings::get_booking_items_family($query['contact_id'], NULL);
        }
        elseif (isset($query['family_id']))
        {
            $booking_items = Model_KES_Bookings::get_booking_items_family(NULL, $query['family_id']);
        }

        // Get the seven most recent unique dates and group bookings by date
        $previous_date = '-';
        $dates = array();
        $cells = array();
        foreach ($booking_items as $i => $booking_item)
        {
            if ($booking_items[$i]['attending'] == 1) {
                $date = date('Y-m-d', strtotime($booking_items[$i]['datetime_start']));
                if (!isset($cells[$date])) $cells[$date] = array();
                $cells[$date][] = $booking_items[$i];
                if ($date != $previous_date) {
                    array_push($dates, $date);
                }
                $previous_date = $date;
            } else {
                unset($booking_items[$i]);
            }
        }
        $booking_items = array_values($booking_items);
//        $booking_items = array_slice($booking_items, 0, $i);
        array_unique($dates);
        $dates = array_reverse($dates);

        $weeks = array();
        $week_number = NULL;
        for ($date = $after ; $date <= $before ; $date = date('Y-m-d',strtotime($date.'+1 day')) )
        {
            if ($week_number != date('W',strtotime($date)) )
            {
                $week_number  = date('W',strtotime($date));
                $weeks[$week_number] = array('dates'=>array(),'times'=>array(),'cells'=>array(),'booking_items'=>array());
            }
            $weeks[$week_number]['dates'][]=$date;
        }
        foreach($weeks as $key=>$week)
        {
            $times = array();
            foreach($week['dates'] as $k=>$date)
            {
                if ( ! isset($weeks[$key]['cells'][$date]))
                {
                    $weeks[$key]['cells'][$date] = array();
                }
                $previous_time = ' ';
                foreach ($booking_items as $booking_item)
                {
                    if ($date == date('Y-m-d', strtotime($booking_item['datetime_start'])) )
                    {
                        $weeks[$key]['booking_items'][] = $booking_item;
                        $weeks[$key]['cells'][$date][] = $booking_item;
                    }
                    if ( date('W',strtotime($week['dates'][0])) == date('W',strtotime($booking_item['datetime_start'])) )
                    {
                        $time = date('H:i', strtotime($booking_item['datetime_start']));
                        if ($time != $previous_time)
                        {
                            $previous_time = $time;
                            array_push($times, $time);
                        }
                    }
                }
            }
            $times = array_unique($times);
            sort($times);
            $weeks[$key]['times']=$times;
        }

        $previous_time = ' ';
        $times = array();
		$calendar_events = array();

		// Get all unique start times
		// and data for the calendar
		foreach ($booking_items as $key => $booking_item)
		{
			$time = date('H:i', strtotime($booking_item['datetime_start']));
			if ($time != $previous_time) {
				$previous_time = $time;
				array_push($times, $time);
			}

			$calendar_events[$key] = $booking_item;
			$calendar_events[$key]['title']  = $booking_item['schedule'];
			$calendar_events[$key]['start']  = $booking_item['datetime_start'];
			$calendar_events[$key]['end']    = $booking_item['datetime_end'];
			$calendar_events[$key]['booked'] = true;

            // So the schedule colour does not dictate the calendar event
            $calendar_events[$key]['schedule_color'] = $booking_item['color'];
            unset($calendar_events[$key]['color']);
		}

		$times = array_unique($times);
		sort($times);

        $view = array(
            'booking_items' => $booking_items,
            'calendar_events' => $calendar_events,
            'dates' => $dates,
            'times' => $times,
            'cells' => $cells,
            'weeks' => $weeks,
            'start_date' => $after,
            'end_date' => $before,
            'print_filter' => $print_filter,
            'filter_contact_id' => isset($query['contact_id']) ? $query['contact_id'] : null
        );
        $this->response->body(View::factory('/admin/bookings/booking_calendar',$view));
    }

    public function action_prev_week()
    {
        $this->auto_render = false;
        $query = $this->request->post();
        $query['before']= date('Y-m-d',strtotime("-1 Week",strtotime($query['before'])));
        $query['after'] = date('Y-m-d',strtotime("-1 Week",strtotime($query['after'])));
        $this->response->body(json_encode($query));
    }

    public function action_next_week()
    {
        $this->auto_render = false;
        $query = $this->request->post();
        $query['before']= date('Y-m-d',strtotime("+1 Week",strtotime($query['before'])));
        $query['after'] = date('Y-m-d',strtotime("+1 Week",strtotime($query['after'])));
        $this->response->body(json_encode($query));
    }

    public function action_ajax_prev_month()
    {
        $this->auto_render  = false;
        $post               = $this->request->post();
        $query              = array();
        $query['before']    = date('Y-m-d',strtotime("-4 Week",strtotime($post['before'])));
        $query['after']     = date('Y-m-d',strtotime("-4 Week",strtotime($post['after'])));
        $this->response->body(json_encode($query));
    }

    public function action_ajax_next_month()
    {
        $this->auto_render  = false;
        $post               = $this->request->post();
        $query              = array();
        $query['before']    = date('Y-m-d',strtotime("+4 Week",strtotime($post['before'])));
        $query['after']     = date('Y-m-d',strtotime("+4 Week",strtotime($post['after'])));
        $this->response->body(json_encode($query));
    }

    public function action_ajax_get_family_member_accounts()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : 0;
        $transactions       = Model_Kes_Transaction::get_contact_transactions($contact_id,NULL);
        $view = View::factory('admin/list_accounts_transactions')
            ->set('transactions', $transactions)
            ->set('transaction_types', ORM::factory('Kes_Transaction')->get_transaction_types())
            ->set('payment_statuses', ORM::factory('Kes_Payment')->get_payment_status());
        $this->response->body($view);
    }

    public function action_ajax_get_family_accounts()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $family_id      = isset($_GET['family_id']) ? $_GET['family_id'] : 0;
        $transactions       = Model_Kes_Transaction::get_contact_transactions(NULL,$family_id);
        $view = View::factory('admin/list_accounts_transactions')
        ->set('transactions', $transactions)
        ->set('transaction_types', ORM::factory('Kes_Transaction')->get_transaction_types())
        ->set('payment_statuses', ORM::factory('Kes_Payment')->get_payment_status());
        $this->response->body($view);
    }

    public function action_ajax_get_family_activities()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $family_id      = $_GET['family_id'] ?? '0';
        $family_activities = Model_Activity::get_family_activities($family_id);
        $view = View::factory('list_family_activities')
            ->set('family_activities', $family_activities);
        $this->response->body($view);
    }

    public function action_ajax_get_family_member_activities()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_activities')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $contact_id      = $_GET['family_member-contact_id'] ?? '0';
        $family_member_activities = Model_Activity::get_contact_activities($contact_id);
        $view = View::factory('list_family_member_activities')
            ->set('family_member_activities', $family_member_activities);
        $this->response->body($view);
    }
	public function action_ajax_get_family_member_messages()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : 0;
        $transactions       = Model_Kes_Transaction::get_contact_transactions($contact_id,NULL);
        $view = View::factory('admin/list_accounts_transactions')
            ->set('transactions', $transactions)
            ->set('transaction_types', ORM::factory('Kes_Transaction')->get_transaction_types())
            ->set('payment_statuses', ORM::factory('Kes_Payment')->get_payment_status());
        $this->response->body($view);
    }

    public function action_ajax_get_family_member_booking_activities()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $booking_id = $_GET['booking_id'] ?? 0;
        $family_member_booking_activities = Model_Activity::get_family_member_booking_activities($booking_id);
        $view = View::factory('admin/list_family_member_booking_activities')
            ->set('family_member_booking_activities', $family_member_booking_activities);
        $this->response->body($view);
    }

    public function action_ajax_edit_note()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $note              = new Model_EducateNotes($this->request->param('id'));
        $table_name        = Model_EducateNotes::get_table_name_from_id($note->get_table_link_id());
        $table_name        = str_replace('plugin_contacts3_', '', $table_name);
        $view              = View::factory('/admin/edit_note')->set('note', $note)->set('table_name', $table_name);
        $this->response->body($view);
    }

    public function action_ajax_get_family_member_booking_delegates()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $booking_id = $_GET['booking_id'] ?? 0;
        $delegates = Model_KES_Bookings::get_delegates($booking_id);
        $view = View::factory('admin/list_family_member_booking_delegates')
            ->set('delegates', $delegates);
        $this->response->body($view);
    }

    function action_ajax_save_note()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $result['success'] = $this->save_note($this->request->post());
        $result['alerts']  = IbHelpers::get_messages();
        $this->response->body(json_encode($result));
    }

    public function action_ajax_delete_note()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $id                = $this->request->param('id');
        $note              = new Model_EducateNotes($id);
        $note_deleted      = $note->delete();
        IbHelpers::set_message((($note_deleted) ? 'Note #'.$id.' deleted.' : 'Failed to delete note'), (($note_deleted) ? 'success popup_box' : 'error popup_box'));
        $result['success'] = $note_deleted;
        $result['alerts']  = IbHelpers::get_messages();
        $this->response->body(json_encode($result));
    }

    public function action_ajax_change_family()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = FALSE;
        $post              = $this->request->post();
        $contact           = new Model_Contacts3($post['contact_id']);
        $old_family        = new Model_Family($contact->get_family_id());
        $contact->set_family_id($post['family_id']);
        $saved             = $contact->save();
        $new_family        = new Model_Family($contact->get_family_id());
        $contact_string    = 'Contact #'.$contact   ->get_id().': '.$contact   ->get_first_name().' '.$contact->get_last_name();
        $old_family_string = 'family #' .$new_family->get_id().': '.$new_family->get_family_name();
        $new_family_string = 'family #' .$new_family->get_id().': '.$new_family->get_family_name();

        if ($saved AND is_null($old_family->get_id()))
        {
            $message = $contact_string.' moved from '.$old_family_string.' to '.$new_family_string();
        }
        elseif ($saved)
        {
            $message = $contact_string.' added to '.$new_family_string;
        }
        else
        {
            $message = 'Failed to add '.$contact_string.' to '.$new_family_string;
        }
        IbHelpers::set_message($message, ($saved) ? 'success popup_box' : 'error popup_box');
        $return['result'] = $saved;
        $return['alerts'] = IbHelpers::get_messages();
        $this->response->body(json_encode($return));
    }

    private function save_note($data)
    {
        if ($data['table'] == 'contacts') {
            DB::update(Model_Contacts3::CONTACTS_TABLE)
                ->set(array('date_modified' => date::now()))
                ->where('id', '=', $data['link_id'])
                ->execute();
        }
        $note                  = new Model_EducateNotes((isset($data['id'])) ? $data['id'] : NULL);
        $data['table_link_id'] = (isset($data['table'])) ? $note->get_table_link_id_from_name('plugin_contacts3_'.$data['table']) : $data['table_link_id'];
        $note->load($data);
        $note_saved            = $note->save();
        IbHelpers::set_message((($note_saved) ? 'Note #'.$note->get_id().' saved.' : 'Failed to save note'), (($note_saved) ? 'success popup_box' : 'error popup_box'));
        return $note_saved;
    }
    /**
     * A function for retrieving invoices and adding them to booking table
     * @param $booking_id
     * @param $contact_id
     * @return string
     */
    private function _get_booking_invoices($booking_id, $contact_id) {
        $booking_invoice_documents = Model_Booking_Files::get_files_by_booking_id($booking_id);
        if(Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
            $nav_api = new Model_NAVAPI();
        }
        $nav_invoice = '';
        if (!empty($booking_invoice_documents)) {
            foreach($booking_invoice_documents as $booking_invoice_document) {
                $nav_invoice .= '<a href="/admin/documents/doc_quick_download/' . $booking_invoice_document['document_id'].'">' .$booking_invoice_document['name']. '</a><br/>';
            }
        } else {
            if(Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
                $booking_transactions = Model_Kes_Transaction::get_contact_transactions($contact_id, null, $booking_id);
                //if booking documents empty, try to retrieve them from nav and store to booking, then check again
                if (!empty($booking_transactions)) {
                    //this can be slow on first run, cause it will call NAV API for each transaction found for the booking
                    foreach ($booking_transactions as $booking_transaction) {
                        $received_pdf = $nav_api->get_pdf($booking_transaction['id']);
                        if ($received_pdf) {
                            $rs = new Model_Remotesync();
                            $rs->save_object_synced(Model_NAVAPI::API_NAME . '-PDF', $booking_transaction['id'],
                                $booking_transaction['id']);
                        }
                    }
                }
                $booking_invoice_documents = Model_Booking_Files::get_files_by_booking_id($booking_id);
                if (!empty($booking_invoice_documents)) {
                    foreach ($booking_invoice_documents as $booking_invoice_document) {
                        $nav_invoice .= '<a href="/admin/documents/doc_quick_download/' . $booking_invoice_document['document_id'] . '">' . $booking_invoice_document['name'] . '</a><br/>';
                    }
                }
            }
        }
        return $nav_invoice;
    }

    public function action_create_todo()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $todos = Model_Todos::get();
        if(count($_POST) > 0)
        {
            $_POST = $_POST['form'];
        }
        
        if($this->request->post())
        {
            $id = Model_Todos::save_from_post($this->request->post('form'));
            
            return 0;
            
         }
    
        if (!empty($this->request->query('contact_id'))) {
            $contact_id = $this->request->query('contact_id');
        } else {
            $family = new Model_Family($this->request->query('family_id'));
            $contact_id = $family->get_primary_contact_id();
        }
        
        $action = 'create_todo';
        $user = Auth::instance()->get_user();
        $related_to_types = Model_Todos::get_related_to();
        $contact_assignee = new Model_Contacts3($contact_id);
        $this->response->body(View::factory('/admin/add_edit_todo')->bind('action',$action)->bind('todo',$todo)
            ->bind('related_to_id',$related_to_id)->bind('user',$user)->bind('contact_assignee', $contact_assignee)->bind('related_to_types',
                $related_to_types));
    }

    public function action_ajax_edit_todo()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $id = $this->request->param('id');
        $this->auto_render = false;
        $related_plugin_name = $this->request->query('related_plugin_name'); // return "plugin_projects" or other related plugin name
        $related_to_id = $this->request->query('related_to_id'); // return in case of "projects" it is project_id
        $return_url = $this->request->query('return_url');

        //default return url is ToDos plugin list form
        if (!$return_url)
        {
            $return_url = '/admin/contacts3/';
        }

        $todos = new Model_Todos();

        if ($this->request->post())
        {
            $todo = $this->request->post();

            if ($todos->validate($todo))
            {
                $todos->update_todo($id, $todo);
                IbHelpers::set_message('Todo updated','success popup_box');
                return $this->request->redirect( $return_url);
            }

        }
        else
        {
            // if it is GET request then data is retrived from database
            $todo = $todos->get_todo($id);
            $todo['due_date'] = Date::ymdh_to_dmy($todo['due_date']);
        }

        //Generate the 'related link'
        switch($todo['related_to_plugin']){
            case 'policy':
                $todo['url'] = Kohana_URL::site() . 'admin/insurance/policy?id=' . $todo['related_to_id'];
                break;
            case 'claim':
                $todo['url'] = Kohana_URL::site() . 'admin/claim?id=' . $todo['related_to_id'];
                break;
            case 'accounts':
                // Go to bookings
                $todo['url'] = Kohana_URL::site() . 'admin/accounts?id=' . $todo['related_to_id'];
                break;
            case 'contacts':
                $todo['url'] = Kohana_URL::site() . 'admin/contacts?id=' . $todo['related_to_id'];
                break;
            default:
                $todo['url'] = null;
        }

        $action = 'create_todo';
        $user_options = $todos->get_users_as_options($todo['from_user_id']);
        $to_user_options = $todos->get_users_as_options($todo['to_user_id']);
        $user = Auth::instance()->get_user();

        $this->response->body(View::factory('/admin/add_edit_todo')->bind('action',$action)->bind('todo',$todo)->bind('from_user_options',$user_options)
            ->bind('to_user_options',$to_user_options)->bind('related_plugin_name',$related_plugin_name)->bind('related_to_id',$related_to_id)->bind('return_url',$return_url)->bind('user',$user)->bind('family_id',$family_id)->bind('contact_id',$contact_id));
    }

    public function action_ajax_get_booking_periods()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render    = FALSE;
        $post                 = $this->request->post();
        $data['booking_id']   = isset($post['booking_id'])  ? $post['booking_id']  : '';
        $data['schedule_id']  = isset($post['schedule_id']) ? $post['schedule_id'] : '';
        $data['student_name'] = isset($post['student_name']) ? $post['student_name'] : '';
        $data['periods']      = Model_KES_Bookings::get_all_booking_periods($data['booking_id'], $data['schedule_id']);
        $html                 = View::factory('/admin/list_booking_periods')->bind('data', $data)->render();

        $this->response->body(json_encode(array('html' => $html)));
    }

    public function action_get_contact_phone()
    {
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $data = $this->request->post('contact_id');
        $this->response->body(Model_Contacts3::get_contact_phone_number($data));
    }

    public function action_ajax_get_payment(){
        $this->auto_render = false;
        $id = $this->request->query('id');
        $payment_model = ORM::factory('Kes_Payment');
        $result = $payment_model->get_payment($id);
        exit(json_encode($result));
    }

	// Link an existing contact to a family
	public function action_ajax_link_to_family()
	{
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = FALSE;
		$contact_id        = $this->request->post('contact_id');
		$family_id         = $this->request->post('family_id');
		$contact           = new Model_Contacts3($contact_id);

		echo $contact->set_column('family_id', $family_id)->save() ? 'true' : 'false';
	}

	// Reload the family members table, with an up-to-date list of members
	public function action_ajax_refresh_family_members()
	{
        if (!Auth::instance()->has_access('contacts3_view')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = FALSE;
		$family_id         = $this->request->param('id');
		$selected_id       = $this->request->query('selected');
		$family_members    = Model_Contacts3::get_all_contacts(array(array('contact.family_id', '=', $family_id)));

		echo View::factory('admin/list_contact_members_table')
			->set('family_members', $family_members)
			->set('selected_member_id', $selected_id);
	}

    /**
     * To load the default menu for a tab
     */
    public function action_ajax_load_default_actions()
    {
        $this->auto_render = false;
        $contact_id        = $this->request->query('contact_id');
        $contact           = $this->request->query('contact');
        $tab               = $this->request->query('tab');

        if (Auth::instance()->has_access('contacts3_tab_actions_menu')) {
            $view = View::factory('/admin/list_menu_default_actions')
                ->set('contact_id', $contact_id)
                ->set('contact',$contact)
                ->set('tab', $tab);
        } else {
            $view = '';
        }
        $this->response->body($view);
    }

    /**
     * To load the accounts menu for a tab
     */
    public function action_ajax_load_accounts_actions()
    {
        $this->auto_render    = FALSE;
        $view                 = View::factory('/admin/list_menu_accounts_actions');
        $this->response->body($view);
    }

    public function action_ajax_remove_contact_info()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render      = FALSE ;
        $data = $this->request->post();
        $answer = Model_Contacts3::set_notification_deleted($data['number_id']);
        if ($answer)
        {
            $result=array('status'=>'success','message'=>'');
        }
        else
        {
            $result=array('status'=>'error','message'=>'Could not remove the number');
        }
        exit(json_encode($result));
    }

    public function action_import_contacts()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        Model_Contacts3::database_bulk_insert_contacts($this->request->query('test'));
        exit;
    }

    public function action_copy_trainers()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        Model_Contacts3::copy_trainers_as_teachers();
        View::factory('/admin/copy_trainers');
    }

    public function action_import_teachers()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        Model_Contacts3::import_teachers_details();
        exit;
    }

    public function action_import_bookings()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        Model_Contacts3::import_contact_bookings();
        View::factory('admin/import_bookings');
    }

    public function action_test_contact_delete()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $contactId = $this->request->post('contact_id');
        $contact = new Model_Contacts3($contactId);
        echo json_encode($contact->test_delete());
    }

    public function action_cleanup_duplicates()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $post = $this->request->post();
        $step = @$post['step'] ? $post['step'] : 1;

        if($step == 2){
            Model_Contacts3::cleanup_duplicates($post);
        }
        if($step == 1 || $step == 2){
            $role_id = $this->request->query('role_id');
            $like = $this->request->query('like');
            $suggestions = Model_Contacts3::cleanup_duplicates_get_suggestions($role_id, $like); // 4=>Role:Teacher
            $optcontacts = $suggestions['contacts'];
            usort($optcontacts, function($s1, $s2){
                return $s1['id'] == $s2['id'] ? 0 : ($s1['id'] < $s2['id'] ? 1 : -1);
            });
            usort($suggestions['contacts'], function($s1, $s2){
                return strcasecmp($s1['first_name'] . ' ' . $s1['last_name'], $s2['first_name'] . ' ' . $s2['last_name']);
            });
            if($step == 2){
                echo '<h1>replacements done!</h1>';
            }
            echo '<form method="post">';
            echo '<input type=hidden name=step value=2>';
            echo '<table id="replacements">';
            echo '<thead><tr><th>Contact</th><th>&nbsp;</th><th>Replace</th></tr></thead>';
            echo '<tbody></tbody>';
            foreach($suggestions['contacts'] as $contact){
                echo '<tr><td align="right">#'. $contact['id'] . ':<input type="text" name="first_name[' . $contact['id'] . ']" value="' . htmlspecialchars($contact['first_name']) . '" size=10 /><input type="text" name="last_name[' . $contact['id'] . ']" value="' . htmlspecialchars($contact['last_name']) . '" size=10 /></td><td> => </td><td>';
                echo '<select name="replace[' . $contact['id'] . ']"><option value=""></option>';
                foreach($optcontacts as $optcontact){
                    echo '<option value="' . $optcontact['id'] . '"' . (@$suggestions['replaced'][$contact['id']] == $optcontact['id'] ? ' selected="selected"' : '') . '>' . $optcontact['id'] . ': ' . $optcontact['fullname'] . '</option>';
                }
                echo '</select></td></tr>';
            }
            echo '</table>';
            echo '<button type=submit>replace</button>';
            echo '</form>';
            //print_r($suggestions['replaced']);
        }
        exit();
    }

    public function action_ajax_get_attendance()
    {
        if (!Auth::instance()->has_access('contacts3_view') || !Auth::instance()->has_access('contacts3_attendance')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        $view = View::factory('/admin/list_contact3_attendance');
        $view->attendance = Model_KES_Bookings::get_attendance($this->request->post());
        if ($view->attendance['not_attended'] > 0) {
            $view->alert = IbHelpers::alert('Not Attending: ' . $view->attendance['not_attended'] ,'info');
        }
        $this->response->body($view);
    }

    public function action_bulk_transfer_delete()
    {
        if (!Auth::instance()->has_access('contacts3_edit')) {
            $error_id = Model_Errorlog::save(null, 'SECURITY');
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $post = $this->request->post();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        Model_Contacts3::bulk_transfer_delete($post['contact']);
        echo json_encode($post);
    }

    public function action_dashboard()
    {
        if (!$this->linked_to_contact) {
            IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
            $this->request->redirect('admin');
        }

        $user = Auth::instance()->get_user();
        if (Auth::instance()->has_access('contacts3_limited_view')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        } else {
            $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        $attendance = Model_KES_Bookings::get_attendance(array('contact_id' => $contact_ids));

        $booked_courses = count(Model_KES_Bookings::search(array('contact_id' => $contact_ids)));
        $mytime_entries = Model_Mytime::search(array('contact_ids' => $contact_ids));
        $this->template->body = View::factory('frontend/dashboard');
        $this->template->body->booked_courses = $booked_courses;
        $this->template->body->contacts_count = count($contacts);
        $this->template->body->mytime_entries = $mytime_entries;
        $this->template->body->attendance = $attendance;
        $this->template->body->contact = $this->contact;
        $this->template->sidebar->breadcrumbs = array();
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
    }

    public function action_profile()
    {
        $this->request->redirect('/admin/profile/edit?section=contact');
        /*
        if (!$this->linked_to_contact) {
            IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
            $this->request->redirect('admin');
        }

        $this->template->body = View::factory('frontend/profile');
        $this->template->body->view = 'primary-profile';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/profile.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/jquery.validationEngine2.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/jquery.validationEngine2-en.js"></script>';
        $styles = array(URL::get_engine_plugin_assets_base('contacts3').'css/validation.css' => 'screen');
        $this->template->styles = isset($this->template->styles) ? array_merge($this->template->styles, $styles) : $styles;

        $this->template->body->select_contact_id = $this->request->query('contact_id');
        $family = new Model_Family($this->contact->get_family_id());
        $error = $this->request->query('error');

        $this->template->body->contact = $this->contact;
        $this->template->body->family = $family;
        $this->template->body->family_members = Model_Contacts3::get_family_members($this->contact->get_family_id());
        $this->template->body->assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
        $this->template->body->can_view_other_contacts = Model_Contacts3::is_contact_has_privilege_preference($this->contact->get_id(), 'db-otr-pf');
        $this->template->body->error = $error;
        $this->template->body->add_student = $this->request->query('add_student');
        $this->template->body->redirect = $this->request->query('redirect');
        $this->template->sidebar->breadcrumbs = array();
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
        */
    }

    public function action_timetables($args = [])
    {
        $user = Auth::instance()->get_user();
        if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        } else {
            if (Auth::instance()->has_access('contacts3_limited_view')) {
                $contacts = array(array(
                    'id' => $this->contact->get_id(),
                    'family_id' => $this->contact->get_family_id(),
                    'first_name' => $this->contact->get_first_name(),
                    'last_name' => $this->contact->get_last_name(),
                ));
            } else {
                $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
            }
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        if (!$this->linked_to_contact) {
            IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
            $this->request->redirect('admin');
        }
        $contact= new Model_Contacts3($contacts[0]['id']);
        
        $contact_roles = $contact->get_roles_stubs(true);

        if (empty($args['old_ui'])) {
            $family_members = $contact->family ? $contact->family->get_members() : [$contact];
            $family_member_options = [];

            foreach ($family_members as $member) {
                $family_member_options[$member->get_id()] = $member->get_first_name() . ' ' . $member->get_last_name();
            }

            $filter_menu_options = [
                ['name' => 'family_members', 'label' => 'Members', 'options' => $family_member_options, 'selected' => $contact->get_id()],
            ];

            $this->template->body = View::factory('iblisting')->set([
                'daterangepicker'     => true,
                'filter_menu_options' => $filter_menu_options,
                'id_prefix'     => 'booking-calendar',
                'popover_mode'  => 'read',
                'timeslots_url' => '/frontend/contacts3/get_timetables_data',
                'views'         => ['calendar']
            ]);
        }

        else {
            $this->template->body = View::factory('admin/timetables');
        }
        $this->template->body->contacts = $contacts;
        $this->template->body->subjects = Model_Subjects::get_all_subjects();
        $this->template->body->is_teacher = in_array('teacher', $contact->get_roles_stubs());
        $this->template->body->contact_role = (count($contact_roles) > 0) ? current($contact_roles)['name'] : 'Guardian';
        $this->template->sidebar->breadcrumbs = array(
            array('name' => __('Home'), 'link' => '/admin'),
            array('name' => __('My timetable'), 'link' => '/timetables.html')
        );
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
    }

    public function action_accounts()
    {
        $user = Auth::instance()->get_user();
        if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            $transactions = Model_Kes_Transaction::get_contact_transactions($contacts[0]['id'], null);
        } else {
            $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
            $transactions = Model_Kes_Transaction::get_contact_transactions(null, $contacts[0]['family_id']);
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        $view = View::factory('admin/accounts')
            ->set('transactions', $transactions)
            ->set('transaction_types', ORM::factory('Kes_Transaction')->get_transaction_types())
            ->set('payment_statuses', ORM::factory('Kes_Payment')->get_payment_status());

        $this->template->body = $view;
        $this->template->sidebar->breadcrumbs = array(
            array('name' => __('Home'), 'link' => '/admin'),
            array('name' => __('Account'), 'link' => '/accounts.html')
        );
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
    }

    public function action_wishlist()
    {
        if (!$this->linked_to_contact) {
            IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
            $this->request->redirect('admin');
        }
        $user = Auth::instance()->get_user();
        if (Auth::instance()->has_access('contacts3_limited_view')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        } else {
            $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        $wishlist = array();
        foreach ($contact_ids as $contact_id) {
            $wishlist[$contact_id] = Model_KES_Wishlist::search(array('contact_id' => $contact_id));
        }
        $view = View::factory('admin/wishlist');
        $view->wishlist = $wishlist;
        $view->contacts = $contacts;

        $this->template->body = $view;
        $this->template->sidebar->breadcrumbs = array(
            array('name' => __('Home'), 'link' => '/admin'),
            array('name' => __('Wishlist'), 'link' => '/wishlist.html')
        );
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
    }

    public function action_wishlist_add()
    {
        $contact_id = $this->request->post('contact_id');
        $schedule_id = $this->request->post('schedule_id');
        $course_id = $this->request->post('course_id');
        $timeslot_id = $this->request->post('timeslot_id');
        if (!$contact_id && $this->contact) {
            $contact_id = $this->contact->get_id();
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        if ($contact_id) {
            $result = array(
                'id' => Model_KES_Wishlist::add($contact_id, $course_id, $schedule_id, $timeslot_id)
            );
        } else {
            $result = array('id' => false);
        }
        echo json_encode($result);
    }

    public function action_wishlist_remove()
    {
        $contact_id = $this->request->post('contact_id');
        $course_id = $this->request->post('course_id');
        $schedule_id = $this->request->post('schedule_id');
        $timeslot_id = $this->request->post('timeslot_id');
        if (!$contact_id && $this->contact) {
            $contact_id = $this->contact->get_id();
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        if ($contact_id) {
            $result = array(
                'removed' => Model_KES_Wishlist::remove($contact_id, $course_id, $schedule_id, $timeslot_id)
            );
        } else {
            $result = array('removed' => false);
        }
        echo json_encode($result);
    }

    public function action_bookings()
    {
        $user = Auth::instance()->get_user();
        if (Auth::instance()->has_access('contacts3_limited_bookings_linked_contacts')) {
            $signed_in_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
            $contacts = Model_KES_Bookings::get_bookings_contacts_linked_to_contact($signed_in_contact['id']);
            $view = View::factory('admin/my_linked_bookings');
            $this->template->scripts[] = '<script src="' . URL::overload_asset('js/educate_template.js', ['cachebust' => true]) .'"></script>';
            $survey_styles = array(URL::get_engine_plugin_asset('surveys', 'css/frontend/survey.css', ['cachebust' => true]) => 'screen');
            $this->template->styles = isset($this->template->styles) ? array_merge($this->template->styles, $survey_styles) : $survey_styles;
        } else if (Auth::instance()->has_access('contacts3_limited_view')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            $view = View::factory('admin/my_bookings');
        } else {
            $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
            $view = View::factory('admin/my_bookings');
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        $bookings = array();
        foreach ($contact_ids as $contact_id) {
            $linked_contact_id = (Auth::instance()->has_access('contacts3_limited_bookings_linked_contacts'))
                ? $signed_in_contact['id'] : null;
            $bookings[$contact_id] = Model_KES_Bookings::get_contact_family_bookings(null ,$contact_id,
                null, false,null, $linked_contact_id);
            foreach ($bookings[$contact_id] as $i => $booking) {
                $transactions = Model_Kes_Transaction::get_contact_transactions($contact_id, null, $booking['booking_id']);
                $bookings[$contact_id][$i]['transactions'] = $transactions;
                $bookings[$contact_id][$i]['contact_booking_info'] = $contact_booking_info = $contact_booking_info = new Model_Contacts3($contact_id);
                $bookings[$contact_id][$i]['school'] = ($contact_booking_info->get_school_id() != null) ? Model_Providers::get_provider($contact_booking_info->get_school_id()): '';
                $mobile = $contact_booking_info->get_mobile(array('components' => true));
                if (!empty($mobile) && is_array($mobile)) {
                    if (isset($mobile['full_number'])) {
                        $bookings[$contact_id][$i]['mobile'] = !empty($mobile['country_code']) ? '+' . $mobile['country_code'] . ' ' . $mobile['code'] . ' ' . $mobile['number'] : $mobile['full_number'] ;
                    } else {
                        $bookings[$contact_id][$i]['mobile'] = !empty( $mobile['country_dial_code']) ?
                            '+' . $mobile['country_dial_code'] .   ' ' . $mobile['dial_code'] . $mobile['value']
                            : $mobile['value'];
                    }
                } elseif(!empty($mobile)) {
                    $bookings[$contact_id][$i]['mobile'] = $mobile;
                } else {
                    $bookings[$contact_id][$i]['mobile'] = '';
                }
                // Do not show all attendees in the booking unless you are the primary booker
                $bookings[$contact_id][$i]['delegates'] = ($contact['id'] == $booking['student_id']) ? Model_KES_Bookings::get_delegates($booking['booking_id']) : [];
                $bookings[$contact_id][$i]['host_family'] = Model_KES_Bookings::get_linked_booking_contacts($booking['booking_id'],
                    Model_Contacts3::find_type('Host Family')['contact_type_id']);
                $bookings[$contact_id][$i]['invoice'] = $this->_get_booking_invoices($booking['booking_id'], $contact_id);
            }
        }
        $view->contacts = $contacts;
        $view->bookings = $bookings;
        $this->template->body = $view;
        $this->template->sidebar->breadcrumbs = array(
            array('name' => __('Home'), 'link' => '/admin'),
            array('name' => __('Bookings'), 'link' => '/bookings.html')
        );
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
    }

    public function action_attendance()
    {
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/attendance.js"></script>';

        $user     = Auth::instance()->get_user();
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $contact  = new Model_Contacts3($contacts[0]['id']);

        $view = array(
            'contact'                        => $contact,
            'family_id'                      => $contact->get_family_id(),
            'assets_folder_path'             => Kohana::$config->load('config')->assets_folder_path,
            'contact_privileges_preferences' => Model_Contacts3::get_contact_privileges_preferences($contact->get_id())
        );
        $this->template->body = View::factory('admin/attendance', $view);

        $this->template->sidebar->breadcrumbs = array(
            array('name' => __('Home'), 'link' => '/admin'),
            array('name' => __('Attendance'), 'link' => '/admin/contacts3/attendance')
        );
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';
        $this->template->body->allow_attendance_edit = Auth::instance()->has_access('contacts3_frontend_attendance_edit') || Session::instance()->get('allow_attendance_edit');
        $this->template->body->ask_attendance_auth = !Auth::instance()->has_access('contacts3_frontend_attendance_edit') && Auth::instance()->has_access('contacts3_frontend_attendance_edit_auth');

        $styles = array(URL::get_engine_assets_base().'css/elegant_icons.css' => 'screen');
        $this->template->styles = isset($this->template->styles) ? array_merge($this->template->styles, $styles) : $styles;
    }

    public function action_ajax_get_contact_attendance_block()
    {
        $this->auto_render = FALSE;
        $contact_id = $this->request->post('contact_id');
        $date       = $this->request->post('date');
        $filters    = $this->request->post('filters');

        $current_user      = Auth::instance()->get_user();
        $contacts          = Model_Contacts3::get_contact_ids_by_user($current_user['id']);
        $logged_in_contact = (isset($contacts[0])) ? new Model_Contacts3($contacts[0]['id']) : null;

        $modelBookings = new Model_KES_Bookings();
        $bookedDays    = $modelBookings->get_days_row($contact_id, $date, $filters);

        if ($filters && $bookedDays && count($bookedDays)) {
            $bookedCourses = $bookedDays[0]['classes'];
        } else {
            $bookedCourses = $bookedDays ? $modelBookings->get_classes_by_day($contact_id, $date, $filters) : false;
        }

        $view = array(
            'bookedCourses'       => $bookedCourses,
            'bookedDays'          => $bookedDays,
            'contact'             => new Model_Contacts3($contact_id),
            'date'                => $date,
            'filtered_statistics' => $modelBookings->get_statistics($contact_id, $filters),
            'filters'             => $filters,
            'logged_in_contact'   => $logged_in_contact,
            'statistics'          => $modelBookings->get_statistics($contact_id),
            'allow_attendance_edit' => Auth::instance()->has_access('contacts3_frontend_attendance_edit') || Session::instance()->get('allow_attendance_edit'),
            'ask_attendance_auth' => !Auth::instance()->has_access('contacts3_frontend_attendance_edit') && Auth::instance()->has_access('contacts3_frontend_attendance_edit_auth')
        );

        echo View::factory('admin/snippets/attendance_contact_block', $view);
    }

    public function action_send_attendance_parent_auth_code()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $student_id = $this->contact->get_id();
        $sent = Model_KES_Bookings::send_sms_attendance_edit_auth_code($student_id);
        if (@is_numeric($sent['id'])) {
            unset ($sent['code']);
            echo json_encode($sent);
        } else {
            echo json_encode(array('error' => 'Guardian mobile does not match existing records'));
        }
    }

    public function action_attendance_auth_confirm()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $student_id = $this->contact->get_id();
        $auth_id = $this->request->post('auth_id');
        $code = $this->request->post('code');
        if (Auth::instance()->has_access('contacts3_frontend_attendance_edit_auth')) {
            $guardian_auth = Model_KES_Bookings::check_student_booking_auth($student_id, $auth_id, $code);
            if (!$guardian_auth) {
                $return = array(
                    'success' => false,
                    'error_message' => __('Guardian authorization failed.'),
                );

                echo json_encode($return);
                return;
            } else {
                Session::instance()->set('allow_attendance_edit', true);
                Model_KES_Bookings::set_student_booking_auth_validated($guardian_auth['id']);
                echo json_encode($guardian_auth);
                return;
            }
        }
        echo json_encode(array('error' => 'auth not enabled'));
    }

    public function action_ajax_get_day_classes()
    {
        $this->auto_render = FALSE;
        $contact_id = $this->request->post('contact_id');
        $date       = $this->request->post('date');
        $filters    = $this->request->post('filters');

        $current_user      = Auth::instance()->get_user();
        $contacts          = Model_Contacts3::get_contact_ids_by_user($current_user['id']);
        $logged_in_contact = (isset($contacts[0])) ? new Model_Contacts3($contacts[0]['id']) : NULL;

        $modelBookings = new Model_KES_Bookings();

        $view = array(
            'logged_in_contact' => $logged_in_contact,
            'bookedCourses' => $modelBookings->get_classes_by_day($contact_id, $date, $filters)
        );
        $this->response->body(View::factory('admin/snippets/booked_classes', $view));
    }

    public function action_ajax_save_attendance()
    {
        $this->auto_render = false;
        $booking_item_ids  = $this->request->post('id');
        $is_attending      = $this->request->post('is_attending');
        $note              = $this->request->post('note');
        if (Auth::instance()->has_access('contacts3_frontend_attendance_edit') || Session::instance()->get('allow_attendance_edit')) {
            $modelBookings = new Model_KES_Bookings();
            $attendance_saved = $modelBookings->save_attendance($booking_item_ids, $is_attending);

            if (trim($note)) {
                Model_Contacts3::save_timetable_bulk_note($booking_item_ids, $note, $is_attending);
            }
        } else {
            return false;
        }

        return $attendance_saved;
    }

    public function action_ajax_get_family_members()
    {
        $this->auto_render = FALSE;

        if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
            return false;
        }

        $user     = Auth::instance()->get_user();
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $family   = isset($contacts[0]) ? new Model_Family($contacts[0]['family_id']) : null;
        $view     = array('family' => $family);

        $this->response->body(View::factory('frontend/snippets/family_members', $view));
    }

    public function action_dashboard_note_save()
    {
        $post = $this->request->post();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $mnote = new Model_EducateNotes();
        $mnote->set_column('table_link_id', '1');
        $mnote->set_column('link_id', $post['contact_id']);
        $mnote->set_column('note', $post['note']);
        $result = $mnote->save();
        echo json_encode($result);
    }

    public function action_import_csv()
    {
        if (!Auth::instance()->has_access('contacts3')) {
            IbHelpers::set_message("You need access to the &quot;edit contact&quot; permission to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin/contacts3');
        }

        $columns = array(
            'First name',
            'Last name',
            'Gender',
            'Date of birth',
            'Email',
            'Mobile',
            'Address 1',
            'Address 2',
            'Address 3',
            'Town',
            'County',
            'Country',
            'Post code',
            'Create user',
            'User role',
            'Contact type'
        );
        $roles =
        $step = $this->request->query('step');
        $mapColumns = array();
        $post = $this->request->post();
        $encoding = isset($post['encoding']) ? $post['encoding'] : 'UTF-8';
        $delimiter = isset($post['delimiter']) ? $post['delimiter'] : ',';
        $enclosure = isset($post['enclosure']) ? $post['enclosure'] : '"';
        $tmpfile = null;
        if (!$step) {
            $step = 1;
        } else if ($step == 2) {
            if ($_FILES['csv']['error']) {
                IbHelpers::set_message('File upload failed', 'error popup_box');
                $this->request->redirect('/admin/contacts3/import_csv');
            } else {
                $tmpfile = tempnam('/tmp', 'import_csv');
                move_uploaded_file($_FILES['csv']['tmp_name'], $tmpfile);
                $mapColumns = IbHelpers::csvGetColumns(
                    $tmpfile,
                    $encoding,
                    $delimiter,
                    $enclosure
                );
            }
        } else if ($step == 3) {
            if (isset($post['start'])) {
                set_time_limit(0);
                ignore_user_abort(true);
                $csv = fopen($post['tmpfile'], "r");
                $mapColumns = $post['map'];
                $report = false;
                if ($csv) {
                    if (flock($csv, LOCK_EX)) {
                        $user = Auth::instance()->get_user();
                        $user_id = $user['id'];
                        $now = date::now();

                        $columns = fgetcsv($csv, 0, $delimiter, $enclosure);
                        $rcolumns = array_flip($columns);
                        $create_family = Settings::instance()->get('contacts_create_family') == 1;
                        $user_roles = DB::select('*')->from('engine_project_role')->execute()->as_array('id', 'role');
                        $user_roles = array_flip($user_roles);
                        while($row = fgetcsv($csv, null, $delimiter, $enclosure)) {
                            $exists = DB::select('*')
                                ->from(Model_Contacts3::CONTACTS_TABLE)
                                ->where('first_name', '=', $row[$rcolumns[$mapColumns['First name']]])
                                ->and_where('last_name', '=', $row[$rcolumns[$mapColumns['Last name']]])
                                ->and_where('delete', '=', 0)
                                ->execute()
                                ->current();
                            if ($exists) {
                                $report[] = array('key' => $row[$rcolumns[$mapColumns['First name']]] . ' ' . $row[$rcolumns[$mapColumns['Last name']]], 'action' => 'already exists', 'id' => $exists['id']);
                            } else {
                                try {
                                    $address_id = null;
                                    if ($row[$rcolumns[$mapColumns['Address 1']]]) {
                                        $residence = array(
                                            'address1' => $row[$rcolumns[$mapColumns['Address 1']]],
                                            'address2' => $row[$rcolumns[$mapColumns['Address 2']]],
                                            'address3' => $row[$rcolumns[$mapColumns['Address 3']]],
                                            'town' => $row[$rcolumns[$mapColumns['Town']]],
                                            'postcode' => $row[$rcolumns[$mapColumns['Post code']]],
                                            'country' => 'IE',
                                            'county' => '',
                                            'publish' => 1,
                                            'delete' => 0
                                        );
                                        $address_id = DB::insert('plugin_contacts3_residences')->values($residence)->execute();
                                        $address_id = $address_id[0];
                                    }

                                    $notification_group = array(
                                        'publish' => 1,
                                        'deleted' => 0,
                                        'date_created' => $now,
                                        'date_modified' => $now,
                                        'created_by' => $user_id,
                                        'modified_by' => $user_id
                                    );
                                    $notification_group_id = DB::insert('plugin_contacts3_notification_groups')->values($notification_group)->execute();
                                    $notification_group_id = $notification_group_id[0];

                                    if ($row[$rcolumns[$mapColumns['Email']]]) {
                                        $notification = array(
                                            'value' => $row[$rcolumns[$mapColumns['Email']]],
                                            'publish' => 1,
                                            'deleted' => 0,
                                            'date_created' => $now,
                                            'date_modified' => $now,
                                            'created_by' => $user_id,
                                            'modified_by' => $user_id,
                                            'notification_id' => 1,
                                            'group_id' => $notification_group_id
                                        );

                                        DB::insert('plugin_contacts3_contact_has_notifications')->values($notification)->execute();
                                    }


                                    if ($row[$rcolumns[$mapColumns['Mobile']]]) {
                                        $mobile = Model_Contacts3_Notification::parse_phone($row[$rcolumns[$mapColumns['Mobile']]]);
                                        $notification = array(
                                            'value' => $mobile['phone'],
                                            'country_dial_code' => $mobile['country_code'],
                                            'dial_code' => $mobile['area_code'],
                                            'publish' => 1,
                                            'deleted' => 0,
                                            'date_created' => $now,
                                            'date_modified' => $now,
                                            'created_by' => $user_id,
                                            'modified_by' => $user_id,
                                            'notification_id' => 2,
                                            'group_id' => $notification_group_id
                                        );

                                        DB::insert('plugin_contacts3_contact_has_notifications')->values($notification)->execute();
                                    }

                                    $family_id = null;
                                    if ($create_family) {
                                        $rfamily = array(
                                            'family_name' => $row[$rcolumns[$mapColumns['Last name']]],
                                            'publish' => 1,
                                            'delete' => 0,
                                            'date_created' => $now,
                                            'date_modified' => $now,
                                            'created_by' => $user_id,
                                            'modified_by' => $user_id
                                        );

                                        $rfamily['residence'] = $contact['residence'] = $address_id;
                                        $family_id = DB::insert('plugin_contacts3_family')->values($rfamily)->execute();
                                        $family_id = $family_id[0];
                                    }

                                    $type = $row[$rcolumns[$mapColumns['Contact type']]];
                                    $type_id = Model_Contacts3::get_contact_type_by_name($type)['contact_type_id'];
                                    $contact = array(
                                        'title' => '',
                                        'type' => $type_id,
                                        'subtype_id' => 1,
                                        'first_name' => $row[$rcolumns[$mapColumns['First name']]],
                                        'last_name' => $row[$rcolumns[$mapColumns['Last name']]],
                                        'date_of_birth' => $row[$rcolumns[$mapColumns['Date of birth']]],
                                        'gender' => $row[$rcolumns[$mapColumns['Gender']]][0],
                                        'residence' => $address_id,
                                        'family_id' => $family_id,
                                        'notifications_group_id' => $notification_group_id,
                                        'is_primary' => 1,
                                        'publish' => 1,
                                        'delete' => 0,
                                        'date_created' => $now,
                                        'date_modified' => $now,
                                        'created_by' => $user_id,
                                        'modified_by' => $user_id
                                    );

                                    $contact_id = DB::insert('plugin_contacts3_contacts')->values($contact)->execute();
                                    $contact_id = $contact_id[0];

                                    $create_user = strtolower(@$row[$rcolumns[$mapColumns['Create user']]]) == 'yes';
                                    if ($create_user) {
                                        $user_inserted = DB::insert('engine_users')
                                            ->values(
                                                array(
                                                    'email' => $row[$rcolumns[$mapColumns['Email']]],
                                                    'password' => '!',
                                                    'name' => $row[$rcolumns[$mapColumns['First name']]],
                                                    'surname' => $row[$rcolumns[$mapColumns['Last name']]],
                                                    'role_id' => $user_roles[$row[$rcolumns[$mapColumns['User role']]]],
                                                    'email_verified' => 1,
                                                    'can_login' => 1,
                                                    'status' => 1,
                                                    'deleted' => 0,
                                                    'register_source' => 'import'
                                                )
                                            )->execute();

                                        DB::update('plugin_contacts3_contacts')
                                            ->set(array('linked_user_id' => $user_inserted[0]))
                                            ->where('id', '=', $contact_id)
                                            ->execute();
                                    }

                                    $report[] = array(
                                        'key' => $row[$rcolumns[$mapColumns['First name']]] . ' ' . $row[$rcolumns[$mapColumns['Last name']]],
                                        'action' => 'insert',
                                        'id' => $contact_id
                                    );
                                } catch (Exception $exc) {
                                    $report[] = array(
                                        'key' => $row[$rcolumns[$mapColumns['First name']]] . ' ' . $row[$rcolumns[$mapColumns['Last name']]],
                                        'action' => 'error',
                                        'id' => $contact_id
                                    );
                                }
                            }
                        }
                        flock($csv, LOCK_UN);
                    } else {

                    }
                    fclose($csv);
                }
                if ($report !== false) {
                    unlink($post['tmpfile']);
                    $_SESSION['contacts3_import_report'] = $report;
                    $this->request->redirect('/admin/contacts3/import_csv?step=3');
                }
            } else {
                if (!isset($_SESSION['contacts3_import_report'])) {
                    $this->request->redirect('/admin/contacts3/import_csv');
                }
            }
        }

        $this->template->body = View::factory('admin/import_csv_step' . $step);
        if ($step == 3){
            $this->template->body->importReport = $_SESSION['contacts3_import_report'];
            unset($_SESSION['contacts3_import_report']);
        } else {
            $this->template->body->columns = $columns;
            $this->template->body->mapColumns = $mapColumns;
            $this->template->body->tmpfile = $tmpfile;
            $this->template->body->encoding = $encoding;
            $this->template->body->delimiter = $delimiter;
            $this->template->body->enclosure = $enclosure;
        }
    }

    public function action_sample_csv_import()
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename=sample.csv');
        echo '"First name","Last name","Gender","Date of birth","Email","Mobile","Address 1","Address 2","Address 3","Town","County","Post code","Country","Create user","User role","Contact type"
"Mary","Allen","Female","tempy@ideabubble.ie","2000-12-31","00353831234567","Thomcor house", "Mungret Street","","","Limerick","abcdef","Ireland","Yes","Student","Student"
"Michael","O\'Callaghan","Male","2000-12-31","michael@ideabubble.ie","00353831234567","Thomcor house", "Mungret Street","","","Limerick","abcdef","Ireland","Yes","Administrator","Staff"
"Tempy","Allen","Female","2000-12-31","tempy@ideabubble.ie","00353831234567","Thomcor house", "Mungret Street","","","Limerick","abcdef","Ireland","No","","Student"
';
        exit();
    }

    public function action_import_contacts2()
    {
        $t1 = microtime(1);
        session_commit();
        set_time_limit(0);
        ignore_user_abort(true);

        $contact_map = array();
        $family_map = array();

        $db = Database::instance();
        $db->begin();

        $now = date::now();
        $log = '';
        try {

            $contacts = DB::select('contacts.*')
                ->from(array('plugin_contacts_contact', 'contacts'))
                ->where('contacts.deleted', '=', 0)
                ->execute()
                ->as_array();

            foreach ($contacts as $contact) {

                $ngroup = DB::insert('plugin_contacts3_notification_groups')
                    ->values(array('date_created' => date::now(), 'date_modified' => date::now()))
                    ->execute();
                $residence = DB::insert('plugin_contacts3_residences')
                    ->values(array(
                        'address1' => $contact['address1'],
                        'address2' => $contact['address2'],
                        'address3' => $contact['address3'],
                        'county' => $contact['address4'],
                        'country' => $contact['country_id'],
                        'postcode' => $contact['postcode'],
                        'coordinates' => $contact['coordinates'],
                    ))
                    ->execute();




                $inserted = DB::insert('plugin_contacts3_contacts')
                    ->values(
                        array(
                            'id' => $contact['id'],
                            'title' => $contact['title'],
                            'first_name' => $contact['first_name'],
                            'last_name' => $contact['last_name'],
                            'type' => 1,
                            'subtype_id' => 1,
                            'date_created' => $contact['last_modification'],
                            'publish' => $contact['publish'],
                            'delete' => 0,
                            'notifications_group_id' => $ngroup[0],
                            'residence' => $residence[0]
                        )
                    )
                    ->execute();

                if ($contact['phone']) {
                    DB::insert('plugin_contacts3_contact_has_notifications')
                        ->values(array(
                            'group_id' => $ngroup[0],
                            'value' => $contact['phone'],
                            'notification_id' => 3
                        ))
                        ->execute();
                }

                if ($contact['mobile']) {
                    DB::insert('plugin_contacts3_contact_has_notifications')
                        ->values(array(
                            'group_id' => $ngroup[0],
                            'value' => $contact['mobile'],
                            'notification_id' => 2
                        ))
                        ->execute();
                }

                if ($contact['email']) {
                    DB::insert('plugin_contacts3_contact_has_notifications')
                        ->values(array(
                            'group_id' => $ngroup[0],
                            'value' => $contact['email'],
                            'notification_id' => 1
                        ))
                        ->execute();
                }
            }
            $db->commit();
        } catch (Exception $exc) {
            $db->rollback();
            throw $exc;
        }

        $t2 = microtime(1);
        header('content-type: text/plain');
        echo "imported in " . ($t2 - $t1) . " seconds\n";
        echo $log;
        exit;
    }

    public function action_fix_linked_used_ids()
    {
        $t1 = microtime(1);
        $db = Database::instance();
        $db->begin();

        $now = date::now();
        $log = '';
        try {
            $links = DB::select('xlink.*')
                ->from(array(Model_Users::MAIN_TABLE, 'users'))
                ->join(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'xlink'))->on('users.id', '=', 'xlink.user_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'))->on('xlink.contact3_id', '=', 'contacts.id')
                ->execute()
                ->as_array();


            foreach ($links as $link) {
                DB::update(Model_Contacts3::CONTACTS_TABLE)
                    ->set(array('linked_user_id' => $link['user_id']))
                    ->where('id', '=', $link['contact3_id'])
                    ->execute();
            }
            $db->commit();
            echo 'Done';
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
            $db->rollback();
        }
        $t2 = microtime(1);
        echo sprintf("\n%.5f in seconds", $t2 - $t1);
        exit;
    }

    public function action_create_contacts_for_users()
    {
        $t1 = microtime(1);
        $db = Database::instance();
        $db->begin();

        $now = date::now();
        $log = '';
        header('content-type: text/plain');
        try {
            $users = DB::select('users.*')
                ->from(array(Model_Users::MAIN_TABLE, 'users'))
                    ->join(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'xlink'), 'left')->on('users.id', '=', 'xlink.user_id')
                ->where('xlink.user_id', 'is', null)
                ->execute()
                ->as_array();


            foreach ($users as $user) {
                echo $user['id'] . ': ' . $user['name'] . ' ' . $user['surname'] . "\n";

                Model_Contacts3::create_for_user($user);
            }
            $db->commit();
            echo 'Done';
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
            $db->rollback();
        }
        $t2 = microtime(1);
        echo sprintf("\n%.5f in seconds", $t2 - $t1);
        exit;
    }

    public function action_autocomplete_contacts()
    {
        $contacts = Model_Contacts3::autocomplete_list($this->request->query('term'), $this->request->query('type'), null, $this->request->query('subtype'), $this->request->query('user_only'));
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($contacts);
    }

    public function action_ajax_invite_contact_popup_details()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', "application/json; charset=utf-8");
        try
        {
            $response = ['popup_messages' => []];
            $contact = new Model_Contacts3_Contact($this->request->post('contact_id'));
            $messaging = new Model_Messaging();
            $user_model = new Model_Users();
            if ($contact->get_email()['value']) {
                $user_exists = $user_model->get_user_by_email($contact->get_email()['value']) ?? false;
                if ($user_exists !== false) {
                    $response['reply'] = 'invalid';
                    $response['popup_messages'][] = [
                        'message' => "User already exists with that email. \n(User ID {$user_exists['id']}",
                    ];
                } else {
                    $mm = new Model_Messaging();
                    $message_template = $mm->get_notification_template('login-invitation-email');
                    $response['contact_email'] = $contact->get_email()['value'];
                    $response['message'] = str_replace('@first_name@', $contact->first_name, $message_template['message']);
                    $response['reply'] = 'valid';
                }

            } else {
                $response['reply'] = 'invalid';
                $response['popup_messages'][] = [
                    'message' => "Please save the contact with an email and try again.",
                ];
            }
        } catch (Exception $e) {
            $response['popup_messages'][] = [
                'message' => "An error has occured while processing your request. Please notify an administrator to check the app logs.",
            ];
        }
        echo json_encode($response);
    }

    public function action_invite_contact()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', "application/json; charset=utf-8");

        $response = array();
        try
        {
            $post = $this->request->post();
            $email = $post['email'];
            $name = @$post['name'];
            $invited_contact_id = $post['contact_id'];

            $result = Model_Contacts3::invite_member($email, $invited_contact_id, $name, $invited_contact_id, true);

            $response = array(
                'success' => true,
                'sent' => $result
            );
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'msg' => $e->getMessage()
            );
        }

        echo json_encode($response);
    }

    public function action_generate_timetable_doc()
    {
        $this->auto_render = false;
        $post = $this->request->post();

        // Generate the document
        $dochelper = new Model_Docarrayhelper();

        $template_data = $dochelper->timetable($post['student_id'], @$post['from'], @$post['to'], @$post['category_id']);
        $template_data['table'] = $template_data['timeslots'];
        $document_id = Model_Files::get_file_id('timetable', Model_Files::get_directory_id_r('/templates'));
        $file = Model_Files::file_path($document_id);
        $tmp_file = tempnam(Kohana::$cache_dir, 'docgen');
  
        $doc = new IbDocx();
        $doc->processDocx($file, $template_data, $tmp_file);

        // Send an email, if the user clicked the "Email" button
        if (!empty($post['email_timetable'])) {

            $sent = false;
            try {
                $recipients  = [['target_type' => 'CMS_CONTACT3', 'target' => $post['student_id']]];
                $attachments = [['path' => $tmp_file, 'name' => 'timetable.docx']];

                $mm = new Model_Messaging();
                $sent = $mm->send_template('student-timetable', ['attachments' => $attachments], null, $recipients);
            } catch (Exception $exc) {
                Log::instance()->add(Log::ERROR, 'Error sending timetable email'."\n".$exc->getMessage()."\n".$exc->getTraceAsString());
            }
            // Return a message to say if the email was sent.
            $this->response->headers('Content-Type', "application/json; charset=utf-8");
            $message = $sent ? 'An email with the timetable attachment has been sent' : 'Error sending email';
            echo json_encode(['success' => (bool) $sent, 'message' => $message]);
        }

        // Send the file to the browser, if the user clicked the "Print" button
        if (!empty($post['print_timetable'])) {
            try {
                header('Content-disposition: attachment; filename="timetable.docx"');
                header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                readfile($tmp_file);
            } catch (Exception $exc) {
                Log::instance()->add(Log::ERROR, 'Error downloading timetable'."\n".$exc->getMessage()."\n".$exc->getTraceAsString());
                echo $exc->getTraceAsString();
            }
        }
        unlink($tmp_file);
        //echo json_encode($document->generated_documents);
    }

    public function action_ajax_update_membership()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $id = $this->request->post('id');
        if (empty($id)) {
            echo json_encode(array());
            exit;
        }
        $organisation = new Model_Contacts3($id);

        if ($organisation->get_id() > 0) {

            $cds = new Model_CDSAPI();
            $cds_account = $cds->get_account($id);

            if (empty($cds_account)) {
                $organisation->update_membership_for_organisation(false);
            } else {
                $organisation->update_membership_for_organisation(@$cds_account['sp_membershipstatus']);
                echo json_encode(array('success'=> true));
                exit;
            }
        } else {
            echo json_encode(array());
            exit;
        }

    }
}
