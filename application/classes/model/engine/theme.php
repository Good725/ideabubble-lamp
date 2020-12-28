<?php defined('SYSPATH') or die('No direct script access.');

class Model_Engine_Theme extends ORM
{
    protected $_table_name = 'engine_site_themes';
    protected $_belongs_to = array(
        'template'    => array('model' => 'Engine_Template'),
        'creator'     => array('model' => 'User', 'foreign_key' => 'created_by'),
        'last_editor' => array('model' => 'User', 'foreign_key' => 'modified_by')
    );

    protected $_has_many = [
        'has_variables' => ['model' => 'engine_theme_hasvariable', 'foreign_key' => 'theme_id'],
        'variables'     => ['model' => 'engine_theme_hasvariable', 'foreign_key' => 'theme_id'],
    ];

    // Get the URL for the theme's stylesheet
    public function get_url()
    {
        // This will be appended with the modification timestamp, so the styles are forced to recache if the template or theme is edited
        $template_modified = strtotime($this->template->date_modified);
        $theme_modified    = strtotime($this->date_modified);
        $last_modified     = $theme_modified > $template_modified ? $theme_modified : $template_modified;

        $theme = !empty($_GET['usetheme']) ? $_GET['usetheme'] : $this->stub;

        return '/frontend/assets/theme_css/'.$theme.'?ts='.$last_modified;
    }

    public function get_parsed_styles()
    {
        // Get all styles needed for the front end.
        $styles = '';

        // Block editor styles (force re-cache every time the file is edited)
        $modified_time = filemtime(APPPATH.'assets/shared/css/frontend/block_editor.css');
        $styles  .= "@import url('/engine/shared/css/frontend/block_editor.css?cb=".$modified_time."');\n";

        // Survey styles, if the plugin is used
        if (Model_Plugin::is_loaded('surveys')) {
            $styles  .= "@import url('/engine/plugins/surveys/css/frontend/survey.css');\n";
        }

        // Imports must be placed before other CSS, so extract the imports from the template and theme and put them on top
        $import_pattern = '/\@import\s?url\s*\((.*)\);\s*\n/';

        if (preg_match_all($import_pattern, $this->template->styles.$this->styles, $matches)) {
            if (isset($matches[1])) {
                foreach ($matches[1] as $url) {
                    $styles .= "@import url(".$url.");\n";
                }
            }
        }

        // Put these comment blocks at the start, so it is clear which are template styles and which are theme styles
        $styles .= "/*====================================*\\\n Template \"".$this->template->title."\" styles\n\\*====================================*/\n";
        $styles .= preg_replace($import_pattern, '', $this->template->styles);
        $styles .= "\n\n\n\n/*====================================*\\\n Theme \"".$this->title."\" styles\n\\*====================================*/\n";
        $styles .= preg_replace($import_pattern, '', $this->styles);

        // Fill in variables
        $variables       = ORM::factory('Engine_Theme_Variable')->find_all_published()->as_array('id');
        $theme_variables = $this->variables->find_all()->as_array('variable_id');

        foreach ($variables as $key => $variable) {
            $value = (isset($theme_variables[$key])) ? $theme_variables[$key]->value : $variable->default;
            $styles = str_replace('$'.$variable->variable, $value, $styles);
        }

        // Run functions
        $styles = preg_replace_callback(
            '/scale_color\((.*)\,(.*)\)/',
            function($matches) {
                return self::scale_color($matches[1], trim($matches[2]));
            },
            $styles
        );

        return $styles;
    }

    /* Ues instead of the standard ORM "save()" or "save_with_moddate()" functions in order to save variable relationships */
    public function save_with_variables($variables)
    {
        try {
            Database::instance()->begin();

            // Save the theme
            parent::save_with_moddate();

            // Clear existing variables. Then insert new ones.
            DB::delete('engine_site_theme_has_variables')->where('theme_id', '=', $this->id)->execute();
            foreach ($variables as $key => $value) {
                DB::insert('engine_site_theme_has_variables', array('theme_id', 'variable_id', 'value'))->values(array($this->id, $key, $value))->execute();
            }

            Database::instance()->commit();
        }
        catch (Exception $e) {
            /* If some, but not all of the above queries run, data will be lost. So rollback, if there is an error. */
            Database::instance()->rollback();

            /* Display error message and write to app logs */
            Log::instance()->add(Log::ERROR, "Error saving theme.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Error saving theme. See the application logs for more information.', 'danger popup_box');
        }
    }


    /* Get the theme specified in the settings/config */
    public static function get_current_theme()
    {
        $current_page = ORM::factory('Page')->where_is_current()->find_published();

        // See if the theme is overwritten at page-level
        if ($current_page->theme) {
            $theme_name = $current_page->theme;
        }
        elseif (Settings::instance()->get('use_config_file')) {
            $theme_name = @Kohana::$config->load('config')->assets_folder_path;
        }
        else {
            $theme_name = Settings::instance()->get('assets_folder_path');
        }
        $theme = ORM::factory('Engine_Theme')->where('stub', '=', $theme_name)->find_published();

        return $theme;
    }


    // Convert hex to HSL, adjust the lightness and convert back to hex
    public static function scale_color($hex, $adjust)
    {
        $hsl = self::hex_to_hsl($hex);

        if ($adjust >= 0 && $hsl[2]) $hsl[2] = $hsl[2] + (1 - $hsl[2]) * $adjust;
        if ($adjust < 0  && $hsl[2]) $hsl[2] = $hsl[2] + ($hsl[2]) * $adjust;

        return self::hsl_to_hex($hsl);
    }


    public static function hex_to_hsl($hex) {
        $hex = trim(str_replace('#', '', $hex));

        if (strlen($hex) == 3) {
            list($R, $G, $B) = str_split($hex, 1);
            $R = $R.$R;
            $G = $G.$G;
            $B = $B.$B;

            $hex = $R.$R.$G.$G.$B.$B;
        }

        $hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
        $rgb = array_map(function($part) {
                return hexdec($part) / 255;
            }, $hex);

        $max = max($rgb);
        $min = min($rgb);

        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch($max) {
                case $rgb[0]:
                    $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                    break;
                case $rgb[1]:
                    $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                    break;
                case $rgb[2]:
                    $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                    break;
            }

            $h /= 6;
        }

        return array($h, $s, $l);
    }

    public static function hsl_to_hex($hsl)
    {
        list($h, $s, $l) = $hsl;

        if ($s == 0) {
            $r = $g = $b = 1;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = self::hue_to_rgb($p, $q, $h + 1/3);
            $g = self::hue_to_rgb($p, $q, $h);
            $b = self::hue_to_rgb($p, $q, $h - 1/3);
        }

        return '#'.self::rgb_to_hex($r) . self::rgb_to_hex($g) . self::rgb_to_hex($b);
    }

    public static function hue_to_rgb($p, $q, $t) {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

        return $p;
    }

    public static function rgb_to_hex($rgb) {
        return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
    }

    public function get_variable($variable_name)
    {
        return $this
            ->has_variables
            ->with('variable')
            ->where('variable.variable', '=', $variable_name)
            ->find()
            ->value;
    }
}
