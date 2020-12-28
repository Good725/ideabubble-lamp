<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Messages extends Controller_Cms
{

	function before() {
		parent::before();
	}

	public function action_index() {

		// Load the body here.
		$this->template->body = IbHelpers::alert('This feature is currently under construction', 'info');

	}
}
