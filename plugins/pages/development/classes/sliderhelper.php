<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create menus structures
 */
class sliderhelper{
    /**
     * Output HTML code, Images in a list.
     * <ul>
     *   <li><img></img></li>
     * </ul>
     *
     * @folder String, images folder
     * @id = String, <ul> id name
     * @options String, Slider Options
     *
     * bxSlider http://bxslider.com/options
     *
     */
    public static function bxslider($folder = '',$id = '', $options = ''){
        if($folder == '' || $id == '')
            return '';
        $images = glob($folder . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        if(count($images) === 0){
            return '';
        }

        $html_list = '<ul id='. $id .'>'. PHP_EOL;
        foreach($images as $image){
			$image = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,basename($image), $id);
            $html_list .= '<li><img src="' . $image . '" /></li>'. PHP_EOL;
        }
        $html_list .= '</ul>'. PHP_EOL;



        $javascript = '<script type="text/javascript" src="'. URL::get_engine_plugin_assets_base('pages') . 'sliders/bxslider/jquery.bxslider.js"></script>'. PHP_EOL;
        $javascript .= <<<EOF
        <script type="text/javascript">

        jQuery(function(){
            $('#$id').bxSlider({
            $options
            });
        });
        </script>
EOF;


    $html_list .= $javascript . PHP_EOL;
        echo $html_list;
    }

}