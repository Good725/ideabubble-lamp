<?php
$session = Session::instance();
if(isset($_SESSION['pl_user']['memberid']) AND !empty($_SESSION['pl_user']['memberid'])){
    $loggedin = true;

    $username = $_SESSION['pl_user']['username'];

    //Assuming that 1 point always is equal to 1 cent
    //If in the future this calculation change, then get this var from the payback model
    $points_in_cash = round((float)$_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance'] / 100, 2);

    //Last transaction date
    $last_transaction_date = $_SESSION['pl_user']['last_transaction']['request_data']['transdate'];
    $last_transaction_date = date('d M Y', strtotime($last_transaction_date));
}
else{
    $loggedin = false;
    $username = 'Login';
}

$cart = $session->get(Model_Checkout::CART_SESSION_ID);
$number_of_items = Model_Checkout::get_cart_value('number_of_items');
$show_cart_on_add = Settings::Instance()->get('show_minicart_on_add');
?>
<!--<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>-->
<div id="checkout_cart"<?= (Settings::instance()->get('cart_hidden_when_empty') == 1) ? ' class="minicart-hidden-when-empty"' : ''; ?> data-product_count="<?= $number_of_items ?>">
    <div class="mycart">
        <div class="mycart_summary">
            <div class="mycart_summary_txt"><a href="/checkout.html"><i class="cart-icon"></i> Shopping Cart</a></div><div class="amount"><span class="mycart_items_amount"><?= $number_of_items ?></span></div>
            <?php if(isset($cart->lines) AND count($cart->lines) > 0): // don't render when there are no products ?>
                <div class="mycart_summary_details mycart_details<?= $show_cart_on_add ? ' show-minicart-on-add' : '' ?>">
                    <?php foreach($cart->lines as $line_number =>$line): ?>
						<?php
						//Get Image
						$product = Model_Product::get_by_category(NULL, $line->product->id);
						if (isset($line->product->sign_thumbnail) AND $line->product->sign_thumbnail != '')
						{
							$thumbnail = '<img class="mini_cart_thumbnail" alt="'.$line->product->title.'" src="'.$line->product->sign_thumbnail.'" />';
						}
						elseif (isset($product['images'][0]))
						{
							$filename = 'shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/products/_thumbs/'.$product['images'][0];
							$filename = file_exists($filename) ? $filename : str_replace('/_thumbs/', '/', $filename);
							$thumbnail = '<img class="mini_cart_thumbnail" alt="'.$line->product->title.'" src="/'.$filename.'" />';
						}
						else
						{
							$thumbnail = '';
						}
						?>

						<div class="mycart_summary_details_line product_line" data-product_id="<?=$line->product->id?>" data-line_id="<?=$line_number?>">
							<div class="mycart_summary_img"><?=$thumbnail?></div>
							<div class="mycart_summary_item_details">
								<div class="mycart_summary_itename" title="<?=$line->product->title ?>"><?=$line->product->title ?><span class="mycart_summary_itename_amount"></span></div>
								<div class="mycart_summary_quantity">Quantity: <span class="quantity"><?=$line->quantity ?></span></div>
								<div class="mycart_summary_price">Price: <span class="price_total_line">&euro;<?= number_format($line->price, 2) ?></span></div>
							</div>
						</div>
                    <?php endforeach;?>
					<div class="details_separator"></div>
					<div>
						<a href="/checkout.html"><?= __('Edit your basket') ?></a>
					</div>
					<div class="details_separator"></div>
                    <div class="mycart_summary_details_line">
                        <div class="mycart_summary_totalitems">Total Items <span class="mycart_summary_itename_amount ">(<span class="mycart_items_amount"><?=$cart->number_of_items ?></span>)</span></div>
                        <div class="mycart_summary_price">&euro;<span id="mycart_total_price"><?= number_format(Model_Checkout::get_cart_total_price_value(), 2)?></span></div>
                    </div>
					<div class="mycart_summary_checkout">
						<a href="/checkout.html">
							<div class="left pl_button" id="pl_member_registration_button">Checkout</div>
						</a>
					</div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (Settings::instance()->get('ws_url') != ''): ?>
            <div class="separator"></div>
            <div class="mycart_register">
                <?php if($loggedin): ?>
                    <div class="mycart_register_txt"><a href="/members-area.html#tabs-3">Points</a></div><div class="amount"><?=@(int)$_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance']?></div>
                    <?php // don't render when the user is not loged in ?>
                    <div class="mycart_register_details mycart_details">
                        <div class="mycart_register_card">
                            <div class="mycart_register_card_txt">Rewards Card No.</div>
                            <div class="mycart_register_card_id"><?=@$_SESSION['pl_user']['account_card_in_use']?></div>
                        </div>
                        <div class="details_separator"></div>
                        <div class="mycart_register_points">
                            <div class="mycart_register_points_txt">Customer Points</div>
                        </div>
                        <div class="mycart_register_points_desc">Remaining points on card <div class="mycart_register_points_amount"><?=@(int)$_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance']?></div></div>
                        <div class="mycart_register_value">
                        </div>
                        <div class="mycart_register_value_desc">Remaining cash balance<div class="mycart_register_value_amount">€<?=@$points_in_cash?></div></div>
                        <div class="details_separator"></div>
                        <div class="mycart_register_card">
                            <div class="mycart_register_card_txt">Last Transaction</div>
                            <div class="mycart_register_card_id"><?=@$last_transaction_date?></div>
                        </div>
                        <div class="mycart_register_value_desc" onclick="$('#tabs-2-btn').click()" ><a href="/members-area.html#tabs-2">View Transaction History »</a></div>
                        <?php if((float)$points_in_cash > 0):?>
                            <a href="">
                                <div class="left pl_button" id="pl_member_redeem_button" name="pl_member_registration_button" type="button">Redeem</div>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="mycart_register_txt" ><a href="/loyalty-registration-form.html">Register</a></div>
                <?php endif; ?>
            </div>
            <div class="separator"></div>
            <?php if($loggedin): ?>
                <div class="mycart_login"><div class="mycart_login_txt"><a href="/members-area.html"><?=@$_SESSION['pl_user']['username']?></a></div>
                    <div class="mycart_login_details mycart_details">
                        <div class="mycart_login_username"><?=@$_SESSION['pl_user']['username']?></div><div class="mycart_login_cardnumber"></div>
                        <div class="details_separator"></div>
                        <div class="mycart_login_line" onclick="$('#tabs-4-btn').click()" ><a href="/members-area.html#tabs-4">Change Password »</a></div>
                        <div class="mycart_login_line" onclick="$('#tabs-1-btn').click()" ><a href="/members-area.html#tabs-1">Edit Details</a></div>
                        <a href="/frontend/paybackloyalty/user_logout">
                            <div class="left pl_button" id="pl_member_logout_button" name="pl_member_registration_button" type="button">Logout</div>
                        </a>
                    </div>
                </div>
            <?php else: ?>
            <div class="mycart_login"><div class="mycart_login_txt"><a href="/login.html">Login</a></div></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>


<link href="/assets/<?= Kohana::$config->load('config')->assets_folder_path ?>/css/minicart.css" rel="stylesheet" type="text/css"/>
