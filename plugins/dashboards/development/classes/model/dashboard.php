<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dashboard extends ORM
{
	const CACHE_TABLE = 'plugin_dashboards_render_cache';
	protected $_table_name = 'plugin_dashboards';
	protected $_has_many   = array(
		'sharing'   => array('model' => 'Dashboards_Sharing'),
		'favorites' => array('model' => 'Dashboards_Favorites'),
		'gadgets'   => array('model' => 'Dashboards_Gadget', 'foreign_key' => 'dashboard_id')
	);

	// Check if the logged-in user has permission to edit this dashboard
	public function user_has_edit_permission()
	{
		$user         = Auth::instance();
		$user_data    = $user->get_user();
		$is_owner     = ($this->created_by == $user_data['id']); // User is the owner
		$can_edit_all = $user->has_access('edit_all_dashboards'); // User can edit all dashboards

		return ($is_owner OR $can_edit_all);
	}

	// Check if the dashboard has been shared with the logged-in user
	public function shared_with_user()
	{
		$user             = Auth::instance()->get_user();
		// Dashboard has been shared with everyone
		$shared_with_all  = (count($this->sharing->find_all()) == 0);
		// Dashboard has been shared with the logged-in user's user group
		$shared_with_user = (count($this->sharing->where('group_id', '=', $user['role_id'])->find_all()) > 0);

		return ($shared_with_user OR $shared_with_all);
	}

	public function save_data($data)
	{
		/** Save the dashboard **/
		// set added/modified parameters
		$user = Auth::instance()->get_user();
		$this->date_modified = date('Y-m-d H:i:s');
		$this->modified_by   = $user['id'];
		if ( ! $this->id)
		{
			$this->created_by = $this->modified_by;
		}
		// Save
		$saved = $this->save();

		if ($saved)
		{
			/** Save favourite data **/
			// Get favourite data as object or empty favorite object, if this not a favorite
			$favorite = ORM::factory('Dashboards_Favorite')->where('dashboard_id', '=', $this->id)->where('user_id', '=', $user['id'])->find();

			// Save favourite data, if favourite option is checked
			if (isset($data['is_favorite']) AND $data['is_favorite'] != 0)
			{
				if ($favorite->user_id == '') // favorite data does not yet exist
				{
					$favorite->set('dashboard_id', $this->id);
					$favorite->set('user_id', $user['id']);
					$favorite->save();
				}
			}
			// If favourite option is unchecked and favourite data exists, remove that data
			elseif ($favorite->user_id)
			{
				// $favorite->delete(); // not working
				DB::delete('plugin_dashboards_favorites')->where('dashboard_id', '=', $this->id)->where('user_id', '=', $user['id'])->execute();
			}

			/** Save sharing data **/
			if (isset($data['save_sharing_data']))
			{
				// remove existing sharing data
				DB::delete('plugin_dashboards_sharing')->where('dashboard_id', '=', $this->id)->execute();

				// insert new sharing data
				if (isset($data['shared_with_groups']) AND count($data['shared_with_groups']) > 0)
				{
					foreach ($data['shared_with_groups'] as $group_id)
					{
						$saved = ORM::factory('Dashboards_Sharing')
							->values(array('dashboard_id' => $this->id, 'group_id' => $group_id))
							->create();
					}
				}
			}
		}
		return $saved;
	}

	// Render the dashboard as HTML
	public function render($arg = '')
	{
		// When in edit mode, "add gadget" buttons and zones appear
		$edit_mode = ($arg == 'edit_mode');

		// Get all available report widgets. (This should really be simplified)
		$reports        = Model_Reports::get_all_accessible_report_widgets();
		$report_widgets = array();
		foreach ($reports as $query)
		{
            if ($query['dashboard']) {
                $report = new Model_Reports($query['id']);
                $report->get(true);
                $report->get_widget(true);

                try {
                    // if ($report->render_widget() != '') { // This check was drastically affecting load time
                        $report_widgets[] = $report;
                    // }
                } catch (Exception $e) {
                    Log::instance()->add(Log::ERROR, "Error rendering widget.\n" . $e->getTraceAsString());
                }
            }
		}

		$user = Auth::instance()->get_user();

        if (Request::$current->query('cache') == 'clear') {
            DB::delete(self::CACHE_TABLE)->execute();
        }
        $use_cache = Settings::instance()->get('dashboards_render_cache') == 1;
        if ($use_cache) {
            DB::delete(self::CACHE_TABLE)
                ->where('dashboard_id', '=', $this->id)
                ->and_where('user_id', '=', $user['id'])
                ->and_where('rendered', '<=', date('Y-m-d H:i:s', time() - Settings::instance()->get('dashboards_render_cache_duration')))
                ->execute();
            $cache_rendered = DB::select('*')
                ->from(self::CACHE_TABLE)
                ->where('dashboard_id', '=', $this->id)
                ->and_where('user_id', '=', $user['id'])
                ->execute()
                ->current();
        } else {
            $cache_rendered = false;
        }
		if ($cache_rendered) {
			$html = $cache_rendered['html'];
		} else {
            // Get all sparklines
            $sparklines = ORM::factory('Reports_Sparkline')->where('publish', '=', 1)->where('deleted', '=', 0)->find_all();

            // Get all gadgets (sparklines and widgets), used by this dashboard (This can probably be simplified.)
            $gadgets     = $this->gadgets->where('publish', '=', 1)->where('deleted', '=', 0)->order_by('column')->order_by('order')->find_all();
            $gadget_html = array();
            foreach ($gadgets as $i => $gadget)
            {
                $report = new Model_Reports($gadget->gadget_id);
                $report->get(TRUE);

                $sparkline = ORM::factory('Dashboards_Gadgettype')->where('stub', '=', 'sparkline')->find();
                if ($gadget->type_id == $sparkline)
                {
                    $gadget_html[$i] = $report->sparkline->render();
                }
                else
                {
                    $report->get_widget(TRUE);
                    $gadget_html[$i] = $report->render_widget();
                }
            }

            $user_favorites = ORM::factory('Dashboards_Favorite')->where('user_id', '=', $user['id'])->find_all();
            // Render the view
			$v = View::factory('dashboards_view')
					->set('dashboard',      $this)
					->set('edit_mode',      $edit_mode)
					->set('user_favorites', $user_favorites)
					->set('report_widgets', $report_widgets)
					->set('sparklines',     $sparklines)
					->set('gadgets',        $gadgets)->set('gadget_html', $gadget_html);
			$html = $v->render();
            if ($use_cache) {
                DB::insert(self::CACHE_TABLE)
                    ->values(
                        array(
                            'dashboard_id' => $this->id,
                            'user_id' => $user['id'],
                            'rendered' => date::now(),
                            'html' => $html
                        )
                    )->execute();
            }
		}
		return $html;
	}

	// Get all dashboards that have been shared with the logged-in user
	public static function get_user_accessible($published_only = FALSE, $role_id = NULL)
	{
		if (is_null($role_id))
		{
			$user = Auth::instance()->get_user();
			$role_id = $user['role_id'];
		}

		$q = DB::select('dashboard.*')
			->from(array('plugin_dashboards', 'dashboard'))
			->join(array('plugin_dashboards_sharing', 'sharing'), 'LEFT')
			->on('sharing.dashboard_id', '=', 'dashboard.id')
			->and_where_open()
				->where('sharing.group_id', '=', $role_id) // shared with the user's group
				->or_where('sharing.group_id', 'is', NULL) // shared with everyone
			->and_where_close()
			->where('deleted', '=', 0);

		if ($published_only)
		{
			$q->where('publish', '=', 1);
		}

		return $q->execute();
	}

	// Get all dashboards in a format usable with the jQuery dataTables plugin
	public static function get_for_datatable($filters)
	{
		$user         = Auth::instance()->get_user();
		$can_edit_all = (Auth::instance()->has_access('edit_all_dashboards'));

		$output    = array();
		// Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
		// These must be ordered, as they appear in the resultant table and there must be one per column
		$columns_s   = array();
		$columns_s[] = 'id';
		$columns_s[] = 'title';
		$columns_s[] = 'owner';
		$columns_s[] = 'shared_with'; // shared with

		$columns   = array();
		$columns[] = 'dashboard.id';
		$columns[] = 'dashboard.title';
		$columns[] = DB::expr("CONCAT_WS(' ', `owner`.`name`, `owner`.`surname`)");
		$columns[] = DB::expr("GROUP_CONCAT(`group`.`role` ORDER BY `group`.`role` SEPARATOR ' ')"); // shared with
		$columns[] = 'dashboard.date_created';
		$columns[] = 'dashboard.date_modified';
		// $columns[] = ''; // clone option currently not available
		$columns[] = ''; // view
		$columns[] = DB::expr('IF (`favorite`.`user_id` > 0, 0, 1)');
		$columns[] = ''; // delete

		$share_sub_select_1 = DB::select('dashboard_id')
				->from('plugin_dashboards_sharing')
				->where('group_id', '=', $user['role_id']);
		$share_sub_select_2 = DB::select(DB::expr('count(*) as `cnt`'))
				->from('plugin_dashboards_sharing')
				->where('dashboard_id', '=', $user['role_id']);

		$q = DB::select(
			'dashboard.id',
			'dashboard.title',
			array('dashboard.created_by', 'owner_id'),
			array(DB::expr("CONCAT_WS(' ', `owner`.`name`, `owner`.`surname`)"), 'owner'),
			'dashboard.date_created',
			'dashboard.date_modified',
			array('favorite.user_id','is_favorite'),
			array(DB::expr("GROUP_CONCAT(`group`.`role` ORDER BY `group`.`role` SEPARATOR '<br />')"), 'shared_with')
		)
			->from(array('plugin_dashboards',           'dashboard'))
			->join(array('engine_users',                'owner'    ), 'LEFT')->on('dashboard.created_by',  '=', 'owner.id')
			->join(array('plugin_dashboards_sharing',   'sharing'  ), 'LEFT')->on('sharing.dashboard_id',  '=', 'dashboard.id')
			->join(array('engine_project_role',         'group'    ), 'LEFT')->on('sharing.group_id',      '=', 'group.id')
			->join(array('plugin_dashboards_favorites', 'favorite' ), 'LEFT')->on('favorite.dashboard_id', '=', 'dashboard.id')
																			 ->on('favorite.user_id',      '=', DB::expr($user['id']))
			->where('dashboard.deleted', '=', 0)
			->group_by('dashboard.id');

		// Only show reports that have been shared with everyone or the logged-in user's group
		$q
			->and_having_open()
				->or_having(DB::expr("CONCAT(',', GROUP_CONCAT(`group`.`id`),',')"), 'LIKE', '%,'.$user['role_id'].',%')
				->or_having(DB::expr("count(`group`.`id`)"), '=', 0)
			->and_having_close();

		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$q->and_having_open();
			for ($i = 0; $i < count($columns_s); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns_s[$i] != '')
				{
					$q->or_having($columns_s[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_having_close();
		}
		// Individual column search
		for ($i = 0; $i < count($columns_s); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
				$q->having($columns_s[$i], 'like', '%'.$filters['sSearch_'.$i].'%');
			}
		}

		// $q_all will be used to count the total number of records.
		// It's largely the same as the main query, but won't be paginated
		
		$q_all = clone $q;

		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($columns[$filters['iSortCol_'.$i]] != '')
				{
					$q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$q->order_by('dashboard.date_modified', 'desc');

		$results = $q->execute()->as_array();

		$output['iTotalDisplayRecords'] = $q_all->execute()->count(); // total number of results
		$output['iTotalRecords']        = $q->execute()->count(); // displayed results
		$output['aaData']               = array();

		// Data to appear in the outputted table cells
		foreach ($results as $result)
		{
			$row   = array();
			$row[] = $result['id'];

			if ($can_edit_all OR $result['owner_id'] == $user['id'])
			{
				// Only link, if the user has permission to edit the dashboard
				$row[] = '<a href="/admin/dashboards/add_edit_dashboard/'.$result['id'].'" class="dashboard_link">'.$result['title'].'</a>';
			}
			else
			{
				$row[] = '<a href="#" class="dashboard_link dashboard_link_disabled">'.$result['title'].'</a>';
			}

			$row[] = $result['owner'];
			$row[] = $result['shared_with'];
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_created']);
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_modified']);
			// $row[] = '<a href="#"><span class="icon-copy"></span></a>'; // clone option currently not available
			$row[] = '<a href="/admin/dashboards/view_dashboard/'.$result['id'].'"><span class="icon-eye"></span></a>';
			// $row[] = '<a href="#" class="list_dashboards_favorite"><span class="icon-star'.($result['is_favorite'] ? '' : '-o').'"></span></a>';

			$row[] = '
				<label class="toggle_favorite">
					<span class="hidden">'.($result['is_favorite'] ? 1 : 0).'</span>
					<input class="star_checkbox toggle_favorite" type="checkbox"'.($result['is_favorite'] ? ' checked="checked"' : '').' />
					<span class="star_checkbox_icon"></span>
				</label>';

			$row[] = '<a href="#" class="list_dashboards_delete" data-id="'.$result['id'].'"><span class="icon-remove"></span></a>';
			$output['aaData'][] = $row;
		}
		$output['sEcho'] = isset($filters['sEcho']) ? intval($filters['sEcho']) : 0;

		return json_encode($output);
	}

}