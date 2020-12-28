<?php
$last_order      = Session::instance()->get('last_order_result');
$order_id        = isset($last_order['order_id']) ? $last_order['order_id'] : null;
$order           = Model_Event::orderLoad($order_id);
$user            = Auth::instance()->get_user();
$success_page    = Model_Payments::get_thank_you_page(['full_url' => false]);
$is_success_page = trim(trim(Request::detect_uri(), '.html'), '/') == trim(trim($success_page, '.html'), '/');
?>
<?php if ($is_success_page && isset($order['id'])): ?>

    <?php
    $currencies      = Model_Currency::getCurrencies(true);
    $currency_symbol = isset($currencies[$order['currency']]) ? $currencies[$order['currency']]['symbol'] : $order['currency'];

    // If "intl" is enabled in the PHP settings, this code can be used instead to format currency amounts
    // $number_formatter = new NumberFormatter('en_us', NumberFormatter::CURRENCY);
    // $number_formatter->formatCurrency($item['total'], $order['currency'])
    ?>

    <div class="row row--checkout event-success">
        <div class="columns small-12">
            <h2 class="text-primary"><?= __('Your Booking Details') ?></h2>

            <div class="widget">
                <table class="table table--checkout">
                    <thead>
                        <tr>
                            <th scope="col" class="item"><?= __('Item') ?></th>
                            <th scope="col" class="price"><?= __('Price') ?></th>
                            <th scope="col" class="total"><?= __('Total') ?></th>
                            <th scope="col" class="quantity"><?= __('Quantity') ?></th>
                        </tr>
                    </thead>

                    <tbody id="event-checkout-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?= htmlentities($item['event']) ?> – <?= $item['name'] ?> - <?= date(' F j g:i a', strtotime($item['dates'])) ?></td>
                                <td><?= $currency_symbol.number_format($item['total'], 2) ?></td>
                                <td><?= $currency_symbol.number_format($item['total'] * $item['quantity'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="checkout-prices row row--event collapse text-right">
                <div class="columns large-10">
                    <strong>Total</strong>
                </div>

                <div class="columns large-2">
                    <strong><?= $currency_symbol.number_format($order['total'], 2); ?></strong>
                </div>
            </div>

            <div class="row row--checkout-details">
                <div class="columns small-12 medium-6">
                    <div class="widget widget--checkout">
                        <div class="widget-heading">
                            <h3 class="widget-title"><?= __('Billing Information') ?></h3>
                        </div>

                        <div class="widget-body">
                            <dl class="dl-horizontal">
                                <dt><?= __('Full Name') ?></dt>
                                <dd><?= htmlentities(trim($order['firstname'].' '.$order['lastname'])) ?></dd>

                                <dt><?= __('Address') ?></dt>
                                <dd><?= str_replace("\n", ', ', htmlentities($order['address_1'])) ?></dd>

                                <dt><?= __('Town/City') ?></dt>
                                <dd><?= htmlentities($order['city']) ?></dd>

                                <dt><?= __('State/County') ?></dt>
                                <dd><?= htmlentities($order['county']) ?></dd>

                                <dt><?= __('Country') ?></dt>
                                <dd><?= htmlentities($order['country']) ?></dd>

                                <dt><?= __('Postcode') ?></dt>
                                <dd><?= htmlentities($order['eircode']) ?></dd>

                                <dt><?= __('Tel/Mobile') ?></dt>
                                <dd><?= htmlentities($order['telephone']) ?></dd>

                                <dt><?= __('Email') ?></dt>
                                <dd><?= htmlentities($order['email']) ?></dd>

                                <dt><?= __('Comments') ?></dt>
                                <dd><?= htmlentities($order['comments']) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="columns small-12 medium-6">
                    <div class="widget widget--checkout">
                        <div class="widget-heading">
                            <h3 class="widget-title"><?= __('Order Information') ?></h3>
                        </div>

                        <div class="widget-body">
                            <?php foreach ($order['payments'] as $payment); ?>
                            <dl class="dl-horizontal">
                                <dt><?= __('Order Number') ?></dt>
                                <dd><?= $order_id ?></dd>
                            </dl>
                        </div>
                    </div>

                    <?php
                    $settings = Settings::instance();
                    $social_media['facebook_url']  = trim($settings->get('facebook_url'));
                    $social_media['twitter_url']   = trim($settings->get('twitter_url'));
                    $social_media['instagram_url'] = trim($settings->get('instagram_url'));
                    $social_media['facebook_url']  = trim($settings->get('facebook_url'));
                    $social_media['snapchat_url']  = trim($settings->get('snapchat_url'));
                    array_filter($social_media);
                    ?>

                    <?php if (count($social_media) > 0): ?>
                        <div class="widget widget--checkout">
                            <div class="widget-heading">
                                <h3 class="widget-title"><?= __('Share Your Experience') ?></h3>
                            </div>

                            <div class="widget-body">
                                <p>Invite your friends to join our community.</p>

                                <ul class="social_media-list">
                                    <?php if ( ! empty($social_media['facebook_url'])): ?>
                                        <li>
                                            <a target="_blank" href="http://facebook.com/<?= $social_media['facebook_url'] ?>" title="<?= __('Facebook') ?>">
                                                <img src="/assets/<?= $assets_folder_path ?>/images/social/facebook-icon.png" alt="<?= __('Facebook') ?>"/>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ( ! empty($social_media['twitter_url'])): ?>
                                        <li>
                                            <a target="_blank" href="http://twitter.com/<?= $social_media['twitter_url'] ?>" title="<?= __('Twitter') ?>">
                                                <img src="/assets/<?= $assets_folder_path ?>/images/social/twitter-icon.png" alt="<?= __('Twitter') ?>"/>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ( ! empty($social_media['instagram_url'])): ?>
                                        <li>
                                            <a target="_blank" href="http://instagram.com/<?= $social_media['instagram_url'] ?>" title="<?= __('Instagram') ?>">
                                                <img src="/assets/<?= $assets_folder_path ?>/images/social/instagram-icon.png" alt="<?= __('Instagram') ?>"/>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( ! empty($social_media['snapchat_url'])): ?>
                                        <li>
                                            <a target="_blank" href="http://snapchat.com/add/<?= $social_media['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
                                                <img src="/assets/<?= $assets_folder_path ?>/images/social/snapchat-icon.png" alt="<?= __('Snapchat') ?>"/>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="event-success-actions">
            <a href="/" class="button secondary"><?= __('Browse More Events') ?></a>
        </div>
    </div>
<?php endif; ?>