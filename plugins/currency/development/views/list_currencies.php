<?= (isset($alert)) ? $alert : ''; ?>

<div class="list_currencies_wrapper">
    <table class="table table-striped" id="list_currencies_table">
        <thead>
            <tr>
                <th scope="col">Currency</th>
                <th scope="col">Description</th>
                <th scope="col">Symbol</th>
                <th scope="col">Rate (<?= Settings::instance()->get('currency_base')?>*)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($currencies as $currency) {
        ?>
            <tr>
                <td><?=$currency['currency']?></td>
                <td><?=$currency['name']?></td>
                <td><?=$currency['symbol']?></td>
                <td><?=@$rates[$currency['currency']]?></td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>