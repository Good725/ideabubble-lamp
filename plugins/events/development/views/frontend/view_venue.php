<?php
if ($venue == null) {
?>
    <p>No venue found!</p>
<?php
} else {
    //echo '<pre>' . print_r($event, true) . '</pre>';
?>
<div>
    <div>
        <h1><?=$venue['name']?></h1>
        <p>
        <address>Address: <?=
            $venue['address_1'] . ' ' . $venue['address_2'] . $venue['address_3'] .
            $venue['city'] . ' ' . $venue['eircode'] . ' '
            ?></address><br />
        Phone: <?=$venue['telephone']?><br />
        Website: <?=$venue['website']?> <br />
        Facebook: <?=$venue['facebook_url']?> <br />
        Twitter: <?=$venue['twitter_url']?> <br />
        </p>
    </div>

    <div>
        <h2>Events</h2>
        <ul>
        <?php
        foreach ($events as $event) {
        ?>
            <li><a href="/event/<?=$event['url']?>.html"><?=$event['name']?></a> <a href="/event/<?=$event['url']?>.html"><?=__('Get Tickets')?></a></li>
        <?php
        }
        ?>
        </ul>
    </div>
</div>
<?php

}
?>
