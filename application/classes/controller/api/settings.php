<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Settings extends Controller_Api
{

    public function before()
    {
        parent::before();
    }

    public function action_variables()
    {
        switch (Kohana::$environment)
        {
            case Kohana::PRODUCTION: $value_label = 'value_live';  break;
            case Kohana::STAGING:    $value_label = 'value_stage'; break;
            case Kohana::TESTING:    $value_label = 'value_test';  break;
            default:                 $value_label = 'value_dev';   break;
        }

        $values = DB::select('variable', array($value_label, 'value'))
            ->from('engine_settings')
            ->where('expose_to_api', '=', 1)
        ->execute()
        ->as_array();
        $settings = array();
        foreach ($values as $value) {
            $settings[$value['variable']] = $value['value'];
        }
        $env = [
            Kohana::PRODUCTION => "PRODUCTION",
            Kohana::STAGING => "STAGING",
            Kohana::TESTING => "TESTING",
            Kohana::DEVELOPMENT => "DEVELOPMENT",
        ];
        $settings['environment'] = $env[Kohana::$environment];

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['variables'] = $settings;
    }
}