<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Dashboard extends Controller_Cms
{

	function before() {
		parent::before();
	}

    public static function get_dashboard_icons()
    {
            $url_path     = URL::overload_asset('img/dashboard/');
			$folder_path  = APPPATH.'assets/shared/img/dashboard/';
			$local_path   = PROJECTPATH.'www/assets/img/';

			$icons        = Model_Plugin::get_dashboard_plugins_icons();
            $off_icons    = Model_Plugin::get_off_dashboard_plugins_icons();
			$text_display = Settings::instance()->get('dashboard_icon_summary');

            $icon_html    = '<div class="dashboard_icons dashboard_icons_active'.($text_display ? ' dashboard_icons_with_descriptions' : '' ).'">';
            foreach ($icons as $icon)
			{
                $icon_html .='
				<div class="dashboard_plugin popinit styled_plugin" data-trigger="hover" rel="popover" data-original-title="'.__($icon['note_title']).'" data-content="'.__($icon['note_body']).'">
					<a href="'. URL::site() .'admin/' . $icon['name'] .'">
						<div class="icon-background">';

                if ($icon['svg']) {
                    $icon_html .= IbHelpers::svg_sprite($icon['svg']);
                }
                // Below items are all deprecated. Each plugin should use an SVG in future
				elseif ($icon['flaticon'])
				{
					$icon_html .= '<span class="flaticon-'.$icon['flaticon'].'"></span>';
				}
				// Use image, if exists
				elseif ($icon['icon'] AND file_exists($local_path.'blue/'.$icon['icon']))
				{
					$icon_html .= '<img src="'.URL::get_project_assets_base().'assets/img/blue/'.$icon['icon'].'" alt="" />';
				}
				// Use default icon, if no icon is set
				elseif (trim($icon['icon']) == '')
				{
					$icon_html .= '<span class="fa icon-file-image-o"></span>';
				}
				// Use image, if exists
				elseif (strpos($icon['icon'], '.png') > -1 AND file_exists($folder_path.'blue/'.$icon['icon']))
				{
					$icon_html .= '<img src="'.$url_path.'blue/'.$icon['icon'].'" alt="" />';
				}
				// Use font awesome icon
				elseif (substr($icon['icon'], 0, 3) == 'fa-')
				{
					$icon_name = substr(preg_replace('/\\.[^.\\s]{3,4}$/', '', $icon['icon']), 3);
					$icon_html .= '<span class="fa icon-'.$icon_name.'"></span>';
				}
				// Use IdeaBubble font icon
				else
				{
					$icon_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $icon['icon']);
					$icon_html .= '<span class="ib-icon-'.$icon_name.'"></span>';
				}

				$icon_html .='
						</div>
						<div class="dashboard_plugin_name">'. __($icon['friendly_name']) .'</div>';
                if ($text_display == 1) {
                    $icon_html .='
                    <span class="product_info_1">'.__($icon['note_title']).'</span>';
                }
                $icon_html .='
					</a>
				</div>';
            }
            $icon_html .= '</div>';

			$icon_off_html = '<div class="dashboard_icons dashboard_icons_inactive">';
            foreach ($off_icons as $off_icon)
			{
                $icon_off_html .=
                    '<div class="dashboard_plugin">
						<div>
							<a href="mailto:sales@ideabubble.ie?Subject='.$off_icon['friendly_name'].' Plugin%20Information" title="'.$off_icon['friendly_name'].' Plugin. Click here to find out more">';

                if ($off_icon['svg']) {
                    $icon_off_html .= IbHelpers::svg_sprite($off_icon['svg']);
                }
                // Below items are all deprecated. Each plugin should use an SVG in future
				elseif (trim($off_icon['flaticon']))
				{
					$icon_off_html .= '<span class="flaticon-'.$off_icon['flaticon'].'"></span>';
				}
				elseif (strpos($off_icon['icon'], '.png') > -1 AND file_exists($folder_path.'grey/inactive_'.$off_icon['icon']))
				{
					$icon_off_html .= '<img src="'.$url_path.'grey/inactive_'.$off_icon['icon'].'" alt="" />';
				}
				elseif (trim($off_icon['icon']) == '')
				{
					$icon_off_html .= '<span class="fa icon-file-image-o"></span>';
				}
				else
				{
					$icon_off_html .= '<span class="ib-icon-'.preg_replace('/\\.[^.\\s]{3,4}$/', '', $off_icon['icon']).'"></span>';
				}

				$icon_off_html .= '
							</a>
						</div>
						<div class="dashboard_plugin_name">'.$off_icon['friendly_name'].'</div>
                    </div>';
            }
            $icon_off_html .= '</div>';

        return [
            'icon_html' => $icon_html,
            'icon_off_html' => $icon_off_html
        ];
    }

	public function action_index()
	{
        $this->template->body = '';
		$this->template->on = '';
		$this->template->off = '';
		$this->template->alert = IbHelpers::get_messages();

        //Check if menu icons are registered into the init.php
        if(DashBoard::is_registered_menu_icons()) {
			$icons= self::get_dashboard_icons();

			$this->template->on  .= $icons['icon_html'];
            $this->template->off .= $icons['icon_off_html'];
        }

		$view = View::factory('dashboard')->set('widgets',DashBoard::factory()->render_widgets());

		$use_default = TRUE;
		$auth       = Auth::instance()->get_user(); // The auth instance returns data relevant to the user during the sign in.
		$user       = ORM::factory('User', $auth['id']); // This step is necessary, in case the user changes their default dashboard during this session
		$user_group = new Model_Roles($auth['role_id']);

		if (class_exists('Model_Dashboard') && ($user->default_dashboard_id != -1 || $user_group->default_dashboard_id != -1))
		{
            $this->template->styles = array_merge($this->template->styles, array(
                URL::get_engine_plugin_assets_base('reports').'css/reports.css' => 'screen'
            ));

			// If a dashboard has been set at user-level and it has been shared with the user, use it
			if ($user->default_dashboard_id)
			{
				$dashboard   = new Model_Dashboard($user->default_dashboard_id);
				$use_default = ($dashboard->id AND $dashboard->shared_with_user()) ? FALSE : TRUE;
			}

			// Else if a dashboard has been set at user-group-level and it has been shared with the user, use it
			if ($use_default AND $user_group->default_dashboard_id)
			{
				$dashboard   = new Model_Dashboard($user_group->default_dashboard_id);
				$use_default = ($dashboard->id AND $dashboard->shared_with_user()) ? FALSE : TRUE;
			}

			if ( ! $use_default AND isset($dashboard))
			{
				$this->template->styles    = array_merge($this->template->styles, array(
					URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
					URL::get_engine_plugin_assets_base('dashboards').'css/dashboards_view.css' => 'screen'
				));
				$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/moment.min.js"></script>';
				$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
				$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('dashboards').'js/view_dashboards.js"></script>';
				$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/sparkline.js"></script>';
				$this->template->show_welcome_text = ($user_group->role == 'Administrator') ?  TRUE : FALSE;

				$view->set('report_widgets', $dashboard->render());
			}
		}

        if ($use_default) {
            // This is only temporary, until this has been set up as a proper dashboard,
            // configured to appear for users of particular groups (KES-4135)
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $user = Auth::instance()->get_user();
                if (Auth::instance()->has_access('contacts3_limited_view')) {
                    $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
                } else {
                    $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
                }

                $user_contact = new Model_Contacts3(isset($contacts[0]['id']) ? $contacts[0]['id'] : null);

                // If the user is linked to a contact, and does not have the administrator role, show this hardcoded dashboard
                if ($user_contact->get_id() && $user['role_id'] != 2) {
                    $contact_ids = array();
                    foreach ($contacts as $contact) {
                        $contact_ids[] = $contact['id'];
                    }

                    $attendance = Model_KES_Bookings::get_attendance(array('contact_id' => $contact_ids));

                    $booked_courses = count(Model_KES_Bookings::search(array('contact_id' => $contact_ids)));
                    $mytime_entries = Model_Mytime::search(array('contact_ids' => $contact_ids));
                    $this->template->body = View::factory('frontend/dashboard');
                    $this->template->body->attendance = $attendance;
                    $this->template->body->booked_courses = $booked_courses;
                    $this->template->body->contact = $user_contact;
                    $this->template->body->contacts_count = count($contacts);
                    $this->template->body->mytime_entries = $mytime_entries;
                    $this->template->show_welcome_text = false;
                    $this->template->off = '';
                    return true;
                }
            }

            // Widgets generated by the reports plugin
            $report_widgets = (class_exists('Model_Reports')) ? Controller_Admin_Reports::action_render_dashboard_reports() : '';
            $view->set('report_widgets', $report_widgets);
            if (!empty($report_widgets)) {
                $this->template->styles[URL::get_engine_plugin_assets_base('reports').'css/reports.css'] = 'screen';
            }
        }

		$view->set('default_dashboard', $use_default);

		$this->template->scripts[] = '<script src="'. URL::get_engine_assets_base() .'js/highcharts/highcharts.js" type="text/javascript"></script>';
		$this->template->body     .=  $view;
	}

	// Send feedback, using the "Feedback Form"
	public function action_send_feedback()
	{
		$this->auto_render = FALSE;
		$sent = FALSE;

		// Send the feedback using a messaging template
		try
		{
			$user       = Auth::instance()->get_user();
			$comment    = $this->request->post('comment');
			// Parameters used in the message. Parse new lines and sanitise to prevent injection.
			$parameters = array(
				'email' => $user['email'],
				'comment' => nl2br(htmlentities($comment, 0, 'UTF-8'))
			);
			$messaging  = new Model_Messaging;
			$sent       = $messaging->send_template('Feedback', null, null, array(), $parameters);
		}
		// If it fails, write an error to the system logs
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getMessage()."\n".$e->getTraceAsString());
		}

		// Display a message, reporting if the feedback was successfully submitted or not
		if ($sent) {
			IbHelpers::set_message(__('Your feedback has been posted.'), 'success');
		}
		else {
			IbHelpers::set_message(__('Error submitting feedback. If this problem persists, ask an administrator to check the system logs.'), 'danger');
		}
		$this->request->redirect('/admin');
	}
}
