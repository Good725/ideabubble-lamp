<?php
Class Controller_Admin_Propman extends Controller_cms{

    function before()
    {
        parent::before();

        // Menu items and breadcrumbs
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array(
            'Property Manager' => array(
                array('name' => 'Groups',          'link' => 'admin/propman/groups'),
                array('name' => 'Properties',      'link' => 'admin/propman/'),
                array('name' => 'Building Types',  'link' => 'admin/propman/buildingtypes'),
                array('name' => 'Property Types',  'link' => 'admin/propman/propertytypes'),
                array('name' => 'Facilities',      'link' => 'admin/propman/facilitygroups'),
                array('name' => 'Suitabilities',   'link' => 'admin/propman/suitabilitygroups'),
                array('name' => 'Periods',         'link' => 'admin/propman/periods'),
                array('name' => 'Rate Cards',      'link' => 'admin/propman/rate_cards')
            )
        );
        if (Auth::instance()->has_access('propman_bookings') == 1) {
            $this->template->sidebar->menus['Property Manager'][] =
                array('name' => 'Bookings',        'link' => 'admin/propman/bookings');
            $this->template->sidebar->menus['Property Manager'][] =
                array('name' => 'Payments',        'link' => 'admin/propman/payments');
        }
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',       'link' => '/admin'),
            array('name' => 'Properties', 'link' => '/admin/propman')
        );

        $this->template->styles['/engine/plugins/propman/css/propman.css'] = 'screen';
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1Bgv2qJYAGDAMfr8ZHiKVxPCMEjBZHtw"></script>';
        $this->template->scripts[] = '<script src="/engine/plugins/propman/js/propman.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validate.min.js"></script>';
    }

    public function action_index()
    {
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_property/new" class="btn btn-primary">'.__('New Property').'</a>';
        $this->template->body           = View::factory('list_properties');
        $this->template->body->properties = Model_Propman::propertyList();
    }

    public function action_edit_property()
    {
        $id = $this->request->param('id');
        $groupId = $this->request->query('group_id');
        $property = Model_Propman::propertyGet($id);
        $bookedDays = Model_Propman::getBookedDays($id);
        if ($groupId) {
            $property['group_id'] = $groupId;
        }
        $post = $this->request->post();
        if (isset($post['name'])) {
            $id = Model_Propman::propertySet(
                $id,
                $post['group_id'],
                $post['name'],
                $post['building_type_id'],
                $post['property_type_id'],
                $post['refcode'],
                $post['beds_single'],
                $post['beds_double'],
                $post['beds_king'],
                $post['beds_bunks'],
                $post['max_occupancy'],
                $post['rooms_ensuite'],
                $post['rooms_bathrooms'],
                $post['summary'],
                $post['description'],
                $post['address1'],
                $post['address2'],
                $post['country_id'],
                $post['county_id'],
                $post['city'],
                $post['eircode'],
                $post['latitude'],
                $post['longitude'],
                @$post['facility'],
                @$post['surcharge'],
                @$post['suitability'],
                @$post['shared_media_id'],
                @$post['linked_property_id'],
                @$post['has_ratecard_id'],
                @json_decode($post['calendar'], true),
                $post['override_group_calendar'],
                $post['published']
            );
			if (is_numeric($id)) {
				IbHelpers::set_message('The Property: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
				if ($post['save_exit'] == 'false')
                {
                    $this->request->redirect('/admin/propman/edit_property/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/');
				}
			} else {
                IbHelpers::alert('Unable to save Property', 'error popup_box');
            }
        }

        $sharedMedia = new Model_Sharedmedia();
        $contentImages = $sharedMedia->get_shared_media_items()->as_array();
        $folder = Kohana::$config->load('config')->project_media_folder;
        foreach ($contentImages as $i => $image) {
            $contentImages[$i]['url'] = Model_Media::get_path_to_media_item_admin($folder, $image['filename'], 'content');
        }

		$this->template->scripts[] = '<script src="/engine/plugins/media/js/image_edit.js"></script>';
		$this->template->body = View::factory('edit_property');
        $this->template->body->contentImages = $contentImages;
        $this->template->body->buildingTypes = Model_Propman::buildingTypesList();
        $this->template->body->propertyTypes = Model_Propman::propertyTypesList();
        $this->template->body->groups = Model_Propman::groupsList();
        $this->template->body->facilityGroups = Model_Propman::facilityGroupsList();
        $this->template->body->facilityTypes = Model_Propman::facilityTypesList();
        $this->template->body->suitabilityGroups = Model_Propman::suitabilityGroupsList();
        $this->template->body->suitabilityTypes = Model_Propman::suitabilityTypesList();
        $this->template->body->linkableProperties = Model_Propman::propertyList(array(),$id);
        $this->template->body->linkableRatecards = Model_Propman::ratecardsList();
        $this->template->body->periods = Model_Propman::periodsList();
        $this->template->body->periods = Model_Propman::periodsCalendarList();
        $this->template->body->property = $property;
        $this->template->body->bookedDays = $bookedDays;
    }

    public function action_clone_property()
    {
        $id = $this->request->param('id');
        $property = Model_Propman::propertyGet($id);
        $property['name'] .= ' - clone';
        $property['id'] = 'new';

        $sharedMedia = new Model_Sharedmedia();
        $contentImages = $sharedMedia->get_shared_media_items()->as_array();
        $folder = Kohana::$config->load('config')->project_media_folder;
        foreach ($contentImages as $i => $image) {
            $contentImages[$i]['url'] = Model_Media::get_path_to_media_item_admin($folder, $image['filename'], 'content');
        }

        $this->template->body = View::factory('edit_property');
        $this->template->body->contentImages = $contentImages;
        $this->template->body->buildingTypes = Model_Propman::buildingTypesList();
        $this->template->body->propertyTypes = Model_Propman::propertyTypesList();
        $this->template->body->groups = Model_Propman::groupsList();
        $this->template->body->facilityGroups = Model_Propman::facilityGroupsList();
        $this->template->body->facilityTypes = Model_Propman::facilityTypesList();
        $this->template->body->suitabilityGroups = Model_Propman::suitabilityGroupsList();
        $this->template->body->suitabilityTypes = Model_Propman::suitabilityTypesList();
        $this->template->body->linkableProperties = Model_Propman::propertyList();
        $this->template->body->linkableRatecards = Model_Propman::ratecardsList();
        $this->template->body->periods = Model_Propman::periodsList();
        $this->template->body->periods = Model_Propman::periodsCalendarList();
        $this->template->body->property = $property;
        $this->template->body->bookedDays = array();
    }

    public function action_publish_property()
    {
        $post = $this->request->post();
        Model_Propman::propertyPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/');
        }
    }
	
	public function action_publish_ratecard()
    {
        $post = $this->request->post();
        Model_Propman::ratecardPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/rate_cards');
        }
    }

    public function action_delete_property()
    {
        $post = $this->request->post();
        Model_Propman::propertyDelete($post['id']);
        IbHelpers::set_message('The Property: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');

        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo $post['id'];
        } else {
            $this->request->redirect('/admin/propman/');
        }
    }

    public function action_fix_property_urls()
    {
        Model_Propman::fixPropertyUrls();
        $this->request->redirect('/admin/propman');
    }

    public function action_link_property_ratecard()
    {
        $post = $this->request->post();
        $id = Model_Propman::ratecardLinkProperty($post['ratecard_id'], $post['property_id']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo json_encode($id);
        } else {
            $this->request->redirect('/admin/propman/edit_property/'. $post['property_id']);
        }
    }

    public function action_unlink_property_ratecard()
    {
        $post = $this->request->post();
        Model_Propman::ratecardUnlinkProperty($post['ratecard_id'], $post['property_id']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo json_encode(true);
        } else {
            $this->request->redirect('/admin/propman/edit_property/'. $post['property_id']);
        }
    }

    public function action_ajax_get_counties()
    {
        $post = $this->request->post();
        $counties = Model_Propman::counties($post['country_id']);
        $html = '<option value="">Please Select</option>';
        foreach($counties as $k=>$county)
        {
            $html .= '<option value="'.$k.'">'.$county.'</option>';
        }
        exit(json_encode($html));
    }

    public function action_groups()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Groups', 'link' => '/admin/propman/groups');
        $this->template->sidebar->tools         = '<a href="/admin/propman/edit_group/new" class="btn btn-primary">'.__('New Group').'</a>';

        $this->template->body      = View::factory('list_groups');
        $this->template->body->groups = Model_Propman::groupsList();
    }

    public function action_edit_group()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Groups', 'link' => '/admin/propman/groups');
		$this->template->sidebar->tools         = '<a href="/admin/propman/edit_group/new" class="btn btn-primary">'.__('New Group').'</a>';
        $id = $this->request->param('id');
        $group = Model_Propman::groupGet($id, true);
        $properties = array();
        if ($group) {
            $properties = Model_Propman::propertyList(array('group_id' => $id, 'thumbs' => true));
        }

        $data = $this->request->post();

        if (isset($data['action'])) {
            $id = Model_Propman::groupSet(
                $data['id'],
                $data['name'],
                $data['address1'],
                $data['address2'],
                $data['country_id'],
                $data['county_id'],
                $data['city'],
                $data['postcode'],
                $data['latitude'],
                $data['longitude'],
                null,
                $data['host_contact_id'],
                $data['arrival_details'],
                @json_decode($data['calendar'], true),
                $data['published']
            );
			if (is_numeric($id)) {
				IbHelpers::set_message('The Property Group: ' . $data['name'] . ' was ' . (is_numeric($data['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');

				if ($data['action'] == 'save') {
					$this->request->redirect('/admin/propman/edit_group/' . $id);
				} else {
					$this->request->redirect('/admin/propman/groups');
				}
			} else {
                IbHelpers::alert('Unable to save Property Group', 'error popup_box');
            }
        }

        //$this->template->scripts[] = '<script src="/engine/shared/js/html.js"></script>';

        $this->template->body = View::factory('edit_group');
        $this->template->body->group = $group;
        $this->template->body->ocontacts = Model_Propman::getOwnerContacts();
        $this->template->body->periods = Model_Propman::periodsList();
        $this->template->body->periods = Model_Propman::periodsCalendarList();
        $this->template->body->properties = $properties;
    }

    public function action_get_group()
    {
        $id = $this->request->query('id');
        $group = Model_Propman::groupGet($id);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($group);
    }

    public function action_clone_group()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Groups', 'link' => '/admin/propman/groups');
        $id = $this->request->param('id');
        $group = Model_Propman::groupGet($id);
        $group['name'] .= ' - clone';
        $group['id'] = 'new';

        $this->template->body = View::factory('edit_group');
        $this->template->body->group = $group;
        $this->template->body->ocontacts = Model_Propman::getOwnerContacts();
        $this->template->body->periods = Model_Propman::periodsList();
        $this->template->body->periods = Model_Propman::periodsCalendarList();
        $this->template->body->properties = array();
    }

    public function action_publish_group()
    {
        $post = $this->request->post();
        Model_Propman::groupPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/groups');
        }
    }

    public function action_delete_group()
    {
        $post = $this->request->post();
        Model_Propman::groupDelete($post['id']);
        IbHelpers::set_message('The Property Group: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/groups');
    }

    public function action_rate_cards()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Rate Cards', 'link' => '/admin/propman/rate_cards');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_rate_card/new" class="btn btn-primary">'.__('New Rate Card').'</a>';

        $this->template->body = View::factory('list_rate_cards');
        $this->template->body->ratecards = Model_Propman::ratecardsList();
    }

    public function action_edit_rate_card()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Rate Cards', 'link' => '/admin/propman/rate_cards');

        //header('content-type: text/plain');print_r(Model_Propman::getDaysInDateRange('2016-01-29', '2016-04-15'));exit();

        $id = $this->request->param('id');
        $ratecard = Model_Propman::ratecardGet($id);
        $post = $this->request->post();
        if (@$post['serialized'] == 'www') {
            $action = $post['action'];
            parse_str($post['data'], $post);
            $post['action'] = $action;
            //header('content-type: text/plain');print_r($post);exit();
        }
        if (isset($post['action'])) {
            $period = Model_Propman::periodGet($post['period_id']);
            $id = Model_Propman::ratecardSet(
                $id,
                $post['name'],
                $post['property_type_id'],
                $post['period_id'],
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $post['published'],
                isset($post['ratecard_range']) ? $post['ratecard_range'] : array(),
                isset($post['has_group_id']) ? $post['has_group_id'] : array()
            );

            if (is_numeric($id)) {
                IbHelpers::set_message('The Rate Card: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_rate_card/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/rate_cards/');
                }
            } else {
                IbHelpers::alert('Unable to save Rate Card', 'error popup_box');
            }
        }

        $this->template->body = View::factory('edit_rate_card');
        $this->template->body->ratecard = $ratecard;
        $this->template->body->periods = Model_Propman::periodsCalendarList();
        $this->template->body->propertyTypes = Model_Propman::propertyTypesList();
        $this->template->body->groups = Model_Propman::groupsList();
    }

    public function action_clone_rate_card()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Rate Cards', 'link' => '/admin/propman/rate_cards');
        $id = $this->request->param('id');
        $ratecard = Model_Propman::ratecardGet($id);
        $ratecard['name'] .= ' - clone';
        $ratecard['id'] = 'new';
        $ratecard['property_type_id'] = null;
        $ratecard['groups'] = array();

        $this->template->body = View::factory('edit_rate_card');
        $this->template->body->ratecard = $ratecard;
        $this->template->body->periods = Model_Propman::periodsCalendarList();
        $this->template->body->propertyTypes = Model_Propman::propertyTypesList();
        $this->template->body->groups = Model_Propman::groupsList();
    }

    public function action_publish_rate_card()
    {
        $post = $this->request->post();
        Model_Propman::ratecardPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/rate_cards');
        }
    }

    public function action_delete_rate_card()
    {
        $post = $this->request->post();
        try {
            Model_Propman::ratecardDelete($post['id']);
            IbHelpers::set_message('The Rate Card: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        } catch (Exception $exc) {
            IbHelpers::set_message($exc->getMessage(), 'error popup_box');
        }
        $this->request->redirect('/admin/propman/rate_cards');
    }

    public function action_ajax_delete_ratecard()
    {
        $post = $this->request->post();
        $answer = Model_Propman::ratecardDelete($post['id']);
        exit(json_encode($answer));
    }

    public function action_delete_used_ratecard()
    {
        $post = $this->request->post();
        Model_Propman::ratecardDeleteUsed($post['id']);
        IbHelpers::set_message('The Facility Group: ' . $post['id'] . ' was deleted successfully. And link to properties was removed', 'success popup_box');
        $this->request->redirect('/admin/propman/facilitygroups');
    }

    public function action_types()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/types');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_type/new" class="btn btn-primary">'.__('New Type').'</a>';

        $this->template->body = View::factory('list_types');
        $this->template->body->buildingTypes = Model_Propman::buildingTypesList();
        $this->template->body->propertyTypes = Model_Propman::propertyTypesList();
    }

    public function action_buildingtypes()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Building Types', 'link' => '/admin/propman/buildingtypes');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_buildingtype/new" class="btn btn-primary">'.__('New Building Type').'</a>';

        $this->template->body = View::factory('list_buildingtypes');
        $this->template->body->buildingTypes = Model_Propman::buildingTypesList();
    }

    public function action_edit_buildingtype()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Building Types', 'link' => '/admin/propman/buildingtypes');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_buildingtype/new" class="btn btn-primary">'.__('New Building Type').'</a>';

        $id = $this->request->param('id');
        $buildingType = Model_Propman::buildingTypeGet($id);
        $post = $this->request->post();
        if (isset($post['action'])) {
            $id = Model_Propman::buildingTypeSet($id, $post['name'], $post['published']);
            if (is_numeric($id)) {
                IbHelpers::set_message('The Building Type: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_buildingtype/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/buildingtypes/');
                }
            } else {
                IbHelpers::alert('Unable to save building type', 'error popup_box');
            }
        }

        $this->template->body = View::factory('edit_buildingtype');
        $this->template->body->buildingType = $buildingType;
    }

    public function action_delete_buildingtype()
    {
        $post = $this->request->post();
        Model_Propman::buildingTypeDelete($post['id']);
        IbHelpers::set_message('The Building Type: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/buildingtypes');
    }

    public function action_publish_buildingtype()
    {
        $post = $this->request->post();
        Model_Propman::buildingTypePublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/buildingtypes');
        }
    }

    public function action_clone_buildingtype()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Building Types', 'link' => '/admin/propman/buildingtypes');
        $id = $this->request->param('id');
        $buildingType = Model_Propman::buildingTypeGet($id);
        $buildingType['id'] = 'new';
        $buildingType['name'] .= ' - clone';

        $this->template->body = View::factory('edit_buildingtype');
        $this->template->body->buildingType = $buildingType;
    }

    public function action_propertytypes()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Property Types', 'link' => '/admin/propman/propertytypes');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_propertytype/new" class="btn btn-primary">'.__('New Property Type').'</a>';

        $this->template->body = View::factory('list_propertytypes');
        $this->template->body->propertyTypes = Model_Propman::propertyTypesList();
    }

    public function action_edit_propertytype()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Property Types', 'link' => '/admin/propman/propertytypes');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_propertytype/new" class="btn btn-primary">'.__('New Property Type').'</a>';

        $id = $this->request->param('id');
        $propertyType = Model_Propman::propertyTypeGet($id);
        $post = $this->request->post();
        if (isset($post['action'])) {
            $id = Model_Propman::propertyTypeSet($id, $post['name'], $post['bedrooms'], $post['sleep'], $post['published']);
            if (is_numeric($id)) {
                IbHelpers::set_message('The Property Type: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_propertytype/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/propertytypes/');
                }
            } else {
                IbHelpers::alert('Unable to save property type', 'error popup_box');
            }
        }

        $this->template->body = View::factory('edit_propertytype');
        $this->template->body->propertyType = $propertyType;
    }

    public function action_delete_propertytype()
    {
        $post = $this->request->post();
        Model_Propman::propertyTypeDelete($post['id']);
        IbHelpers::set_message('The Property Type: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/propertytypes');
    }

    public function action_publish_propertytype()
    {
        $post = $this->request->post();
        Model_Propman::propertyTypePublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/propertytypes');
        }
    }

    public function action_clone_propertytype()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Property Types', 'link' => '/admin/propman/propertytypes');
        $id = $this->request->param('id');
        $propertyType = Model_Propman::propertyTypeGet($id);
        $propertyType['id'] = 'new';
        $propertyType['name'] .= ' - clone';

        $this->template->body = View::factory('edit_propertytype');
        $this->template->body->propertyType = $propertyType;
    }

    public function action_facilitygroups()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Facilities', 'link' => '/admin/propman/facilitygroups');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_facilitygroup/new" class="btn btn-primary">'.__('New Facility Group').'</a>';

        $this->template->body = View::factory('list_facilitygroups');
        $this->template->body->facilityGroups = Model_Propman::facilityGroupsList();
    }

    public function action_edit_facilitygroup()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Facilities', 'link' => '/admin/propman/facilitygroups');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_facilitygroup/new" class="btn btn-primary">'.__('New Facility Group').'</a>';

        $id = $this->request->param('id');
        $facilityGroup = Model_Propman::facilityGroupGet($id, true);
        $post = $this->request->post();
        if (isset($post['action'])) {
            $types = array();
            if (isset($post['facilityType'])) {
                foreach ($post['facilityType'] as $i => $facilityType) {
                    if (isset($post['facilityTypeId'][$i]) && $facilityType != '') {
                        $types[] = array('id' => $post['facilityTypeId'][$i], 'type' => $facilityType);
                    }
                }
            }
            $id = Model_Propman::facilityGroupSet($id, $post['name'], $post['sort'], $post['published'], $types);
            if (is_numeric($id)) {
                IbHelpers::set_message('The Facility Group: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_facilitygroup/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/facilitygroups/');
                }
            } else {
                IbHelpers::alert('Unable to save facility groups', 'error popup_box');
            }
        }

        $this->template->body = View::factory('edit_facilitygroup');
        $this->template->body->facilityGroup = $facilityGroup;
    }

    public function action_ajax_delete_facilitygroup()
    {
        $post = $this->request->post();
        $answer = Model_Propman::facilityGroupDelete($post['id']);
        exit(json_encode($answer));
    }

    public function action_delete_used_facilitygroup()
    {
        $post = $this->request->post();
        Model_Propman::facilityGroupDeleteUsed($post['id']);
        IbHelpers::set_message('The Facility Group: ' . $post['id'] . ' was deleted successfully. And link to properties was removed', 'success popup_box');
        $this->request->redirect('/admin/propman/facilitygroups');
    }

    public function action_publish_facilitygroup()
    {
        $post = $this->request->post();
        Model_Propman::facilityGroupPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/facilitygroups');
        }
    }

    public function action_clone_facilitygroup()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Facilities', 'link' => '/admin/propman/facilitygroups');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_facilitygroup/new" class="btn btn-primary">'.__('New Facility Group').'</a>';

        $id = $this->request->param('id');
        $facilityGroup = Model_Propman::facilityGroupGet($id, true);
        $facilityGroup['id'] = 'new';
        $facilityGroup['name'] = $facilityGroup['name'] . ' - clone';

        $this->template->body = View::factory('edit_facilitygroup');
        $this->template->body->facilityGroup = $facilityGroup;
    }

    public function action_facilitytypes()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Facilities', 'link' => '/admin/propman/facilitygroups');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/facilitytypes');
		$this->template->sidebar->tools = '<a href="/admin/propman/edit_facilitytype/new" class="btn btn-primary">'.__('New Facility Type').'</a>';

        $this->template->body = View::factory('list_facilitytypes');
        $this->template->body->facilityTypes = Model_Propman::facilityTypesList();
    }

    public function action_edit_facilitytype()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Facilities', 'link' => '/admin/propman/facilitygroups');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/facilitytypes');
		$this->template->sidebar->tools = '<a href="/admin/propman/edit_facilitytype/new" class="btn btn-primary">'.__('New Facility Type').'</a>';

        $id = $this->request->param('id');
        $facilityType = Model_Propman::facilityTypeGet($id);
        $post = $this->request->post();
        if (isset($post['action'])) {
            $id = Model_Propman::facilityTypeSet($id, $post['name'], $post['facility_group_id'], $post['sort'], $post['published']);
            if (is_numeric($id)) {
                IbHelpers::set_message('The Facility Type: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_facilitytype/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/facilitytypes/');
                }
            } else {
                IbHelpers::alert('Unable to save facility type', 'error popup_box');
            }
        }

        $this->template->body = View::factory('edit_facilitytype');
        $this->template->body->facilityType = $facilityType;
        $this->template->body->facilityGroups = Model_Propman::facilityGroupsList();
    }

    public function action_delete_facilitytype()
    {
        $post = $this->request->post();
        Model_Propman::facilityTypeDelete($post['id']);
        IbHelpers::set_message('The Facility Type: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/facilitytypes');
    }

    public function action_publish_facilitytype()
    {
        $post = $this->request->post();
        Model_Propman::facilityTypePublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/facilitytypes');
        }
    }

    public function action_clone_facilitytype()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Facilities', 'link' => '/admin/propman/facilitygroups');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/facilitytypes');
		$this->template->sidebar->tools = '<a href="/admin/propman/edit_facilitytype/new" class="btn btn-primary">'.__('New Facility Type').'</a>';

        $id = $this->request->param('id');
        $facilityType = Model_Propman::facilityTypeGet($id);
        $facilityType['id'] = 'new';
        $facilityType['name'] .= ' - clone';

        $this->template->body = View::factory('edit_facilitytype');
        $this->template->body->facilityType = $facilityType;
        $this->template->body->facilityGroups = Model_Propman::facilityGroupsList();
    }

    public function action_suitabilitygroups()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Suitabilities', 'link' => '/admin/propman/suitabilitygroups');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_suitabilitygroup/new" class="btn btn-primary">'.__('New Suitability Group').'</a>';

        $this->template->body = View::factory('list_suitabilitygroups');
        $this->template->body->suitabilityGroups = Model_Propman::suitabilityGroupsList();
    }

    public function action_edit_suitabilitygroup()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Suitabilities', 'link' => '/admin/propman/suitabilitygroups');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_suitabilitygroup/new" class="btn btn-primary">'.__('New Suitability Group').'</a>';

        $id = $this->request->param('id');
        $suitabilityGroup = Model_Propman::suitabilityGroupGet($id, true);
        $post = $this->request->post();
        if (isset($post['action'])) {
            $types = array();
            if (isset($post['suitabilityType'])) {
                foreach ($post['suitabilityType'] as $i => $suitabilityType) {
                    if (isset($post['suitabilityTypeId'][$i]) && $suitabilityType != '') {
                        $types[] = array('id' => $post['suitabilityTypeId'][$i], 'type' => $suitabilityType);
                    }
                }
            }
            $id = Model_Propman::suitabilityGroupSet($id, $post['name'], $post['sort'], $post['published'], $types);
            if (is_numeric($id)) {
                IbHelpers::set_message('The Suitability Group: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_suitabilitygroup/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/suitabilitygroups/');
                }
            } else {
                IbHelpers::alert('Unable to save Suitabilities group', 'error popup_box popup_box');
            }
        }

        $this->template->body = View::factory('edit_suitabilitygroup');
        $this->template->body->suitabilityGroup = $suitabilityGroup;
    }

    public function action_delete_suitabilitygroup()
    {
        $post = $this->request->post();
        Model_Propman::suitabilityGroupDelete($post['id']);
        IbHelpers::set_message('The Suitability Group: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/suitabilitygroups');
    }

    public function action_ajax_delete_suitabilitygroup()
    {
        $post = $this->request->post();
        $answer = Model_Propman::suitabilityGroupDelete($post['id']);
        exit(json_encode($answer));
    }

    public function action_delete_used_suitabilitygroup()
    {
        $post = $this->request->post();
        Model_Propman::suitabilityGroupDeleteUsed($post['id']);
        IbHelpers::set_message('The Suitability Group: ' . $post['id'] . ' was deleted successfully. And link to properties was removed', 'success popup_box');
        $this->request->redirect('/admin/propman/suitabilitygroups');
    }

    public function action_publish_suitabilitygroup()
    {
        $post = $this->request->post();
        Model_Propman::suitabilityGroupPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/suitabilitygroups');
        }
    }

    public function action_clone_suitabilitygroup()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Suitabilities', 'link' => '/admin/propman/suitabilitytypes');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Groups', 'link' => '/admin/propman/suitabilitygroups');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_suitabilitygroup/new" class="btn btn-primary">'.__('New Suitability Group').'</a>';

        $id = $this->request->param('id');
        $suitabilityGroup = Model_Propman::suitabilityGroupGet($id, true);
        $suitabilityGroup['id'] = 'new';
        $suitabilityGroup['name'] = $suitabilityGroup['name'] . '- clone';

        $this->template->body = View::factory('edit_suitabilitygroup');
        $this->template->body->suitabilityGroup = $suitabilityGroup;
    }

	/* Possibly deprecated */
    public function action_suitabilitytypes()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Suitabilities', 'link' => '/admin/propman/suitabilitygroups');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/suitabilitytypes');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_suitabilitytype/new" class="btn btn-primary">'.__('New Suitability Type').'</a>';

        $this->template->body = View::factory('list_suitabilitytypes');
        $this->template->body->suitabilityTypes = Model_Propman::suitabilityTypesList();
    }

	/* Possibly deprecated */
    public function action_edit_suitabilitytype()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Suitabilities', 'link' => '/admin/propman/suitabilitygroups');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/suitabilitytypes');
		$this->template->sidebar->tools = '<a href="/admin/propman/edit_suitabilitytype/new" class="btn btn-primary">'.__('New Suitability Type').'</a>';

        $id = $this->request->param('id');
        $suitabilityType = Model_Propman::suitabilityTypeGet($id);
        $post = $this->request->post();
        if (isset($post['action'])) {
            $id = Model_Propman::suitabilityTypeSet($id, $post['name'], $post['suitability_group_id'], $post['sort'], $post['published']);
            if (is_numeric($id)) {
                IbHelpers::set_message('The Suitability Type: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                if ($post['action'] == 'save') {
                    $this->request->redirect('/admin/propman/edit_suitabilitytype/' . $id);
                } else {
                    $this->request->redirect('/admin/propman/suitabilitytypes/');
                }
            } else {
                IbHelpers::alert('Unable to save Suitability Type', 'error popup_box');
            }
        }

        $this->template->body = View::factory('edit_suitabilitytype');
        $this->template->body->suitabilityType = $suitabilityType;
        $this->template->body->suitabilityGroups = Model_Propman::suitabilityGroupsList();
    }

	/* Possibly deprecated */
    public function action_delete_suitabilitytype()
    {
        $post = $this->request->post();
        Model_Propman::suitabilityTypeDelete($post['id']);
        IbHelpers::set_message('The Suitability: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/suitabilitytypes');
    }

	/* Possibly deprecated */
    public function action_publish_suitabilitytype()
    {
        $post = $this->request->post();
        Model_Propman::suitabilityTypePublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/suitabilitytypes');
        }
    }

    public function action_clone_suitabilitytype()
    {
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Suitabilities', 'link' => '/admin/propman/suitabilitygroups');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/propman/suitabilitytypes');

        $id = $this->request->param('id');
        $suitabilityType = Model_Propman::suitabilityTypeGet($id);
        $suitabilityType['id'] = 'new';
        $suitabilityType['name'] .= ' - clone';

        $this->template->body = View::factory('edit_suitabilitytype');
        $this->template->body->suitabilityType = $suitabilityType;
        $this->template->body->suitabilityGroups = Model_Propman::suitabilityGroupsList();
    }

    public function action_periods()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Periods', 'link' => '/admin/propman/periods');
        $this->template->sidebar->tools = '<a href="/admin/propman/edit_period/new" class="btn btn-primary">'.__('New Period').'</a>';

        $this->template->body = View::factory('list_periods');
        $this->template->body->periods = Model_Propman::periodsList();
    }

    public function action_edit_period()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Periods', 'link' => '/admin/propman/periods');
		$this->template->sidebar->tools = '<a href="/admin/propman/edit_period/new" class="btn btn-primary">'.__('New Period').'</a>';

        $id = $this->request->param('id');
        $period = Model_Propman::periodGet($id);
        $post = $this->request->post();
        if (isset($post['action'])) {

			$errors = Model_Propman::periodValidate($post);

			if (count($errors) > 0)
			{
				foreach ($errors as $error)
				{
					IbHelpers::set_message($error, 'danger popup_box');
				}
				$post['starts'] = Date::dmy_to_ymd($this->request->post('starts'));
				$post['ends']   = Date::dmy_to_ymd($this->request->post('ends'));

				$period = $period ? array_merge($period, $post) : $post;
			}
			else
			{
				$id = Model_Propman::periodSet($id, $post['name'], $post['starts'], $post['ends'], $post['published']);
				if (is_numeric($id)) {
                    IbHelpers::set_message('The Period: ' . $post['name'] . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.'), 'success popup_box');
                    if ($post['action'] == 'save') {
						$this->request->redirect('/admin/propman/edit_period/' . $id);
					} else {
						$this->request->redirect('/admin/propman/periods/');
					}
				} else {
					IbHelpers::alert('Unable to save Period', 'error popup_box');
				}
			}
        }

        $this->template->body = View::factory('edit_period');
        $this->template->body->period = $period;
    }

    public function action_delete_period()
    {
        $post = $this->request->post();
        Model_Propman::periodDelete($post['id']);
        IbHelpers::set_message('The Period: ' . $post['id'] . ' was deleted successfully.', 'success popup_box');
        $this->request->redirect('/admin/propman/periods');
    }

    public function action_publish_period()
    {
        $post = $this->request->post();
        Model_Propman::periodPublish($post['id'], $post['published']);
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            echo $post['published'];
        } else {
            $this->request->redirect('/admin/propman/periods');
        }
    }

    public function action_clone_period()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Periods', 'link' => '/admin/propman/periods');
        $id = $this->request->param('id');
        $period = Model_Propman::periodGet($id);
        $period['id'] = 'new';
        $period['name'] .= ' - clone';

        $this->template->body = View::factory('edit_period');
        $this->template->body->period = $period;
    }

    public function action_import_properties()
    {
        Model_Propman::import_properties_rac();
        View::factory('import_properties');
    }

    public function action_bookings()
    {
        Model_Propman::bookingCancelIfUnpaid();
        $bookings = Model_Propman::bookingsList();
        $this->template->body = View::factory('list_bookings');
        $this->template->body->bookings = $bookings;
    }

    public function action_booking()
    {
        Model_Propman::bookingCancelIfUnpaid();
        $id = $this->request->param('id');
        $post = $this->request->post();
        if (isset($post['status'])) {
            Model_Propman::bookingSetStatus($post['id'], $post['status']);
            $this->request->redirect('/admin/propman/booking/' . $post['id']);
        }
        $booking = Model_Propman::bookingGet($id);
        $unlinkedPayments = Model_Propman::paymentslistUnlinked();
        $this->template->body = View::factory('view_booking');
        $this->template->body->booking = $booking;
        $this->template->body->unlinkedPayments = $unlinkedPayments;
    }

    public function action_payments()
    {
        Model_Propman::bookingCancelIfUnpaid();
        $payments = Model_Propman::paymentsList();
        $this->template->body = View::factory('list_payments');
        $this->template->body->payments = $payments;
    }

    public function action_payment()
    {
        Model_Propman::bookingCancelIfUnpaid();
        $post = $this->request->post();
        if (isset($post['booking_id']) ) {
            Model_Propman::bookingLinkPayment($post['booking_id'], $post['id']);
        }
        $id = $this->request->param('id');
        $payment = Model_Propman::paymentGet($id);
        $outstandingBookings = null;
        if ($payment['booking_id'] == null) {
            $outstandingBookings = Model_Propman::bookingsListOutstanding();
        }
        $this->template->body = View::factory('view_payment');
        $this->template->body->payment = $payment;
        $this->template->body->outstandingBookings = $outstandingBookings;
    }

    public function action_fix_ratecard_calendar()
    {
        Model_Propman::fixRatecardCalendar();
        echo "done";
        exit();
    }

    public function action_check_property_group_name_available()
    {
        $name = $this->request->post('name');
        $id = $this->request->post('id');
        $group = Model_Propman::groupGet($name);
        $result = array('available' => false);
        if (!$group || $group['id'] == $id) {
            $result['available'] = true;
        }
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }
}
?>
