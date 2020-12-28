<?php defined('SYSPATH') or die('No direct script access.');

class Model_Kes_IgnoredDiscount extends ORM
{
	protected $_table_name = 'plugin_ib_educate_bookings_ignored_discounts';

	public static function update_ignored_list($booking_id, $discount_ids)
	{
		DB::delete('plugin_ib_educate_bookings_ignored_discounts')->where('booking_id', '=', $booking_id)->execute();
		if (count($discount_ids) > 0)
		{
			$insert = DB::insert('plugin_ib_educate_bookings_ignored_discounts', array('booking_id', 'discount_id'));
			foreach ($discount_ids as $discount_id)
			{
				$insert->values(array($booking_id, $discount_id));
			}
			$insert->execute();
		}
	}
}
