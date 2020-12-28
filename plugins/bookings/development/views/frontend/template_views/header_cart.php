<?php
$has_cart = Model_Plugin::is_loaded('bookings') && Settings::instance()->get('show_cart_in_mobile_header');
?>

<?php if ($has_cart): ?>
    <div class="header-cart">
        <button type="button" class="header-icon header-cart-button button--plain" data-hide_toggle="#cart-dropdown-menu">
            <span class="header-cart-amount" data-amount="0"></span>
            <span class="header-cart-icon header-cart-icon--empty"><?= file_get_contents(APPPATH.'assets/shared/img/cart-dots.svg') ?></span>
            <span class="header-cart-icon header-cart-icon--not_empty"><?= file_get_contents(APPPATH.'assets/shared/img/cart.svg') ?></span>
        </button>

        <div class="dropdown-menu dropdown-menu--cover hidden" id="cart-dropdown-menu">
            <div class="dropdown-menu-header">
                <h2><?= __('Price breakdown') ?></h2>

                <button type="button" class="button--plain dropdown-menu-close" data-hide_toggle="#cart-dropdown-menu">
                    <span class="icon_close"></span>
                </button>
            </div>

            <div class="dropdown-menu-body" id="checkout-sidebar-items">
                <?php include 'cart.php'; ?>
            </div>

            <div class="dropdown-menu-footer">
                <a href="/checkout.html" class="btn btn-success button button--continue button--full"><?= __('Continue') ?></a>
            </div>
        </div>
    </div>
<?php else: ?>
    <a class="header-profile" href="/admin/profile">
        <?= IbHelpers::embed_svg('user-icon'); ?>
    </a>
<?php endif; ?>