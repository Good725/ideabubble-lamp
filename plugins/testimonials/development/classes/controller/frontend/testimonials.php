<?php defined('SYSPATH') OR die('No Direct Script Access');


final class Controller_Frontend_Testimonials extends Controller_Template
{
    public static function embed_testimonial($name = null)
    {
        $lookup = is_numeric($name) ? $name : ['title' => $name, 'deleted' => 0];
        $testimonial = new Model_Testimonial($lookup);

        return View::factory('front_end/embed_testimonial')->set(compact('testimonial'));
    }

    public function action_ajax_get_paginated_testimonials_html()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type','application/json');
        $query = $this->request->query();
        $current_page = $this->request->query('page') ?: 1;

        $testimonials = ORM::factory('Testimonial')->apply_filters($query)->order_by('date_modified', 'desc')->find_all_published();
        unset($query['page']);
        $total      = count(ORM::factory('Testimonial')->apply_filters($query)->find_all_published());

        $view = View::factory('front_end/testimonial_results')->set(compact('current_page', 'testimonials', 'total'))->render();

        echo json_encode(['html' => $view, 'count' => $total]);
    }


}