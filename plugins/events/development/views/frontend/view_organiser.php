<?php
if ($organiser == null) {
?>
    <p>No organiser found!</p>
<?php
} else {
    //echo '<pre>' . print_r($event, true) . '</pre>';
?>
<div>
    <div>
        <h1><?=$organiser['first_name'] . ' ' . $organiser['last_name']?></h1>
        <p>
        <address>Address: <?=
            $organiser['address1'] . ' ' . $organiser['address2'] . $organiser['address3']
            ?></address><br />
        Phone: <?=$organiser['phone']?><br />
        Mobile: <?=$organiser['mobile']?><br />
        Facebook: <?=$organiser['facebook']?> <br />
        Twitter: <?=$organiser['twitter']?> <br />
        Linked In: <?=$organiser['linkedin']?> <br />
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
