<div class="dropdown">
    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
        <?= __('Actions') ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <?php if ($order['archived']) { ?>
            <li>
                <button type="button" class="btn-link list-unarchive-button"
                        data-id="<?= $order['id'] ?>">
                    <span class="icon-calendar-minus-o"></span> <?= __('Unarchive') ?>
                </button>
            </li>
        <?php } else { ?>
            <li>
                <button type="button" class="btn-link list-archive-button"
                        data-id="<?= $order['id'] ?>">
                    <span class="icon-calendar-times-o"></span> <?= __('Archive') ?>
                </button>
            </li>
        <?php } ?>
        <?php if ($order['status'] == 'PAID') { ?>
            <li>
                <a class="btn-link list-print-button" href="/admin/events/ticket?order_id=<?=$order['id']?>&ticket_id=&action=print">
                    <span class="icon-print"></span> <?= __('Print') ?>
                </a>
            </li>
            <li>
                <a class="btn-link list-download-button" href="/admin/events/ticket?order_id=<?=$order['id']?>&ticket_id=&action=download">
                    <span class="icon-print"></span> <?= __('Download') ?>
                </a>
            </li>
            <li>
                <a class="btn-link list-email-button"
                   data-toggle="modal"
                   data-target="#email-order-modal"
                   data-id="<?= $order['id'] ?>">
                    <span class="icon-print"></span> <?= __('Email') ?>
                </a>
            </li>

            <li>
                <a class="btn-link list-receipt-button" href="/admin/events/receipt/<?=$order['id']?>">
                    <span class="icon-print"></span> <?= __('Receipt') ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>