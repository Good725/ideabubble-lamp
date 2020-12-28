<div id="product">
    <div id="breadcrumb-nav">
		<?=IbHelpers::breadcrumb_navigation();?>
    </div>
    <?=IbHelpers::get_messages()?>

    <?=$products?>
</div>
<script type="text/javascript" src="<?=URL::site()?>assets/default/js/checkout.js"></script>