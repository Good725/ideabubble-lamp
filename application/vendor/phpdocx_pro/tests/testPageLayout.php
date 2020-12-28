<?php

/** Unit test. Modify page layout.
 *
 * @category   Phpdocx
 * @package    testing
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2.2
 * @link       http://www.phpdocx.com
 * @since      File available since Release 2.5
 */
require_once('simpletest/autorun.php');
require_once('../classes/CreateDocx.inc');

class TestPropertiesApp extends UnitTestCase
{

    function testPropertiesGenerate()
    {
        @unlink('test_page_layout.docx');
        $docx = new CreateDocx();
		$paramsProperties = array(
		    'orient' => 'landscape',
		    'numberCols' => 2
		);
        $docx->modifyPageLayout('letter', $paramsProperties);
        $docx->createDocx('test_page_layout');
        $this->assertTrue(file_exists('test_page_layout.docx'));
        @unlink('test_page_layout.docx');
    }

}
