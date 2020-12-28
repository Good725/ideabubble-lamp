<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Invoices</h2>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<form class="form-horizontal" id="form_sort_expiry" name="form_sort_expiry" action="/admin/extra/invoices" method="get">
    <div id="date_from">
        <label for="date_from">Created From</label>
        <input name="date_from" type="text" id="date_from" class="datepicker" value="<?= @$date_from ?>" size="20" />
    </div>
    <div id="date_to">
        <label for="date_to">Created to</label>
        <input name="date_to" type="text" id="date_to" class="datepicker" value="<?= @$date_to ?>" size="20" />
    </div>
    <input type="submit" id="btn_sort" class="btn" value="Filter" />
</form>

<table class="table table-striped dataTable" id="categories_table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Service</th>
            <th scope="col">Url</th>
            <th scope="col">Company</th>
			<th scope="col">Amount</th>
			<th scope="col">Created</th>
			<th scope="col">Status</th>
			<th scope="col">BulletHQ</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($invoices as $invoice): ?>
            <tr id="invoice_<?= $invoice['id'] ?>">
                <td><?= $invoice['id'] ?></td>
                <td><?= $invoice['service_type'] ?></td>
                <td><?= $invoice['url'] ?></td>
                <td><?= $invoice['company_title'] ?></td>
                <td><?= $invoice['amount'] ?></td>
				<td><?= $invoice['created'] ?></td>
				<td><?= $invoice['status']?></td>
				<td>
                <?php
                if ($invoice['bullethq_token']) {
                ?>
					<a href="<?=Model_BulletHQB::get_invoice_view_url($invoice['bullethq_token'])?>" target="_blank">view</a>
					&nbsp;&minus;&nbsp;
					<a href="<?=Model_BulletHQB::get_invoice_download_url($invoice['bullethq_token'])?>" target="_blank">pdf</a>
					&nbsp;&minus;&nbsp;
                <?php
                }
                ?>
                <?php
                if ($invoice['bullethq_id']) {
                ?>
                    <a href="/admin/extra/email_invoice?bullethq_invoice_id=<?= $invoice['bullethq_id'] ?>">send email</a>
                <?php
                }
                ?>
				</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
