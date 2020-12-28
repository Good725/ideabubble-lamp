<?php
$feed = new Model_Feeds;
$display = $feed->display_feed(__FILE__);

if ($display)
    echo Model_Panels::get_panels_feed($position);
?>