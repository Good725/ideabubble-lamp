<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contact_MailingList extends ORM
{
	protected $_table_name = Model_Contacts::TABLE_MAILING_LIST;
	protected $_belongs_to = array(
		'dashboard'   => array('model' => 'Dashboard',        'foreign_key' => 'dashboard_id'),
		'gadget_type' => array('model' => 'Dashboard_Gadget', 'foreign_key' => 'type_id')
	);

}
