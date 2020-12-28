<?php defined ('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Pages extends Controller_Api
{
	public function action_list()
	{
		$p = new Model_Pages();
		$pages = $p->get_all_pages();

		$this->response_data['success'] = 1;
		$this->response_data['msg'] = '';
		$this->response_data['pages'] = array();
		foreach ($pages as $page) {
            $page['content'] = str_replace("href='/", "href='" . URL::site(), $page['content']);
			$page['content'] = str_replace('href="/', 'href="' . URL::site(), $page['content']);
            $page['content'] = str_replace('src="/', 'src="' . URL::site(), $page['content']);
			$this->response_data['pages'][] = array(
				'id' => $page['id'],
				'title' => $page['title'],
				'content' => $page['content'],
				'name_tag' => $page['name_tag'],
				'category' => $page['category'],
			);
		}
	}

	public function action_details()
	{
		$page = Model_Pages::get_page($this->request->query('id'));
		$page = $page[0];

		$page['banner_data'] = Model_PageBanner::get_banner_data ($page['banner_photo'], TRUE);
        $page['content'] = str_replace("href='/", "href='" . URL::site(), $page['content']);
        $page['content'] = str_replace('href="/', 'href="' . URL::site(), $page['content']);
        $page['content'] = str_replace('src="/', 'src="' . URL::site(), $page['content']);
		$this->response_data['success'] = 1;
		$this->response_data['msg'] = '';
		$this->response_data['page'] = array(
			'id' => $page['id'],
			'title' => $page['title'],
			'content' => $page['content'],
			'banner_image' => $page['banner_image'],
			'banner_map' => $page['banner_map'],
			'banner_sequence_data' => $page['banner_sequence_data'],
			'banner_slides' => $page['banner_slides'],
			'banner' => $page['banner_data'],
			'banner_preview' => $page['banner_data']['banner_sequence'] ? Model_PageBanner::get_banner_preview($page['banner_data']['banner_sequence'], 'sequence_list') : ''
		);
	}
}
