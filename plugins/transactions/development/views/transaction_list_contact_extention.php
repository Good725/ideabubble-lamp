<?= (isset($alert)) ? $alert : '' ?>
<div class="heading-buttons right">
    <div class="btn-group">
        <button type="button" class="btn btn-lg dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
        <ul class="dropdown-menu">
            <li><a href="#" class="add payment">Make a payment</a></li>
        </ul>
    </div>
</div>

<fieldset class="col-sm-12">
    <legend>Transactions</legend>
    <?= View::factory('transactions_list', array('transactions' => $data['transactions']));?>
</fieldset>

<fieldset class="col-sm-12">
    <legend>Payments</legend>
    <?= View::factory("payments_list", array('payments' => array())) ?>
</fieldset>

<?= View::factory("make_payment_modal") ?>

