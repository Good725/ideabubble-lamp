<?php defined('SYSPATH') or die('No direct script access.');

class View extends Kohana_View
{
    protected $source = null;
    protected $useSource = false;

    protected static function captureSource($source, array $kohanaViewData)
    {
        extract($kohanaViewData, EXTR_SKIP);

        if (View::$_global_data) {
            extract(View::$_global_data, EXTR_SKIP);
        }

        ob_start();
        try {
            $source = '?>' . $source;
            eval($source);
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }

        $output = ob_get_clean();
        return $output;
    }

    protected static function capture($kohana_view_filename, array $kohana_view_data)
    {
        $view = parent::capture($kohana_view_filename, $kohana_view_data);

         //Skip this debugging output for production
        if (Kohana::$environment > Kohana::STAGING
            and
            !(isset($skip_comments_in_beginning_of_included_view_file)
                or
                isset($kohana_view_data['skip_comments_in_beginning_of_included_view_file'])
                or
                isset($kohana_view_data['page_data']))) {
            $info  = "\n";
            $info .= '<!-- '.str_pad('', strlen($kohana_view_filename), '-').' -->'."\n";
            $info .= "<!-- $kohana_view_filename -->\n";
            $info .= '<!-- '.str_pad('', strlen($kohana_view_filename), '-').' -->'."\n";
            $info .= "\n";

            $view = $info.$view;
        }

        return $view;
    }

    public function setSource($source, $useSource = true)
    {
        $this->source = $source;
        $this->useSource = $useSource;
    }

    public function setUseSource($useSource)
    {
        $this->useSource = $useSource;
    }

    public function render($file = null, $source = null)
    {
        if ($file !== null) {
            $this->set_filename($file);
        }
        if ($source !== null) {
            $this->source = $source;
        }

        if (empty($this->_file) && !$this->useSource && $this->source === null) {
            throw new View_Exception('You must set the file or source to use within your view before rendering');
        }

        if ($this->useSource) {
            return View::captureSource($this->source, $this->_data);
        } else {
            // Combine local and global data and capture the output
            return View::capture($this->_file, $this->_data);
        }
    }
}
