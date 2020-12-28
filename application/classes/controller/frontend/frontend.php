<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Frontend extends Controller
{
    /**
     * This function render a view, is useful for reload snippets through AJAX (example update mini cart)
     * @Example: http://garretts.websitecms.dev/frontend/frontend/get_template/mini_cart
     *
     */
    final public function action_get_template(){
        $view = $this->request->param('id');
        //The view name can only content leter, numbers and the symbol "-" and "_"
        $view = preg_replace("/[^a-zA-Z0-9_-]+/", "", $view);

		$template_folder_path = @Kohana::$config->load('config')->template_folder_path;

		if ($template_folder_path AND Kohana::find_file('views', 'templates/'.$template_folder_path.'/'.$view))
		{
			$html_view = View::factory('templates/'.$template_folder_path.'/' . $view)->set('skip_comments_in_beginning_of_included_view_file',true)->render();
		}
		else
		{
			$html_view = View::factory('templates/default/' . $view)->set('skip_comments_in_beginning_of_included_view_file',true)->render();
		}
        $this->response->body( $html_view );
    }

	public function action_js_error_log()
	{
		$this->response->headers('Content-type', 'application/json; charset=utf-8');
		$error_id = Model_Errorlog::save(null);
		echo json_encode(array('errorlog_id' => $error_id));
	}

    public function action_eventcalendar_items()
    {
        $courses = Model_Plugin::is_loaded('courses') ? Model_Courses::get_for_eventcalendar() : [];
        $events  = Model_Plugin::is_loaded('events')  ? Model_Event::get_for_eventcalendar()   : [];
        $news    = Model_Plugin::is_loaded('news')    ? Model_News::get_for_eventcalendar()    : [];
        $results = $courses + $events + $news;

        $this->response->headers('Content-Type','application/json');
        $this->response->body(json_encode($results));
        $this->auto_render = false;
    }

    public function action_finder_html()
    {
        $view = View::factory('finder_menu')->set('is_external', true);
        $this->auto_render = false;
        $this->response->body($view);
    }
}
