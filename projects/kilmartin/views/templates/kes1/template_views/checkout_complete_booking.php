<div class="terms-txt">
    <p>By clicking ‘<?=$total > 0 ? 'Complete booking' : 'Apply'?>’ you agree to the Terms &amp; Conditions</p>
</div>

<div class="checkout-processed_by">
    <div><?= __('Processed by') ?></div>

    <a href="https://stripe.com/ie" target="_blank">
        <img src="<?= URL::get_engine_assets_base() ?>img/stripe.svg" alt="Stripe" style="width: 70px;">
    </a>
</div>

<button
    type="button"
    class="button button--continue btn-primary checkout-complete_booking"
    data-book_text="<?= htmlspecialchars($total > 0 ? 'Complete booking' : 'Apply') ?>"
    data-sales_quote_text="<?= htmlspecialchars(__('Send me a sales quote')) ?>"
><?= htmlspecialchars($total > 0 ? 'Complete booking' : 'Apply') ?></button>