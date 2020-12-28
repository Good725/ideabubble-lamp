<?php
class Controller_Robots extends Controller
{
    public function action_index()
    {
        $this->action_robots();
    }

    public function action_robots()
    {
        $this->response->headers('Content-Type', 'text/plain');
        $robots = Settings::instance()->get('robots_txt');
        $this->response->body($robots);
    }
}
?>