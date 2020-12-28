<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * Class Controller_Admin_Courses
 * @author wojtek.zderkiewicz@ideabubble.ie
 * @version 1.1
 * @component courses
 *
 * Backend controller for courses plugin
 * WMS version 2
 * @updated: 05.Sep.2013
 */

Class Controller_Admin_Courses extends Controller_Cms
{
    protected $_plugin = 'courses';

    protected $_crud_items = [
        'curriculum' => [
            'name' => 'curriculum',
            'model' => 'Course_Curriculum',
            'delete_permission' => 'courses_course_edit',
            'edit_permission'   => 'courses_course_edit',
        ],
        'learning_outcome' => [
            'name'  => 'learning outcome',
            'model' => 'Course_LearningOutcome',
            'delete_permission' => 'courses_course_edit',
            'edit_permission'   => 'courses_course_edit',
         ],
        'spec' => [
            'name'  => 'spec',
            'model' => 'Course_Spec',
            'delete_permission' => 'courses_course_edit',
            'edit_permission'   => 'courses_course_edit',
        ]
    ];

    public function before() {
        $auth = Auth::instance();

        // Temporary, until proper permissions around this section are set up
        $courses_permission_exempt = in_array($this->request->action(), ['index', 'my_courses', 'my_course', 'my_course1', 'my_course2']);
        if (!$auth->has_access('courses_limited_access') && !Auth::instance()->has_access('courses') && !$courses_permission_exempt) {
            if ($this->request->is_ajax()) {
                $this->response->status(403);
                exit();
            } else {
                IbHelpers::set_message(__('You have no permission for this page.'), 'info popup_box');
                $this->request->redirect('/admin');
            }
        }

        parent::before();

        if ($auth->has_access('courses')) {
            $this->template->sidebar = View::factory('sidebar');
            $this->template->sidebar->menus = ['Courses' => self::get_menu_links()];

            // Set up breadcrumbs
            $this->template->sidebar->breadcrumbs = [
                ['name' => 'Home',    'link' => '/admin'],
                ['name' => 'Courses', 'link' => '/admin/courses']
            ];

            switch ($this->request->action()) {
                case 'bookings':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Bookings',
                        'link' => '/admin/courses/bookings'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/generate_bookings_csv"><button type="button" class="btn" id="bookingCSV">Download CSV</button></a>';
                    break;

                case 'bookings_people':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Bookings',
                        'link' => '/admin/courses/bookings'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/generate_course_bookings_csv?schedule_id=' . $_GET['id'] . '"><button type="button" id="bookingCSV" class="btn">Download CSV</button></a>';
                    break;

                case 'autotimetables':
                case 'add_autotimetable':
                case 'edit_autotimetable':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Time Tables',
                        'link' => '/admin/courses/autotimetables'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_autotimetable"><button class="btn" type="button">' . __('Add Time Table') . '</button></a>';
                    break;

                case 'schedules':
                case 'add_schedule':
                case 'edit_schedule':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Schedules',
                        'link' => '/admin/courses/schedules'
                    );
					$this->template->sidebar->tools = '<a href="/admin/courses/add_schedule"><button class="btn" type="button">' . __('Add Schedule') . '</button></a>';
                    break;

                case 'subjects':
                case 'add_subject':
                case 'edit_subject':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Subjects',
                        'link' => '/admin/courses/subjects'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_subject"><button class="btn" type="button">' . __('Add Subject') . '</button></a>';
                    break;

                case 'categories':
                case 'add_category':
                case 'edit_category':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Categories',
                        'link' => '/admin/courses/categories'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_category"><button class="btn" type="button">' . __('Add Category') . '</button></a>';
                    break;

                case 'locations':
                case 'add_location':
                case 'edit_location':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Locations',
                        'link' => '/admin/courses/locations'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_location"><button class="btn" type="button">' . __('Add Location') . '</button></a>';
                    break;

                case 'providers':
                case 'add_provider':
                case 'edit_provider':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Providers',
                        'link' => '/admin/courses/providers'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_provider"><button class="btn" type="button">' . __('Add Provider') . '</button></a>';
                    break;

                case 'study_modes':
                case 'add_study_mode':
                case 'edit_study_mode':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Study Modes',
                        'link' => '/admin/courses/study_modes'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_study_mode"><button class="btn" type="button">' . __('Add Study Mode') . '</button></a>';
                    break;

                case 'types':
                case 'add_type':
                case 'edit_type':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Types',
                        'link' => '/admin/courses/types'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_type"><button class="btn" type="button">' . __('Add Type') . '</button></a>';
                    break;

                case 'levels':
                case 'add_level':
                case 'edit_level':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Levels',
                        'link' => '/admin/courses/levels'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_level"><button class="btn" type="button">' . __('Add Level') . '</button></a>';
                    break;

                case 'years':
                case 'add_year':
                case 'edit_year':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Years',
                        'link' => '/admin/courses/years'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_year"><button class="btn" type="button">' . __('Add Year') . '</button></a>';
                    break;
                case 'topics':
                case 'add_topic':
                case 'edit_topic':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Topics',
                        'link' => '/admin/courses/topics'
                    );
                $this->template->sidebar->tools = '<a href="/admin/courses/add_topic"><button class="btn" type="button">'.__('ADD TOPICS').'</button></a>';
                    break;

                case 'academic_years':
                case 'add_edit_academic_year':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Academic Years',
                        'link' => '/admin/courses/academic_years'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_edit_academic_year"><button class="btn" type="button">' . __('Add Academic Year') . '</button></a>';
                    break;

                case 'discounts':
                    $this->template->sidebar->tools = '<a href="/admin/courses/edit_discount"><button class="btn" type="button">' . __('Add Discount') . '</button></a>';
                    break;
                case 'zones':
                case 'add_zone':
                case 'edit_zone':
                    $this->template->sidebar->breadcrumbs[] = array(
                        'name' => 'Zones',
                        'link' => '/admin/courses/zones'
                    );
                    $this->template->sidebar->tools = '<a href="/admin/courses/add_zone"><button class="btn" type="button">'.__('ADD ZONE').'</button></a>';
                    break;

                default:
                    if (Auth::instance()->has_access('courses_course_edit')) {
                        $this->template->sidebar->tools = '<a href="/admin/courses/add_course"><button class="btn" type="button">' . __('Add Course') . '</button></a>';
                    }
                    break;
            }
        }
    }

    /**
     * default action
     */
    public function action_index()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_course_edit') && $auth->has_access('courses_view_mycourses')) {
            $this->request->redirect('/admin/courses/my_courses');
        } elseif ($auth->has_access('courses_limited_access')){
            $this->action_bookings();
        } else {
            if (!Auth::instance()->has_access('courses_course_edit')) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses') . 'css/lists.css'] = 'screen';
            //additional scripts
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/courses_list.js"></script>';

            //get_icon
            $results['plugin'] = Model_Plugin::get_plugin_by_name('courses');

            //select template to display
            $this->template->body = View::factory('list_courses', $results);
        }
    }

    public function get_menu_links()
    {
        $auth = Auth::instance();
        $menu = [];
        if ($auth->has_access('courses')) {
            if ($auth->has_access('courses_course_edit')) {
                $menu[] = [
                    'name' => 'Courses',
                    'link' => '/admin/courses',
                    'icon' => 'courses'
                ];
            }

            if ($auth->has_access('courses_view_mycourses')) {
                $menu[] = [
                    'name' => 'My Courses',
                    'link' => '/admin/courses/my_courses',
                    'icon' => 'courses'
                ];
            }

            if (Settings::instance()->get('courses_enable_bookings') == 1 && !Model_Plugin::is_enabled_for_role('Administrator', 'bookings') && $auth->has_access('courses_booking_edit')) {
                $menu[] = [
                    'name' => 'Bookings',
                    'link' => '/admin/courses/bookings',
                    'icon' => 'booking'
                ];
            }

            if ($auth->has_access('courses_schedule_edit')) {
                $menu[] = [
                    'name' => 'Schedules',
                    'link' => '/admin/courses/schedules',
                    'icon' => 'schedule'
                ];
            }

            if ($auth->has_access('courses_schedule_edit_limited')) {
                $menu[] = [
                    'name' => 'My Schedules',
                    'link' => '/admin/courses/schedules',
                    'icon' => 'calendar'
                ];
            }

            if ($auth->has_access('courses_timetable_edit')) {
                $menu[] = [
                    'name' => 'Time Tables',
                    'link' => '/admin/courses/autotimetables',
                    'icon' => 'timetable'
                ];
            }

            if ($auth->has_access('courses_category_edit')) {
                $menu[] = [
                    'name' => 'Categories',
                    'link' => '/admin/courses/categories',
                    'icon' => 'category'
                ];
            }

            if ($auth->has_access('courses_subject_edit')) {
                $menu[] = [
                    'name' => 'Subjects',
                    'link' => '/admin/courses/subjects',
                    'icon' => 'exams'
                ];
            }

            if ($auth->has_access('courses_location_edit')) {
                $menu[] = [
                    'name' => 'Locations',
                    'link' => '/admin/courses/locations',
                    'icon' => 'location'
                ];
            }

            if ($auth->has_access('courses_location_edit_limited')) {
                $menu[] = [
                    'name' => 'My Locations',
                    'link' => '/admin/courses/locations',
                    'icon' => 'location'
                ];
            }

            if ($auth->has_access('courses_provider_edit')) {
                $menu[] = [
                    'name' => 'Providers',
                    'link' => '/admin/courses/providers',
                    'icon' => 'contacts'
                ];
            }

            if ($auth->has_access('courses_studymode_edit')) {
                $menu[] = [
                    'name' => 'Study Modes',
                    'link' => '/admin/courses/study_modes',
                    'icon' => 'study-mode'
                ];
            }

            if ($auth->has_access('courses_type_edit')) {
                $menu[] = [
                    'name' => 'Types',
                    'link' => '/admin/courses/types',
                    'icon' => 'type'
                ];
            }

            if ($auth->has_access('courses_level_edit')) {
                $menu[] = [
                    'name' => 'Levels',
                    'link' => '/admin/courses/levels',
                    'icon' => 'levels'
                ];
            }

            if ($auth->has_access('courses_year_edit')) {
                $menu[] = [
                    'name' => 'Years',
                    'link' => '/admin/courses/years',
                    'icon' => 'years'
                ];
            }

            if ($auth->has_access('courses_academicyear_edit')) {
                $menu[] = [
                    'name' => 'Academic Years',
                    'link' => '/admin/courses/academic_years',
                    'icon' => 'academic-year'
                ];
            }

            if ($auth->has_access('courses_registration_edit') && Settings::instance()->get('courses_enable_registrations') == 1 && Model_Plugin::get_isplugin_enabled_foruser('current', 'homework')) {
                $menu[] = [
                    'name' => 'Registrations',
                    'link' => '/admin/courses/student_schedule_registrations',
                    'icon' => 'registration'
                ];
            }

            if (Settings::instance()->get('courses_enable_bookings') == 1 && !Model_Plugin::is_enabled_for_role('Administrator', 'bookings') && $auth->has_access('courses_booking_edit')) {
                $menu[] = [
                    'name' => 'Discounts',
                    'link' => '/admin/courses/discounts',
                    'icon' => 'discounts'
                ];
            }

            if ($auth->has_access('courses_topic_edit')) {
                $menu[] = [
                    'name' => 'Topics',
                    'link' => '/admin/courses/topics',
                    'icon' => 'topics'
                ];
            }

            if ($auth->has_access('courses_zone_edit')) {
                $menu[] = [
                    'name' => 'Zones',
                    'link' => '/admin/courses/zones',
                    'icon' => 'zones'
                ];
            }

            if ($auth->has_access('courses_credits')) {
                $menu[] = [
                    'name' => 'Credits',
                    'link' => '/admin/courses/credits',
                    'icon' => 'credit'
                ];
            }

            if ($auth->has_access('courses_course_edit')) { // replace with curriculum permission when set up
                $menu[] = [
                    'name' => 'Curriculums',
                    'link' => '/admin/courses/curriculums',
                    'icon' => 'all-applicants'
                ];
            }

            if ($auth->has_access('courses_course_edit')) { // replace with spec permission when set up
                $menu[] = [
                    'name' => 'Specs',
                    'link' => '/admin/courses/specs',
                    'icon' => 'surveys'
                ];
            }

            if ($auth->has_access('courses_course_edit')) { // replace with learning outcome permission when set up
                $menu[] = [
                    'name' => 'Learning outcomes',
                    'link' => '/admin/courses/learning_outcomes',
                    'icon' => 'surveys'
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

    public function action_add_course()
    {
        // If the user doesn't have permission, redirect them away and display a notice.
        IbHelpers::permission_redirect('courses_course_edit');

        try
        {
            $years         = Model_Years::get_all_years();
			$levels        = Model_Levels::get_all_levels();
			$categories    = Model_Categories::get_all_categories();
			$types         = Model_Types::get_all_types();
            $topics        = Model_Topics::get_all_topics();
            $subjects      = Model_Subjects::get_all_subjects();
			$providers     = Model_Providers::get_all_providers();
			$accreditation_bodies = Model_Providers::get_accreditation_bodies();
			$media         = new Model_Media();
			$documents     = $media->get_all_items_based_on('location', 'docs', 'details', '=');
			$images        = $media->get_all_items_based_on('location', Model_Courses::MEDIA_IMAGES_FOLDER, 'as_details', '=');
			$banner_preset = Model_Presets::get_preset_details('Course Banners');
			$banner_images = isset($banner_preset['id']) ? $media->get_all_items_based_on('preset_id', $banner_preset['id']) : array();
            // Additional scripts
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/courses_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            // Additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            // Select template to display
            $this->template->body      = View::factory('form_course', array(
				'years'         => $years,
				'levels'        => $levels,
				'types'         => $types,
				'topics'        => $topics,
				'categories'    => $categories,
				'subjects'      => $subjects,
				'providers'     => $providers,
				'accreditation_bodies' => $accreditation_bodies,
				'images'        => $images,
				'banner_images' => $banner_images,
				'documents'     => $documents,
                'data'          => [
                    'category_id' => '',
                    'code' => '',
                    'description' => '',
                    'file_id' => '',
                    'has_providers' => [],
                    'level_id' => '',
                    'publish' => 1,
                    'summary' => '',
                    'subject_id' => '',
                    'title' => '',
                    'type_id' => '',
                    'year_ids' => [],
                ]
            ));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/courses');
        }

    }

    public function action_edit_course()
    {
        // If the user doesn't have permission, redirect them away and display a notice.
        IbHelpers::permission_redirect('courses_course_edit');

        try
        {
			$years         = Model_Years::get_all_years();
			$levels        = Model_Levels::get_all_levels();
			$categories    = Model_Categories::get_all_categories();
			$types         = Model_Types::get_all_types();
            $topics         = Model_Topics::get_all_topics();
			$subjects      = Model_Subjects::get_all_subjects();
            $curriculums   = ORM::factory('Course_Curriculum')->find_all_undeleted();
			$providers     = Model_Providers::get_all_providers();
            $accreditation_bodies = Model_Providers::get_accreditation_bodies();
            $media         = new Model_Media();
			$documents     = $media->get_all_items_based_on('location', 'docs', 'details', '=');
			$images        = $media->get_all_items_based_on('location', Model_Courses::MEDIA_IMAGES_FOLDER, 'as_details', '=');
			$banner_preset = Model_Presets::get_preset_details('Course Banners');
			$banner_images = isset($banner_preset['id']) ? $media->get_all_items_based_on('preset_id', $banner_preset['id']) : array();
            $data          = Model_Courses::get_course(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            // Additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/courses_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/schedules_list.js"></script>';
            // fnFilterClear library
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/fnFilterClear.js"></script>';
            // Additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            // Select template to display
            $this->template->body      = View::factory('form_course', array(
                'years'         => $years,
				'levels'        => $levels,
				'types'         => $types,
				'topics'         => $topics,
				'categories'    => $categories,
				'subjects'      => $subjects,
                'curriculums'   => $curriculums,
				'providers'     => $providers,
                'accreditation_bodies' => $accreditation_bodies,
                'documents'     => $documents,
				'images'        => $images,
				'banner_images' => $banner_images,
				'data'          => $data
            ));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses');
        }
    }


    public function action_ajax_publish_course()
    {
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Courses::set_publish_course($id, $state));
        exit;
    }


    public function action_ajax_remove_course()
    {
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Courses::remove_course($id));
        exit;
    }

    public function action_remove_course()
    {
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $response = Model_Courses::remove_course($id);
        if ($response['message'] === 'success') {
            IbHelpers::set_message("Course successfully deleted", 'success popup_box');
            $return = array('redirect' => '/admin/courses');
        } else {
            IbHelpers::set_message($response['error_msg'], 'warning popup_box');
            $return = array('redirect' => "/admin/courses/edit_course?id={$id}");
        }
        echo json_encode($return);
        exit;
    }

    public function action_save_course()
    {
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        $redirect = $_POST['redirect'];
        $id = Model_Courses::save_course($data);
        if($redirect == 'save_and_exit')
        {
            $this->request->redirect('/admin/courses');
        }
        else
        {
            $this->request->redirect('/admin/courses/edit_course/?id='.$id);
        }
    }


    public function action_duplicate_course()
    {
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_GET['id'];
        Model_Courses::duplicate_course($id);
        $this->request->redirect('/admin/courses');
    }

    public function action_get_course_by_selected_categories_subjects()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();
        $categories = json_decode($data['categories']);
        $subjects    = json_decode($data['subjects']);
        $courses     = json_decode($data['courses']);
        $courses = is_array($courses)?$courses:(array)$courses;
        $courses_subjects = Model_Courses::get_courses_based_on_selected_category_subject($categories,$subjects);

        $result = '<option value="0">Any Courses</option>';
        foreach ( $courses_subjects as $key=>$subject)
        {
            $selected = (in_array($subject['id'],$courses)) ? '" selected="selected">' : '">';
            $result.='<option value="'.$subject['id'].$selected.$subject['title'].'</option>';
        }

        echo $result ;
    }

    public function action_categories()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/categories_list.js"></script>';
        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //select template to display
        $this->template->body = View::factory('list_categories');
    }

    public function action_add_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $media = new Model_Media();
            $documents = $media->get_all_items_based_on('location', Model_Courses::MEDIA_IMAGES_FOLDER, 'as_details', '=');
            $categories = Model_Categories::get_categories_without_parent();
            //additional scripts
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/categories_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/codemirror/merged/codemirror.js"></script>';
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/jquery.datetimepicker.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/timepicker.css'] = 'screen';
            $this->template->styles[URL::get_engine_assets_base().'js/codemirror/merged/codemirror.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_category', array('categories' => $categories, 'documents' => $documents));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/categories');
        }

    }

    public function action_edit_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $media = new Model_Media();
            $documents = $media->get_all_items_based_on('location', Model_Courses::MEDIA_IMAGES_FOLDER, 'as_details', '=');
            $categories = Model_Categories::get_categories_without_parent();
            $data = Model_Categories::get_category(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/categories_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/codemirror/merged/codemirror.js"></script>';
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/jquery.datetimepicker.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/timepicker.css'] = 'screen';
            $this->template->styles[URL::get_engine_assets_base().'js/codemirror/merged/codemirror.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_category', array('categories' => $categories, 'data' => $data, 'documents' => $documents));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/categories');
        }
    }


    public function action_ajax_publish_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Categories::set_publish_category($id, $state));
        exit;
    }

    public function action_ajax_tutorial_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int) $_POST['state'];
        echo json_encode(Model_Categories::set_tutorial_category($id,$state));
        exit;
    }

    public function action_ajax_remove_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Categories::remove_category($id));
        exit;
    }

    public function action_remove_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        Model_Categories::remove_category($id);
        IbHelpers::set_message('<strong>Success: </strong> Category is successfully removed.', 'success popup_box');
        $return = array('redirect' => '/admin/courses/categories');
        echo json_encode($return);
        exit;
    }

    public function action_save_category()
    {
        if (!Auth::instance()->has_access('courses_category_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['category'])) < 1)
        {
            $categories = Model_Categories::get_categories_without_parent();
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/categories_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_category', array('categories' => $categories, 'data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Categories::save_category($data);
            $location = ($redirect == 'save_and_exit') ? 'categories' : 'edit_category/?id='.$id;
            $this->request->redirect('/admin/courses/'.$location);
        }
    }

    public function action_subjects()
    {
        if (!Auth::instance()->has_access('courses_subject_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses').'js/subjects_list.js"></script>';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        $this->template->body      = View::factory('list_subjects');
    }

    public function action_add_subject()
    {
        self::action_edit_subject();
    }

    public function action_edit_subject()
    {
        if (!Auth::instance()->has_access('courses_subject_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try {
            $id                        = $this->request->param('id');
            $data                      = Model_Subjects::get_subject($id);

            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2-en.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media').'js/multiple_upload.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media').'js/image_edit.js"></script>';
			$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/spectrum.min.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/subject_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

            $this->template->styles[URL::get_engine_plugin_assets_base('events').'css/validation.css'] = 'screen';
			$this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
			$this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/spectrum.min.css'] = 'screen';
            $this->template->body      = View::factory('form_subject')
                ->set('data', $data)
                ->set('subject_object', new Model_Course_Subject($data['id']));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/subjects');
        }
    }

    public function action_save_subject()
    {
        if (!Auth::instance()->has_access('courses_subject_edit')) {
            IbHelpers::set_message("You need access to the &quot;courses_subject_edit&quot; permission to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $this->request->post();

        $redirect = $_POST['redirect'];
        $subject  = ORM::factory('Course_Subject')->where('id', '=', $data['id'])->find_undeleted();
        $subject->values($data);
        $subject->save_with_moddate($data);
        IbHelpers::set_message ('Subject #'.$subject->id.': '.$subject->name.' has been saved', 'success popup_box');

        $location = ($redirect == 'save_and_exit') ? 'subjects' : 'edit_subject/'.$subject->id;
        $this->request->redirect('/admin/courses/'.$location);
    }

    public function action_delete_subject()
    {
        $id = $this->request->param('id');
        if (!Auth::instance()->has_access('courses_subject_edit')) {
            IbHelpers::set_message("You need access to the &quot;course_subject_edit&quot; permission to perform this action", 'warning popup_box');
            $this->request->redirect('/admin/courses/add_edit_subject/'.$id);
        } else {
            Model_Subjects::delete($id);
            IBHelpers::set_message('Subject has been deleted', 'success popup_box');
            $this->request->redirect('/admin/courses/subjects');
        }
    }

    public function action_ajax_get_subjects()
    {
        $post = $this->request->post();
        (isset($post['sEcho'])) ? $return['sEcho'] = $post['sEcho'] : NULL;
        $return['iTotalRecords'] = Model_Subjects::count_subjects($post['sSearch']);

        $sort = 'date_modified';
        switch ($post['iSortCol_0'])
        {
            case 0: $sort = 'id';            break;
            case 1: $sort = 'color';         break;
            case 2: $sort = 'name';          break;
            case 3: $sort = 'publish';       break;
            case 4: $sort = 'delete';        break;
            case 5: $sort = 'date_created';  break;
            case 6: $sort = 'date_modified'; break;
        }

        // Get data for response
        $return['aaData']               = Model_Subjects::get_datatable($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];

        echo json_encode($return);
        exit;
    }

    public function action_ajax_publish_subject()
    {
        if (!Auth::instance()->has_access('courses_subject_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id    = (int) $this->request->post('id');
        $state = (int) $this->request->post('state');
        echo json_encode(Model_Subjects::set_publish($id, $state));
        exit;
    }


    public function action_ajax_delete_subject()
    {
        if (!Auth::instance()->has_access('courses_subject_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $id    = (int) $this->request->post('id');
        echo json_encode(Model_Subjects::delete($id));
    }


    public function action_locations()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/locations_list.js"></script>';
        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //select template to display
        $this->template->body = View::factory('list_locations');
    }

    public function action_add_location()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
            $locations = Model_Locations::get_locations_without_parent($auth->has_access('courses_schedule_edit') ? null : $user['id']);
            $counties = Model_Cities::get_counties();
            $types = Model_Locations::get_location_types();
            $rows = array();
            //additional scripts
            $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . Settings::instance()->get('google_map_key') . '&libraries=places&sensor=false"></script>';
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/locations_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_location', array('locations' => $locations, 'counties' => $counties, 'types' => $types, 'rows' => $rows, 'data' => array()));
    }

    public function action_edit_location()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
            $locations = Model_Locations::get_locations_without_parent($auth->has_access('courses_schedule_edit') ? null : $user['id']);
            $data = Model_Locations::get_location(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            if (!$auth->has_access('courses_location_edit') && $data['owned_by'] != $user['id']) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
            $counties = Model_Cities::get_counties();
            $types = Model_Locations::get_location_types();
            $rows = Model_Locations::get_rows(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            //additional scripts
            $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . Settings::instance()->get('google_map_key') . '&libraries=places&sensor=false"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/locations_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_location', array('locations' => $locations, 'data' => $data, 'counties' => $counties, 'types' => $types, 'rows' => $rows));
    }


    public function action_ajax_remove_location()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $data = Model_Locations::get_location($id);
        if (!$auth->has_access('courses_location_edit') && $data['owned_by'] != $user['id']) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        echo json_encode(Model_Locations::remove_location($id));
        exit;
    }

    public function action_remove_location()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $data = Model_Locations::get_location($id);
        if (!$auth->has_access('courses_location_edit') && $data['owned_by'] != $user['id']) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        Model_Locations::remove_location($id);
        IbHelpers::set_message('<strong>Success: </strong> Location is successfully removed.', 'success popup_box');
        $return = array('redirect' => '/admin/courses/locations');
        echo json_encode($return);
        exit;
    }

    public function action_ajax_get_cities_for_county()
    {
        $id = (int)$_POST['id'];
        echo Model_Cities::get_cities_for_county_html($id);
        exit;
    }

    public function action_save_location()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        $data = $_POST;

        if (!$auth->has_access('courses_location_edit')) {
            $id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
            if (is_numeric($id)) {
                $data = Model_Locations::get_location();
                if ($data['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }
            $data['owned_by'] = $user['id'];
        }

        if (strlen(trim($data['name'])) < 1)
        {
            $locations = Model_Locations::get_locations_without_parent();
            $data = Model_Locations::get_location(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $counties = Model_Cities::get_counties();
            $types = Model_Locations::get_location_types();
            //additional scripts
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/locations_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_location', array('locations' => $locations, 'data' => $data, 'counties' => $counties, 'types' => $types, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $json_response = @$data['modal'] == 1;
            unset($data['modal']);
            $redirect = $_POST['redirect'];
            $id       = Model_Locations::save_location($data);
            if ($json_response) {
                $this->auto_render = false;
                $this->response->headers('Content-Type', 'application/json; charset=utf-8');
                echo json_encode(array('id' => $id));
            } else {
                $location = ($redirect == 'save_and_exit') ? 'locations' : 'edit_location/?id=' . $id;
                $this->request->redirect('/admin/courses/' . $location);
            }
        }
    }
    
    public function action_providers()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/providers_list.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_providers');
    }

    public function action_add_provider()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $counties = Model_Cities::get_counties();
            $types = Model_Providers::get_all_types();
            $r = new Model_Roles();
            $franchisees = Model_Users::search(array('role_id' => $r->get_id_for_role('Franchisee')));
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/providers_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_provider', array('counties' => $counties, 'types' => $types, 'franchisees' => $franchisees));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/providers');
        }

    }

    public function action_edit_provider()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $data = Model_Providers::get_provider(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $counties = Model_Cities::get_counties();
            $types = Model_Providers::get_all_types();
            $r = new Model_Roles();
            $franchisees = Model_Users::search(array('role_id' => $r->get_id_for_role('Franchisee')));

            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/providers_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_provider', array('data' => $data, 'counties' => $counties, 'types' => $types, 'franchisees' => $franchisees));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/providers');
        }
    }


    public function action_ajax_remove_provider()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Providers::remove_provider($id));
        exit;
    }

    public function action_remove_provider()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        Model_Providers::remove_provider($id);
        IbHelpers::set_message('<strong>Success: </strong> Provider is successfully removed.', 'success popup_box');
        $return = array('redirect' => '/admin/courses/providers');
        echo json_encode($return);
        exit;
    }


    public function action_save_provider()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['name'])) < 1)
        {
            $data = Model_Providers::get_provider(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $counties = Model_Cities::get_counties();
            //additional scripts
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/providers_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_provider', array('data' => $data, 'counties' => $counties, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Providers::save_provider($data);
            $location = ($redirect == 'save_and_exit') ? 'providers' : 'edit_provider/?id='.$id;
            $this->request->redirect('/admin/courses/'.$location);
        }
    }



    public function action_ajax_publish_provider()
    {
        if (!Auth::instance()->has_access('courses_provider_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Providers::set_publish_provider($id, $state));
        exit;
    }

	public function action_cleanup_providers()
	{
		$post = $this->request->post();
		$step = @$post['step'] ? $post['step'] : 1;
	
		if($step == 2){
			Model_Providers::cleanup_schools($post);
		}
		if($step == 1 || $step == 2){
			$suggestions = Model_Providers::cleanup_schools_get_suggestions();
			$optschools = $suggestions['schools'];
			usort($optschools, function($s1, $s2){
				return $s1['id'] == $s2['id'] ? 0 : ($s1['id'] < $s2['id'] ? 1 : -1);
			});
			usort($suggestions['schools'], function($s1, $s2){
				return strcasecmp($s1['name'], $s2['name']);
			});
			if($step == 2){
				echo '<h1>replacements done!</h1>';
			}
			echo '<form method="post">';
			echo '<input type=hidden name=step value=2>';
			echo '<table id="replacements">';
			echo '<thead><tr><th>School</th><th>Replace</th></tr></thead>';
			echo '<tbody></tbody>';
			foreach($suggestions['schools'] as $school){
				echo '<tr><td align="right">#'. $school['id'] . ':<input type="text" name="name[' . $school['id'] . ']" value="' . htmlspecialchars($school['name']) . '" size=50/></td><td>';
				echo '<select name="replace[' . $school['id'] . ']"><option value=""></option>';
				foreach($optschools as $optschool){
					echo '<option value="' . $optschool['id'] . '"' . (@$suggestions['replaced'][$school['id']] == $optschool['id'] ? '  selected="selected"' : '') . '>' . $optschool['id'] . ': ' . $optschool['name'] . '</option>';
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

    public function action_schedules()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/schedules_list.js"></script>';
		// fnFilterClear library 
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/fnFilterClear.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_schedules');
    }

    public function action_add_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        Model_Courses::update_schedule_status();
            $engineCalendarEvents = Model_Calendar_Event::getEventList('courses');
            $blackout_calendar_event_ids = array();
            foreach($engineCalendarEvents as $engineCalendarEvent){
                $blackout_calendar_event_ids[] = $engineCalendarEvent['id'];
            }
            $calendar_dates = Model_Schedules::get_calendar_dates();
            if ($auth->has_access('courses_schedule_edit')) {
                $course_filter = array();
                $edit_all = 1;
            } else {
                $provider = Model_Franchisee::get_provider($user['id']);
                if (!$provider) {
                    IbHelpers::set_message("Unexpected error(invalid franchisee)!", 'error popup_box');
                    $this->request->redirect('/admin');
                }
                $course_filter = array('provider_id' => $provider['id']);
                $edit_all = 0;
            }
            $courses = Model_Courses::get_all_published($course_filter);

            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
            {
                $trainers = Model_Contacts3::get_teachers();
                if (!$auth->has_access('courses_schedule_edit')) {
                    $franchisee_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
                    $trainers = array($franchisee_contact);
                }

                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/contacts.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/dialog.js"></script>';
//                $locations = Model_Locations::get_parent_locations();
            }
            else
            {
                $trainers = Model_Contacts::get_trainers();
                if (!$auth->has_access('courses_schedule_edit')) {
                    $franchisee_contact = Model_Contacts::get_linked_contact_to_user($user['id']);
                    $trainers = array($franchisee_contact);
                }
//                $locations = Model_Locations::get_locations_only();
            }
            $locations = Model_Locations::get_locations_without_parent($auth->has_access('courses_schedule_edit') ? null : $user['id']);

            $schedule_zones = array();
            $zones = Model_Zones::get_all_zones();
            $location_rows = array();

            $topics = Model_Topics::get_all_topics();
            $subjects = Model_Subjects::get_all_subjects();
            $frequencies = Model_Schedulefrequencies::get_all_frequencies();
            $study_modes = Model_Studymodes::get_all_study_modes();
            $schedules = Model_Schedules::get_all_schedules();
            $academic_years = Model_AcademicYear::get_academic_years_options();
            $booking_count = 0;
            $navision_events = array();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
                $navision_events = Model_NAVAPI::event_search();
            }
        //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/bootstrap-timepicker.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/schedules_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/jquery.eventCalendar.js"></script>';
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/jquery.datetimepicker.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/jquery.timeago.js"></script>';
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/dialog.js"></script>';
            }
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/timepicker.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/eventCalendar.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/eventCalendar_theme_responsive.css'] = 'screen';

            //select template to display
            $this->template->body = View::factory(
                'form_schedule',
                array(
                    'academic_years'       => $academic_years,
                    'blackout_calendar_event_ids' => $blackout_calendar_event_ids,
                    'booking_count'        => $booking_count,
                    'calendar_dates'       => $calendar_dates,
                    'children_locations'   => array(),
                    'cloned'               => false,
                    'content'              => new Model_Content(),
                    'courses'              => $courses,
                    'delivery_modes'       => array_column(Model_Lookup::lookupList('Delivery mode'), 'label', 'id'),
                    'subjects'             => $subjects,
                    'engineCalendarEvents' => $engineCalendarEvents,
                    'frequencies'          => $frequencies,
                    'learning_modes'       => array_column(Model_Lookup::lookupList('Learning mode'), 'label', 'id'),
                    'location_rows'        => $location_rows,
                    'locations'            => $locations,
                    'parent_location_id'   => null,
                    'schedules'            => $schedules,
                    'study_modes'          => $study_modes,
                    'topics'               => $topics,
                    'trainers'             => $trainers,
                    'schedule_zones'       => $schedule_zones,
                    'zones'                => $zones,
                    'edit_all'             => $edit_all,
                    'navision_events'      => $navision_events
                )
            );
    }

    public function action_edit_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $engineCalendarEvents = Model_Calendar_Event::getEventList('courses');
        $id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
        Model_Courses::update_schedule_status($id);
        $data = Model_Schedules::get_schedule($id);
        $selected_course = null;
        if (@$data['course_id']) {
            $selected_course = Model_Courses::get_course($data['course_id']);
        }
        if (is_numeric($id) && !$auth->has_access('courses_schedule_edit') && $data['owned_by'] != $user['id']) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $schedule_zones = Model_Schedules::get_zones($data['id']);
        $zones = Model_Zones::get_all_zones();
        $location_rows = $data['location_id'] != '' ? Model_Locations::get_rows($data['location_id']) : array();

        $topics = Model_Topics::get_all_topics();
        $subjects = Model_Subjects::get_all_subjects();
        $schedule_topics = Model_Topics::get_topics(array('schedule_id' => $data['id'], 'format' => 'data'));

        $blackout_event_ids = Model_Schedules::get_schedule_blackout_events($data['id']);
        $calendar_dates = Model_Schedules::get_calendar_dates(count($blackout_event_ids) ? $blackout_event_ids : 'none');
        $cloned = isset($_GET['cloned']) ? TRUE : FALSE ;
        if ($auth->has_access('courses_schedule_edit')) {
            $course_filter = array();
            $edit_all = 1;
        } else {
            $provider = Model_Franchisee::get_provider($user['id']);
            if (!$provider) {
                IbHelpers::set_message("Unexpected error(invalid franchisee)!", 'error popup_box');
                $this->request->redirect('/admin');
            }
            $course_filter = array('provider_id' => $provider['id']);
            $edit_all = 0;
        }
        $courses = Model_Courses::get_all_published($course_filter);
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
        {
            $trainers = Model_Contacts3::get_teachers();
            if (!$auth->has_access('courses_schedule_edit')) {
                $franchisee_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
                $trainers = array($franchisee_contact);
            }
        }
        else
        {
            $trainers = Model_Contacts::get_trainers();
            if (!$auth->has_access('courses_schedule_edit')) {
                $franchisee_contact = Model_Contacts::get_linked_contact_to_user($user['id']);
                $trainers = array($franchisee_contact);
            }
        }
        $locations = Model_Locations::get_locations_without_parent($auth->has_access('courses_schedule_edit') ? null : $user['id']);
        $parent_location = $data['location_id']!='' ? Model_Locations::get_parent_location_id($data['location_id']) :NULL;
        $children_location = $data['location_id']!='' ? Model_Locations::get_children_locations($parent_location) : array();
        $frequencies = Model_Schedulefrequencies::get_all_frequencies();
        $study_modes = Model_Studymodes::get_all_study_modes();

        //additional scripts
        $schedules = Model_Schedules::get_all_schedules();
        $academic_years = Model_AcademicYear::get_academic_years_options();
        $booking_count = Model_Schedules::get_schedule_booking_count($data['id']);
        $navision_events = array();
        if (Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
            $navision_events = Model_NAVAPI::event_search();
        }
        //$study_course = Model_Schedules::get_schedule_category($data['id']);
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/bootstrap-timepicker.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/schedules_form.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/jquery.eventCalendar.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/jquery.datetimepicker.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/jquery.timeago.js"></script>';
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/dialog.js"></script>';
        }
        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/timepicker.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/eventCalendar.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/eventCalendar_theme_responsive.css'] = 'screen';
        //$this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
        //select template to display
        //header('content-type: text/plain');print_r($data);exit;
        $this->template->body = View::factory('form_schedule',
            array(
                'academic_years'       => $academic_years,
                'blackout_calendar_event_ids' => $blackout_event_ids,
                'booking_count'        => $booking_count,
                'calendar_dates'       => $calendar_dates,
                'children_locations'   => $children_location,
                'cloned'               => $cloned,
                'courses'              => $courses,
                'content'              => ORM::factory('Content')->where('id', '=', isset($data['content_id']) ? $data['content_id'] : '')->find_undeleted(),
                'subjects'             => $subjects,
                'data'                 => $data,
                'delivery_modes'       => array_column(Model_Lookup::lookupList('Delivery mode'), 'label', 'id'),
                'engineCalendarEvents' => $engineCalendarEvents,
                'frequencies'          => $frequencies,
                'learning_modes'       => array_column(Model_Lookup::lookupList('Learning mode'), 'label', 'id'),
                'location_rows'        => $location_rows,
                'navision_events'      => $navision_events,
                'locations'            => $locations,
                'parent_location_id'   => $parent_location,
                'study_modes'          => $study_modes,
                'schedule_topics'      => $schedule_topics,
                'schedule_zones'       => $schedule_zones,
                'schedules'            => $schedules,
                'topics'               => $topics,
                'trainers'             => $trainers,
                'zones'                => $zones,
                'edit_all'             => $edit_all,
                'selected_course'      => $selected_course
            )
        );
    }

    public function action_ajax_get_children_location()
    {
        $this->auto_render = false;
        $post   = $this->request->post();
        $id     = $post['id'];
        $loc    = $post['location_id'];
        $option = Model_Locations::get_children_location_html($id,$loc);
        echo $option;
    }
    public function action_schedule_has_booking(){
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post   = $this->request->post();
        $schedule_id = $post['id'];
        $response=[];
        $bookings = DB::select('booking_id')->from('plugin_ib_educate_booking_has_schedules')
            ->where('schedule_id', '=', $schedule_id)->execute()->as_array();
        if(count($bookings) > 0){
            $response['status'] = 'success';
        }

        echo json_encode($response);
    }

    public function action_ajax_remove_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        if (!$auth->has_access('courses_schedule_edit')) {
            if (is_numeric($id)) {
                $check_owner = Model_Schedules::get_schedule($id);
                if ($check_owner['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }
        echo json_encode(Model_Schedules::remove_schedule($id));
        exit;
    }

    public function action_remove_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->post('id') ? $this->request->post('id') : $this->request->query('id');
        if (!$auth->has_access('courses_schedule_edit')) {
            if (is_numeric($id)) {
                $check_owner = Model_Schedules::get_schedule($id);
                if ($check_owner['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }
        Model_Schedules::remove_schedule($id);
        IbHelpers::set_message('<strong>Success: </strong> Schedule is successfully removed.', 'success popup_box');
        $return = array('redirect' => '/admin/courses/schedules');
        echo json_encode($return);
        $this->request->redirect($return['redirect']);
        exit;
    }


    public function action_save_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        // zones
        $zone_rows   = array();
        $zone_zones  = array();
        $zone_prices = array();
        if(isset($data['zone_rows'])){
            $zone_rows   = explode(",",$data['zone_rows']);
            $zone_zones  = explode(",",$data['zone_zones']);
            $zone_prices = explode(",",$data['zone_prices']);
            unset($data['zone_rows']);
            unset($data['zone_zones']);
            unset($data['zone_prices']);
        }


        $blackoutEventIds = $this->request->post('blackout_calendar_event_ids');
        unset($data['blackout_calendar_event_ids']);
        $timeslots_data = json_decode($data['timeslots_json'], true);
        $custom_intervals = $data['custom_hidden'];
        unset($data['custom_hidden']);
        $custom_frequency = $data['frequency'];
        unset($data['frequency']);

        $schedule_id = $data['schedule_id'];
        $timetable_name = $data['timetable_post_name'];
        $timetable_id = $data['timetable_id'];
        if(is_numeric($timetable_name))
        {
            $timetable_name = $data['name'].$data['timetable_post_name'];
        }
//        unset($data['timetable_id']);
        unset($data['new_timetable_name']);
        unset($data['start_day_time']);
        unset($data['end_day_time']);
        unset($data['interval_id']);
        unset($data['start_time']);
        unset($data['end_time']);
        unset($data['bulk_timeslot_update_email_students']);
        if (!isset($data['weekdays_monday']))   $data['weekdays_monday'] = 0;
        if (!isset($data['weekdays_tuesday']))   $data['weekdays_tuesday'] = 0;
        if (!isset($data['weekdays_wednesday']))   $data['weekdays_wednesday'] = 0;
        if (!isset($data['weekdays_thursday']))   $data['weekdays_thursday'] = 0;
        if (!isset($data['weekdays_friday']))   $data['weekdays_friday'] = 0;
        if (!isset($data['weekdays_saturday']))   $data['weekdays_saturday'] = 0;
        if (!isset($data['weekdays_sunday']))   $data['weekdays_sunday'] = 0;

        $data['allow_credit_card']    = in_array('allow_credit_card',    $data['payment_options'] ?? []) ? '1' : '0';
        $data['allow_purchase_order'] = in_array('allow_purchase_order', $data['payment_options'] ?? []) ? '1' : '0';
        $data['allow_sales_quote']    = in_array('allow_sales_quote',    $data['payment_options'] ?? []) ? '1' : '0';

        // Find which save button was clicked
        if (isset($data['save_exit']) AND ($data['save_exit'] == 'true')) {
            $save_exit = TRUE;
        }
        else {
            $save_exit = FALSE;
        }

        // Unset this, so the save function doesn't try to add a save_button value to the database
        unset($data['save_exit']);

        // We don't need this for the moment..... $r = Model_Schedules::check_room_availability($data['location_id'],$return);
        $r = true;
        if(!$r)
        {
            IbHelpers::set_message('A schedule with this room/date combination already exists', 'error popup_box');
            $this->request->redirect('/admin/courses/edit_schedule/?id='.$data['schedule_id']);
            exit();
        }

        if (strlen(trim($data['name'])) < 1 AND !$r)
        {
            $courses = Model_Courses::get_all();
            $locations = Model_Locations::get_locations_only();
            $providers = Model_Providers::get_all_providers();
            $frequencies = Model_Schedulefrequencies::get_all_frequencies();
            $schedules = Model_Schedules::get_all_schedules();
            $schedule_zones = array();
            $zones = Model_Zones::get_all_zones();
            $location_rows = array();
            $topics = Model_Topics::get_all_topics();
            $schedule_topics = Model_Topics::get_topics(array('schedule_id' => $data['id'], 'format' => 'data'));

            //additional scripts
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/bootstrap-timepicker.js"></script>';
            $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/schedules_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/timepicker.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory(
                'form_schedule',
                array(
                    'alert'           => 'Please make sure that form is filled out correctly.',
                    'content'         => ORM::factory('Content')->where('id', '=', isset($data['content_id']) ? $data['content_id'] : '')->find_undeleted(),
                    'courses'         => $courses,
                    'data'            => $data,
                    'delivery_modes'  => array_column(Model_Lookup::lookupList('Delivery mode'), 'label', 'id'),
                    'frequencies'     => $frequencies,
                    'learning_modes'  => array_column(Model_Lookup::lookupList('Learning mode'), 'label', 'id'),
                    'location_rows'   => $location_rows,
                    'locations'       => $locations,
                    'providers'       => $providers,
                    'schedule_topics' => $schedule_topics,
                    'schedule_zones'  => $schedule_zones,
                    'schedules'       => $schedules,
                    'topics'          => $topics,
                    'zones'           => $zones
                )
            );

        }
        else
        {
            if (!$auth->has_access('courses_schedule_edit')) {
                $check_owner = null;
                if (is_numeric(@$data['id'])) {
                    $check_owner = Model_Schedules::get_schedule($data['id']);
                    if ($check_owner['owned_by'] != $user['id']) {
                        IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                        $this->request->redirect('/admin');
                    }
                }
                $course = Model_Courses::get_course($data['course_id']);
                if ($course['schedule_allow_price_override'] != 1) {
                    if (is_array($check_owner) && @$check_owner['fee_amount'] > 0) {
                        $data['fee_amount'] = $check_owner['fee_amount'];
                        $data['fee_per'] = $check_owner['fee_per'];
                        $data['is_fee_required'] = $check_owner['is_fee_required'];
                    } else {
                        $data['fee_amount'] = $course['schedule_fee_amount'];
                        $data['fee_per'] = $course['schedule_fee_per'];
                        $data['is_fee_required'] = $course['schedule_is_fee_required'];
                    }
                    if ($data['fee_per'] == 'Timeslot') {
                        foreach ($timeslots_data as $i => $timeslot_data) {
                            $timeslots_data[$i]['fee_amount'] = $data['fee_amount'];
                        }
                    }
                }

                $data['owned_by'] = $user['id'];
            }
            $data['start_date'] = $timeslots_data[0]['datetime_start'];
            $data['end_date'] = $timeslots_data[count($timeslots_data) - 1]['datetime_end'];
            $data['payg_apply_fees_when_absent'] = (is_numeric($data['payg_absent_fee']) && $data['payg_absent_fee'] > 0) ? 1 : 0;
            $data_copy = $data;
            $alert_trainer = false;
            $alert_trainer_timeslots = array();
            if (!is_numeric($data['schedule_id'])) {
                $alert_trainer = true;
                $alert_trainer_timeslots = $timeslots_data;
            } else {
                $previous_data = Model_Schedules::get_schedule($data['schedule_id']);
                if ($previous_data['trainer_id'] != $data['trainer_id']) {
                    $alert_trainer = true;
                    $alert_trainer_timeslots = $timeslots_data;
                } else {
                    foreach ($timeslots_data as $alert_trainer_timeslot) {
                        $alert_timeslot = true;
                        foreach ($previous_data['timeslots'] as $previous_data_timeslot) {
                            if ($previous_data_timeslot['id'] == $alert_trainer_timeslot['id']) {
                                if ($previous_data_timeslot['trainer_id'] == $alert_trainer_timeslot['trainer_id'] &&
                                    $previous_data_timeslot['datetime_start'] == $alert_trainer_timeslot['datetime_start'] &&
                                    $previous_data_timeslot['datetime_end'] == $alert_trainer_timeslot['datetime_end']) {
                                    $alert_timeslot = false;
                                }
                                break;
                            }
                        }
                        if ($alert_timeslot) {
                            $alert_trainer_timeslots[] = $alert_trainer_timeslot;
                        }
                    }
                }
            }
            if (!empty($data['content_id'])) {
                $content = new Model_Content($data['content_id']);
                $content->values($this->request->post());
                $content->save();
            } else {
                $content = new Model_Content($this->request->param('id'));
                $content->values($this->request->post());
                $content->save();
                $data['content_id'] = $content->id;
            }
            $schedule_id = Model_Schedules::save_schedule($data);
            if (isset($data['navision_id'])) {
                Model_NAVAPI::event_link_schedule($data['navision_id'], $schedule_id);
            }


            // If the schedule has a content tree from content-plugin...
            // ... update the name of the top-level item to be the schedule name
            if (!empty($data['content_id']) && !empty($data['name'])) {
                $content = new Model_Content($data['content_id']);
                if ($content->name != $data['name']) {
                    $content->name = $data['name'];
                    $content->save_with_moddate();
                }
            }

            // update schedule zones
            if($schedule_id){
                if ( sizeof($zone_rows) == sizeof($zone_zones) AND sizeof($zone_rows) == sizeof($zone_prices) ) {
                    Model_Schedules::delete_schedule_zones($schedule_id);
                    if (sizeof($zone_rows) != 0) {
                        Model_Schedules::save_schedule_zones($zone_rows, $zone_zones, $zone_prices, $schedule_id);
                    }
                }
            }
//header('content-type: text/plain');print_r($data);print_r($timeslots_data);exit;
            if(!isset($timeslots_data) OR !isset($timetable_name)){exit;}
            $new_timetable = false;
            //add and get the timetable id if it's a new timetable.
            $timetables = DB::select('id')->from('plugin_courses_timetable')->where('id','=',$timetable_id)->execute()->as_array();
            if($timetable_id === 'new' OR $timetable_id === 0 OR is_null($timetable_id))
            {
                $new_timetable = true;
                $query = DB::insert('plugin_courses_timetable',array('timetable_name'))->values(array($timetable_name))->execute();
                $timetable_id = $query[0];
            }
        }

        if (@$_POST['bulk_timeslot_update_email_students'] == 'YES') {
            Model_KES_Bookings::email_students_timeslot_change($timeslots_data);
        }
        $result         = Model_Schedules::save_timetable_and_schedule($timeslots_data,$schedule_id,$timetable_id,$blackoutEventIds,$new_timetable,true);

        if ($custom_frequency != '')
        {
            Model_Schedules::delete_custom_repeat($schedule_id);
            Model_Schedules::get_custom_repeat($schedule_id);
        }
        $schedule_id    = $result['schedule'];
        Model_Automations::run_triggers(
            Model_Courses_Schedulesavetrigger::NAME,
            array(
                'schedule_id' => $schedule_id,
                'schedule' => $data,
                'timeslots' => $timeslots_data,
                'alert_trainer' => $alert_trainer,
                'alert_trainer_timeslots' => $alert_trainer_timeslots
            )
        );
        Model_Automations::run_triggers(
            Model_Courses_Schedulechangedtrigger::NAME,
            array(
                'schedule_id' => $schedule_id,
                'schedule' => $data,
                'timeslots' => $timeslots_data,
            )
        );
        $message        = $result['message'];
        if ($message != '')
        {
            $alerts[] =  $message != '' ? $message : '';
        }
        if(is_numeric($schedule_id) AND !$save_exit)
        {
            $this->request->redirect('/admin/courses/edit_schedule/?id='.$schedule_id);
        }
        else
        {
            $this->request->redirect('/admin/courses/schedules');
        }
    }

    public function action_duplicate_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_GET['id'];
        if (!$auth->has_access('courses_schedule_edit')) {
            if (is_numeric($id)) {
                $check_owner = Model_Schedules::get_schedule($id);
                if ($check_owner['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }

        $duplicate = Model_Schedules::duplicate_schedule($id);
        $this->request->redirect('/admin/courses/edit_schedule/?cloned=true&id=' . $duplicate);
    }


    public function action_cancel_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        $id = $this->request->query('id');
        $schedule = new Model_Course_Schedule($id);
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else if(!is_numeric($schedule->id)) {
            IbHelpers::set_message("Invalid schedule ID", 'warning popup_box');
            $this->request->redirect('/admin/courses/schedules');
        }

        $schedule_bookings = Model_KES_Bookings::search(array('schedule_id'    => $id,
            'booking_status' => [Model_KES_Bookings::CONFIRMED, Model_KES_Bookings::INPROGRESS, Model_KES_Bookings::COMPLETED]
        ));
        if(count($schedule_bookings) > 0) {
            IbHelpers::set_message (
                "There  " . (count($schedule_bookings) == 1 ? 'is 1' : 'are '.count($schedule_bookings))
                ."bookings on this schedule that need to be cancelled  before you can cancel this schedule.",
                'warning popup_box'
            );
            $this->request->redirect('/admin/courses/schedules');
        }
        $schedule->set('schedule_status', Model_Schedules::CANCELLED)->save_with_moddate();
        IbHelpers::set_message (
            "Schedule #{$id} successfully cancelled",
            'success popup_box'
        );
        $this->request->redirect('/admin/courses/schedules');

    }
    public function action_ajax_publish_schedule()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        if (!$auth->has_access('courses_schedule_edit')) {
            if (is_numeric($id)) {
                $check_owner = Model_Schedules::get_schedule($id);
                if ($check_owner['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }
        echo json_encode(Model_Schedules::set_publish_schedule($id, $state));
        exit;
    }

    public function action_curriculums()
    {
        // todo: replace with curriculum permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        } else {
            $this->template->sidebar->tools = '<a href="/admin/courses/edit_curriculum" class="btn btn-primary">' . __('Add Curriculum') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Curriculums', 'link' => '/admin/courses/curriculums'];

            $this->template->body = View::factory('iblisting')->set([
                'columns'   => ['ID', 'Title', 'Course', 'Updated', 'Publish', 'Actions'],
                'id_prefix' => 'course-curriculums',
                'plugin'    => 'courses',
                'type'      => 'curriculum'
            ]);
        }
    }

    public function action_edit_curriculum()
    {
        // todo: replace with spec permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        }
        else {
            $curriculum = ORM::factory('Course_Curriculum')->where('id', '=', $this->request->param('id'))->find_undeleted();
            $learning_outcomes = ORM::factory('Course_LearningOutcome')->find_all_undeleted();
            $specs = ORM::factory('Course_Spec')->find_all_undeleted();

            $this->template->sidebar->tools = '<a href="/admin/courses/edit_curriculum/" class="btn btn-primary">' . __('Add Curriculum') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Curriculums', 'link' => '/admin/courses/curriculums'];

            $this->template->body = View::factory('form_curriculum')->set([
                'curriculum' => $curriculum,
                'learning_outcomes' => $learning_outcomes,
                'specs' => $specs
            ]);
        }
    }

    public function action_save_curriculum()
    {
        // todo: replace with spec permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
        }
        else {
            try {
                $curriculum = new Model_Course_Curriculum($this->request->param('id'));
                $curriculum->values($this->request->post());
                $curriculum->save_relationships($this->request->post());

                IbHelpers::set_message(htmlspecialchars('Curriculum #'.$curriculum->id.': "'.$curriculum->title.'" successfully saved.'), 'success popup_box');

                if ($this->request->post('action') == 'save_and_exit') {
                    $this->request->redirect('/admin/courses/curriculums');
                } else {
                    $this->request->redirect('admin/courses/edit_curriculum/'.$curriculum->id);
                }
            }
            catch (Exception $e) {
                Log::instance()->add(Log::ERROR, "Error saving curriculum.\n".$e->getMessage()."\n".$e->getTraceAsString());
                IbHelpers::set_message('Unexpected error saving curriculum. If this problem continues, please ask an administrator to check the error logs.', 'danger popup_box');
                $this->request->redirect('/admin/courses/curriculums');
            }
        }
    }

    public function action_specs()
    {
        // todo: replace with spec permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        } else {
            $this->template->sidebar->tools = '<a href="/admin/courses/edit_spec" class="btn btn-primary">' . __('Add spec') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Specs', 'link' => '/admin/courses/specs'];

            // $this->template->body = View::factory('list_specs');
            $this->template->body = View::factory('iblisting')->set([
                'columns'   => ['ID', 'Title', 'Updated', 'Publish', 'Actions'],
                'id_prefix' => 'course-specs',
                'plugin'    => 'courses',
                'type'      => 'spec',
            ]);
        }
    }

    public function action_edit_spec()
    {
        // todo: replace with spec permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        }
        else {
            $spec         = ORM::factory('Course_Spec')->where('id', '=', $this->request->param('id'))->find_undeleted();
            $courses      = ORM::factory('Course')->order_by('title')->find_all_undeleted();
            $credit_types = ORM::factory('Course_Credit')->get_enum_options('type');
            $products     = ORM::factory('Product_Product')->order_by('title')->find_all_undeleted();
            $providers    = ORM::factory('Course_Provider')->order_by('name')->find_all_undeleted();
            $study_modes  = ORM::factory('Course_StudyMode')->order_by('study_mode')->find_all_undeleted();
            $subjects     = ORM::factory('Course_Subject')->order_by('name')->find_all_undeleted(); // aka "modules"
            $learning_methodologies = ORM::factory('Lookup_Field')->where('name', '=', 'Learning methodology')->find()->lookups->order_by('label')->find_all();
            $qqi_components         = ORM::factory('Lookup_Field')->where('name', '=', 'QQI component')->find()->lookups->order_by('label')->find_all();
            $requirement_types      = ORM::factory('Lookup_Field')->where('name', '=', 'Requirement type')->find()->lookups->order_by('label')->find_all();
            $grading_schemas        = ORM::factory('Todo_GradingSchema')->order_by('title')->find_all_undeleted();

            $this->template->sidebar->tools = '<a href="/admin/courses/download_spec/'.$spec->id.'" class="btn btn-primary">' . __('Download spec') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Specs', 'link' => '/admin/courses/specs'];

            $this->template->body = View::factory('form_spec')->set([
                'spec'                   => $spec,
                'credit_types'           => $credit_types,
                'courses'                => $courses,
                'grading_schemas'        => $grading_schemas,
                'learning_methodologies' => $learning_methodologies,
                'products'               => $products,
                'providers'              => $providers,
                'qqi_components'         => $qqi_components,
                'requirement_types'      => $requirement_types,
                'study_modes'            => $study_modes,
                'subjects'               => $subjects
            ]);
        }
    }

    public function action_save_spec()
    {
        // todo: replace with spec permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
        }
        else {
            try {
                $spec = new Model_Course_Spec($this->request->param('id'));
                $spec->values($this->request->post());
                $spec->save_relationships($this->request->post());

                IbHelpers::set_message(htmlspecialchars('Spec #'.$spec->id.': "'.$spec->title.'" successfully saved.'), 'success popup_box');

                if ($this->request->post('redirect') == 'save_and_exit') {
                    $this->request->redirect('/admin/courses/specs');
                } else {
                    $this->request->redirect('admin/courses/edit_spec/'.$spec->id);
                }
            }
            catch (Exception $e) {
                Log::instance()->add(Log::ERROR, "Error saving course spec.\n".$e->getMessage()."\n".$e->getTraceAsString());
                IbHelpers::set_message('Unexpected error saving spec. If this problem continues, please ask an administrator to check the error logs.', 'danger popup_box');
                $this->request->redirect('/admin/courses/specs');
            }
        }
    }

    public function action_download_spec()
    {
        $this->auto_render = false;

        $spec       = ORM::factory('Course_Spec')->where('id', '=', $this->request->param('id'))->find_undeleted();
        $curriculum = $spec->curriculums->find_undeleted();
        $course     = $curriculum->courses->find_undeleted();
        $requirement_type = lcfirst($spec->requirement_type->label);
        $learning_outcomes = $curriculum->get_learning_outcomes();

        $learning_outcomes_array = [];
        foreach ($learning_outcomes as $order => $learning_outcome) {
            $learning_outcomes_array[] = $order.".\t".$learning_outcome->title;
        }

        $learning_methodologies_array = [];
        $learning_methodologies = $spec->get_learning_methodologies();
        foreach ($learning_methodologies as $methodology) {
            $learning_methodologies_array[] = '•    '.$methodology->label;
        }

        $syllabus_content = $curriculum->id ? $curriculum->content->get_ordered_children() : [];
        $syllabus_content_tags = [];
        foreach ($syllabus_content as $number => $content) {
            $los = $content->get_ordered_learning_outcomes($curriculum->id);
            $section_sub_content = $content->get_ordered_children();

            if (count($los)) {
                $los_string = '(';
                foreach ($los as $lo_number => $lo) {
                    $los_string .= 'LO'.$lo_number.' & ';
                }
                $los_string = trim($los_string, ' &').' '.($content->duration / 60).' hrs)';
            } else {
                $los_string = '';
            }

            $content_string = '';
            foreach ($section_sub_content as $sub_number => $sub_content) {
                $content_string .= ($number+1).'.'.($sub_number+1).'   '.$sub_content->name."\n";
            }

            $syllabus_content_tags[] = [
                'content_heading' => ($number+1).'.    '.$content->name,
                'content_los'     => $los_string,
                'content_items'   => $content_string
            ];
        }

        $assignment_marks = $spec->get_total_marks('Assignment');
        $exam_marks       = $spec->get_total_marks('Exam');
        $practical_marks  = $spec->get_total_marks('Practical');
        $total_marks      = $spec->get_total_marks();

        $template_data = [
            'aims'                   => trim(html_entity_decode(strip_tags($spec->aims))), // ['type' => 'block', 'html' => $spec->subject->aims],
            'assessment_methods'     => ['type' => 'block', 'html' => '<div style="font-family: Tahoma, Arial, sans-serif; font-size: 10pt;">'.$spec->assessment_methods.'</div>'],
            'assignment_marks'       => $assignment_marks ? $assignment_marks.'%' : '-',
            'credits'                => $spec->number_of_credits,
            'code'                   => $spec->code,
            'curriculum_title'       => $curriculum->title,
            'date_updated'           => date('d/m/Y'),
            'exam_duration'          => $spec->exam_duration,
            'exam_marks'             => $exam_marks ? $exam_marks.'%' : '-',
            'grading_schema'         => $spec->grading_schema->get_grades_string(),
            'learning_methodologies' => $learning_methodologies_array,
            'learning_outcomes'      => $learning_outcomes_array,
            'level'                  => $course->level->level,
            'number_of_exams'        => $spec->number_of_exams,
            'practical_marks'        => $practical_marks ? $practical_marks.'%' : '-',
            'qqi_component'          => $spec->qqi_component->label,
            'requirement_type'       => ($requirement_type == 'elective' ? 'an ' : 'a ') . $requirement_type,
            'spec_title'             => $spec->title,
            'table'                  => $syllabus_content_tags,
            'table2'                 => $spec->get_recommended_material(),
            'table3'                 => $spec->get_credits_by_type(['doc_gen' => true]),
            'version'                => $spec->version,
            'total_marks'            => $total_marks ? $total_marks.'%' : '-'
        ];

        $document_id = Model_Files::get_file_id('course_specification', Model_Files::get_directory_id_r('/templates'));
        $prefix = 'course_specification-'.$spec->code.'-'.$spec->version;
        $file = Model_Files::file_path($document_id);
        $tmp_file = tempnam(Kohana::$cache_dir, 'docgen');
        $doc = new IbDocx();
        $doc->processDocx($file, $template_data, $tmp_file);

        $pdf = true;
        if ($pdf) {
            header('Content-disposition: attachment; filename="'.$prefix.'.pdf"');
            header('Content-type: application/pdf');
            $tmp_file_pdf = tempnam(Kohana::$cache_dir, 'docgenpdf');
            $doc->generate_pdf($tmp_file, $tmp_file_pdf);
            readfile($tmp_file_pdf);
            unlink($tmp_file_pdf);
        } else {
            header('Content-disposition: attachment; filename="'.$prefix.'.docx"');
            header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            readfile($tmp_file);
        }

        unlink($tmp_file);
    }

    public function action_learning_outcomes()
    {
        // todo: replace with learning outcome permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to acess this feature.', 'error popup_box');
            $this->request->redirect('/admin');
        } else {
            $this->template->sidebar->tools = '<a href="/admin/courses/edit_learning_outcome" class="btn btn-primary">' . __('Add Learning outcome') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Learning Outcomes', 'link' => '/admin/courses/learning_outcomes'];

            $this->template->body = View::factory('iblisting')->set([
                'columns'   => ['ID', 'Title', 'Updated', 'Publish', 'Actions'],
                'id_prefix' => 'course-learning_outcomes',
                'plugin'    => 'courses',
                'type'      => 'learning_outcome'
            ]);
        }
    }

    public function action_edit_learning_outcome()
    {
        // todo: replace with learning outcome permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        }
        else {
            $learning_outcome  = ORM::factory('Course_LearningOutcome')->where('id', '=', $this->request->param('id'))->find_undeleted();

            $this->template->sidebar->tools = '<a href="/admin/courses/edit_learning_outcome" class="btn btn-primary">' . __('Add learning outcome') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Learning outcomes', 'link' => '/admin/courses/learning_outcomes'];

            $this->template->body = View::factory('form_learning_outcome')->set([
                'learning_outcome' => $learning_outcome,
            ]);
        }
    }

    public function action_save_learning_outcome()
    {
        // todo: replace with learning outcome permission
        if (!Auth::instance()->has_access('courses_course_edit')) {
            IbHelpers::set_message('You need access to the &quot;courses_course_edit&quot; permission to perform this action.', 'error popup_box');
        }
        else {
            try {
                $learning_outcome = new Model_Course_LearningOUtcome($this->request->param('id'));
                $learning_outcome->values($this->request->post());
                $learning_outcome->save_with_moddate($this->request->post());

                IbHelpers::set_message(htmlspecialchars('Learning outcome #'.$learning_outcome->id.': "'.$learning_outcome->title.'" successfully saved.'), 'success popup_box');

                if ($this->request->post('redirect') == 'save_and_exit') {
                    $this->request->redirect('/admin/courses/learning_outcomes');
                } else {
                    $this->request->redirect('admin/courses/edit_learning_outcome/'.$learning_outcome->id);
                }
            }
            catch (Exception $e) {
                Log::instance()->add(Log::ERROR, "Error saving learning outcome.\n".$e->getMessage()."\n".$e->getTraceAsString());
                IbHelpers::set_message('Unexpected error saving learning outcome. If this problem continues, please ask an administrator to check the error logs.');
            }
        }
    }

    public function action_study_modes()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/study_modes_list.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_study_modes');
    }

    public function action_add_study_mode()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/study_modes_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_study_mode');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/study_modes');
        }

    }

    public function action_edit_study_mode()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $data = Model_Studymodes::get_study_mode(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/study_modes_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_study_mode', array('data' => $data));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/study_modes');
        }
    }


    public function action_ajax_publish_study_mode()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Studymodes::set_publish_study_mode($id, $state));
        exit;
    }


    public function action_ajax_remove_study_mode()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Studymodes::remove_study_mode($id));
        exit;
    }

    public function action_remove_study_mode()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        Model_Studymodes::remove_study_mode($id);
        IbHelpers::set_message('<strong>Success: </strong> Study mode is successfully removed.', 'success popup_box');
        $return = array('redirect' => '/admin/courses/study_modes');
        echo json_encode($return);
        exit;
    }

    public function action_save_study_mode()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_studymode_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['study_mode'])) < 1)
        {
            $study_modes = Model_Studymodes::get_study_modes_without_parent();
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/study_modes_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_study_mode', array('study_modes' => $study_modes, 'data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Studymodes::save_study_mode($data);
            $location = ($redirect == 'save_and_exit') ? 'study_modes' : 'edit_study_mode/?id='.$id;
            $this->request->redirect('/admin/courses/'.$location);
        }
    }
    

    public function action_types()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/types_list.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_types');

    }

    public function action_add_type()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/types_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_type');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/types');
        }

    }

    public function action_edit_type()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $data = Model_Types::get_type(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/types_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_type', array('data' => $data));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/types');
        }
    }


    public function action_ajax_publish_type()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Types::set_publish_type($id, $state));
        exit;
    }


    public function action_ajax_remove_type()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Types::remove_type($id));
        exit;
    }

    public function action_remove_type()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        Model_Types::remove_type($id);
        IbHelpers::set_message('<strong>Success: </strong> Type is successfully removed.', 'success popup_box');
        $return = array('redirect' => '/admin/courses/types');
        echo json_encode($return);
        exit;
    }

    public function action_save_type()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_type_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['type'])) < 1)
        {
            $types = Model_Types::get_types_without_parent();
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/types_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_type', array('types' => $types, 'data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Types::save_type($data);
            $location = ($redirect == 'save_and_exit') ? 'types' : 'edit_type/?id='.$id;
            $this->request->redirect('/admin/courses/'.$location);
        }
    }
    
    public function action_levels()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/levels_list.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_levels');
    }

    public function action_add_level()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            //select template to display
            $this->template->body = View::factory('form_level');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/levels');
        }

    }

    public function action_edit_level()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $data = Model_Levels::get_level(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $this->template->body = View::factory('form_level', array('data' => $data));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/levels');
        }
    }


    public function action_ajax_publish_level()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Levels::set_publish_level($id, $state));
        exit;
    }


    public function action_ajax_remove_level()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Levels::remove_level($id));
        exit;
    }

    public function action_remove_level()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->param('id');;
        Model_Levels::remove_level($id);
        IbHelpers::set_message('<strong>Success: </strong> Level is successfully removed.', 'success popup_box');
        $this->request->redirect ('/admin/courses/levels');
    }

    public function action_save_level()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_level_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['level'])) < 1)
        {
            IbHelpers::set_message('Please give the level a name.', 'danger popup_box');
            //select template to display
            $this->template->body = View::factory('form_level', array('data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Levels::save_level($data);
            $location = ($redirect == 'save_and_exit') ? 'levels' : 'edit_level/?id='.$id;
            $this->request->redirect('/admin/courses/'.$location);
        }
    }


    public function action_years()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/years_list.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_years');
    }

    public function action_add_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/years_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_year');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/years');
        }

    }

    public function action_edit_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $data = Model_Years::get_year(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/years_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_year', array('data' => $data));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error  popup_box');
            $this->request->redirect('/admin/courses/years');
        }
    }


    public function action_ajax_publish_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Years::set_publish_year($id, $state));
        exit;
    }


    public function action_ajax_remove_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Years::remove_year($id));
        exit;
    }

    public function action_remove_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        Model_Years::remove_year($id);
        IbHelpers::set_message('<strong>Success: </strong> Year is successfully removed.', 'success  popup_box');
        $return = array('redirect' => '/admin/courses/years');
        echo json_encode($return);
        exit;
    }

    public function action_save_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_year_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['year'])) < 1)
        {
            $years = Model_Years::get_years_without_parent();
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/years_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_year', array('years' => $years, 'data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Years::save_year($data);
            $location = ($redirect == 'save_and_exit') ? 'years' : 'edit_year/?id='.$id;
            $this->request->redirect('/admin/courses/'.$location);
        }
    }

    /**
     * loading all courses by ajax request for jQuery Dabatables
     */

    public function action_ajax_get_courses()
    {
        //get parameters
        $post = $this->request->post();
        if (isset($post['sEcho']))
        {
            $return['sEcho'] = $post['sEcho'];
        }
        // converts the search filters in post to an array search filters
        $search_filters = array();
        for($i = 0; $i < 15; $i ++){
            if(!isset($post["sSearch_{$i}"])){
                // if $i column doesn't exist, no further columns exist, break loop
                break;
            }
            $search_filters[] = $post["sSearch_{$i}"];
        }
        //calculate all matching records
        //$return['iTotalRecords'] = Model_Courses::count_courses($post['sSearch']);
        //sorting order
        $sort = 'title';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'title';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "code";
        }
        if ($post['iSortCol_0'] == 2) {
            $sort = "year";
        }
        if ($post['iSortCol_0'] == 3) {
            $sort = "level";
        }
        if ($post['iSortCol_0'] == 4) {
            $sort = "category";
        }
        if ($post['iSortCol_0'] == 5) {
            $sort = "subject";
        }
        if ($post['iSortCol_0'] == 6) {
            $sort = "type";
        }
        if ($post['iSortCol_0'] == 7) {
            $sort = "provider";
        }
        if ($post['iSortCol_0'] == 8) {
            $sort = "topics";
        }
        if ($post['iSortCol_0'] == 10) {
            $sort = "plugin_courses_courses.publish";
        }
        $search_filters = array();
        for($i = 0; $i < 15; $i ++){
            if(!isset($post["sSearch_{$i}"])){
                // if $i column doesn't exist, no further columns exist, break loop
                break;
            }
            $search_filters[] = $post["sSearch_{$i}"];
        }
        //get data for response
        $return['aaData'] = Model_Courses::get_courses($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch'], $search_filters);
        $return['iTotalRecords'] = DB::select(DB::expr('FOUND_ROWS() as total'))->execute()->get('total');
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        //display response (json encoded)
        echo json_encode($return);
        exit;
    }
    
    public function action_ajax_get_course_by_subject_id()
    {
        $subject_id  = $this->request->query('subject_id') ?? $this->request->query('module_id')?? '0';
        echo json_encode(Model_Courses::get_course_by_subject_id($subject_id));
        exit;
    }
    
    public function action_ajax_get_categories()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Categories::count_categories($post['sSearch']);
        $sort = 'category';
        if ($post['iSortCol_0'] == 0)
        {
            $sort = 'category';
        }
        if ($post['iSortCol_0'] == 1)
        {
            $sort = "summary";
        }
        $return['aaData'] = Model_Categories::get_categories($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;

    }


    public function action_ajax_get_locations()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!$auth->has_access('courses_location_edit') && !$auth->has_access('courses_location_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Locations::count_locations($post['sSearch'], $auth->has_access('courses_location_edit') ? null : $user['id']);
        $sort = 'name';
        if ($post['iSortCol_0'] == 0)
        {
            if (in_array(strtolower($post['sSortDir_0']), array('asc', 'desc'))) {
                $sort = 'parent ' . $post['sSortDir_0'] . ', name';
            }
        }
        if ($post['iSortCol_0'] == 1)
        {
            if (in_array(strtolower($post['sSortDir_0']), array('asc', 'desc'))) {
                $sort = "parent " . $post['sSortDir_0'] . ", name";
            }
        }
        if ($post['iSortCol_0'] == 2)
        {
            $sort = "city";
        }
        if ($post['iSortCol_0'] == 3)
        {
            $sort = "county";
        }

        $return['aaData'] = Model_Locations::get_locations($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch'], $auth->has_access('courses_location_edit') ? null : $user['id']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;

    }

    public function action_ajax_get_providers()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Providers::count_providers($post['sSearch']);
        $sort = 'name';
        if ($post['iSortCol_0'] == 0)
        {
            $sort = 'name';
        }
        if ($post['iSortCol_0'] == 1)
        {
            $sort = "city";
        }
        if ($post['iSortCol_0'] == 2)
        {
            $sort = "county";
        }
        $return['aaData'] = Model_Providers::get_providers($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;

    }

    public function action_ajax_get_schedules()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_schedule_edit') && !$auth->has_access('courses_schedule_edit_limited')){
            if ($this->request->is_ajax()) {
                $this->response->status(403);
                exit();
            } else {
                IbHelpers::set_message(__('You have no permission for this page.'), 'info popup_box');
                $this->request->redirect('/admin');
            }
        }

        $post = $this->request->post();

        if (!$auth->has_access('courses_schedule_edit')) {
            $user = $auth->get_user();
            $post['owned_by'] = $user['id'];
        }
        if (isset($post['sEcho'])) {
            $return['sEcho'] = $post['sEcho'];
        }
        $sort_columns = ['id', 'course', 'name', 'category', 'fee_amount', 'repeat_name',  'status_label', 'start_date',
            'location', 'trainer', 'is_confirmed', 'date_modified'];
        $sort = $sort_columns[$post['iSortCol_0']] ?? 'date_modified';
        $return = Model_Schedules::get_schedules($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch'],$post);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_get_schedules_data()
    {
        $this->auto_render = false;

        if (Auth::instance()->has_access('courses')) {
            $schedules = Model_Schedules::get_all_schedules(['course_id' => $this->request->query('course_id')]);
        } else {
            $schedules = [];
        }
        echo json_encode($schedules);
    }

    public function action_ajax_get_study_modes()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Studymodes::count_study_modes($post['sSearch']);
        $sort = 'study_mode';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'study_mode';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "summary";
        }
        $return['aaData'] = Model_Studymodes::get_study_modes($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_get_types()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Types::count_types($post['sSearch']);
        $sort = 'type';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'type';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "summary";
        }
        $return['aaData'] = Model_Types::get_types($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_get_levels()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Levels::count_levels($post['sSearch']);
        $sort = 'level';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'level';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "summary";
        }
        $return['aaData'] = Model_Levels::get_levels($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_get_years()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Years::count_years($post['sSearch']);
        $sort = 'year';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'year';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "summary";
        }
        $return['aaData'] = Model_Years::get_years($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_edit_booking()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $booking_id = $this->request->param('id');
        if (is_numeric($booking_id)) {
            $booking = Model_CourseBookings::load($booking_id);
        } else {
            $booking = null;
        }

        $view = View::factory('edit_coursebooking');
        $view->booking = $booking;

        if ($this->request->is_ajax()) {
            $this->auto_render = false;
            echo $view;
        } else {
            $this->template->sidebar->menus = array();
            $this->template->sidebar->breadcrumbs = array(
                array('name' => 'Home', 'link' => '/admin'),
                array('name' => 'Bookings', 'link' => '/admin/courses/bookings')
            );
            $this->template->sidebar->tools = '';
            $this->template->body = $view;
        }
    }

    public function action_save_booking()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        //print_r($this->request->post());
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        parse_str(substr($this->request->post('data_json'), 1, -1), $data);
        //$booking = $data;
        //print_r($data);
        if ($data['action'] == 'confirm') {
            if (in_array($data['booking']['status'], array('Processing', 'Pending'))) {
                $data['booking']['status'] = 'Confirmed';
                foreach ($data['booking']['has_schedules'] as $i => $has_schedule) {
                    if (in_array($data['booking']['has_schedules'][$i]['status'], array('Processing', 'Pending'))) {
                        $data['booking']['has_schedules'][$i]['status'] = 'Confirmed';
                    }
                }
            }
        }
        if ($data['action'] == 'cancel') {
            $data['booking']['status'] = 'Cancelled';
            foreach ($data['booking']['has_schedules'] as $i => $has_schedule) {
                $data['booking']['has_schedules'][$i]['status'] = 'Cancelled';
            }
        }

        $booking = Model_CourseBookings::save($data['booking']);
        if ($data['action'] == 'cancel') {
            Model_CourseBookings::set_cancel_transactions($booking['id']);
        }

        echo json_encode($booking);
    }

    public function action_bookings()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        Model_CourseBookings::migrate_old_bookings();

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/bookings_list.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('transactions') .'js/transactions.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_bookings');

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array();
        $this->template->sidebar->breadcrumbs = array(array('name' => 'Home',  'link' => '/admin'),array('name' => 'Bookings', 'link' => '/admin/courses/bookings'));
        //$this->template->sidebar->tools = '<a href="/admin/courses/edit_booking/new"><button type="button" class="btn">Add Booking</button></a>';
    }


    public function action_ajax_get_bookings()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        $params = array();
        if (isset($post['sEcho'])) {
            $params['sEcho'] = $post['sEcho'];
        }
        if ($post['iSortCol_0'] == 0) {
            $params['sort'] = 'bookings.id';
        } else if ($post['iSortCol_0'] == 1) {
            $params['sort'] = 'courses.title';
        } else if ($post['iSortCol_0'] == 2) {
            $params['sort'] = "ccategories.category";
        } else {
            $params['sort'] = 'bookings.updated';
        }
        $params['sort_dir'] = $post['sSortDir_0'];
        if (@$post['sSearch']) {
            $params["term"] = $post['sSearch'];
        }
        if (@$post['iDisplayStart']) {
            $params["offset"] = $post['iDisplayStart'];
        }
        if (@$post['iDisplayLength']) {
            $params["limit"] = $post['iDisplayLength'];
        }

        $user = Auth::instance()->get_user();
        if (Auth::instance()->has_access('courses_limited_access')){
            $params['user_id'] = $user['id'];
        }
        $result = Model_CourseBookings::get_datatable($params);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_bookings_people()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_GET['id'];
        $schedule = Model_Schedules::get_schedule($id);
        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';
        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/bookings_people_list.js"></script>';
        //select template to display
        $this->template->body = View::factory('list_bookings_people', array('schedule' => $schedule));
    }


    public function action_ajax_get_bookings_people()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        $id = $_GET['id'];
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Bookings::count_all_for_schedule($id, $post['sSearch']);
        $sort = 'first_name';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'first_name';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "last_name";
        }
        if ($post['iSortCol_0'] == 2) {
            $sort = 'email';
        }
        if ($post['iSortCol_0'] == 3) {
            $sort = "phone";
        }
        if ($post['iSortCol_0'] == 4) {
            $sort = 'comments';
        }
        if ($post['iSortCol_0'] == 5) {
            $sort = "school";
        }
		if ($post['iSortCol_0'] == 6) {
			$sort = "school_address";
		}
		if ($post['iSortCol_0'] == 7) {
			$sort = "roll_no";
		}
		if ($post['iSortCol_0'] == 8) {
			$sort = "school_phone";
        }
		if ($post['iSortCol_0'] == 9) {
			$sort = "county";
		}
		if ($post['iSortCol_0'] == 10) {
			$sort = "paid";
		}

        $return['aaData'] = Model_Bookings::get_all_for_schedule($id, $post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_add_image_to_course()
    {
        $data = $_POST;
        Model_Courses::save_image($data);
        exit;
    }

    public function action_ajax_save_course()
    {
        $data = $_POST;
        $info = Model_Courses::ajax_save_course($data);
        echo $info;
        exit;
    }

    public function action_ajax_add_city()
    {
        $data = $_POST;
        $info = Model_Cities::ajax_save_city($data);
        echo $info;
        exit;
    }

    public function action_ajax_add_type()
    {
        $data = $_POST;
        $info = Model_Locationtypes::ajax_save_type($data);
        echo $info;
        exit;
    }

    public function action_ajax_get_location_types()
    {
        echo Model_Locationtypes::get_types_html();
        exit;
    }

    public function action_ajax_add_frequency()
    {
        $data = $_POST;
        $info = Model_Schedulefrequencies::ajax_save_frequency($data);
        echo $info;
        exit;
    }

    public function action_ajax_get_frequencies()
    {
        echo Model_Schedulefrequencies::get_frequencies_html();
        exit;
    }

    public function action_ajax_get_courses_images()
    {
        $post = $this->request->post();
        $id = (int)$_GET['id'];
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Courses::count_images($id, $post['sSearch']);
        $sort = 'image';
        $return['aaData'] = Model_Courses::get_images($id, $post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_remove_course_image()
    {
        $id = (int)$_POST['id'];
        echo json_encode(Model_Courses::remove_image($id));
        exit;
    }

    public function action_ajax_save_schedule()
    {
        $data = $_POST;
        $response = Model_Schedules::ajax_save_schedule($data);
        echo $response;
        exit;
    }

    public function action_ajax_create_events()
    {
        $data = $_POST;
        $response = Model_Schedules::ajax_create_events($data);
        $return = array();
        if (is_array($response) AND count($response) > 0)
        {
            $return['message'] = 'success';
            $return['count'] = count($response);
        }
        else
        {
            $return['message'] = 'Error while creating events!';
        }
        echo json_encode($return);
        exit;
    }

    public function action_ajax_get_events()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Schedules::get_count_events($post['sSearch']);
        $sort = 'datetime_start';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'schedule';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "datetime_start";
        }
        if ($post['iSortCol_0'] == 2) {
            $sort = "datetime_end";
        }

        $return['aaData'] = Model_Schedules::get_events($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_publish_event()
    {
        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Schedules::set_publish_event($id, $state));
        exit;
    }


    public function action_ajax_remove_event()
    {
        $id = (int)$_POST['id'];
        echo json_encode(Model_Schedules::remove_event($id));
        exit;
    }

    public function action_get_csv_report()
    {
        $id = $_GET['id'];
        $search = urldecode($_GET['search']);
    }

    public function action_generate_bookings_csv()
    {
        $bookings = Model_Bookings::get_bookings_csv();
        ExportCsv::export_report_data_array($this->response, $bookings, "bookings");
    }

    public function action_generate_course_bookings_csv()
    {
        if (is_numeric($_GET['schedule_id'])) {
            ExportCsv::export_report_data_array($this->response, Model_Bookings::get_all_applicants($_GET['schedule_id']), Model_Courses::get_course_name_by_schedule_id($_GET['schedule_id']));
        } else {
            echo "Invalid schedule ID";
        }
    }
    public function action_get_day_by_date_js()
    {
        $this->auto_render = false;
        $date = strtotime(urldecode($_GET['date']));
        $return = date("l",strtotime("+1 month",$date));
        echo $return;
    }
    public function action_save_timetable()
    {
        $this->auto_render = false;
        $timetable_data = $_POST['data'];
        $schedule_id = $_POST['schedule_id'];
        $timetable_name = $_POST['timetable_name'];
        $new_timetable = !is_numeric($timetable_name);
        if(!isset($timetable_data)){echo "invalid_timetable_name";exit;}
        if(!$new_timetable)
        {
            $timetable_name = Model_Schedules::get_timetable_id_by_name($timetable_name);
        }
        else
        {
            $not_exists = Model_Schedules::create_timetable($timetable_name);
            if(!$not_exists){echo "timetable_exists";exit;}
            $timetable_name = Model_Schedules::get_timetable_id_by_name($timetable_name);
        }
        $blackoutEventIds = $this->request->post('blackout_calendar_event_ids');
        echo Model_Schedules::save_timetable_and_schedule($timetable_data,$schedule_id,$timetable_name, $blackoutEventIds, $new_timetable,$trainers_data);
    }
    public function action_get_timetables()
    {
        $this->auto_render = false;
        echo Model_Schedules::get_timetables();
    }
    public function action_timetable_get_dates()
    {
        $this->auto_render = false;
        $timetable_id = @$_POST['timetable_id'];
        $schedule_id = @$_POST['schedule_id'];
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(Model_Schedules::get_timetable_dates($timetable_id, $schedule_id));
    }

    public function action_get_active_timetable()
    {
        $this->auto_render = false;
        $schedule = $_GET['schedule'];
        echo ($schedule === "new")? Model_Schedules::get_schedule_timetable($schedule) : Model_Schedules::get_active_timetable($schedule);
    }

    public function action_autotimetables()
    {
        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses') . 'css/lists.css'] = 'screen';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/autotimetables_list.js"></script>';
        $this->template->body = View::factory('list_autotimetables');
    }

    public function action_ajax_get_autotimetables()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Autotimetables::count_autotimetables($post['sSearch']);
        $sort = 'date_start';

        switch ($post['iSortCol_0'])
        {
            case 0: $sort = 'id';
                break;
            case 1: $sort = 'name';
                break;
            case 2: $sort = 'category';
                break;
            case 3: $sort = 'location';
                break;
            case 4: $sort = 'date_start';
                break;
            case 5: $sort = 'date_end';
                break;
            case 7: $sort = 'publish';
        }
        // converts the search filters in post to an array search filters
        $search_filters = array();
        for($i = 0; $i < 15; $i ++){
            if(!isset($post["sSearch_{$i}"])){
                // if $i column doesn't exist, no further columns exist, break loop
                break;
            }
            $search_filters[] = $post["sSearch_{$i}"];
        }
        $return['aaData'] = Model_Autotimetables::get_autotimetables_as_html($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch'], $search_filters);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_edit_autotimetable()
    {
            $categories = Model_Categories::get_categories_without_parent();
            $locations = Model_Locations::get_locations_only();
            $years = Model_Years::get_all_years();
            $data = Model_Autotimetables::get_autotimetable(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $preview_data = Model_Autotimetables::get_course_data(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);

            // additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/autotimetables_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/ZeroClipboard.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/TableTools.min.js"></script>';
            //$this->template->scripts[] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('courses').'js/lmcbutton.js"></script>';

            // additional styles
            $this->template->styles[URL::get_engine_assets_base().'css/bootstrap-multiselect.css'] = 'multiselect';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/TableTools.css'] = 'tabletools';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/TableTools_JUI.css'] = 'TTJUI';
            // select template to display
            $this->template->body = View::factory('form_autotimetables', array('categories' => $categories, 'locations' => $locations, 'years' => $years, 'preview_data' => $preview_data, 'data' => $data));
    }

    public function action_add_autotimetable()
    {
        try
        {
            $categories = Model_Categories::get_categories_without_parent();
            $locations = Model_Locations::get_locations_only();
            $years = Model_Years::get_all_years();

            // additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/autotimetables_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/ZeroClipboard.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/TableTools.min.js"></script>';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/TableTools.css'] = 'tabletools';
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/TableTools_JUI.css'] = 'TTJUI';
            // additional styles
            // select template to display
            $this->template->body = View::factory('form_autotimetables', array('categories' => $categories, 'locations' => $locations, 'years' => $years));
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/autotimetables');
        }

    }

    public function action_save_autotimetable()
    {
        $data = $_POST;
        $save = Model_Autotimetables::save_autotimetable($data);

        if ($data['redirect'] == '/admin/courses/edit_autotimetable/?id=')
        {
            if (is_numeric($save[0])) {
                $data['redirect'] .= $save[0];
            }
            else {
                $data['redirect'] .= $data['id'];
            }
        }

        $this->request->redirect($data['redirect']);
    }

    public function action_ajax_publish_autotimetable()
    {
        $id = (int)$_POST['id'];
        $state = (int)$_POST['state'];
        echo json_encode(Model_Autotimetables::set_publish_autotimetable($id, $state));
        exit;
    }

    public function action_ajax_remove_autotimetable()
    {
        $id = (int)$_POST['id'];
        echo json_encode(Model_Autotimetables::remove_autotimetable($id));
        exit;
    }

    public function action_get_trainers()
    {
        $post = $this->request->post();
        $id = (isset($post['id'])) ? $post['id'] : '';
        $this->auto_render = false;
        exit(json_encode(Model_Schedules::get_trainers($id)));
    }

    public function action_get_valid_date()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $valid = TRUE;
        $date = date('Y-m-d',strtotime($post['date']));
        $category = DB::select('start_date','end_date','category')
            ->from(array('plugin_courses_categories','cat'))
            ->join(array('plugin_courses_courses','course'),'RIGHT')
            ->on('cat.id','=','course.category_id')
            ->where('course.id','=',@$post['course_id'])
            ->execute()
            ->current();
        if ($category)
        {
            if (! is_null($category['start_date']) AND ! is_null($category['end_date']) )
            {
                $valid = Model_Schedules::check_event_for_category_dates($date,$date,$category['start_date'],$category['end_date']) ;
            }
        }
        exit(json_encode($valid) );
    }

    public function action_calculate_frequency()
    {
        $this->auto_render = false;
        $post           = $this->request->post();
        $frequency      = (isset($post['frequency'])) ? $post['frequency'] : '';
        $start_date     = $post['start_date'];
        $end_date       = $post['end_date'];
        $days           = (isset($post['days']) AND is_array(json_decode($post['days']))) ? json_decode($post['days']) : '';
        $duration       = (isset($post['duration'])) ? $post['duration'] : 0;
        $timeslots      = (isset($post['timeslots']) AND is_array(json_decode($post['timeslots'])) ) ? json_decode($post['timeslots']) : '' ;
        $trainer_id     = $post['trainer_id'];
        $location_id     = $post['location_id'];
        $custom_repeat  = (isset($post['custom_repeat'])) ? $post['custom_repeat'] : '';
        $course_id      = @$post['course_id'];
        $fee_per        = @$post['fee_per'];
        $fee_amount     = @$post['fee_amount'];
        $blackoutEventIds = $this->request->post('blackout_calendar_event_ids');
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(
            Model_Schedules::calculate_frequency(
                $frequency,
                $start_date,
                $end_date,
                $days,
                $duration,
                $timeslots,
                $custom_repeat,
                $trainer_id,
                $course_id,
                $blackoutEventIds,
                $fee_per,
                $fee_amount,
                $location_id
            )
        );
    }

    public function action_get_location_spaces()
    {
        $this->auto_render = false;
        $data = $this->request->post();
        $schedule_location = Model_Schedules::get_schedule_location_id($data['schedule_id']);
        $seats = Model_Locations::get_location($schedule_location);
        $this->response->body(json_encode(array('size' => $seats)));
    }

    /**
     * Get the Custom timetable selection tables
     */
    public function action_get_custom_timetable()
    {
        $this->auto_render  = FALSE;
        $data = $this->request->post();
        echo Model_Schedules::get_custom_timetable($data['selected_days']) ;
//        $this->response->body(json_encode(array('custom_timetable' => $custom_timetable)));
    }

    public function action_add_custom_day_timeslots()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();
        $result = Model_Schedules::add_custom_day_timeslots($data);
        exit(json_encode($result)) ;
    }

    /**
     * Get a new timeslot for the day
     */
    public function action_get_custom_timeslot_row()
    {
        $this->auto_render  = FALSE;
        $data = $this->request->post();
        echo Model_Schedules::get_new_timeslot_row($data['day'],$data['trainer'],$data['row'],$data['course_id'], $data['location_id']);
//        $this->response->body(json_encode(array('new_timeslot' => $timeslot)));
    }

    public function action_ajax_get_custom_intervals()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();
        exit(json_encode(Model_Schedules::get_custom_interval_html($data['schedule_id'])));
    }
	/* Build the Master View table for the Room-Allocation Board */
	public function action_ajax_get_rab_master_view()
	{
		$this->auto_render = FALSE;
		if (class_exists('Model_Reports'))
		{
			// Run the SQL from the report
			$report = new Model_Reports($this->request->param('id'));
			$report->get(TRUE);
			$report->set_sql($this->request->post('sql'));
			$report->set_parameters($this->request->post('parameters'));
			$report->set_parameters($report->prepare_parameters());
			$results = $report->run_report();

			// Reformat the results, in a way that they can be easily looped through to build the table
			$rooms = array();
			$data = array(
				'Monday'    => array(),
				'Tuesday'   => array(),
				'Wednesday' => array(),
				'Thursday'  => array(),
				'Friday'    => array(),
				'Saturday'  => array(),
				'Sunday'    => array()
			);
			foreach ($results as $result)
			{
				$room = $result['Room'];
				$day  = $result['Day'];
				if ( ! in_array($room, $rooms))
				{
					$rooms[] = $room;
				}
				if ( ! isset($data[$day][$room]))
				{
					$data[$day][$room] = array();
				}
				$data[$day][$room][] = array(
					'time'    => $result['Time'],
					'class'   => $result['Class']
				);
			}

			echo View::factory('widget_rab_master_table')->set('data', $data)->set('rooms', $rooms);
		}
	}

    public function action_academic_years()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_academicyear_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->body = View::factory('list_academic_year');
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/academic_year_list.js"></script>';
    }

    public function action_ajax_get_all_academic_years()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = ORM::factory('AcademicYear')->count_all();
        $sort = 'date_modified';
        switch ($post['iSortCol_0'])
        {
            case 0: $sort = 'id';
                break;
            case 1: $sort = 'title';
                break;
            case 2: $sort = 'start_date';
                break;
            case 3: $sort = 'end_date';
                break;
            case 4: $sort = 'status';
                break;
            case 5: $sort = 'publish';
                break;
            case 7: $sort = 'updated_on';
                break;
        }
        $return['aaData'] = Model_AcademicYear::get_all_academic_years($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_publish_academic_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_academicyear_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $this->request->post();
        $result = array('status'=>'error') ;
        $logged_in_user      = Auth::instance()->get_user();
        $academic = ORM::factory('AcademicYear', $data['id']);
        $academic->set('updated_by',$logged_in_user['id']);
        $academic->set('publish',$academic->publish == 1 ? 0 : 1 );
        $academic->save();
        $answer = ORM::factory('AcademicYear', $data['id']);
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_status_academic_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_academicyear_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $this->request->post();
        $result = array('status'=>'error') ;
        $logged_in_user      = Auth::instance()->get_user();
        $academic = ORM::factory('AcademicYear', $data['id']);
        $academic->set('updated_by',$logged_in_user['id']);
        $academic->set('status',$academic->status == 1 ? 0 : 1 );
        $academic->save();
        $answer = ORM::factory('AcademicYear', $data['id']);
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_delete_academic_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_academicyear_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $this->request->post();
        $result = array('status'=>'error') ;
        $logged_in_user      = Auth::instance()->get_user();
        $academic = ORM::factory('AcademicYear', $data['id']);
        $academic->set('updated_by',$logged_in_user['id']);
        $academic->set('deleted',$academic->deleted == 1 ? 0 : 1 );
        $academic->save();
        $answer = ORM::factory('AcademicYear', $data['id']);
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }

    public function action_add_edit_academic_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_academicyear_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->param('id');
        $data = ORM::factory('AcademicYear', $id);

        $this->template->styles[URL::get_engine_plugin_assets_base('events') . 'css/validation.css'] = 'screen';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/academic_year_form.js"></script>';

        $this->template->body = View::factory('form_academic_year');
        $this->template->body->data = $data;
    }

    public function action_save_academic_year()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_academicyear_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();

            $year = ORM::factory('AcademicYear', $this->request->post('id'));
            $year->values($post);
            $year->set('start_date',date("Y-m-d H:i:s",strtotime($post['start_date'])));
            $year->set('end_date',date("Y-m-d H:i:s",strtotime($post['end_date'])));
            $year->set('updated_by', $user['id']);
            if ( ! is_numeric($post['id']))
            {
                $year->set('created_by', $user['id']);
                $year->set('created_on', date("Y-m-d H:i:s"));
                $year->set('updated_on', date("Y-m-d H:i:s"));
            }
            $year->save();
            IbHelpers::set_message('The calendar event: '.$post['title'].' was '.is_numeric($post['id'])?'Updated':'Created'.' successfully.', 'success  popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/courses/academic_years');
            }
            else
            {
                $this->request->redirect('/admin/courses/add_edit_academic_year/' . $year->id);
            }
        }
        catch(Exception $e)
        {
            IbHelpers::set_message('Error saving the academic year.', 'error popup_box');
            $this->request->redirect('admin/courses/academic_years');
        }
    }

    public function action_cleanup_duplicate_intervals()
    {
        $result = Model_Schedules::cleanup_duplicate_intervals();
        echo $result . " records deleted";
        exit();
    }

    public function action_cleanup_duplicate_events()
    {
        $result = Model_Schedules::cleanup_duplicate_events();
        echo $result . " records deleted";
        exit();
    }

    public function action_get_schedule_min_start() {
        $value = Settings::instance()->get('schedule_start_time');
        $value = $value=='' ? '00:00' : $value;
        exit(json_encode($value) );
    }

    public function action_get_schedule_max_start() {
        $value = Settings::instance()->get('schedule_end_time');
        $value = $value=='' ? '23:59' : $value;
        exit(json_encode($value) );
    }

    public function action_get_schedule_interval_setting() {
        $value = Settings::instance()->get('schedule_time_interval');
        $value = $value=='' ? 15 : intval($value);
        exit(json_encode($value) );
    }

    public function action_bulk_delete_schedule_events()
    {
        $evendIds = json_decode($this->request->post('events'), true);
        Model_ScheduleEvent::bulkDelete($evendIds);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(array('message' => 'deleted'));
    }

    public function action_fix_schedule_timeslots_without_trainers()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(
            array(
                'updated' => Model_Schedules::fix_timeslots_without_trainers()
            )
        );
    }

    public function action_student_schedule_registrations()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_registration_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->sidebar->tools = '<a href="/admin/courses/student_schedule_registration/new"><button class="btn" type="button">'.__('Register Student').'</button></a>';

        //additional styles
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/lists.css'] = 'screen';

        //get_icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('courses');

        //select template to display
        $this->template->body = View::factory('list_student_schedule_registrations', $results);
    }

    public function action_student_schedule_registrations_list_data()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_registration_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $term = $this->request->query('sSearch');
        $offset = $this->request->query('iDisplayStart');
        $limit = $this->request->query('iDisplayLength');
        $scol = $this->request->query('iSortCol_0');
        $sdir = $this->request->query('iSortDir_0');
        $data = Model_SchedulesStudents::search_for_datatable($term, $offset, $limit, $scol, $sdir);
        echo json_encode($data);
    }

    public function action_student_schedule_registration()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_registration_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->param('id');
        $post = $this->request->post();
        if (@$post['contact_id']) {
            if (!is_numeric($id)) {
                $exists = Model_SchedulesStudents::search(
                    array('contact_id' => $post['contact_id'], 'schedule_id' => $post['schedule_id'])
                );
                if (count($exists) > 0) {
                    IBHelpers::set_message('There is already a registration for same student/schedule', 'error popup_box');
                    $this->request->redirect('/admin/courses/student_schedule_registration/new');
                }
            }
            $id = Model_SchedulesStudents::save(
                $id,
                $post['contact_id'],
                $post['schedule_id'],
                $post['status'],
                $post['notes']
            );

            IBHelpers::set_message('Student successfully saved','success popup_box');

            if ($post['action'] == 'save')
                $this->request->redirect('/admin/courses/student_schedule_registration/' . $id);

            if ($post['action'] == 'save_and_exit')
                $this->request->redirect('/admin/courses/student_schedule_registrations');
        }

        if (is_numeric($id)) {
            $registration = Model_SchedulesStudents::get($id);
        } else {
            $registration = array('id' => 'new', 'contact_id' => null, 'student' => '', 'schedule_id' => null, 'schedule' => '', 'status' => 'Pending', 'notes' => '');
        }

        //select template to display
        $this->template->styles[URL::get_engine_plugin_assets_base('events') . 'css/validation.css'] = 'screen';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->body = View::factory('student_schedule_registration');
        $this->template->body->registration = $registration;
    }

    public function action_autocomplete_schedules()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $trainer_id = null;
        $auth = Auth::instance();
        if (!$auth->has_access('courses_schedule_edit')) {
            $user = $auth->get_user();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $trainer = Model_Contacts3::get_linked_contact_to_user($user['id']);
            } else {
                $trainer = Model_Contacts::get_linked_contact_to_user($user['id']);
            }
            if ($trainer) {
                $trainer_id = $trainer['id'];
            }
        }
        $alltime = false;
        if ($this->request->query('alltime') == 'yes') {
            $alltime = true;
        }

        $course_id = $this->request->query("course_id");
        echo json_encode(Model_courses::autocomplete_search_schedules($this->request->query('term'), $trainer_id, true, !$alltime, $course_id));
    }

    public function action_autocomplete_schedule_events()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        echo json_encode(Model_Courses::autocomplete_search_schedule_events($this->request->query('schedule_id')));
    }

    public function action_migrate()
    {
        Model_CourseBookings::migrate_old_bookings();
    }

    public function action_cancel_booking()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        Model_CourseBookings::set_processing_status($post['booking_id'], 'Cancelled', @$post['note'], null, (int)@$post['clear_outstanding']);
        $response = array(
            'success' => true
        );
        echo json_encode($response);
    }

    public function action_get_booking_data()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $booking = Model_CourseBookings::load($post['booking_id']);
        $schedules = DB::query(Database::SELECT, "select concat_ws(' ', s.`name`) as item, s.id as schedule_id, 'all' as timeslot_id, s.fee_amount, s.fee_per from plugin_courses_courses c inner join plugin_courses_schedules s on c.id = s.course_id where s.`delete` = 0 and s.end_date >= CURDATE() and s.booking_type = 'Whole Schedule'
union
select concat_ws(' ', s.`name`, e.datetime_start) as item, s.id as schedule_id, e.id as timeslot_id, if (e.fee_amount is not null, e.fee_amount, s.fee_amount), s.fee_per from plugin_courses_courses c inner join plugin_courses_schedules s on c.id = s.course_id inner join plugin_courses_schedules_events e on s.id = e.schedule_id where e.datetime_end >= NOW() and e.`delete` = 0 and s.booking_type = 'One Timeslot'
order by item")->execute()->as_array();
        $response = array(
            'booking' => $booking,
            'schedules' => $schedules
        );
        echo json_encode($response);
    }

    public function action_transfer_booking()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $result = Model_CourseBookings::transfer_booking($post);
        echo json_encode($result);
    }

    public function action_discounts()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $discounts = Model_Coursediscounts::get_all_discounts_for_listing();
        $this->template->body = View::factory('list_coursediscounts')->bind('discounts',$discounts);
    }

    public function action_edit_discount()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->param('id');
        $discount = Model_Coursediscounts::create($id);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/edit_coursediscount.js"></script>';
        $this->template->body = View::factory('edit_coursediscount')->bind('discount', $discount);
    }

    public function action_copy_timeslots()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_schedule_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'text/plain; charset=utf-8');

        $from_schedule_id = $this->request->query('from');
        $to_schedule_ids = explode(',', $this->request->query('to'));

        foreach ($to_schedule_ids as $to_schedule_id) {
            Model_Schedules::copy_timeslots($from_schedule_id, $to_schedule_id);
            echo 'timeslots has been copied from ' . $from_schedule_id . ' to ' . $to_schedule_id . "\r\n";
        }
    }

    public function action_save_discount()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_booking_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->auto_render = false;
        $post = $this->request->post();
        if (isset($post['for_contacts'])) {// some cleanup, handle empty input
            if ($post['for_contacts'][0] == 0 || $post['for_contacts'][0] == '') {
                unset ($post['for_contacts'][0]);
            }
        }

        if (@$post['has_schedules'] || @$post['has_courses']) {
            $post['is_package'] = 1;
        }

        $id = Model_Coursediscounts::create()->set($post)->save();
        if($post['redirect'] == 'save' AND is_numeric($id))
        {
            $this->request->redirect('/admin/courses/edit_discount/'.$id);
        }
        else
        {
            $this->request->redirect('/admin/courses/discounts');
        }
    }

    public function action_autocomplete_contacts()
    {
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $contacts = Model_Contacts3::autocomplete_list($this->request->query('term'));
        } else {
            $contacts = Model_Contacts::autocomplete_list($this->request->query('term'), $this->request->query('list'));
        }
        if ($this->request->query('schedule_id_not_registered')) {
            $registrations = Model_SchedulesStudents::search(array('schedule_id' => $this->request->query('schedule_id_not_registered')));
            foreach ($registrations as $registration) {
                foreach ($contacts as $i => $contact) {
                    if ($contact['value'] == $registration['contact_id']) {
                        unset($contacts[$i]);
                        break;
                    }
                }
            }
        }
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($contacts);
    }

    public function action_autocomplete_trainers()
    {
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $contacts = Model_Contacts3::autocomplete_list($this->request->query('term'), null, 'teacher');
        } else {
            $contacts = Model_Contacts::autocomplete_list($this->request->query('term'), $this->request->query('list'));
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($contacts);
    }

    /**
     * Topics
     */
    public function action_topics()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/topics_list.js"></script>';

        //select template to display
        $this->template->body = View::factory('list_course_topics');

    }

    public function action_ajax_get_topics()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post        = $this->request->post();
        $query       = $this->request->query();
        $course_id   = isset($query['course_id']  ) ? $query['course_id']   : null;
        $schedule_id = isset($query['schedule_id']) ? $query['schedule_id'] : null;
        $editable    = ($this->request->query('editable') == 1);

        if (isset($post['sEcho'])) {
            $return['sEcho'] = $post['sEcho'];
        }
        $return['iTotalRecords'] = Model_Topics::count_topics(array(
            'search'      => $post['sSearch'],
            'course_id'   => $course_id,
            'schedule_id' => $schedule_id
        ));

        switch ($post['iSortCol_0']) {
            case 0  : $sort = 'name';        break;
            case 1  : $sort = 'description'; break;
            default : $sort = 'name';        break;
        }

        $return['aaData'] = Model_Topics::get_topics(array(
            'course_id'   => $course_id,
            'dir'         => $post['sSortDir_0'],
            'editable'    => $editable,
            'format'      => 'datatable',
            'limit'       => $post['iDisplayLength'],
            'offset'      => $post['iDisplayStart'],
            'schedule_id' => $schedule_id,
            'search'      => $post['sSearch'],
            'sort'        => $sort
        ));

        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_ajax_edit_topic()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        try
        {
            $data = Model_Topics::get_topic_by_id($post['id']);
            echo json_encode($data);
            exit;
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error  popup_box');
            $this->request->redirect('/admin/courses/topics');
        }
    }


    public function action_ajax_update_topic()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        $id = isset($post['id']) ? $post['id'] : null;
        $name = isset($post['name']) ? $post['name'] : null;
        $description = isset($post['description']) ? $post['description'] : null;

       if($id == null){
           IbHelpers::set_message('Bad request.', 'error  popup_box');
           $this->request->redirect('/admin/courses/topics');
       }

        try
        {
            $data = Model_Topics::update_topic($id,$name,$description);
            echo json_encode($data);
            exit;
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error  popup_box');
            $this->request->redirect('/admin/courses/topics');
        }
    }

    public function action_add_topic()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/topics_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_topic');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/topics');
        }

    }

    public function action_save_topic()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if (strlen(trim($data['name'])) < 1)
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/topics_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_topic', array('data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Topics::save_topic($data);
            $location = 'topics';
            $this->request->redirect('/admin/courses/'.$location);
        }
    }

    public function action_ajax_add_topic_to_course()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        Model_Courses::save_topic($data);
        exit;
    }

    public function action_ajax_add_topic_to_schedule()
    {
        $this->auto_render = false;
        $data = $this->request->post();
        $topic = Model_Topics::get_topic_by_id($data['topic_id']);
        $tr = isset($topic['id']) ? View::factory('snippets/item_topic_tr')->set('topic', $topic)->render() : '';
        echo $tr;
        return $tr;

    }

    public function action_ajax_remove_course_topic()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_course_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        echo json_encode(Model_Courses::remove_topic($data));
        exit;
    }

    public function action_ajax_remove_topic()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_topic_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = (int)$_POST['id'];
        echo json_encode(Model_Topics::remove_topic($id));
        exit;
    }

    /**
     * Zones
     */
    public function action_zones()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        //additional scripts
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('courses') .'js/zones_list.js"></script>';

        //select template to display
        $this->template->body = View::factory('list_course_zones');

    }

    public function action_ajax_get_zones()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = Model_Zones::count_zones($post['sSearch']);
        $sort = 'name';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'name';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "price";
        }
        $return['aaData'] = Model_Zones::get_zones($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_add_zone()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        try
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/zones_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_zone');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');
            $this->request->redirect('/admin/courses/zones');
        }

    }

    public function action_save_zone()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $data = $_POST;
        if ( strlen(trim($data['name'])) < 1 )
        {
            //additional scripts
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/zones_form.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
            //additional styles
            $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
            //select template to display
            $this->template->body = View::factory('form_zone', array('data' => $data, 'alert' => 'Please make sure, that form is filled properly!'));
        }
        else
        {
            $redirect = $_POST['redirect'];
            $id       = Model_Zones::save_zone($data);
            $location = 'zones';
            $this->request->redirect('/admin/courses/'.$location);
        }
    }

    public function action_ajax_edit_zone()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        try
        {
            $data = Model_Zones::get_zone_by_id($post['id']);
            echo json_encode($data);
            exit;
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error  popup_box');
            $this->request->redirect('/admin/courses/zones');
        }
    }

    public function action_ajax_update_zone()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        $id = isset($post['id']) ? $post['id'] : null;
        $name = isset($post['name']) ? $post['name'] : null;

        if($id == null){
            IbHelpers::set_message('Bad request.', 'error  popup_box');
            $this->request->redirect('/admin/courses/zones');
        }

        try
        {
            $data = Model_Zones::update_zone($id,$name);
            echo json_encode($data);
            exit;
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error  popup_box');
            $this->request->redirect('/admin/courses/zones');
        }
    }

    public function action_ajax_remove_zone()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_zone_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        
        $id = (int)$_POST['id'];
        echo json_encode(Model_Zones::remove_zone($id));
        exit;
    }



    public function action_get_rows_for_location()
    {
        $post = $this->request->post();
        $location_id = (isset($post['location_id'])) ? $post['location_id'] : '';
        $this->auto_render = false;
        $rows = Model_Locations::get_rows($location_id);
        $str = '';
        if(sizeof($rows)>0){
            $zones = Model_Zones::get_all_zones();
            foreach ($rows as $row){
                $str .= '<tr>';
                    $str .= '<td data-row-id="'.$row["id"].'" data-content="row">'.$row['name'].'</td>';
                    $str .= '<td data-content="zone"> <select class="form-control">';
                        foreach ($zones as $zone){
                            $str .= '<option value="'.$zone['id'].'" >'.$zone['name'].'</option>';
                        }
                    $str .= '</select></td>';
                    $str .= '<td data-content="price"><input data-row-id="' . $row["id"] . '" type="number" min="0" value="0"></td>';
                $str .= '</tr>';
            }
        }
       exit($str);
   //        exit(json_encode(Model_Locations::get_rows($location_id)));
    }

    public function action_my_courses()
    {
        if (!Auth::instance()->has_access('courses_view_mycourses')) {
            IbHelpers::set_message('You need access to the &quot;courses_view_mycourses&quot; permission to view this page.', 'warning popup_box');
            $this->request->redirect('/admin');
        }
        else {
            $this->template->sidebar->breadcrumbs[] = ['name' => 'My Courses', 'link' => '#'];
            $this->template->sidebar->tools = ' ';

            $my_bookings = Auth::instance()->get_contact()->bookings()->find_all_undeleted();
            $this->template->body = View::factory('list_my_courses')->set('my_bookings', $my_bookings);
        }
    }


    public function action_my_course()
    {
        if (!Auth::instance()->has_access('courses_view_mycourses')) {
            IbHelpers::set_message('You need access to the &quot;courses_view_mycourses&quot; permission to view this page.', 'warning popup_box');
            $this->request->redirect('/admin');
        }
        else {
            $can_view_all = Auth::instance()->has_access('courses_view_mycourses_global');

            if (!$can_view_all) {
                $my_bookings = Auth::instance()->get_contact()->bookings()->find_all_undeleted();
                $has_booked_schedule = false;

                for ($i = 0; $i < count($my_bookings) && !$has_booked_schedule; $i++) {
                    $has_booked_schedule = $my_bookings[$i]->schedules->where('course_schedule.id', '=', $this->request->param('id'))->find_all()->count() > 0;
                }

                if (!$has_booked_schedule) {
                    IbHelpers::set_message('You cannot view this course content unless you have booked it.', 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }

            $schedule = new Model_Course_Schedule($this->request->param('id'));

            if ($can_view_all) {
                // Get the content for the schedule.
                $content = ORM::factory('Content')->where('id', '=', $schedule->content_id)->find_undeleted();
            } else {
                // Get the content for the schedule, if the user has booked it.
                $content = ORM::factory('Content')->where('id', '=', $schedule->content_id)->where_is_booked()->find_undeleted();
            }

            if (!$content->id) {
                IbHelpers::set_message('This content does not exist or is not currently available.', 'warning popup_box');
                $this->request->redirect('/admin');
            }

            $this->template->sidebar->breadcrumbs[] = ['name' => 'My Courses', 'link' => '/admin/courses/my_courses'];
            $this->template->sidebar->breadcrumbs[] = ['name' => $schedule->name, 'link' => '#'];


            if ($schedule->content_id) {
                $assets_folder_path = @Kohana::$config->load('config')->assets_folder_path ?: 'default';
                $this->template->styles[URL::get_engine_plugin_assets_base('surveys') . 'css/frontend/survey.css'] = 'screen';
                $this->template->styles['https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.css'] = 'screen';
                $this->template->scripts[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.min.js"></script>';
                $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
                $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
                $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('content') . 'js/my_content.js"></script>';

                $this->template->body = View::factory('/admin/my_content')
                    ->set('allow_skipping', $schedule->content->allow_skipping)
                    ->set('content', $schedule->content)
                    ->set('open_section', 0);
            }
            else {
                $this->template->body = 'No content has been set up for this schedule';
            }
        }
    }

    public function action_seed()
    {
        $db = Database::instance();
        $db->commit();

        try {
            // Save the course
            $online_study_mode = ORM::factory('Course_StudyMode')->where('study_mode', '=', 'Online')->find_undeleted();

            $course = new Model_Course();
            $course->title = 'English 123';
            $course->summary = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>';
            $course->save_with_moddate();

            // Save the schedule
            $repeat_weekly   = ORM::factory('Course_Repeat')->where('name', '=', 'Weekly')->find();
            $online_location = ORM::factory('Course_Location')->where('name', '=', 'Online')->find_undeleted();

            $schedule = new Model_Course_Schedule();
            $schedule->name = 'English 123 online';
            $schedule->course_id = $course->id;
            $schedule->start_date = date('Y-m-d', strtotime('+4 weeks' )).' 14:30:00';
            $schedule->end_date   = date('Y-m-d', strtotime('+18 weeks')).' 17:00:00';
            $schedule->is_confirmed = 1;
            $schedule->is_fee_required = 1;
            $schedule->rental_fee = 50;
            $schedule->fee_amount = 60;
            $schedule->repeat = $repeat_weekly->id;
            $schedule->book_on_website = 1;
            $schedule->fee_per = 'Timeslot';
            $schedule->payg_period = 'Timeslot';
            $schedule->amendable = 1;
            $schedule->location_id = $online_location->id;
            $schedule->study_mode_id = $online_study_mode->id;
            $schedule->save_with_moddate();

            // Save the timetable
            $timetable = new Model_Course_timetable();
            $timetable->timetable_name = $schedule->name.' '.date('U');
            $timetable->save_with_moddate();

            // Save the timeslots
            for ($i = 4; $i < 19; $i++) {
                $timeslot = new Model_Course_Schedule_Event();
                $timeslot->schedule_id    = $schedule->id;
                $timeslot->timetable_id   = $timetable->id;
                $timeslot->datetime_start = date('Y-m-d', strtotime('+'.$i.' weeks' )).' 14:30:00';
                $timeslot->datetime_end   = date('Y-m-d', strtotime('+'.$i.' weeks' )).' 17:00:00';
                $timeslot->save();
            }

            IbHelpers::set_message('Dummy course created.', 'success');

            $this->request->redirect('/admin/courses/edit_course/?id='.$course->id);

        } catch (Exception $e) {
            $db->rollback();

            throw $e;
        }
    }


    public function action_find_course()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $data = $this->request->query();
        $courses = DB::select('id', DB::expr("title AS `value`"))
            ->from(Model_Courses::TABLE_COURSES)
            ->where('title', 'like', '%' . $data['term'] . '%')
            ->and_where('deleted', '=', 0)
            ->order_by('title', 'asc')
            ->execute()
            ->as_array();
        $this->response->body(json_encode($courses));
    }

    public function action_autocomplete_categories()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $data = $this->request->query();
        $courses = DB::select('id', DB::expr("category AS `value`"))
            ->from(Model_Categories::TABLE_CATEGORIES)
            ->where('category', 'like', '%' . $data['term'] . '%')
            ->and_where('delete', '=', 0)
            ->order_by('category', 'asc')
            ->execute()
            ->as_array();
        $this->response->body(json_encode($courses));
    }

    public function action_find_schedule()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $data = $this->request->query();
        if (Auth::instance()->has_access('courses_limited_access')) {
            $user = Auth::instance()->get_user();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $trainer = Model_Contacts3::get_linked_contact_to_user($user['id']);
            } else {
                $trainer = Model_Contacts::get_linked_contact_to_user($user['id']);
            }
            $data['trainer_id'] = $trainer['id'];
        }
        $result = Model_Courses::get_booking_search_term($data);
        $this->response->body(json_encode($result));
    }

    public function action_find_schedule_periods()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $schedule_id = $this->request->post('schedule_id');
        $periods = Model_ScheduleEvent::get_periods($schedule_id);
        $this->response->body(json_encode($periods));
    }

    public function action_get_students()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();

        $result = Model_Schedules::get_students($post['schedule_id']);
        $this->response->body(json_encode($result));
    }

    public function action_find_subject()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $data = $this->request->query();
        $subjects = DB::select('id', DB::expr("name AS `value`"))
            ->from(Model_Subjects::TABLE_SUBJECTS)
            ->where('name', 'like', '%' . $data['term'] . '%')
            ->and_where('deleted', '=', 0)
            ->order_by('name', 'asc')
            ->execute()
            ->as_array();
        $this->response->body(json_encode($subjects));
    }


    public function action_credits()
    {
        $this->template->sidebar->tools = false;

        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings').'admin/css/fullcalendar.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );

        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/coursecredits.js"></script>';

        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Courses', 'link' => '/admin/courses'),
            array('name' => 'Credits', 'link' => '/admin/courses/credits')
        );

        $this->template->body = View::factory('course_credits');
        $this->template->body->academic_years = Model_AcademicYear::get_all();
        $this->template->body->subjects = Model_Subjects::get_all_subjects();
        $this->template->body->courses = array();
        foreach (Model_Courses::get_all() as $course) {
            $this->template->body->courses[$course['id']] = $course['title'];
        }
    }

    public function action_get_timeslots()
    {
        $course_id = $this->request->post();
        $params = array();
        $params['course_id'] = $course_id;
        $timeslots = Model_ScheduleEvent::search($params);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($timeslots, JSON_PRETTY_PRINT);
    }

    public function action_timeslot_attendees_table()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $timeslot  = new Model_Course_Schedule_Event($this->request->param('id'));
        $has_access = $timeslot->has_access();

        if ($has_access) {
            $attendees = $timeslot->get_attendees()->find_all_undeleted();
            $html = View::factory('timeslot_list_attendees', compact('attendees', 'timeslot'))->render();
            $count = count($attendees);
        } else {
            $html = '';
            $count = 0;
        }

        echo json_encode([
            'has_access' => $has_access,
            'html'       => $html,
            'count'      => $count,
        ]);

    }

    public function action_autocomplete_academicyears()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        echo json_encode(Model_courses::autocomplete_search_academicyears($this->request->query('term')));
    }

    public function action_autocomplete_topics()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        echo json_encode(Model_Topics::autocomplete_topics($this->request->query('term')));
    }

    public function action_autocomplete_locations()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        echo json_encode(Model_Locations::autocomplete_locations($this->request->query('term')));
    }
}
