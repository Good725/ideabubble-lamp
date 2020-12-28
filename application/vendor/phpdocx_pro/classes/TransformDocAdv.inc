<?php

/**
 * Transform DOCX to PDF, ODT, SXW, RTF, DOC, TXT, HTML or WIKI
 *
 * @category   Phpdocx
 * @package    converters
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2011.10.05
 * @link       http://www.phpdocx.com
 * @since      File available since Release 2.5
 */

require_once dirname(__FILE__) . '/CreateDocx.inc';

class TransformDocAdv {
	/**
	 * Transform DOCX to PDF, ODT, SXW, RTF, DOC, TXT, HTML or WIKI
	 * OpenOffice AUTO START: Off
	 * @access public
	 * @param $docSource
	 * @param $docDestination
	 * @return void
	 */
	public function transformDocument($docSource, $docDestination, $script) {

		// Load script and execute
		if (!file_exists($script))
		{
			throw new Exception('The script' . $script . ' cannot be accessed.');
		}
		else
		{

			$number_of_lines = exec($script);

			if ($number_of_lines == 0)
			{
				throw New Exception('The Openoffice service is not running. Please contact Ideabubble');
				// Start soffice service
				// $resultOpenOffice = exec("/templates/scripts/start_oo.sh");
				//[phpdocx] $resultOpenOffice = system(dirname(__FILE__) . '/../lib/openoffice/program/soffice -headless -nofirststartwizard -accept="socket,host=127.0.0.1,port=8100;urp;"');
			}
			else
			{
				$resultTransform = @system('java -jar ' . dirname(__FILE__) . '/../lib/openoffice/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar ' . $docSource . ' ' . $docDestination);
			}
		}
	}
}

?>