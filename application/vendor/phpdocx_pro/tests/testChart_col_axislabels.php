<?php

/**
 * Unit test. Chart col with axis labels.
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
require_once('../classes/CreateDocx.inc');

class TestChart_col_axislabels extends UnitTestCase 
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
            'type' => 'colChart',
            'haxlabel' => 'X Axis label',
            'vaxlabel' => 'Y Axis label',
        );

        $docx->addChart($args);
        $docx->createDocx('test_chart');
        $this->assertTrue(file_exists('test_chart.docx'));
        @unlink('test_chart.docx');
    }

}
