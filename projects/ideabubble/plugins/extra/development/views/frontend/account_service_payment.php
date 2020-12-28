<?php if ( ! Auth::instance()->logged_in()): ?>
    <?php include('account_service_login.php'); ?>
<?php else: ?>
    <div style="float:right;"><a href="/frontend/extra/logout"><button type="button">Log out</button></a></div>

    <div class="service-tabs" style="clear:both;">
        <ul>
            <li class="active"><a href="#tab-account">Account</a></li>
            <li><a href="#tab-contact_details">Contact Details</a></li>
            <li><a href="#tab-payment_history">Payment History</a></li>
			<li><a href="#tab-bullethq_invoices">Invoices</a></li>
			<li><a href="#tab-cards">Payment Cards</a></li>
			<li><a href="#tab-user_details">User Details</a></li>
        </ul>
    </div>
    <div class="service-tab-content">
        <div id="tab-account" class="service-tab-pane active"><?php include 'account_services.php'; ?></div>
        <div id="tab-contact_details" class="service-tab-pane"><?php include 'account_contact_details.php'; ?></div>
        <div id="tab-payment_history" class="service-tab-pane"><?php include 'payment_history.php'; ?></div>
		<div id="tab-bullethq_invoices" class="service-tab-pane"><?php include 'bullethq_invoices.php'; ?></div>
		<div id="tab-cards" class="service-tab-pane"><?php include 'account_cards.php'; ?></div>
		<div id="tab-user_details" class="service-tab-pane"><?php include 'account_user_details.php'; ?></div>
    </div>
<?php endif; ?>
<script type="text/javascript" src="<?= URL::get_project_plugin_assets_base('extra').'js/frontend/general.js'?>"></script>