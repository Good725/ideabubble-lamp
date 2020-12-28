<?php defined('SYSPATH') or die('No direct script access.');

class DashBoard {
    private static $dashboard;
    private $registered_widgets = array();
    private $registered_menu_icons = false;

    public function __construct() {

    }

    public static function factory() {
        return self::$dashboard ? self::$dashboard : self::$dashboard = new DashBoard();
    }

	public function register_widget($url, $title, $title_tag = '',$floatright=false,$id='') {
		$this->registered_widgets[$url] = array('url' => $url, 'title' => $title, 'title_tag' => $title_tag,'floatright' => $floatright,'id'=>$id);
	}

    //Set DashBoard::factory()->register_menu_icons() for display menu incons in the dashboard
    public function register_menu_icons() {
        $this->registered_menu_icons = true;
    }

    public function render_widgets() {
        $result = '';
        foreach ( $this->registered_widgets as $widget ) {
            $widget_view = View::factory('widget');
            $widget_view->title = $widget['title'];
			$widget_view->title_tag = $widget['title_tag'];
            $widget_view->floatright = $widget['floatright'];
            $widget_view->id = $widget['id'];
            $widget_view->body = Request::factory($widget['url'])->execute()->body();
            $result .= $widget_view->render();
        }
        return $result;
    }

	public static function count_registered_widgets() {
		return count(DashBoard::factory()->registered_widgets);
	}

    public static function is_registered_menu_icons() {
        return DashBoard::factory()->registered_menu_icons;
    }


}