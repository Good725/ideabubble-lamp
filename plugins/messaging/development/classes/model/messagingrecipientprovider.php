<?php defined('SYSPATH') or die('No direct script access.');

interface Model_MessagingRecipientProvider
{
	public function pid();
	public function supports($driver);
	public function get_by_id($id);
	public function get_by_label($label);
	public function search($term);
	public function to_autocomplete($term, &$data);
	public function resolve_final_targets($target, &$target_list, &$warnings);
	public function message_details_column();
	public function message_details_join($query);
}