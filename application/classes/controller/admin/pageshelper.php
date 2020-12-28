<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kosta
 * Date: 19/09/2013
 * Time: 17:39
 * To change this template use File | Settings | File Templates.
 */
defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_PagesHelper extends Controller_Cms
{
	/**
	 * Return a list of Pages.
	 *
	 * @return The list of all Pages.
	 */
	public function action_ajax_get_pages_list() {
		// Get the media model
		$plugin_model = new Model_Pages();

		// Get the complete list of Pages
		$list = array();
		foreach ($plugin_model->get_all_pages('name_tag ASC') as $item) {
			array_push($list, $item);
		}

		// Return
		$this->auto_render = false;
		$this->response->body(json_encode($list));
	}
}