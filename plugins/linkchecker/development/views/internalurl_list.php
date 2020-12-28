<ul>
    <?php foreach ($urls as $url) { ?>
        <?php if ($url) { ?>
        <li><a href="<?=$url?>" target="_blank"><?=$url?></a></li>
        <?php } else { ?>
            <br />
        <?php } ?>
    <?php } ?>
</ul>