<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 15/12/2014
 * Time: 15:17
 */
?>
<div id='payment_plan_editor'>
    <div id="trans_payment_editor_alert_area_poopupwin"></div>
    <div style="width:100%;height:30px;border-bottom:1px solid #000000;">
        <h3>Make Payment Plan</h3>
    </div>
<style>
    .ui-autocomplete{top:5% !important;margin-top:74px;}/*DROP DOWN FIX.*/
</style>
<div style="position:relative;height:280px;">
    <div id="trans_payment_editor_alert_area_poopupwin"></div>
    <input type="hidden" id="transaction_id" name="transaction_id" value="<?=$payment_plan->get_transaction_id();?>"/>
    <fieldset class="payment_plan_details left" style="width:405px;position:absolute;left:0;top:0;height:245px;">
        <legend>Transaction</legend>
        <ul>
            <li>
                <label>Outstanding</label>
                <div class="text_input input-prepend">
                    <span class="add-on currency_symbol">€</span>
                    <input type="text" id="outstanding" name="outstanding" class="date_width" value="<?=$payment_plan->get_outstanding();?>"/>
                </div>
            </li>
            <li>
                <label>Deposit</label>
                <div class="text_input input-prepend">
                    <span class="add-on currency_symbol">€</span>
                    <input type="text" id="deposit" name="deposit" class="date_width" value="0.00"/>
                </div>
            </li>
            <li>
                <label>Balance</label>
                <div class="text_input input-prepend">
                    <span class="add-on currency_symbol">€</span>
                    <input type="text" id="balance" name="balance" class="date_width" value="0.00" readonly/>
                </div>
            </li>
            <li>
                <label>Term</label>
                <input type="text" class="" readonly value="<?=Settings::instance()->get('payment_plan_term');?>" name="months" id="months"/>

                <select>
                    <option value="months">Months</option>
                </select>
            </li>
            <li>
                <label>Interest</label>
                <input type="text" class="" name="interest_rate" id="interest_rate" readonly value="<?=Settings::instance()->get('payment_plan_interest_rate');?>"/>

                <div class="text_input input-prepend">
                    <span class="add-on currency_symbol">€</span>
                    <input type="text" id="interest_amount" name="interest_amount" class="date_width" value="0.00" readonly/>
                </div>
            </li>
            <li>
                <label>Start Date</label>
                <input type="datepicker" id="start_date" name="start_date" value="<?=date('Y-m-d');?>"/>
            </li>
            <li>
                <label>Total Due</label>
                <div class="text_input input-prepend">
                    <span class="add-on currency_symbol">€</span>
                    <input type="text" id="total_due" name="total_due" class="date_width" value="0.00"/>
                </div>
                <button class="btn" type="button" id="calculate_payment_plan">Calculate</button>
            </li>
        </ul>
    </fieldset>

    <table class="table table-striped table-hover right datatable" style="width:450px;position:absolute;right:0;top:0;height:280px;overflow-y:scroll;display:inline-block;" id="payment_plan_schedule_table">
        <thead>
            <tr>
                <th>
                    Amount
                </th>
                <th>
                    Interest
                </th>
                <th>
                    Total
                </th>
                <th>
                    Due Date
                </th>
                <th>
                    Status
                </th>
                <th>
                    Balance
                </th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

</div>
</fieldset>

    <div style="width:100%;height:30px;border-top:1px solid #000000;text-align:center;">
        <button class="btn" onclick="popup('close');">Cancel</button> <button class="btn btn-primary" id="start_payment_plan">Start Plan</button> <button class="btn btn-danger" id="cancel_payment_plan">Cancel Plan</button>
    </div>

<div class="floating-nav-marker"></div>
</div>
