<?php $cart = Session::instance()->get('cart'); ?>
<?php
if(isset($cart) AND is_object($cart) AND count($cart) > 0):
?>
<table width="100%">
    <tbody>
        <tr>
            <td><img src="http://ideabubble.websitecms.dev/assets/default/images/logo-ideabubble.png" /></td>
            <td align="right">
                Thomcor House<br />
                Mungret Street<br />
                Limerick<br />
                Ireland<br />
                <br />
                <br />
                Tel: + 353 (0)61 513030<br />
                Email: hello@ideabubble.ie
            </td>
        </tr>
        <tr bgcolor="#EEEEEE">
            <td colspan="2" height="50">
                &nbsp;&nbsp;<font size="3"><b>Order Reference: <?= $checkout->payment_ref; ?></b></font><br />
                &nbsp;&nbsp;Total Paid: &euro;<?= $checkout->payment_total ?>
            </td>
        </tr>
        <tr>
            <td>
                <b>Invoiced To</b><br />
                <?= $checkout->ccName; ?>
            </td>
            <td align="center"><font color="#99cc00" size="15"><b>PAID</b></font></td>
        </tr>
        <tr>
            <td colspan="3" height="10">&nbsp;</td>
        </tr>
    </tbody>
</table>
<table width="100%">
    <tbody>
        <tr>
            <td>&nbsp;</td>
            <td width="700">
                <table border="1" bordercolor="#AAAAAA" cellpadding="5" cellspacing="0" width="100%">
                    <thead>
                        <tr bgcolor="#EEEEEE">
                            <th scope="col" width="100">Service ID</th>
                            <th scope="col">Item</th>
                            <th scope="col">URL</th>
                            <th scope="col" width="100">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart->items AS $key=>$item): ?>
                            <?php $service = Model_Extra::get_service_data($item->id); ?>
                            <?php if ($item->billable): ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td><?= $item->item; ?></td>
                                    <td><?= $service['url']; ?></td>
                                    <td>&euro;<?= $item->price; ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <tr bgcolor="#EEEEEE">
                            <th colspan="3" align="right">Total</th>
                            <th align="left">&euro;<?= $checkout->payment_total ?></th>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
</table>
<br />
<br />
<p>View/edit your account details by <a href="<?= URL::site(); ?>customer-login.html">logging into Idea Bubble</a>.</p>
<p>Any questions about this payment please email accounts@ideabubble.ie</p>
<p>
    Kind Regards,<br />
    <i>Accounts Team</i><br />
    Idea Bubble Ltd
</p>
<br />
<br />
<p>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</p>

<?php
else:
?>

<h2>Thank you for your payment</h2>

TOTAL: <b>&euro;<?=$checkout->payment_total?></b><br /><br />

<h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</h5>
<?php
endif;
?>