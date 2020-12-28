<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
    'Permissions' => array('IBIS' => array('access_insurance_options', 'authorized_to_bind','claims_authority','tadhg_view_claim')),
    'KnowledgeTree' => array(
        'username' => 'knowlegetree@ideabubble.ie',
        'password' => 'Knowledgetree!951',
        'url' => 'https://ideabubble.knowledgetree.com',
        'scan' => '5355524651554a56516b4a4d525541344e546b3d@vault.knowledgetree.com',
        'custom_letter' => '5355524651554a56516b4a4d5255417a4e445577@vault.knowledgetree.com'
    ),
	'doc_config' => array(
    'cache' => '/templates/',
    'script' => 'scripts/oo_check.sh'
    ),
	'upload_location' => array(
    'temp' => '/temp_location/'
    ),
    'pdf_api' => '562713330',
    /** Print Mail **/
    'print_mails' => array(
        //Normal Printer
        'print_mail' => 'docs-print_mail@ideabubble.ie',
        //Header Printer
        'header_print_mail' => 'docs-header_print_mail@ideabubble.ie',
        //Server Printer
        'server_print_mail' => 'docs-server_print_mail@ideabubble.ie',
        //knowledgetree mail
        'knowledgetree_mail' => 'docs-knowledgetree_mail@ideabubble.ie'
    ),
    'dev_print_mails' => array(
        //Normal Printer
        'print_mail' => 'docs-print_mail@ideabubble.ie',
        //Header Printer
        'header_print_mail' => 'docs-header_print_mail@ideabubble.ie',
        //Server Printer
        'server_print_mail' => 'docs-server_print_mail@ideabubble.ie',
        //knowledgetree mail
        'knowledgetree_mail' => 'docs-knowledgetree_mail@ideabubble.ie'
    ),

    /** Error Email **/
    'error_email' => 'error@ideabubble.ie',

);