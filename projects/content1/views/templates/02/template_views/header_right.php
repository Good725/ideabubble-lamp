<?
$address1 = Settings::instance()->get('addres_line_1');
$address2 = Settings::instance()->get('addres_line_2');
$address3 = Settings::instance()->get('addres_line_3');
$telephone = Settings::instance()->get('telephone');
$email = Settings::instance()->get('email');
?>
<div id="header-right">
    <div id="top">
        <p class="header-company-title">
            <?= Settings::instance()->get('company_title').(($address1 != '') ? '<span class="header-address-line">, '.$address1.'.</span>' : '') ?>
        </p>
        <p><span>TEL:</span> <?= $telephone ?> <span>E-Mail: </span><?= $email ?></p>
    </div>
    <?php if (file_exists(DOCROOT.'/assets/'.$assets_folder_path.'/images/applynow.png')): ?>
        <div id="bottom">
            <?php if (Kohana::$config->load('config')->get('db_id') == 'lsomusic'): ?>
                <a id="book_now" href="https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom" target="_blank">
                    <img alt="Apply Now" src="<?= URL::get_skin_urlpath(TRUE) ?>/images/applynow.png" />
                </a>
            <?php else: ?>
                <a id="book_now" href="contact-us.html">
                    <img alt="Make a Booking" src="<?= URL::get_skin_urlpath(TRUE) ?>/images/applynow.png" />
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

