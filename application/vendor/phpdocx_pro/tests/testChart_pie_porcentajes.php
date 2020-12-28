<?php

/**
 * Unit test. Chart pie show percent.
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

class TestChart_pie_porcentaje extends UnitTestCase
{

    function testTextGenerate()
    {
        @unlink('test_chart.docx');
        $docx = new CreateDocx();
        $legends = array(
            'legend1' => array(10),
            'legend2' => array(0),
            'legend3' => array(40)
        );
        $args = array(
            'data' => $legends,
            'type' => 'pieChart',
            'showPercent' => 1
        );

        $docx->addChart($args);
        $docx->createDocx('test_chart');
        $this->assertTrue(file_exists('test_chart.docx'));
        @unlink('test_chart.docx');
    }

}
