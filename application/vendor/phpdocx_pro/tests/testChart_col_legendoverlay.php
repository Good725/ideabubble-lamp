<?php

/**
 * Unit test. Chart col with legend in overlay.
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

class TestChart_col_legendoverlay extends UnitTestCase 
{

    function testTextGenerate() 
    {
        @unlink('test_chart.docx');
        $docx = new CreateDocx();
        $legends = array(
            'legend' => array('sublegend1', 'sublegend2', 'sublegend3'),
            'legend1' => array(40, 41, 42),
            'legend2' => array(10, 11, 12),
            'legend3' => array(0, 1, 2)
        );
        $args = array(
            'data' => $legends,
            'type' => 'colChart',
            'legendoverlay' => 1
        );

        $docx->addChart($args);
        $docx->createDocx('test_chart');
        $this->assertTrue(file_exists('test_chart.docx'));
        @unlink('test_chart.docx');
    }

}
