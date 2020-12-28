<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Theme extends Controller_Api
{

    public function before()
    {
        parent::before();
    }

    public function action_default_variables()
    {
        $template_stub = Settings::instance()->get('assets_folder_path');
        $template = DB::select('id', 'title','stub','email_header_color','email_link_color')
            ->from('engine_site_themes')
            ->where('stub', '=', $template_stub)
            ->execute()
            ->current();
        $variables = DB::select('v.variable', 'v.name', 'v.default', 'hv.value')
            ->from(array('engine_site_theme_has_variables', 'hv'))
                ->join(array('engine_site_theme_variables', 'v'), 'inner')
                    ->on('hv.variable_id', '=', 'v.id')
                ->where('hv.theme_id', '=', $template['id'])
            ->execute()
            ->as_array();
        foreach ($variables as $variable) {
            $template[$variable['variable']] = $variable['value'];
        }
        $template['defaults'] = $variables;
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['template'] = $template;
    }

    public function action_get_spritesheet()
    {
        //header('content-type: image/svg+xml');
        header('content-type: text/plain; charset=utf-8');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + ((60 * 60) * 24)));
        echo IbHelpers::get_spritesheet();
        exit;
    }
}