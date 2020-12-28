<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Api_News extends Controller_Api
{
	public function action_list()
	{
		$category = $this->request->query('category');
		if (!$category) {
			$category = 'News';
		}
		$limit = $this->request->query('limit') ?: 100;
		$offset = $this->request->query('offset') ?: 0;
		$before = $this->request->query('before');
		$after = $this->request->query('after');
		$keyword = $this->request->query('keyword');

		$news = Model_News::get_feed_for_courses_plugin_frontend($category, $limit, $offset, $before, $after, $keyword);
		$this->response_data['success'] = 1;
		$this->response_data['msg'] = '';
		$this->response_data['news'] = $news;
	}
}