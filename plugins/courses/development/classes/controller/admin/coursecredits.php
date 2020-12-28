<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Coursecredits extends Controller_Cms
{
    public function action_save()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $params = $this->request->post();
            $id = Model_Coursecredits::save($params);
            $response_data['success'] = 1;
            $response_data['msg'] = 'saved';
            $response_data['id'] = $id;
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_load()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $id = $this->request->query('id');
            $credit = Model_Coursecredits::load($id);
            $response_data['success'] = 1;
            $response_data['msg'] = 'load';
            $response_data['credit'] = $credit;
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_delete()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $id = $this->request->query('id');
            $deleted = Model_Coursecredits::delete($id);
            $response_data['success'] = 1;
            $response_data['msg'] = 'delete';
            $response_data['deleted'] = $deleted;
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_list()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $get = $this->request->query();
            $params = array();
            if (@$get['before']) {
                $params['before'] = $get['before'];
            }
            if (@$get['after']) {
                $params['after'] = $get['after'];
            }
            if (@$get['date']) {
                $params['date'] = $get['date'];
            }
            if (@$get['offset']) {
                $params['offset'] = $get['offset'];
            }
            if (@$get['limit']) {
                $params['limit'] = $get['limit'];
            }
            if (@$get['subject_id']) {
                $params['subject_id'] = explode(',', $get['subject_id']);
            }
            if (@$get['course_id']) {
                $params['course_id'] = explode(',', $get['course_id']);
            }
            if (@$get['schedule_ids']) {
                $params['schedule_ids'] = explode(',', $get['schedule_ids']);
            }
            if (@$get['type']) {
                $params['type'] = explode(',', $get['type']);
            }
            if (@$get['trainer_id']) {
                $params['trainer_id'] = $get['trainer_id'];
            }
            if (@$get['sSearch']) {
                $params['keyword'] = $get['sSearch'];
            }
            $response_data = Model_Coursecredits::datatable($params);
            $response_data['sEcho'] = $get['sEcho'];
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_trainer_totals()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $get = $this->request->query();
            $params = array();
            if (@$get['before']) {
                $params['before'] = $get['before'];
            }
            if (@$get['after']) {
                $params['after'] = $get['after'];
            }
            if (@$get['date']) {
                $params['date'] = $get['date'];
            }
            if (@$get['offset']) {
                $params['offset'] = $get['offset'];
            }
            if (@$get['limit']) {
                $params['limit'] = $get['limit'];
            }
            if (@$get['subject_id']) {
                if (is_array($get['subject_id'])) {
                    $params['subject_id'] = $get['subject_id'];
                } else {
                    $params['subject_id'] = explode(',', $get['subject_id']);
                }
            }
            if (@$get['course_id']) {
                if (is_array($get['course_id'])) {
                    $params['course_id'] = $get['course_id'];
                } else {
                    $params['course_id'] = explode(',', $get['course_id']);
                }
            }
            if (@$get['schedule_ids']) {
                if (is_array($get['schedule_ids'])) {
                    $params['schedule_ids'] = $get['schedule_ids'];
                } else {
                    $params['schedule_ids'] = explode(',', $get['schedule_ids']);
                }
            }
            if (@$get['type']) {
                if (is_array($get['type'])) {
                    $params['type'] = $get['type'];
                } else {
                    $params['type'] = explode(',', $get['type']);
                }
            }
            $response_data = Model_Coursecredits::trainer_totals($params);
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_trainer_calendar()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $get = $this->request->query();
            $params = array();
            if (@$get['before']) {
                $params['before'] = $get['before'];
            }
            if (@$get['after']) {
                $params['after'] = $get['after'];
            }
            if (@$get['date']) {
                $params['date'] = $get['date'];
            }
            if (@$get['offset']) {
                $params['offset'] = $get['offset'];
            }
            if (@$get['limit']) {
                $params['limit'] = $get['limit'];
            }
            if (@$get['subject_id']) {
                if (is_array($get['subject_id'])) {
                    $params['subject_id'] = $get['subject_id'];
                } else {
                    $params['subject_id'] = explode(',', $get['subject_id']);
                }
            }
            if (@$get['course_id']) {
                if (is_array($get['course_id'])) {
                    $params['course_id'] = $get['course_id'];
                } else {
                    $params['course_id'] = explode(',', $get['course_id']);
                }
            }
            if (@$get['schedule_ids']) {
                if (is_array($get['schedule_ids'])) {
                    $params['schedule_ids'] = $get['schedule_ids'];
                } else {
                    $params['schedule_ids'] = explode(',', $get['schedule_ids']);
                }
            }
            if (@$get['type']) {
                if (is_array($get['type'])) {
                    $params['type'] = $get['type'];
                } else {
                    $params['type'] = explode(',', $get['type']);
                }
            }
            $response_data = Model_Coursecredits::get_calendar($params);
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_calendar_totals()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $get = $this->request->query();
            $params = array();
            if (@$get['before']) {
                $params['before'] = $get['before'];
            }
            if (@$get['after']) {
                $params['after'] = $get['after'];
            }
            if (@$get['date']) {
                $params['date'] = $get['date'];
            }
            if (@$get['offset']) {
                $params['offset'] = $get['offset'];
            }
            if (@$get['limit']) {
                $params['limit'] = $get['limit'];
            }
            if (@$get['subject_id']) {
                if (is_array($get['subject_id'])) {
                    $params['subject_id'] = $get['subject_id'];
                } else {
                    $params['subject_id'] = explode(',', $get['subject_id']);
                }
            }
            if (@$get['course_id']) {
                if (is_array($get['course_id'])) {
                    $params['course_id'] = $get['course_id'];
                } else {
                    $params['course_id'] = explode(',', $get['course_id']);
                }
            }
            if (@$get['schedule_ids']) {
                if (is_array($get['schedule_ids'])) {
                    $params['schedule_ids'] = $get['schedule_ids'];
                } else {
                    $params['schedule_ids'] = explode(',', $get['schedule_ids']);
                }
            }
            if (@$get['type']) {
                if (is_array($get['type'])) {
                    $params['type'] = $get['type'];
                } else {
                    $params['type'] = explode(',', $get['type']);
                }
            }

            if (@$get['unit']) {
                $params['unit'] = $get['unit'];
            } else {
                $params['unit'] = 'credit';
            }
            $response_data = Model_Coursecredits::get_calendar_totals($params);
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    public function action_stats()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('courses_credits')) {
            $response_data['success'] = 0;
            $response_data['msg'] = 'Access Denied';
        } else {
            $get = $this->request->query();
            $params = array();
            if (@$get['before']) {
                $params['before'] = $get['before'];
            }
            if (@$get['after']) {
                $params['after'] = $get['after'];
            }
            if (@$get['date']) {
                $params['date'] = $get['date'];
            }
            if (@$get['subject_id']) {
                if (is_array($get['subject_id'])) {
                    $params['subject_id'] = $get['subject_id'];
                } else {
                    $params['subject_id'] = explode(',', $get['subject_id']);
                }
            }
            if (@$get['course_id']) {
                if (is_array($get['course_id'])) {
                    $params['course_id'] = $get['course_id'];
                } else {
                    $params['course_id'] = explode(',', $get['course_id']);
                }
            }
            if (@$get['schedule_ids']) {
                if (is_array($get['schedule_ids'])) {
                    $params['schedule_ids'] = $get['schedule_ids'];
                } else {
                    $params['schedule_ids'] = explode(',', $get['schedule_ids']);
                }
            }
            if (@$get['type']) {
                if (is_array($get['type'])) {
                    $params['type'] = $get['type'];
                } else {
                    $params['type'] = explode(',', $get['type']);
                }
            }

            if (@$get['unit']) {
                $params['unit'] = $get['unit'];
            } else {
                $params['unit'] = 'credit';
            }
            $response_data = Model_Coursecredits::stats($params);
        }
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }
}
