<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Test extends Controller_Head
{

	function before()
    {
        parent::before();
	}

	public function action_index()
    {
//		$z= file_get_contents('/Users/peter/Sites/wpp/projects/technopath/plugins/technopath/development/views/select_lot.php');
//		preg_match_all('/__\(\'([^\']*)\'\)/is', $z, $matches);
//		var_dump($matches);


		//phpinfo();
		i18n::lang('es');
		echo __('Hello, world!');



		//$result = Model_Country::$countries;
		//$x = array_intersect_key($result, array_flip(array('IE','GB')));
		echo Kohana::$config->load('config')->get('allowed_countries_for_registration');

		IbHelpers::pre_r($x);


		// Load the body here.
		$this->response->body(View::factory('test')->render());
		//Response::body(View::factory('test')->render());
	}

	public function action_error_trigger()
	{
		$post = $this->request->post();
		if (count($post) > 0) {
			throw new Exception(print_r(debug_backtrace(), 1));
		}
		$this->template->body = View::factory('admin/error_trigger');
	}
}