<?php include 'template_views/header.php';
$addthis_id   = Settings::instance()->get('addthis_id');
$addthis_url  = 'https://www.addthis.com/bookmark.php?v=250&amp;username='.$addthis_id;
?>

<style>
    .addthis_button_compact {
        color: #222;
        border: 1px solid #858585;
        background: rgb(236,237,237);
        background: -moz-linear-gradient(top, rgba(236,237,237,1) 0%, rgba(204,204,204,1) 100%);
        background: -webkit-linear-gradient(top, rgba(236,237,237,1) 0%,rgba(204,204,204,1) 100%);
        background: linear-gradient(to bottom, rgba(236,237,237,1) 0%,rgba(204,204,204,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eceded', endColorstr='#cccccc',GradientType=0 );
    }
</style>

<div class="content">
    <div class="container">
        <section class="row">
			<?php
			$sidebar = new stdClass();
			$sidebar->frontend_menus = array();
			if (Model_Plugin::is_enabled_for_role('Administrator', 'Contacts3') && Auth::instance()->get_user()) {
				$c3 = new Controller_Frontend_Contacts3(new Request(''), new Response());
				$c3->before();
				$sidebar->frontend_menus = $c3->get_frontend_menus();
			}

            if (Model_Plugin::is_enabled_for_role('Administrator', 'Events')) {
                if (Auth::instance()->has_access('events')) {
                    $sidebar->frontend_menus[] = array('name' => 'Home',    'icon' => 'fa fa-home',        'link' => '/');
                    $sidebar->frontend_menus[] = array('name' => 'Events',  'icon' => 'flaticon-calendar', 'link' => '/admin/events');
                    $sidebar->frontend_menus[] = array('name' => 'Profile', 'icon' => 'flaticon-avatar ',  'link' => '/admin/profile/edit?section=contact');
                    $sidebar->frontend_menus[] = array('name' => 'Log Out', 'icon' => 'flaticon-logout',   'link' => '/admin/login/logout');
                }
            }

            $current_action = 'thankyou';
			?>

			<?php require_once 'template_views/sidebar_logged_in.php'; ?>

            <article class="right-section-content">
                <?php // include Kohana::find_file('views', 'checkout_progress'); ?>

				<div class="page-content"><?= $page_data['content'] ?></div>
            </article>
        </section>
        <div><?= Ibhelpers::parse_page_content($page_data['footer']) ?></div>
        <?php
        if (@$_REQUEST['id']) {
            echo Model_Activecampaign::get_conversion_script($_REQUEST['id']);
        }
        ?>
    </div>
</div>

<?php include Kohana::find_file('views', 'footer'); ?>
