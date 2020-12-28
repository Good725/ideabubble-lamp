<?php
require Kohana::find_file('vendor', 'phpdocx_pro/classes/CreateDocx', 'inc');

class Docgenerator {

	// PHPDOCX Object
	public $docx;
	public $template_location;
	public $scipt;
    public $temporary_folder;


	public function __construct($temp_location, $scipt_location, $temporary_folder) {
        $this->docx = new CreateDocx(PHPDOCX_BASE_TEMPLATE,$temporary_folder);
		$this->template_location = $temp_location;
		$this->scipt = $scipt_location;
        $this->temporary_folder = $temporary_folder;
	}

    // Choose template
    public function add_template($template) {
        $this->docx->addTemplate($this->template_location . $template);
    }

	// Creates the document in places it in documents directory
	public function create($name, $feedback = 0)
    {
        $feedback = $this->docx->createDocx($this->template_location .$name);

        return $feedback;
	}

	// Add template data via variables - requires an array
	public function initalise_document_template($variables) {
//		$DEBUG = FALSE;
		foreach ($variables as $id => $variable)
		{
//			if ($DEBUG)
//			{
//				if (empty($variable))
//				{
//					$variable = 'NO-VALUE';
//				}
//			}
			try {
				if (is_array($variable) AND isset($variable['html']) and isset($variable['type'])) {
				    if (isset($variable['styles'])) {
                        $html = IbHelpers::parse_page_content(IbDocx::strip_untagged($variable['html'], $variable['styles']));
                    } else {
                        $html = IbHelpers::parse_page_content(IbDocx::strip_untagged($variable['html']));
                    }
					$this->docx->replaceTemplateVariableByHTML($id, $variable['type'], $html);
				} elseif (is_array($variable) and isset($variable['type']) and $variable['type'] == 'multiline') {
                    $this->docx->addTemplateVariable($id, $variable['lines']);
                } elseif (is_array($variable) and isset($variable['type']) and $variable['type'] == 'image') {
                    $this->docx->addTemplateImage($id, $variable['file']);
				} elseif (is_array($variable)) {
					$error_reporting = error_reporting();
					error_reporting(0); // some work around for an exception array to string conversation in createdocx.php:4017
					//If is an array we assume that is a table
					$this->docx->addTemplateVariable($variable, 'table', array('header' => true));
					error_reporting($error_reporting);
				} else {
					$this->docx->addTemplateVariable($id, $variable);
				}
			}catch(Exception $exc) {
			}
		}
	}

	// Complex function - multipart operations
	public function make_document($variables, $name) {
		$this->docx->initalise_document_template($variables);
		$this->create($name);
	}

	// Complex function - multipart operations
	public function convert_document($document_in, $document_out) {
		// enable compat mode for pdf creationd
		$this->docx->enableCompatibilityMode();
		// Convert the document
		$this->docx->transformDocx($this->template_location . $document_in, $this->template_location . $document_out . '.pdf', $this->scipt);
	}
}

?>