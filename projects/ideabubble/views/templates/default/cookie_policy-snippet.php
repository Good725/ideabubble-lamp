<?php

if( Settings::instance()->get('cookie_enabled') === 'TRUE' AND !isset($_COOKIE['ibPolicyNotice'])){
    echo "<div id='display_message'>";
    echo "<div id='inner_text'>";
    echo Settings::instance()->get('cookie_text');
    echo "<a href='".Model_Pages::get_page_by_id(Settings::instance()->get('cookie_page'))."'>" . Settings::instance()->get('link_text') . "</a>";
    echo "<span id='got_it' onclick='hide_notice()'> " . Settings::instance()->get('hide_notice_message') . "</span>";
    echo "</div></div>";
}

?>