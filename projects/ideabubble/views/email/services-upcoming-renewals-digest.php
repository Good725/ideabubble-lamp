<!--
http://ideabubble.websitecms.ie/admin/extra/view_customer/4
http://ideabubble.websitecms.ie/admin/extra/edit_service/31
-->
<table width="100%">
    <tbody>
    <tr>
        <td><img src="http://ideabubble.websitecms.dev/assets/default/images/logo-ideabubble.png"/></td>
        <td align="right">
            Thomcor House<br/>
            Mungret Street<br/>
            Limerick<br/>
            Ireland<br/>
            <br/>
            <br/>
            Tel: + 353 (0)61 513030<br/>
            Email: hello@ideabubble.ie
        </td>
    </tr>
    </tbody>
</table>
<table width="100%">
    <caption><?= $title; ?></caption>
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
                    <th scope="col">Expiry Date</th>
                    <th scope="col" width="100">Price</th>
                    <th scope="col" width="100">Details</th>
                    <th scope="col" width="100">Contact Customer</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // services[0]=>[id]
                // 'id', 'url', 'date_end','company_id','price'
                foreach ($services AS $key => $value): ?>
                    <?php $service = Model_Extra::get_service_data($value['id']); ?>
                    <?php // if ($item->billable): ?>
                    <tr>
                        <td><?= $value['id']; ?></td>
                        <td><?= $service['domain_type'] . ' ' . $service['service_type']; ?></td>
                        <td><a target='_parent' href='http://www.<?= $value['url']; ?>'><?= $value['url']; ?> <a/></td>
                        <td><?= $value['date_end']; ?></td>
                        <td>&euro;<?= $value['price']; ?></td>
                        <td><a href='http://www.ideabubble.ie/admin/extra/edit_service/<?= $value['id']; ?>'> View details</a></td>
                        <td>
                            <?php  if ($service['email'] !=''): ?>
                                <a href='mailto:<?= $service['email']; ?>'> Email</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </td>
        <td>&nbsp;</td>
    </tr>
    </tbody>
</table>
<br/>
Kind Regards,<br/>
<i>Accounts Team</i><br/>
Idea Bubble Ltd
</p>
<br/>
<br/>
<p>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> extra </p>
