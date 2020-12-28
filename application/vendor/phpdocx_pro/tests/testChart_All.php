<?php

/**
 * Unit test. Tests charts.
 *
 * @category   Phpdocx
 * @package    testing
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2.4
 * @link       http://www.phpdocx.com
 * @since      File available since Release 2.4
 */
require_once('simpletest/autorun.php');

class AllTests_Chart extends TestSuite
{

    function AllTests_Chart()
    {
        $this->TestSuite('All tests');
        $this->addFile('testChart_area_simple.php');
        $this->addFile('testChart_area3D_simple.php');
        $this->addFile('testChart_bar_centered.php');
        $this->addFile('testChart_bar_clustered.php');
        $this->addFile('testChart_bar_color1.php');
        $this->addFile('testChart_bar_color2.php');
        $this->addFile('testChart_bar_right_align.php');
        $this->addFile('testChart_bar_simple.php');
        $this->addFile('testChart_bar_size.php');
        $this->addFile('testChart_bar_stacked.php');
        $this->addFile('testChart_bar_title.php');
        $this->addFile('testChart_bar3D_corners.php');
        $this->addFile('testChart_bar3D_simple.php');
        $this->addFile('testChart_col_axislabel_display.php');
        $this->addFile('testChart_col_axislabels.php');
        $this->addFile('testChart_col_border.php');
        $this->addFile('testChart_col_grid.php');
        $this->addFile('testChart_col_legendoverlay.php');
        $this->addFile('testChart_col_legendpos.php');
        $this->addFile('testChart_col_showtable.php');
        $this->addFile('testChart_col_simple.php');
        $this->addFile('testChart_col3D_simple.php');
        $this->addFile('testChart_line_simple.php');
        $this->addFile('testChart_line3D_simple.php');
        $this->addFile('testChart_pie_porcentajes.php');
        $this->addFile('testChart_pie_simple.php');
        $this->addFile('testChart_pie3D_simple_font.php');
        $this->addFile('testChart_pie3D_simple.php');
        $this->addFile('testChart_radar_simple.php');
    }

}
