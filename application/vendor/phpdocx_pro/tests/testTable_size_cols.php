<?php

/**
 * Unit test. Table size of columns.
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

class TestTableSizeCols extends UnitTestCase
{

    function testTextGenerate()
    {
        @unlink('test_table_size_col.docx');
        $docx = new CreateDocx();
        $valuesTable = array(
            array(
                'Title A',
                'Title B',
                'Title C',
                'Title D',
                'Title E'
            ),
            array(
                'Line A',
                'Value 01',
                'Value 02',
                'Value 03',
                'Value 04',
                'Value 05'
            ),
            array(
                'Line B',
                'Value 11',
                'Value 12',
                'Value 13',
                'Value 14',
                'Value 15'
            ),
            array(
                'Line C',
                'Value 21',
                'Value 22',
                'Value 23',
                'Value 24',
                'Value 25'
            )
        );

        $widthTableCols = array(
            1000,
            2500,
            3000,
            2500
        );

        $paramsTable = array(
            'border' => 'single',
            'border_sz' => 20,
            'size_col' => $widthTableCols
        );

        $docx->addTable($valuesTable, $paramsTable);
        $docx->createDocx('test_table_size_col');
        $this->assertTrue(file_exists('test_table_size_col.docx'));
        @unlink('test_table_size_col.docx');
    }

}
