<?php
if (Settings::instance()->get('sidebar_news_feed') == 1 )
{
	echo Model_News::get_plugin_items_front_end_feed('News');
}