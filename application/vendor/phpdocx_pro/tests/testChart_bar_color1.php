<?php

/**
 * Unit test. Chart bar color.
 *
 * @category   Phpdocx
 * @package    testing
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    1.0
 * @link       http://www.phpdocx.com
 * @since      File available since Release 1.0
 */
require_once('simpletest/autorun.php');
require_once('../classes/CreateDocx.inc');

class TestChart_bar_color1 extends UnitTestCase
{

    function testTextGenerate()
    {
        @unlink('test_chart.docx');
        $docx = new CreateDocx();
        $legends = array(
            'legend' => array('sublegend1', 'sublegend2', 'sublegend3'),
            'legend1' => array(10, 11, 12),
            'legend2' => array(0, 1, 2),
            'legend3' => array(40, 41, 42)
        );
        $args = array(
        	'data' => $legends,
            'type' => 'barChart',
            'title' => 'titulo',
            'color' => 1
        );

        $docx->addChart($args);
        $docx->createDocx('test_chart');
        $this->assertTrue(file_exists('test_chart.docx'));
        @unlink('test_chart.docx');
    }

}
