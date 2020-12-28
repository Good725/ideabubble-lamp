/*
ts:2018-03-28 21:00:00
*/

update plugin_formbuilder_forms set `fields` = concat('<input type="hidden" name="custom_form_call" value="save_groody_booking">', `fields`) where `form_name`= 'Book Online';
