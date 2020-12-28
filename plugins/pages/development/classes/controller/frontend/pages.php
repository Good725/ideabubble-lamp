<?php
defined('SYSPATH') OR die('No Direct Script Access');

final class Controller_Frontend_Pages extends Controller_Template {

    public function action_ajax_search_autocomplete()
    {
        $term = $this->request->query('term');
        $pages = ORM::factory('Page')->search($term)->limit(10)->find_all_published()->as_array();

        $results = [];
        foreach ($pages as $page) {
            $results[] = ['id' => $page->id, 'value' => $page->title, 'label' => $page->title, 'name_tag' => $page->name_tag];
        }

        $this->auto_render = false;
        echo json_encode($results);
    }

    public function action_search()
    {
        $term  = $this->request->query('term');

        // Get matching pages and matching courses
        $pages   = ORM::factory('Page')->search($term, ['search_content' => true])->find_all_published()->as_array();
        $courses = ORM::factory('Course')->search($term)->find_all_available()->as_array();

        // Combine results
        $results = $pages + $courses;

        // Ordered the merged array by relevance and by title
        usort($results, function($a, $b) { return $a->search_title   <= $b->search_title;   });
        usort($results, function($a, $b) { return $a->is_title_match <= $b->is_title_match; });

        $this->template = View::factory('front_end/search_results');
        $this->template->theme = Model_Engine_Theme::get_current_theme();
        $this->template->results = $results;
        $this->template->page_data = [
            'content'         => '',
            'layout'          => 'content',
            'seo_description' => '',
            'seo_keywords'    => '',
            'page_title'      => __('Search results'),
            'title'           => __('Search results'),
            'term'            => $term
        ];
    }
}