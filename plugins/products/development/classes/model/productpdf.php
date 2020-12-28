<?php
require APPPATH.'/vendor/mpdf/mpdf.php';
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 17/09/2014
 * Time: 09:16
 */

class Model_ProductPDF extends Model
{
    private static $margin_top      = 0;
    private static $margin_bottom   = 0;
    private static $margin_left     = 0;
    private static $margin_right    = 0;
    private $mpdf;
    private $compression    = '';
    private $title          = '';
    private $display_mode   = '';
    private $html           = '';
    private $css            = '';
    private $filename       = '';
    private $font_size      = 14;
    private $multiplier     = 1;
    private $dpi            = 96;


    function __construct($width,$height,$font_size = 56,$page_format = NULL)
    {
		$orientation = ($width > $height) ? 'L' : 'P';
		$size        = ($page_format != NULL) ? $page_format : array($width,$height);
		$size        = ($orientation == 'L') ? $size.'-L': $size;
        $this->mpdf  = new mPDF('utf-8', $size,$font_size,'',self::$margin_left,self::$margin_right,self::$margin_top,self::$margin_bottom,0,0,$orientation);
    }

    public function generate_pdf()
    {
        //$stylesheet = file_get_contents($css);

        $this->mpdf->mirrorMargins = true;
        $this->mpdf->SetCompression($this->compression);
        $this->mpdf->SetTitle($this->title);
        $this->mpdf->cacheTables = true;
        $this->mpdf->SetDisplayMode($this->display_mode);
        $this->mpdf->SetImportUse();
        $this->mpdf->img_dpi = $this->dpi;;
        $this->mpdf->showImageErrors = true;
        $this->mpdf->WriteHTML($this->css,1);
        $this->mpdf->WriteHTML($this->html);   //
        $this->mpdf->Output('/var/tmp/'.$this->filename,'F');
    }

    public function set_compression($compression)
    {
        $this->compression = $compression ? true : false;
        return $this;
    }

    public function set_title($title)
    {
        $this->title = is_string($title) ? trim($title) : '';
        return $this;
    }

    public function set_display_mode($mode)
    {
        $this->display_mode = is_string($mode) ? trim($mode) : '';
        return $this;
    }

    public function set_html($html)
    {
        $this->html = $html;
        return $this;
    }

    public function set_dpi($dpi)
    {
        $this->dpi = $dpi;
        return $this;
    }

    public function add_html($html)
    {
        $this->html+=$html;
        return $this;
    }

    public function set_css($css)
    {
        $this->css = $css;
        return $this;
    }

    public function add_css($css)
    {
        $this->css+= $css;
        return $this;
    }

    public function set_filename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function set_multiplier($multiplier)
    {
        $this->multiplier = $multiplier;
        return $this;
    }

    public function set_margin_top($margin)
    {
        self::$margin_top = is_numeric($margin) ? intval($margin) : self::$margin_top;
    }
}