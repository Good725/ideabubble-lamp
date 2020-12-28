<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_News extends Controller_Template
{
	public $template = 'plugin_template';

	public function action_ajax_get_news_items()
	{
		$this->auto_render = FALSE;
		echo Model_News::get_plugin_items_front_end_list(NULL, $_GET['category'], TRUE, $_GET['amount'], $_GET['offset']);
	}

    public function action_ajax_get_paginated_news()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $page           = $this->request->query('page');
        $items_per_page = (int) Settings::instance()->get('news_feed_item_count');
        $items_per_page = $items_per_page ? $items_per_page : 10;
        $offset         = ($page - 1) * $items_per_page;
        $html           = Model_News::get_plugin_items_front_end_list(null, 'news', false, $items_per_page, $offset);

        $return = ['html' => $html];
        echo json_encode($return);
    }

	public function action_ajax_get_calendar_event_feed()
	{
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->body(Model_News::get_calender_items_json());
	}

    public static function embed_news_category($name = null, $intro = null)
    {
        $category = ORM::factory('News_Category')->where('category', '=', $name)->find_published();
        $news = $category->items->order_by('event_date', 'desc')->order_by('date_modified', 'desc')->find_all_published();

        return View::factory('front_end/news_category_embed')
            ->set('category', $category)
            ->set('intro', $intro)
            ->set('news', $news)
            ->render();
    }

    public function action_ajax_get_news_html()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type','application/json');

        $news = ORM::factory('News_Item')->apply_filters($this->request->query())->find_all_frontend();

        $items_html = [];
        foreach ($news as $item) {
            $items_html[] = View::factory('front_end/snippets/news_item_embed')->set('item', $item)->set('button_class', 'bg-category')->render();
        }

        echo json_encode(['items_html' => $items_html]);
    }

    public function action_ajax_get_paginated_news_html()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type','application/json');
        $query = $this->request->query();
        $media_type = $this->request->query('media_type');
        $current_page = $this->request->query('page') ?: 1;

        $news_items = ORM::factory('News_Item')->apply_filters($query)->find_all_frontend();
        unset($query['page']);
        $total      = count(ORM::factory('News_Item')->apply_filters($query)->find_all_frontend());

        $view = View::factory('front_end/news_results')->set(compact('current_page', 'media_type', 'news_items', 'total'))->render();

        echo json_encode(['html' => $view, 'count' => $total]);
    }
}