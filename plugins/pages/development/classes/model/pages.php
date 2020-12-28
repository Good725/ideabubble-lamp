<?php
defined('SYSPATH')or die('No direct script access.');

class Model_Pages extends Model_Database
{
    const PAGES_TABLE = 'plugin_pages_pages';
    const CATEGORIES_TABLE = 'plugin_pages_categorys';
    const LAYOUTS_TABLE = 'plugin_pages_layouts';

    /*               *
     * Backend Model *
     *               */

    /*
     * replacement for previous ppages_view
     * */
    public static function getPagesSelect()
    {
        $select = DB::select(
            'pages.id',
            'pages.name_tag',
            'pages.title',
            'pages.content',
            'pages.banner_photo',
            'pages.category_id',
            'pages.course_id',
            'pages.course_category_id',
            'pages.subject_id',
            'pages.parent_id',
            'pages.layout_id',
            'pages.theme',
            'pages.seo_keywords',
            'pages.seo_description',
            'pages.date_entered',
            'pages.footer',
            'pages.last_modified',
            'pages.modified_by',
            'pages.created_by',
            'pages.publish',
            'pages.deleted',
            'pages.include_sitemap',
            'pages.force_ssl',
            'pages.nocache',
            'pages.data_helper_call',
            'pages.x_robots_tag',
            'pages.draft_of',
            'categories.category',
            'layouts.layout'
        )
            ->from(array(self::PAGES_TABLE, 'pages'))
                ->join(array(self::LAYOUTS_TABLE, 'layouts'), 'left')->on('pages.layout_id', '=', 'layouts.id')
                ->join(array(self::CATEGORIES_TABLE, 'categories'), 'left')->on('pages.category_id', '=', 'categories.id');
        return $select;
    }

    /**
     *  Get all pages
     * @return Array
     */
    public function get_all_pages($order_by = '`last_modified` DESC')
    {
        $query = self::getPagesSelect()->where('pages.deleted', '=', 0);
        $order_by = preg_split('/\s+/', $order_by);
        if (count($order_by) == 2 ){
            $query->order_by(trim($order_by[0], '`'), trim($order_by[1], '`'));
        }

        return $query->execute()->as_array();
    }

    public static function sort_lightness($a, $b)
    {
        $a = $a['hsl'][2];
        $b = $b['hsl'][2];
        if ($a == $b)
        {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    static function get_pages_json($term)
    {
        $query = DB::select()
            ->from(self::PAGES_TABLE)
            ->where('deleted', '=', 0)
            ->and_where_open()
                ->where('title', 'LIKE', '%'.$term.'%')
                ->or_where('name_tag', 'LIKE', '%'.$term.'%.html')
            ->and_where_close();
        $count = clone $query;

        $return['results'] = $query->select('id','name_tag','title')->order_by('title')->limit(5)->execute()->as_array();
        $return['count']   = $count->select(array(DB::expr('count(*)'), 'count'))->execute()->get('count', 0);

        return json_encode($return);
    }

    /**
     * Get data of $id_pag page (Edit page)
     *
     * @param $id_pag
     * @return Array
     */
    public static function get_page_data($id_pag, $array = TRUE)
    {
		$query_res = self::getPagesSelect()
            ->where('pages.id', '=', $id_pag)
            ->and_where('pages.deleted', '=', 0)
            ->execute()
            ->as_array();

		if (count($query_res) == 0)
		{
			// If a page isn't found, get the columns from the query and return an associative array with empty values
			$return = array(
                array(
                    'id' => null,
                    'name_tag' => null,
                    'title' => null,
                    'content' => null,
                    'banner_photo' => null,
                    'category_id' => null,
                    'course_id' => null,
                    'course_category_id' => null,
                    'subject_id' => null,
                    'parent_id' => null,
                    'layout_id' => null,
                    'theme' => null,
                    'seo_keywords' => null,
                    'seo_description' => null,
                    'date_entered' => null,
                    'footer' => null,
                    'last_modified' => null,
                    'modified_by' => null,
                    'created_by' => null,
                    'publish' => null,
                    'deleted' => null,
                    'include_sitemap' => null,
                    'force_ssl' => null,
                    'nocache' => null,
                    'x_robots_tag' => '',
                    'category' => null,
                    'layout' => null
                )
            );
		}
		else
		{
			$return = $query_res;
		}

		return ( ! $array AND isset($return[0])) ? $return[0] : $return;
    }

    /**
     *
     * @return Array
     */
    public function get_categorys()
    {

        $query = DB::query(Database::SELECT, 'SELECT * FROM ' . self::CATEGORIES_TABLE);

        $query_res = $query->execute()->as_array();


        return $query_res;
    }

    /**
     *
     * @return Array
     */
    public function get_layouts()
    {

        $query = DB::query(Database::SELECT, 'SELECT * FROM ' . self::LAYOUTS_TABLE);

        $query_res = $query->execute()->as_array();


        return $query_res;
    }

    /**
     *
     *  Delete selected page ($id_pag)
     *
     * @param $id_pag
     * @return int
     */
    public function delete_page($id_pag)
    {
        $total_rows = DB::update(self::PAGES_TABLE)->set(array('deleted' => 1))->where('id', '=', (int)$id_pag)->execute();
        return $total_rows;
    }

    /**
     *
     * Update seo data for all pages
     *
     * @param $data
     * @return int
     */
    public function set_seo_data($data)
    {
        $total_rows = DB::query(Database::UPDATE, 'UPDATE ' . self::PAGES_TABLE . ' SET
        title = :title,
        seo_keywords = :seo_keywords,
        seo_description = :seo_description,
        footer = :footer,
        last_modified = NOW(),
        modified_by = :modified_by,
        x_robots_tag = :x_robots_tag
        WHERE id = :id ')
            ->parameters(array(
                ':id' => $data['id'],
                ':title' => $data['title'],
                ':seo_keywords' => $data['seo_keywords'],
                ':seo_description' => $data['seo_description'],
                ':footer' => $data['footer'],
                ':modified_by' => $_SESSION['admin_user']['id'],
                ':x_robots_tag' => $data['x_robots_tag']
            ))->execute();
        return $total_rows;
    }

    /**
     *
     * Update seo data for all pages
     *
     * @param $data
     * @return int
     */
    public function set_seo_data_all_pages($data)
    {
        $total_rows = DB::query(Database::UPDATE, 'UPDATE ' . self::PAGES_TABLE . ' SET
        title = :title,
        seo_keywords = :seo_keywords,
        seo_description = :seo_description,
        footer = :footer,
        last_modified = NOW(),
        modified_by = :modified_by,
        x_robots_tag = :x_robots_tag')
            ->parameters(array(
                ':title' => $data['title'],
                ':seo_keywords' => $data['seo_keywords'],
                ':seo_description' => $data['seo_description'],
                ':footer' => $data['footer'],
                ':modified_by' => $_SESSION['admin_user']['id'],
                ':x_robots_tag' => $data['x_robots_tag']
            ))->execute();
        return $total_rows;
    }

    /**
     *
     * Update selected page
     *
     * @param $data
     * @return int
     */
    public function set_page_data($data)
    {
        $name_tag = $data['page_name'];
        if (!empty($data['page_extension'])) {
            $name_tag .= '.' . $data['page_extension'];
        }
        $name_tag = $this->filter_name_tag($name_tag);

        if (isset($data['course_item_id'])) {
            // Only one of these can be set at a time
            $data['course_id'] = '';
            $data['course_category_id'] = '';
            $data['subject_id'] = '';

            if (strpos($data['course_item_id'], '-') > 0) {
                $data[explode('-', $data['course_item_id'])[0].'_id'] = explode('-', $data['course_item_id'])[1];
            }

            unset($data['course_item_id']);
        }

        if (!isset($data['pages_id']) || (int)$data['pages_id'] < 1 OR (strlen($name_tag) == 0))
            return false;

        if (isset($data['banner_type']))
        {
            switch($data['banner_type'])
            {
                case 3: // Custom sequence
                    if (isset($data['sequence_data']))
                    {
                        $cs_model = new Model_Customscroller();
                        // Add / update custom scroller sequence
                        if ($data['sequence_data']['id'] == 'new')
                        {
                            // ADD Custom Sequence
                            $data['sequence_data']['plugin'] = 'customscroller';
                            $data['plugin_item_sequence_id'] = $cs_model->add_custom_sequence($data['sequence_data']);
                        }
                        else if (trim($data['sequence_data']['id']) != '' AND (int)$data['sequence_data']['id'] > 0)
                        {
                            // UPDATE Custom Sequence
                            $sequence_updated = $cs_model->update_custom_sequence($data['sequence_data']);
                            $data['plugin_item_sequence_id'] = (int)$data['sequence_data']['id'];
                        }
                    }
                    break;

                case 4: // Google map
                    $logged_in_user = Auth::instance()->get_user();

                    $map_data['name']          = @$data['google_map_name'];
                    $map_data['html']          = @$data['google_map_code'];
                    $map_data['date_modified'] = date('Y-m-d H:i:s');
                    $map_data['modified_by']   = $logged_in_user['id'];
                    $map_data['publish']       = (@$data['google_map_publish'] == 0) ? 0 : 1;

                    if ($data['google_map_id'] == 'new')
                    {
                        $map_data['created_by']   = $map_data['modified_by'];
                        $map_data['date_created'] = $map_data['date_modified'];

                        $query = DB::insert('plugin_pages_maps')
                            ->columns(array_keys($map_data))
                            ->values($map_data)
                            ->execute();

                        $data['google_map_id'] = $query[0];
                    }
                    else {
                        DB::update('plugin_pages_maps')
                            ->set($map_data)
                            ->where('id', '=', $data['google_map_id'])
                            ->execute();
                    }
                    unset($logged_in_user);
                    break;
            }
        }


        // Don't save the page if it has an invalid custom sequence
        if (!isset($data['plugin_item_sequence_id']) OR $data['plugin_item_sequence_id'] != FALSE)
        {
            //Prepare the Banner Photo for this Page
            $banner_photo_string = Model_PageBanner::set_page_banner_photo($data);

            $parent_id = $data['parent_id'];

            if (empty($parent_id))
                $parent_id = 0;

            $total_rows = DB::query(Database::UPDATE, 'UPDATE ' . self::PAGES_TABLE . ' SET
            name_tag = :name_tag,
            title = :title,
            content = :content,
            banner_photo = :banner_photo,
            category_id = :category_id,'.
            (isset($data['course_id'])          ? 'course_id = :course_id,' : '').
            (isset($data['course_category_id']) ? 'course_category_id = :course_category_id,' : '').
            (isset($data['subject_id'])         ? 'subject_id = :subject_id,' : '').'
            parent_id = :parent_id,'.
            (isset($data['draft_of'])           ? 'draft_of = :draft_of,' : '').'
            layout_id = :layout_id,'.
            (isset($data['theme']) ? 'theme = :theme,' : '').'
            seo_keywords = :seo_keywords,
            seo_description = :seo_description,
            footer = :footer,
            last_modified = NOW(),
            modified_by = :modified_by,
            publish = :publish,
            include_sitemap = :include_sitemap,
            force_ssl = :force_ssl,
            nocache = :nocache,
            x_robots_tag = :x_robots_tag
            WHERE id = :id ')
                ->parameters(array(
                    ':id' => $data['pages_id'],
                    ':name_tag' => $name_tag,
                    ':title' => (!empty($data['title']) ? $data['title'] : $data['page_name']),
                    ':content' => $data['content'],
                    ':banner_photo' => $banner_photo_string,
                    ':category_id' => $data['category_id'],
                    ':course_id' => isset($data['course_id']) ? $data['course_id'] : '',
                    ':course_category_id' => isset($data['course_category_id']) ? $data['course_category_id'] : '',
                    ':subject_id' => isset($data['subject_id']) ? $data['subject_id'] : '',
                    ':layout_id' => $data['layout_id'],
                    ':parent_id' => $parent_id,
                    ':draft_of' => (isset($data['draft_of']) ? $data['draft_of'] : null),
                    ':theme' => isset($data['theme']) ? $data['theme'] : '',
                    ':seo_keywords' => $data['seo_keywords'],
                    ':seo_description' => $data['seo_description'],
                    ':footer' => $data['footer'],
                    ':modified_by' => $_SESSION['admin_user']['id'],
                    ':publish' => (int)$data['publish'],
                    ':include_sitemap' => (int)$data['include_sitemap'],
                    ':force_ssl' => (int)$data['force_ssl'],
                    ':nocache' => (int)$data['nocache'],
                    ':x_robots_tag' => $data['x_robots_tag']
                ))->execute();
            return $total_rows;
        }
        else
            return FALSE;

        //@TODO: GENERATE SITEMAP.XML
    }

    /**
     * Save new menu
     * @param $data
     * @return Array [0]=Inserted id, [1]=number of row affected
     */
    public function set_page_data_new($data)
    {

// @TODO: complete the CREATION of a NEW PAGE similar to: set_page_data() above

        $name_tag = $data['page_name'];
        if (!empty($data['page_extension'])) {
            $name_tag .= '.' . $data['page_extension'];
        }

        $title = (!empty($data['title'])) ? $data['title'] : $data['page_name'];

        $name_tag = $this->filter_name_tag($name_tag);
        if (!isset($data) || empty($data))
            return false;
        $total_rows = DB::query(Database::INSERT, 'INSERT INTO ' . self::PAGES_TABLE . '(
            name_tag,
            title,
            content,
            category_id,'.
            (isset($data['course_id']) ? 'course_id,' : '').
            (isset($data['course_category_id']) ? 'course_category_id,' : '').
            (isset($data['subject_id']) ? 'subject_id,' : '').'
            parent_id,
            layout_id,'.
            (isset($data['draft_of']) ? 'draft_of,' : '').
            (isset($data['theme']) ? 'theme,' : '').'
            seo_keywords,
            seo_description,
            footer,
            date_entered,
            last_modified,
            created_by,
            publish,
            include_sitemap,
            force_ssl,
            nocache,
            x_robots_tag)
            VALUES(
            :name_tag,
            :title,
            :content,
            :category_id,'.
            (isset($data['course_id']) ? ':course_id,' : '').
            (isset($data['course_category_id']) ? ':course_category_id,' : '').
            (isset($data['subject_id']) ? ':subject_id,' : '').'
            :parent_id,
            :layout_id,'.
            (isset($data['draft_of']) ? ':draft_of,' : '').
            (isset($data['theme']) ? ':theme,' : '').'
            :seo_keywords,
            :seo_description,
            :footer,
            NOW(),
            NOW(),
            :created_by,
            :publish,
            :include_sitemap,
            :force_ssl,
            :nocache,
            :x_robots_tag
            )')->parameters(array(
                ':name_tag' => $name_tag,
                ':title' => $title,
                ':content' => $data['content'],
                ':category_id' => $data['category_id'],
                ':course_id' => (isset($data['course_id']) ? $data['course_id'] : ''),
                ':course_category_id' => (isset($data['course_category_id']) ? $data['course_category_id'] : ''),
                ':subject_id' => (isset($data['subject_id']) ? $data['subject_id'] : ''),
                ':parent_id' => $data['parent_id'],
                ':layout_id' => $data['layout_id'],
                ':draft_of' => (isset($data['draft_of']) ? $data['draft_of'] : ''),
                ':theme' => (isset($data['theme']) ? $data['theme'] : ''),
                ':seo_keywords' => $data['seo_keywords'],
                ':seo_description' => $data['seo_description'],
                ':footer' => $data['footer'],
                ':created_by' => $_SESSION['admin_user']['id'],
                ':publish' => (int)$data['publish'],
                ':include_sitemap' => (int)$data['include_sitemap'],
                ':force_ssl' => $data['force_ssl'],
                ':nocache' => $data['nocache'],
                ':x_robots_tag' => $data['x_robots_tag']
            ))->execute();
        if (!empty($data['menu_group']) && $data['menu_group'] != "none") {
            DB::query(Database::INSERT, "INSERT INTO plugin_menus (category,title,link_tag,publish,deleted,menus_target) VALUES('" . $data['menu_group'] . "','" . $title . "','" . $total_rows[0] . "','" . (int)$data['publish'] . "','0','_self')")->execute();

            if (!empty($data['submenu_group']) && $data['submenu_group'] != "none") {
                DB::query(Database::UPDATE, "UPDATE plugin_menus SET parent_id = '" . $data['submenu_group'] . "',menu_order = '" . $data['order_no'] . "' WHERE category = '" . $data['menu_group'] . "' AND link_tag = " . $total_rows[0])->execute();
                DB::query(Database::UPDATE, "UPDATE plugin_menus SET has_sub = '1' WHERE id = " . $data['submenu_group'])->execute();
            }
        }
        return $total_rows;

    }

    /**
     * Filter for the page_tag (url), to lowercase + replace spaces + only allow letters and numbers
     *
     * @param $name_tag
     * @return string
     */
    public static function filter_name_tag($name_tag)
    {
        $name_tag = strtolower($name_tag);
        $allow_characters = array(".", "-", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        $filter_name_tag = array();
        for ($i = 0; $i < strlen($name_tag); $i++) {
            if (in_array($name_tag[$i], $allow_characters)) {
                $filter_name_tag[] = $name_tag[$i];
            } elseif ($name_tag[$i] == ' ') {
                $filter_name_tag[] = '-';
            }
        }
        $filter_name_tag = implode('', $filter_name_tag);
        return $filter_name_tag;
    }

    /**
     * Change the published status of the selected page
     *
     * @param $id
     * @return string
     */
    public function change_published_status($id)
    {
        $query = DB::query(Database::SELECT, 'SELECT publish FROM ' . self::PAGES_TABLE . ' WHERE id = :id')->parameters(array(':id' => $id));
        $query_res = $query->execute()->as_array();
        if (empty($query_res)) {
            $str_res = 'Error, the selected page doesn\'t exist';
        } else {
            $str_res = $query_res[0]['publish'];
            if ($str_res == 0) {
                $total_rows = DB::update(self::PAGES_TABLE)->set(array('publish' => '1'))->where('id', '=', $id)->limit(1)->execute();
            } else {
                $total_rows = DB::update(self::PAGES_TABLE)->set(array('publish' => '0'))->where('id', '=', $id)->limit(1)->execute();
            }
            if ($total_rows > 0) {
                $str_res = 'success';
            } else {
                $str_res = 'Error: Can\'t update the database';
            }

        }

        return $str_res;
    }


    //@TODO: Add Documentation-comment
    public static function get_pages_as_options($selected_page = NULL)
    {
        $pages_options = '<option value="">-- Please select --</option>';

        $pages = self::factory('Pages')->get_all_pages('`name_tag` ASC');

        foreach ($pages as $page) {
            $pages_options .= '<option value="' . $page['id'] . '"' .
                (($selected_page == $page['id']) ? ' selected="selected"' : '') . '>' .
                $page['name_tag'] . '</option>';
        }

        return $pages_options;
    }
    // End of function

    /*                   *
     * End Backend Model *
     *                   */

    /*                   *
     * Frontend Model    *
     *                   */

    /**
     * projects/<name>/views/layouts
     * @return string
     */
    public static function get_page($page, $args = false)
    {
        $published_only = is_bool($args) ? $args : (isset($args['published_only']) ? $args['published_only'] : false);
        $draft = isset($args['draft']) ? (bool) $args['draft'] : false;

        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

        // Check if a version of the page specific to the microsite exists e.g. page-url--suffix
        if ($microsite_suffix) {
            $tags = array();
            // Check both /page-url--suffix and /page-url--suffix.html
            $tags[] = str_replace('.html', '', $page.'--'.$microsite_suffix).'.html';
            $tags[] = str_replace('.html', '', $page.'--'.$microsite_suffix);

            $res = self::getPagesSelect()
                ->where('pages.name_tag', 'in', $tags)
                ->where('pages.deleted', '=', 0);

            if ($published_only) {
                $res = $res->where('pages.publish', '=', 1);
            }

            if ($draft) {
                $res->where('pages.draft_of', '>', 0);
            } else {
                $res->where(DB::expr("COALESCE(`pages`.`draft_of`,0)"), '=', 0);
            }

            $res = $res->execute()->as_array();
        }

        // Check if the page exists not specific to any microsite e.g. page-url
        if (empty($res)) {
            // check both /page-url and /page-url.html
            $tags = array();
            $tags[] = str_replace('.html', '', $page).'.html';
            $tags[] = str_replace('.html', '', $page);

            $res = self::getPagesSelect()
                ->where('pages.name_tag', 'in', $tags)
                ->where('pages.deleted', '=', 0);

            if ($published_only) {
                $res = $res->where('pages.publish', '=', 1);
            }

            if ($draft) {
                $res->where('pages.draft_of', '>', 0);
            } else {
                $res->where(DB::expr("COALESCE(`pages`.`draft_of`,0)"), '=', 0);
            }

            $res = $res->execute()->as_array();
        }

		if (!isset($res[0]))
		{
            $res = FALSE;
		}
		else
		{
			$banner_sequence_data = array();
			$banner_slides = array();
			$banner_image = '';
            $banner_map = null;
			$banner_parts = explode('|', $res[0]['banner_photo']);

			if (isset($banner_parts[3]) AND $banner_parts[2] == 'banners')
			{
                // Custom sequence
				$cs = new Model_Customscroller();
				$banner_sequence_data = $cs->get_custom_sequence_admin($banner_parts[3]);
				$banner_slides = $cs->get_custom_sequence_items_data_front_end($banner_parts[3]);
			}
            elseif ( ! empty($banner_parts[3]))
			{
                // Dynamic sequence
				$banner_data = Model_PageBanner::get_banner_data($res[0]['banner_photo']);
                $banner_slides = array();
                $banner_sequence_data = array(
                    'controls'       => 1,
                    'pagination'     => 0,
                    'rotating_speed' => 300,
                    'timeout'        => 8000
                );
                if (isset($banner_data['banner_sequence_images']))
                {
                    $max_height = null;
                    foreach ($banner_data['banner_sequence_images'] as $banner_item)
                    {
                        $image_data = Model_Media::get_by_filename($banner_item['filename'], 'banners');

                        $banner_slide                     = array();
                        $banner_slide['image']            = $banner_item['filename'];
                        $banner_slide['html']             = '';
                        $banner_slide['title']            = '';
                        $banner_slide['width']            = $image_data['width'];
                        $banner_slide['height']           = $image_data['height'];
                        $banner_slide['overlay_position'] = 'center';
                        $banner_slides[]                  = $banner_slide;

                        if (is_null($max_height) || $max_height < $image_data['height']) {
                            $max_height = $image_data['height'];
                        }
                    }

                    foreach ($banner_slides as &$slide) {
                        $slide['max_height'] = $max_height;
                    }
                }
			}
            elseif (isset($banner_parts[0]) && $banner_parts[0] == 4) {
                // Map banner
                $banner_map = Model_Pagebanner::generate_maps_banner_html($res[0]['banner_photo']);;
            }
            elseif ( ! empty($banner_parts[1]))
            {
                // Static banner
                $banner_image = $banner_parts[1];
            }
			$res[0]['banner_image'] = $banner_image;
            $res[0]['banner_map'] = $banner_map;
			$res[0]['banner_sequence_data'] = $banner_sequence_data;
			$res[0]['banner_slides'] = $banner_slides;
		}

        return $res;
    }

	public static function get_by_layout($layout)
	{
        return self::getPagesSelect()
            ->where('layouts.layout', '=', $layout)
            ->and_where('pages.publish', '=', 1)
            ->and_where('pages.deleted', '=', 0)
            ->execute()
            ->as_array();
	}

    /*
     * Please use this function for insert the pages content in to a custom template (example: ideabubble.ie home banner)
     *
     * @page String page number
     * @return String content
     */
    public static function get_raw_page($page)
    {
        $page_model = new Model_Pages();

        $page = $page_model->get_page($page);

        if ($page['0']['content']) {
            return $page['0']['content'];
        } else {
            return '';
        }
    }

    /*
    * Please use this function to generate a list of pages for the sites html sitemap
    * @return String content
    */
    /**
     * @return string
     */
    public static function get_pages_html_sitemap()
    {

        $sitemap = '<ul id="pages-sitemap"> <li>Pages: <ul>';

        $pages = self::factory('Pages')->get_all_sitemap_pages();

        foreach ($pages as $page) {
            //Add ONLY Published Pages to the sitemap
            if ($page['publish'] == 1) {
                $sitemap .= '<li><a href="./' . $page['name_tag'] . '">' .
                    $page['name_tag'] . '</a></li>';
            }
        }
        //end foreach

        $sitemap .= '</ul></li></ul>';

        return $sitemap;
    }

    //end of function

    public static function get_page_by_id($id)
    {
        $name_tag = DB::select('name_tag')
            ->from(self::PAGES_TABLE)
            ->where('id', '=', $id)
            ->or_where('name_tag', '=', $id)
            ->execute()
            ->get('name_tag');
        return $name_tag;
    }
    /*
        * Please use this function to generate a list of pages for the sites html sitemap
        * @return String content
        */
    /**
     * @return string
     */
    public static function get_pages_xml_sitemap()
    {
        //<url>
        //<loc>https://www.readerswriters.ie/home.html</loc>
        //</url>

        $sitemap = "";


        $pages = self::factory('Pages')->get_all_sitemap_pages();

        foreach ($pages as $page) {
            //Add ONLY Published Pages to the sitemap
            if ($page['publish'] == 1) {
                $sitemap .= '<url><loc>'. URL::site(htmlentities($page['name_tag'])).'</loc></url>';
            }
        }
        //end foreach

        return $sitemap;
    }

    /**
     *  Get all pages with sitemap enabled
     * @return Array
     */
    public function get_all_sitemap_pages()
    {
        $query = self::getPagesSelect()
            ->where('pages.deleted', '=', 0)
            ->and_where('pages.include_sitemap', '=', 1)
            ->execute()
            ->as_array();

        return $query;
    }

	public static function get_rendered_output($page_id)
	{
		$page = self::get_page_by_id($page_id);
		return file_get_contents(URL::site('/' . $page));
	}

	public static function get_theme_home_page($column = NULL)
	{
		$config = Kohana::$config->load('config');

		$page = FALSE;
		if ( ! empty($config->assets_folder_path))
		{
			$page = self::getPagesSelect()
                ->where('pages.theme', '=', $config->assets_folder_path)
                ->where('layouts.layout', '=', 'home')
                ->where('pages.deleted', '=', 0)
                ->execute()
                ->current();
		}

		if ( ! $page)
		{
			$default_home_page = Settings::instance()->get('default_home_page');
			$page = self::getPagesSelect()->where('pages.id', '=', $default_home_page)->execute()->current();
		}

		if ($column)
			return (isset($page[$column])) ? $page[$column] : '';
		else
			return $page;
	}

    public static function get_page_list() {
        $query = DB::query(Database::SELECT, 'SELECT id, title FROM ' . self::PAGES_TABLE . ' WHERE deleted = 0 ORDER BY title')->execute()->as_array();

        return $query;
    }

	/**
	 * Get related pages
	 * - Loop through ancestor pages and get the furthest ancestor
	 * - Get all descendants of the furthest ancestor
	 *
	 * @param	$id	int	ID of the current page
	 * @return	array	Array of related pages, with nested hierarchical structure
	 */
	public static function get_related_pages($id)
	{
		$current_page = self::get_page_data($id, FALSE);
		$ancestors    = self::get_ancestors($id);
		$top_page     = isset($ancestors[0]) ? $ancestors[0] : $current_page;
		$descendants  = self::get_descendants($top_page['id']);

		$top_page['children'] = $descendants;

		return array($top_page);
	}

	/**
	 * Get descendants of a page
	 * - Get all children of the current page and recursively get their children
	 *
	 * @param $id int	The ID of the current page
	 * @return mixed
	 */
	public static function get_descendants($id)
	{
		$children = self::getPagesSelect()
            ->where('pages.parent_id', '=', $id)
            ->where('pages.publish', '=', 1)
            ->where('pages.deleted', '=', 0)
            ->execute()
            ->as_array();

		foreach ($children as $key => $child)
		{
			$children[$key]['children'] = self::get_descendants($child['id']);
		}

		return $children;
	}

	/**
	 ** Get a page's parent and that page's parent
	 *
	 * @param $id
	 * @return array
	 */
	public static function get_ancestors($id)
	{
		$current_page = self::get_page_data($id, FALSE);
		$ancestors = array();

		// Just go back two levels. This can be replaced with a recursive loop, if necessary.
		if ($current_page['parent_id'])
		{
			$parent = self::get_page_data($current_page['parent_id'], FALSE);
			$ancestors[] = $parent;
			if ($parent['parent_id'])
			{
				$grandparent = self::get_page_data($parent['parent_id'], FALSE);
				$ancestors[] = $grandparent;
			}
		}
		return array_reverse($ancestors);
	}

}

?>