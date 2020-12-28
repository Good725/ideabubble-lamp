<?php
defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Frontend_Media extends Controller_Template {

    public function action_fonts()
    {
        $this->auto_render = FALSE;
        header("Content-type: text/css; charset: UTF-8");
        $fonts = Model_Media::get_fonts(TRUE);
        $return = '';
        foreach ($fonts as $font)
        {
            $return .= '@font-face{'."\n";
            $return .= 'font-family:"'.$font['name'].'";'."\n";
            $return .= 'src:url(\''.$font['src'].'\')'."\n";
            $return .= '}'."\n";
        }
        echo $return;
        exit();
    }

}