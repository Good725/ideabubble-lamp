<?php defined('SYSPATH') or die('No direct script access.');

class Model_Engine_Layout extends ORM
{
	protected $_table_name = 'plugin_pages_layouts';
    protected $_belongs_to = array(
        'template' => array('model' => 'Engine_Template')
    );

	public static function get_as_options($current)
	{
		$html = '<option value="">-- Please select --</option>';
		foreach (ORM::factory('Engine_Layout')->find_all_undeleted() as $layout)
		{
			$selected = ($layout->id == $current || $layout->layout == $current) ? ' selected="selected"' : '';
			$html .= '<option value="'.$layout->id.'"'.$selected.'>'.$layout->layout.($layout->template->stub ? '('.$layout->template->stub.')' : '').'</option>';
		}
		return $html;
	}

    public static function get_default_layout()
    {
        // Get default layout form the settings
        $setting = Settings::instance()->get('default_page_layout');
        $layout  = ORM::factory('Engine_Layout')->where('id', '=', $setting)->find_published();
        $template_folder_path = isset(Kohana::$config->load('config')->template_folder_path)
            ? Kohana::$config->load('config')->template_folder_path
            : Settings::instance()->get('template_folder_path');

        // If none, get the template's content layout
        if (!$layout->id) {
            $template = ORM::factory('Engine_template')->where('stub', '=', $template_folder_path)->find_undeleted();

            $layout = ORM::factory('Engine_Layout')->where('template_id', '=', $template->id)->where('layout', '=', 'content')->find_published();

            // If the selected template does not have a content layout, use the generic content layout
            if (!$layout->id) {
                $layout = ORM::factory('Engine_Layout')->where('template_id', 'is', null)->where('layout', '=', 'content')->find_published();
            }
        }
        return $layout;
    }

	public static function get_for_datatable($filters)
	{

		$output    = array();
		// Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
		// These must be ordered, as they appear in the resultant table and there must be one per column
		$columns   = array();
		$columns[] = 'layout.id';
        $columns[] = 'template.title';
		$columns[] = 'layout.layout';
		$columns[] = 'layout.date_created';
		$columns[] = 'layout.date_modified';
		$columns[] = 'modified_by.email';
		$columns[] = 'layout.publish';
		$columns[] = ''; //actions

		$q = DB::select('layout.id', 'layout.layout', array('template.title', 'template'), 'layout.date_created', 'layout.date_modified', 'layout.publish', array('modified_by.email', 'modified_by'))
			->from(array('plugin_pages_layouts',   'layout'))
			->join(array('engine_site_templates', 'template'), 'left')->on('layout.template_id', '=', 'template.id')
			->join(array('engine_users',       'modified_by'), 'left')->on('layout.modified_by', '=', 'modified_by.id')
			->where('layout.deleted', '=', 0);

		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$q->and_where_open();
			for ($i = 0; $i < count($columns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '')
				{
					$q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_where_close();
		}
		// Individual column search
		for ($i = 0; $i < count($columns); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
				if ($columns[$i] instanceof Database_Expression AND strpos($columns[$i]->value(), 'GROUP_CONCAT') !== FALSE)
				{
					$q->having($columns[$i], 'like', '%'.$filters['sSearch_'.$i].'%');
				}
				else
				{
					$q->and_where($columns[$i], 'like', '%'.$filters['sSearch_'.$i].'%');
				}
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
		$q->order_by('layout.date_modified', 'desc');

		$results = $q->execute()->as_array();

		$output['iTotalDisplayRecords'] = $q_all->execute()->count(); // total number of results
		$output['iTotalRecords']        = $q->execute()->count(); // displayed results
		$output['aaData']               = array();

		// Data to appear in the outputted table cells
		foreach ($results as $result)
		{
			$row   = array();
			$row[] = $result['id'];
			$row[] = $result['template'];
			$row[] = $result['layout'];
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_created']);
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_modified']);
			$row[] = $result['modified_by'];
			$row[] = '<button type="button" class="btn-link publish-toggle" data-id="'.$result['id'].'"><span class="icon-'.(($result['publish'] == 1) ? 'ok' : 'ban-circle').'"></span></button>';
			$row[] = '<ul class="list-unstyled list-actions">
				<li><a href="/admin/settings/edit_layout/'.$result['id'].'" class="edit-link"><span class="icon-pencil"></span> edit</a></li>
				<li><a href="/admin/settings/clone_layout/'.$result['id'].'"><span class="icon-copy"></span> clone</a></li>
				<li><button type="button" class="btn-link delete-button" data-id="'.$result['id'].'"><span class="icon-remove"></span> delete</button></li>
			</ul>';
			$output['aaData'][] = $row;
		}
		$output['sEcho'] = isset($filters['sEcho']) ? intval($filters['sEcho']) : 0;

		return json_encode($output);
	}

    /**
     * Get source, including the header and footer
     */
    public function get_full_source()
    {
        if ($this->template->publish == 1 && $this->template->deleted == 0) {
            return $this->template->header . $this->source . $this->template->footer;
        }
        else {
            return $this->source;
        }
    }
}