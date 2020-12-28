<?php if (in_array(Request::detect_uri(), array('', '/', '/home.html', '/home.html/'))): ?>
    <div id="latest-news" class="news_feed left">
        <h1><a href="news.html">Latest news</a></h1>
        <ul id="newsfeed_slider" class="left feed_slider">
            <?=$feed_items?>
        </ul>
    </div>
<?php else: ?>
    <div id="latest-news" class="news_feed">
        <ul id="newsfeed_slider" class="left feed_slider">
            <li><h4><a href="news.html">Latest news</a></h4></li>
            <?=$feed_items?>
        </ul>
    </div>
<?php endif; ?>