<p>A payment was processed through our site : <?= $_SERVER['HTTP_HOST'] ?> website</p>
<?php $cart = Session::instance()->get('cart'); ?>

<h2>Customer details:</h2>
<?php
if(isset($cart) AND is_object($cart) AND count($cart) > 0):
?>
<table width="100%">
    <tbody>
        <tr bgcolor="#EEEEEE">
            <td colspan="2" height="50">
                &nbsp;&nbsp;<font size="3"><b>Order Reference: <?= $checkout->payment_ref; ?></b></font><br />
                &nbsp;&nbsp;Email:    <?= $checkout->email    ?><br />
                &nbsp;&nbsp;Phone:    <?= $checkout->phone    ?><br />
                &nbsp;&nbsp;Comments: <?= $checkout->comments ?><br />
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
                <?php
                    foreach($cart->items AS $key=>$item): ?>
                        <?php $service = Model_Extra::get_service_data($item->id); ?>
                        <?php if ($item->billable): ?>
                            <tr>
                                <td><?= $item->id; ?></td>
                                <td><?= $item->item; ?></td>
                                <td><?= $service['url']; ?></td>
                                <td>&euro;<?= $item->price; ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php
                    endforeach;
                ?>
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
<p>TOTAL: <b>&euro;<?= $checkout->payment_total ?></b></p>
<br />
<br />
<p>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</p>
<?php
else:
?>


<p>
    <b>payment_ref:</b> <?= $checkout->payment_ref ?><br />
    <b>comments:</b> <?= $checkout->comments ?><br />
    <b>email:</b> <?= $checkout->email ?><br />
    <b>ccName:</b> <?= $checkout->ccName ?><br />
    <b>phone:</b> <?= $checkout->phone ?><br />
    <br/>

    <?php
    if(!is_null($cart)):?>
    <table>
        <thead>
        <tr>
            <th>Service ID</th>
            <th>Item</th>
            <th>URL</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($cart->items AS $key=>$item):
            $service = Model_Extra::get_service_data($item->id);
            ?>
            <tr>
                <td><?=$item->id;?></td>
                <td><?=$item->item;?></td>
                <td><?=$service['url'];?></td>
                <td>€<?=$item->price;?></td>
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
<?php
endif;
?>

TOTAL: <b>&euro;<?=$checkout->payment_total?></b><br /><br />

<a href="<?=URL::site();?>customer-payment.html">Click here to see your updated account information.</a>
<h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</h5>
<?php
endif;
?>