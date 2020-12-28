<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Api extends Controller
{
	protected $response_data = null;
	protected $json = true;

    function before()
	{
		parent::before();
		$this->response_data = array(
			'success' => false,
			'msg' => __('Unknown Error')
		);
	}

	public static function null_to_empty_string($value)
	{

		if (is_array($value)) {
			return array_map('Controller_Api::null_to_empty_string', $value);
		} else if ($value === null) {
			return "";
		} else {
			return $value;
		}
	}

	function after()
	{
		if ($this->json) {
			$this->auto_render = false;
		}
		parent::after();
		if ($this->json) {
			$this->response->headers('Content-type', 'application/json; charset=utf-8');

            if (is_array($this->response_data)) {
                $this->response_data = array_map('Controller_Api::null_to_empty_string', $this->response_data);
            }
			echo json_encode($this->response_data);
		}
	}
}
