<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_PostageCountry extends ORM
{
	protected $_table_name = 'engine_countries';
	protected $_publish_column = 'published';
	const MAIN_TABLE = 'engine_countries';

	public static function get_all()
	{
		return DB::select('id', array('name', 'title'), array('published', 'publish'))
			->from(self::MAIN_TABLE)
			->where('deleted', '=', 0)
			->order_by('name')
			->execute()
			->as_array();
	}

}