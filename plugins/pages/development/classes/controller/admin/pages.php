<?php defined ('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Pages extends Controller_cms {
	function before ()
	{
		parent::before ();

		if ( ! Auth::instance()->has_access('pages'))
		{
			IbHelpers::set_message("You need access to the &quot;pages&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

		// Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('name' => 'Pages', 'icon' => 'pages', 'link' => '/admin/pages'),
			array('name' => 'SEO',   'icon' => 'seo', 'link' => '/admin/pages/seo')
        ));

		$pages = new Model_Pages();
		$layouts = $pages->get_layouts();
		$hasNewsletter = false;
		foreach ($layouts as $layout) {
			if ($layout['layout'] == 'Newsletter') {
				$hasNewsletter = $layout['id'];
			}
		}
		$this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Pages', 'link' => '/admin/pages')
        );
        $this->template->sidebar->tools = '<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="page-plugin-actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					Actions <span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="page-plugin-actions">
					<li><a href="/admin/pages/new_pag">Add Page</a></li>
					' . (Auth::instance()->has_access('enable_layout_add') ? '<li><a href="/admin/settings/edit_layout">Add Layout</a></li>' : '') .'
					' . ($hasNewsletter && Auth::instance()->has_access('enable_newsletter_add') ? '<li><a href="/admin/pages/new_pag?layout=' . $hasNewsletter . '">Add Newsletter</a></li>' : '') . '
				</ul>
			</div>';
	}

	public function action_index ()
	{
		// Create an instance of the pages module
		$pages = new Model_Pages();

		// Get attributes from the database
		$results['pages'] = $pages->get_all_pages ();

		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('pages', 'js/page_list.js', ['cachebust' => true]).'"></script>';

		//Pages icon
		$results['plugin'] = Model_Plugin::get_plugin_by_name ('pages');

		//Send database attributes to the view and load the body here.
		$this->template->body = View::factory ('pages_list', $results);
	}

    public function action_seo()
    {
        // Create an instance of the pages module
        $pages = new Model_Pages();
        $seoEntries = $this->request->post('seo');

        if ($seoEntries) {
            foreach ($seoEntries as $seo) {
                $pages->set_seo_data($seo);
            }
        }

        // Get attributes from the database
        $results['pages'] = $pages->get_all_pages();

        //Pages icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name ('pages');

        //Send database attributes to the view and load the body here.
        $this->template->body = View::factory ('seo_list', $results);
    }

	public function action_edit_pag ($pag = FALSE)
	{
        /**
		 * loads the data from the selected page and sends the body
		 */

		//Get the ID from the request
		if ($pag === FALSE) $get['id'] = $this->request->param ('id');
		else
			$get['id'] = $pag;

		$page = new Model_Pages();
		//Get the data
		$results['page_data'] = $page->get_page_data ($get['id']);
        $layout_id = $this->request->query('layout');
        if ($layout_id) {
            $results['page_data'][0]['layout_id'] = $layout_id;
        }

		//Split page name and extension
		if (strpos ($results['page_data'][0]['name_tag'], '.') === FALSE)
		{
			$results['page_data'][0]['page_name']      = $results['page_data'][0]['name_tag'];
			$results['page_data'][0]['page_extension'] = '';
		}
		else
		{
			$page_tag                                  = $results['page_data'][0]['name_tag'];
			$found_position                            = strrpos ($page_tag, '.');
			$results['page_data'][0]['page_name']      = substr ($page_tag, 0, $found_position);
			$results['page_data'][0]['page_extension'] = substr ($page_tag, $found_position + 1);
		}

		//Get the Banner-Data for the specified Page.
		$results['page_data'][0]['banner_data'] = Model_PageBanner::get_banner_data ($results['page_data'][0]['banner_photo'], TRUE);
		//@TODO: banner_photo might be replaced with a Banner-ID, when the banner is further Improved/Refactored/Rebuilt

		if (empty($results['page_data']))
		{
			$this->request->redirect ('admin/pages');

			return FALSE;
		}

		$results['categories']        = $page->get_categorys();
        $results['courses']           = ORM::factory('Course')->order_by('title')->find_all_undeleted();
        $results['course_categories'] = ORM::factory('Course_Category')->order_by('category')->find_all_undeleted();
        $results['subjects']          = ORM::factory('Course_Subject')->order_by('name')->find_all_undeleted();
		$results['layouts']           = ORM::factory('Engine_Layout')->order_by('layout')->find_all_undeleted();
		$results['pages']             = $page->get_all_pages();
        $results['messaging_enabled'] = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');
        $results['signatures']        = $results['messaging_enabled'] ? Model_Signature::search() : array();

		$use_config_file = (Settings::instance()->get('use_config_file') === '0');
		$results['theme_options'] = $use_config_file ? Model_Settings::get_site_themes_as_options($results['page_data'][0]['theme'], TRUE, TRUE) : '';

		//Loads the CSS and  javascript files
		$this->template->styles[URL::get_engine_assets_base().'css/validation.css'] = 'screen';
		$this->template->styles[URL::get_engine_plugin_assets_base ('pages').'css/page_edit.css'] = 'screen';

		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';

        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
            $this->template->styles[URL::get_engine_plugin_assets_base('messaging') . 'css/list_messages.css'] = 'screen';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('messaging') . 'js/messaging.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('messaging') . 'js/list_messages.js"></script>';
        }

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base ('pages').'js/page_edit.js"></script>';


        //Load the body
		$this->template->body = View::factory ('page_edit', $results);
	}

	/**
	 * Show edit new page
	 */
	public function action_new_pag()
	{
		self::action_edit_pag();
	}


	public function action_delete_pag()
	{
		/**
		 * Delete the selected page
		 */
		//IbHelpers::die_r($this->request->post());
		$id_pag     = (int)$this->request->param ('id');
		$page       = new Model_Pages();
		$total_rows = $page->delete_page ($id_pag);
		if ($total_rows > 0)
		{
			IbHelpers::set_message ('The page has been deleted.', 'success popup_box');
		}
		else
		{
			IbHelpers::set_message ('The page could not be deleted', 'error popup_box');
		}
		$this->request->redirect ('admin/pages');
	}

    /**
     * Save a page
     *
     * Scenarios:
     * 1. Save new page:
     *    - Update the page
     *
     * 2. Save an existing page:
     *    - Create a page
     *
     * 3. Save a new page as a draft:
     *    - Create an unpublished page
     *    - Create a copy, saved as a draft
     *
     * 4. Save an existing page as a draft:
     *    - Don't touch the existing page
     *    - Check if a draft already exists: If yes, overwrite it. If no, create a new one.
     *
     * 5. Save an existing draft as a draft
     *    - Update the draft
     *
     * 6. Save an existing draft as a page
     *    - Update the original page
     *    - Delete the draft
     */

    public function action_save_page()
    {
        $data          = $this->request->post();
        $is_new        = empty($data['pages_id']);
        $was_draft     = !empty($data['draft_of']);
        $save_as_draft = !empty($data['draft']);

        // Redirect the user away, if they attempt to save a draft, without the necessary permission.
        if ($save_as_draft) {
            Ibhelpers::permission_redirect('pages_save_draft', '/admin/pages');
        }

        // ID and object for the original page (the non-draft version).
        $main_page_id = $is_new ? null : ($was_draft ? $data['draft_of'] : $data['pages_id']);
        $page = new Model_Page($main_page_id);

        // Save the original page.
        if (!$save_as_draft || $is_new) {
            $page->values($data);
            $page->save_data($data);
            $page->draft_of = null;

            // If creating a draft on the first save, the original page is not to be published.
            if ($is_new && $save_as_draft) {
                $page->set('publish', 0);
            }

            // If making a draft live, ensure the page is published.
            if ($was_draft && $save_as_draft) {
                $page->set('publish', 1);
            }

            $page->save();
            $main_page_id = $page->id;
        }

        // Save the draft.
        if ($save_as_draft) {
            // Load existing draft for the page or create a new one.
            $draft = ORM::factory('Page')->where('draft_of','=', $main_page_id)->find_undeleted();

            unset($data['id']);
            $draft->values($data);
            $draft->save_data($data);
            $draft->set('draft_of', $main_page_id);
            $draft->save_with_moddate();
        }

        // If the draft is overwriting the original, delete the draft after the original has been updated.
        if ($was_draft && !$save_as_draft) {
            $draft = ORM::factory('Page')->where('draft_of', '=', $main_page_id)->find_undeleted();
            if ($draft->id) {
                $draft->set_deleted();
                $draft->save_with_moddate();
            }
        }

        // Display notice for what just happened
        if ($was_draft && !$save_as_draft) {
            IbHelpers::set_message('Draft has overwritten the original page.', 'success');
        } elseif ($save_as_draft) {
            IbHelpers::set_message('Draft has been saved.', 'success');
        } else {
            IbHelpers::set_message('Page has been saved.', 'success');
        }

        // Redirect the user
        if ($this->request->post('action') == 'save_and_exit' || $save_as_draft) {
            // ... to the list if they clicked "Save & Exit" or "Save as Draft"
            $this->request->redirect('admin/pages');
        } else {
            // ... to the non-draft page, if the clicked "Save"
            $this->request->redirect('admin/pages/edit_pag/'.$main_page_id);
        }
    }


    /**
     * Ajax call: Change the publish status of the selected page.
     */
    public function action_publish()
    {
        $id = $this->request->param('id');
        $page = new Model_Page($id);

        if ($page->id) {
            // Set publish status to the opposite of its current value
            $publish = $page->publish == 1 ? 0 : 1;
            $page->set('publish', $publish);
            $page->save_with_moddate();
            $message = $publish ? __('Page has been published') : __('Page has been unpublished');
            $success = true;
        } else {
            $success = false;
            $message = 'Page does not exist';
        }

        $this->auto_render = false;
        echo json_encode(['success' => $success, 'message' => $message]);
    }

	public function action_ajax_get_image_preview ()
	{
		//IbHelpers::die_r($this->request->post());

		$image_to_preview = $this->request->post ('image_to_preview');

		$image_preview = '<strong>'.$image_to_preview.'</strong><br />
				<img
						src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$image_to_preview, 'banners'.DIRECTORY_SEPARATOR.'_thumbs_cms').'"

						alt="'.$image_to_preview.'"/><br /><br />';

		$this->auto_render = FALSE;
		$this->response->body ($image_preview);
	}

	public function action_ajax_get_banner_sequence_data ()
	{
//		IbHelpers::die_r($this->request->post());

		$sequence_data = array ('sequence_images_list' => '', 'banner_preview' => '');

		$sequence_to_get = $this->request->post ('banner_sequence');

		$sequence_data['sequence_images_list'] = Model_PageBanner::get_banner_sequence_images_as_options ($sequence_to_get);
		$sequence_data['banner_preview']       = Model_PageBanner::get_banner_preview ($sequence_to_get, 'sequence_list');

		$this->auto_render = FALSE;
		$this->response->body (json_encode ($sequence_data));
	}

    public function action_ajax_get_banner_data()
    {
        $id = $this->request->param('id');
        if ($id == 'new')
        {
            $map_data['name'] = '';
            $map_data['html'] = '';
        }
        else
        {
            $data = Model_PageBanner::get_maps($id);
            $map_data['name'] = $data['name'];
            $map_data['html'] = $data['html'];
        }
        $this->auto_render = FALSE;
        $this->response->body(json_encode($map_data));
    }

    // Ajax function to get all hex colours used in a list of stylesheets
    public function action_ajax_parse_css_colors()
    {
        $this->auto_render = FALSE;
        $options = array(CURLOPT_RETURNTRANSFER => TRUE); // return web page
        $post    = $this->request->post();
        $css     = '';
        $colors = array();

        if ( ! empty($post) AND is_array($post['links']))
        {
            // loop through each stylesheet link and merge their contents
            foreach ($post['links'] as $link)
            {
                $ch   = curl_init( $link );
                curl_setopt_array( $ch, $options );
                $css .= curl_exec( $ch );
                curl_close( $ch );
            }

            // get all instances of 3 and 6-digit hexes in the stylesheets
            preg_match_all('/#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})/', $css, $finds, PREG_OFFSET_CAPTURE);

            $hexes = array();
            if (isset($finds[0]))
            {
                foreach ($finds[0] as $find)
                {
                    $hex = $find[0];
                    // convert three-digit hex to 6-digit hex
                    if (strlen($hex) == 4)
                    {
                        $hex = '#' . $hex[1] .$hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];
                    }
                    // put unique hexes in array
                    if ( ! in_array($hex, $hexes))
                    {
                        array_push($hexes, $hex);
                        $hsl      = $this->hex_to_hsl($hex);
                        $colors[] = array('hex' => $hex, 'hsl' => $hsl);
                    }
                }
            }
        }
        else
        {
            $colors = array();
        }

        usort($colors, array('Model_Pages', 'sort_lightness'));

        $return = array();
        foreach ($colors as $color)
        {
            $return[] = $color['hex'];
        }

        $this->response->body(json_encode($return));
    }

    private function hex_to_hsl($hex)
    {
        $r   = (hexdec(substr($hex, 1, 2)) / 255);
        $g   = (hexdec(substr($hex, 3, 4)) / 255);
        $b   = (hexdec(substr($hex, 5, 6)) / 255);

        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );

        $h = 0;
        $s = 0;
        $l = ( $max + $min ) / 2;
        $d = $max - $min;

        if( $d == 0 ){
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs( 2 * $l - 1 ) );

            switch( $max ){
                case $r:
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;

                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }

        return array( round( $h, 2 ), round( $s, 2 ), round( $l, 2 ) );
    }



	function after ()
	{
		// Load the messages from the IbHelper.
		$messages = IbHelpers::get_messages ();

		// If there are messages
		if ($messages)
		{
			// Add the message to the alert string if it exists
			if (isset($this->template->body->alert))
			{
				$this->template->body->alert = $this->template->body->alert.$messages;
			} // Else create an alert string
			else
			{
				$this->template->body->alert = $messages;
			}
		}

		parent::after ();

	}

	public function action_getSubMenus ()
	{
		$category          = $_POST['category'];
		$this->auto_render = FALSE;
		Model_Menus::getSubMenu ($category);
	}

    // AJAX function for generating sublist in the plugins' dropdown
    public function action_ajax_get_submenu($data_only = false)
    {
        $model           = new Model_Pages;
        $items           = $model->get_all_pages();
        $return['link']  = 'edit_pag';
        $return['items'] = array();

        for ($i = 0; $i < sizeof($items) && $i < 10; $i++) {
            $title = ($items[$i]['name_tag'] == '') ? $items[$i]['title'] : $items[$i]['name_tag'];
            // Replace hyphens with spaces, and remove .html, then Capitalize first letter. 
            $title = ucfirst(str_replace('.html','', str_replace('-' , ' ', $title)));
            $return['items'][] = array(
                'id'    => $items[$i]['id'],
                'title' =>  $title
            );
        }

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
        }
    }


}
