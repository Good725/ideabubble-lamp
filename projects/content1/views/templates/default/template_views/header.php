<div id="header" class="header">
    <a id="logo" class="logo" href="<?= URL::site() ?>"><img src="<?= $page_data['logo'] ?>"" alt="Home" /></a>
    <?php
    switch (Kohana::$config->load('config')->get('db_id'))
    {
        case 'ailesbury':
            require_once 'locations_module.php';
            break;
        case 'saoirsetreatmentcenter':
			echo '<a href="/make-a-donation.html" id="donation_btn" class="button"><span>Donate Now</span></a>';
            echo '<p id="header_slogan" class="header_slogan">'.Settings::instance()->get('company_slogan').'</p>';
            break;
        default:
            require_once 'header_right.php';
            break;
    }
    ?>
</div>